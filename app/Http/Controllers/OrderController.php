<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Support\CurrencyFormatter;
use App\Services\InventoryService;
use App\Services\PromotionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly PromotionService $promotionService,
        private readonly InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,paid,cancelled'],
            'order_type' => ['nullable', 'in:dine_in,takeaway,delivery'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        if (! $from && ! $to) {
            $today = now()->toDateString();
            $from = $today;
            $to = $today;
        } elseif ($from && ! $to) {
            $to = $from;
        } elseif (! $from && $to) {
            $from = $to;
        }

        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        $orders = Order::query()
            ->withCount('items')
            ->with(['user:id,name', 'restaurantTable:id,name'])
            ->when($validated['q'] ?? null, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->when($validated['order_type'] ?? null, fn($query, string $orderType) => $query->where('order_type', $orderType))
            ->when($from, fn($query, string $value) => $query->whereDate('created_at', '>=', $value))
            ->when($to, fn($query, string $value) => $query->whereDate('created_at', '<=', $value))
            ->latest('order_serial')
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
                'order_type' => $validated['order_type'] ?? '',
                'from' => $from ?? '',
                'to' => $to ?? '',
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'user:id,name,email', 'restaurantTable:id,name']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order, \App\Services\PrintService $printService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,cancelled'],
            'payment_method' => ['nullable', 'in:cash,visa,instapay,wallet'],
        ]);

        // Capture pre-transaction state for post-transaction print decision
        $previousStatus = (string) $order->status;
        $nextStatus     = (string) $validated['status'];
        $isDineIn       = (string) $order->order_type === 'dine_in';

        $cashierInventoryRecipeLinkEnabled = $this->isCashierInventoryRecipeLinkEnabled();

        try {
            DB::transaction(function () use ($order, $validated, $cashierInventoryRecipeLinkEnabled): void {
                $managedOrder = Order::query()
                    ->whereKey($order->order_serial)
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousStatus = (string) $managedOrder->status;
                $nextStatus = (string) $validated['status'];

                if ($previousStatus === 'paid' && $nextStatus !== 'paid') {
                    throw ValidationException::withMessages([
                        'status' => __('messages.errors.order_status_locked_after_paid'),
                    ]);
                }

                if ($previousStatus === 'cancelled' && $nextStatus !== 'cancelled') {
                    throw ValidationException::withMessages([
                        'status' => __('messages.errors.order_status_locked_after_cancelled'),
                    ]);
                }

                $tableId = $managedOrder->restaurant_table_id
                    ? (int) $managedOrder->restaurant_table_id
                    : null;

                $updates = ['status' => $nextStatus];
                if (isset($validated['payment_method']) && $nextStatus === 'paid') {
                    $updates['payment_method'] = $validated['payment_method'];
                }

                $managedOrder->update($updates);

                if ($nextStatus === 'paid' && $previousStatus !== 'paid' && $cashierInventoryRecipeLinkEnabled) {
                    $this->inventoryService->deductInventoryForOrder($managedOrder, true);
                }

                $this->syncTableStatus($tableId);
            });
        } catch (QueryException $exception) {
            if ($this->isActiveTableGuardUniqueViolation($exception)) {
                return back()->withErrors([
                    'status' => __('messages.errors.table_has_active_order'),
                ]);
            }

            throw $exception;
        }

        // Queue cashier receipt when dine-in table is closed
        if ($isDineIn && $previousStatus !== 'paid' && $nextStatus === 'paid') {
            try {
                $order->refresh();
                \App\Models\PrintJob::create([
                    'printer_type'   => 'cashier',
                    'payload'        => json_encode(['order_serial' => $order->order_serial]),
                    'payload_type'   => 'json',
                    'status'         => 'pending',
                    'printable_type' => \App\Models\Order::class,
                    'printable_id'   => $order->order_serial,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('OrderController: failed to queue cashier print job', [
                    'order_number' => $order->order_number,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', __('messages.success.order_status_updated'));
    }

    public function updateDiscount(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'discount_type' => ['required', 'in:none,fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        $discountTypeInput = (string) ($validated['discount_type'] ?? 'none');
        $discountType = $discountTypeInput === 'none' ? null : $discountTypeInput;
        $discountValue = $discountType
            ? (float) ($validated['discount_value'] ?? 0)
            : 0;
        $couponCode = strtoupper(trim((string) ($validated['coupon_code'] ?? '')));
        $couponCode = $couponCode !== '' ? $couponCode : null;

        if ($couponCode && $discountValue > 0) {
            throw ValidationException::withMessages([
                'discount_value' => __('messages.errors.manual_discount_with_coupon'),
            ]);
        }

        DB::transaction(function () use ($order, $discountType, $discountValue, $couponCode): void {
            $managedOrder = Order::query()
                ->whereKey($order->order_serial)
                ->lockForUpdate()
                ->firstOrFail();

            if ($managedOrder->status !== 'pending') {
                throw ValidationException::withMessages([
                    'discount_type' => __('messages.errors.order_discount_only_pending'),
                ]);
            }

            $subtotal = (float) $managedOrder->subtotal;

            $manualDiscountAmount = $discountType
                ? $this->promotionService->calculateDiscountAmount($discountType, $discountValue, $subtotal, null)
                : 0;

            $couponResult = $this->promotionService->validateCoupon($couponCode, $subtotal, true);
            $appliedCoupon = $couponResult['coupon'] ?? null;
            $couponAppliedAmount = (float) ($couponResult['discount_amount'] ?? 0);

            $discountAmount = min($manualDiscountAmount + $couponAppliedAmount, $subtotal);
            $total = max($subtotal - $discountAmount, 0);

            $existingRedemptions = CouponRedemption::query()
                ->where('order_id', $managedOrder->order_serial)
                ->lockForUpdate()
                ->get();

            $existingCouponIds = $existingRedemptions
                ->pluck('coupon_id')
                ->filter()
                ->map(static fn($id): int => (int) $id)
                ->unique()
                ->values();

            if ($existingCouponIds->isNotEmpty()) {
                $existingCoupons = Coupon::query()
                    ->whereIn('id', $existingCouponIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($existingRedemptions as $redemption) {
                    $coupon = $existingCoupons->get((int) $redemption->coupon_id);

                    if ($coupon && (int) $coupon->used_count > 0) {
                        $coupon->decrement('used_count');
                    }
                }

                CouponRedemption::query()
                    ->whereIn('id', $existingRedemptions->pluck('id'))
                    ->delete();
            }

            $managedOrder->update([
                'discount_type' => $discountType,
                'discount_value' => round($discountValue, 2),
                'coupon_id' => $appliedCoupon?->id,
                'offer_id' => null,
                'coupon_code' => $appliedCoupon?->code,
                'offer_name' => null,
                'discount_amount' => round($discountAmount, 2),
                'total' => round($total, 2),
            ]);

            if ($appliedCoupon && $couponAppliedAmount > 0) {
                $appliedCoupon->redemptions()->create([
                    'order_id' => $managedOrder->order_serial,
                    'user_id' => Auth::id(),
                    'discount_amount' => round($couponAppliedAmount, 2),
                    'redeemed_at' => now(),
                ]);

                $appliedCoupon->increment('used_count');
            }
        });

        return back()->with('success', __('messages.success.order_discount_updated'));
    }

    public function destroyItem(Request $request, Order $order, OrderItem $orderItem): RedirectResponse|JsonResponse
    {
        $result = DB::transaction(function () use ($order, $orderItem): array {
            $managedOrder = Order::query()
                ->whereKey($order->order_serial)
                ->lockForUpdate()
                ->firstOrFail();

            if ($managedOrder->status !== 'pending') {
                throw ValidationException::withMessages([
                    'item' => __('messages.errors.order_item_delete_only_pending'),
                ]);
            }

            $managedItem = $managedOrder->items()
                ->whereKey($orderItem->id)
                ->lockForUpdate()
                ->first();

            if (! $managedItem) {
                throw ValidationException::withMessages([
                    'item' => __('messages.errors.order_item_not_found_for_order'),
                ]);
            }

            $managedItem->delete();

            $remainingItems = $managedOrder->items();

            if (! $remainingItems->exists()) {
                $tableId = $managedOrder->restaurant_table_id
                    ? (int) $managedOrder->restaurant_table_id
                    : null;

                $managedOrder->delete();

                $this->syncTableStatus($tableId);

                return ['order_deleted' => true];
            }

            $totals = $this->recalculateOrderTotals($managedOrder);

            return ['order_deleted' => false, 'totals' => $totals];
        });

        $orderDeleted = ($result['order_deleted'] ?? false) === true;

        if ($request->expectsJson()) {
            if ($orderDeleted) {
                return response()->json([
                    'order_deleted'    => true,
                    'message'          => __('messages.success.order_deleted'),
                    'redirect'         => route('orders.index'),
                ]);
            }

            $totals = $result['totals'];

            return response()->json([
                'order_deleted'          => false,
                'message'                => __('messages.success.order_item_deleted'),
                'subtotal_formatted'     => CurrencyFormatter::format($totals['subtotal']),
                'total_formatted'        => CurrencyFormatter::format($totals['total']),
                'discount_formatted'     => CurrencyFormatter::format($totals['discount_amount']),
            ]);
        }

        if ($orderDeleted) {
            return redirect()
                ->route('orders.index')
                ->with('success', __('messages.success.order_deleted'));
        }

        return back()->with('success', __('messages.success.order_item_deleted'));
    }

    public function updateItemQuantity(Request $request, Order $order, OrderItem $orderItem): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:increment,decrement'],
        ]);

        $result = DB::transaction(function () use ($order, $orderItem, $validated): array {
            $managedOrder = Order::query()
                ->whereKey($order->order_serial)
                ->lockForUpdate()
                ->firstOrFail();

            if ($managedOrder->status !== 'pending') {
                throw ValidationException::withMessages([
                    'quantity' => __('messages.errors.order_item_quantity_only_pending'),
                ]);
            }

            $managedItem = $managedOrder->items()
                ->whereKey($orderItem->id)
                ->lockForUpdate()
                ->first();

            if (! $managedItem) {
                throw ValidationException::withMessages([
                    'item' => __('messages.errors.order_item_not_found_for_order'),
                ]);
            }

            $currentQuantity = (int) $managedItem->quantity;
            $isIncrement = $validated['action'] === 'increment';

            if (! $isIncrement && $currentQuantity <= 1) {
                throw ValidationException::withMessages([
                    'quantity' => __('messages.errors.order_item_quantity_minimum'),
                ]);
            }

            $newQuantity = $isIncrement ? $currentQuantity + 1 : $currentQuantity - 1;
            $newLineTotal = round((float) $managedItem->unit_price * $newQuantity, 2);

            $managedItem->update([
                'quantity' => $newQuantity,
                'line_total' => $newLineTotal,
            ]);

            $totals = $this->recalculateOrderTotals($managedOrder);

            return [
                'item_id' => (int) $managedItem->id,
                'quantity' => $newQuantity,
                'line_total' => $newLineTotal,
                'line_total_formatted' => CurrencyFormatter::format($newLineTotal),
                'subtotal' => $totals['subtotal'],
                'subtotal_formatted' => CurrencyFormatter::format($totals['subtotal']),
                'discount_amount' => $totals['discount_amount'],
                'discount_amount_formatted' => CurrencyFormatter::format(-$totals['discount_amount']),
                'total' => $totals['total'],
                'total_formatted' => CurrencyFormatter::format($totals['total']),
            ];
        });

        return response()->json([
            'message' => __('messages.success.order_item_quantity_updated'),
            'data' => $result,
        ]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $managedOrder = Order::query()
                ->whereKey($order->order_serial)
                ->lockForUpdate()
                ->firstOrFail();

            $tableId = $managedOrder->restaurant_table_id
                ? (int) $managedOrder->restaurant_table_id
                : null;

            $managedOrder->delete();

            $this->syncTableStatus($tableId);
        });

        return redirect()
            ->route('orders.index')
            ->with('success', __('messages.success.order_deleted'));
    }

    public function invoice(Order $order): View
    {
        abort_if((string) $order->status !== 'paid', 404);

        $order->load([
            'items',
            'user:id,name,email',
            'restaurantTable:id,name',
            'customer:id,first_name,phone,address',
        ]);

        return view('orders.invoice', [
            'order' => $order,
        ]);
    }

    public function directPrint(Order $order, \App\Services\PrintService $printService): JsonResponse
    {
        abort_if((string) $order->status !== 'paid', 404);

        $order->load([
            'items',
            'user:id,name,email',
            'restaurantTable:id,name',
            'customer:id,first_name,phone,address',
        ]);

        [$success, $errorReason] = $printService->printOrderInvoice($order);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Printed successfully on ZKP8001']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Printer error — check that the USB cable is connected.',
            'reason'  => $errorReason,
        ], 500);
    }


    public function queuePrint(Order $order): \Illuminate\Http\JsonResponse
    {
        abort_if((string) $order->status !== 'paid', 404);

        \App\Models\PrintJob::create([
            'printer_type'   => 'cashier',
            'payload'        => json_encode(['order_serial' => $order->order_serial]),
            'payload_type'   => 'json',
            'status'         => 'pending',
            'printable_type' => Order::class,
            'printable_id'   => $order->order_serial,
        ]);

        return response()->json(['ok' => true]);
    }

    public function receiptData(Order $order, \App\Services\PrintService $printService): JsonResponse
    {
        abort_if((string) $order->status !== 'paid', 404);

        $order->loadMissing([
            'items',
            'user:id,name',
            'restaurantTable:id,name',
            'customer:id,first_name',
        ]);

        $base64 = $printService->buildOrderReceiptBase64($order);

        return response()->json([
            'data'         => $base64,
            'printer_name' => \App\Models\Printer::windowsNameFor('cashier'),
        ]);
    }


    private function recalculateOrderTotals(Order $managedOrder): array
    {
        $newSubtotal = (float) $managedOrder->items()->sum('line_total');
        $oldSubtotal = max((float) $managedOrder->subtotal, 0);
        $oldDiscountAmount = max((float) $managedOrder->discount_amount, 0);

        $oldManualDiscount = $managedOrder->discount_type
            ? (float) $this->promotionService->calculateDiscountAmount(
                (string) $managedOrder->discount_type,
                (float) $managedOrder->discount_value,
                $oldSubtotal,
                null,
            )
            : 0;

        $oldCouponDiscount = max($oldDiscountAmount - min($oldManualDiscount, $oldDiscountAmount), 0);

        $newManualDiscount = $managedOrder->discount_type
            ? (float) $this->promotionService->calculateDiscountAmount(
                (string) $managedOrder->discount_type,
                (float) $managedOrder->discount_value,
                $newSubtotal,
                null,
            )
            : 0;

        $newDiscountAmount = min($newManualDiscount + $oldCouponDiscount, $newSubtotal);
        $newTotal = max($newSubtotal - $newDiscountAmount, 0);

        $totals = [
            'subtotal' => round($newSubtotal, 2),
            'discount_amount' => round($newDiscountAmount, 2),
            'total' => round($newTotal, 2),
        ];

        $managedOrder->update($totals);

        return $totals;
    }

    private function syncTableStatus(?int $tableId): void
    {
        if (! $tableId) {
            return;
        }

        RestaurantTable::query()->whereKey($tableId)->lockForUpdate()->first();

        $hasActiveOrder = Order::query()
            ->where('order_type', 'dine_in')
            ->where('restaurant_table_id', $tableId)
            ->whereIn('status', Order::activeDineInStatuses())
            ->exists();

        RestaurantTable::query()
            ->whereKey($tableId)
            ->update([
                'status' => $hasActiveOrder ? 'occupied' : 'available',
            ]);
    }

    private function isCashierInventoryRecipeLinkEnabled(): bool
    {
        return (bool) config('features.cashier_inventory_recipe_link_enabled', false);
    }

    private function isActiveTableGuardUniqueViolation(QueryException $exception): bool
    {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1062
            && str_contains($exception->getMessage(), 'orders_active_table_guard_unique');
    }
}
