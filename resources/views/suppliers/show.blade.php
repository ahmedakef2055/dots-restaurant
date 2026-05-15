<x-layouts.app :title="$supplier->name">

@php
$initials = collect(explode(' ', trim($supplier->name)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
$location = trim(($supplier->city ?: '') . ' ' . ($supplier->country ?: ''));
@endphp

{{-- ── Page Header ────────────────────────────────────────────────── --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1 text-sm mb-2" style="color:var(--on-surface-var)">
            <a href="{{ route('suppliers.index') }}"
               class="transition-colors hover:underline"
               style="color:var(--on-surface-var)"
               onmouseenter="this.style.color='var(--primary)'"
               onmouseleave="this.style.color='var(--on-surface-var)'">Suppliers</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/></svg>
            <span style="color:var(--on-surface)">{{ $supplier->name }}</span>
        </nav>
        <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Supplier Profile</h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('suppliers.edit', $supplier) }}"
           class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>
            Edit Supplier
        </a>
    </div>
</div>

{{-- ── KPI Metric Cards ────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Status --}}
    <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center border shrink-0
                    {{ $supplier->is_active ? 'bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]' : '' }}"
             style="{{ !$supplier->is_active ? 'background-color:color-mix(in srgb,var(--outline-var) 12%,transparent);border-color:color-mix(in srgb,var(--outline-var) 25%,transparent)' : '' }}">
            <x-icon name="{{ $supplier->is_active ? 'check_circle' : 'cancel' }}"
                    class="{{ $supplier->is_active ? 'text-[var(--success)]' : '' }}"
                    style="{{ !$supplier->is_active ? 'color:var(--on-surface-var)' : '' }};font-variation-settings:'FILL' 1" />
        </div>
        <div>
            <p class="text-sm mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.status.title') }}</p>
            <p class="text-xl font-semibold {{ $supplier->is_active ? 'text-[var(--success)]' : '' }}"
               style="{{ !$supplier->is_active ? 'color:var(--on-surface-var)' : '' }}">
                {{ $supplier->is_active ? __('ui.suppliers.status.active') : __('ui.suppliers.status.inactive') }}
            </p>
        </div>
    </div>

    {{-- Total Purchases --}}
    <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center border"
             style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--primary)"><path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z"/></svg>
        </div>
        <div>
            <p class="text-sm mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.cards.purchases') }}</p>
            <p class="text-xl font-semibold" style="color:var(--on-surface)">{{ (int) $supplier->purchases_count }}</p>
        </div>
    </div>

    {{-- Supply Lines --}}
    <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center border"
             style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border-color:color-mix(in srgb,var(--tertiary) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--tertiary)"><path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
        </div>
        <div>
            <p class="text-sm mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.cards.supply_lines') }}</p>
            <p class="text-xl font-semibold" style="color:var(--on-surface)">{{ (int) ($supplyTotals->total_lines ?? 0) }}</p>
        </div>
    </div>

    {{-- Total Value --}}
    <div class="glass-panel rounded-xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center border"
             style="background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border-color:color-mix(in srgb,var(--secondary) 20%,transparent)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--secondary)"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
        </div>
        <div>
            <p class="text-sm mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.cards.total_supplied_value') }}</p>
            <p class="text-xl font-semibold" style="color:var(--on-surface)">
                {{ \App\Support\CurrencyFormatter::format((float) ($supplyTotals->total_value ?? 0)) }}
            </p>
        </div>
    </div>

</div>

{{-- ── Main Content Grid ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Left: Supplier Details --}}
    <div class="lg:col-span-1">
        <div class="glass-panel-elevated rounded-2xl p-6 h-full">
            {{-- Avatar + Name --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center border text-2xl font-bold shrink-0"
                     style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary)">
                    {{ $initials }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold" style="color:var(--on-surface)">{{ $supplier->name }}</h2>
                    <p class="text-sm" style="color:var(--on-surface-var)">
                        {{ $location ?: 'Location not set' }}
                    </p>
                </div>
            </div>

            {{-- Contact Details --}}
            <div class="space-y-4">
                @if($supplier->contact_person)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.contact_person') }}</p>
                    <p class="text-sm flex items-center gap-2" style="color:var(--on-surface)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-sm" style="color:var(--primary)"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>
                        {{ $supplier->contact_person }}
                    </p>
                </div>
                @endif

                @if($supplier->email)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.email') }}</p>
                    <p class="text-sm flex items-center gap-2" style="color:var(--on-surface)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-sm" style="color:var(--primary)"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg>
                        {{ $supplier->email }}
                    </p>
                </div>
                @endif

                @if($supplier->phone)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.mobile_number') }}</p>
                    <p class="text-sm flex items-center gap-2" style="color:var(--on-surface)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-sm" style="color:var(--primary)"><path d="M798-120q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z"/></svg>
                        {{ $supplier->phone }}
                    </p>
                </div>
                @endif

                @if($supplier->address || $location)
                <div class="pt-4 border-t" style="border-color:color-mix(in srgb,var(--outline) 30%,transparent)">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.address') }}</p>
                    <p class="text-sm" style="color:var(--on-surface)">
                        {{ $supplier->address ? $supplier->address . ($location ? ', ' . $location : '') : $location }}
                    </p>
                </div>
                @endif

                @if($supplier->notes)
                <div class="pt-4 border-t" style="border-color:color-mix(in srgb,var(--outline) 30%,transparent)">
                    <p class="text-[10px] font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.notes') }}</p>
                    <p class="text-sm italic" style="color:var(--on-surface-var)">{{ $supplier->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Raw Materials Supplied --}}
            <div class="mt-6 pt-4 border-t" style="border-color:color-mix(in srgb,var(--outline) 30%,transparent)">
                <p class="text-[10px] font-semibold uppercase tracking-wider mb-3" style="color:var(--on-surface-var)">
                    {{ __('ui.suppliers.cards.raw_materials') }}
                </p>
                <div class="space-y-2">
                    @forelse($supplier->ingredients as $ingredient)
                    <div class="flex items-center justify-between rounded-xl px-3 py-2 border"
                         style="border-color:color-mix(in srgb,var(--primary) 12%,transparent);background-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                        <span class="text-sm font-medium" style="color:var(--on-surface)">{{ $ingredient->name }}</span>
                        <span class="text-xs font-mono px-1.5 py-0.5 rounded"
                              style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                            {{ strtoupper((string) $ingredient->unit) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.suppliers.empty.no_linked_materials') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Financials + Actions --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Account Summary --}}
        <div class="glass-panel-elevated rounded-2xl p-6">
            <h3 class="text-base font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v100h-80v-100H200v560h560v-100h80v100q0 33-23.5 56.5T760-120H200Zm320-160q-33 0-56.5-23.5T440-360v-240q0-33 23.5-56.5T520-680h280q33 0 56.5 23.5T880-600v240q0 33-23.5 56.5T800-280H520Zm280-80v-240H520v240h280Zm-117.5-77.5Q700-455 700-480t-17.5-42.5Q665-540 640-540t-42.5 17.5Q580-505 580-480t17.5 42.5Q615-420 640-420t42.5-17.5Z"/></svg>
                {{ __('ui.suppliers.cards.supplier_account') }}
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="rounded-xl p-4 border"
                     style="background-color:color-mix(in srgb,var(--surface-container) 50%,transparent);border-color:color-mix(in srgb,var(--outline) 20%,transparent)">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.total_purchases') }}</p>
                    <p class="text-lg font-semibold" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($supplierAccount['total_purchases'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="background-color:color-mix(in srgb,var(--success) 4%,transparent 96%);border-color:color-mix(in srgb,var(--success) 15%,transparent 85%)">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1 text-[var(--success)]">{{ __('ui.suppliers.labels.paid') }}</p>
                    <p class="text-lg font-semibold text-[var(--success)]">{{ \App\Support\CurrencyFormatter::format($supplierAccount['total_payments'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="background-color:color-mix(in srgb,var(--secondary) 5%,transparent);border-color:color-mix(in srgb,var(--secondary) 20%,transparent)">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--secondary)">{{ __('ui.suppliers.labels.returns') }}</p>
                    <p class="text-lg font-semibold" style="color:var(--secondary)">{{ \App\Support\CurrencyFormatter::format($supplierAccount['total_returns'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl p-4 border"
                     style="background-color:color-mix(in srgb,var(--error) 6%,transparent);border-color:color-mix(in srgb,var(--error) 20%,transparent)">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--error)">{{ __('ui.suppliers.labels.balance_due') }}</p>
                    <p class="text-lg font-semibold" style="color:var(--error)">{{ \App\Support\CurrencyFormatter::format($supplierAccount['balance_due'] ?? 0) }}</p>
                </div>
            </div>

            {{-- Quick Actions: Payment + Return --}}
            <div class="grid md:grid-cols-2 gap-5">

                {{-- Record Payment --}}
                <div class="rounded-xl p-5 border" style="border-color:color-mix(in srgb,var(--outline) 25%,transparent)">
                    <h4 class="text-sm font-semibold mb-4" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.record_payment') }}</h4>
                    <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.date') }}</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                            @error('payment_date')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.amount') }}</label>
                            <input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm font-mono">
                            @error('amount')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.method') }}</label>
                            <div class="relative">
                                <select name="method" class="w-full rounded-lg glass-input px-3 py-2 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="cash" @selected(old('method','cash')==='cash')>{{ __('ui.suppliers.methods.cash') }}</option>
                                    <option value="bank_transfer" @selected(old('method')==='bank_transfer')>{{ __('ui.suppliers.methods.bank_transfer') }}</option>
                                    <option value="wallet" @selected(old('method')==='wallet')>{{ __('ui.suppliers.methods.wallet') }}</option>
                                    <option value="cheque" @selected(old('method')==='cheque')>{{ __('ui.suppliers.methods.cheque') }}</option>
                                    <option value="other" @selected(old('method')==='other')>{{ __('ui.suppliers.methods.other') }}</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('method')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.purchase_optional') }}</label>
                            <div class="relative">
                                <select name="purchase_id" class="w-full rounded-lg glass-input px-3 py-2 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.suppliers.actions.general_payment') }}</option>
                                    @foreach($recentPurchases as $purchase)
                                    <option value="{{ $purchase->id }}" @selected((string)old('purchase_id')===(string)$purchase->id)>{{ $purchase->purchase_number }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('purchase_id')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.notes') }}</label>
                            <input name="notes" value="{{ old('notes') }}" class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="w-full rounded-lg py-2 text-sm font-medium transition-all flex items-center justify-center gap-2"
                                style="background-color:color-mix(in srgb,var(--primary) 15%,transparent);border:1px solid color-mix(in srgb,var(--primary) 25%,transparent);color:var(--primary)"
                                onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 25%,transparent)'"
                                onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--primary) 15%,transparent)'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                            {{ __('ui.suppliers.actions.add_payment') }}
                        </button>
                    </form>
                </div>

                {{-- Record Return --}}
                <div class="rounded-xl p-5 border" style="border-color:color-mix(in srgb,var(--outline) 25%,transparent)">
                    <h4 class="text-sm font-semibold mb-4" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.record_return') }}</h4>
                    <form method="POST" action="{{ route('suppliers.returns.store', $supplier) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.purchase') }}</label>
                            <div class="relative">
                                <select name="purchase_id" required class="w-full rounded-lg glass-input px-3 py-2 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.suppliers.actions.select_purchase') }}</option>
                                    @foreach($recentPurchases as $purchase)
                                    <option value="{{ $purchase->id }}" @selected((string)old('purchase_id')===(string)$purchase->id)>{{ $purchase->purchase_number }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('purchase_id')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.date') }}</label>
                            <input type="date" name="return_date" value="{{ old('return_date', now()->format('Y-m-d')) }}" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                            @error('return_date')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.amount') }}</label>
                            <input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm font-mono">
                            @error('amount')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ __('ui.suppliers.labels.reason') }}</label>
                            <input name="reason" value="{{ old('reason') }}"
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                            @error('reason')<p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit"
                                class="w-full rounded-lg py-2 text-sm font-medium transition-all flex items-center justify-center gap-2"
                                style="background-color:color-mix(in srgb,var(--tertiary) 12%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 25%,transparent);color:var(--tertiary)"
                                onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--tertiary) 22%,transparent)'"
                                onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--tertiary) 12%,transparent)'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m480-320 56-56-63-64h167v-80H473l63-64-56-56-160 160 160 160ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm301.5-598.5Q510-807 510-820t-8.5-21.5Q493-850 480-850t-21.5 8.5Q450-833 450-820t8.5 21.5Q467-790 480-790t21.5-8.5ZM200-200v-560 560Z"/></svg>
                            {{ __('ui.suppliers.actions.add_return') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Purchases --}}
        <div class="glass-panel rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b flex justify-between items-center"
                 style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
                <h3 class="text-sm font-semibold" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.recent_purchases') }}</h3>
            </div>
            <div class="p-4 space-y-3">
                @forelse($recentPurchases as $purchase)
                <a href="{{ route('purchases.show', $purchase) }}"
                   class="flex items-center justify-between rounded-xl px-4 py-3 border transition-colors"
                   style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--primary) 3%,transparent)"
                   onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 8%,transparent)'"
                   onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--primary) 3%,transparent)'">
                    <div>
                        <p class="text-sm font-semibold" style="color:var(--on-surface)">{{ $purchase->purchase_number }}</p>
                        <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">
                            {{ $purchase->purchase_date?->format('Y-m-d') }}
                            • {{ __('ui.suppliers.items_count', ['count' => $purchase->items_count]) }}
                        </p>
                    </div>
                    <span class="text-sm font-mono font-semibold" style="color:var(--primary)">
                        {{ \App\Support\CurrencyFormatter::format($purchase->total) }}
                    </span>
                </a>
                @empty
                <p class="text-sm px-2" style="color:var(--on-surface-var)">{{ __('ui.suppliers.empty.no_purchases') }}</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ── Transaction History ─────────────────────────────────────────── --}}
<div class="glass-panel-elevated rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b flex justify-between items-center"
         style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
        <h3 class="text-base font-semibold" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.transaction_history') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 6%,transparent)">
                    @foreach([__('ui.suppliers.labels.date'), __('ui.suppliers.labels.type'), __('ui.suppliers.labels.reference'), __('ui.suppliers.labels.method'), __('ui.suppliers.labels.amount'), __('ui.suppliers.labels.notes')] as $h)
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                @forelse($transactions as $tx)
                @php
                $txType = (string) $tx->transaction_type;
                $txLabel = match($txType) {
                    'purchase' => __('ui.suppliers.transactions.purchase'),
                    'payment'  => __('ui.suppliers.transactions.payment'),
                    'return'   => __('ui.suppliers.transactions.return'),
                    default    => $txType,
                };
                $txBadgeStyle = match($txType) {
                    'payment'  => 'background-color:color-mix(in srgb,var(--success) 10%,transparent 90%);border:1px solid color-mix(in srgb,var(--success) 25%,transparent 75%);color:var(--success)',
                    'return'   => 'background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)',
                    default    => 'background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border:1px solid color-mix(in srgb,var(--secondary) 20%,transparent);color:var(--secondary)',
                };
                $dotStyle = match($txType) {
                    'payment' => 'background-color:var(--success)',
                    'return'  => 'background-color:var(--tertiary)',
                    default   => 'background-color:var(--secondary)',
                };
                $methodLabel = match((string) $tx->payment_method) {
                    'cash'          => __('ui.suppliers.methods.cash'),
                    'bank_transfer' => __('ui.suppliers.methods.bank_transfer'),
                    'wallet'        => __('ui.suppliers.methods.wallet'),
                    'cheque'        => __('ui.suppliers.methods.cheque'),
                    'other'         => __('ui.suppliers.methods.other'),
                    'credit'        => __('ui.suppliers.methods.credit'),
                    'return'        => __('ui.suppliers.methods.return'),
                    default         => (string) $tx->payment_method,
                };
                @endphp
                <tr class="transition-colors"
                    onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--surface-highest) 30%,transparent)'"
                    onmouseleave="this.style.backgroundColor=''">
                    <td class="px-6 py-4 whitespace-nowrap" style="color:var(--on-surface)">
                        {{ $tx->transaction_date ? \Illuminate\Support\Carbon::parse($tx->transaction_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium" style="{{ $txBadgeStyle }}">
                            <span class="w-1.5 h-1.5 rounded-full" style="{{ $dotStyle }}"></span>
                            {{ $txLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" style="color:var(--on-surface-var)">{{ $tx->reference_number }}</td>
                    <td class="px-6 py-4" style="color:var(--on-surface-var)">{{ $methodLabel }}</td>
                    <td class="px-6 py-4 font-semibold font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($tx->amount) }}</td>
                    <td class="px-6 py-4" style="color:var(--on-surface-var)">{{ $tx->notes ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.suppliers.empty.no_transactions') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Supply History ──────────────────────────────────────────────── --}}
<div class="glass-panel rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b"
         style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
        <h3 class="text-base font-semibold" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.supply_history') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 6%,transparent)">
                    @foreach([__('ui.suppliers.labels.date'), __('ui.suppliers.labels.purchase_number'), __('ui.suppliers.labels.material'), __('ui.suppliers.labels.qty'), __('ui.suppliers.labels.unit_cost'), __('ui.suppliers.labels.line_total')] as $h)
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                @forelse($supplyHistory as $line)
                <tr class="transition-colors"
                    onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                    onmouseleave="this.style.backgroundColor=''">
                    <td class="px-5 py-3 whitespace-nowrap" style="color:var(--on-surface-var)">
                        {{ $line->purchase_date ? \Illuminate\Support\Carbon::parse($line->purchase_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('purchases.show', $line->purchase_id) }}"
                           class="font-semibold hover:underline" style="color:var(--primary)">
                            {{ $line->purchase_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3 font-semibold" style="color:var(--on-surface)">{{ $line->ingredient_name }}</td>
                    <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ number_format((float) $line->quantity, 3) }}</td>
                    <td class="px-5 py-3 font-mono" style="color:var(--on-surface-var)">{{ \App\Support\CurrencyFormatter::format($line->unit_cost) }}</td>
                    <td class="px-5 py-3 font-semibold font-mono" style="color:var(--on-surface)">{{ \App\Support\CurrencyFormatter::format($line->line_total) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.suppliers.empty.no_supply_history') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Supplier Price Comparison ───────────────────────────────────── --}}
<div class="glass-panel rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b"
         style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
        <h3 class="text-base font-semibold" style="color:var(--on-surface)">{{ __('ui.suppliers.cards.supplier_prices') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 6%,transparent)">
                    @foreach([__('ui.suppliers.labels.material'), __('ui.suppliers.labels.supplier'), __('ui.suppliers.labels.best_unit_cost'), __('ui.suppliers.labels.best_offer')] as $h)
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                @forelse($supplierPrices as $materialRows)
                @php
                $bestRow = $materialRows->sortBy('best_unit_cost')->first();
                $materialName = $bestRow->ingredient_name ?? '-';
                @endphp
                @foreach($materialRows as $index => $row)
                <tr class="transition-colors"
                    onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                    onmouseleave="this.style.backgroundColor=''">
                    @if($index === 0)
                    <td class="px-5 py-3 font-semibold" style="color:var(--on-surface)" rowspan="{{ $materialRows->count() }}">
                        {{ $materialName }}
                    </td>
                    @endif
                    <td class="px-5 py-3 {{ (int) $row->supplier_id === (int) $supplier->id ? 'font-semibold' : '' }}"
                        style="color:{{ (int) $row->supplier_id === (int) $supplier->id ? 'var(--primary)' : 'var(--on-surface-var)' }}">
                        {{ $row->supplier_name }}
                    </td>
                    <td class="px-5 py-3 font-mono font-semibold" style="color:var(--on-surface)">
                        {{ \App\Support\CurrencyFormatter::format((float) $row->best_unit_cost) }}
                    </td>
                    <td class="px-5 py-3">
                        @if((int) $bestRow->supplier_id === (int) $row->supplier_id)
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[12px]" ><path d="m354-287 126-76 126 77-33-144 111-96-146-13-58-136-58 135-146 13 111 97-33 143ZM233-120l65-281L80-590l288-25 112-265 112 265 288 25-218 189 65 281-247-149-247 149Zm247-350Z"/></svg>
                            {{ __('ui.suppliers.badges.best') }}
                        </span>
                        @else
                        <span style="color:var(--on-surface-var)">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.suppliers.empty.no_price_history') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-layouts.app>
