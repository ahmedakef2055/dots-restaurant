<x-layouts.app title="Salaries">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Salaries</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Payroll management &amp; payment tracking</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('employees.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Employees">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/></svg><span class="hidden sm:inline">Employees</span>
            </a>
            <a href="{{ route('attendance.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Attendance">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/></svg><span class="hidden sm:inline">Attendance</span>
            </a>
            <a href="{{ route('salaries.export.pdf') }}?{{ http_build_query(array_filter($filters)) }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="PDF">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z"/></svg><span class="hidden sm:inline">PDF</span>
            </a>
            <a href="{{ route('salaries.export.excel') }}?{{ http_build_query(array_filter($filters)) }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="CSV">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg><span class="hidden sm:inline">CSV</span>
            </a>
            <a href="{{ route('salaries.create') }}" class="glass-button-primary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg><span class="hidden sm:inline">Create</span><span class="sm:hidden">Add</span>
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('salaries.index') }}"
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
                    <option value="unpaid" @selected($filters['status']==='unpaid')>Unpaid</option>
                    <option value="partial" @selected($filters['status']==='partial')>Partial</option>
                    <option value="paid" @selected($filters['status']==='paid')>Paid</option>
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
            <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm font-medium" style="color:var(--on-surface-var)">{{ $salaryPayments->total() }} salary records total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Employee', 'Period', 'Gross', 'Net', 'Paid', 'Remaining', 'Status', 'Actions'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($salaryPayments as $salary)
                    @php
                        $remaining = max((float) $salary->net_amount - (float) $salary->paid_amount, 0);
                        $statusKey = strtolower($salary->status ?? 'unpaid');
                    @endphp
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                                    {{ mb_strtoupper(mb_substr($salary->employee?->first_name ?? '?', 0, 1)) }}
                                </span>
                                <span>
                                    <span class="font-mono text-xs" style="color:var(--on-surface-var)">{{ $salary->employee?->employee_code }}</span>
                                    <span class="font-medium" style="color:var(--on-surface)">{{ $salary->employee?->full_name }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            <div class="text-xs font-mono">{{ $salary->period_start?->format('Y-m-d') }}</div>
                            <div class="text-xs font-mono" style="color:var(--outline)">→ {{ $salary->period_end?->format('Y-m-d') }}</div>
                        </td>
                        <td class="px-5 py-3 font-mono text-sm" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($salary->gross_amount) }}</td>
                        <td class="px-5 py-3 font-mono text-sm font-medium" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($salary->net_amount) }}</td>
                        <td class="px-5 py-3 font-mono text-sm" style="color:var(--success)">{{ \App\Support\CurrencyFormatter::format($salary->paid_amount) }}</td>
                        <td class="px-5 py-3 font-mono text-sm" style="color:{{ $remaining > 0 ? 'var(--error)' : 'var(--on-surface-var)' }}">
                            {{ \App\Support\CurrencyFormatter::format($remaining) }}
                        </td>
                        <td class="px-5 py-3">
                            @if($statusKey === 'paid')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)]"></span>Paid
                            </span>
                            @elseif($statusKey === 'partial')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--tertiary)"></span>Partial
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--error)"></span>Unpaid
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('salaries.show', $salary) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM214-281.5Q94-363 40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200q-146 0-266-81.5ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/></svg>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-14 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--outline)"><path d="M200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v100h-80v-100H200v560h560v-100h80v100q0 33-23.5 56.5T760-120H200Zm320-160q-33 0-56.5-23.5T440-360v-240q0-33 23.5-56.5T520-680h280q33 0 56.5 23.5T880-600v240q0 33-23.5 56.5T800-280H520Zm280-80v-240H520v240h280Zm-117.5-77.5Q700-455 700-480t-17.5-42.5Q665-540 640-540t-42.5 17.5Q580-505 580-480t17.5 42.5Q615-420 640-420t42.5-17.5Z"/></svg>
                            <p class="text-sm font-medium" style="color:var(--on-surface-var)">No salary records found</p>
                            <p class="text-xs mt-1" style="color:var(--outline)">Create a salary record for an employee to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($salaryPayments->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $salaryPayments->links() }}</div>
        @endif
    </div>

</x-layouts.app>