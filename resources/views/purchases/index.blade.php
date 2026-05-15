<x-layouts.app :title="__('ui.purchases.title')">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.purchases.title') }}</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">{{ __('ui.purchases.index_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('suppliers.index') }}" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="{{ __('ui.purchases.buttons.suppliers') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M155-195q-35-35-35-85H40v-440q0-33 23.5-56.5T120-800h560v160h120l120 160v200h-80q0 50-35 85t-85 35q-50 0-85-35t-35-85H360q0 50-35 85t-85 35q-50 0-85-35Zm113.5-56.5Q280-263 280-280t-11.5-28.5Q257-320 240-320t-28.5 11.5Q200-297 200-280t11.5 28.5Q223-240 240-240t28.5-11.5ZM120-360h32q17-18 39-29t49-11q27 0 49 11t39 29h272v-360H120v360Zm628.5 108.5Q760-263 760-280t-11.5-28.5Q737-320 720-320t-28.5 11.5Q680-297 680-280t11.5 28.5Q703-240 720-240t28.5-11.5ZM680-440h170l-90-120h-80v120ZM360-540Z"/></svg><span class="hidden sm:inline">{{ __('ui.purchases.buttons.suppliers') }}</span>
            </a>
            <a href="{{ route('purchases.pdf', request()->query()) }}" target="_blank" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Export PDF">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]"><path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg><span class="hidden sm:inline">Export PDF</span>
            </a>
            <a href="{{ route('purchases.create') }}" class="glass-button-primary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>{{ __('ui.purchases.buttons.create_purchase') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('purchases.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-[160px]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input name="q" value="{{ $filters['q'] }}" placeholder="{{ __('ui.purchases.purchase_number') }}"
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <div class="relative">
            <select name="request_type" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-[144px]">
                <option value="">{{ __('ui.purchases.all_request_types') }}</option>
                <option value="inventory" @selected((string)$filters['request_type']==='inventory')>{{ __('ui.purchases.request_types.inventory') }}</option>
                <option value="general_expense" @selected((string)$filters['request_type']==='general_expense')>{{ __('ui.purchases.request_types.general_expense') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <div class="relative">
            <select name="approval_status" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-[144px]">
                <option value="">{{ __('ui.purchases.all_approval_statuses') }}</option>
                <option value="pending" @selected((string)$filters['approval_status']==='pending')>{{ __('ui.purchases.statuses.pending') }}</option>
                <option value="approved" @selected((string)$filters['approval_status']==='approved')>{{ __('ui.purchases.statuses.approved') }}</option>
                <option value="rejected" @selected((string)$filters['approval_status']==='rejected')>{{ __('ui.purchases.statuses.rejected') }}</option>
                <option value="completed" @selected((string)$filters['approval_status']==='completed')>{{ __('ui.purchases.statuses.completed') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <div class="relative">
            <select name="supplier_id" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-[160px]">
                <option value="">{{ __('ui.purchases.all_suppliers') }}</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((string)$filters['supplier_id']===(string)$supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <div class="relative">
            <input type="date" name="from" value="{{ $filters['from'] }}"
                   class="glass-input rounded-xl px-4 py-2 text-sm min-w-[144px]">
        </div>
        <div class="relative">
            <input type="date" name="to" value="{{ $filters['to'] }}"
                   class="glass-input rounded-xl px-4 py-2 text-sm min-w-[144px]">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/></svg>{{ __('ui.purchases.buttons.filter') }}
            </button>
            <a href="{{ route('purchases.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">{{ __('ui.purchases.buttons.reset') }}</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between"
             style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.purchases.total_count', ['count' => $purchases->total()]) }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach([__('ui.purchases.table.purchase_no'), __('ui.purchases.table.request_type'), __('ui.purchases.table.supplier'), __('ui.purchases.table.date'), __('ui.purchases.table.approval_status'), __('ui.purchases.payment'), __('ui.purchases.table.items'), __('ui.purchases.table.total'), __('ui.purchases.table.by'), __('ui.purchases.table.action')] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider whitespace-nowrap" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($purchases as $purchase)
                    @php
                    $approvalStatus = strtolower((string) ($purchase->approval_status ?: 'pending'));
                    $purchaseStatus = strtolower((string) ($purchase->status ?: 'pending'));
                    $isCompleted = $purchaseStatus === 'completed';

                    $displayStatus = match (true) {
                        $isCompleted => 'completed',
                        $approvalStatus === 'approved' => 'approved',
                        $approvalStatus === 'rejected' => 'rejected',
                        default => 'pending',
                    };

                    $displayLabel = match ($displayStatus) {
                        'completed' => __('ui.purchases.statuses.completed'),
                        'approved' => __('ui.purchases.statuses.approved'),
                        'rejected' => __('ui.purchases.statuses.rejected'),
                        default => __('ui.purchases.statuses.pending'),
                    };

                    $secondaryLabel = $displayStatus === 'approved'
                        ? __('ui.purchases.completion.waiting_completion')
                        : null;
                    @endphp
                    <tr class="transition-colors" style="border-bottom:1px solid color-mix(in srgb,var(--primary) 5%,transparent)"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-mono text-xs font-semibold" style="color:var(--primary)">{{ $purchase->purchase_number }}</td>

                        <td class="px-5 py-3">
                            @if(($purchase->request_type ?? 'inventory') === 'general_expense')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--surface-low);border:1px solid color-mix(in srgb,var(--outline) 35%,transparent 65%);color:var(--on-surface-var)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[12px]" ><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>{{ __('ui.purchases.request_types.general_expense') }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--primary) 15%,var(--surface-lowest) 85%);border:1px solid color-mix(in srgb,var(--primary) 40%,transparent 60%);color:var(--primary)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[12px]" ><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>{{ __('ui.purchases.request_types.inventory') }}
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            @if(($purchase->request_type ?? 'inventory') === 'general_expense')
                            {{ $purchase->expense_title ?: '-' }}
                            @else
                            {{ $purchase->supplier?->name ?? '-' }}
                            @endif
                        </td>

                        <td class="px-5 py-3 whitespace-nowrap text-xs" style="color:var(--on-surface-var)">{{ $purchase->purchase_date?->format('Y-m-d') }}</td>

                        <td class="px-5 py-3">
                            <div class="inline-flex flex-col gap-1">
                                @if($displayStatus === 'completed')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--success)"></span>{{ $displayLabel }}
                                </span>
                                @elseif($displayStatus === 'approved')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color:color-mix(in srgb,var(--primary) 15%,var(--surface-lowest) 85%);border:1px solid color-mix(in srgb,var(--primary) 40%,transparent 60%);color:var(--primary)">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--primary)"></span>{{ $displayLabel }}
                                </span>
                                @elseif($displayStatus === 'rejected')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color:var(--error-container);border:1px solid color-mix(in srgb,var(--error) 35%,transparent 65%);color:var(--error)">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--error)"></span>{{ $displayLabel }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color:var(--surface-low);border:1px solid color-mix(in srgb,var(--outline) 35%,transparent 65%);color:var(--on-surface-var)">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--on-surface-var)"></span>{{ $displayLabel }}
                                </span>
                                @endif

                                @if($secondaryLabel)
                                <span class="text-[11px] font-medium" style="color:var(--primary)">{{ $secondaryLabel }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-3">
                            @if(($purchase->payment_method ?? 'cash') === 'credit')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--error-container);border:1px solid color-mix(in srgb,var(--error) 35%,transparent 65%);color:var(--error)">
                                {{ __('ui.purchases.payment_credit') }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)">
                                {{ __('ui.purchases.payment_cash') }}
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-center" style="color:var(--on-surface-var)">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg text-xs font-semibold"
                                  style="background-color:color-mix(in srgb,var(--primary) 15%,var(--surface-lowest) 85%);color:var(--primary)">{{ $purchase->items_count }}</span>
                        </td>

                        <td class="px-5 py-3 font-mono text-xs font-semibold whitespace-nowrap" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($purchase->total) }}</td>

                        <td class="px-5 py-3 text-xs" style="color:var(--on-surface-var)">{{ $purchase->user?->name ?? __('ui.purchases.system') }}</td>

                        <td class="px-5 py-3">
                            <a href="{{ route('purchases.show', $purchase) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM214-281.5Q94-363 40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200q-146 0-266-81.5ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/></svg>{{ __('ui.purchases.buttons.view') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-14 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--outline)"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
                            <p class="text-sm font-medium" style="color:var(--on-surface-var)">{{ __('ui.purchases.no_results') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $purchases->withQueryString()->links() }}</div>
        @endif
    </div>

</x-layouts.app>