<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Support\PdfExportRenderer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    private const ATTENDANCE_EXPORT_LIMIT = 600;

    public function index(Request $request): View
    {
        $validated = $this->validateAttendanceFilters($request);

        $attendances = $this->buildAttendanceQuery($validated)
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('attendance.index', [
            'attendances' => $attendances,
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
        $validated = $this->validateAttendanceFilters($request);

        $baseQuery = $this->buildAttendanceQuery($validated);
        $totalCount = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->limit(self::ATTENDANCE_EXPORT_LIMIT)
            ->get([
                'id',
                'employee_id',
                'attendance_date',
                'status',
                'check_in',
                'check_out',
                'work_minutes',
                'note',
            ])
            ->map(static function (Attendance $attendance): array {
                $employeeCode = trim((string) ($attendance->employee?->employee_code ?? ''));
                $employeeName = trim((string) ($attendance->employee?->full_name ?? ''));
                $status = strtolower((string) ($attendance->status ?? 'absent'));

                if (! in_array($status, ['present', 'absent', 'late', 'half_day', 'leave'], true)) {
                    $status = 'absent';
                }

                $checkIn = trim((string) ($attendance->check_in ?? ''));
                $checkOut = trim((string) ($attendance->check_out ?? ''));

                if (strlen($checkIn) >= 5) {
                    $checkIn = substr($checkIn, 0, 5);
                }

                if (strlen($checkOut) >= 5) {
                    $checkOut = substr($checkOut, 0, 5);
                }

                $workedHours = $attendance->work_minutes !== null
                    ? round(((int) $attendance->work_minutes) / 60, 2)
                    : null;

                return [
                    'attendance_date' => $attendance->attendance_date?->format('Y-m-d') ?? '',
                    'employee_label' => trim($employeeCode !== '' ? $employeeCode . ' - ' . $employeeName : $employeeName),
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'time_label' => trim(($checkIn !== '' ? $checkIn : '--:--') . ' - ' . ($checkOut !== '' ? $checkOut : '--:--')),
                    'work_hours' => $workedHours,
                    'note' => trim((string) ($attendance->note ?? '')),
                ];
            })
            ->values()
            ->all();

        $employeeFilterLabel = '';
        if ($validated['employee_id'] ?? null) {
            $selectedEmployee = Employee::query()->find((int) $validated['employee_id']);

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
            'attendance.exports.attendance-pdf',
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
            'attendance-report-' . now()->format('Ymd_His') . '.pdf',
            route('attendance.index', $fallbackFilters)
        );
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $validated = $this->validateAttendanceFilters($request);

        $rows = $this->buildAttendanceQuery($validated)
            ->limit(self::ATTENDANCE_EXPORT_LIMIT)
            ->get([
                'id',
                'employee_id',
                'attendance_date',
                'status',
                'check_in',
                'check_out',
                'work_minutes',
                'note',
            ]);

        $fileName = 'attendance-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                __('ui.attendance.exports.excel.headers.date'),
                __('ui.attendance.exports.excel.headers.employee'),
                __('ui.attendance.exports.excel.headers.status'),
                __('ui.attendance.exports.excel.headers.check_in'),
                __('ui.attendance.exports.excel.headers.check_out'),
                __('ui.attendance.exports.excel.headers.work_hours'),
                __('ui.attendance.exports.excel.headers.note'),
            ]);

            foreach ($rows as $attendance) {
                $employeeCode = trim((string) ($attendance->employee?->employee_code ?? ''));
                $employeeName = trim((string) ($attendance->employee?->full_name ?? ''));
                $status = strtolower((string) ($attendance->status ?? 'absent'));
                $statusLabel = match ($status) {
                    'present' => __('ui.attendance.statuses.present'),
                    'late' => __('ui.attendance.statuses.late'),
                    'half_day' => __('ui.attendance.statuses.half_day'),
                    'leave' => __('ui.attendance.statuses.leave'),
                    default => __('ui.attendance.statuses.absent'),
                };

                $checkIn = trim((string) ($attendance->check_in ?? ''));
                $checkOut = trim((string) ($attendance->check_out ?? ''));

                if (strlen($checkIn) >= 5) {
                    $checkIn = substr($checkIn, 0, 5);
                }

                if (strlen($checkOut) >= 5) {
                    $checkOut = substr($checkOut, 0, 5);
                }

                $workedHours = $attendance->work_minutes !== null
                    ? round(((int) $attendance->work_minutes) / 60, 2)
                    : null;

                fputcsv($output, [
                    $attendance->attendance_date?->format('Y-m-d') ?? '',
                    trim($employeeCode !== '' ? $employeeCode . ' - ' . $employeeName : $employeeName),
                    $statusLabel,
                    $checkIn,
                    $checkOut,
                    $workedHours !== null ? number_format($workedHours, 2, '.', '') : '',
                    trim((string) ($attendance->note ?? '')),
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
            ->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('attendance.create', [
            'employees' => $employees,
            'prefillEmployeeId' => $request->integer('employee_id') ?: null,
            'prefillDate' => $request->input('date', now()->toDateString()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAttendance($request);
        $employee = Employee::query()->findOrFail($validated['employee_id']);

        $validated['status'] = $validated['status']
            ?? $this->resolveAttendanceStatus($employee, $validated['check_in'] ?? null, $validated['check_out'] ?? null);
        $validated['work_minutes'] = $this->calculateWorkMinutes($validated['check_in'] ?? null, $validated['check_out'] ?? null);
        $validated['recorded_by'] = Auth::id();

        Attendance::query()->create($validated);

        return redirect()
            ->route('attendance.index')
            ->with('success', __('messages.success.attendance_created'));
    }

    public function edit(Attendance $attendance): View
    {
        $employees = Employee::query()
            ->where('status', 'active')
            ->orWhere('id', $attendance->employee_id)
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('attendance.edit', [
            'attendance' => $attendance,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $this->validateAttendance($request, $attendance->id);
        $employee = Employee::query()->findOrFail($validated['employee_id']);

        $validated['status'] = $validated['status']
            ?? $this->resolveAttendanceStatus($employee, $validated['check_in'] ?? null, $validated['check_out'] ?? null);
        $validated['work_minutes'] = $this->calculateWorkMinutes($validated['check_in'] ?? null, $validated['check_out'] ?? null);
        $validated['recorded_by'] = Auth::id();

        $attendance->update($validated);

        return redirect()
            ->route('attendance.index')
            ->with('success', __('messages.success.attendance_updated'));
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()
            ->route('attendance.index')
            ->with('success', __('messages.success.attendance_deleted'));
    }

    public function quickCheckIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i'],
        ]);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $attendance = Attendance::query()->firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $validated['attendance_date'],
        ]);

        $attendance->check_in = $validated['check_in'];
        $attendance->status = $this->resolveAttendanceStatus($employee, $attendance->check_in, $attendance->check_out);
        $attendance->work_minutes = $this->calculateWorkMinutes($attendance->check_in, $attendance->check_out);
        $attendance->recorded_by = Auth::id();
        $attendance->save();

        return redirect()
            ->route('employees.index')
            ->with('success', __('messages.success.attendance_check_in_recorded'));
    }

    public function quickCheckOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'check_out' => ['required', 'date_format:H:i'],
        ]);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $attendance = Attendance::query()->firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $validated['attendance_date'],
        ]);

        $attendance->check_out = $validated['check_out'];
        $attendance->status = $this->resolveAttendanceStatus($employee, $attendance->check_in, $attendance->check_out);
        $attendance->work_minutes = $this->calculateWorkMinutes($attendance->check_in, $attendance->check_out);
        $attendance->recorded_by = Auth::id();
        $attendance->save();

        return redirect()
            ->route('employees.index')
            ->with('success', __('messages.success.attendance_check_out_recorded'));
    }

    private function validateAttendanceFilters(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'status' => ['nullable', 'in:present,absent,late,half_day,leave'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
    }

    private function buildAttendanceQuery(array $filters)
    {
        return Attendance::query()
            ->with('employee:id,employee_code,first_name,last_name')
            ->when($filters['employee_id'] ?? null, fn($query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['status'] ?? null, fn($query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn($query, string $from) => $query->whereDate('attendance_date', '>=', $from))
            ->when($filters['to'] ?? null, fn($query, string $to) => $query->whereDate('attendance_date', '<=', $to))
            ->latest('attendance_date')
            ->latest('id');
    }

    private function validateAttendance(Request $request, ?int $attendanceId = null): array
    {
        return $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'attendance_date' => [
                'required',
                'date',
                Rule::unique('attendances')
                    ->where(fn($query) => $query->where('employee_id', $request->integer('employee_id')))
                    ->ignore($attendanceId),
            ],
            'status' => ['nullable', 'in:present,absent,late,half_day,leave'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function calculateWorkMinutes(?string $checkIn, ?string $checkOut): ?int
    {
        if (! $checkIn || ! $checkOut) {
            return null;
        }

        $in = $this->parseTime($checkIn);
        $out = $this->parseTime($checkOut);

        if (! $in || ! $out) {
            return null;
        }

        if ($out->lessThanOrEqualTo($in)) {
            return null;
        }

        return $in->diffInMinutes($out);
    }

    private function resolveAttendanceStatus(Employee $employee, ?string $checkIn, ?string $checkOut): string
    {
        if (! $checkIn) {
            return 'absent';
        }

        $shiftStart = $this->parseTime($employee->shift_start);
        $checkInTime = $this->parseTime($checkIn);

        $isLate = false;
        if ($shiftStart && $checkInTime) {
            $isLate = $checkInTime->greaterThan($shiftStart->copy()->addMinutes(15));
        }

        if ($checkOut) {
            $expectedMinutes = max((int) round(((float) $employee->work_hours_per_day ?: 8) * 60), 1);
            $workedMinutes = $this->calculateWorkMinutes($checkIn, $checkOut);

            if ($workedMinutes === null || $workedMinutes < (int) round($expectedMinutes * 0.6)) {
                return 'half_day';
            }
        }

        return $isLate ? 'late' : 'present';
    }

    private function parseTime(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        $normalized = strlen($value) >= 5 ? substr($value, 0, 5) : $value;

        try {
            return Carbon::createFromFormat('H:i', $normalized);
        } catch (\Throwable) {
            return null;
        }
    }
}
