<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\RecipeItem;
use App\Models\RecipeVersion;
use App\Models\RestaurantTable;
use App\Models\ShiftLog;
use App\Services\InventoryService;
use App\Services\PromotionService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private readonly PromotionService $promotionService,
        private readonly InventoryService $inventoryService
    ) {}

    public function index(): View
    {
        $categories = Category::query()
            ->with('parent:id,name')
            ->orderByRaw("CASE WHEN type = 'main' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id'])
            ->map(static fn(Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent?->name,
            ])
            ->values();

        $activeRecipeVersionByProduct = RecipeVersion::query()
            ->whereNotNull('product_id')
            ->where('is_active', true)
            ->where('is_semi_finished', false)
            ->get(['id', 'product_id'])
            ->keyBy('product_id');

        $products = Product::query()
            ->with('category:id,name,type,parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock', 'image_url', 'category_id'])
            ->map(static function (Product $product) use ($activeRecipeVersionByProduct): array {
                $activeVersion = $activeRecipeVersionByProduct->get((int) $product->id);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'stock' => (int) $product->stock,
                    'image_url' => $product->image_url,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'category_type' => $product->category?->type,
                    'category_parent_id' => $product->category?->parent_id,
                    'recipe_version_id' => $activeVersion?->id,
                ];
            })
            ->values();

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'tables' => $this->buildTablesPayload(),
            'deliveryEmployees' => $this->deliveryEmployeesPayload(),
            'activeShift' => $this->activeShiftPayload(),
        ]);
    }

    public function startShift(Request $request): JsonResponse
    {
        if (! Schema::hasTable('cashier_shifts')) {
            throw ValidationException::withMessages([
                'opening_cash' => __('messages.errors.cashier_shift_feature_unavailable'),
            ]);
        }

        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        try {
            $shift = DB::transaction(function () use ($validated): CashierShift {
                $userId = (int) Auth::id();

                $existingShift = CashierShift::query()
                    ->where('user_id', $userId)
                    ->where('status', 'open')
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                if ($existingShift) {
                    throw ValidationException::withMessages([
                        'opening_cash' => __('messages.errors.cashier_shift_already_open'),
                    ]);
                }

                $shiftStartTime = now();

                $shift = CashierShift::query()->create([
                    'user_id' => $userId,
                    'opening_cash' => round((float) $validated['opening_cash'], 2),
                    'start_time' => $shiftStartTime,
                    'status' => 'open',
                ]);

                if ($this->supportsShiftLogs()) {
                    ShiftLog::query()->create([
                        'user_id' => $userId,
                        'shift_start' => $shiftStartTime,
                    ]);
                }

                return $shift;
            });
        } catch (QueryException $exception) {
            if ($this->isOpenShiftGuardUniqueViolation($exception)) {
                throw ValidationException::withMessages([
                    'opening_cash' => __('messages.errors.cashier_shift_already_open'),
                ]);
            }

            throw $exception;
        }

        return response()->json([
            'message' => __('messages.success.cashier_shift_started'),
            'shift' => $this->formatShiftPayload($shift),
        ], 201);
    }

    public function endShift(Request $request, \App\Services\PrintService $printService): JsonResponse
    {
        if (! Schema::hasTable('cashier_shifts')) {
            throw ValidationException::withMessages([
                'actual_cash' => __('messages.errors.cashier_shift_feature_unavailable'),
            ]);
        }

        if (! $this->supportsShiftClosingFields()) {
            throw ValidationException::withMessages([
                'actual_cash' => __('messages.errors.cashier_shift_close_unavailable'),
            ]);
        }

        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'shift_id')) {
            throw ValidationException::withMessages([
                'actual_cash' => __('messages.errors.cashier_shift_sales_link_unavailable'),
            ]);
        }

        $validated = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'tips' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        $tips = round((float) ($validated['tips'] ?? 0), 2);
        $cashierName = (string) ($request->user()?->name ?? 'System');

        $shift = DB::transaction(function () use ($validated, $tips): CashierShift {
            $userId = (int) Auth::id();

            $activeShift = CashierShift::query()
                ->where('user_id', $userId)
                ->where('status', 'open')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (! $activeShift) {
                $latestShift = CashierShift::query()
                    ->where('user_id', $userId)
                    ->latest('id')
                    ->first();

                throw ValidationException::withMessages([
                    'actual_cash' => $latestShift && (string) $latestShift->status === 'closed'
                        ? __('messages.errors.cashier_shift_already_closed')
                        : __('messages.errors.cashier_shift_not_open'),
                ]);
            }

            $totalSales = (float) Order::query()
                ->where('shift_id', (int) $activeShift->id)
                ->where('status', 'paid')
                ->sum('total');

            $expectedCash = round((float) $activeShift->opening_cash + $totalSales, 2);
            $actualCash = round((float) $validated['actual_cash'], 2);
            $difference = round($actualCash - $expectedCash, 2);
            $shiftEndTime = now();

            $activeShift->update([
                'total_sales' => round($totalSales, 2),
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'tips' => $tips,
                'difference' => $difference,
                'end_time' => $shiftEndTime,
                'status' => 'closed',
            ]);

            if ($this->supportsShiftLogs()) {
                $openShiftLog = ShiftLog::query()
                    ->where('user_id', $userId)
                    ->whereNull('shift_end')
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                if ($openShiftLog) {
                    $openShiftLog->update([
                        'shift_end' => $shiftEndTime,
                        'cash_difference' => $difference,
                    ]);
                } else {
                    ShiftLog::query()->create([
                        'user_id' => $userId,
                        'shift_start' => $activeShift->start_time ?? $shiftEndTime,
                        'shift_end' => $shiftEndTime,
                        'cash_difference' => $difference,
                    ]);
                }
            }

            return $activeShift->refresh();
        });

        $receipt = $this->buildShiftReceiptPayload($shift, $cashierName);

        // Queue shift-close receipt for QZ Tray printing
        $printJobId = null;
        try {
            $html = view('reports.shift-log-receipt', ['receipt' => $receipt, 'isDirectPrint' => true])->render();

            $printJob = \App\Models\PrintJob::create([
                'printer_type' => 'shift_close',
                'payload'      => $printService->buildHtmlBase64($html),
                'payload_type' => 'base64',
                'status'       => 'pending',
            ]);
            $printJobId = $printJob->id;
        } catch (\Throwable) {
            // Non-blocking: printer failure must not prevent shift close
        }

        // Logout is deferred to the JS side so QZ Tray can pick up the print job first
        return response()->json([
            'message'       => __('messages.success.cashier_shift_ended'),
            'shift'         => $this->formatShiftPayload($shift),
            'receipt'       => $receipt,
            'print_job_id'  => $printJobId,
            'logout_url'    => route('logout'),
            'redirect_to'   => route('login'),
        ]);
    }

    public function tableOrder(RestaurantTable $restaurantTable): JsonResponse
    {
        $tableId = (int) $restaurantTable->id;
        $order = $this->findActiveDineInOrder($tableId);

        if (! $order && $restaurantTable->status !== 'available') {
            return response()->json([
                'message' => __('messages.errors.table_not_available'),
                'order' => null,
                'tables' => $this->buildTablesPayload(),
            ], 422);
        }

        if ($order && $restaurantTable->status !== 'occupied') {
            $restaurantTable->update([
                'status' => 'occupied',
            ]);

            $restaurantTable->refresh();
        }

        return response()->json([
            'message' => $order
                ? __('messages.success.table_order_loaded', [
                    'order_number' => $order->order_number,
                    'table' => $restaurantTable->name,
                ])
                : __('messages.success.table_ready_for_new_order', [
                    'table' => $restaurantTable->name,
                ]),
            'order' => $order ? $this->formatOrderPayload($order) : null,
            'tables' => $this->buildTablesPayload(),
        ]);
    }

    public function transferTable(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_table_id' => ['required', 'integer', 'exists:restaurant_tables,id'],
        ]);

        $destinationTableId = (int) $validated['restaurant_table_id'];

        try {
            $order = DB::transaction(function () use ($order, $destinationTableId) {
                $managedOrder = Order::query()
                    ->whereKey($order->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $managedOrder->order_type !== 'dine_in'
                    || ! $managedOrder->restaurant_table_id
                    || ! $this->isActiveDineInStatus($managedOrder->status)
                ) {
                    throw ValidationException::withMessages([
                        'restaurant_table_id' => __('messages.errors.order_not_transferable'),
                    ]);
                }

                $sourceTableId = (int) $managedOrder->restaurant_table_id;

                if ($sourceTableId === $destinationTableId) {
                    throw ValidationException::withMessages([
                        'restaurant_table_id' => __('messages.errors.transfer_same_table'),
                    ]);
                }

                $tableIdsToLock = [$sourceTableId, $destinationTableId];
                sort($tableIdsToLock);

                $lockedTables = RestaurantTable::query()
                    ->whereIn('id', $tableIdsToLock)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get(['id', 'status'])
                    ->keyBy('id');

                if (! $lockedTables->has($destinationTableId)) {
                    throw ValidationException::withMessages([
                        'restaurant_table_id' => __('messages.errors.destination_table_not_available'),
                    ]);
                }

                $destinationTable = $lockedTables->get($destinationTableId);

                if (! $destinationTable || $destinationTable->status !== 'available') {
                    throw ValidationException::withMessages([
                        'restaurant_table_id' => __('messages.errors.destination_table_not_available'),
                    ]);
                }

                if ($this->hasActiveDineInOrder($destinationTableId, $managedOrder->id)) {
                    throw ValidationException::withMessages([
                        'restaurant_table_id' => __('messages.errors.destination_table_not_available'),
                    ]);
                }

                $managedOrder->update([
                    'restaurant_table_id' => $destinationTableId,
                ]);

                $this->syncTableStatuses([$sourceTableId, $destinationTableId]);

                return $managedOrder->load(['items', 'restaurantTable']);
            });
        } catch (QueryException $exception) {
            if ($this->isActiveTableGuardUniqueViolation($exception)) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => __('messages.errors.destination_table_not_available'),
                ]);
            }

            throw $exception;
        }

        return response()->json([
            'message' => __('messages.success.table_transferred', [
                'order_number' => $order->order_number,
                'table' => $order->restaurantTable?->name,
            ]),
            'order' => $this->formatOrderPayload($order),
            'tables' => $this->buildTablesPayload(),
        ]);
    }

    public function lookupCustomers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $term = trim((string) ($validated['phone'] ?? ''));

        if ($term === '') {
            return response()->json([
                'customers' => [],
            ]);
        }

        $customers = Customer::query()
            ->whereNotNull('phone')
            ->where('phone', 'like', "%{$term}%")
            ->orderByRaw('CASE WHEN phone = ? THEN 0 ELSE 1 END', [$term])
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get(['id', 'phone', 'first_name', 'address'])
            ->map(static fn(Customer $customer): array => [
                'id' => (int) $customer->id,
                'phone' => (string) $customer->phone,
                'name' => $customer->full_name,
                'address' => (string) ($customer->address ?? ''),
            ])
            ->values();

        return response()->json([
            'customers' => $customers,
        ]);
    }

    public function tablesStatus(): JsonResponse
    {
        return response()->json([
            'tables' => $this->buildTablesPayload(),
        ]);
    }

    public function lookupBarcode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
        ]);

        $product = Product::query()
            ->where('barcode', $validated['code'])
            ->where('is_active', true)
            ->first(['id', 'name', 'price', 'category_id', 'preparation_station']);

        if (! $product) {
            return response()->json(['found' => false, 'product' => null]);
        }

        return response()->json([
            'found' => true,
            'product' => [
                'id' => (int) $product->id,
                'name' => (string) $product->name,
                'price' => (float) $product->price,
                'category_id' => (int) $product->category_id,
                'preparation_station' => (string) $product->preparation_station,
            ],
        ]);
    }

    public function store(Request $request, \App\Services\PrintService $printService): JsonResponse
    {
        $supportsOrderCustomer = Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_id');
        $supportsDeliveryEmployee = Schema::hasTable('orders') && Schema::hasColumn('orders', 'delivery_employee_id');

        $validated = $request->validate([
            'order_type' => ['required', 'in:dine_in,takeaway,delivery'],
            'restaurant_table_id' => [
                'nullable',
                'required_if:order_type,dine_in',
                'integer',
                Rule::exists('restaurant_tables', 'id'),
            ],
            'active_order_id' => ['nullable', 'integer', 'exists:orders,order_serial'],
            'discount_type' => ['nullable', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'customer_phone' => ['nullable', 'required_if:order_type,delivery', 'string', 'max:30'],
            'customer_name' => ['nullable', 'required_if:order_type,delivery', 'string', 'max:160'],
            'customer_address' => ['nullable', 'required_if:order_type,delivery', 'string', 'max:255'],
            'delivery_employee_id' => ['nullable', 'required_if:order_type,delivery', 'integer'],
            'payment_method' => ['nullable', 'in:cash,visa,instapay,wallet'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = (float) ($validated['discount_value'] ?? 0);
        $couponCode = $validated['coupon_code'] ?? null;
        $itemsInput = $validated['items'];
        $restaurantTableId = $validated['order_type'] === 'dine_in'
            ? (int) ($validated['restaurant_table_id'] ?? 0)
            : null;
        $activeOrderId = isset($validated['active_order_id'])
            ? (int) $validated['active_order_id']
            : null;
        $cashierInventoryRecipeLinkEnabled = $this->isCashierInventoryRecipeLinkEnabled();

        if ($couponCode && $discountValue > 0) {
            throw ValidationException::withMessages([
                'discount_value' => __('messages.errors.manual_discount_with_coupon'),
            ]);
        }

        if ($activeOrderId && ! $restaurantTableId) {
            throw ValidationException::withMessages([
                'active_order_id' => __('messages.errors.active_order_requires_table'),
            ]);
        }

        if (($validated['order_type'] ?? null) === 'delivery' && ! $supportsOrderCustomer) {
            throw ValidationException::withMessages([
                'customer_phone' => __('messages.errors.delivery_customer_feature_unavailable'),
            ]);
        }

        if (($validated['order_type'] ?? null) === 'delivery' && ! $supportsDeliveryEmployee) {
            throw ValidationException::withMessages([
                'delivery_employee_id' => __('messages.errors.delivery_employee_feature_unavailable'),
            ]);
        }

        $deliveryEmployee = null;

        if (($validated['order_type'] ?? null) === 'delivery' && $supportsDeliveryEmployee) {
            $deliveryEmployee = $this->resolveDeliveryEmployee((int) ($validated['delivery_employee_id'] ?? 0));
        }

        try {
            $result = DB::transaction(function () use ($validated, $itemsInput, $discountType, $discountValue, $couponCode, $restaurantTableId, $activeOrderId, $supportsOrderCustomer, $deliveryEmployee, $cashierInventoryRecipeLinkEnabled) {
                $targetOrder = null;
                $deliveryCustomer = null;

                if (($validated['order_type'] ?? null) === 'delivery' && $supportsOrderCustomer) {
                    $deliveryCustomer = $this->resolveDeliveryCustomer(
                        phone: (string) ($validated['customer_phone'] ?? ''),
                        name: (string) ($validated['customer_name'] ?? ''),
                        address: (string) ($validated['customer_address'] ?? ''),
                    );
                }

                if ($restaurantTableId) {
                    $managedTable = RestaurantTable::query()
                        ->whereKey($restaurantTableId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $activeTableOrder = $this->findActiveDineInOrder($restaurantTableId, true);

                    if (! $activeTableOrder && $managedTable->status !== 'available') {
                        throw ValidationException::withMessages([
                            'restaurant_table_id' => __('messages.errors.table_not_available'),
                        ]);
                    }

                    if ($activeOrderId && (! $activeTableOrder || $activeTableOrder->order_serial !== $activeOrderId)) {
                        throw ValidationException::withMessages([
                            'active_order_id' => __('messages.errors.active_order_not_found_for_table'),
                        ]);
                    }

                    if ($activeTableOrder) {
                        $targetOrder = $activeTableOrder;
                    }
                }

                $productIds = collect($itemsInput)->pluck('product_id')->unique()->values();

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->where('is_active', true)
                    ->get(['id', 'name', 'price', 'preparation_station'])
                    ->keyBy('id');

                if ($products->count() !== $productIds->count()) {
                    throw ValidationException::withMessages([
                        'items' => __('messages.errors.products_not_available'),
                    ]);
                }

                $legacyRecipeProductIds = RecipeItem::query()
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->map(static fn($id): int => (int) $id)
                    ->unique()
                    ->flip();

                $orderItems = [];
                $subtotal = 0;

                $inventoryItems = [];
                $resolvedVersionIdCache = [];

                foreach ($itemsInput as $item) {
                    $product = $products->get($item['product_id']);
                    $quantity = (int) $item['quantity'];
                    $unitPrice = (float) $product->price;
                    $lineTotal = $unitPrice * $quantity;
                    $itemNotes = trim((string) ($item['notes'] ?? ''));
                    $cacheKey = (int) $product->id;

                    if (! array_key_exists($cacheKey, $resolvedVersionIdCache)) {
                        $resolvedVersionIdCache[$cacheKey] = $this->resolveSnapshotRecipeVersion(
                            productId: (int) $product->id,
                        )?->id;
                    }

                    $recipeVersionId = $resolvedVersionIdCache[$cacheKey]
                        ? (int) $resolvedVersionIdCache[$cacheKey]
                        : null;

                    if ($cashierInventoryRecipeLinkEnabled && ! $recipeVersionId && ! $legacyRecipeProductIds->has($cacheKey)) {
                        throw ValidationException::withMessages([
                            'items' => __('messages.errors.product_requires_recipe', [
                                'name' => $product->name,
                            ]),
                        ]);
                    }

                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'recipe_version_id' => $recipeVersionId,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => round($unitPrice, 2),
                        'quantity' => $quantity,
                        'line_total' => round($lineTotal, 2),
                        'notes' => $itemNotes !== '' ? $itemNotes : null,
                        'preparation_station' => $product->preparation_station === 'bar' ? 'bar' : 'kitchen',
                    ];

                    $inventoryItems[] = [
                        'recipe_version_id' => $recipeVersionId,
                        'product_id' => (int) $product->id,
                        'quantity' => $quantity,
                    ];
                }

                if ($cashierInventoryRecipeLinkEnabled) {
                    $stockIssues = $this->inventoryService->validateOrderStock(
                        items: $inventoryItems,
                        warehouseId: $this->inventoryService->orderConsumptionWarehouseId(),
                        requireRecipe: true,
                    );

                    if (! empty($stockIssues)) {
                        throw ValidationException::withMessages([
                            'items' => $stockIssues,
                        ]);
                    }
                }

                if ($targetOrder) {
                    if ($discountValue > 0 || $couponCode) {
                        throw ValidationException::withMessages([
                            'discount_value' => __('messages.errors.cannot_change_discount_for_active_order'),
                        ]);
                    }

                    $nextKitchenBatch = ((int) ($targetOrder->items()->max('kitchen_batch') ?? 0)) + 1;

                    if ($nextKitchenBatch < 1) {
                        $nextKitchenBatch = 1;
                    }

                    $batchedOrderItems = array_map(
                        static fn(array $orderItem): array => array_merge($orderItem, [
                            'kitchen_status' => 'pending',
                            'kitchen_batch' => $nextKitchenBatch,
                        ]),
                        $orderItems,
                    );

                    $targetOrder->items()->createMany($batchedOrderItems);

                    $targetOrder->update([
                        'subtotal' => round((float) $targetOrder->subtotal + $subtotal, 2),
                        'total' => round((float) $targetOrder->total + $subtotal, 2),
                        'kitchen_status' => 'pending',
                        'notes' => $this->mergeOrderNotes($targetOrder->notes, $validated['notes'] ?? null),
                    ]);

                    $this->syncTableStatus($restaurantTableId);

                    return [
                        'order' => $targetOrder->load(['items', 'restaurantTable']),
                        'updated' => true,
                    ];
                }

                $manualDiscountAmount = match ($discountType) {
                    'percentage' => ($subtotal * $discountValue) / 100,
                    'fixed' => $discountValue,
                    default => 0,
                };

                $manualDiscountAmount = min($manualDiscountAmount, $subtotal);

                $couponResult = $this->promotionService->validateCoupon($couponCode, $subtotal, true);
                $appliedCoupon = $couponResult['coupon'] ?? null;
                $couponAppliedAmount = (float) ($couponResult['discount_amount'] ?? 0);

                $discountAmount = $manualDiscountAmount + $couponAppliedAmount;

                $discountAmount = min($discountAmount, $subtotal);
                $total = max($subtotal - $discountAmount, 0);

                $orderNumber = $this->generateOrderNumber();
                $orderDailyNumber = $this->generateOrderDailyNumber();
                $activeShiftId = $this->resolveActiveShiftIdForUser((int) Auth::id());

                $order = Order::query()->create([
                    'order_number' => $orderNumber,
                    'order_daily_number' => $orderDailyNumber,
                    'user_id' => Auth::id(),
                    'shift_id' => $activeShiftId,
                    'customer_id' => $deliveryCustomer?->id,
                    'delivery_employee_id' => $deliveryEmployee?->id,
                    'coupon_id' => $appliedCoupon?->id,
                    'offer_id' => null,
                    'coupon_code' => $appliedCoupon?->code,
                    'offer_name' => null,
                    'order_type' => $validated['order_type'],
                    'restaurant_table_id' => $restaurantTableId,
                    'discount_type' => $discountType,
                    'discount_value' => round($discountValue, 2),
                    'subtotal' => round($subtotal, 2),
                    'discount_amount' => round($discountAmount, 2),
                    'total' => round($total, 2),
                    'status' => $restaurantTableId ? 'pending' : 'paid',
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'kitchen_status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);

                $trackedOrderItems = array_map(
                    static fn(array $orderItem): array => array_merge($orderItem, [
                        'kitchen_status' => 'pending',
                        'kitchen_batch' => 1,
                    ]),
                    $orderItems,
                );

                $order->items()->createMany($trackedOrderItems);

                if ($order->status === 'paid' && $cashierInventoryRecipeLinkEnabled) {
                    $this->inventoryService->deductInventoryForOrder($order, true);
                }

                if ($restaurantTableId) {
                    $this->syncTableStatus($restaurantTableId);
                }

                if ($appliedCoupon && $couponAppliedAmount > 0) {
                    $appliedCoupon->redemptions()->create([
                        'order_id' => $order->order_serial,
                        'user_id' => Auth::id(),
                        'discount_amount' => round($couponAppliedAmount, 2),
                        'redeemed_at' => now(),
                    ]);

                    $appliedCoupon->increment('used_count');
                }

                return [
                    'order' => $order->load(['items', 'restaurantTable']),
                    'updated' => false,
                ];
            });
        } catch (QueryException $exception) {
            if ($this->isActiveTableGuardUniqueViolation($exception)) {
                throw ValidationException::withMessages([
                    'restaurant_table_id' => __('messages.errors.table_has_active_order'),
                ]);
            }

            throw $exception;
        }

        /** @var \App\Models\Order $order */
        $order = $result['order'];
        $updated = (bool) $result['updated'];

        // ── Auto-print via QZ Tray print queue ──────────────────────────────
        try {
            $latestBatch = $updated ? (int) $order->items->max('kitchen_batch') : null;
            \App\Models\PrintJob::create([
                'printer_type'   => 'bar',
                'payload'        => json_encode([
                    'order_serial'  => $order->order_serial,
                    'label'         => $updated ? 'ADD ITEMS' : 'NEW ORDER',
                    'kitchen_batch' => $latestBatch,
                ]),
                'payload_type'   => 'json',
                'status'         => 'pending',
                'printable_type' => \App\Models\Order::class,
                'printable_id'   => $order->order_serial,
            ]);

            // Cashier receipt for takeaway/delivery immediately on order creation
            if (!$updated && (string) $order->order_type !== 'dine_in') {
                \App\Models\PrintJob::create([
                    'printer_type'   => 'cashier',
                    'payload'        => json_encode(['order_serial' => $order->order_serial]),
                    'payload_type'   => 'json',
                    'status'         => 'pending',
                    'printable_type' => \App\Models\Order::class,
                    'printable_id'   => $order->order_serial,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PosController: failed to queue print job', [
                'order_number' => $order->order_number,
                'error'        => $e->getMessage(),
            ]);
        }
        // ─────────────────────────────────────────────────────────────────────

        $message = $updated
            ? __('messages.success.order_updated')
            : __('messages.success.order_placed');

        return response()->json([
            'message' => $message,
            'order' => $this->formatOrderPayload($order),
            'tables' => $this->buildTablesPayload(),
            'updated' => $updated,
        ], $updated ? 200 : 201);
    }

    private function resolveDeliveryCustomer(string $phone, string $name, string $address): Customer
    {
        $normalizedPhone = trim($phone);
        $normalizedName = trim($name) !== '' ? trim($name) : 'Customer';
        $normalizedAddress = trim($address);

        $customer = Customer::query()
            ->where('phone', $normalizedPhone)
            ->lockForUpdate()
            ->first();

        if ($customer) {
            $updates = [];

            if ((string) $customer->first_name !== $normalizedName) {
                $updates['first_name'] = $normalizedName;
            }

            if ((string) ($customer->address ?? '') !== $normalizedAddress) {
                $updates['address'] = $normalizedAddress;
            }

            if (! $customer->is_active) {
                $updates['is_active'] = true;
            }

            if (! empty($updates)) {
                $customer->update($updates);
            }

            return $customer->fresh();
        }

        $payload = [
            'first_name' => $normalizedName,
            'phone' => $normalizedPhone,
            'address' => $normalizedAddress,
            'is_active' => true,
        ];

        return Customer::query()->create($payload);
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function generateOrderDailyNumber(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $max = Order::query()
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->max('order_daily_number');

        return ($max ?? 0) + 1;
    }

    private function buildTablesPayload(): array
    {
        $activeOrdersByTable = Order::query()
            ->where('order_type', 'dine_in')
            ->whereNotNull('restaurant_table_id')
            ->whereIn('status', Order::activeDineInStatuses())
            ->latest('order_serial')
            ->get(['order_serial', 'order_number', 'restaurant_table_id'])
            ->groupBy('restaurant_table_id')
            ->map(static fn($orders) => $orders->first());

        return RestaurantTable::query()
            ->orderByRaw('CAST(name AS UNSIGNED), name')
            ->get(['id', 'name', 'status'])
            ->map(static function (RestaurantTable $table) use ($activeOrdersByTable): array {
                $activeOrder = $activeOrdersByTable->get((int) $table->id);
                $hasActiveOrder = (bool) $activeOrder;

                // Determine effective status:
                // - If there is an active dine-in order → occupied (regardless of DB flag)
                // - Otherwise keep the actual DB status (available / reserved / occupied)
                if ($hasActiveOrder) {
                    $effectiveStatus = 'occupied';
                } elseif ($table->status === 'occupied') {
                    // DB says occupied but no active order → treat as available
                    $effectiveStatus = 'available';
                } else {
                    $effectiveStatus = $table->status; // 'available' or 'reserved'
                }

                return [
                    'id' => $table->id,
                    'name' => $table->name,
                    'status' => $effectiveStatus,
                    'active_order_id' => $activeOrder?->order_serial,
                    'active_order_number' => $activeOrder?->order_number,
                ];
            })
            ->values()
            ->all();
    }

    private function formatOrderPayload(Order $order): array
    {
        $order->loadMissing(['items', 'restaurantTable', 'deliveryEmployee:id,first_name,last_name']);

        return [
            'id' => $order->order_serial,
            'order_number' => $order->order_number,
            'order_daily_number' => (int) $order->order_daily_number,
            'status' => $order->status,
            'coupon_code' => $order->coupon_code,
            'offer_name' => $order->offer_name,
            'restaurant_table_id' => $order->restaurant_table_id,
            'restaurant_table_name' => $order->restaurantTable?->name,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'total' => (float) $order->total,
            'delivery_employee_id' => $order->delivery_employee_id ? (int) $order->delivery_employee_id : null,
            'delivery_employee_name' => $order->deliveryEmployee?->full_name,
            'notes' => $order->notes,
            'items_count' => $order->items->count(),
            'items' => $order->items
                ->map(static fn($item): array => [
                    'id' => $item->id,
                    'recipe_version_id' => $item->recipe_version_id ? (int) $item->recipe_version_id : null,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'line_total' => (float) $item->line_total,
                    'notes' => $item->notes,
                ])
                ->values()
                ->all(),
        ];
    }

    private function findActiveDineInOrder(int $tableId, bool $lock = false): ?Order
    {
        $query = Order::query()
            ->where('order_type', 'dine_in')
            ->where('restaurant_table_id', $tableId)
            ->whereIn('status', Order::activeDineInStatuses())
            ->latest('order_serial');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function hasActiveDineInOrder(int $tableId, ?int $exceptOrderId = null): bool
    {
        return Order::query()
            ->where('order_type', 'dine_in')
            ->where('restaurant_table_id', $tableId)
            ->whereIn('status', Order::activeDineInStatuses())
            ->when($exceptOrderId, fn($query, int $orderId) => $query->where('order_serial', '!=', $orderId))
            ->exists();
    }

    private function syncTableStatus(?int $tableId): void
    {
        $this->syncTableStatuses([$tableId]);
    }

    private function syncTableStatuses(array $tableIds): void
    {
        $normalizedTableIds = collect($tableIds)
            ->filter()
            ->map(static fn($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($normalizedTableIds)) {
            return;
        }

        $sync = function () use ($normalizedTableIds): void {
            RestaurantTable::query()
                ->whereIn('id', $normalizedTableIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            $activeTableIds = Order::query()
                ->where('order_type', 'dine_in')
                ->whereIn('restaurant_table_id', $normalizedTableIds)
                ->whereIn('status', Order::activeDineInStatuses())
                ->pluck('restaurant_table_id')
                ->map(static fn($id): int => (int) $id)
                ->unique()
                ->all();

            $activeTableMap = array_fill_keys($activeTableIds, true);

            foreach ($normalizedTableIds as $normalizedTableId) {
                $newStatus = isset($activeTableMap[$normalizedTableId]) ? 'occupied' : 'available';
                RestaurantTable::query()
                    ->whereKey($normalizedTableId)
                    ->where('status', '!=', 'reserved')
                    ->update(['status' => $newStatus]);
            }
        };

        if (DB::transactionLevel() > 0) {
            $sync();

            return;
        }

        DB::transaction($sync);
    }

    private function isActiveDineInStatus(?string $status): bool
    {
        return in_array((string) $status, Order::activeDineInStatuses(), true);
    }

    private function isActiveTableGuardUniqueViolation(QueryException $exception): bool
    {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1062
            && str_contains($exception->getMessage(), 'orders_active_table_guard_unique');
    }

    private function mergeOrderNotes(?string $existing, ?string $incoming): ?string
    {
        $normalizedExisting = trim((string) $existing);
        $normalizedIncoming = trim((string) $incoming);

        if ($normalizedIncoming === '') {
            return $normalizedExisting !== '' ? $normalizedExisting : null;
        }

        if ($normalizedExisting === '') {
            return $normalizedIncoming;
        }

        return $normalizedExisting . PHP_EOL . $normalizedIncoming;
    }

    private function activeShiftPayload(): ?array
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return null;
        }

        $activeShift = CashierShift::query()
            ->where('user_id', (int) Auth::id())
            ->where('status', 'open')
            ->latest('id')
            ->first();

        return $activeShift ? $this->formatShiftPayload($activeShift) : null;
    }

    private function formatShiftPayload(CashierShift $shift): array
    {
        $difference = $shift->difference !== null ? (float) $shift->difference : null;
        $cashOverage = $difference !== null ? round(max($difference, 0), 2) : 0.0;
        $cashShortage = $difference !== null ? round(max(-1 * $difference, 0), 2) : 0.0;

        return [
            'id' => (int) $shift->id,
            'opening_cash' => (float) $shift->opening_cash,
            'start_time' => $shift->start_time?->toIso8601String(),
            'end_time' => $shift->end_time?->toIso8601String(),
            'total_sales' => (float) ($shift->total_sales ?? 0),
            'expected_cash' => (float) ($shift->expected_cash ?? 0),
            'actual_cash' => $shift->actual_cash !== null ? (float) $shift->actual_cash : null,
            'tips' => (float) ($shift->tips ?? 0),
            'difference' => $difference,
            'cash_overage' => $cashOverage,
            'cash_shortage' => $cashShortage,
            'status' => (string) $shift->status,
        ];
    }

    private function buildShiftReceiptPayload(CashierShift $shift, string $cashierName): array
    {
        $difference = (float) ($shift->difference ?? 0);

        return [
            'cashier_name' => trim($cashierName) !== '' ? trim($cashierName) : 'System',
            'start_time' => $shift->start_time?->toIso8601String(),
            'end_time' => $shift->end_time?->toIso8601String(),
            'opening_cash' => (float) $shift->opening_cash,
            'total_sales' => (float) ($shift->total_sales ?? 0),
            'expected_cash' => (float) ($shift->expected_cash ?? 0),
            'actual_cash' => (float) ($shift->actual_cash ?? 0),
            'difference' => $difference,
            'cash_overage' => round(max($difference, 0), 2),
            'cash_shortage' => round(max(-1 * $difference, 0), 2),
            'tips' => (float) ($shift->tips ?? 0),
            'labels' => [
                'title' => __('ui.pos.shift.receipt_title'),
                'cashier' => __('ui.pos.shift.cashier_name'),
                'shift_time' => __('ui.pos.shift.shift_time'),
                'opening_cash' => __('ui.pos.shift.opening_cash'),
                'total_sales' => __('ui.pos.shift.total_sales'),
                'expected_cash' => __('ui.pos.shift.expected_cash'),
                'actual_cash' => __('ui.pos.shift.actual_cash'),
                'difference' => __('ui.pos.shift.difference'),
                'cash_overage' => __('ui.pos.shift.cash_overage'),
                'cash_shortage' => __('ui.pos.shift.cash_shortage'),
                'tips' => __('ui.pos.shift.tips'),
                'tips_note' => __('ui.pos.shift.tips_note'),
            ],
        ];
    }

    private function isOpenShiftGuardUniqueViolation(QueryException $exception): bool
    {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1062
            && str_contains($exception->getMessage(), 'cashier_shifts_open_shift_guard_unique');
    }

    private function resolveActiveShiftIdForUser(int $userId): ?int
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return null;
        }

        return CashierShift::query()
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('id')
            ->value('id');
    }

    private function supportsShiftClosingFields(): bool
    {
        return Schema::hasColumn('cashier_shifts', 'end_time')
            && Schema::hasColumn('cashier_shifts', 'total_sales')
            && Schema::hasColumn('cashier_shifts', 'expected_cash')
            && Schema::hasColumn('cashier_shifts', 'actual_cash')
            && Schema::hasColumn('cashier_shifts', 'tips')
            && Schema::hasColumn('cashier_shifts', 'difference');
    }

    private function supportsShiftLogs(): bool
    {
        return Schema::hasTable('shift_logs')
            && Schema::hasColumn('shift_logs', 'user_id')
            && Schema::hasColumn('shift_logs', 'shift_start')
            && Schema::hasColumn('shift_logs', 'shift_end')
            && Schema::hasColumn('shift_logs', 'cash_difference');
    }

    private function isCashierInventoryRecipeLinkEnabled(): bool
    {
        return (bool) config('features.cashier_inventory_recipe_link_enabled', false);
    }

    private function resolveSnapshotRecipeVersion(int $productId): ?RecipeVersion
    {
        return RecipeVersion::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('is_semi_finished', false)
            ->latest('id')
            ->first();
    }

    private function deliveryEmployeesPayload(): array
    {
        return Employee::query()
            ->where('status', 'active')
            ->where(function ($query): void {
                $query
                    ->where('position', 'like', '%ديلفري%')
                    ->orWhere('position', 'like', '%Delivery%')
                    ->orWhere('position', 'like', '%delivery%');
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name'])
            ->map(static function (Employee $employee): array {
                return [
                    'id' => (int) $employee->id,
                    'label' => trim($employee->employee_code . ' - ' . $employee->full_name),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveDeliveryEmployee(int $employeeId): Employee
    {
        $deliveryEmployee = Employee::query()
            ->whereKey($employeeId)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query
                    ->where('position', 'like', '%ديلفري%')
                    ->orWhere('position', 'like', '%Delivery%')
                    ->orWhere('position', 'like', '%delivery%');
            })
            ->first();

        if (! $deliveryEmployee) {
            throw ValidationException::withMessages([
                'delivery_employee_id' => __('messages.errors.delivery_employee_invalid'),
            ]);
        }

        return $deliveryEmployee;
    }
}
