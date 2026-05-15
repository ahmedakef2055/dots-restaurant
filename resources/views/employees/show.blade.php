<x-layouts.app :title="$employee->full_name">
<div
    x-data="{
        editModalId: @js((string) old('adjustment_id', '')),
        deliverySettlementBaseTotal: Number(@js((float) $unsettledDeliveryOrdersTotal)),
        deliverySettlementPercentage: Number(@js((float) old('commission_percentage', 10))) || 10,
        closeEditModal() { this.editModalId = ''; },
        get deliverySettlementShare() {
            const value = this.deliverySettlementBaseTotal * (this.deliverySettlementPercentage / 100);
            return Number.isFinite(value) ? value : 0;
        },
        get deliverySettlementRemaining() {
            const value = this.deliverySettlementBaseTotal - this.deliverySettlementShare;
            return Number.isFinite(value) ? value : 0;
        },
        formatCurrency(value) {
            const locale = document.documentElement.lang === 'ar' ? 'ar-EG' : 'en-US';
            return new Intl.NumberFormat(locale, { style: 'currency', currency: 'EGP' }).format(value || 0);
        },
    }"
    @keydown.escape.window="closeEditModal()">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('employees.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>Employees
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight" style="color:var(--on-surface)">{{ $employee->full_name }}</h1>
                <p class="text-sm" style="color:var(--on-surface-var)">{{ $employee->position }} · {{ $employee->employee_code }}</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('employees.financial-report.pdf', $employee) }}" class="glass-button-secondary rounded-xl py-2 px-3 text-sm font-medium flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z"/></svg>{{ __('ui.employees.financial_report.actions.export_pdf') }}
            </a>
            <a href="{{ route('employees.financial-report.excel', $employee) }}" class="glass-button-secondary rounded-xl py-2 px-3 text-sm font-medium flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120ZM200-640h560v-120H200v120Zm100 80H200v360h100v-360Zm360 0v360h100v-360H660Zm-80 0H380v360h200v-360Z"/></svg>{{ __('ui.employees.financial_report.actions.export_excel') }}
            </a>
            <a href="{{ route('attendance.create', ['employee_id' => $employee->id]) }}" class="glass-button-secondary rounded-xl py-2 px-3 text-sm font-medium flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/></svg>Add Attendance
            </a>
            <a href="{{ route('salaries.create', ['employee_id' => $employee->id]) }}" class="glass-button-secondary rounded-xl py-2 px-3 text-sm font-medium flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>Create Salary
            </a>
            <a href="{{ route('employees.edit', $employee) }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>Edit Employee
            </a>
        </div>
    </div>

    {{-- Profile + Attendance --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-6">

        <div class="glass-panel-elevated rounded-2xl p-6 lg:col-span-2">
            <h2 class="text-base font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M160-80q-33 0-56.5-23.5T80-160v-440q0-33 23.5-56.5T160-680h200v-120q0-33 23.5-56.5T440-880h80q33 0 56.5 23.5T600-800v120h200q33 0 56.5 23.5T880-600v440q0 33-23.5 56.5T800-80H160Zm0-80h640v-440H600q0 33-23.5 56.5T520-520h-80q-33 0-56.5-23.5T360-600H160v440Zm80-80h240v-18q0-17-9.5-31.5T444-312q-20-9-40.5-13.5T360-330q-23 0-43.5 4.5T276-312q-17 8-26.5 22.5T240-258v18Zm320-60h160v-60H560v60Zm-157.5-77.5Q420-395 420-420t-17.5-42.5Q385-480 360-480t-42.5 17.5Q300-445 300-420t17.5 42.5Q335-360 360-360t42.5-17.5ZM560-420h160v-60H560v60ZM440-600h80v-200h-80v200Zm40 220Z"/></svg>
                Employee Profile
            </h2>
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                @foreach([
                    ['Name', $employee->full_name],
                    ['National ID', $employee->national_id_display ?: '-'],
                    ['Position', $employee->position],
                    ['Phone', $employee->phone ?: '-'],
                    ['Hire Date', $employee->hire_date?->format('Y-m-d')],
                    ['Base Salary', \App\Support\CurrencyFormatter::format($employee->base_salary)],
                ] as [$label, $value])
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ $label }}</dt>
                    <dd class="font-medium" style="color:var(--on-surface)">{{ $value }}</dd>
                </div>
                @endforeach
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Current Month Deductions</dt>
                    <dd class="font-medium" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format($currentMonthAdjustmentTotal) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Net Monthly Salary</dt>
                    <dd class="font-semibold text-[var(--success)]">{{ \App\Support\CurrencyFormatter::format($currentMonthNetSalary) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Daily Salary</dt>
                    <dd class="font-medium font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($employee->daily_salary) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Status</dt>
                    <dd>
                        @if($employee->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">Active</span>
                        @elseif($employee->status === 'inactive')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">Inactive</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">Terminated</span>
                        @endif
                    </dd>
                </div>
                @if($employee->address)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Address</dt>
                    <dd style="color:var(--on-surface)">{{ $employee->address }}</dd>
                </div>
                @endif
                @if($employee->notes_without_national_id)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">Notes</dt>
                    <dd class="text-sm italic" style="color:var(--on-surface-var)">{{ $employee->notes_without_national_id }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="glass-panel rounded-2xl p-5">
            <h2 class="text-base font-semibold flex items-center gap-2 mb-4" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--secondary)"><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/></svg>
                Recent Attendance
            </h2>
            <div class="space-y-3 text-sm">
                @forelse($employee->attendances as $attendance)
                <div class="rounded-xl px-3 py-2.5 border" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--primary) 4%,transparent)">
                    <p class="font-semibold" style="color:var(--on-surface)">{{ $attendance->attendance_date?->format('Y-m-d') }}</p>
                    <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">
                        {{ ucfirst(str_replace('_', ' ', $attendance->status)) }} · {{ $attendance->check_in ?: '--:--' }} – {{ $attendance->check_out ?: '--:--' }}
                    </p>
                </div>
                @empty
                <p style="color:var(--on-surface-var)">No attendance records yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Delivery section --}}
    @if($isDeliveryEmployee)
    <div class="glass-panel-elevated rounded-2xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">Delivery Orders & Settlement</h3>
            <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Track delivered orders and settle employee share</p>
        </div>

        @if(! $deliverySettlementFeatureEnabled)
        <p class="px-6 py-4 text-sm" style="color:var(--on-surface-var)">Delivery settlement features are not available yet. Please run latest migrations.</p>
        @else
        <div class="p-6 space-y-6">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl p-4 border"
                     style="border-color:color-mix(in srgb,var(--primary) 12%,transparent);background-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <p class="text-xs mb-1" style="color:var(--on-surface-var)">Delivered Orders</p>
                    <p class="text-lg font-semibold" style="color:var(--on-surface)">{{ $deliveryOrdersCount }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="border-color:color-mix(in srgb,var(--primary) 12%,transparent);background-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <p class="text-xs mb-1" style="color:var(--on-surface-var)">Delivered Total</p>
                    <p class="text-lg font-semibold font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($deliveryOrdersTotal) }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="border-color:color-mix(in srgb,var(--tertiary) 25%,transparent);background-color:color-mix(in srgb,var(--tertiary) 8%,transparent)">
                    <p class="text-xs mb-1" style="color:var(--on-surface-var)">Unsettled Orders</p>
                    <p class="text-lg font-semibold" style="color:var(--tertiary)">{{ $unsettledDeliveryOrdersCount }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="border-color:color-mix(in srgb,var(--tertiary) 25%,transparent);background-color:color-mix(in srgb,var(--tertiary) 8%,transparent)">
                    <p class="text-xs mb-1" style="color:var(--on-surface-var)">Unsettled Total</p>
                    <p class="text-lg font-semibold font-mono" style="color:var(--tertiary)">{{ \App\Support\CurrencyFormatter::format($unsettledDeliveryOrdersTotal) }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('employees.delivery-settlements.store', $employee) }}" class="rounded-xl border p-5 space-y-4"
                  style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 15%,transparent)">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Commission Percentage</label>
                        <input type="number" name="commission_percentage"
                               x-model.number="deliverySettlementPercentage"
                               min="0" max="100" step="0.01" inputmode="decimal" required
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono"
                               placeholder="e.g. 10">
                        @error('commission_percentage')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Settlement Note</label>
                        <textarea name="note" rows="2" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm resize-none" placeholder="Optional settlement note">{{ old('note') }}</textarea>
                        @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <p class="text-xs mb-1" style="color:var(--on-surface-var)">Total Before %</p>
                        <p class="text-base font-semibold font-mono" style="color:var(--on-surface)" x-text="formatCurrency(deliverySettlementBaseTotal)"></p>
                    </div>
                    <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <p class="text-xs mb-1" style="color:var(--on-surface-var)">Selected %</p>
                        <p class="text-base font-semibold font-mono" style="color:var(--on-surface)"><span x-text="deliverySettlementPercentage"></span>%</p>
                    </div>
                    <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--success) 20%,transparent 80%);background-color:color-mix(in srgb,var(--success) 5%,transparent 95%)">
                        <p class="text-xs mb-1" style="color:var(--on-surface-var)">Employee Share</p>
                        <p class="text-base font-semibold font-mono text-[var(--success)]" x-text="formatCurrency(deliverySettlementShare)"></p>
                    </div>
                    <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--primary) 20%,transparent);background-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                        <p class="text-xs mb-1" style="color:var(--on-surface-var)">After %</p>
                        <p class="text-base font-semibold font-mono" style="color:var(--primary)" x-text="formatCurrency(deliverySettlementRemaining)"></p>
                    </div>
                </div>
                <div>
                    <button type="submit"
                            x-bind:disabled="deliverySettlementBaseTotal <= 0"
                            x-bind:class="deliverySettlementBaseTotal <= 0 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold">
                        Settle Delivery Account
                    </button>
                </div>
            </form>

            {{-- Delivery orders table --}}
            <div>
                <h4 class="text-sm font-semibold mb-3" style="color:var(--on-surface)">Delivered Orders (Latest)</h4>
                <div class="glass-panel rounded-xl overflow-hidden">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                                @foreach(['Order #','Total','Date','Settlement'] as $h)
                                <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                            @forelse($deliveryOrders as $deliveryOrder)
                            <tr class="transition-colors" onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseleave="this.style.backgroundColor=''">
                                <td class="px-5 py-3">
                                    <a href="{{ route('orders.show', $deliveryOrder) }}" class="font-semibold hover:underline" style="color:var(--primary)">{{ $deliveryOrder->order_number }}</a>
                                </td>
                                <td class="px-5 py-3 font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($deliveryOrder->total) }}</td>
                                <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $deliveryOrder->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-5 py-3">
                                    @if($deliveryOrder->delivery_settlement_id)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">Settled</span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">No delivered orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Settlement history table --}}
            <div>
                <h4 class="text-sm font-semibold mb-3" style="color:var(--on-surface)">Settlement History</h4>
                <div class="glass-panel rounded-xl overflow-hidden">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                                @foreach(['Date','Orders','Gross Total','%','Delivery Share','After %'] as $h)
                                <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                            @forelse($deliverySettlements as $ds)
                            <tr class="transition-colors" onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseleave="this.style.backgroundColor=''">
                                <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $ds->settled_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-5 py-3" style="color:var(--on-surface)">{{ $ds->order_count }}</td>
                                <td class="px-5 py-3 font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($ds->gross_total) }}</td>
                                <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ rtrim(rtrim(number_format((float)$ds->commission_percentage, 2, '.', ''), '0'), '.') }}%</td>
                                <td class="px-5 py-3 font-semibold font-mono text-[var(--success)]">{{ \App\Support\CurrencyFormatter::format($ds->commission_amount) }}</td>
                                <td class="px-5 py-3 font-semibold font-mono" style="color:var(--primary)">{{ \App\Support\CurrencyFormatter::format($ds->restaurant_share_amount) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">No settlement records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Salary Deductions --}}
    @if($salaryAdjustmentsEnabled)
    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        <div class="glass-panel-elevated rounded-2xl p-6">
            <h3 class="text-base font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--error)"><path d="M280-440h400v-80H280v80ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                Salary Deductions
            </h3>
            <form method="POST" action="{{ route('employees.deductions.store', $employee) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Amount</label>
                    <input name="amount" type="number" min="0.01" step="0.01" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                    @error('amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Date</label>
                    <input name="adjustment_date" type="date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                    @error('adjustment_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Reason</label>
                    <textarea name="note" rows="3" required
                              class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none"
                              placeholder="Reason for deduction"></textarea>
                    @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="rounded-xl py-2.5 px-6 text-sm font-semibold transition-all"
                        style="background-color:color-mix(in srgb,var(--error) 12%,transparent);border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 20%,transparent)'"
                        onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--error) 12%,transparent)'">
                    Apply Deduction
                </button>
            </form>
        </div>

        <div class="glass-panel-elevated rounded-2xl p-6">
            <h3 class="text-base font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--secondary)"><path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z"/></svg>
                Load Product Price
            </h3>
            <form method="POST" action="{{ route('employees.product-charges.store', $employee) }}" class="space-y-4">
                @csrf
                @if($errors->has('items'))
                <div class="rounded-xl px-4 py-3 text-xs border" style="border-color:color-mix(in srgb,var(--error) 20%,transparent);background-color:color-mix(in srgb,var(--error) 8%,transparent);color:var(--error)">
                    @foreach($errors->get('items') as $stockError)<p>{{ $stockError }}</p>@endforeach
                </div>
                @endif
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Product</label>
                    <div class="relative">
                        <select name="product_id" required class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ \App\Support\CurrencyFormatter::format($product->price) }})</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                    </div>
                    @error('product_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Quantity</label>
                    <input name="quantity" type="number" min="1" step="0.01" value="1" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                    @error('quantity')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Date</label>
                    <input name="adjustment_date" type="date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                    @error('adjustment_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Comment</label>
                    <textarea name="note" rows="2" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm resize-none" placeholder="Optional note"></textarea>
                    @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold">Add Product Charge</button>
            </form>
        </div>
    </div>

    {{-- Adjustments table --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">Recent Salary Adjustments</h3>
            <div class="grid gap-3 sm:grid-cols-3 mt-3">
                <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                    <p class="text-xs mb-0.5" style="color:var(--on-surface-var)">Base Salary</p>
                    <p class="font-semibold font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($employee->base_salary) }}</p>
                </div>
                <div class="rounded-xl p-3 border" style="border-color:color-mix(in srgb,var(--error) 20%,transparent);background-color:color-mix(in srgb,var(--error) 5%,transparent)">
                    <p class="text-xs mb-0.5" style="color:var(--on-surface-var)">Deductions</p>
                    <p class="font-semibold font-mono" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format($currentMonthAdjustmentTotal) }}</p>
                </div>
                <div class="rounded-xl p-3 border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)] bg-[color-mix(in_srgb,var(--success)_5%,transparent_95%)]">
                    <p class="text-xs mb-0.5" style="color:var(--on-surface-var)">Net Salary</p>
                    <p class="font-semibold font-mono text-[var(--success)]">{{ \App\Support\CurrencyFormatter::format($currentMonthNetSalary) }}</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Date','Type','Details','Amount','Actions'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($employee->salaryAdjustments as $adjustment)
                    <tr class="transition-colors" onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $adjustment->adjustment_date?->format('Y-m-d') }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $adjustment->type_label }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            @if($adjustment->product)
                            {{ $adjustment->product->name }} × {{ $adjustment->quantity }} @ {{ \App\Support\CurrencyFormatter::format($adjustment->unit_price) }}
                            @else
                            {{ $adjustment->note ?: '-' }}
                            @endif
                            @if($adjustment->note && $adjustment->product)
                            <div class="text-xs mt-0.5" style="color:var(--on-surface-var)">{{ $adjustment->note }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format($adjustment->amount) }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="editModalId = '{{ $adjustment->id }}'"
                                        class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Edit</button>
                                <form method="POST" action="{{ route('employees.adjustments.destroy', [$employee, $adjustment]) }}"
                                      onsubmit="return confirm('Delete this transaction?');" class="inline-block">
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
                    <tr><td colspan="5" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">No salary adjustments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit adjustment modals --}}
    @foreach($employee->salaryAdjustments as $adjustment)
    @php
    $isCurrentAdjustment = (string) old('adjustment_id') === (string) $adjustment->id;
    $oldAmount = $isCurrentAdjustment ? old('amount', (float) $adjustment->amount) : (float) $adjustment->amount;
    $oldProductId = (string) ($isCurrentAdjustment ? old('product_id', $adjustment->product_id) : $adjustment->product_id);
    $oldQuantity = $isCurrentAdjustment ? old('quantity', (float) $adjustment->quantity) : (float) $adjustment->quantity;
    $oldAdjustmentDate = $isCurrentAdjustment ? old('adjustment_date', $adjustment->adjustment_date?->format('Y-m-d')) : $adjustment->adjustment_date?->format('Y-m-d');
    $oldNote = $isCurrentAdjustment ? old('note', $adjustment->note) : $adjustment->note;
    @endphp
    <div x-cloak x-show="editModalId === '{{ $adjustment->id }}'"
         class="fixed inset-0 z-80 flex items-center justify-center p-4"
         role="dialog" aria-modal="true">
        <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.6)" @click="editModalId = ''"></div>
        <div x-show="editModalId === '{{ $adjustment->id }}'"
             x-transition:enter="transform ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="glass-panel-elevated relative w-full max-w-lg rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-lg font-semibold" style="color:var(--on-surface)">Edit Salary Transaction</h3>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold"
                      style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                    {{ $adjustment->type_label }}
                </span>
            </div>
            <form method="POST" action="{{ route('employees.adjustments.update', [$employee, $adjustment]) }}" class="space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="adjustment_id" value="{{ $adjustment->id }}">
                @if($adjustment->type === 'manual_deduction')
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Amount</label>
                    <input name="amount" type="number" min="0.01" step="0.01" required value="{{ $oldAmount }}"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                    @error('amount', 'adjustmentUpdate')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                @else
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Product</label>
                    <div class="relative">
                        <select name="product_id" required class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected((string)$product->id === $oldProductId)>{{ $product->name }} ({{ \App\Support\CurrencyFormatter::format($product->price) }})</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                    </div>
                    @error('product_id', 'adjustmentUpdate')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Quantity</label>
                    <input name="quantity" type="number" min="1" step="0.01" required value="{{ $oldQuantity }}"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                    @error('quantity', 'adjustmentUpdate')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                @endif
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Date</label>
                    <input name="adjustment_date" type="date" required value="{{ $oldAdjustmentDate }}"
                           class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                    @error('adjustment_date', 'adjustmentUpdate')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                        {{ $adjustment->type === 'manual_deduction' ? 'Reason' : 'Comment (optional)' }}
                    </label>
                    <textarea name="note" rows="3" @required($adjustment->type === 'manual_deduction')
                              class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none"
                              placeholder="{{ $adjustment->type === 'manual_deduction' ? 'Reason for deduction' : 'Optional note' }}">{{ $oldNote }}</textarea>
                    @error('note', 'adjustmentUpdate')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editModalId = ''" class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">Cancel</button>
                    <button type="submit" class="glass-button-primary rounded-xl py-2.5 px-6 text-sm font-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    @else
    <div class="glass-panel rounded-2xl px-6 py-5 mb-6">
        <h3 class="text-base font-semibold mb-2" style="color:var(--on-surface)">Salary Deductions</h3>
        <p class="text-sm" style="color:var(--on-surface-var)">Employee deductions and product charges are disabled until the latest migrations are applied.</p>
    </div>
    @endif

    {{-- Salary records --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">Recent Salary Records</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['Period','Gross','Net','Paid','Status','Action'] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($employee->salaryPayments as $salary)
                    <tr class="transition-colors" onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $salary->period_start?->format('Y-m-d') }} → {{ $salary->period_end?->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($salary->gross_amount) }}</td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($salary->net_amount) }}</td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($salary->paid_amount) }}</td>
                        <td class="px-5 py-3">
                            @php
                            $sc = ['paid'=>'bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]',
                                   'partial'=>'border text-[color:var(--tertiary)]',
                                   'unpaid'=>'border text-[color:var(--error)]'][$salary->status] ?? '';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($salary->status) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('salaries.show', $salary) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-sm" style="color:var(--on-surface-var)">No salary records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</x-layouts.app>
