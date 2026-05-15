<x-layouts.app title="Employees">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Employees & HR</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Staff management & payroll</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Attendance">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/></svg><span class="hidden sm:inline">Attendance</span>
            </a>
            <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Salaries">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg><span class="hidden sm:inline">Salaries</span>
            </a>
            <a href="{{ route('employees.export.pdf', request()->query()) }}" target="_blank" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Export PDF">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]"><path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg><span class="hidden sm:inline">Export PDF</span>
            </a>
            <a href="{{ route('employees.create') }}" class="glass-button-primary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q440-607 440-640t-23.5-56.5Q393-720 360-720t-56.5 23.5Q280-673 280-640t23.5 56.5Q327-560 360-560t56.5-23.5ZM360-640Zm0 400Z"/></svg><span class="hidden sm:inline">Register Employee</span><span class="sm:hidden">Add</span>
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('employees.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-56">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input name="q" value="{{ $filters['q'] }}" placeholder="Search name/phone/national ID"
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <div class="relative">
            <select name="status" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-36">
                <option value="">All Statuses</option>
                <option value="active"     @selected($filters['status']==='active')>Active</option>
                <option value="inactive"   @selected($filters['status']==='inactive')>Inactive</option>
                <option value="terminated" @selected($filters['status']==='terminated')>Terminated</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <button type="submit" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Filter</button>
        <a href="{{ route('employees.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Reset</a>
    </form>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ $employees->total() }} employees total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Name','National ID','Phone','Position','Salary','Status','Records','Actions'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($employees as $employee)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $employee->full_name }}</td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">{{ $employee->national_id_display ?: '-' }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $employee->phone ?: '-' }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $employee->position }}</td>
                        <td class="px-5 py-3">
                            <div class="font-mono text-sm" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($employee->base_salary) }}</div>
                            <div class="text-xs font-mono" style="color:var(--on-surface-var)">Daily: {{ \App\Support\CurrencyFormatter::format($employee->daily_salary) }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @if($employee->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)">
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color:var(--success)"></span>Active
                            </span>
                            @elseif($employee->status === 'inactive')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--surface-low);border:1px solid color-mix(in srgb,var(--outline) 30%,transparent 70%);color:var(--on-surface-var)">
                                Inactive
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--error-container);border:1px solid color-mix(in srgb,var(--error) 35%,transparent 65%);color:var(--error)">
                                Terminated
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs" style="color:var(--on-surface-var)">
                            A: {{ $employee->attendances_count }}<br>S: {{ $employee->salary_payments_count }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employees.show', $employee) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">View</a>
                                <a href="{{ route('employees.edit', $employee) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline-block">
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
                    <tr><td colspan="8" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $employees->withQueryString()->links() }}</div>
        @endif
    </div>

</x-layouts.app>
