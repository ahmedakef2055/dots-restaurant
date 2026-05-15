<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryPayment;
use App\Support\PdfExportRenderer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalaryController extends Controller
{
    private const SALARIES_EXPORT_LIMIT = 600;

    public function index(Request $request): View
    {
        $validated = $this->validateSalaryFilters($request);

        $salaryPayments = $this->buildSalaryPaymentsQuery($validated)
            ->paginate(15)
            ->withQueryString();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('salaries.index', [
            'salaryPayments' => $salaryPayments,
            'employees' => $employees,
            'filters' => [
                'employee_id' => $validated['employee_id'] ?? '',
                'status' => $validated['status'] ?? '',
                'from' => $validated['from'] ?? '',
                'to' => $validated['to'] ?? '',
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfExportRenderer $pdfExportRenderer): Response
    {
        $validated = $this->validateSalaryFilters($request);

        $baseQuery = $this->buildSalaryPaymentsQuery($validated);
        $totalCount = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->limit(self::SALARIES_EXPORT_LIMIT)
            ->get([
                'id',
                'employee_id',
                'period_start',
                'period_end',
                'gross_amount',
                'net_amount',
                'paid_amount',
                'status',
                'payment_date',
                'note',
            ])
            ->map(static function (SalaryPayment $salaryPayment): array {
                $employeeCode = trim((string) ($salaryPayment->employee?->employee_code ?? ''));
                $employeeName = trim((string) ($salaryPayment->employee?->full_name ?? ''));
                $status = strtolower((string) ($salaryPayment->status ?? 'unpaid'));

                if (! in_array($status, ['unpaid', 'partial', 'paid'], true)) {
                    $status = 'unpaid';
                }

                return [
                    'employee_code' => $employeeCode,
                    'employee_name' => $employeeName,
                    'employee_label' => trim($employeeCode !== '' ? $employeeCode . ' - ' . $employeeName : $employeeName),
                    'period_start' => $salaryPayment->period_start?->format('Y-m-d') ?? '',
                    'period_end' => $salaryPayment->period_end?->format('Y-m-d') ?? '',
                    'gross_amount' => (float) ($salaryPayment->gross_amount ?? 0),
                    'net_amount' => (float) ($salaryPayment->net_amount ?? 0),
                    'paid_amount' => (float) ($salaryPayment->paid_amount ?? 0),
                    'remaining_amount' => max((float) ($salaryPayment->net_amount ?? 0) - (float) ($salaryPayment->paid_amount ?? 0), 0),
                    'status' => $status,
                    'payment_date' => $salaryPayment->payment_date?->format('Y-m-d') ?? '',
                    'note' => trim((string) ($salaryPayment->note ?? '')),
                ];
            })
            ->values()
            ->all();

        $employeeFilterLabel = '';
        if ($validated['employee_id'] ?? null) {
            $selectedEmployee = Employee::query()
                ->find((int) $validated['employee_id']);

            if ($selectedEmployee) {
                $employeeFilterLabel = trim(($selectedEmployee->employee_code ? $selectedEmployee->employee_code . ' - ' : '') . $selectedEmployee->full_name);
            }
        }

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;

        $fallbackFilters = array_filter([
            'employee_id' => $validated['employee_id'] ?? null,
            'status' => $validated['status'] ?? null,
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');

        return $pdfExportRenderer->downloadPdfFromView(
            'salaries.exports.salaries-pdf',
            [
                'rows' => $rows,
                'filters' => [
                    'employee_id' => $validated['employee_id'] ?? null,
                    'employee_label' => $employeeFilterLabel,
                    'status' => (string) ($validated['status'] ?? ''),
                    'from' => (string) ($validated['from'] ?? ''),
                    'to' => (string) ($validated['to'] ?? ''),
                ],
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'salaries-report-' . now()->format('Ymd_His') . '.pdf',
            route('salaries.index', $fallbackFilters)
        );
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $validated = $this->validateSalaryFilters($request);

        $rows = $this->buildSalaryPaymentsQuery($validated)
            ->limit(self::SALARIES_EXPORT_LIMIT)
            ->get([
                'id',
                'employee_id',
                'period_start',
                'period_end',
                'gross_amount',
                'net_amount',
                'paid_amount',
                'status',
                'payment_date',
                'note',
            ]);

        $fileName = 'salaries-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                __('ui.salaries.exports.excel.headers.employee'),
                __('ui.salaries.exports.excel.headers.period_start'),
                __('ui.salaries.exports.excel.headers.period_end'),
                __('ui.salaries.exports.excel.headers.gross'),
                __('ui.salaries.exports.excel.headers.net'),
                __('ui.salaries.exports.excel.headers.paid'),
                __('ui.salaries.exports.excel.headers.remaining'),
                __('ui.salaries.exports.excel.headers.status'),
                __('ui.salaries.exports.excel.headers.payment_date'),
                __('ui.salaries.exports.excel.headers.note'),
            ]);

            foreach ($rows as $salaryPayment) {
                $employeeCode = trim((string) ($salaryPayment->employee?->employee_code ?? ''));
                $employeeName = trim((string) ($salaryPayment->employee?->full_name ?? ''));
                $status = strtolower((string) ($salaryPayment->status ?? 'unpaid'));
                $statusLabel = match ($status) {
                    'paid' => __('ui.salaries.statuses.paid'),
                    'partial' => __('ui.salaries.statuses.partial'),
                    default => __('ui.salaries.statuses.unpaid'),
                };

                fputcsv($output, [
                    trim($employeeCode !== '' ? $employeeCode . ' - ' . $employeeName : $employeeName),
                    $salaryPayment->period_start?->format('Y-m-d') ?? '',
                    $salaryPayment->period_end?->format('Y-m-d') ?? '',
                    number_format((float) ($salaryPayment->gross_amount ?? 0), 2, '.', ''),
                    number_format((float) ($salaryPayment->net_amount ?? 0), 2, '.', ''),
                    number_format((float) ($salaryPayment->paid_amount ?? 0), 2, '.', ''),
                    number_format(max((float) ($salaryPayment->net_amount ?? 0) - (float) ($salaryPayment->paid_amount ?? 0), 0), 2, '.', ''),
                    $statusLabel,
                    $salaryPayment->payment_date?->format('Y-m-d') ?? '',
                    trim((string) ($salaryPayment->note ?? '')),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create(Request $request): View
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name', 'base_salary']);

        return view('salaries.create', [
            'employees' => $employees,
            'prefillEmployeeId' => $request->integer('employee_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'bonus_amount' => ['nullable', 'numeric', 'min:0'],
            'other_deduction' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:unpaid,partial,paid'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $periodStart = Carbon::parse($validated['period_start'])->startOfDay();
        $periodEnd = Carbon::parse($validated['period_end'])->endOfDay();

        $baseSalary = (float) ($validated['base_salary'] ?? $employee->base_salary);
        $bonusAmount = (float) ($validated['bonus_amount'] ?? 0);
        $manualOtherDeduction = (float) ($validated['other_deduction'] ?? 0);
        $periodAdjustmentsDeduction = $this->calculateSalaryAdjustmentsDeduction($employee, $periodStart, $periodEnd);
        $otherDeduction = $manualOtherDeduction + $periodAdjustmentsDeduction;
        $attendanceDeduction = $this->calculateAttendanceDeduction($employee, $periodStart, $periodEnd, $baseSalary);

        $grossAmount = $baseSalary + $bonusAmount;
        $netAmount = max($grossAmount - $attendanceDeduction - $otherDeduction, 0);
        $paidAmount = min((float) ($validated['paid_amount'] ?? 0), $netAmount);

        $status = $validated['status'];
        if ($status === 'paid' && $paidAmount <= 0) {
            $paidAmount = $netAmount;
        }

        if ($paidAmount >= $netAmount && $netAmount > 0) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $note = $validated['note'] ?? null;
        if ($periodAdjustmentsDeduction > 0) {
            $adjustmentSummary = 'Includes employee deductions and product charges: ' . number_format($periodAdjustmentsDeduction, 2, '.', '');
            $note = $note ? $note . PHP_EOL . $adjustmentSummary : $adjustmentSummary;
        }

        $salaryPayment = SalaryPayment::query()->create([
            'employee_id' => $employee->id,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'base_salary' => round($baseSalary, 2),
            'attendance_deduction' => round($attendanceDeduction, 2),
            'bonus_amount' => round($bonusAmount, 2),
            'other_deduction' => round($otherDeduction, 2),
            'gross_amount' => round($grossAmount, 2),
            'net_amount' => round($netAmount, 2),
            'paid_amount' => round($paidAmount, 2),
            'payment_date' => $status === 'paid' ? ($validated['payment_date'] ?? now()->toDateString()) : ($validated['payment_date'] ?? null),
            'status' => $status,
            'note' => $note,
            'processed_by' => Auth::id(),
        ]);

        if ($status === 'paid') {
            $this->clearEmployeeSalaryAdjustments($employee);
        }

        return redirect()
            ->route('salaries.show', $salaryPayment)
            ->with('success', __('messages.success.salary_payment_created'));
    }

    public function show(SalaryPayment $salary): View
    {
        $salary->load(['employee', 'processor']);

        return view('salaries.show', [
            'salary' => $salary,
        ]);
    }

    public function markPaid(Request $request, SalaryPayment $salary): RedirectResponse
    {
        $validated = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
        ]);

        $wasPaid = $salary->status === 'paid';

        $newPaidAmount = min(((float) $salary->paid_amount + (float) $validated['paid_amount']), (float) $salary->net_amount);

        $status = $newPaidAmount >= (float) $salary->net_amount
            ? 'paid'
            : 'partial';

        $salary->update([
            'paid_amount' => round($newPaidAmount, 2),
            'payment_date' => $validated['payment_date'],
            'status' => $status,
            'processed_by' => Auth::id(),
        ]);

        if (! $wasPaid && $status === 'paid') {
            $employee = Employee::query()->find($salary->employee_id);
            if ($employee) {
                $this->clearEmployeeSalaryAdjustments($employee);
            }
        }

        return redirect()
            ->route('salaries.show', $salary)
            ->with('success', __('messages.success.salary_payment_updated'));
    }

    private function calculateAttendanceDeduction(Employee $employee, Carbon $periodStart, Carbon $periodEnd, float $baseSalary): float
    {
        $totalDays = max($periodStart->diffInDays($periodEnd) + 1, 1);

        $unpaidUnits = (float) $employee->attendances()
            ->whereBetween('attendance_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 WHEN status = 'half_day' THEN 0.5 ELSE 0 END) as unpaid_units")
            ->value('unpaid_units');

        if ($unpaidUnits <= 0 || $baseSalary <= 0) {
            return 0;
        }

        $dailyRate = $baseSalary / $totalDays;

        return $dailyRate * $unpaidUnits;
    }

    private function calculateSalaryAdjustmentsDeduction(Employee $employee, Carbon $periodStart, Carbon $periodEnd): float
    {
        if (! Schema::hasTable('employee_salary_adjustments')) {
            return 0;
        }

        return (float) $employee->salaryAdjustments()
            ->whereBetween('adjustment_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->sum('amount');
    }

    private function clearEmployeeSalaryAdjustments(Employee $employee): void
    {
        if (! Schema::hasTable('employee_salary_adjustments')) {
            return;
        }

        $employee->salaryAdjustments()->delete();
    }

    private function validateSalaryFilters(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'status' => ['nullable', 'in:unpaid,partial,paid'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
    }

    private function buildSalaryPaymentsQuery(array $filters)
    {
        return SalaryPayment::query()
            ->with('employee:id,employee_code,first_name,last_name')
            ->when($filters['employee_id'] ?? null, fn($query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn($query, string $from) => $query->whereDate('period_start', '>=', $from))
            ->when($filters['to'] ?? null, fn($query, string $to) => $query->whereDate('period_end', '<=', $to))
            ->latest('period_end')
            ->latest('id');
    }
}
