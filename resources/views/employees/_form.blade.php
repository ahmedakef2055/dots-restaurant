@props([
'employee' => null,
'positionOptions' => [],
'supportsScheduleFields' => false,
'nationalIdValue' => '',
])

@php
$selectedPosition      = old('position', $employee?->position);
$defaultShiftStart     = old('shift_start', $employee?->shift_start_hm ?? '09:00');
$defaultShiftEnd       = old('shift_end', $employee?->shift_end_hm ?? '17:00');
$defaultWorkHours      = old('work_hours_per_day', $employee?->work_hours_per_day ?? 8);
$defaultAttendanceDays = old('attendance_days_per_week', $employee?->attendance_days_per_week ?? 6);
$defaultBaseSalary     = old('base_salary', $employee?->base_salary ?? 0);
$initialDailySalary    = $employee?->daily_salary ?? 0;
$defaultNationalId     = old('national_id', $nationalIdValue);
@endphp

<div class="grid gap-5 md:grid-cols-2" data-daily-salary-calculator data-shift-hours-sync>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Name <span style="color:var(--error)">*</span></label>
        <input name="first_name" value="{{ old('first_name', $employee?->first_name) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('first_name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">National ID <span style="color:var(--error)">*</span></label>
        <input name="national_id" value="{{ $defaultNationalId }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('national_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Phone <span style="color:var(--error)">*</span></label>
        <input name="phone" value="{{ old('phone', $employee?->phone) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('phone')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Position <span style="color:var(--error)">*</span></label>
        <div class="relative">
            <select name="position" required class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="">Select Position</option>
                @foreach($positionOptions as $option)
                <option value="{{ $option }}" @selected($selectedPosition===$option)>{{ $option }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('position')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Hire Date <span style="color:var(--error)">*</span></label>
        <input name="hire_date" type="date"
               value="{{ old('hire_date', $employee?->hire_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('hire_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Monthly Salary <span style="color:var(--error)">*</span></label>
        <input name="base_salary" data-monthly-salary type="number" min="0" step="0.01"
               value="{{ $defaultBaseSalary }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('base_salary')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Daily Salary (Auto)</label>
        <input data-daily-salary-preview type="text" readonly
               value="{{ number_format((float)$initialDailySalary, 2, '.', '') }}"
               class="w-full rounded-xl px-4 py-2.5 text-sm font-mono cursor-not-allowed"
               style="background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent);border:1px solid color-mix(in srgb,var(--outline-var) 20%,transparent);color:var(--on-surface-var)">
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">Calculated from monthly salary and attendance days.</p>
    </div>

    @if($supportsScheduleFields)
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Work Hours / Day <span style="color:var(--error)">*</span></label>
        <input name="work_hours_per_day" data-work-hours type="number" min="1" max="24" step="0.25"
               value="{{ $defaultWorkHours }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('work_hours_per_day')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Attendance Days / Week <span style="color:var(--error)">*</span></label>
        <input name="attendance_days_per_week" data-attendance-days type="number" min="1" max="7" step="1"
               value="{{ $defaultAttendanceDays }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('attendance_days_per_week')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Shift Start <span style="color:var(--error)">*</span></label>
        <input name="shift_start" data-shift-start type="time" value="{{ $defaultShiftStart }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('shift_start')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Shift End <span style="color:var(--error)">*</span></label>
        <input name="shift_end" data-shift-end type="time" value="{{ $defaultShiftEnd }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('shift_end')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
    @else
    <input type="hidden" data-attendance-days value="6">
    @endif

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Status</label>
        <div class="relative">
            <select name="status" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="active"     @selected(old('status', $employee?->status ?? 'active') === 'active')>Active</option>
                <option value="inactive"   @selected(old('status', $employee?->status) === 'inactive')>Inactive</option>
                <option value="terminated" @selected(old('status', $employee?->status) === 'terminated')>Terminated</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('status')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Address</label>
        <textarea name="address" rows="2" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm resize-none">{{ old('address', $employee?->address) }}</textarea>
        @error('address')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none">{{ old('notes', $employee?->notes) }}</textarea>
        @error('notes')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>

@once
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-daily-salary-calculator]').forEach((calculator) => {
            const monthlyInput = calculator.querySelector('[data-monthly-salary]');
            const daysInput = calculator.querySelector('[data-attendance-days]');
            const preview = calculator.querySelector('[data-daily-salary-preview]');
            if (!monthlyInput || !daysInput || !preview) return;
            const compute = () => {
                const monthly = Number.parseFloat(monthlyInput.value || '0');
                const days = Number.parseFloat(daysInput.value || '0');
                const monthlyWorkingDays = days * 4.33;
                if (!Number.isFinite(monthly) || !Number.isFinite(days) || monthly <= 0 || days <= 0 || monthlyWorkingDays <= 0) { preview.value = '0.00'; return; }
                preview.value = (monthly / monthlyWorkingDays).toFixed(2);
            };
            monthlyInput.addEventListener('input', compute);
            daysInput.addEventListener('input', compute);
            compute();
        });

        document.querySelectorAll('[data-shift-hours-sync]').forEach((container) => {
            const workHoursInput = container.querySelector('[data-work-hours]');
            const shiftStartInput = container.querySelector('[data-shift-start]');
            const shiftEndInput = container.querySelector('[data-shift-end]');
            if (!workHoursInput || !shiftStartInput || !shiftEndInput) return;
            const parseTimeToMinutes = (value) => {
                if (typeof value !== 'string') return null;
                const parts = value.split(':');
                if (parts.length < 2) return null;
                const hour = Number.parseInt(parts[0], 10);
                const minute = Number.parseInt(parts[1], 10);
                if (!Number.isFinite(hour) || !Number.isFinite(minute) || hour < 0 || hour > 23 || minute < 0 || minute > 59) return null;
                return (hour * 60) + minute;
            };
            const minutesToTime = (minutes) => {
                const normalized = Math.min(Math.max(Math.round(minutes), 0), (24 * 60) - 1);
                return `${String(Math.floor(normalized / 60)).padStart(2, '0')}:${String(normalized % 60).padStart(2, '0')}`;
            };
            const parseWorkHours = () => {
                const value = Number.parseFloat(workHoursInput.value || '');
                return (Number.isFinite(value) && value > 0) ? value : null;
            };
            const syncWorkHoursFromShiftTimes = () => {
                const startMinutes = parseTimeToMinutes(shiftStartInput.value);
                const endMinutes = parseTimeToMinutes(shiftEndInput.value);
                if (startMinutes === null || endMinutes === null || endMinutes <= startMinutes) return;
                workHoursInput.value = ((endMinutes - startMinutes) / 60).toFixed(2);
            };
            const syncShiftEndFromWorkHours = () => {
                const startMinutes = parseTimeToMinutes(shiftStartInput.value);
                const workHours = parseWorkHours();
                if (startMinutes === null || workHours === null) return;
                const requestedMinutes = Math.max(1, Math.round(workHours * 60));
                const maxAvailableMinutes = ((24 * 60) - 1) - startMinutes;
                const boundedMinutes = Math.min(requestedMinutes, maxAvailableMinutes);
                if (boundedMinutes <= 0) return;
                shiftEndInput.value = minutesToTime(startMinutes + boundedMinutes);
                workHoursInput.value = (boundedMinutes / 60).toFixed(2);
            };
            shiftStartInput.addEventListener('input', () => { if (parseWorkHours() !== null) { syncShiftEndFromWorkHours(); return; } syncWorkHoursFromShiftTimes(); });
            shiftEndInput.addEventListener('input', syncWorkHoursFromShiftTimes);
            workHoursInput.addEventListener('input', syncShiftEndFromWorkHours);
            syncWorkHoursFromShiftTimes();
        });
    });
</script>
@endonce
