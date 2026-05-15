<x-layouts.app title="Add Attendance">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>Back to Attendance
            </a>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="glass-panel-elevated rounded-2xl overflow-hidden">
            {{-- Card header --}}
            <div class="px-6 py-5 border-b"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 4%,transparent),color-mix(in srgb,var(--tertiary) 3%,transparent))">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl"
                          style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" ><path d="M438-226 296-368l58-58 84 84 168-168 58 58-226 226ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold" style="color:var(--on-surface)">Record Attendance</h2>
                        <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Track employee attendance status, check-in &amp; check-out times</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('attendance.store') }}" class="px-6 py-5">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    {{-- Employee --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Employee <span style="color:var(--error)">*</span></label>
                        <div class="relative">
                            <select name="employee_id" required class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected((string) old('employee_id', $prefillEmployeeId) === (string) $employee->id)>{{ $employee->employee_code }} — {{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                        </div>
                        @error('employee_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Date <span style="color:var(--error)">*</span></label>
                        <input name="attendance_date" type="date" value="{{ old('attendance_date', $prefillDate ?? now()->format('Y-m-d')) }}" required
                               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                        @error('attendance_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Status</label>
                        <div class="relative">
                            <select name="status" class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                <option value="" @selected(old('status') === null || old('status') === '')>Auto (based on check in/out)</option>
                                <option value="present" @selected(old('status') === 'present')>Present</option>
                                <option value="late" @selected(old('status') === 'late')>Late</option>
                                <option value="half_day" @selected(old('status') === 'half_day')>Half Day</option>
                                <option value="leave" @selected(old('status') === 'leave')>Leave</option>
                                <option value="absent" @selected(old('status') === 'absent')>Absent</option>
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                        </div>
                        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">Leave empty to auto-detect from check-in/out times</p>
                        @error('status')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>

                    {{-- Spacer for grid alignment --}}
                    <div class="hidden md:block"></div>

                    {{-- Check In --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--success)"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg>Check In</span>
                        </label>
                        <input name="check_in" type="time" value="{{ old('check_in') }}"
                               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                        @error('check_in')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>

                    {{-- Check Out --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>Check Out</span>
                        </label>
                        <input name="check_out" type="time" value="{{ old('check_out') }}"
                               class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                        @error('check_out')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>

                    {{-- Note --}}
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Note</label>
                        <textarea name="note" rows="3" class="w-full glass-input rounded-xl px-4 py-2.5 text-sm" placeholder="Optional notes about this attendance record...">{{ old('note') }}</textarea>
                        @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="rounded-xl py-2.5 px-6 text-sm font-semibold transition-all flex items-center gap-2"
                            style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));color:var(--on-primary);box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)"
                            onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--primary) 40%,transparent)'"
                            onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>Save Attendance
                    </button>
                    <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>