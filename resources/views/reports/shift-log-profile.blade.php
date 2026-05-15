<x-layouts.app :title="__('ui.reports.shift_logs.profile.title')">
    <style>
        @media print {
            #app-sidebar,
            .glass-nav,
            .print-hidden {
                display: none !important;
            }

            .app-content {
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-card {
                border-color: var(--outline-var) !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
            }
        }
    </style>

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 print-hidden">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.reports.shift_logs.profile.title') }}</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">
                {{ __('ui.reports.shift_logs.profile.subtitle') }}
                <span class="font-mono font-semibold" style="color:var(--primary)">#{{ $shiftLog->id }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.shift-logs') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
                <span class="hidden sm:inline">{{ __('ui.reports.shift_logs.profile.back') }}</span>
            </a>
            <a href="{{ route('reports.shift-logs.receipt', $shiftLog) }}" target="_blank" rel="noopener"
               class="rounded-xl py-2 px-4 text-sm font-semibold inline-flex items-center gap-2 transition-all"
               style="background:linear-gradient(135deg,var(--primary),var(--accent-gold));color:var(--on-primary);box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)"
               onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px color-mix(in srgb,var(--primary) 40%,transparent)'"
               onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>{{ __('ui.reports.shift_logs.profile.print') }}
            </a>
        </div>
    </div>

    {{-- Shift Identity Card --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden mb-6 print-card">
        <div class="px-5 py-4 border-b"
             style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 4%,transparent),color-mix(in srgb,var(--tertiary) 3%,transparent))">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl shrink-0"
                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[22px]" ><path d="M160-80q-33 0-56.5-23.5T80-160v-440q0-33 23.5-56.5T160-680h200v-120q0-33 23.5-56.5T440-880h80q33 0 56.5 23.5T600-800v120h200q33 0 56.5 23.5T880-600v440q0 33-23.5 56.5T800-80H160Zm0-80h640v-440H600q0 33-23.5 56.5T520-520h-80q-33 0-56.5-23.5T360-600H160v440Zm80-80h240v-18q0-17-9.5-31.5T444-312q-20-9-40.5-13.5T360-330q-23 0-43.5 4.5T276-312q-17 8-26.5 22.5T240-258v18Zm320-60h160v-60H560v60Zm-157.5-77.5Q420-395 420-420t-17.5-42.5Q385-480 360-480t-42.5 17.5Q300-445 300-420t17.5 42.5Q335-360 360-360t42.5-17.5ZM560-420h160v-60H560v60ZM440-600h80v-200h-80v200Zm40 220Z"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 class="text-base sm:text-lg font-bold truncate" style="color:var(--on-surface)">{{ __('ui.reports.shift_logs.profile.identity.title') }}</h2>
                    <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">Cashier shift session details</p>
                </div>
            </div>
        </div>
        <div class="px-5 py-4">
            <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                @php
                $identityFields = [
                    ['icon' => 'person', 'label' => __('ui.reports.shift_logs.profile.identity.cashier'), 'value' => $shiftLog->user?->name ?? '-'],
                    ['icon' => 'play_circle', 'label' => __('ui.reports.shift_logs.profile.identity.shift_start'), 'value' => $shiftLog->shift_start?->format('Y-m-d H:i') ?? '-'],
                    ['icon' => 'stop_circle', 'label' => __('ui.reports.shift_logs.profile.identity.shift_end'), 'value' => $shiftLog->shift_end?->format('Y-m-d H:i') ?? '-'],
                    ['icon' => 'fingerprint', 'label' => __('ui.reports.shift_logs.profile.identity.cashier_shift_id'), 'value' => $cashierShift?->id ?? '-'],
                ];
                @endphp
                @foreach($identityFields as $field)
                <div class="rounded-xl p-3"
                     style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent);border:1px solid color-mix(in srgb,var(--primary) 5%,transparent)">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <x-icon name="{{ $field['icon'] }}" class="text-[14px]"  style="color:var(--primary)" />
                        <p class="text-[11px] font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $field['label'] }}</p>
                    </div>
                    <p class="text-sm font-semibold truncate" style="color:var(--on-surface)">{{ $field['value'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Financial KPI Cards --}}
    <div class="mb-6 grid gap-3 grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        @php
        $kpiCards = [
            ['icon' => 'account_balance_wallet', 'label' => __('ui.reports.shift_logs.profile.breakdown.opening_cash'), 'value' => $financials['opening_cash'], 'color' => 'var(--primary)'],
            ['icon' => 'point_of_sale', 'label' => __('ui.reports.shift_logs.profile.breakdown.total_paid_sales'), 'value' => $financials['total_paid_sales'], 'color' => 'var(--primary)'],
            ['icon' => 'calculate', 'label' => __('ui.reports.shift_logs.profile.breakdown.expected_cash'), 'value' => $financials['expected_cash'], 'color' => 'var(--primary)'],
            ['icon' => 'payments', 'label' => __('ui.reports.shift_logs.profile.breakdown.actual_cash'), 'value' => $financials['actual_cash'], 'color' => 'var(--primary)', 'nullable' => true],
            ['icon' => 'trending_up', 'label' => __('ui.reports.shift_logs.profile.breakdown.cash_overage'), 'value' => $financials['cash_overage'], 'color' => 'var(--success)'],
            ['icon' => 'trending_down', 'label' => __('ui.reports.shift_logs.profile.breakdown.cash_shortage'), 'value' => $financials['cash_shortage'], 'color' => 'var(--error)'],
            ['icon' => 'volunteer_activism', 'label' => __('ui.reports.shift_logs.profile.breakdown.tips'), 'value' => $financials['tips'], 'color' => 'var(--tertiary)'],
        ];
        @endphp

        @foreach($kpiCards as $kpi)
        <div class="glass-panel rounded-2xl p-4 print-card transition-all duration-200"
             onmouseenter="this.style.transform='translateY(-2px)';this.style.borderColor='color-mix(in srgb,var(--primary) 25%,transparent)'"
             onmouseleave="this.style.transform='';this.style.borderColor=''">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg shrink-0"
                      style="background-color:color-mix(in srgb,{{ $kpi['color'] }} 10%,transparent);color:{{ $kpi['color'] }}">
                    <x-icon name="{{ $kpi['icon'] }}" class="text-[18px]"  />
                </span>
                <p class="text-[11px] sm:text-xs font-semibold leading-tight" style="color:var(--on-surface-var)">{{ $kpi['label'] }}</p>
            </div>
            <p class="text-lg sm:text-xl font-bold font-mono" style="color:{{ $kpi['color'] }}">
                @if(($kpi['nullable'] ?? false) && $kpi['value'] === null)
                    -
                @else
                    {{ \App\Support\CurrencyFormatter::format((float) $kpi['value']) }}
                @endif
            </p>
        </div>
        @endforeach
    </div>

    {{-- Settlement Breakdown --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden mb-6 print-card">
        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Zm-40 404h560v-568H200v568Zm0-568v568-568Z"/></svg>
            <h3 class="text-sm font-bold" style="color:var(--on-surface)">{{ __('ui.reports.shift_logs.profile.settlement_title') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.reports.shift_logs.profile.settlement_label') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.reports.shift_logs.profile.settlement_value') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @foreach ($settlementRows as $row)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $row['label'] }}</td>
                        <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">
                            {{ $row['value'] !== null ? \App\Support\CurrencyFormatter::format((float) $row['value']) : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Orders Section --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden print-card">
        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z"/></svg>
            <h3 class="text-sm font-bold" style="color:var(--on-surface)">{{ __('ui.reports.shift_logs.profile.orders.title') }}</h3>
        </div>

        {{-- Order stat cards --}}
        <div class="px-5 py-4">
            <div class="grid gap-3 grid-cols-2 xl:grid-cols-4 mb-4">
                @php
                $orderKpis = [
                    ['icon' => 'receipt_long', 'label' => __('ui.reports.shift_logs.profile.orders.total_orders'), 'value' => number_format($orderStats['total_orders']), 'color' => 'var(--primary)'],
                    ['icon' => 'check_circle', 'label' => __('ui.reports.shift_logs.profile.orders.paid_orders'), 'value' => number_format($orderStats['paid_orders']), 'color' => 'var(--success)'],
                    ['icon' => 'cancel', 'label' => __('ui.reports.shift_logs.profile.orders.cancelled_orders'), 'value' => number_format($orderStats['cancelled_orders']), 'color' => 'var(--error)'],
                    ['icon' => 'pie_chart', 'label' => __('ui.reports.shift_logs.profile.orders.order_mix'), 'value' => null, 'color' => 'var(--tertiary)'],
                ];
                @endphp

                @foreach($orderKpis as $stat)
                <div class="rounded-xl p-3"
                     style="background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent);border:1px solid color-mix(in srgb,var(--primary) 5%,transparent)">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <x-icon name="{{ $stat['icon'] }}" class="text-[14px]"  style="color:{{ $stat['color'] }}" />
                        <p class="text-[11px] font-semibold" style="color:var(--on-surface-var)">{{ $stat['label'] }}</p>
                    </div>
                    @if($stat['value'] !== null)
                    <p class="text-lg font-bold" style="color:var(--on-surface)">{{ $stat['value'] }}</p>
                    @else
                    <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs font-medium" style="color:var(--on-surface-var)">
                        <span>{{ __('ui.reports.shift_logs.profile.orders.mix.dine_in') }}: <strong style="color:var(--on-surface)">{{ number_format($orderStats['dine_in_orders']) }}</strong></span>
                        <span>{{ __('ui.reports.shift_logs.profile.orders.mix.takeaway') }}: <strong style="color:var(--on-surface)">{{ number_format($orderStats['takeaway_orders']) }}</strong></span>
                        <span>{{ __('ui.reports.shift_logs.profile.orders.mix.delivery') }}: <strong style="color:var(--on-surface)">{{ number_format($orderStats['delivery_orders']) }}</strong></span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Orders table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach([
                            __('ui.reports.shift_logs.profile.orders.table.order_number'),
                            __('ui.reports.shift_logs.profile.orders.table.type'),
                            __('ui.reports.shift_logs.profile.orders.table.status'),
                            __('ui.reports.shift_logs.profile.orders.table.subtotal'),
                            __('ui.reports.shift_logs.profile.orders.table.discount'),
                            __('ui.reports.shift_logs.profile.orders.table.total'),
                            __('ui.reports.shift_logs.profile.orders.table.time'),
                        ] as $h)
                        <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider whitespace-nowrap" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse ($orders as $order)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-mono text-xs font-semibold" style="color:var(--primary)">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 whitespace-nowrap" style="color:var(--on-surface-var)">
                            @php $ot = strtolower($order->order_type ?? ''); @endphp
                            <span class="inline-flex items-center gap-1 text-xs">
                                <x-icon name="{{ $ot === 'dine_in' ? 'restaurant' : ($ot === 'takeaway' ? 'takeout_dining' : 'delivery_dining') }}" class="text-[14px]"  />
                                {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @php $os = strtolower($order->status ?? ''); @endphp
                            @if($os === 'paid')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)]"></span>Paid
                            </span>
                            @elseif($os === 'cancelled')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--error)"></span>Cancelled
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--tertiary)"></span>{{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">{{ \App\Support\CurrencyFormatter::format((float) $order->subtotal) }}</td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format((float) $order->discount_amount) }}</td>
                        <td class="px-5 py-3 font-mono text-xs font-semibold" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format((float) $order->total) }}</td>
                        <td class="px-5 py-3 text-xs whitespace-nowrap" style="color:var(--on-surface-var)">{{ $order->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--outline)"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
                            <p class="text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.reports.shift_logs.profile.orders.empty') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.app>
