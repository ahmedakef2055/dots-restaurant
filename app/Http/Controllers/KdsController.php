<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class KdsController extends Controller
{
    private const STATION_KITCHEN = 'kitchen';
    private const STATION_BAR = 'bar';
    private const READY_AUTO_HIDE_MINUTES = 5;
    private const AUTO_HIDE_READY_ORDER_TYPES = ['delivery', 'takeaway'];

    public function index(): View
    {
        return $this->renderBoard(self::STATION_KITCHEN);
    }

    public function barIndex(): View
    {
        return $this->renderBoard(self::STATION_BAR);
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'ordersByStage' => $this->buildOrdersByStage(self::STATION_KITCHEN),
        ]);
    }

    public function barData(): JsonResponse
    {
        return response()->json([
            'ordersByStage' => $this->buildOrdersByStage(self::STATION_BAR),
        ]);
    }

    public function transition(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        return $this->transitionForStation($request, $order, self::STATION_KITCHEN);
    }

    public function barTransition(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        return $this->transitionForStation($request, $order, self::STATION_BAR);
    }

    private function transitionForStation(
        Request $request,
        Order $order,
        string $station,
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'action'        => ['required', 'in:start,done,back,handoff'],
            'kitchen_batch' => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($order, $validated, $station): void {
                $managedOrder = Order::query()
                    ->whereKey($order->order_serial)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($managedOrder->status === 'cancelled') {
                    throw new RuntimeException(__('messages.errors.cancelled_order_cannot_be_processed'));
                }

                if ($managedOrder->status === 'paid' && ! $this->canProcessPaidOrderOnStations($managedOrder)) {
                    throw new RuntimeException(__('messages.errors.paid_order_cannot_be_processed'));
                }

                $kitchenBatch = (int) $validated['kitchen_batch'];

                $batchItemsQuery = OrderItem::query()
                    ->where('order_id', $managedOrder->order_serial)
                    ->where('kitchen_batch', $kitchenBatch);

                $this->applyStationFilterToItems($batchItemsQuery, $station);

                $batchItems = $batchItemsQuery
                    ->lockForUpdate()
                    ->get(['id', 'kitchen_status']);

                if ($batchItems->isEmpty()) {
                    throw new RuntimeException(__('messages.errors.invalid_kds_transition'));
                }

                $currentKitchenStatus = $this->resolveBatchStage($batchItems);
                $nextKitchenStatus = $this->resolveNextKitchenStatus(
                    $currentKitchenStatus,
                    $validated['action'],
                );

                if (! $nextKitchenStatus) {
                    throw new RuntimeException(__('messages.errors.invalid_kds_transition'));
                }

                $batchItemsUpdateQuery = OrderItem::query()
                    ->where('order_id', $managedOrder->order_serial)
                    ->where('kitchen_batch', $kitchenBatch);

                $this->applyStationFilterToItems($batchItemsUpdateQuery, $station);

                $batchItemsUpdateQuery
                    ->update([
                        'kitchen_status' => $nextKitchenStatus,
                    ]);

                $this->syncOrderKitchenStatus($managedOrder);
            });
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return back()->with('error', $exception->getMessage());
        }

        $message = $validated['action'] === 'handoff'
            ? __('messages.success.kds_order_served')
            : __('messages.success.kds_status_updated');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'ordersByStage' => $this->buildOrdersByStage($station),
            ]);
        }

        return back()->with('success', $message);
    }

    private function renderBoard(string $station): View
    {
        $isBarBoard = $station === self::STATION_BAR;

        return view('kds.index', [
            'boardTitle' => $isBarBoard ? __('ui.bar.title') : __('ui.kds.title'),
            'boardSubtitle' => $isBarBoard ? __('ui.bar.subtitle') : __('ui.kds.subtitle'),
            'stageConfigs' => $this->stageConfigs(),
            'initialBoard' => $this->buildOrdersByStage($station),
            'fetchEndpoint' => $isBarBoard ? route('bar.data') : route('kds.data'),
            'transitionTemplate' => $isBarBoard
                ? route('bar.orders.transition', ['order' => '__ORDER__'])
                : route('kds.orders.transition', ['order' => '__ORDER__']),
            'pollingMs' => 2500,
        ]);
    }

    private function buildOrdersByStage(string $station): array
    {
        $orders = Order::query()
            ->with([
                'items' => function ($query) use ($station): void {
                    $query->select([
                        'id',
                        'order_id',
                        'product_name',
                        'quantity',
                        'notes',
                        'kitchen_status',
                        'kitchen_batch',
                        'preparation_station',
                        'created_at',
                        'updated_at',
                    ]);

                    $this->applyStationFilterToItems($query, $station);
                },
                'restaurantTable:id,name',
            ])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query): void {
                $query
                    ->where('status', '!=', 'paid')
                    ->orWhereIn('order_type', self::AUTO_HIDE_READY_ORDER_TYPES);
            })
            ->where('created_at', '>=', now()->subDay())
            ->latest('order_serial')
            ->get([
                'order_serial',
                'order_number',
                'order_type',
                'restaurant_table_id',
                'status',
                'notes',
                'created_at',
            ]);

        return $this->groupTicketsByStage($orders, $station);
    }

    private function stageConfigs(): array
    {
        return [
            [
                'key' => 'pending',
                'title' => __('ui.kds.stages.pending'),
                'icon' => 'clock',
                'icon_class' => 'text-amber-500',
            ],
            [
                'key' => 'preparing',
                'title' => __('ui.kds.stages.preparing'),
                'icon' => 'chef',
                'icon_class' => 'text-blue-500',
            ],
            [
                'key' => 'ready',
                'title' => __('ui.kds.stages.ready'),
                'icon' => 'check',
                'icon_class' => 'text-emerald-500',
            ],
        ];
    }

    private function applyStationFilterToItems($query, string $station): void
    {
        if ($station === self::STATION_BAR) {
            $query->where('preparation_station', self::STATION_BAR);

            return;
        }

        $query->where(function ($nested): void {
            $nested
                ->where('preparation_station', self::STATION_KITCHEN)
                ->orWhereNull('preparation_station');
        });
    }

    private function groupTicketsByStage(Collection $orders, string $station): array
    {
        $grouped = collect(Order::kitchenStatuses())
            ->mapWithKeys(static fn(string $status): array => [$status => []])
            ->all();

        foreach ($orders as $order) {
            if ($order->items->isEmpty()) {
                continue;
            }

            $itemsByBatch = $order->items
                ->groupBy(static fn($item): int => ((int) ($item->kitchen_batch ?? 1)) > 0
                    ? (int) ($item->kitchen_batch ?? 1)
                    : 1)
                ->sortKeys();

            foreach ($itemsByBatch as $batch => $batchItems) {
                if ($batchItems->isEmpty()) {
                    continue;
                }

                $stage = $this->resolveBatchStage($batchItems);

                // Never show served tickets on the board
                if ($stage === 'served') {
                    continue;
                }
                $batchCreatedAt = $batchItems->sortBy('created_at')->first()?->created_at ?? $order->created_at;
                $batchReadyAt = $batchItems->sortByDesc('updated_at')->first()?->updated_at;

                if ($this->shouldHideReadyTicket((string) $order->order_type, $stage, $batchReadyAt)) {
                    continue;
                }

                $grouped[$stage][] = [
                    'id' => sprintf('%d-%d-%s', $order->order_serial, (int) $batch, $station),
                    'order_id' => (int) $order->order_serial,
                    'order_number' => $order->order_number,
                    'order_type' => $order->order_type,
                    'source_label' => $order->restaurantTable?->name
                        ?? Str::upper(str_replace('_', ' ', (string) $order->order_type)),
                    'order_notes' => $order->notes,
                    'kitchen_batch' => (int) $batch,
                    'created_at' => $batchCreatedAt?->toIso8601String(),
                    'created_at_human' => $batchCreatedAt?->diffForHumans() ?? '',
                    'items' => $batchItems
                        ->values()
                        ->map(static fn($item): array => [
                            'id' => (int) $item->id,
                            'product_name' => $item->product_name,
                            'quantity' => (int) $item->quantity,
                            'notes' => $item->notes,
                        ])
                        ->all(),
                ];
            }
        }

        foreach (Order::kitchenStatuses() as $status) {
            $grouped[$status] = collect($grouped[$status])
                ->sortBy(static fn(array $ticket): string => (string) ($ticket['created_at'] ?? ''))
                ->values()
                ->all();
        }

        return $grouped;
    }

    private function shouldHideReadyTicket(string $orderType, string $stage, mixed $readyAt): bool
    {
        if ($stage !== 'ready') {
            return false;
        }

        if (! in_array($orderType, self::AUTO_HIDE_READY_ORDER_TYPES, true)) {
            return false;
        }

        if (! $readyAt) {
            return false;
        }

        return $readyAt->lessThanOrEqualTo(now()->subMinutes(self::READY_AUTO_HIDE_MINUTES));
    }

    private function canProcessPaidOrderOnStations(Order $order): bool
    {
        return in_array((string) $order->order_type, self::AUTO_HIDE_READY_ORDER_TYPES, true);
    }

    private function resolveBatchStage(Collection $batchItems): string
    {
        $normalizedStatuses = $batchItems
            ->pluck('kitchen_status')
            ->map(fn($status): string => $this->normalizeKitchenStatus($status))
            ->unique()
            ->values();

        if ($normalizedStatuses->contains('pending')) {
            return 'pending';
        }

        if ($normalizedStatuses->contains('preparing')) {
            return 'preparing';
        }

        if ($normalizedStatuses->contains('ready')) {
            return 'ready';
        }

        if ($normalizedStatuses->contains('served')) {
            return 'served';
        }

        return 'pending';
    }

    private function syncOrderKitchenStatus(Order $order): void
    {
        $itemStatuses = OrderItem::query()
            ->where('order_id', $order->order_serial)
            ->pluck('kitchen_status')
            ->map(fn($status): string => $this->normalizeKitchenStatus($status));

        $nextStatus = 'pending';

        if ($itemStatuses->contains('pending')) {
            $nextStatus = 'pending';
        } elseif ($itemStatuses->contains('preparing')) {
            $nextStatus = 'preparing';
        } elseif ($itemStatuses->contains('ready')) {
            $nextStatus = 'ready';
        } elseif ($itemStatuses->every(fn($s): bool => $s === 'served')) {
            $nextStatus = 'served';
        }

        $order->update([
            'kitchen_status' => $nextStatus,
        ]);
    }

    private function normalizeKitchenStatus(?string $status): string
    {
        $normalized = Str::lower((string) $status);

        return in_array($normalized, Order::kitchenStatuses(), true)
            ? $normalized
            : 'pending';
    }

    private function resolveNextKitchenStatus(string $currentStatus, string $action): ?string
    {
        if ($action === 'start' && $currentStatus === 'pending') {
            return 'preparing';
        }

        if ($action === 'done' && $currentStatus === 'preparing') {
            return 'ready';
        }

        if ($action === 'handoff' && $currentStatus === 'ready') {
            return 'served';
        }

        if ($action !== 'back') {
            return null;
        }

        return match ($currentStatus) {
            'preparing' => 'pending',
            'ready'     => 'preparing',
            default     => null,
        };
    }
}
