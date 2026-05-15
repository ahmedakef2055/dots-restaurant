<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.waiter.title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.waiter.title'))]); ?>
    <div
        x-data="waiterPage({ products: <?php echo \Illuminate\Support\Js::from($products)->toHtml() ?>, categories: <?php echo \Illuminate\Support\Js::from($categories)->toHtml() ?>, tables: <?php echo \Illuminate\Support\Js::from($tables)->toHtml() ?> }, { storeOrder: '<?php echo e(route('pos.orders.store')); ?>', tableOrderTemplate: '<?php echo e(route('pos.tables.order', ['restaurantTable' => '__TABLE__'])); ?>', selectTableRequiredMessage: '<?php echo e(__('messages.errors.select_table_for_dine_in')); ?>', csrf: '<?php echo e(csrf_token()); ?>' })"
        class="waiter-canvas">

        
        <div class="waiter-page-header">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="page-title"><?php echo e(__('ui.waiter.title')); ?></h1>
                    <p class="page-subtitle hidden sm:block"><?php echo e(__('ui.waiter.subtitle')); ?></p>
                </div>
            </div>

            
            <div class="hidden md:flex items-center gap-2">
                <div x-show="cart.length > 0" x-cloak
                     class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                     style="background:color-mix(in srgb,var(--primary) 12%,transparent 88%);color:var(--primary);border:1px solid color-mix(in srgb,var(--primary) 25%,transparent 75%)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
                    <span x-text="cart.length + ' <?php echo e(__('ui.waiter.order')); ?>'"></span>
                    <span style="color:var(--outline)">·</span>
                    <span x-text="currency(subtotal)"></span>
                </div>
            </div>
        </div>

        
        <div class="waiter-mobile-tabs lg:hidden">
            <button type="button"
                @click="activeTab = 'tables'"
                :class="activeTab === 'tables' ? 'waiter-mobile-tab-active' : 'waiter-mobile-tab'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M173-600h614l-34-120H208l-35 120Zm307-60Zm192 140H289l-11 80h404l-10-80ZM160-160l49-360h-89q-20 0-31.5-16T82-571l57-200q4-13 14-21t24-8h606q14 0 24 8t14 21l57 200q5 19-6.5 35T840-520h-88l48 360h-80l-27-200H267l-27 200h-80Z"/></svg>
                <span><?php echo e(__('ui.waiter.select_table')); ?></span>
                <span x-show="selectedTableId" x-cloak
                      class="waiter-tab-badge">✓</span>
            </button>
            <button type="button"
                @click="selectedTableId ? activeTab = 'products' : activeTab = 'tables'"
                :class="activeTab === 'products' ? 'waiter-mobile-tab-active' : 'waiter-mobile-tab'"
                :style="!selectedTableId ? 'opacity:0.45' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                <span><?php echo e(__('ui.waiter.quick_add')); ?></span>
            </button>
            <button type="button"
                @click="cart.length ? activeTab = 'order' : null"
                :class="activeTab === 'order' ? 'waiter-mobile-tab-active' : 'waiter-mobile-tab'"
                :style="!cart.length ? 'opacity:0.45' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 relative  text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                <span><?php echo e(__('ui.waiter.order')); ?></span>
                <span x-show="cart.length > 0" x-cloak
                      class="waiter-tab-badge" x-text="cart.length"></span>
            </button>
        </div>

        
        <div class="waiter-layout">

            
            <aside class="waiter-col waiter-col-tables"
                   :class="{ 'waiter-panel-hidden': activeTab !== 'tables' }">
                
                <div class="waiter-card-header">
                    <div class="waiter-card-icon" style="--wcard-color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary);font-variation-settings:'FILL' 1"><path d="M173-600h614l-34-120H208l-35 120Zm307-60Zm192 140H289l-11 80h404l-10-80ZM160-160l49-360h-89q-20 0-31.5-16T82-571l57-200q4-13 14-21t24-8h606q14 0 24 8t14 21l57 200q5 19-6.5 35T840-520h-88l48 360h-80l-27-200H267l-27 200h-80Z"/></svg>
                    </div>
                    <h2 class="waiter-card-title"><?php echo e(__('ui.waiter.select_table')); ?></h2>
                </div>

                
                <div class="waiter-table-grid">
                    <template x-for="table in tables" :key="table.id">
                        <button
                            type="button"
                            @click="selectTable(table); activeTab = 'products'"
                            class="waiter-table-btn"
                            :class="selectedTableId === Number(table.id)
                                ? 'waiter-table-selected'
                                : (table.status === 'occupied'
                                    ? 'waiter-table-occupied'
                                    : (table.status === 'reserved'
                                        ? 'waiter-table-reserved'
                                        : 'waiter-table-free'))"
                            :title="table.status === 'occupied' ? '<?php echo e(__('ui.common.occupied')); ?>' : (table.status === 'reserved' ? '<?php echo e(__('ui.common.reserved')); ?>' : '<?php echo e(__('ui.common.available')); ?>')">
                            <span class="waiter-table-name" x-text="table.name"></span>
                            <span class="waiter-table-status"
                                x-text="table.status === 'occupied' ? '<?php echo e(__('ui.common.occupied')); ?>' : (table.status === 'reserved' ? '<?php echo e(__('ui.common.reserved')); ?>' : '<?php echo e(__('ui.common.available')); ?>')"></span>
                        </button>
                    </template>

                    <template x-if="!tables.length">
                        <div class="waiter-no-tables">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl mb-1" style="color:var(--outline)"><path d="M173-600h614l-34-120H208l-35 120Zm307-60Zm192 140H289l-11 80h404l-10-80ZM160-160l49-360h-89q-20 0-31.5-16T82-571l57-200q4-13 14-21t24-8h606q14 0 24 8t14 21l57 200q5 19-6.5 35T840-520h-88l48 360h-80l-27-200H267l-27 200h-80Z"/></svg>
                            <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.waiter.no_tables')); ?></p>
                        </div>
                    </template>
                </div>

                
                <div x-cloak x-show="tableLoading" class="waiter-loading-bar">
                    <div class="waiter-loading-inner"></div>
                </div>

                
                <div x-cloak x-show="activeOrder" class="waiter-active-order-banner">
                    <div class="flex items-center gap-2 mb-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" style="color:inherit;font-variation-settings:'FILL' 1"><path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Zm-40 404h560v-568H200v568Zm0-568v568-568Z"/></svg>
                        <span class="font-bold text-xs"><?php echo e(__('ui.waiter.active_order.title')); ?></span>
                    </div>
                    <div class="waiter-active-order-row">
                        <span style="opacity:0.75"><?php echo e(__('ui.waiter.active_order.number')); ?></span>
                        <span class="font-bold" x-text="activeOrder ? activeOrder.order_number : ''"></span>
                    </div>
                    <div class="waiter-active-order-row">
                        <span style="opacity:0.75"><?php echo e(__('ui.waiter.active_order.items')); ?></span>
                        <span class="font-bold" x-text="activeOrder ? activeOrder.items_count : 0"></span>
                    </div>
                    <div class="waiter-active-order-row">
                        <span style="opacity:0.75"><?php echo e(__('ui.waiter.active_order.total')); ?></span>
                        <span class="font-bold" x-text="currency(activeOrder ? activeOrder.total : 0)"></span>
                    </div>
                </div>
            </aside>

            
            <section class="waiter-col waiter-col-quick"
                     :class="{ 'waiter-panel-hidden': activeTab !== 'products' }">
                
                <div class="waiter-card-header">
                    <div class="waiter-card-icon" style="--wcard-color:var(--secondary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--secondary);font-variation-settings:'FILL' 1"><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                    </div>
                    <h2 class="waiter-card-title"><?php echo e(__('ui.waiter.quick_add')); ?></h2>
                    
                    <div x-show="selectedTableId" x-cloak
                         class="ms-auto flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg lg:hidden"
                         style="background:color-mix(in srgb,var(--primary) 12%,transparent 88%);color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]" ><path d="M173-600h614l-34-120H208l-35 120Zm307-60Zm192 140H289l-11 80h404l-10-80ZM160-160l49-360h-89q-20 0-31.5-16T82-571l57-200q4-13 14-21t24-8h606q14 0 24 8t14 21l57 200q5 19-6.5 35T840-520h-88l48 360h-80l-27-200H267l-27 200h-80Z"/></svg>
                        <span x-text="tables.find(t=>Number(t.id)===selectedTableId)?.name ?? ''"></span>
                    </div>
                </div>

                
                <div class="waiter-search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px] waiter-search-icon" ><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                    <input
                        x-model="search"
                        type="text"
                        placeholder="<?php echo e(__('ui.waiter.search_placeholder')); ?>"
                        class="waiter-search-input"
                        dir="auto">
                </div>

                
                <div class="waiter-cat-type-row">
                    <button type="button" class="waiter-cat-type-btn"
                        @click="setCategoryType('all')"
                        :class="categoryTypeFilter === 'all' ? 'waiter-cat-type-active' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]"><path d="M120-520v-320h320v320H120Zm0 400v-320h320v320H120Zm400-400v-320h320v320H520Zm0 400v-320h320v320H520ZM200-600h160v-160H200v160Zm400 0h160v-160H600v160Zm0 400h160v-160H600v160Zm-400 0h160v-160H200v160Zm400-400Zm0 240Zm-240 0Zm0-240Z"/></svg>
                        <?php echo e(__('ui.waiter.filters.all_categories')); ?>

                    </button>
                    <template x-if="categories.some(c => c.type === 'main')">
                        <button type="button" class="waiter-cat-type-btn"
                            @click="setCategoryType('main')"
                            :class="categoryTypeFilter === 'main' ? 'waiter-cat-type-active' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]"><path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
                            <?php echo e(__('ui.waiter.filters.main_categories')); ?>

                        </button>
                    </template>
                    <template x-if="categories.some(c => c.type === 'sub')">
                        <button type="button" class="waiter-cat-type-btn"
                            @click="setCategoryType('sub')"
                            :class="categoryTypeFilter === 'sub' ? 'waiter-cat-type-active' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]"><path d="M600-120v-120H440v-400h-80v120H80v-320h280v120h240v-120h280v320H600v-120h-80v320h80v-120h280v320H600ZM160-760v160-160Zm520 400v160-160Zm0-400v160-160Zm0 160h120v-160H680v160Zm0 400h120v-160H680v160ZM160-600h120v-160H160v160Z"/></svg>
                            <?php echo e(__('ui.waiter.filters.sub_categories')); ?>

                        </button>
                    </template>
                </div>

                
                <div class="waiter-chips-row">
                    <button type="button"
                        @click="selectCategory(null)"
                        :class="selectedCategoryId === null ? 'waiter-chip-active' : 'waiter-chip'">
                        <?php echo e(__('ui.waiter.filters.all_products')); ?>

                    </button>
                    <template x-for="category in visibleCategories" :key="category.id">
                        <button type="button"
                            @click="selectCategory(category.id)"
                            :title="categoryLabel(category)"
                            :class="selectedCategoryId === category.id ? 'waiter-chip-active' : 'waiter-chip'">
                            <span x-text="categoryLabel(category)"></span>
                        </button>
                    </template>
                    <template x-if="!visibleCategories.length">
                        <span class="waiter-chip" style="opacity:0.5;pointer-events:none"><?php echo e(__('ui.waiter.filters.no_categories')); ?></span>
                    </template>
                </div>

                
                <div class="waiter-product-grid-wrap">
                    <template x-if="!filteredProducts.length">
                        <div class="waiter-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl mb-1" style="color:var(--outline)"><path d="M138.5-138.5Q80-197 80-280t58.5-141.5Q197-480 280-480t141.5 58.5Q480-363 480-280t-58.5 141.5Q363-80 280-80t-141.5-58.5ZM824-120 568-376q-12-13-25.5-26.5T516-428q38-24 61-64t23-88q0-75-52.5-127.5T420-760q-75 0-127.5 52.5T240-580q0 6 .5 11.5T242-557q-18 2-39.5 8T164-535q-2-11-3-22t-1-23q0-109 75.5-184.5T420-840q109 0 184.5 75.5T680-580q0 43-13.5 81.5T629-428l251 252-56 56Zm-615-61 71-71 70 71 29-28-71-71 71-71-28-28-71 71-71-71-28 28 71 71-71 71 28 28Z"/></svg>
                            <p><?php echo e(__('ui.waiter.no_products')); ?></p>
                        </div>
                    </template>

                    <div class="waiter-product-grid">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <button
                                type="button"
                                class="waiter-product-card"
                                @click="addToCart(product, $event)">
                                <div class="waiter-product-info">
                                    <p class="waiter-product-name" x-text="product.name"></p>
                                    <p class="waiter-product-desc" x-show="product.category_id" x-text="productCategoryLabel(product)"></p>
                                    <p class="waiter-product-price" x-text="currency(Number(product.price))"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            
            <aside class="waiter-col waiter-col-order"
                   :class="{ 'waiter-panel-hidden': activeTab !== 'order' }">
                
                <div class="waiter-card-header">
                    <div class="waiter-card-icon" style="--wcard-color:var(--tertiary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary);font-variation-settings:'FILL' 1"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                    </div>
                    <h2 class="waiter-card-title"><?php echo e(__('ui.waiter.order')); ?></h2>
                    
                    <button type="button"
                        @click="activeTab = 'products'"
                        class="ms-auto flex items-center gap-1 text-xs font-medium lg:hidden"
                        style="color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                        <?php echo e(__('ui.waiter.quick_add')); ?>

                    </button>
                </div>

                
                <div class="waiter-order-items">
                    <template x-if="!cart.length">
                        <div class="waiter-empty" style="height:100%">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-4xl mb-2" style="color:var(--outline)"><path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/></svg>
                            <p><?php echo e(__('ui.waiter.tap_to_add')); ?></p>
                        </div>
                    </template>

                    <template x-for="item in cart" :key="item.cart_key">
                        <div class="waiter-cart-item" :data-cart-key="item.cart_key">
                            <div class="flex items-start justify-between gap-2">
                                <p class="waiter-cart-item-name" x-text="item.name"></p>
                                <button type="button" class="waiter-cart-delete" @click="removeItem(item.cart_key)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <div class="waiter-qty-stepper">
                                    <button type="button" class="waiter-qty-btn" @click="decreaseQty(item.cart_key)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M200-440v-80h560v80H200Z"/></svg>
                                    </button>
                                    <span class="waiter-qty-val pos-qty-val" x-text="item.quantity"></span>
                                    <button type="button" class="waiter-qty-btn" @click="increaseQty(item.cart_key)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                                    </button>
                                </div>
                                <span class="waiter-cart-line-total" x-text="currency(item.price * item.quantity)"></span>
                            </div>
                            <input
                                x-model="item.notes"
                                type="text"
                                placeholder="<?php echo e(__('ui.waiter.item_notes_placeholder')); ?>"
                                class="waiter-notes-input mt-2"
                                dir="auto">
                        </div>
                    </template>
                </div>

                
                <div class="waiter-order-footer">
                    <div class="waiter-subtotal-row">
                        <span style="color:var(--on-surface-var)"><?php echo e(__('ui.waiter.subtotal')); ?></span>
                        <span class="font-bold" style="color:var(--on-surface)" x-text="currency(subtotal)"></span>
                    </div>
                    <div>
                        <label class="waiter-label"><?php echo e(__('ui.waiter.order_notes_label')); ?></label>
                        <textarea
                            x-model="notes"
                            rows="2"
                            placeholder="<?php echo e(__('ui.waiter.order_notes_placeholder')); ?>"
                            class="waiter-textarea"
                            dir="auto"></textarea>
                    </div>
                    <p x-cloak x-show="error" class="text-sm font-medium" style="color:var(--error)" x-text="error"></p>
                    <p x-cloak x-show="success" class="text-sm font-medium text-[var(--success)]" x-text="success"></p>
                    <button
                        type="button"
                        class="waiter-cta-btn"
                        @click="placeOrder"
                        x-bind:disabled="processing || !cart.length"
                        x-bind:class="(processing || !cart.length) ? 'opacity-50 cursor-not-allowed' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" ><path d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"/></svg>
                        <span x-show="!processing"><?php echo e(__('ui.waiter.send_to_kitchen')); ?></span>
                        <span x-show="processing"><?php echo e(__('ui.waiter.processing')); ?></span>
                    </button>
                </div>
            </aside>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /var/www/dots-main/resources/views/waiter/index.blade.php ENDPATH**/ ?>