<x-layouts.app title="Salary Record">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>Back to Salaries
            </a>
            <a href="{{ route('employees.show', $salary->employee) }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>Employee Profile
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Payroll Summary --}}
        <div class="glass-panel-elevated rounded-2xl overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 4%,transparent),color-mix(in srgb,var(--tertiary) 3%,transparent))">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl"
                          style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" ><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold" style="color:var(--on-surface)">Payroll Summary</h2>
                        <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Salary record #{{ $salary->id }}</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-5">
                <div class="grid gap-4 text-sm sm:grid-cols-2">
                    @php
                    $fields = [
                        ['label' => 'Employee', 'value' => ($salary->employee?->employee_code ? $salary->employee->employee_code . ' — ' : '') . ($salary->employee?->full_name ?? '-'), 'icon' => 'person'],
                        ['label' => 'Period', 'value' => ($salary->period_start?->format('Y-m-d') ?? '-') . ' → ' . ($salary->period_end?->format('Y-m-d') ?? '-'), 'icon' => 'date_range'],
                        ['label' => 'Base Salary', 'value' => \App\Support\CurrencyFormatter::format($salary->base_salary), 'icon' => 'account_balance'],
                        ['label' => 'Attendance Deduction', 'value' => \App\Support\CurrencyFormatter::format($salary->attendance_deduction), 'icon' => 'event_busy', 'color' => 'var(--error)'],
                        ['label' => 'Bonus', 'value' => \App\Support\CurrencyFormatter::format($salary->bonus_amount), 'icon' => 'redeem', 'color' => 'var(--success)'],
                        ['label' => 'Other Deduction', 'value' => \App\Support\CurrencyFormatter::format($salary->other_deduction), 'icon' => 'remove_circle_outline', 'color' => 'var(--error)'],
                        ['label' => 'Gross Amount', 'value' => \App\Support\CurrencyFormatter::format($salary->gross_amount), 'icon' => 'calculate'],
                        ['label' => 'Net Amount', 'value' => \App\Support\CurrencyFormatter::format($salary->net_amount), 'icon' => 'price_check', 'bold' => true],
                        ['label' => 'Paid Amount', 'value' => \App\Support\CurrencyFormatter::format($salary->paid_amount), 'icon' => 'paid', 'color' => 'var(--success)'],
                        ['label' => 'Payment Date', 'value' => $salary->payment_date?->format('Y-m-d') ?: '-', 'icon' => 'calendar_today'],
                        ['label' => 'Processed By', 'value' => $salary->processor?->name ?: '-', 'icon' => 'admin_panel_settings'],
                    ];
                    @endphp

                    @foreach($fields as $field)
                    <div class="rounded-xl p-3 flex items-start gap-3"
                         style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent);border:1px solid color-mix(in srgb,var(--primary) 5%,transparent)">
                        <x-icon name="{{ $field['icon'] }}" class="text-[18px] mt-0.5"  style="color:{{ $field['color'] ?? 'var(--primary)' }}" />
                        <div>
                            <p class="text-xs font-semibold" style="color:var(--on-surface-var)">{{ $field['label'] }}</p>
                            <p class="mt-0.5 {{ ($field['bold'] ?? false) ? 'text-base font-bold' : 'font-medium' }}" style="color:var(--on-surface)">{{ $field['value'] }}</p>
                        </div>
                    </div>
                    @endforeach

                    {{-- Status badge --}}
                    <div class="rounded-xl p-3 flex items-start gap-3"
                         style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent);border:1px solid color-mix(in srgb,var(--primary) 5%,transparent)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px] mt-0.5" style="color:var(--primary)"><path d="m344-60-76-128-144-32 14-148-98-112 98-112-14-148 144-32 76-128 136 58 136-58 76 128 144 32-14 148 98 112-98 112 14 148-144 32-76 128-136-58-136 58Zm34-102 102-44 104 44 56-96 110-26-10-112 74-84-74-86 10-112-110-24-58-96-102 44-104-44-56 96-110 24 10 112-74 86 74 84-10 114 110 24 58 96Zm102-318Zm-42 142 226-226-56-58-170 170-86-84-56 56 142 142Z"/></svg>
                        <div>
                            <p class="text-xs font-semibold" style="color:var(--on-surface-var)">Status</p>
                            <div class="mt-1">
                                @php $statusKey = strtolower($salary->status ?? 'unpaid'); @endphp
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
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                @if($salary->note)
                <div class="mt-4 rounded-xl p-4"
                     style="background-color:color-mix(in srgb,var(--primary) 4%,transparent);border:1px solid color-mix(in srgb,var(--primary) 10%,transparent)">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M120-240v-80h480v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
                        <span class="text-xs font-semibold" style="color:var(--on-surface-var)">Note</span>
                    </div>
                    <p class="text-sm whitespace-pre-wrap" style="color:var(--on-surface)">{{ $salary->note }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Mark Payment Panel --}}
        <div class="glass-panel-elevated rounded-2xl overflow-hidden self-start">
            <div class="px-5 py-4 border-b"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--success) 6%,transparent 94%),color-mix(in srgb,var(--primary) 3%,transparent 97%))">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--success)"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                    <h3 class="text-sm font-bold" style="color:var(--on-surface)">Mark Payment</h3>
                </div>
            </div>
            <div class="px-5 py-4">
                @if($salary->status !== 'paid')
                    @php $remaining = max((float) $salary->net_amount - (float) $salary->paid_amount, 0); @endphp
                    <div class="rounded-xl p-3 mb-4 text-center"
                         style="background-color:color-mix(in srgb,var(--primary) 6%,transparent);border:1px solid color-mix(in srgb,var(--primary) 12%,transparent)">
                        <p class="text-xs font-semibold" style="color:var(--on-surface-var)">Remaining Amount</p>
                        <p class="text-xl font-bold mt-1" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format($remaining) }}</p>
                    </div>

                    <form method="POST" action="{{ route('salaries.mark-paid', $salary) }}" class="grid gap-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold" style="color:var(--primary)">EGP</span>
                                <input name="paid_amount" type="number" min="0.01" step="0.01" required
                                       class="w-full glass-input rounded-xl pl-11 pr-4 py-2.5 text-sm">
                            </div>
                            @error('paid_amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Payment Date</label>
                            <input name="payment_date" type="date" value="{{ now()->format('Y-m-d') }}" required
                                   class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                            @error('payment_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="w-full rounded-xl py-2.5 text-sm font-semibold transition-all flex items-center justify-center gap-2"
                                style="background:linear-gradient(135deg,var(--success),color-mix(in srgb,var(--success) 80%,#000 20%));color:#fff;box-shadow:0 4px 14px color-mix(in srgb,var(--success) 25%,transparent 75%)"
                                onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--success) 35%,transparent 65%)'"
                                onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--success) 25%,transparent 75%)'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M444-200h70v-50q50-9 86-39t36-89q0-42-24-77t-96-61q-60-20-83-35t-23-41q0-26 18.5-41t53.5-15q32 0 50 15.5t26 38.5l64-26q-11-35-40.5-61T516-710v-50h-70v50q-50 11-78 44t-28 74q0 47 27.5 76t86.5 50q63 23 87.5 41t24.5 47q0 33-23.5 48.5T486-314q-33 0-58.5-20.5T390-396l-66 26q14 48 43.5 77.5T444-252v52Zm36 120q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>Apply Payment
                        </button>
                    </form>
                @else
                    <div class="text-center py-6">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--success)"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                        <p class="text-sm font-semibold" style="color:var(--success)">Fully Paid</p>
                        <p class="text-xs mt-1" style="color:var(--on-surface-var)">This salary record has been settled</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.app>