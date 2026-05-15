<x-layouts.app :title="__('ui.financial.title')">

@php $isArabic = app()->getLocale() === 'ar'; @endphp

{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="page-title">{{ __('ui.financial.title') }}</h2>
        <p class="page-subtitle" style="color:var(--on-surface-var)">{{ __('ui.financial.subtitle') }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Export PDF --}}
        <a href="{{ route('financial.export.pdf', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
           style="background:color-mix(in srgb,var(--error) 12%,transparent 88%);color:var(--error);border:1px solid color-mix(in srgb,var(--error) 25%,transparent)">
            <x-icon name="picture_as_pdf" class="text-base" />
            {{ __('ui.financial.actions.export_pdf') }}
        </a>
        {{-- Export Excel --}}
        <a href="{{ route('financial.export.excel', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
           style="background:color-mix(in srgb,var(--tertiary) 12%,transparent 88%);color:var(--tertiary);border:1px solid color-mix(in srgb,var(--tertiary) 25%,transparent)">
            <x-icon name="table_view" class="text-base" />
            {{ __('ui.financial.actions.export_excel') }}
        </a>
    </div>
</div>

{{-- ── KPI Cards ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    @foreach($kpis as $kpi)
    <div class="kpi-card">
        <div class="absolute ltr:-right-6 rtl:-left-6 -top-6 w-28 h-28 rounded-full blur-3xl opacity-20 transition-all duration-500"
             style="background-color:var(--{{ $kpi['color'] ?? 'primary' }}-container)"></div>
        <div class="flex justify-between items-start mb-2 relative z-10">
            <div class="p-2.5 rounded-xl flex items-center justify-center"
                 style="background-color:color-mix(in srgb, var(--{{ $kpi['color'] ?? 'primary' }}) 15%, transparent 85%); color:var(--{{ $kpi['color'] ?? 'primary' }})">
                <x-icon name="{{ $kpi['icon'] ?? 'bar_chart' }}" class="text-[22px]" />
            </div>
            @if(isset($kpi['profit_sign']))
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="color:var(--{{ $kpi['profit_sign'] === 'positive' ? 'tertiary' : 'error' }});
                             background-color:color-mix(in srgb,var(--{{ $kpi['profit_sign'] === 'positive' ? 'tertiary' : 'error' }}) 15%,transparent 85%)">
                    {{ $kpi['profit_sign'] === 'positive' ? '▲' : '▼' }}
                </span>
            @endif
        </div>
        <div class="relative z-10">
            <p @class(['text-[13px] font-semibold mb-1','uppercase tracking-widest' => !$isArabic])
               style="color:var(--on-surface-var)">{{ $kpi['label'] }}</p>
            <p class="text-3xl font-extrabold tracking-tight" style="color:var(--on-surface)">{{ $kpi['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filters Bar ──────────────────────────────────────────────────── --}}
<div class="chart-card p-5 mb-6">
    <form method="GET" action="{{ route('financial.index') }}" id="fin-filter-form">
        <input type="hidden" name="period" id="fin-period" value="{{ $period }}">
        <div class="flex flex-wrap items-end gap-3">

            {{-- Period Tabs --}}
            <div class="flex items-center gap-1 p-1 rounded-xl" style="background:color-mix(in srgb,var(--surface-container) 60%,transparent)">
                @foreach(['today','week','month'] as $p)
                <button type="button"
                        onclick="document.getElementById('fin-from').value='';document.getElementById('fin-to').value='';document.getElementById('fin-period').value='{{ $p }}';document.getElementById('fin-filter-form').submit();"
                        @class(['fin-period-btn', 'active' => $period === $p])>
                    {{ __('ui.financial.filters.'.$p) }}
                </button>
                @endforeach
            </div>

            {{-- Month Picker (only visible when period=month) --}}
            <div class="flex flex-col gap-0.5" id="fin-month-wrap" style="{{ $period !== 'month' ? 'display:none' : '' }}">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)">{{ __('ui.financial.filters.month') }}</label>
                <input type="month" name="month_year" id="fin-month-year"
                       value="{{ $monthYear ?? now()->format('Y-m') }}"
                       class="fin-input"
                       onchange="document.getElementById('fin-period').value='month';document.getElementById('fin-from').value='';document.getElementById('fin-to').value='';document.getElementById('fin-filter-form').submit()">
            </div>
            <script>
            (function(){
                var wrap = document.getElementById('fin-month-wrap');
                document.querySelectorAll('.fin-period-btn').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var p = btn.getAttribute('onclick')||'';
                        var isMonth = p.indexOf("'month'") !== -1;
                        if(wrap) wrap.style.display = isMonth ? '' : 'none';
                    });
                });
            })();
            </script>

            {{-- Hidden from/to (cleared by month picker JS) --}}
            <input type="hidden" name="from" id="fin-from" value="">
            <input type="hidden" name="to" id="fin-to" value="">

            {{-- Payment Method --}}
            <div class="flex flex-col gap-0.5">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)">{{ __('ui.financial.filters.payment_method') }}</label>
                <select name="payment_method" class="fin-input" onchange="this.form.submit()">
                    <option value="">{{ __('ui.financial.filters.all_methods') }}</option>
                    @foreach(['cash','card','credit','bank_transfer','wallet','visa','instapay'] as $m)
                    <option value="{{ $m }}" @selected($paymentMethodFilter === $m)>{{ __('ui.financial.filters.'.$m) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Transaction Type --}}
            <div class="flex flex-col gap-0.5">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)">{{ __('ui.financial.filters.type') }}</label>
                <select name="type" class="fin-input" onchange="this.form.submit()">
                    <option value="">{{ __('ui.financial.filters.all_types') }}</option>
                    @foreach(['order','purchase','salary'] as $t)
                    <option value="{{ $t }}" @selected($typeFilter === $t)>{{ __('ui.financial.filters.'.$t) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset --}}
            <a href="{{ route('financial.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all self-end"
               style="color:var(--on-surface-var);background:color-mix(in srgb,var(--surface-container) 50%,transparent)">
                <x-icon name="refresh" class="text-base" />
                {{ __('ui.financial.filters.reset') }}
            </a>
        </div>
    </form>
</div>

{{-- ── Summary Strip ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @php
        $formatter = \App\Support\CurrencyFormatter::class;
    @endphp
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,var(--tertiary) 10%,var(--surface-low) 90%);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">
            <x-icon name="arrow_upward" class="text-xl" />
        </div>
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)">{{ __('ui.financial.summary.total_income') }}</p>
            <p class="text-xl font-extrabold" style="color:var(--tertiary)" dir="ltr">{{ $formatter::format($totalRevenue) }}</p>
        </div>
    </div>
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,var(--error) 10%,var(--surface-low) 90%);border:1px solid color-mix(in srgb,var(--error) 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
            <x-icon name="arrow_downward" class="text-xl" />
        </div>
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)">{{ __('ui.financial.summary.total_expenses') }}</p>
            <p class="text-xl font-extrabold" style="color:var(--error)" dir="ltr">{{ $formatter::format($totalExpenses) }}</p>
        </div>
    </div>
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,{{ $netProfit >= 0 ? 'var(--primary)' : 'var(--error)' }} 10%,var(--surface-low) 90%);
                border:1px solid color-mix(in srgb,{{ $netProfit >= 0 ? 'var(--primary)' : 'var(--error)' }} 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,{{ $netProfit >= 0 ? 'var(--primary)' : 'var(--error)' }} 20%,transparent);
                    color:{{ $netProfit >= 0 ? 'var(--primary)' : 'var(--error)' }}">
            <x-icon name="{{ $netProfit >= 0 ? 'trending_up' : 'trending_down' }}" class="text-xl" />
        </div>
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)">{{ __('ui.financial.summary.net') }}</p>
            <p class="text-xl font-extrabold"
               style="color:{{ $netProfit >= 0 ? 'var(--primary)' : 'var(--error)' }}" dir="ltr">
                {{ $formatter::format($netProfit) }}
            </p>
        </div>
    </div>
</div>

{{-- ── Main Transactions Table ──────────────────────────────────────── --}}
<div class="chart-card overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4"
         style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent 70%)">
        <div>
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">
                {{ $isArabic ? 'سجل المعاملات المالية' : 'Financial Transactions' }}
            </h3>
            <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">
                {{ $transactions->count() }} {{ $isArabic ? 'معاملة' : 'transactions' }}
                &nbsp;·&nbsp; {{ $from }} → {{ $to }}
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="color:var(--on-surface-var)">
            <thead style="background:color-mix(in srgb,var(--surface-container) 40%,transparent);
                          border-bottom:1px solid color-mix(in srgb,var(--outline-var) 20%,transparent)">
                <tr>
                    @foreach(['type','reference','description','payment_method','amount','remaining','actor','status','date'] as $col)
                    <th class="px-4 py-3 text-start text-xs font-semibold @if(!$isArabic) uppercase tracking-wide @endif"
                        style="color:var(--on-surface)">
                        {{ __('ui.financial.table.'.$col) }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $row)
                <tr class="transition-colors"
                    style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 10%,transparent)"
                    onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--surface-container) 30%,transparent)'"
                    onmouseleave="this.style.backgroundColor='transparent'">

                    {{-- Type badge --}}
                    <td class="px-4 py-3">
                        <span class="fin-type-badge fin-type-{{ $row['type'] }}">
                            {{ $row['type_label'] }}
                        </span>
                    </td>

                    {{-- Reference --}}
                    <td class="px-4 py-3 font-semibold tabular-nums" style="color:var(--on-surface)" dir="ltr">
                        {{ $row['reference'] }}
                    </td>

                    {{-- Description --}}
                    <td class="px-4 py-3 max-w-[180px] truncate">{{ $row['description'] }}</td>

                    {{-- Payment Method badge --}}
                    <td class="px-4 py-3">
                        <span class="fin-pay-badge fin-pay-{{ $row['payment_method'] }}">
                            {{ $row['payment_method_label'] }}
                        </span>
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3 font-bold tabular-nums"
                        style="color:{{ $row['category'] === 'income' ? 'var(--tertiary)' : 'var(--error)' }}" dir="ltr">
                        {{ $row['category'] === 'income' ? '+' : '−' }}{{ $row['amount'] }}
                    </td>

                    {{-- Remaining --}}
                    <td class="px-4 py-3 tabular-nums" style="color:var(--error)">
                        {{ $row['remaining'] ?? '—' }}
                    </td>

                    {{-- Actor --}}
                    <td class="px-4 py-3 text-xs">{{ $row['actor'] }}</td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        <span @class([
                            'badge-ok'      => $row['status'] === 'paid',
                            'badge-warn'    => $row['status'] === 'partial',
                            'badge-neutral' => !in_array($row['status'],['paid','partial']),
                        ])>{{ $row['status_label'] }}</span>
                    </td>

                    {{-- Date --}}
                    <td class="px-4 py-3 text-xs tabular-nums" dir="ltr">{{ $row['date'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center" style="color:var(--on-surface-var)">
                        <div class="flex flex-col items-center gap-3">
                            <x-icon name="receipt_long" class="text-5xl opacity-30" />
                            <p class="text-sm">{{ __('ui.financial.table.no_results') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Page-specific styles ─────────────────────────────────────────── --}}
<style>
/* Period buttons */
.fin-period-btn {
    padding: 6px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--on-surface-var);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all .18s;
}
.fin-period-btn.active,
.fin-period-btn:hover {
    background: var(--primary);
    color: var(--on-primary);
}

/* Inputs */
.fin-input {
    padding: 7px 10px;
    border-radius: 10px;
    font-size: 13px;
    background: color-mix(in srgb, var(--surface-container) 60%, transparent);
    color: var(--on-surface);
    border: 1px solid color-mix(in srgb, var(--outline-var) 40%, transparent);
    outline: none;
    transition: border-color .15s;
}
.fin-input:focus { border-color: var(--primary); }

/* Type badges */
.fin-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.fin-type-order    { background: color-mix(in srgb,var(--success) 15%,transparent); color: var(--success); border: 1px solid color-mix(in srgb,var(--success) 35%,transparent); }
.fin-type-purchase { background: color-mix(in srgb,var(--error) 15%,transparent); color: var(--error); border: 1px solid color-mix(in srgb,var(--error) 35%,transparent); }
.fin-type-salary   { background: color-mix(in srgb,var(--warning) 15%,transparent); color: var(--warning); border: 1px solid color-mix(in srgb,var(--warning) 35%,transparent); }

/* Payment method badges */
.fin-pay-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.fin-pay-cash          { background: color-mix(in srgb,var(--success) 12%,transparent); color: var(--success); border: 1px solid color-mix(in srgb,var(--success) 35%,transparent); }
.fin-pay-card          { background: color-mix(in srgb,var(--primary) 12%,transparent); color: var(--primary); border: 1px solid color-mix(in srgb,var(--primary) 35%,transparent); }
.fin-pay-credit        { background: color-mix(in srgb,var(--warning) 12%,transparent); color: var(--warning); border: 1px solid color-mix(in srgb,var(--warning) 35%,transparent); }
.fin-pay-bank_transfer { background: color-mix(in srgb,var(--secondary) 20%,transparent); color: var(--on-surface-var); border: 1px solid color-mix(in srgb,var(--secondary) 50%,transparent); }
.fin-pay-wallet        { background: color-mix(in srgb,var(--accent-gold) 15%,transparent); color: var(--on-surface-var); border: 1px solid color-mix(in srgb,var(--accent-gold) 40%,transparent); }
.fin-pay-visa          { background: color-mix(in srgb,var(--primary) 12%,transparent); color: var(--primary); border: 1px solid color-mix(in srgb,var(--primary) 35%,transparent); }
.fin-pay-instapay      { background: color-mix(in srgb,var(--error) 12%,transparent); color: var(--error); border: 1px solid color-mix(in srgb,var(--error) 35%,transparent); }

@media print {
    .page-header, nav, aside, .fin-period-btn, form { display: none !important; }
}
</style>

</x-layouts.app>
