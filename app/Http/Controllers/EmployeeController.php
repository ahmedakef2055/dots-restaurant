<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDeliverySettlement;
use App\Models\EmployeeSalaryAdjustment;
use App\Models\Order;
use App\Models\Product;
use App\Support\PdfExportRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    private const EMPLOYEES_PDF_LIMIT = 600;

    private const POSITION_OPTIONS = [
        'كاشير',
        'شيف',
        'مساعد شيف',
        'مدير صاله',
        'مدير مخزن',
        'محاسب',
        'كول سنتر',
        'باريستا',
        'ويتر',
        'تنظيف',
        'ديلفري',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive,terminated'],
        ]);

        $hasNationalIdColumn = $this->hasEmployeeColumn('national_id');

        $employees = Employee::query()
            ->withCount(['attendances', 'salaryPayments'])
            ->when($validated['q'] ?? null, function ($query, string $search) use ($hasNationalIdColumn): void {
                $query->where(function ($inner) use ($search, $hasNationalIdColumn): void {
                    $inner
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%[NID:{$search}%");

                    if ($hasNationalIdColumn) {
                        $inner->orWhere('national_id', 'like', "%{$search}%");
                    }
                });
            })
            ->when($validated['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'hasNationalIdColumn' => $hasNationalIdColumn,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'status' => $validated['status'] ?? '',
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive,terminated'],
        ]);

        $hasNationalIdColumn = $this->hasEmployeeColumn('national_id');
        $salaryAdjustmentsEnabled = Schema::hasTable('employee_salary_adjustments');

        $columns = [
            'id',
            'first_name',
            'last_name',
            'phone',
            'position',
            'base_salary',
            'status',
            'notes',
        ];

        if ($hasNationalIdColumn) {
            $columns[] = 'national_id';
        }

        $baseQuery = Employee::query()
            ->when($validated['q'] ?? null, function ($query, string $search) use ($hasNationalIdColumn): void {
                $query->where(function ($inner) use ($search, $hasNationalIdColumn): void {
                    $inner
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%[NID:{$search}%");

                    if ($hasNationalIdColumn) {
                        $inner->orWhere('national_id', 'like', "%{$search}%");
                    }
                });
            })
            ->when($validated['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->latest('id');

        $totalCount = (clone $baseQuery)->count();

        $employees = $baseQuery
            ->limit(self::EMPLOYEES_PDF_LIMIT)
            ->get($columns);

        $monthlyAdjustmentTotals = collect();

        if ($salaryAdjustmentsEnabled && $employees->isNotEmpty()) {
            $monthlyAdjustmentTotals = EmployeeSalaryAdjustment::query()
                ->whereIn('employee_id', $employees->pluck('id'))
                ->whereBetween('adjustment_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])
                ->selectRaw('employee_id, SUM(amount) as total_amount')
                ->groupBy('employee_id')
                ->pluck('total_amount', 'employee_id');
        }

        $rows = $employees
            ->map(static function (Employee $employee) use ($monthlyAdjustmentTotals): array {
                $baseSalary = round((float) $employee->base_salary, 2);
                $adjustmentTotal = (float) ($monthlyAdjustmentTotals[(int) $employee->id] ?? 0);
                $netMonthlySalary = max($baseSalary - $adjustmentTotal, 0);

                $status = strtolower((string) ($employee->status ?? 'inactive'));
                if (! in_array($status, ['active', 'inactive', 'terminated'], true)) {
                    $status = 'inactive';
                }

                return [
                    'name' => (string) $employee->full_name,
                    'position' => (string) $employee->position,
                    'national_id' => (string) ($employee->national_id_display ?: '-'),
                    'phone' => (string) ($employee->phone ?: '-'),
                    'base_salary' => $baseSalary,
                    'net_monthly_salary' => round($netMonthlySalary, 2),
                    'status' => $status,
                ];
            })
            ->values()
            ->all();

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;

        $fallbackFilters = array_filter([
            'q' => $validated['q'] ?? null,
            'status' => $validated['status'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');

        return $pdfExportRenderer->downloadPdfFromView(
            'employees.exports.active-list-pdf',
            [
                'rows' => $rows,
                'filters' => [
                    'q' => (string) ($validated['q'] ?? ''),
                    'status' => (string) ($validated['status'] ?? ''),
                ],
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'employees-report-' . now()->format('Ymd_His') . '.pdf',
            route('employees.index', $fallbackFilters)
        );
    }

    public function exportExcel(): StreamedResponse
    {
        $rows = $this->buildActiveEmployeesExportRows();
        $fileName = 'active-employees-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Employee Name',
                'Position',
                'National ID',
                'Phone',
                'Base Salary',
                'Net Monthly Salary',
            ]);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['position'],
                    $row['national_id'],
                    $row['phone'],
                    number_format((float) $row['base_salary'], 2, '.', ''),
                    number_format((float) $row['net_monthly_salary'], 2, '.', ''),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function printFinancialMonthlyReport(Employee $employee): View
    {
        $report = $this->buildEmployeeMonthlyFinancialReport($employee);

        return view('employees.reports.monthly-financial', [
            'employee' => $employee,
            'report' => $report,
            'showPrintButton' => true,
            'generatedAt' => now(),
        ]);
    }

    public function exportFinancialMonthlyReportPdf(Employee $employee, PdfExportRenderer $pdfExportRenderer): Response
    {
        $report = $this->buildEmployeeMonthlyFinancialReport($employee);

        return $pdfExportRenderer->downloadPdfFromView(
            'employees.reports.monthly-financial',
            [
                'employee' => $employee,
                'report' => $report,
                'showPrintButton' => false,
                'generatedAt' => now(),
            ],
            'employee-financial-report-' . $employee->id . '-' . now()->format('Ym') . '.pdf',
            route('employees.show', $employee)
        );
    }

    public function exportFinancialMonthlyReportExcel(Employee $employee): StreamedResponse
    {
        $report = $this->buildEmployeeMonthlyFinancialReport($employee);
        $fileName = 'employee-financial-report-' . $employee->id . '-' . now()->format('Ym') . '.csv';

        return response()->streamDownload(function () use ($employee, $report): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [__('ui.employees.financial_report.excel.employee_name'), $employee->full_name]);
            fputcsv($output, [__('ui.employees.financial_report.excel.month'), $report['month_label']]);
            fputcsv($output, [__('ui.employees.financial_report.excel.base_salary'), number_format((float) $report['base_salary'], 2, '.', '')]);
            fputcsv($output, [__('ui.employees.financial_report.excel.total_adjustments'), number_format((float) $report['total_adjustments'], 2, '.', '')]);
            fputcsv($output, [__('ui.employees.financial_report.excel.net_salary'), number_format((float) $report['net_salary'], 2, '.', '')]);
            fputcsv($output, []);

            fputcsv($output, [
                __('ui.employees.financial_report.table.date'),
                __('ui.employees.financial_report.table.type'),
                __('ui.employees.financial_report.table.reason'),
                __('ui.employees.financial_report.table.product_name'),
                __('ui.employees.financial_report.table.unit_price'),
                __('ui.employees.financial_report.table.quantity'),
                __('ui.employees.financial_report.table.amount'),
            ]);

            if ($report['rows']->isEmpty()) {
                fputcsv($output, ['-', '-', __('ui.employees.financial_report.empty'), '-', '0.00', '0.00', '0.00']);
            } else {
                foreach ($report['rows'] as $row) {
                    fputcsv($output, [
                        $row['date'],
                        $row['type_label'],
                        $row['reason'],
                        $row['product_name'],
                        number_format((float) $row['unit_price'], 2, '.', ''),
                        number_format((float) $row['quantity'], 2, '.', ''),
                        number_format((float) $row['amount'], 2, '.', ''),
                    ]);
                }
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'positionOptions' => $this->getPositionOptions(),
            'supportsScheduleFields' => $this->supportsScheduleFields(),
            'nationalIdValue' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEmployee($request);
        $payload = $this->buildEmployeePayload($validated);

        Employee::query()->create($payload);

        return redirect()
            ->route('employees.index')
            ->with('success', __('messages.success.employee_created'));
    }

    public function show(Employee $employee): View
    {
        $salaryAdjustmentsEnabled = Schema::hasTable('employee_salary_adjustments');
        $hasNationalIdColumn = $this->hasEmployeeColumn('national_id');
        $isDeliveryEmployee = $this->isDeliveryEmployeePosition((string) $employee->position);
        $deliverySettlementFeatureEnabled = $isDeliveryEmployee && $this->supportsDeliverySettlementFeature();

        $relations = [
            'attendances' => fn($query) => $query->latest('attendance_date')->limit(10),
            'salaryPayments' => fn($query) => $query->latest('period_end')->limit(10),
        ];

        if ($salaryAdjustmentsEnabled) {
            $relations['salaryAdjustments'] = fn($query) => $query
                ->with('product:id,name')
                ->latest('adjustment_date')
                ->latest('id')
                ->limit(15);
        }

        $employee->load($relations);

        $products = collect();
        $currentMonthAdjustmentTotal = 0;
        $currentMonthNetSalary = (float) $employee->base_salary;
        $deliveryOrders = collect();
        $deliverySettlements = collect();
        $deliveryOrdersCount = 0;
        $deliveryOrdersTotal = 0;
        $unsettledDeliveryOrdersCount = 0;
        $unsettledDeliveryOrdersTotal = 0;

        if ($salaryAdjustmentsEnabled) {
            $products = Product::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price']);

            $currentMonthAdjustmentTotal = (float) $employee->salaryAdjustments()
                ->whereBetween('adjustment_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('amount');

            $currentMonthNetSalary = max((float) $employee->base_salary - $currentMonthAdjustmentTotal, 0);
        } else {
            $employee->setRelation('salaryAdjustments', collect());
        }

        if ($deliverySettlementFeatureEnabled) {
            $baseDeliveryOrdersQuery = Order::query()
                ->where('order_type', 'delivery')
                ->where('status', 'paid')
                ->where('delivery_employee_id', $employee->id);

            $deliveryOrdersCount = (clone $baseDeliveryOrdersQuery)->count();
            $deliveryOrdersTotal = (float) (clone $baseDeliveryOrdersQuery)->sum('total');

            $unsettledDeliveryOrdersQuery = (clone $baseDeliveryOrdersQuery)
                ->whereNull('delivery_settlement_id');

            $unsettledDeliveryOrdersCount = (clone $unsettledDeliveryOrdersQuery)->count();
            $unsettledDeliveryOrdersTotal = (float) (clone $unsettledDeliveryOrdersQuery)->sum('total');

            $deliveryOrders = (clone $baseDeliveryOrdersQuery)
                ->latest('order_serial')
                ->limit(20)
                ->get(['order_serial', 'order_number', 'total', 'created_at', 'delivery_settlement_id']);

            $deliverySettlements = EmployeeDeliverySettlement::query()
                ->with('settler:id,name')
                ->where('employee_id', $employee->id)
                ->latest('settled_at')
                ->limit(10)
                ->get([
                    'id',
                    'employee_id',
                    'order_count',
                    'gross_total',
                    'commission_percentage',
                    'commission_amount',
                    'restaurant_share_amount',
                    'settled_at',
                    'settled_by',
                    'note',
                ]);
        }

        return view('employees.show', [
            'employee' => $employee,
            'products' => $products,
            'currentMonthAdjustmentTotal' => $currentMonthAdjustmentTotal,
            'currentMonthNetSalary' => $currentMonthNetSalary,
            'salaryAdjustmentsEnabled' => $salaryAdjustmentsEnabled,
            'hasNationalIdColumn' => $hasNationalIdColumn,
            'isDeliveryEmployee' => $isDeliveryEmployee,
            'deliverySettlementFeatureEnabled' => $deliverySettlementFeatureEnabled,
            'deliveryOrders' => $deliveryOrders,
            'deliveryOrdersCount' => $deliveryOrdersCount,
            'deliveryOrdersTotal' => $deliveryOrdersTotal,
            'unsettledDeliveryOrdersCount' => $unsettledDeliveryOrdersCount,
            'unsettledDeliveryOrdersTotal' => $unsettledDeliveryOrdersTotal,
            'deliverySettlements' => $deliverySettlements,
        ]);
    }

    public function settleDeliveryOrders(Request $request, Employee $employee): RedirectResponse
    {
        if (! $this->supportsDeliverySettlementFeature()) {
            return back()->with('error', __('messages.errors.employee_delivery_settlement_feature_unavailable'));
        }

        if (! $this->isDeliveryEmployeePosition((string) $employee->position)) {
            return back()->with('error', __('messages.errors.employee_not_delivery_role'));
        }

        $validated = $request->validate([
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $percentage = round((float) $validated['commission_percentage'], 2);

        DB::transaction(function () use ($employee, $percentage, $validated): void {
            $pendingOrders = Order::query()
                ->where('order_type', 'delivery')
                ->where('status', 'paid')
                ->where('delivery_employee_id', $employee->id)
                ->whereNull('delivery_settlement_id')
                ->lockForUpdate()
                ->get(['order_serial', 'total']);

            if ($pendingOrders->isEmpty()) {
                throw ValidationException::withMessages([
                    'commission_percentage' => __('messages.errors.employee_delivery_no_unsettled_orders'),
                ]);
            }

            $grossTotal = round((float) $pendingOrders->sum('total'), 2);
            $commissionAmount = round($grossTotal * ($percentage / 100), 2);
            $restaurantShareAmount = round($grossTotal - $commissionAmount, 2);

            $settlement = EmployeeDeliverySettlement::query()->create([
                'employee_id' => $employee->id,
                'order_count' => $pendingOrders->count(),
                'gross_total' => $grossTotal,
                'commission_percentage' => $percentage,
                'commission_amount' => $commissionAmount,
                'restaurant_share_amount' => $restaurantShareAmount,
                'settled_at' => now(),
                'settled_by' => Auth::id(),
                'note' => $validated['note'] ?? null,
            ]);

            Order::query()
                ->whereIn('order_serial', $pendingOrders->pluck('order_serial'))
                ->update([
                    'delivery_settlement_id' => $settlement->id,
                ]);
        });

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_delivery_settlement_created'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'positionOptions' => $this->getPositionOptions($employee),
            'supportsScheduleFields' => $this->supportsScheduleFields(),
            'nationalIdValue' => $employee->national_id_display ?? '',
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $this->validateEmployee($request, $employee->id);
        $payload = $this->buildEmployeePayload($validated, $employee);

        $employee->update($payload);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('messages.success.employee_updated'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->attendances()->exists() || $employee->salaryPayments()->exists()) {
            return back()->with('error', __('messages.errors.cannot_delete_employee_with_records'));
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', __('messages.success.employee_deleted'));
    }

    private function buildEmployeeMonthlyFinancialReport(Employee $employee): array
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();
        $salaryAdjustmentsEnabled = Schema::hasTable('employee_salary_adjustments');

        $rows = collect();
        $totalAdjustments = 0.0;

        if ($salaryAdjustmentsEnabled) {
            $adjustments = EmployeeSalaryAdjustment::query()
                ->with('product:id,name')
                ->where('employee_id', $employee->id)
                ->whereBetween('adjustment_date', [
                    $periodStart->toDateString(),
                    $periodEnd->toDateString(),
                ])
                ->orderBy('adjustment_date')
                ->orderBy('id')
                ->get([
                    'id',
                    'employee_id',
                    'type',
                    'product_id',
                    'quantity',
                    'unit_price',
                    'amount',
                    'adjustment_date',
                    'note',
                ]);

            $rows = $adjustments->map(function (EmployeeSalaryAdjustment $adjustment): array {
                $reason = trim((string) ($adjustment->note ?? ''));

                return [
                    'date' => $adjustment->adjustment_date?->format('Y-m-d') ?? '-',
                    'type_label' => $this->getAdjustmentTypeLabel((string) $adjustment->type),
                    'reason' => $reason !== '' ? $reason : '-',
                    'product_name' => (string) ($adjustment->product?->name ?? '-'),
                    'unit_price' => round((float) ($adjustment->unit_price ?? 0), 2),
                    'quantity' => round((float) ($adjustment->quantity ?? 0), 2),
                    'amount' => round((float) $adjustment->amount, 2),
                ];
            });

            $totalAdjustments = round((float) $adjustments->sum('amount'), 2);
        }

        $baseSalary = round((float) $employee->base_salary, 2);
        $netSalary = round(max($baseSalary - $totalAdjustments, 0), 2);

        return [
            'month_label' => now()->format('F Y'),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'base_salary' => $baseSalary,
            'total_adjustments' => $totalAdjustments,
            'net_salary' => $netSalary,
            'salary_adjustments_enabled' => $salaryAdjustmentsEnabled,
            'rows' => $rows,
        ];
    }

    private function getAdjustmentTypeLabel(string $type): string
    {
        return match ($type) {
            'manual_deduction' => __('ui.employees.financial_report.types.manual_deduction'),
            'product_charge' => __('ui.employees.financial_report.types.product_charge'),
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    private function buildActiveEmployeesExportRows(): Collection
    {
        $hasNationalIdColumn = $this->hasEmployeeColumn('national_id');
        $salaryAdjustmentsEnabled = Schema::hasTable('employee_salary_adjustments');

        $columns = [
            'id',
            'first_name',
            'last_name',
            'phone',
            'position',
            'base_salary',
            'notes',
        ];

        if ($hasNationalIdColumn) {
            $columns[] = 'national_id';
        }

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get($columns);

        $monthlyAdjustmentTotals = collect();

        if ($salaryAdjustmentsEnabled && $employees->isNotEmpty()) {
            $monthlyAdjustmentTotals = EmployeeSalaryAdjustment::query()
                ->whereIn('employee_id', $employees->pluck('id'))
                ->whereBetween('adjustment_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])
                ->selectRaw('employee_id, SUM(amount) as total_amount')
                ->groupBy('employee_id')
                ->pluck('total_amount', 'employee_id');
        }

        return $employees->map(function (Employee $employee) use ($monthlyAdjustmentTotals): array {
            $baseSalary = round((float) $employee->base_salary, 2);
            $adjustmentTotal = (float) ($monthlyAdjustmentTotals[(int) $employee->id] ?? 0);
            $netMonthlySalary = max($baseSalary - $adjustmentTotal, 0);

            return [
                'name' => $employee->full_name,
                'position' => (string) $employee->position,
                'national_id' => (string) ($employee->national_id_display ?: '-'),
                'phone' => (string) ($employee->phone ?: '-'),
                'base_salary' => $baseSalary,
                'net_monthly_salary' => round($netMonthlySalary, 2),
            ];
        });
    }

    private function validateEmployee(Request $request, ?int $employeeId = null): array
    {
        $employee = $employeeId ? Employee::query()->find($employeeId) : null;

        $rules = [
            'first_name' => ['required', 'string', 'max:120'],
            'national_id' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('employees', 'phone')->ignore($employeeId)],
            'position' => ['required', 'string', 'max:120', Rule::in($this->getPositionOptions($employee))],
            'hire_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,terminated'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($this->hasEmployeeColumn('national_id')) {
            $rules['national_id'][] = Rule::unique('employees', 'national_id')->ignore($employeeId);
        }

        if ($this->supportsScheduleFields()) {
            $rules['work_hours_per_day'] = ['required', 'numeric', 'min:1', 'max:24'];
            $rules['attendance_days_per_week'] = ['required', 'integer', 'between:1,7'];
            $rules['shift_start'] = ['required', 'date_format:H:i'];
            $rules['shift_end'] = ['required', 'date_format:H:i', 'after:shift_start'];
        }

        return $request->validate($rules);
    }

    private function buildEmployeePayload(array $validated, ?Employee $employee = null): array
    {
        $payload = [
            'first_name' => $validated['first_name'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'hire_date' => $validated['hire_date'],
            'base_salary' => $validated['base_salary'],
            'status' => $validated['status'],
            'address' => $validated['address'] ?? null,
        ];

        if ($this->supportsScheduleFields()) {
            $payload['work_hours_per_day'] = $validated['work_hours_per_day'];
            $payload['attendance_days_per_week'] = $validated['attendance_days_per_week'];
            $payload['shift_start'] = $validated['shift_start'];
            $payload['shift_end'] = $validated['shift_end'];
        }

        $notes = $validated['notes'] ?? null;

        if ($this->hasEmployeeColumn('national_id')) {
            $payload['national_id'] = $validated['national_id'];
            $payload['notes'] = $notes;
        } else {
            $payload['notes'] = $this->mergeNationalIdIntoNotes($notes, $validated['national_id']);
        }

        if (! $employee) {
            $payload['employee_code'] = $this->generateEmployeeCode();
            $payload['last_name'] = '';
            $payload['employment_type'] = 'full_time';
            $payload['email'] = null;
            $payload['department'] = null;
            $payload['hourly_rate'] = null;
        }

        return $payload;
    }

    private function generateEmployeeCode(): string
    {
        do {
            $candidate = 'EMP-' . now()->format('Ymd') . '-' . random_int(100, 999);
        } while (Employee::query()->where('employee_code', $candidate)->exists());

        return $candidate;
    }

    private function mergeNationalIdIntoNotes(?string $notes, string $nationalId): string
    {
        $cleanNotes = trim((string) $notes);
        $withoutOldTag = preg_replace('/\s*\[NID:[^\]]+\]\s*/', ' ', $cleanNotes);
        $withoutOldTag = trim((string) $withoutOldTag);

        $tag = '[NID:' . $nationalId . ']';

        return $withoutOldTag === '' ? $tag : $tag . PHP_EOL . $withoutOldTag;
    }

    private function hasEmployeeColumn(string $column): bool
    {
        try {
            return Schema::hasColumn('employees', $column);
        } catch (\Throwable) {
            return false;
        }
    }

    private function supportsScheduleFields(): bool
    {
        return $this->hasEmployeeColumn('work_hours_per_day')
            && $this->hasEmployeeColumn('attendance_days_per_week')
            && $this->hasEmployeeColumn('shift_start')
            && $this->hasEmployeeColumn('shift_end');
    }

    private function supportsDeliverySettlementFeature(): bool
    {
        return Schema::hasTable('orders')
            && Schema::hasTable('employee_delivery_settlements')
            && Schema::hasColumn('orders', 'delivery_employee_id')
            && Schema::hasColumn('orders', 'delivery_settlement_id');
    }

    private function isDeliveryEmployeePosition(string $position): bool
    {
        $normalized = mb_strtolower(trim($position));

        if ($normalized === '') {
            return false;
        }

        return str_contains($normalized, 'ديلفري')
            || str_contains($normalized, 'delivery');
    }

    private function getPositionOptions(?Employee $employee = null): array
    {
        $options = self::POSITION_OPTIONS;
        $currentPosition = trim((string) ($employee?->position ?? ''));

        if ($currentPosition !== '' && ! in_array($currentPosition, $options, true)) {
            $options[] = $currentPosition;
        }

        return array_values(array_unique($options));
    }
}
