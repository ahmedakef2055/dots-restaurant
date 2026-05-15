<x-layouts.app :title="__('ui.inventory.edit.title')">
    <div class="px-2 pt-4 pb-6 z-10 relative">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('inventory.index') }}" class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
                {{ __('ui.inventory.back') }}
            </a>
            <div>
                <h1 class="text-3xl font-bold text-on-surface tracking-tight">{{ $ingredient->name }}</h1>
                <p class="text-sm text-on-surface-variant mt-1">{{ __('ui.inventory.edit.card_subtitle') }}</p>
            </div>
        </div>

        <div class="glass-panel rounded-xl overflow-hidden shadow-2xl relative max-w-4xl mx-auto">
            <!-- Decorative Glow -->
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-[color-mix(in_srgb,var(--secondary)_15%,transparent)] rounded-full blur-3xl pointer-events-none"></div>

            <form method="POST" action="{{ route('inventory.update', $ingredient) }}" class="p-6 md:p-8 space-y-8 relative z-10">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-on-surface mb-4 flex items-center gap-2 border-b border-[color-mix(in_srgb,var(--primary)_10%,transparent)] pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[var(--primary)]" ><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                        Basic Information
                    </h3>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.name') }}</label>
                            <input name="name" value="{{ old('name', $ingredient->name) }}" required class="w-full rounded-lg glass-input px-4 py-2.5 text-sm transition-all">
                            @error('name')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.supplier') }}</label>
                            <div class="relative">
                                <select name="supplier_id" class="w-full rounded-lg glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.inventory.fields.select_supplier') }}</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $ingredient->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" ><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('supplier_id')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.unit') }}</label>
                            <div class="relative">
                                <select name="unit_id" required class="w-full rounded-lg glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.inventory.fields.select_unit') }}</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" @selected((string) old('unit_id', $ingredient->unit_id) === (string) $unit->id)>{{ $unit->name }} ({{ strtoupper($unit->code) }})</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" ><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('unit_id')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Stock & Costing -->
                <div>
                    <h3 class="text-lg font-semibold text-on-surface mb-4 flex items-center gap-2 border-b border-[color-mix(in_srgb,var(--primary)_10%,transparent)] pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[var(--secondary)]" ><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                        Stock & Costing
                    </h3>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.default_warehouse') }}</label>
                            <div class="relative">
                                <select name="default_warehouse_id" required class="w-full rounded-lg glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="">{{ __('ui.inventory.fields.select_warehouse') }}</option>
                                    @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected((string) old('default_warehouse_id', $ingredient->default_warehouse_id) === (string) $warehouse->id)>
                                        {{ $warehouse->name }}@if($warehouse->code) ({{ strtoupper($warehouse->code) }}) @endif
                                    </option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" ><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                            @error('default_warehouse_id')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.edit.current_quantity') }}</label>
                            <input value="{{ number_format((float) $ingredient->quantity, 3, '.', '') }}" disabled class="w-full rounded-lg bg-[color-mix(in_srgb,var(--surface-highest)_50%,transparent)] border border-[color-mix(in_srgb,var(--outline-var)_30%,transparent)] text-on-surface-variant px-4 py-2.5 text-sm cursor-not-allowed font-mono">
                            <p class="mt-1.5 text-[11px] text-on-surface-variant opacity-70">{{ __('ui.inventory.edit.stock_hint') }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.cost_per_unit') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">$</span>
                                <input name="cost" type="number" step="0.0001" min="0" value="{{ old('cost', $ingredient->cost) }}" required class="w-full rounded-lg glass-input pl-8 pr-4 py-2.5 text-sm transition-all font-mono">
                            </div>
                            @error('cost')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.low_stock_threshold') }}</label>
                            <input name="threshold" type="number" step="0.001" min="0" value="{{ old('threshold', $ingredient->threshold) }}" required class="w-full rounded-lg glass-input px-4 py-2.5 text-sm transition-all font-mono">
                            @error('threshold')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <details class="group bg-[color-mix(in_srgb,var(--surface-highest)_20%,transparent)] border border-[color-mix(in_srgb,var(--primary)_10%,transparent)] rounded-xl overflow-hidden transition-all">
                    <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between text-sm font-semibold text-on-surface hover:bg-[color-mix(in_srgb,var(--surface-highest)_30%,transparent)] transition-colors [&::-webkit-details-marker]:hidden">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[var(--tertiary)]" ><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                            {{ __('ui.inventory.simplified.additional_settings') }}
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-on-surface-variant group-open:rotate-180 transition-transform" ><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
                    </summary>

                    <div class="grid gap-5 border-t border-[color-mix(in_srgb,var(--primary)_10%,transparent)] px-5 py-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.expiry_date') }}</label>
                            <div class="relative">
                                <input type="date" name="expiry_date" value="{{ old('expiry_date', $ingredient->expiry_date?->format('Y-m-d')) }}" class="w-full rounded-lg glass-input px-4 py-2.5 text-sm transition-all">
                            </div>
                            @error('expiry_date')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.expiry_alert_days') }}</label>
                            <input type="number" name="expiry_alert_days" min="1" max="180" value="{{ old('expiry_alert_days', $ingredient->expiry_alert_days ?? 7) }}" class="w-full rounded-lg glass-input px-4 py-2.5 text-sm transition-all font-mono">
                            @error('expiry_alert_days')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-on-surface-variant">{{ __('ui.inventory.fields.status') }}</label>
                            <div class="relative">
                                <select name="is_active" class="w-full rounded-lg glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="1" @selected(old('is_active', (string) (int) $ingredient->is_active) == '1')>{{ __('ui.inventory.fields.active') }}</option>
                                    <option value="0" @selected(old('is_active', (string) (int) $ingredient->is_active) == '0')>{{ __('ui.inventory.fields.inactive') }}</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" ><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                        </div>
                    </div>
                </details>

                <div class="pt-4 flex justify-end gap-3 border-t border-[color-mix(in_srgb,var(--primary)_10%,transparent)]">
                    <a href="{{ route('inventory.adjust.form', $ingredient) }}" class="glass-button-secondary rounded-lg py-2.5 px-6 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M686-132 444-376q-20 8-40.5 12t-43.5 4q-100 0-170-70t-70-170q0-36 10-68.5t28-61.5l146 146 72-72-146-146q29-18 61.5-28t68.5-10q100 0 170 70t70 170q0 23-4 43.5T584-516l244 242q12 12 12 29t-12 29l-84 84q-12 12-29 12t-29-12Zm29-85 27-27-256-256q18-20 26-46.5t8-53.5q0-60-38.5-104.5T386-758l74 74q12 12 12 28t-12 28L332-500q-12 12-28 12t-28-12l-74-74q9 57 53.5 95.5T360-440q26 0 52-8t47-25l256 256ZM472-488Z"/></svg>
                        {{ __('ui.inventory.edit.adjust') }}
                    </a>
                    <button type="submit" class="glass-button-primary rounded-lg py-2.5 px-8 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                        {{ __('ui.inventory.edit.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>