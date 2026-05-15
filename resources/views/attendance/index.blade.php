<x-layouts.app title="Attendance">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Attendance</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Track & manage employee attendance records</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('employees.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Employees">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/></svg><span class="hidden sm:inline">Employees</span>
            </a>
            <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Salaries">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg><span class="hidden sm:inline">Salaries</span>
            </a>
            <a href="{{ route('attendance.export.pdf') }}?{{ http_build_query(array_filter($filters)) }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="PDF">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z"/></svg><span class="hidden sm:inline">PDF</span>
            </a>
            <a href="{{ route('attendance.export.excel') }}?{{ http_build_query(array_filter($filters)) }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="CSV">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg><span class="hidden sm:inline">CSV</span>
            </a>
            <a href="{{ route('attendance.create') }}" class="glass-button-primary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>Add
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('attendance.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative min-w-52">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">Employee</label>
            <div class="relative">
                <select name="employee_id" class="w-full glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) $filters['employee_id']===(string) $employee->id)>{{ $employee->employee_code }} — {{ $employee->full_name }}</option>
                    @endforeach
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
            </div>
        </div>
        <div class="relative min-w-36">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">Status</label>
            <div class="relative">
                <select name="status" class="w-full glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                    <option value="">All Statuses</option>
                    <option value="present" @selected($filters['status']==='present')>Present</option>
                    <option value="late" @selected($filters['status']==='late')>Late</option>
                    <option value="half_day" @selected($filters['status']==='half_day')>Half Day</option>
                    <option value="leave" @selected($filters['status']==='leave')>Leave</option>
                    <option value="absent" @selected($filters['status']==='absent')>Absent</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
            </div>
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">From</label>
            <input type="date" name="from" value="{{ $filters['from'] }}" class="w-full glass-input rounded-xl px-4 py-2 text-sm">
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">To</label>
            <input type="date" name="to" value="{{ $filters['to'] }}" class="w-full glass-input rounded-xl px-4 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2 pb-px">
            <button type="submit" class="glass-button-primary rounded-xl py-2 px-5 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/></svg>Filter
            </button>
            <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Reset</a>
        </div>
    </form>

    {{-- Quick Attendance --}}
    <div class="grid gap-4 lg:grid-cols-2 mb-6">
        {{-- Quick Check-In --}}
        <div class="glass-panel rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b flex items-center gap-2"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--success) 6%,transparent 94%),color-mix(in srgb,var(--primary) 4%,transparent 96%))">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--success)"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg>
                <span class="text-sm font-semibold" style="color:var(--on-surface)">Quick Check-In</span>
            </div>
            <form method="POST" action="{{ route('attendance.quick-check-in') }}" class="px-5 py-4 grid gap-3">
                @csrf
                <div class="relative">
                    <select name="employee_id" required class="w-full glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) old('employee_id')===(string) $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <input type="date" name="attendance_date" value="{{ old('attendance_date', now()->format('Y-m-d')) }}" required class="glass-input rounded-xl px-4 py-2 text-sm">
                    <input type="time" name="check_in" value="{{ old('check_in', now()->format('H:i')) }}" required class="glass-input rounded-xl px-4 py-2 text-sm">
                </div>
                <button type="submit" class="w-full rounded-xl py-2.5 text-sm font-semibold transition-all flex items-center justify-center gap-2"
                        style="background:linear-gradient(135deg,var(--success),color-mix(in srgb,var(--success) 80%,#000 20%));color:#fff;box-shadow:0 4px 14px color-mix(in srgb,var(--success) 25%,transparent 75%)"
                        onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--success) 35%,transparent 65%)'"
                        onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--success) 25%,transparent 75%)'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg>Confirm Check-In
                </button>
            </form>
        </div>

        {{-- Quick Check-Out --}}
        <div class="glass-panel rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b flex items-center gap-2"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 6%,transparent),color-mix(in srgb,var(--tertiary) 4%,transparent))">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                <span class="text-sm font-semibold" style="color:var(--on-surface)">Quick Check-Out</span>
            </div>
            <form method="POST" action="{{ route('attendance.quick-check-out') }}" class="px-5 py-4 grid gap-3">
                @csrf
                <div class="relative">
                    <select name="employee_id" required class="w-full glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) old('employee_id')===(string) $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <input type="date" name="attendance_date" value="{{ old('attendance_date', now()->format('Y-m-d')) }}" required class="glass-input rounded-xl px-4 py-2 text-sm">
                    <input type="time" name="check_out" value="{{ old('check_out', now()->format('H:i')) }}" required class="glass-input rounded-xl px-4 py-2 text-sm">
                </div>
                <button type="submit" class="w-full glass-button-secondary rounded-xl py-2.5 text-sm font-semibold flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>Confirm Check-Out
                </button>
            </form>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm font-medium" style="color:var(--on-surface-var)">{{ $attendances->total() }} records total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Date', 'Employee', 'Status', 'Check In', 'Check Out', 'Work Hours', 'Actions'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($attendances as $attendance)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $attendance->attendance_date?->format('Y-m-d') }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                                    {{ mb_strtoupper(mb_substr($attendance->employee?->first_name ?? '?', 0, 1)) }}
                                </span>
                                <span>
                                    <span class="font-mono text-xs" style="color:var(--on-surface-var)">{{ $attendance->employee?->employee_code }}</span>
                                    <span class="font-medium" style="color:var(--on-surface)">{{ $attendance->employee?->full_name }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @php $st = strtolower($attendance->status ?? 'absent'); @endphp
                            @if($st === 'present')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)]"></span>Present
                            </span>
                            @elseif($st === 'late')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--tertiary)"></span>Late
                            </span>
                            @elseif($st === 'half_day')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary)">
                                Half Day
                            </span>
                            @elseif($st === 'leave')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border:1px solid color-mix(in srgb,var(--secondary) 20%,transparent);color:var(--secondary)">
                                Leave
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                Absent
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">{{ $attendance->check_in ?: '--:--' }}</td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">{{ $attendance->check_out ?: '--:--' }}</td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">
                            @if($attendance->work_minutes)
                                {{ number_format($attendance->work_minutes / 60, 1) }}h
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('attendance.edit', $attendance) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>Edit
                                </a>
                                <form method="POST" action="{{ route('attendance.destroy', $attendance) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--outline)"><path d="m388-212-56-56 92-92-92-92 56-56 92 92 92-92 56 56-92 92 92 92-56 56-92-92-92 92ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                            <p class="text-sm font-medium" style="color:var(--on-surface-var)">No attendance records found</p>
                            <p class="text-xs mt-1" style="color:var(--outline)">Add attendance records manually or use quick check-in above</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $attendances->links() }}</div>
        @endif
    </div>

</x-layouts.app>