<x-layouts.app :title="__('ui.purchases.create_title')">

    @php
    $initialRequestType = old('request_type', 'inventory');
    $oldItems = old('items');
    $initialItems = (is_array($oldItems) && count($oldItems) > 0)
        ? collect($oldItems)->map(static function ($item): array {
            return [
                'ingredient_id' => (string) ($item['ingredient_id'] ?? ''),
                'quantity'      => (string) ($item['quantity'] ?? ''),
                'unit_cost'     => (string) ($item['unit_cost'] ?? ''),
                'expiry_date'   => (string) ($item['expiry_date'] ?? ''),
            ];
        })->values()->all()
        : [['ingredient_id' => '', 'quantity' => '', 'unit_cost' => '', 'expiry_date' => '']];
    @endphp

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('purchases.index') }}"
           class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
            {{ __('ui.purchases.back') }}
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">
                {{ __('ui.purchases.create_card_title') }}
            </h1>
            <p class="text-sm mt-0.5" style="color:var(--on-surface-var)">
                {{ __('ui.purchases.create_card_subtitle') }}
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="glass-panel-elevated rounded-2xl overflow-hidden max-w-5xl mx-auto"
         x-data="{
             requestType: @js($initialRequestType),
             items: @js($initialItems),
             add() {
                 this.items.push({ ingredient_id: '', quantity: '', unit_cost: '', expiry_date: '' });
             },
             remove(index) {
                 this.items.splice(index, 1);
             },
             subtotal() {
                 if (this.requestType === 'general_expense') {
                     return (Number(this.$refs.expenseAmount?.value || 0)).toFixed(2);
                 }
                 return this.items
                     .reduce((s, i) => s + ((Number(i.quantity) || 0) * (Number(i.unit_cost) || 0)), 0)
                     .toFixed(2);
             },
         }">

        {{-- Card Header --}}
        <div class="px-6 pt-6 pb-5 border-b flex items-center gap-2"
             style="background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent);border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg"
                  style="background-color:color-mix(in srgb,var(--primary) 12%,transparent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="color:var(--primary)"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
            </span>
            <h2 class="text-sm font-semibold" style="color:var(--on-surface)">
                {{ __('ui.purchases.create_card_title') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('purchases.store') }}" class="relative z-10">
            @csrf

            <div class="px-6 py-6 space-y-6">

                {{-- Info notice --}}
                <div class="flex items-start gap-2.5 rounded-xl border px-4 py-3 text-sm"
                     style="border-color:color-mix(in srgb,var(--warning) 30%,transparent);background-color:color-mix(in srgb,var(--warning) 8%,transparent);color:var(--on-surface)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px] mt-0.5 shrink-0" style="color:var(--warning)"><path d="M440-280h80v-240h-80v240Zm68.5-331.5Q520-623 520-640t-11.5-28.5Q497-680 480-680t-28.5 11.5Q440-657 440-640t11.5 28.5Q463-600 480-600t28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                    <span>{{ __('ui.purchases.create_note') }}</span>
                </div>

                {{-- Primary fields --}}
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                    {{-- Request Type --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.request_type') }}
                            <span style="color:var(--error)">*</span>
                        </label>
                        <div class="relative">
                            <select name="request_type" x-model="requestType" required
                                    class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none">
                                <option value="inventory">{{ __('ui.purchases.type_inventory') }}</option>
                                <option value="general_expense">{{ __('ui.purchases.type_general_expense') }}</option>
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--outline)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                        </div>
                        @error('request_type')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Supplier (inventory only) --}}
                    <div x-show="requestType === 'inventory'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.supplier') }}
                        </label>
                        <div class="relative">
                            <select name="supplier_id" :required="requestType === 'inventory'"
                                    class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none">
                                <option value="">{{ __('ui.purchases.fields.select_supplier') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--outline)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                        </div>
                        @error('supplier_id')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purchase Date --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.purchase_date') }}
                            <span style="color:var(--error)">*</span>
                        </label>
                        <input type="date" name="purchase_date"
                               value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                        @error('purchase_date')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.payment') }}
                        </label>
                        @if(! empty($supportsPaymentMethod))
                            <div class="relative">
                                <select name="payment_method" required
                                        class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none">
                                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>
                                        {{ __('ui.purchases.payment_cash') }}
                                    </option>
                                    <option value="credit" @selected(old('payment_method') === 'credit')>
                                        {{ __('ui.purchases.payment_credit') }}
                                    </option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--outline)"><path d="M480-360 280-560h400L480-360Z"/></svg>
                            </div>
                        @else
                            <div class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-medium"
                                 style="color:var(--on-surface-var)">
                                {{ __('ui.purchases.payment_cash') }}
                            </div>
                        @endif
                        @error('payment_method')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tax Amount (inventory only) --}}
                    <div x-show="requestType === 'inventory'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.tax_amount') }}
                        </label>
                        <input type="number" step="0.01" min="0" name="tax_amount"
                               value="{{ old('tax_amount', 0) }}"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                        @error('tax_amount')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Expense Title (general expense only) --}}
                    <div x-show="requestType === 'general_expense'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.expense_title') }}
                            <span style="color:var(--error)">*</span>
                        </label>
                        <input name="expense_title" value="{{ old('expense_title') }}"
                               :required="requestType === 'general_expense'"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                        @error('expense_title')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Expense Amount (general expense only) --}}
                    <div x-show="requestType === 'general_expense'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.expense_amount') }}
                            <span style="color:var(--error)">*</span>
                        </label>
                        <input x-ref="expenseAmount" type="number" step="0.01" min="0.01"
                               name="expense_amount" value="{{ old('expense_amount') }}"
                               :required="requestType === 'general_expense'"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                        @error('expense_amount')
                            <p class="mt-1 text-xs" style="color:var(--error)">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Items Table (inventory only) ─────────────────────── --}}
                <div x-show="requestType === 'inventory'" x-cloak>
                    <div class="overflow-hidden rounded-xl border"
                         style="border-color:color-mix(in srgb,var(--outline-var) 40%,transparent 60%)">
                        <table class="min-w-full divide-y"
                               style="divide-color:color-mix(in srgb,var(--outline-var) 40%,transparent 60%)">
                            <thead style="background-color:color-mix(in srgb,var(--surface-highest) 40%,transparent 60%)">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider"
                                        style="color:var(--outline)">
                                        {{ __('ui.purchases.fields.ingredient') }}
                                    </th>
                                    <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider"
                                        style="color:var(--outline)">
                                        {{ __('ui.purchases.fields.quantity') }}
                                    </th>
                                    <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider"
                                        style="color:var(--outline)">
                                        {{ __('ui.purchases.fields.unit_cost') }}
                                    </th>
                                    <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider"
                                        style="color:var(--outline)">
                                        {{ __('ui.purchases.expiry_date') }}
                                    </th>
                                    <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider"
                                        style="color:var(--outline)">
                                        {{ __('ui.purchases.fields.action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y"
                                   style="divide-color:color-mix(in srgb,var(--outline-var) 25%,transparent 75%);background-color:var(--surface-lowest)">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-4 py-3">
                                            <select :name="`items[${index}][ingredient_id]`"
                                                    x-model="item.ingredient_id"
                                                    :required="requestType === 'inventory'"
                                                    class="w-full rounded-xl glass-input px-3 py-2 text-sm appearance-none min-w-[180px]">
                                                <option value="">{{ __('ui.purchases.fields.select_ingredient') }}</option>
                                                @foreach($ingredients as $ingredient)
                                                    <option value="{{ $ingredient->id }}">
                                                        {{ $ingredient->name }} ({{ strtoupper($ingredient->unit) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input :name="`items[${index}][quantity]`" x-model="item.quantity"
                                                   type="number" min="0.001" step="0.001"
                                                   :required="requestType === 'inventory'"
                                                   class="w-full rounded-xl glass-input px-3 py-2 text-sm min-w-[100px]" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input :name="`items[${index}][unit_cost]`" x-model="item.unit_cost"
                                                   type="number" min="0.01" step="0.01"
                                                   :required="requestType === 'inventory'"
                                                   class="w-full rounded-xl glass-input px-3 py-2 text-sm min-w-[100px]" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input :name="`items[${index}][expiry_date]`" x-model="item.expiry_date"
                                                   type="date"
                                                   class="w-full rounded-xl glass-input px-3 py-2 text-sm" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" @click="remove(index)"
                                                    x-show="items.length > 1"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all"
                                                    style="border:1px solid color-mix(in srgb,var(--error) 30%,transparent);color:var(--error);background:color-mix(in srgb,var(--error) 5%,transparent)"
                                                    onmouseover="this.style.background='color-mix(in srgb,var(--error) 12%,transparent)'"
                                                    onmouseout="this.style.background='color-mix(in srgb,var(--error) 5%,transparent)'">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                                {{ __('ui.purchases.buttons.remove') }}
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Add row + live subtotal --}}
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <button type="button" @click="add"
                                class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                            {{ __('ui.purchases.buttons.add_item') }}
                        </button>
                        <p class="text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.live_subtotal') }}:
                            <span class="font-bold" style="color:var(--primary)" x-text="`EGP ${subtotal()}`"></span>
                        </p>
                    </div>
                </div>

                {{-- General expense live total --}}
                <div x-show="requestType === 'general_expense'" x-cloak>
                    <p class="text-sm font-medium" style="color:var(--on-surface-var)">
                        {{ __('ui.purchases.fields.live_subtotal') }}:
                        <span class="font-bold" style="color:var(--primary)" x-text="`EGP ${subtotal()}`"></span>
                    </p>
                </div>

                {{-- Notes + Discount --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <div x-show="requestType === 'inventory'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.discount_amount') }}
                        </label>
                        <input type="number" step="0.01" min="0" name="discount_amount"
                               value="{{ old('discount_amount', 0) }}"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                            {{ __('ui.purchases.fields.notes') }}
                        </label>
                        <input name="notes" value="{{ old('notes') }}"
                               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm" />
                    </div>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="border-t px-6 py-5 flex items-center justify-end gap-3"
                 style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
                <a href="{{ route('purchases.index') }}"
                   class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">
                    {{ __('ui.common.cancel') }}
                </a>
                <button type="submit"
                        class="glass-button-primary rounded-xl py-2.5 px-8 text-sm font-semibold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
                    {{ __('ui.purchases.buttons.submit_request') }}
                </button>
            </div>

        </form>
    </div>

</x-layouts.app>
