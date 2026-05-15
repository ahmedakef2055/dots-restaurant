<x-layouts.app :title="__('ui.inventory.create.title')">
    <div class="pb-8">

        {{-- ── Page Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('inventory.index') }}"
               class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
                {{ __('ui.inventory.back') }}
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">
                    {{ __('ui.inventory.create.card_title') }}
                </h1>
                <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">
                    {{ __('ui.inventory.create.card_subtitle') }}
                </p>
            </div>
        </div>

        {{-- ── Form Card ───────────────────────────────────────────────────── --}}
        <div class="glass-panel-elevated rounded-2xl overflow-hidden relative max-w-4xl mx-auto">

            {{-- Decorative glow (dark mode accent) --}}
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full blur-3xl pointer-events-none"
                 style="background-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

            <form method="POST" action="{{ route('inventory.store') }}" class="relative z-10">
                @csrf

                {{-- ── Section 1: Basic Information ─────────────────────────── --}}
                <div class="px-6 md:px-8 pt-8 pb-6">
                    <h2 class="text-base font-semibold flex items-center gap-2 pb-3 mb-5 border-b"
                        style="color:var(--on-surface);border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
                              style="background-color:color-mix(in srgb,var(--primary) 12%,transparent)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                        </span>
                        Basic Information
                    </h2>

                    <div class="grid gap-5 md:grid-cols-2">
                        {{-- Material Name --}}
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.name') }}
                                <span style="color:var(--error)">*</span>
                            </label>
                            <input name="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Wagyu Beef Striploin"
                                   class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                            @error('name')
                            <p class="mt-1.5 text-xs flex items-center gap-1" style="color:var(--error)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M508.5-291.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5ZM440-440h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Supplier --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.supplier') }}
                            </label>
                            <div class="relative">
                                <select name="supplier_id"
                                        class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.inventory.fields.select_supplier') }}</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('supplier_id')
                            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.unit') }}
                                <span style="color:var(--error)">*</span>
                            </label>
                            <div class="relative">
                                <select name="unit_id" required
                                        class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.inventory.fields.select_unit') }}</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }} ({{ strtoupper($unit->code) }})</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('unit_id')
                            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t mx-6 md:mx-8" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

                {{-- ── Section 2: Stock & Costing ───────────────────────────── --}}
                <div class="px-6 md:px-8 py-6">
                    <h2 class="text-base font-semibold flex items-center gap-2 pb-3 mb-5 border-b"
                        style="color:var(--on-surface);border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
                              style="background-color:color-mix(in srgb,var(--secondary) 12%,transparent)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--secondary)"><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                        </span>
                        Stock &amp; Costing
                    </h2>

                    <div class="grid gap-5 md:grid-cols-2">
                        {{-- Default Warehouse (read-only) --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.default_warehouse') }}
                            </label>
                            <input value="{{ $mainWarehouse?->name }}@if($mainWarehouse?->code) ({{ strtoupper($mainWarehouse->code) }})@endif"
                                   disabled
                                   class="w-full rounded-xl px-4 py-2.5 text-sm cursor-not-allowed"
                                   style="background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent);border:1px solid color-mix(in srgb,var(--outline-var) 20%,transparent);color:var(--on-surface-var)">
                            <p class="mt-1.5 text-[11px] opacity-70" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.create.main_warehouse_hint') }}
                            </p>
                        </div>

                        {{-- Cost Per Unit --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.cost_per_unit') }}
                                <span style="color:var(--error)">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-medium"
                                      style="color:var(--on-surface-var)">$</span>
                                <input name="cost" type="number" step="0.0001" min="0"
                                       value="{{ old('cost', 0) }}" required
                                       class="w-full rounded-xl glass-input pl-8 pr-4 py-2.5 text-sm font-mono">
                            </div>
                            @error('cost')
                            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Opening Quantity --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.opening_quantity') }}
                                <span style="color:var(--error)">*</span>
                            </label>
                            <input name="quantity" type="number" step="0.001" min="0"
                                   value="{{ old('quantity', 0) }}" required
                                   class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                            @error('quantity')
                            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Low Stock Threshold --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                {{ __('ui.inventory.fields.low_stock_threshold') }}
                                <span style="color:var(--error)">*</span>
                            </label>
                            <input name="threshold" type="number" step="0.001" min="0"
                                   value="{{ old('threshold', 0) }}" required
                                   class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                            @error('threshold')
                            <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t mx-6 md:mx-8" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"></div>

                {{-- ── Section 3: Advanced Settings (collapsible) ───────────── --}}
                <div class="px-6 md:px-8 py-4">
                    <details class="group rounded-xl overflow-hidden border transition-all"
                             style="background-color:color-mix(in srgb,var(--surface-highest) 15%,transparent);border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between text-sm font-semibold transition-colors [&::-webkit-details-marker]:hidden hover:bg-[var(--surface-lowest)]">
                            <div class="flex items-center gap-2" style="color:var(--on-surface)">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
                                      style="background-color:color-mix(in srgb,var(--tertiary) 12%,transparent)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--tertiary)"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                                </span>
                                {{ __('ui.inventory.simplified.additional_settings') }}
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 group-open:rotate-180 transition-transform" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
                        </summary>

                        <div class="border-t px-5 py-5 grid gap-5 md:grid-cols-2"
                             style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                            {{-- Expiry Date --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                    {{ __('ui.inventory.fields.expiry_date') }}
                                </label>
                                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                       class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
                                @error('expiry_date')
                                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Expiry Alert Days --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                    {{ __('ui.inventory.fields.expiry_alert_days') }}
                                </label>
                                <input type="number" name="expiry_alert_days" min="1" max="180"
                                       value="{{ old('expiry_alert_days', 7) }}"
                                       class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
                                @error('expiry_alert_days')
                                <p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                                    {{ __('ui.inventory.fields.status') }}
                                </label>
                                <div class="relative">
                                    <select name="is_active"
                                            class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                        <option value="1" @selected(old('is_active', '1') == '1')>{{ __('ui.inventory.fields.active') }}</option>
                                        <option value="0" @selected(old('is_active') == '0')>{{ __('ui.inventory.fields.inactive') }}</option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                                </div>
                            </div>
                        </div>
                    </details>
                </div>

                {{-- ── Action Buttons ────────────────────────────────────────── --}}
                <div class="px-6 md:px-8 py-6 border-t flex items-center justify-end gap-3"
                     style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
                    <a href="{{ route('inventory.index') }}"
                       class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">
                        {{ __('ui.common.cancel') ?? 'Cancel' }}
                    </a>
                    <button type="submit"
                            class="glass-button-primary rounded-xl py-2.5 px-8 text-sm font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                        {{ __('ui.inventory.create.submit') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-layouts.app>
