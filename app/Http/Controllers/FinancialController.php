<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\SalaryPayment;
use App\Support\CurrencyFormatter;
use App\Support\PdfExportRenderer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FinancialController extends Controller
{
    /* ─────────────────────────────────────────────────────────────────
     |  INDEX – main financial dashboard
     ─────────────────────────────────────────────────────────────────*/
    public function index(Request $request): \Illuminate\View\View
    {
        [$from, $to, $period] = $this->resolveDateRange($request);

        $paymentMethodFilter = $request->input('payment_method', '');
        $typeFilter          = $request->input('type', '');

        // ── Revenue (paid orders only) ───────────────────────────────
        $orderQuery = Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$from->startOfDay()->toDateTimeString(), $to->copy()->endOfDay()->toDateTimeString()]);

        $todayRevenue   = Order::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $monthRevenue   = Order::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $totalRevenue   = $orderQuery->sum('total');

        // ── Expenses (purchases + salaries in range) ─────────────────
        $purchaseExpenses = Purchase::whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()])
            ->where('approval_status', 'completed')
            ->sum('total');

        $salaryExpenses = SalaryPayment::whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->sum('paid_amount');

        $totalExpenses  = $purchaseExpenses + $salaryExpenses;
        $netProfit      = $totalRevenue - $totalExpenses;

        // ── Build unified transaction rows ───────────────────────────
        $transactions = $this->buildTransactions($from, $to, $paymentMethodFilter, $typeFilter);

        // ── KPI summary cards ────────────────────────────────────────
        $kpis = [
            [
                'label'  => __('ui.financial.kpi.today_revenue'),
                'value'  => CurrencyFormatter::format($todayRevenue),
                'icon'   => 'today',
                'color'  => 'primary',
            ],
            [
                'label'  => __('ui.financial.kpi.month_revenue'),
                'value'  => CurrencyFormatter::format($monthRevenue),
                'icon'   => 'calendar_month',
                'color'  => 'secondary',
            ],
            [
                'label'  => __('ui.financial.kpi.total_expenses'),
                'value'  => CurrencyFormatter::format($totalExpenses),
                'icon'   => 'payments',
                'color'  => 'error',
            ],
            [
                'label'       => __('ui.financial.kpi.net_profit'),
                'value'       => CurrencyFormatter::format($netProfit),
                'icon'        => $netProfit >= 0 ? 'trending_up' : 'trending_down',
                'color'       => $netProfit >= 0 ? 'tertiary' : 'error',
                'is_profit'   => true,
                'profit_sign' => $netProfit >= 0 ? 'positive' : 'negative',
            ],
        ];

        return view('financial.index', [
            'kpis'                 => $kpis,
            'transactions'         => $transactions,
            'totalRevenue'         => $totalRevenue,
            'totalExpenses'        => $totalExpenses,
            'netProfit'            => $netProfit,
            'from'                 => $from->toDateString(),
            'to'                   => $to->toDateString(),
            'period'               => $period,
            'monthYear'            => $request->input('month_year', $from->format('Y-m')),
            'paymentMethodFilter'  => $paymentMethodFilter,
            'typeFilter'           => $typeFilter,
            'generatedAt'          => now(),
        ]);
    }

    /* ─────────────────────────────────────────────────────────────────
     |  EXPORT PDF
     ─────────────────────────────────────────────────────────────────*/
    public function exportPdf(Request $request, PdfExportRenderer $renderer): Response
    {
        [$from, $to] = $this->resolveDateRange($request);

        $paymentMethodFilter = $request->input('payment_method', '');
        $typeFilter          = $request->input('type', '');

        $transactions = $this->buildTransactions($from, $to, $paymentMethodFilter, $typeFilter);

        $purchaseExpenses = Purchase::whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()])
            ->where('approval_status', 'completed')->sum('total');
        $salaryExpenses = SalaryPayment::whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->sum('paid_amount');

        $totalRevenue  = $transactions->where('category', 'income')->sum('raw_amount');
        $totalExpenses = $purchaseExpenses + $salaryExpenses;

        return $renderer->downloadPdfFromView(
            'financial.exports.financial-pdf',
            [
                'transactions'  => $transactions,
                'totalRevenue'  => $totalRevenue,
                'totalExpenses' => $totalExpenses,
                'netProfit'     => $totalRevenue - $totalExpenses,
                'from'          => $from->toDateString(),
                'to'            => $to->toDateString(),
                'generatedAt'   => now(),
                'filters'       => [
                    'payment_method' => $paymentMethodFilter,
                    'type'           => $typeFilter,
                ],
            ],
            'financial-report-' . now()->format('Ymd_His') . '.pdf',
            route('financial.index')
        );
    }

    /* ─────────────────────────────────────────────────────────────────
     |  EXPORT EXCEL (CSV stream)
     ─────────────────────────────────────────────────────────────────*/
    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        $paymentMethodFilter = $request->input('payment_method', '');
        $typeFilter          = $request->input('type', '');

        $transactions = $this->buildTransactions($from, $to, $paymentMethodFilter, $typeFilter);

        $isAr   = app()->getLocale() === 'ar';
        $headers = $isAr
            ? ['النوع', 'المرجع', 'الوصف', 'طريقة الدفع', 'المبلغ', 'الحالة', 'التاريخ']
            : ['Type', 'Reference', 'Description', 'Payment Method', 'Amount', 'Status', 'Date'];

        $fileName = 'financial-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($transactions, $headers): void {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);

            foreach ($transactions as $row) {
                fputcsv($handle, [
                    $row['type_label'],
                    $row['reference'],
                    $row['description'],
                    $row['payment_method_label'],
                    number_format((float) $row['raw_amount'], 2),
                    $row['status_label'],
                    $row['date'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────────
     |  HELPERS
     ─────────────────────────────────────────────────────────────────*/

    /**
     * Resolve date range from request; returns [Carbon $from, Carbon $to, string $period].
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', 'month');

        if ($request->filled('from') && $request->filled('to')) {
            $period = 'custom';
            $from   = Carbon::parse($request->input('from'))->startOfDay();
            $to     = Carbon::parse($request->input('to'))->endOfDay();
        } elseif ($period === 'today') {
            $from = now()->startOfDay();
            $to   = now()->endOfDay();
        } elseif ($period === 'week') {
            $from = now()->startOfWeek();
            $to   = now()->endOfWeek();
        } elseif ($period === 'month' && $request->filled('month_year')) {
            $parsed = Carbon::createFromFormat('Y-m', $request->input('month_year'));
            $from   = $parsed->copy()->startOfMonth()->startOfDay();
            $to     = $parsed->copy()->endOfMonth()->endOfDay();
        } else {
            $period = 'month';
            $from   = now()->startOfMonth();
            $to     = now()->endOfMonth();
        }

        return [$from, $to, $period];
    }

    /**
     * Build a unified, sorted collection of financial transactions.
     */
    private function buildTransactions(Carbon $from, Carbon $to, ?string $pmFilter, ?string $typeFilter): \Illuminate\Support\Collection
    {
        $pmFilter = (string) $pmFilter;
        $typeFilter = (string) $typeFilter;
        
        $isAr = app()->getLocale() === 'ar';
        $rows = collect();

        // ── 1. Order payments (income) ───────────────────────────────
        if ($typeFilter === '' || $typeFilter === 'order') {
            $orders = Order::with('user')
                ->where('status', 'paid')
                ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->when($pmFilter !== '', fn ($q) => $q->where('payment_method', $pmFilter))
                ->orderByDesc('created_at')
                ->get(['order_serial', 'order_number', 'order_daily_number', 'payment_method',
                       'total', 'user_id', 'created_at', 'status']);

            foreach ($orders as $order) {
                $rows->push([
                    'category'            => 'income',
                    'type'                => 'order',
                    'type_label'          => $isAr ? 'مبيعات' : 'Sales',
                    'reference'           => $order->order_number,
                    'serial'              => $order->order_number,
                    'description'         => ($isAr ? 'طلب رقم ' : 'Order ') . ($order->order_daily_number ?: $order->order_number),
                    'payment_method'      => $order->payment_method ?? 'cash',
                    'payment_method_label'=> $this->paymentLabel($order->payment_method ?? 'cash'),
                    'raw_amount'          => (float) $order->total,
                    'amount'              => CurrencyFormatter::format((float) $order->total),
                    'remaining'           => null,
                    'actor'               => $order->user?->name ?? ($isAr ? 'النظام' : 'System'),
                    'status'              => 'paid',
                    'status_label'        => $isAr ? 'مدفوع' : 'Paid',
                    'date'                => $order->created_at?->format('Y-m-d H:i'),
                    'sort_date'           => $order->created_at,
                ]);
            }
        }

        // ── 2. Purchase payments (expense) ───────────────────────────
        if ($typeFilter === '' || $typeFilter === 'purchase') {
            $purchasePayments = PurchasePayment::with(['supplier', 'purchase', 'user'])
                ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
                ->when($pmFilter !== '', fn ($q) => $q->where('method', $pmFilter))
                ->orderByDesc('payment_date')
                ->get();

            foreach ($purchasePayments as $pp) {
                $purchase   = $pp->purchase;
                $remaining  = $purchase
                    ? max(0, (float) $purchase->total - (float) ($purchase->payments()->sum('amount')))
                    : null;

                $rows->push([
                    'category'            => 'expense',
                    'type'                => 'purchase',
                    'type_label'          => $isAr ? 'مشتريات' : 'Purchase',
                    'reference'           => $pp->payment_number ?? ($purchase?->purchase_number ?? '-'),
                    'serial'              => $purchase?->purchase_number ?? '-',
                    'description'         => $pp->supplier?->name ?? ($purchase?->expense_title ?? ($isAr ? 'مورد' : 'Supplier')),
                    'payment_method'      => $pp->method ?? 'cash',
                    'payment_method_label'=> $this->paymentLabel($pp->method ?? 'cash'),
                    'raw_amount'          => (float) $pp->amount,
                    'amount'              => CurrencyFormatter::format((float) $pp->amount),
                    'remaining'           => $remaining !== null ? CurrencyFormatter::format($remaining) : null,
                    'actor'               => $pp->user?->name ?? ($isAr ? 'النظام' : 'System'),
                    'status'              => 'paid',
                    'status_label'        => $isAr ? 'مدفوع' : 'Paid',
                    'date'                => $pp->payment_date?->format('Y-m-d'),
                    'sort_date'           => Carbon::parse($pp->payment_date),
                ]);
            }
        }

        // ── 3. Salary payments (expense) ─────────────────────────────
        if ($typeFilter === '' || $typeFilter === 'salary') {
            $salaries = SalaryPayment::with('employee')
                ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
                ->orderByDesc('payment_date')
                ->get();

            foreach ($salaries as $salary) {
                if ($pmFilter !== '' && $pmFilter !== 'cash') {
                    continue;
                }

                $rows->push([
                    'category'            => 'expense',
                    'type'                => 'salary',
                    'type_label'          => $isAr ? 'رواتب' : 'Salary',
                    'reference'           => 'SAL-' . $salary->id,
                    'serial'              => 'SAL-' . $salary->id,
                    'description'         => $salary->employee?->name ?? ($isAr ? 'موظف' : 'Employee'),
                    'payment_method'      => 'cash',
                    'payment_method_label'=> $this->paymentLabel('cash'),
                    'raw_amount'          => (float) $salary->paid_amount,
                    'amount'              => CurrencyFormatter::format((float) $salary->paid_amount),
                    'remaining'           => null,
                    'actor'               => $isAr ? 'قسم الموارد البشرية' : 'HR',
                    'status'              => $salary->status,
                    'status_label'        => $this->salaryStatusLabel($salary->status),
                    'date'                => $salary->payment_date?->format('Y-m-d'),
                    'sort_date'           => Carbon::parse($salary->payment_date),
                ]);
            }
        }

        return $rows->sortByDesc('sort_date')->values();
    }

    private function paymentLabel(string $method): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match (strtolower($method)) {
            'cash'         => $isAr ? 'نقدي'        : 'Cash',
            'card'         => $isAr ? 'بطاقة'       : 'Card',
            'credit'       => $isAr ? 'آجل'         : 'Credit',
            'bank_transfer'=> $isAr ? 'تحويل بنكي'  : 'Bank Transfer',
            'wallet'       => $isAr ? 'محفظة'       : 'Wallet',
            'visa'         => $isAr ? 'فيزا'        : 'Visa',
            'instapay'     => $isAr ? 'إنستاباي'    : 'Instapay',
            default        => ucfirst($method),
        };
    }

    private function salaryStatusLabel(string $status): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($status) {
            'paid'    => $isAr ? 'مدفوع' : 'Paid',
            'partial' => $isAr ? 'جزئي' : 'Partial',
            default   => $isAr ? 'غير مدفوع' : 'Unpaid',
        };
    }
}
