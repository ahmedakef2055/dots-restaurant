<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\ShiftLog;
use App\Models\User;
use App\Support\PdfExportRenderer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{

    public function shiftLogs(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(29)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $logsBase = ShiftLog::query()
            ->with('user')
            ->whereBetween('shift_start', [$from, $to]);

        if (isset($validated['user_id'])) {
            $logsBase->where('user_id', (int) $validated['user_id']);
        }

        $shiftLogs = (clone $logsBase)
            ->latest('shift_start')
            ->paginate(20)
            ->withQueryString();

        $cashierIds = ShiftLog::query()
            ->select('user_id')
            ->distinct()
            ->pluck('user_id')
            ->all();

        $cashiers = User::query()
            ->whereIn('id', $cashierIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $canViewShiftLogProfile = $this->canAccessShiftLogProfile($request->user());

        return view('reports.shift-logs', [
            'shiftLogs' => $shiftLogs,
            'cashiers' => $cashiers,
            'canViewShiftLogProfile' => $canViewShiftLogProfile,
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'user_id' => isset($validated['user_id']) ? (int) $validated['user_id'] : null,
            ],
        ]);
    }

    public function shiftLogsExportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(29)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $shiftLogs = ShiftLog::query()
            ->with('user')
            ->whereBetween('shift_start', [$from, $to])
            ->when(isset($validated['user_id']), fn($q) => $q->where('user_id', (int) $validated['user_id']))
            ->latest('shift_start')
            ->get();

        return $pdfExportRenderer->downloadPdfFromView(
            'reports.exports.shift-logs-pdf',
            [
                'shiftLogs' => $shiftLogs,
                'filters' => [
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                ],
                'generatedAt' => now(),
            ],
            'shift-logs-' . now()->format('Ymd_His') . '.pdf',
            route('reports.shift-logs'),
        );
    }

    public function shiftLogProfile(Request $request, ShiftLog $shiftLog): View
    {
        if (! $this->canAccessShiftLogProfile($request->user())) {
            abort(403, __('messages.errors.permission_denied'));
        }

        $shiftLog->loadMissing('user:id,name');

        $cashierShift = $this->resolveCashierShiftFromLog($shiftLog);

        $orders = collect();
        $orderStats = [
            'total_orders' => 0,
            'paid_orders' => 0,
            'cancelled_orders' => 0,
            'dine_in_orders' => 0,
            'takeaway_orders' => 0,
            'delivery_orders' => 0,
            'gross_sales' => 0.0,
            'discount_amount' => 0.0,
            'net_sales' => 0.0,
        ];

        if ($cashierShift) {
            $ordersBase = Order::query()
                ->where('shift_id', (int) $cashierShift->id);

            $paidOrdersBase = (clone $ordersBase)->where('status', 'paid');

            $orderStats = [
                'total_orders' => (int) (clone $ordersBase)->count(),
                'paid_orders' => (int) (clone $paidOrdersBase)->count(),
                'cancelled_orders' => (int) (clone $ordersBase)->where('status', 'cancelled')->count(),
                'dine_in_orders' => (int) (clone $ordersBase)->where('order_type', 'dine_in')->count(),
                'takeaway_orders' => (int) (clone $ordersBase)->where('order_type', 'takeaway')->count(),
                'delivery_orders' => (int) (clone $ordersBase)->where('order_type', 'delivery')->count(),
                'gross_sales' => round((float) (clone $paidOrdersBase)->sum('subtotal'), 2),
                'discount_amount' => round((float) (clone $paidOrdersBase)->sum('discount_amount'), 2),
                'net_sales' => round((float) (clone $paidOrdersBase)->sum('total'), 2),
            ];

            $orders = (clone $ordersBase)
                ->latest('order_serial')
                ->get([
                    'order_serial',
                    'order_number',
                    'order_type',
                    'status',
                    'subtotal',
                    'discount_amount',
                    'total',
                    'created_at',
                ]);
        }

        $openingCash = round((float) ($cashierShift?->opening_cash ?? 0), 2);
        $totalPaidSales = $cashierShift
            ? round((float) ($cashierShift->total_sales ?? 0), 2)
            : (float) $orderStats['net_sales'];
        $expectedCash = $cashierShift
            ? round((float) ($cashierShift->expected_cash ?? 0), 2)
            : round($openingCash + $totalPaidSales, 2);
        $actualCash = $cashierShift && $cashierShift->actual_cash !== null
            ? round((float) $cashierShift->actual_cash, 2)
            : null;
        $tips = $cashierShift
            ? round((float) ($cashierShift->tips ?? 0), 2)
            : 0.0;
        $difference = $cashierShift && $cashierShift->difference !== null
            ? round((float) $cashierShift->difference, 2)
            : ($shiftLog->cash_difference !== null ? round((float) $shiftLog->cash_difference, 2) : null);
        $cashOverage = $difference !== null ? round(max($difference, 0), 2) : 0.0;
        $cashShortage = $difference !== null ? round(max(-1 * $difference, 0), 2) : 0.0;

        $settlementRows = collect([
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.opening_cash'),
                'value' => $openingCash,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.total_paid_sales'),
                'value' => $totalPaidSales,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.expected_cash'),
                'value' => $expectedCash,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.actual_cash'),
                'value' => $actualCash,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.cash_overage'),
                'value' => $cashOverage,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.cash_shortage'),
                'value' => $cashShortage,
            ],
            [
                'label' => __('ui.reports.shift_logs.profile.breakdown.tips'),
                'value' => $tips,
            ],
        ]);

        return view('reports.shift-log-profile', [
            'shiftLog' => $shiftLog,
            'cashierShift' => $cashierShift,
            'orders' => $orders,
            'orderStats' => $orderStats,
            'financials' => [
                'opening_cash' => $openingCash,
                'total_paid_sales' => $totalPaidSales,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
                'cash_overage' => $cashOverage,
                'cash_shortage' => $cashShortage,
                'tips' => $tips,
            ],
            'settlementRows' => $settlementRows,
        ]);
    }

    public function shiftLogReceipt(Request $request, ShiftLog $shiftLog): View
    {
        if (! $this->canAccessShiftLogProfile($request->user())) {
            abort(403, __('messages.errors.permission_denied'));
        }

        $shiftLog->loadMissing('user:id,name');

        $cashierShift = $this->resolveCashierShiftFromLog($shiftLog);
        $difference = $cashierShift && $cashierShift->difference !== null
            ? round((float) $cashierShift->difference, 2)
            : ($shiftLog->cash_difference !== null ? round((float) $shiftLog->cash_difference, 2) : 0.0);

        $receipt = $this->buildShiftReceiptPayload(
            shiftLog: $shiftLog,
            cashierShift: $cashierShift,
            cashierName: (string) ($shiftLog->user?->name ?? 'System'),
            difference: $difference,
        );

        return view('reports.shift-log-receipt', [
            'receipt' => $receipt,
        ]);
    }

    public function directPrint(Request $request, ShiftLog $shiftLog, \App\Services\PrintService $printService): \Illuminate\Http\JsonResponse
    {
        if (! $this->canAccessShiftLogProfile($request->user())) {
            abort(403, __('messages.errors.permission_denied'));
        }

        $shiftLog->loadMissing('user:id,name');

        $cashierShift = $this->resolveCashierShiftFromLog($shiftLog);
        $difference = $cashierShift && $cashierShift->difference !== null
            ? round((float) $cashierShift->difference, 2)
            : ($shiftLog->cash_difference !== null ? round((float) $shiftLog->cash_difference, 2) : 0.0);

        $receipt = $this->buildShiftReceiptPayload(
            shiftLog: $shiftLog,
            cashierShift: $cashierShift,
            cashierName: (string) ($shiftLog->user?->name ?? 'System'),
            difference: $difference,
        );

        $html = view('reports.shift-log-receipt', [
            'receipt' => $receipt,
            'isDirectPrint' => true,
        ])->render();

        \App\Models\PrintJob::create([
            'printer_type' => 'shift_close',
            'payload'      => $printService->buildHtmlBase64($html),
            'payload_type' => 'base64',
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sent to printer successfully',
        ]);
    }

    private function applyOrderFilters(Builder $query, ?string $orderType, ?string $status): Builder
    {
        return $query
            ->when($orderType, fn($builder, string $type) => $builder->where('order_type', $type))
            ->when($status, fn($builder, string $state) => $builder->where('status', $state));
    }

    private function applyOrderFiltersToItems(Builder $query, ?string $orderType, ?string $status): Builder
    {
        return $query
            ->when($orderType, fn($builder, string $type) => $builder->where('orders.order_type', $type))
            ->when($status, fn($builder, string $state) => $builder->where('orders.status', $state));
    }

    private function resolveCashierShiftFromLog(ShiftLog $shiftLog): ?CashierShift
    {
        $shiftStart = $shiftLog->shift_start ? Carbon::parse($shiftLog->shift_start) : null;

        if (! $shiftStart) {
            return null;
        }

        $query = CashierShift::query()
            ->where('user_id', (int) $shiftLog->user_id)
            ->whereBetween('start_time', [
                $shiftStart->copy()->subSecond(),
                $shiftStart->copy()->addSecond(),
            ]);

        if ($shiftLog->shift_end) {
            $shiftEnd = Carbon::parse($shiftLog->shift_end);

            $query->whereBetween('end_time', [
                $shiftEnd->copy()->subSecond(),
                $shiftEnd->copy()->addSecond(),
            ]);
        }

        return $query->latest('id')->first();
    }

    private function buildShiftReceiptPayload(ShiftLog $shiftLog, ?CashierShift $cashierShift, string $cashierName, ?float $difference = null): array
    {
        $normalizedDifference = round((float) ($difference ?? 0), 2);

        $openingCash = round((float) ($cashierShift?->opening_cash ?? 0), 2);
        $totalSales = round((float) ($cashierShift?->total_sales ?? 0), 2);
        $expectedCash = $cashierShift
            ? round((float) ($cashierShift->expected_cash ?? 0), 2)
            : round($openingCash + $totalSales, 2);
        $actualCash = $cashierShift && $cashierShift->actual_cash !== null
            ? round((float) $cashierShift->actual_cash, 2)
            : round($expectedCash + $normalizedDifference, 2);
        $tips = round((float) ($cashierShift?->tips ?? 0), 2);

        $cashOverage = round(max($normalizedDifference, 0), 2);
        $cashShortage = round(max(-1 * $normalizedDifference, 0), 2);

        $startTime = $cashierShift?->start_time ?? $shiftLog->shift_start;
        $endTime = $cashierShift?->end_time ?? $shiftLog->shift_end;

        return [
            'shift_id'     => $cashierShift?->id ?? $shiftLog->id,
            'cashier_name' => trim($cashierName) !== '' ? trim($cashierName) : 'System',
            'start_time' => $startTime?->toIso8601String(),
            'end_time' => $endTime?->toIso8601String(),
            'opening_cash' => $openingCash,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $normalizedDifference,
            'cash_overage' => $cashOverage,
            'cash_shortage' => $cashShortage,
            'tips' => $tips,
            'labels' => [
                'title' => __('ui.pos.shift.receipt_title'),
                'cashier' => __('ui.pos.shift.cashier_name'),
                'shift_time' => __('ui.pos.shift.shift_time'),
                'opening_cash' => __('ui.pos.shift.opening_cash'),
                'total_sales' => __('ui.pos.shift.total_sales'),
                'expected_cash' => __('ui.pos.shift.expected_cash'),
                'actual_cash' => __('ui.pos.shift.actual_cash'),
                'cash_overage' => __('ui.pos.shift.cash_overage'),
                'cash_shortage' => __('ui.pos.shift.cash_shortage'),
                'tips' => __('ui.pos.shift.tips'),
                'tips_note' => __('ui.pos.shift.tips_note'),
            ],
        ];
    }

    private function canAccessShiftLogProfile(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $managerRoleSlugs = ['admin', 'warehouse_manager'];

        if (in_array((string) ($user->role?->slug ?? ''), $managerRoleSlugs, true)) {
            return true;
        }

        return $user->roles()
            ->whereIn('slug', $managerRoleSlugs)
            ->exists();
    }
}
