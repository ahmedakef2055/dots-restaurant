<x-layouts.pos-shell :title="__('ui.pos.title')">
    <div
        x-data="posPage({ products: @js($products), categories: @js($categories), tables: @js($tables), deliveryEmployees: @js($deliveryEmployees), activeShift: @js($activeShift) }, { storeOrder: '{{ route('pos.orders.store') }}', startShift: '{{ route('pos.shifts.start') }}', endShift: '{{ route('pos.shifts.end') }}', tableOrderTemplate: '{{ route('pos.tables.order', ['restaurantTable' => '__TABLE__']) }}', transferTableTemplate: '{{ route('pos.orders.transfer-table', ['order' => '__ORDER__']) }}', invoiceTemplate: '{{ route('orders.invoice', ['order' => '__ORDER__']) }}', customerLookupTemplate: '{{ route('pos.customers.lookup', ['phone' => '__PHONE__']) }}', barcodeLookup: '{{ route('pos.barcode.lookup') }}', tablesStatus: '{{ route('pos.tables.status') }}', selectTableRequiredMessage: '{{ __('messages.errors.select_table_for_dine_in') }}', deliveryCustomerRequiredMessage: '{{ __('ui.pos.delivery_customer.required_message') }}', deliveryEmployeeRequiredMessage: '{{ __('ui.pos.delivery_customer.delivery_employee_required_message') }}', deliveryEmployeeUnavailableMessage: '{{ __('ui.pos.delivery_customer.no_delivery_employees') }}', barcodeNotFoundMessage: '{{ __('ui.pos.barcode.not_found') }}', csrf: '{{ csrf_token() }}' })"
        class="pos-canvas">

        {{-- ══════════════════════════════════════════════════════════
             SHIFT LOCKED OVERLAY — when no active shift
        ══════════════════════════════════════════════════════════ --}}
        <div x-cloak x-show="!activeShift && !showEndShiftDonePrompt" class="pos-shift-locked-overlay">
            <div class="pos-shift-locked-card relative w-full max-w-sm rounded-3xl p-8 shadow-2xl text-center flex flex-col items-center" style="background:var(--surface-lowest);border:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent 60%)">
                {{-- Glow --}}
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-40 h-20 rounded-full blur-3xl pointer-events-none"
                     style="background:color-mix(in srgb,var(--primary) 20%,transparent 80%)"></div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5"
                         style="background:color-mix(in srgb,var(--primary) 12%,transparent 88%);border:1px solid color-mix(in srgb,var(--primary) 25%,transparent 75%)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl" style="color:var(--primary)"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm296.5-143.5Q560-327 560-360t-23.5-56.5Q513-440 480-440t-56.5 23.5Q400-393 400-360t23.5 56.5Q447-280 480-280t56.5-23.5ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80ZM240-160v-400 400Z"/></svg>
                    </div>

                    <h2 class="text-xl font-bold mb-1" style="color:var(--on-surface)">{{ __('ui.pos.shift.title') }}</h2>
                    <p class="text-sm mb-6 max-w-xs" style="color:var(--on-surface-var)">{{ __('ui.pos.shift.locked_message') }}</p>

                    {{-- Last closed shift summary --}}
                    <template x-if="lastClosedShift">
                        <div class="w-full mb-5 rounded-xl p-3 text-left" style="background:color-mix(in srgb,var(--surface-container) 60%,transparent 40%);border:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent 70%)">
                            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:var(--outline)">{{ __('ui.pos.shift.last_closed_title') }}</p>
                            <div class="flex justify-between text-xs" style="color:var(--on-surface-var)">
                                <span>{{ __('ui.pos.shift.total_sales') }}</span>
                                <span x-text="currency(lastClosedShift.total_sales ?? 0)" style="color:var(--on-surface)"></span>
                            </div>
                            <div class="flex justify-between text-xs mt-1" style="color:var(--on-surface-var)">
                                <span>{{ __('ui.pos.shift.difference') }}</span>
                                <span x-text="currency(lastClosedShift.difference ?? 0)" :style="(lastClosedShift.difference ?? 0) >= 0 ? 'color:var(--success)' : 'color:var(--error)'"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Opening cash input --}}
                    <div class="w-full max-w-xs">
                        <label class="pos-label text-left block">{{ __('ui.pos.shift.opening_cash') }}</label>
                        <input
                            x-model="openingCash"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="{{ __('ui.pos.shift.opening_cash_placeholder') }}"
                            class="pos-input mb-3"
                            @keydown.enter="startShift()">

                        <button
                            type="button"
                            class="pos-cta-btn"
                            @click="startShift()"
                            x-bind:disabled="startingShift"
                            x-bind:class="startingShift ? 'opacity-60 cursor-not-allowed' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" ><path d="m380-300 280-180-280-180v360ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            <span x-show="!startingShift">{{ __('ui.pos.shift.start_button') }}</span>
                            <span x-show="startingShift">{{ __('ui.pos.shift.starting_button') }}</span>
                        </button>

                        <p x-cloak x-show="error" class="mt-2 text-xs font-medium" style="color:var(--error)" x-text="error"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             MOBILE TAB BAR (only when shift active)
        ══════════════════════════════════════════════════════════ --}}
        <div x-cloak class="pos-mobile-tabs lg:hidden" x-show="!!activeShift">
            <button type="button"
                @click="posTab = 'products'"
                :class="posTab === 'products' ? 'pos-mobile-tab-active' : 'pos-mobile-tab'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-5 h-5"><path d="M120-520v-320h320v320H120Zm0 400v-320h320v320H120Zm400-400v-320h320v320H520Zm0 400v-320h320v320H520ZM200-600h160v-160H200v160Zm400 0h160v-160H600v160Zm0 400h160v-160H600v160Zm-400 0h160v-160H200v160Z"/></svg>
                <span>{{ __('ui.pos.filters.all_products') }}</span>
            </button>
            <button type="button"
                @click="posTab = 'cart'"
                :class="posTab === 'cart' ? 'pos-mobile-tab-active' : 'pos-mobile-tab'"
                class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-5 h-5"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Z"/></svg>
                <span>{{ __('ui.pos.cart.title') }}</span>
                <span x-show="cart.length" x-cloak x-text="cart.length"
                      class="absolute -top-1 -end-1 min-w-[1.1rem] h-[1.1rem] rounded-full text-[10px] font-bold flex items-center justify-center"
                      style="background:var(--primary);color:#fff"></span>
            </button>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             LEFT PANEL — Product catalogue (visible only when shift is active)
        ══════════════════════════════════════════════════════════ --}}
        <section x-cloak class="pos-left" x-show="!!activeShift && (posTab === 'products')" :class="{ 'pos-mobile-hidden': posTab !== 'products' }">

            {{-- ── Search bar ── --}}
            <div class="px-3 pt-3 pb-1">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"
                         class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                         style="color:var(--on-surface-var)">
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="{{ __('ui.pos.search_products_placeholder') }}"
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl"
                        style="background:color-mix(in srgb,var(--surface-highest) 60%,transparent);border:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent);color:var(--on-surface);outline:none"
                        autocomplete="off">
                </div>
            </div>

            {{-- ── Category type tabs + filter row ── --}}
            <div class="pos-filter-wrap">
                {{-- Category Type Tabs --}}
                <div class="pos-cat-type-tabs">
                    <button type="button"
                        @click="setCategoryType('all'); selectCategory(null)"
                        :class="categoryTypeFilter === 'all' ? 'pos-cat-type-active' : 'pos-cat-type'"
                        class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]"><path d="M120-520v-320h320v320H120Zm0 400v-320h320v320H120Zm400-400v-320h320v320H520Zm0 400v-320h320v320H520ZM200-600h160v-160H200v160Zm400 0h160v-160H600v160Zm0 400h160v-160H600v160Zm-400 0h160v-160H200v160Zm400-400Zm0 240Zm-240 0Zm0-240Z"/></svg>
                        <span>{{ __('ui.pos.filters.all_products') }}</span>
                    </button>
                    <template x-if="categories.some(c => c.type === 'main')">
                        <button type="button"
                            @click="setCategoryType('main'); selectCategory(null)"
                            :class="categoryTypeFilter === 'main' ? 'pos-cat-type-active' : 'pos-cat-type'"
                            class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]"><path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
                            <span>{{ __('ui.pos.filters.main_categories') }}</span>
                        </button>
                    </template>
                    <template x-if="categories.some(c => c.type === 'sub')">
                        <button type="button"
                            @click="setCategoryType('sub'); selectCategory(null)"
                            :class="categoryTypeFilter === 'sub' ? 'pos-cat-type-active' : 'pos-cat-type'"
                            class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]"><path d="M600-120v-120H440v-400h-80v120H80v-320h280v120h240v-120h280v320H600v-120h-80v320h80v-120h280v320H600ZM160-760v160-160Zm520 400v160-160Zm0-400v160-160Zm0 160h120v-160H680v160Zm0 400h120v-160H680v160ZM160-600h120v-160H160v160Z"/></svg>
                            <span>{{ __('ui.pos.filters.sub_categories') }}</span>
                        </button>
                    </template>
                </div>

                {{-- Category chips --}}
                <div class="pos-category-bar">
                    <button type="button"
                        @click="selectCategory(null)"
                        :class="selectedCategoryId === null ? 'pos-chip-active' : 'pos-chip'"
                        class="whitespace-nowrap">
                        {{ __('ui.pos.filters.all') }}
                    </button>
                    <template x-for="category in visibleCategories" :key="category.id">
                        <button type="button"
                            @click="selectCategory(category.id)"
                            :title="categoryLabel(category)"
                            :class="selectedCategoryId === category.id ? 'pos-chip-active' : 'pos-chip'"
                            class="whitespace-nowrap">
                            <span x-text="categoryLabel(category)"></span>
                        </button>
                    </template>
                    <template x-if="!visibleCategories.length">
                        <span class="pos-chip opacity-50">{{ __('ui.pos.filters.no_categories') }}</span>
                    </template>
                </div>
            </div>

            {{-- ── Barcode Scanner ── --}}
            <div class="px-3 pb-2 pt-1">
                <div class="flex items-center gap-2 rounded-xl px-3 py-2"
                     style="background:color-mix(in srgb,var(--primary) 5%,transparent);border:1px solid color-mix(in srgb,var(--primary) 15%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"
                         class="w-5 h-5 shrink-0" style="color:var(--primary)">
                        <path d="M40-120v-720h80v720H40Zm120 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h120v720H400Zm160 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h80v720h-80Z"/>
                    </svg>
                    <input
                        id="pos-barcode-input"
                        type="text"
                        x-model="barcodeInput"
                        @keydown.enter.prevent="scanBarcode()"
                        @input="onBarcodeInput()"
                        autocomplete="off"
                        placeholder="{{ __('ui.pos.barcode.scan_placeholder') }}"
                        class="flex-1 bg-transparent text-sm font-mono outline-none tracking-widest"
                        style="color:var(--on-surface)"
                        inputmode="none">
                    <button type="button"
                        @click="scanBarcode()"
                        x-show="barcodeInput.length"
                        class="text-xs font-semibold px-2 py-1 rounded-lg transition-colors"
                        style="color:var(--primary);background:color-mix(in srgb,var(--primary) 10%,transparent)">
                        {{ __('ui.pos.barcode.scan_button') }}
                    </button>
                    <div x-cloak x-show="barcodeScanning" class="w-4 h-4 rounded-full border-2 border-t-transparent animate-spin shrink-0"
                         style="border-color:var(--primary);border-top-color:transparent"></div>
                </div>
                <p x-cloak x-show="barcodeError" class="mt-1 text-xs font-medium px-1" style="color:var(--error)" x-text="barcodeError"></p>
            </div>

            {{-- ── Product grid ── --}}
            <div class="pos-product-grid-wrap">
                <div class="pos-product-grid">
                    <template x-if="!filteredProducts.length">
                        <div class="pos-empty-state col-span-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-4xl mb-2" style="color:var(--outline)"><path d="M138.5-138.5Q80-197 80-280t58.5-141.5Q197-480 280-480t141.5 58.5Q480-363 480-280t-58.5 141.5Q363-80 280-80t-141.5-58.5ZM824-120 568-376q-12-13-25.5-26.5T516-428q38-24 61-64t23-88q0-75-52.5-127.5T420-760q-75 0-127.5 52.5T240-580q0 6 .5 11.5T242-557q-18 2-39.5 8T164-535q-2-11-3-22t-1-23q0-109 75.5-184.5T420-840q109 0 184.5 75.5T680-580q0 43-13.5 81.5T629-428l251 252-56 56Zm-615-61 71-71 70 71 29-28-71-71 71-71-28-28-71 71-71-71-28 28 71 71-71 71 28 28Z"/></svg>
                            <p>{{ __('ui.pos.no_products_for_filter') }}</p>
                        </div>
                    </template>

                    <template x-for="product in filteredProducts" :key="product.id">
                        <button
                            type="button"
                            class="pos-product-card group"
                            @click="addToCart(product, $event)">
                            <div class="pos-product-info">
                                <p class="pos-product-name" x-text="product.name"></p>
                                <p class="pos-product-price" x-text="currency(Number(product.price))"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </section>


        {{-- ══════════════════════════════════════════════════════════
             RIGHT PANEL — Order ticket / cart (visible only when shift is active)
        ══════════════════════════════════════════════════════════ --}}
        <aside x-cloak class="pos-right" x-show="!!activeShift && (posTab === 'cart')" :class="{ 'pos-mobile-hidden': posTab !== 'cart' }">

            {{-- ── Ticket header ── --}}
            <div class="pos-ticket-header flex justify-between items-start">
                <div>
                    <h2 class="pos-ticket-title">
                        {{ __('ui.pos.cart.title') }}
                    </h2>
                    {{-- Order-type switcher --}}
                    <div class="pos-order-type-tabs mt-2">
                        <button type="button"
                            @click="setOrderType('dine_in')"
                            :class="orderType === 'dine_in' ? 'pos-type-tab-active' : 'pos-type-tab'">
                            {{ __('ui.pos.order_types.dine_in') }}
                        </button>
                        <button type="button"
                            @click="setOrderType('takeaway')"
                            :class="orderType === 'takeaway' ? 'pos-type-tab-active' : 'pos-type-tab'">
                            {{ __('ui.pos.order_types.takeaway') }}
                        </button>
                        <button type="button"
                            @click="setOrderType('delivery')"
                            :class="orderType === 'delivery' ? 'pos-type-tab-active' : 'pos-type-tab'">
                            {{ __('ui.pos.order_types.delivery') }}
                        </button>
                    </div>
                </div>

                {{-- End Shift Button --}}
                <button type="button" @click="requestEndShift()" class="h-8 px-3 rounded-lg flex items-center gap-1.5 transition-colors" style="background:color-mix(in srgb,var(--error) 10%,transparent 90%);color:var(--error)" title="{{ __('ui.pos.shift.end_confirm_title') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                    <span class="text-xs font-semibold">{{ __('ui.pos.shift.end_confirm_title') }}</span>
                </button>
            </div>

            {{-- ── Table selector (dine-in only) ── --}}
            <div x-cloak x-show="orderType === 'dine_in'" class="pos-table-selector">
                <p class="pos-section-label">{{ __('ui.pos.select_table') }}</p>
                <div class="pos-table-grid">
                    <template x-for="table in tables" :key="table.id">
                        <button
                            type="button"
                            class="pos-table-btn"
                            @click="selectTable(table)"
                            :class="selectedTableId === table.id
                                ? 'pos-table-btn-active'
                                : (table.status === 'occupied'
                                    ? 'pos-table-btn-occupied'
                                    : (table.status === 'reserved'
                                        ? 'pos-table-btn-reserved'
                                        : 'pos-table-btn-free'))"
                            :title="table.status === 'occupied' ? '{{ __('ui.common.occupied') }}' : (table.status === 'reserved' ? '{{ __('ui.common.reserved') }}' : '{{ __('ui.common.available') }}')">
                            <span class="block font-semibold text-xs" x-text="table.name"></span>
                            <span
                                class="mt-0.5 block text-[9px] font-medium opacity-75"
                                x-text="table.status === 'occupied' ? '{{ __('ui.common.occupied') }}' : (table.status === 'reserved' ? '{{ __('ui.common.reserved') }}' : '{{ __('ui.common.available') }}')"
                            </span>
                        </button>
                    </template>
                </div>

                {{-- Active order info --}}
                <div x-cloak x-show="activeOrder" class="pos-active-order-banner">
                    <p class="font-semibold text-xs">{{ __('ui.pos.active_order.title') }}</p>
                    <p class="text-xs mt-0.5">
                        <span class="font-medium">{{ __('ui.pos.active_order.number') }}:</span>
                        <span x-text="activeOrder ? activeOrder.order_number : ''"></span>
                    </p>
                    <p class="text-xs">
                        <span class="font-medium">{{ __('ui.pos.active_order.items') }}:</span>
                        <span x-text="activeOrder ? activeOrder.items_count : 0"></span>
                    </p>
                    <p class="text-xs">
                        <span class="font-medium">{{ __('ui.pos.active_order.total') }}:</span>
                        <span x-text="currency(activeOrder ? activeOrder.total : 0)"></span>
                    </p>
                </div>

                {{-- Transfer table --}}
                <div x-cloak x-show="activeOrder" class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                    <select x-model="transferTableId" class="pos-select flex-1">
                        <option value="">{{ __('ui.pos.transfer.select_destination') }}</option>
                        <template x-for="table in availableTransferTables" :key="`transfer-${table.id}`">
                            <option :value="table.id" x-text="table.name"></option>
                        </template>
                    </select>
                    <x-ui.button type="button" variant="secondary" class="shrink-0"
                        @click="transferOrderTable"
                        x-bind:disabled="processing || !transferTableId"
                        x-bind:class="(processing || !transferTableId) ? 'opacity-60 cursor-not-allowed' : ''">
                        {{ __('ui.pos.transfer.button') }}
                    </x-ui.button>
                </div>
            </div>

            {{-- ── Unified scroll zone: cart items + footer ── --}}
            <div class="pos-cart-scroll-area">

                {{-- Cart items --}}
                <div class="pos-cart-items">
                    <template x-if="!cart.length">
                        <div class="pos-empty-state h-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-4xl mb-2" style="color:var(--outline)"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
                            <p>{{ __('ui.pos.cart.empty') }}</p>
                        </div>
                    </template>

                    <template x-for="item in cart" :key="item.cart_key">
                        <div class="pos-cart-item group" :data-cart-key="item.cart_key">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="pos-cart-item-name" x-text="item.name"></p>
                                    <p class="pos-cart-item-price" x-text="currency(item.price)"></p>
                                </div>
                                <button type="button"
                                    class="pos-cart-delete-btn opacity-0 group-hover:opacity-100"
                                    @click="removeItem(item.cart_key)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between mt-2">
                                {{-- Qty stepper --}}
                                <div class="pos-qty-stepper">
                                    <button type="button" class="pos-qty-btn" @click="decreaseQty(item.cart_key)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M200-440v-80h560v80H200Z"/></svg>
                                    </button>
                                    <span class="pos-qty-val" x-text="item.quantity"></span>
                                    <button type="button" class="pos-qty-btn" @click="increaseQty(item.cart_key)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                                    </button>
                                </div>
                                <span class="text-sm font-semibold" style="color:var(--on-surface)"
                                    x-text="currency(item.price * item.quantity)"></span>
                            </div>

                            {{-- Notes input --}}
                            <input
                                x-model="item.notes"
                                type="text"
                                placeholder="{{ __('ui.pos.item_notes_placeholder') }}"
                                class="pos-notes-input mt-2">
                        </div>
                    </template>
                </div>

                {{-- ── Discount / notes / totals / CTA ── --}}
                <div class="pos-ticket-footer">

                    {{-- Discount controls --}}
                    <div class="grid grid-cols-2 gap-2" x-cloak x-show="orderType !== 'dine_in'">
                        <div>
                            <label class="pos-label">{{ __('ui.pos.discount_type') ?? 'Discount Type' }}</label>
                            <select x-model="discountType" @change="onDiscountTypeChange()" class="pos-select">
                                <option value="none">{{ __('ui.pos.no_discount') ?? 'None' }}</option>
                                <option value="fixed">{{ __('ui.pos.fixed') ?? 'Fixed' }}</option>
                                <option value="percentage">{{ __('ui.pos.percentage') ?? 'Percentage' }}</option>
                            </select>
                        </div>
                        <div x-cloak x-show="discountType !== 'none'">
                            <label class="pos-label">{{ __('ui.pos.discount_value') ?? 'Value' }}</label>
                            <input x-model.number="discountValue" min="0" type="number" step="0.01" class="pos-input">
                        </div>
                    </div>

                    <div x-cloak x-show="orderType !== 'dine_in' && discountType !== 'none'">
                        <label class="pos-label">{{ __('ui.pos.coupon_code') ?? 'Coupon' }}</label>
                        <input x-model="couponCode" type="text" placeholder="{{ __('ui.pos.enter_coupon') ?? 'Enter coupon code' }}" class="pos-input uppercase">
                    </div>

                    <div>
                        <label class="pos-label">{{ __('ui.pos.notes') ?? 'Notes' }}</label>
                        <textarea x-model="notes" rows="2" class="pos-input resize-none"></textarea>
                    </div>

                    {{-- Totals summary --}}
                    <div class="pos-totals">
                        <div class="pos-totals-row">
                            <span style="color:var(--on-surface-var)">{{ __('ui.pos.subtotal') ?? 'Subtotal' }}</span>
                            <span x-text="currency(subtotal)"></span>
                        </div>
                        <div class="pos-totals-row" x-show="calculatedDiscount > 0">
                            <span style="color:var(--tertiary)">{{ __('ui.pos.discount') ?? 'Discount' }}</span>
                            <span style="color:var(--tertiary)" x-text="'- ' + currency(calculatedDiscount)"></span>
                        </div>
                        <div class="pos-totals-row pos-totals-grand">
                            <span>{{ __('ui.pos.total') ?? 'Total' }}</span>
                            <span x-text="currency(total)"></span>
                        </div>
                    </div>

                    {{-- Payment method --}}
                    <div x-cloak x-show="orderType !== 'dine_in'">
                        <label class="pos-label">{{ __('ui.pos.payment_method') }}</label>
                        <div class="grid grid-cols-4 gap-1.5">

                            @php
                                $pmMethods = [
                                    'cash'     => ['label_en' => 'Cash',     'label_ar' => 'كاش',   'svg' => 'M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z'],
                                    'visa'     => ['label_en' => 'Visa',     'label_ar' => 'فيزا',  'svg' => 'M880-720v480q0 33-23.5 56.5T800-160H160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720Zm-720 80h480v-80H160v80Zm0 160v240h640v-240H160Zm0 240v-480 480Z'],
                                    'instapay' => ['label_en' => 'Instapay', 'label_ar' => 'إنستاباي', 'svg' => 'M280-40q-33 0-56.5-23.5T200-120v-720q0-33 23.5-56.5T280-920h400q33 0 56.5 23.5T760-840v720q0 33-23.5 56.5T680-40H280Zm0-200v120h400v-120H280Zm200 100q17 0 28.5-11.5T520-180q0-17-11.5-28.5T480-220q-17 0-28.5 11.5T440-180q0 17 11.5 28.5T480-140ZM280-320h400v-400H280v400Zm0-480h400v-40H280v40Zm0 560v120-120Zm0-560v-40 40Z'],
                                    'wallet'   => ['label_en' => 'Wallet',   'label_ar' => 'محفظة', 'svg' => 'M240-160q-66 0-113-47T80-320v-320q0-66 47-113t113-47h480q66 0 113 47t47 113v320q0 66-47 113t-113 47H240Zm0-480h480q22 0 42 5t38 15v-20q0-33-23.5-56.5T720-720H240q-33 0-56.5 23.5T160-640v20q18-10 38-15t42-5Zm-74 130 445 108q9 2 18 0t16-8l139-139q-11-15-28-23t-36-8H240q-26 0-46 13.5T166-510Z'],
                                ];
                                $isAr = app()->getLocale() === 'ar';
                            @endphp
                            @foreach($pmMethods as $pmKey => $pm)
                            <button type="button" @click="paymentMethod = '{{ $pmKey }}'"
                                :style="paymentMethod === '{{ $pmKey }}'
                                    ? 'border-color:var(--primary);background:color-mix(in srgb,var(--primary) 10%,transparent 90%);color:var(--primary)'
                                    : 'border-color:var(--outline-var);color:var(--on-surface-var)'"
                                class="flex flex-col items-center gap-1 rounded-lg border py-2 text-[10px] font-semibold transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-4 h-4"><path d="{{ $pm['svg'] }}"/></svg>
                                <span>{{ $isAr ? $pm['label_ar'] : $pm['label_en'] }}</span>
                            </button>
                            @endforeach

                        </div>
                    </div>

                    {{-- Feedback --}}
                    <p x-cloak x-show="error" class="text-xs font-medium" style="color:var(--error)" x-text="error"></p>
                    <p x-cloak x-show="success" class="text-xs font-medium text-[var(--success)]" x-text="success"></p>

                    {{-- CTA --}}
                    <button
                        type="button"
                        class="pos-cta-btn"
                        @click="placeOrder()"
                        x-bind:disabled="!cart.length || processing"
                        x-bind:class="(!cart.length || processing) ? 'opacity-50 cursor-not-allowed' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                        <span x-show="!processing">{{ __('ui.pos.send_to_kitchen') ?? 'Send to Kitchen' }}</span>
                        <span x-show="processing">{{ __('ui.pos.processing') ?? 'Processing…' }}</span>
                    </button>
                </div>

            </div>{{-- /pos-cart-scroll-area --}}

        </aside>


        {{-- ══════════════════════════════════════════════════════════
             DELIVERY CUSTOMER MODAL
        ══════════════════════════════════════════════════════════ --}}
        <div
            x-cloak
            x-show="showDeliveryCustomerPrompt"
            class="pos-modal-backdrop"
            aria-modal="true"
            role="dialog">
            <div class="pos-modal-overlay" @click="closeDeliveryCustomerPrompt"></div>

            <div
                x-show="showDeliveryCustomerPrompt"
                x-transition:enter="transform ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="pos-modal-card">

                {{-- Icon --}}
                <div class="pos-modal-icon" style="--modal-color:var(--secondary)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-2xl" style="font-variation-settings:'FILL' 1;color:var(--secondary)"><path d="M195-235q-35-35-35-85H80v-120q0-66 47-113t113-47h160v200h140l140-174v-106H560v-80h120q33 0 56.5 23.5T760-680v134L580-320H400q0 50-35 85t-85 35q-50 0-85-35Zm125-165Zm-11.5 108.5Q320-303 320-320h-80q0 17 11.5 28.5T280-280q17 0 28.5-11.5ZM200-640v-80h200v80H200Zm475 405q-35-35-35-85t35-85q35-35 85-35t85 35q35 35 35 85t-35 85q-35 35-85 35t-85-35Zm113.5-56.5Q800-303 800-320t-11.5-28.5Q777-360 760-360t-28.5 11.5Q720-337 720-320t11.5 28.5Q743-280 760-280t28.5-11.5ZM160-400h160v-120h-80q-33 0-56.5 23.5T160-440v40Z"/></svg>
                </div>

                <h3 class="pos-modal-title">{{ __('ui.pos.delivery_customer.title') }}</h3>
                <p class="pos-modal-subtitle">{{ __('ui.pos.delivery_customer.subtitle') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="pos-label">{{ __('ui.pos.delivery_customer.delivery_employee') }}</label>
                        <select x-model="deliveryCustomer.employee_id" class="pos-select">
                            <option value="">{{ __('ui.pos.delivery_customer.select_delivery_employee') }}</option>
                            <template x-for="employee in deliveryEmployees" :key="employee.id">
                                <option :value="employee.id" x-text="employee.label"></option>
                            </template>
                        </select>
                        <p x-cloak x-show="!deliveryEmployees.length" class="mt-1 text-xs" style="color:var(--tertiary)">
                            {{ __('ui.pos.delivery_customer.no_delivery_employees') }}
                        </p>
                    </div>

                    <div class="relative md:col-span-2" @click.away="showDeliverySuggestions = false">
                        <label class="pos-label">{{ __('ui.pos.delivery_customer.phone') }}</label>
                        <input
                            x-model="deliveryCustomer.phone"
                            type="text"
                            autocomplete="off"
                            @focus="showDeliverySuggestions = true; lookupDeliveryCustomers()"
                            @input="onDeliveryPhoneInput"
                            class="pos-input"
                            placeholder="{{ __('ui.pos.delivery_customer.phone_placeholder') }}">

                        <div x-cloak x-show="showDeliverySuggestions && deliveryCustomerSuggestions.length"
                             class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border shadow-lg"
                             style="border-color:var(--outline-var);background:var(--surface-lowest)">
                            <template x-for="customer in deliveryCustomerSuggestions" :key="customer.id">
                                <button type="button"
                                    class="flex w-full items-center justify-between border-b px-3 py-2 text-left text-sm last:border-b-0 hover:opacity-80"
                                    style="border-color:var(--surface-high)"
                                    @click="selectDeliveryCustomer(customer)">
                                    <span class="font-semibold" style="color:var(--on-surface)" x-text="customer.phone"></span>
                                    <span class="text-xs" style="color:var(--on-surface-var)" x-text="customer.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="pos-label">{{ __('ui.pos.delivery_customer.name') }}</label>
                        <input x-model="deliveryCustomer.name" type="text" class="pos-input"
                            placeholder="{{ __('ui.pos.delivery_customer.name_placeholder') }}">
                    </div>

                    <div>
                        <label class="pos-label">{{ __('ui.pos.delivery_customer.address') }}</label>
                        <input x-model="deliveryCustomer.address" type="text" class="pos-input"
                            placeholder="{{ __('ui.pos.delivery_customer.address_placeholder') }}">
                    </div>
                </div>

                <p class="mt-2 pos-field-error" x-show="deliveryCustomerError" x-text="deliveryCustomerError"></p>

                <div class="pos-modal-actions">
                    <x-ui.button type="button" variant="secondary" @click="closeDeliveryCustomerPrompt">{{ __('ui.common.cancel') }}</x-ui.button>
                    <x-ui.button type="button" @click="confirmDeliveryCustomer">{{ __('ui.pos.delivery_customer.confirm') }}</x-ui.button>
                </div>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════
             PRINT PROMPT MODAL
        ══════════════════════════════════════════════════════════ --}}
        <div
            x-cloak
            x-show="showPrintPrompt"
            class="pos-modal-backdrop"
            aria-modal="true"
            role="dialog">
            <div class="pos-modal-overlay" @click="closePrintPrompt"></div>

            <div
                x-show="showPrintPrompt"
                x-transition:enter="transform ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="pos-modal-card max-w-md">

                {{-- Icon --}}
                <div class="pos-modal-icon" style="--modal-color:var(--primary)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-2xl" style="font-variation-settings:'FILL' 1;color:var(--primary)"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                </div>

                <h3 class="pos-modal-title">{{ __('ui.pos.print_prompt.title') }}</h3>
                <p class="pos-modal-subtitle">{{ __('ui.pos.print_prompt.message') }}</p>
                <p class="mt-1 text-xs" style="color:var(--outline)" x-show="printPromptDailyNumber">
                    {{ __('ui.orders.col.number', ['default' => 'Order #']) }}
                    <span class="font-bold text-base" x-text="printPromptDailyNumber" style="color:var(--primary)"></span>
                </p>
                <p class="mt-0.5 text-xs font-mono" style="color:var(--outline)" x-show="printPromptOrderNumber">
                    <span x-text="printPromptOrderNumber"></span>
                </p>

                <div class="pos-modal-actions">
                    <x-ui.button type="button" variant="secondary" @click="closePrintPrompt">{{ __('ui.pos.print_prompt.no') }}</x-ui.button>
                    <x-ui.button type="button" @click="printPromptInvoice">{{ __('ui.pos.print_prompt.yes') }}</x-ui.button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             END SHIFT: CONFIRM PROMPT
        ══════════════════════════════════════════════════════════ --}}
        <div x-cloak x-show="showEndShiftConfirmPrompt" class="pos-modal-backdrop">
            <div class="pos-modal-overlay" @click="closeEndShiftConfirmPrompt()"></div>
            <div
                x-show="showEndShiftConfirmPrompt"
                x-transition:enter="transform ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="pos-modal-card max-w-sm">

                {{-- Icon --}}
                <div class="pos-modal-icon" style="--modal-color:var(--error)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-2xl" style="font-variation-settings:'FILL' 1;color:var(--error)"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                </div>

                <h3 class="pos-modal-title">{{ __('ui.pos.shift.end_confirm_title') }}</h3>
                <p class="pos-modal-subtitle">{{ __('ui.pos.shift.end_confirm_message') }}</p>

                <div class="pos-modal-actions">
                    <x-ui.button type="button" variant="secondary" @click="closeEndShiftConfirmPrompt()">{{ __('ui.common.cancel') }}</x-ui.button>
                    <x-ui.button type="button" @click="openEndShiftSettlementPrompt()">{{ __('ui.pos.shift.end_confirm_yes') }}</x-ui.button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             END SHIFT: SETTLEMENT PROMPT
        ══════════════════════════════════════════════════════════ --}}
        <div x-cloak x-show="showEndShiftSettlementPrompt" class="pos-modal-backdrop">
            <div class="pos-modal-overlay" @click="closeEndShiftSettlementPrompt()"></div>
            <div
                x-show="showEndShiftSettlementPrompt"
                x-transition:enter="transform ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="pos-modal-card max-w-md">

                {{-- Icon --}}
                <div class="pos-modal-icon" style="--modal-color:var(--tertiary)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-2xl" style="font-variation-settings:'FILL' 1;color:var(--tertiary)"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                </div>

                <h3 class="pos-modal-title">{{ __('ui.pos.shift.settlement_title') }}</h3>
                <p class="pos-modal-subtitle">{{ __('ui.pos.shift.settlement_subtitle') }}</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="pos-label">{{ __('ui.pos.shift.actual_cash') }}</label>
                        <div class="pos-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 pos-input-icon  text-[16px]" ><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
                            <input x-model="actualCash" type="number" min="0" max="9999999.99" step="0.01"
                                   inputmode="decimal"
                                   class="pos-input pos-input-with-icon"
                                   placeholder="{{ __('ui.pos.shift.actual_cash_placeholder') }}">
                        </div>
                    </div>

                    <div>
                        <label class="pos-label">{{ __('ui.pos.shift.tips') }}</label>
                        <div class="pos-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 pos-input-icon  text-[16px]" ><path d="M640-440 474-602q-31-30-52.5-66.5T400-748q0-55 38.5-93.5T532-880q32 0 60 13.5t48 36.5q20-23 48-36.5t60-13.5q55 0 93.5 38.5T880-748q0 43-21 79.5T807-602L640-440Zm0-112 109-107q19-19 35-40.5t16-48.5q0-22-15-37t-37-15q-14 0-26.5 5.5T700-778l-60 72-60-72q-9-11-21.5-16.5T532-800q-22 0-37 15t-15 37q0 27 16 48.5t35 40.5l109 107ZM280-220l278 76 238-74q-5-9-14.5-15.5T760-240H558q-27 0-43-2t-33-8l-93-31 22-78 81 27q17 5 40 8t68 4q0-11-6.5-21T578-354l-234-86h-64v220ZM40-80v-440h304q7 0 14 1.5t13 3.5l235 87q33 12 53.5 42t20.5 66h80q50 0 85 33t35 87v40L560-60l-280-78v58H40Zm80-80h80v-280h-80v280Zm520-546Z"/></svg>
                            <input x-model="shiftTips" type="number" min="0" max="9999999.99" step="0.01"
                                   inputmode="decimal"
                                   class="pos-input pos-input-with-icon"
                                   placeholder="{{ __('ui.pos.shift.tips_placeholder') }}">
                        </div>
                        <p class="mt-1 text-xs" style="color:var(--outline)">{{ __('ui.pos.shift.tips_note') }}</p>
                    </div>

                    <p x-show="endShiftError" class="pos-field-error" x-text="endShiftError"></p>
                </div>

                <div class="pos-modal-actions">
                    <x-ui.button type="button" variant="secondary" @click="closeEndShiftSettlementPrompt()"
                                 x-bind:disabled="endingShift">
                        {{ __('ui.common.cancel') }}
                    </x-ui.button>
                    <x-ui.button type="button" @click="endShift()"
                                 x-bind:disabled="endingShift"
                                 x-bind:class="endingShift ? 'opacity-60 cursor-not-allowed' : ''">
                        <span x-show="!endingShift">{{ __('ui.pos.shift.end_submit_button') }}</span>
                        <span x-show="endingShift">{{ __('ui.pos.shift.ending_button') }}</span>
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             END SHIFT: DONE PROMPT
        ══════════════════════════════════════════════════════════ --}}
        <div x-cloak x-show="showEndShiftDonePrompt" class="pos-modal-backdrop" style="z-index:110"
             x-effect="if (showEndShiftDonePrompt) setTimeout(() => { if (showEndShiftDonePrompt) finalizeEndShiftFlow() }, 1500)">
            <div class="absolute inset-0 bg-[color-mix(in_srgb,var(--background)_90%,transparent_10%)] backdrop-blur-sm"></div>
            <div
                x-show="showEndShiftDonePrompt"
                x-transition:enter="transform ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                class="pos-modal-card max-w-sm text-center items-center flex flex-col" style="position:relative">

                {{-- Animated success icon --}}
                <div class="pos-modal-success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-4xl" style="font-variation-settings:'FILL' 1;color:var(--success)"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                </div>

                <h3 class="pos-modal-title mt-2">{{ __('ui.pos.shift.end_done_title') }}</h3>
                <p class="pos-modal-subtitle mb-6">{{ __('ui.pos.shift.end_done_message') }}</p>
            </div>
        </div>

    </div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof startPrintQueuePolling === 'function') {
      startPrintQueuePolling();
    }
  });
</script>
</x-layouts.pos-shell>