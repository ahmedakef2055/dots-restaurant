@props(['attendance' => null, 'employees', 'prefillEmployeeId' => null, 'prefillDate' => null])

<div class="grid gap-5 md:grid-cols-2">
    {{-- Employee --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Employee <span style="color:var(--error)">*</span></label>
        <div class="relative">
            <select name="employee_id" required class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected((string) old('employee_id', $attendance?->employee_id ?? $prefillEmployeeId) === (string) $employee->id)>{{ $employee->employee_code }} — {{ $employee->full_name }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('employee_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- Date --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Date <span style="color:var(--error)">*</span></label>
        <input name="attendance_date" type="date" value="{{ old('attendance_date', $attendance?->attendance_date?->format('Y-m-d') ?? $prefillDate ?? now()->format('Y-m-d')) }}" required
               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
        @error('attendance_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Status</label>
        <div class="relative">
            <select name="status" class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="" @selected(old('status', $attendance?->status) === null || old('status', $attendance?->status) === '')>Auto (based on check in/out)</option>
                <option value="present" @selected(old('status', $attendance?->status) === 'present')>Present</option>
                <option value="late" @selected(old('status', $attendance?->status) === 'late')>Late</option>
                <option value="half_day" @selected(old('status', $attendance?->status) === 'half_day')>Half Day</option>
                <option value="leave" @selected(old('status', $attendance?->status) === 'leave')>Leave</option>
                <option value="absent" @selected(old('status', $attendance?->status) === 'absent')>Absent</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">Leave empty to auto-detect from check-in/out times</p>
        @error('status')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- Spacer --}}
    <div class="hidden md:block"></div>

    {{-- Check In --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--success)"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg>Check In</span>
        </label>
        <input name="check_in" type="time" value="{{ old('check_in', $attendance?->check_in) }}"
               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
        @error('check_in')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- Check Out --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>Check Out</span>
        </label>
        <input name="check_out" type="time" value="{{ old('check_out', $attendance?->check_out) }}"
               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
        @error('check_out')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- Note --}}
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Note</label>
        <textarea name="note" rows="3" class="w-full glass-input rounded-xl px-4 py-2.5 text-sm" placeholder="Optional notes...">{{ old('note', $attendance?->note) }}</textarea>
        @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>