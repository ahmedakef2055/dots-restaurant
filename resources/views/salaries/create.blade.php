<x-layouts.app title="Create Salary Record">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>Back to Salaries
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="glass-panel-elevated rounded-2xl overflow-hidden">
            {{-- Card header --}}
            <div class="px-6 py-5 border-b"
                 style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 4%,transparent),color-mix(in srgb,var(--tertiary) 3%,transparent))">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl"
                          style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold" style="color:var(--on-surface)">Create Salary Record</h2>
                        <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Generate payroll for a selected employee and period</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('salaries.store') }}" class="px-6 py-5">
                @csrf

                {{-- Section: Employee & Period --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>
                        <h3 class="text-sm font-semibold" style="color:var(--on-surface)">Employee & Period</h3>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        {{-- Employee --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Employee <span style="color:var(--error)">*</span></label>
                            <div class="relative">
                                <select name="employee_id" required class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected((string) old('employee_id', $prefillEmployeeId)===(string) $employee->id)>{{ $employee->employee_code }} — {{ $employee->full_name }} (Base: {{ \App\Support\CurrencyFormatter::format($employee->base_salary) }})</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('employee_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Status <span style="color:var(--error)">*</span></label>
                            <div class="relative">
                                <select name="status" required class="w-full glass-input rounded-xl px-4 py-2.5 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="unpaid" @selected(old('status', 'unpaid')==='unpaid')>Unpaid</option>
                                    <option value="partial" @selected(old('status')==='partial')>Partial</option>
                                    <option value="paid" @selected(old('status')==='paid')>Paid</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('status')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Period Start --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Period Start <span style="color:var(--error)">*</span></label>
                            <input name="period_start" type="date" value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" required
                                   class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                            @error('period_start')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Period End --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Period End <span style="color:var(--error)">*</span></label>
                            <input name="period_end" type="date" value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}" required
                                   class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                            @error('period_end')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t mb-6" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

                {{-- Section: Financials --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M200-280v-280h80v280h-80Zm240 0v-280h80v280h-80ZM80-120v-80h800v80H80Zm600-160v-280h80v280h-80ZM80-640v-80l400-200 400 200v80H80Zm178-80h444-444Zm0 0h444L480-830 258-720Z"/></svg>
                        <h3 class="text-sm font-semibold" style="color:var(--on-surface)">Financial Details</h3>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        {{-- Base Salary --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Base Salary (optional override)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold" style="color:var(--on-surface-var)">EGP</span>
                                <input name="base_salary" type="number" min="0" step="0.01" value="{{ old('base_salary') }}"
                                       class="w-full glass-input rounded-xl pl-11 pr-4 py-2.5 text-sm" placeholder="From employee profile">
                            </div>
                            <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">Leave empty to use employee's configured base salary</p>
                            @error('base_salary')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Bonus --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Bonus Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold" style="color:var(--success)">+</span>
                                <input name="bonus_amount" type="number" min="0" step="0.01" value="{{ old('bonus_amount', 0) }}"
                                       class="w-full glass-input rounded-xl pl-8 pr-4 py-2.5 text-sm">
                            </div>
                            @error('bonus_amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Deduction --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Other Deduction (manual)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold" style="color:var(--error)">−</span>
                                <input name="other_deduction" type="number" min="0" step="0.01" value="{{ old('other_deduction', 0) }}"
                                       class="w-full glass-input rounded-xl pl-8 pr-4 py-2.5 text-sm">
                            </div>
                            <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">Employee deductions &amp; product charges in this period are auto-calculated</p>
                            @error('other_deduction')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Paid Amount --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Initial Paid Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold" style="color:var(--primary)">EGP</span>
                                <input name="paid_amount" type="number" min="0" step="0.01" value="{{ old('paid_amount', 0) }}"
                                       class="w-full glass-input rounded-xl pl-11 pr-4 py-2.5 text-sm">
                            </div>
                            @error('paid_amount')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>

                        {{-- Payment Date --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">Payment Date</label>
                            <input name="payment_date" type="date" value="{{ old('payment_date') }}"
                                   class="w-full glass-input rounded-xl px-4 py-2.5 text-sm">
                            @error('payment_date')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t mb-6" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

                {{-- Note --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M120-240v-80h480v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
                        <h3 class="text-sm font-semibold" style="color:var(--on-surface)">Additional Notes</h3>
                    </div>
                    <textarea name="note" rows="3" class="w-full glass-input rounded-xl px-4 py-2.5 text-sm" placeholder="Optional notes about this salary payment...">{{ old('note') }}</textarea>
                    @error('note')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                </div>

                {{-- Info Box --}}
                <div class="rounded-xl p-4 mb-6 flex items-start gap-3"
                     style="background-color:color-mix(in srgb,var(--primary) 6%,transparent);border:1px solid color-mix(in srgb,var(--primary) 15%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px] mt-0.5" style="color:var(--primary)"><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                    <div class="text-xs leading-relaxed" style="color:var(--on-surface-var)">
                        <p class="font-semibold mb-1" style="color:var(--on-surface)">How salary calculation works</p>
                        <p>The system will automatically calculate <strong>attendance deductions</strong> (absent days, half days) and <strong>employee salary adjustments</strong> (product charges, fines) for the selected period. The final status is auto-adjusted based on the paid amount vs net amount.</p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-xl py-2.5 px-6 text-sm font-semibold transition-all flex items-center gap-2"
                            style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));color:var(--on-primary);box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)"
                            onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--primary) 40%,transparent)'"
                            onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>Create Salary Record
                    </button>
                    <a href="{{ route('salaries.index') }}" class="glass-button-secondary rounded-xl py-2.5 px-5 text-sm font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>