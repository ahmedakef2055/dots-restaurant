<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.inventory.title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.inventory.title'))]); ?>

    <div id="inventoryWarehouseTabPanel">
        <?php
        $mainTabWarehouse   = $warehouseTabs['main']   ?? null;
        $branchTabWarehouse = $warehouseTabs['branch'] ?? null;
        ?>

        
        <div class="shrink-0 z-10 relative pb-6">

            
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.title')); ?></h1>
                    <p class="text-sm mt-1" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.subtitle')); ?></p>
                </div>
                <div class="flex gap-3">
                    <button type="button"
                        onclick="document.getElementById('transferDetails').open=true;document.getElementById('transferDetails').scrollIntoView({behavior:'smooth'})"
                        class="glass-button-secondary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M280-120 80-320l200-200 57 56-104 104h607v80H233l104 104-57 56Zm400-320-57-56 104-104H120v-80h607L623-784l57-56 200 200-200 200Z"/></svg>
                        <?php echo e(__('ui.inventory.transfer.title')); ?>

                    </button>
                    <a href="<?php echo e(route('inventory.create')); ?>"
                        class="glass-button-primary rounded-lg py-2 px-4 text-sm font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                        <?php echo e(__('ui.inventory.add_material')); ?>

                    </a>
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                
                <div class="glass-panel rounded-xl p-5 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full blur-2xl transition-colors duration-500"
                         style="background-color:color-mix(in srgb,var(--primary) 10%,transparent)"
                         onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 20%,transparent)'"
                         onmouseleave="this.style.backgroundColor='color-mix(in srgb,var(--primary) 10%,transparent)'"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <h3 class="text-sm font-medium flex items-center gap-2" style="color:var(--on-surface-var)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v100h-80v-100H200v560h560v-100h80v100q0 33-23.5 56.5T760-120H200Zm320-160q-33 0-56.5-23.5T440-360v-240q0-33 23.5-56.5T520-680h280q33 0 56.5 23.5T880-600v240q0 33-23.5 56.5T800-280H520Zm280-80v-240H520v240h280Zm-117.5-77.5Q700-455 700-480t-17.5-42.5Q665-540 640-540t-42.5 17.5Q580-505 580-480t17.5 42.5Q615-420 640-420t42.5-17.5Z"/></svg>
                            <?php echo e(__('ui.inventory.dashboard.total_cost')); ?>

                        </h3>
                    </div>
                    <div class="text-3xl font-bold tracking-tight relative z-10" style="color:var(--on-surface)">
                        <?php echo e(\App\Support\CurrencyFormatter::format($costDashboard['total_cost'])); ?>

                    </div>
                    <div class="mt-2 text-xs relative z-10" style="color:var(--on-surface-var)">
                        <?php echo e(__('ui.inventory.tabs.viewing')); ?>: <?php echo e($selectedWarehouse?->name ?? __('ui.inventory.tabs.not_configured')); ?>

                    </div>
                </div>

                
                <div class="glass-panel rounded-xl p-5 relative overflow-hidden group"
                     style="border-color:color-mix(in srgb,var(--error) 15%,transparent)">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full blur-2xl transition-colors duration-500"
                         style="background-color:color-mix(in srgb,var(--error) 10%,transparent)"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <h3 class="text-sm font-medium flex items-center gap-2" style="color:var(--on-surface-var)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--error)"><path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5ZM440-360h80v-200h-80v200Zm40-100Z"/></svg>
                            <?php echo e(__('ui.inventory.simplified.low_stock_items')); ?>

                        </h3>
                        <?php if($lowStockCount > 0): ?>
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color:var(--error);box-shadow:0 0 8px color-mix(in srgb,var(--error) 60%,transparent 40%)"></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-3xl font-bold tracking-tight relative z-10" style="color:var(--on-surface)">
                        <?php echo e(number_format((int) $lowStockCount)); ?>

                        <span class="text-lg font-normal" style="color:var(--on-surface-var)">Items</span>
                    </div>
                    <?php if($lowStockCount > 0): ?>
                    <div class="mt-2 text-xs relative z-10" style="color:var(--error)"><?php echo e(__('ui.inventory.low_stock.message', ['count' => $lowStockCount])); ?></div>
                    <?php else: ?>
                    <div class="mt-2 text-xs relative z-10 text-[var(--success)]">Optimal Levels</div>
                    <?php endif; ?>
                </div>

                
                <div class="glass-panel rounded-xl p-5 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full blur-2xl transition-colors duration-500"
                         style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent)"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <h3 class="text-sm font-medium flex items-center gap-2" style="color:var(--on-surface-var)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary)"><path d="M640-240v-80h104L536-526 376-366 80-664l56-56 240 240 160-160 264 264v-104h80v240H640Z"/></svg>
                            <?php echo e(__('ui.inventory.dashboard.usage_30_days')); ?>

                        </h3>
                    </div>
                    <div class="text-3xl font-bold tracking-tight relative z-10" style="color:var(--on-surface)">
                        <?php echo e(number_format((float) $costDashboard['usage_quantity_30d'], 3)); ?>

                        <span class="text-lg font-normal" style="color:var(--on-surface-var)">Units</span>
                    </div>
                    <div class="mt-2 text-xs relative z-10" style="color:var(--on-surface-var)">Based on 30-day moving average</div>
                </div>

            </div>
        </div>

        
        <div class="flex flex-col md:flex-row gap-6 mb-6">

            
            <div class="flex-1 flex flex-col glass-panel-elevated rounded-xl overflow-hidden min-w-0">

                
                <div class="p-4 border-b flex flex-wrap items-center justify-between gap-4 shrink-0"
                     style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent)">
                    <div class="flex gap-2">
                        <a data-inventory-tab-link="1"
                           href="<?php echo e(route('inventory.index', ['warehouse_tab' => 'main'])); ?>"
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors border
                                  <?php echo e($selectedWarehouseTab === 'main'
                                     ? 'text-[var(--primary)] font-semibold'
                                     : 'hover:bg-[var(--surface-lowest)]'); ?>"
                           style="<?php echo e($selectedWarehouseTab === 'main'
                                     ? 'background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary)'
                                     : 'border-color:transparent;color:var(--on-surface-var)'); ?>">
                            <?php echo e(__('ui.inventory.tabs.main')); ?> (<?php echo e($mainTabWarehouse?->name ?? __('ui.inventory.tabs.not_configured')); ?>)
                        </a>
                        <a data-inventory-tab-link="1"
                           href="<?php echo e(route('inventory.index', ['warehouse_tab' => 'branch'])); ?>"
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors border
                                  <?php echo e($selectedWarehouseTab === 'branch'
                                     ? 'font-semibold'
                                     : 'hover:bg-[var(--surface-lowest)]'); ?>"
                           style="<?php echo e($selectedWarehouseTab === 'branch'
                                     ? 'background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary)'
                                     : 'border-color:transparent;color:var(--on-surface-var)'); ?>">
                            <?php echo e(__('ui.inventory.tabs.branch')); ?> (<?php echo e($branchTabWarehouse?->name ?? __('ui.inventory.tabs.not_configured')); ?>)
                        </a>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="p-1.5 rounded-md transition-colors border border-transparent hover:border-[color-mix(in_srgb,var(--primary)_20%,transparent)]"
                                style="color:var(--on-surface-var)"
                                onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 8%,transparent)';this.style.color='var(--primary)'"
                                onmouseleave="this.style.backgroundColor='';this.style.color='var(--on-surface-var)'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" ><path d="M400-240v-80h160v80H400ZM240-440v-80h480v80H240ZM120-640v-80h720v80H120Z"/></svg>
                        </button>
                    </div>
                </div>

                
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="sticky top-0 backdrop-blur-md z-10"
                               style="background-color:color-mix(in srgb,var(--surface-highest) 80%,transparent)">
                            <tr>
                                <?php $__currentLoopData = [
                                    __('ui.inventory.table.material'),
                                    __('ui.inventory.table.quantity'),
                                    __('ui.inventory.table.cost'),
                                    __('ui.inventory.table.status'),
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider border-b"
                                    style="color:var(--on-surface-var);border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                                    <?php echo e($col); ?>

                                </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider border-b text-right"
                                    style="color:var(--on-surface-var);border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                                    <?php echo e(__('ui.inventory.table.actions')); ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                            <?php $__empty_1 = true; $__currentLoopData = $ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ingredient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                            $warehouseQuantity = (float) ($ingredient->warehouseStocks->first()?->quantity ?? 0);
                            $isLowStock        = (bool) $ingredient->is_active && $warehouseQuantity <= (float) $ingredient->threshold;
                            $percentage        = $ingredient->threshold > 0
                                ? min(100, max(0, ($warehouseQuantity / ($ingredient->threshold * 3)) * 100))
                                : 100;
                            ?>
                            <tr class="transition-colors group <?php echo e($isLowStock ? '' : ''); ?>"
                                style="<?php echo e($isLowStock ? 'background-color:color-mix(in srgb,var(--error) 4%,transparent)' : ''); ?>"
                                onmouseenter="this.style.backgroundColor='<?php echo e($isLowStock ? 'color-mix(in srgb,var(--error) 6%,transparent)' : 'rgba(255,255,255,0.02)'); ?>'"
                                onmouseleave="this.style.backgroundColor='<?php echo e($isLowStock ? 'color-mix(in srgb,var(--error) 4%,transparent)' : ''); ?>'">

                                
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded flex items-center justify-center border shrink-0"
                                             style="background-color:var(--surface-container);border-color:color-mix(in srgb,var(--primary) 12%,transparent)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--secondary)"><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-medium" style="color:var(--on-surface)"><?php echo e($ingredient->name); ?></div>
                                            <div class="text-xs" style="color:var(--on-surface-var)"><?php echo e($ingredient->supplier?->name ?? '-'); ?></div>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="font-medium" style="color:<?php echo e($isLowStock ? 'var(--error)' : 'var(--on-surface)'); ?>">
                                            <?php echo e(number_format($warehouseQuantity, 3)); ?>

                                            <span class="text-xs font-normal" style="color:<?php echo e($isLowStock ? 'var(--error)' : 'var(--on-surface-var)'); ?>">
                                                <?php echo e(strtoupper($ingredient->unitModel?->code ?? $ingredient->unit)); ?>

                                            </span>
                                        </div>
                                        <div class="w-16 h-1.5 rounded-full overflow-hidden hidden sm:block" style="background-color:var(--surface-container)">
                                            <div class="h-full rounded-full" style="width:<?php echo e($percentage); ?>%;background-color:<?php echo e($isLowStock ? 'var(--error)' : 'var(--primary)'); ?>"></div>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-4 py-3 text-sm" style="color:var(--on-surface-var)">
                                    <?php echo e(\App\Support\CurrencyFormatter::format($ingredient->cost)); ?>

                                </td>

                                
                                <td class="px-4 py-3">
                                    <?php if(! $ingredient->is_active): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                          style="background-color:color-mix(in srgb,var(--outline-var) 15%,transparent);color:var(--on-surface-var);border:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent)">
                                        <?php echo e(__('ui.inventory.fields.inactive')); ?>

                                    </span>
                                    <?php elseif($isLowStock): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                          style="background-color:color-mix(in srgb,var(--error) 10%,transparent);color:var(--error);border:1px solid color-mix(in srgb,var(--error) 20%,transparent)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[12px]" ><path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5ZM440-360h80v-200h-80v200Zm40-100Z"/></svg> Critical
                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary-container)]0"></span> Optimal
                                    </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="<?php echo e(route('inventory.adjust.form', $ingredient)); ?>"
                                           class="glass-button-secondary rounded-md px-2.5 py-1 text-xs font-medium">
                                            <?php echo e(__('ui.inventory.edit.adjust')); ?>

                                        </a>
                                        <a href="<?php echo e(route('inventory.edit', $ingredient)); ?>"
                                           class="glass-button-secondary rounded-md px-2.5 py-1 text-xs font-medium">
                                            <?php echo e(__('ui.common.edit')); ?>

                                        </a>
                                        <form method="POST" action="<?php echo e(route('inventory.destroy', $ingredient)); ?>" class="inline-block">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="rounded-md px-2.5 py-1 text-xs font-medium transition-all"
                                                    style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error);background-color:transparent"
                                                    onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 10%,transparent)'"
                                                    onmouseleave="this.style.backgroundColor='transparent'">
                                                <?php echo e(__('ui.common.delete')); ?>

                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm" style="color:var(--on-surface-var)">
                                    <?php echo e(__('ui.inventory.table.none')); ?>

                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="p-3 border-t shrink-0"
                     style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 25%,transparent)">
                    <?php echo e($ingredients->appends(['warehouse_tab' => $selectedWarehouseTab])->links()); ?>

                </div>
            </div>

            
            <div class="w-full md:w-80 shrink-0 flex flex-col gap-5">

                
                <div class="glass-panel rounded-xl flex flex-col overflow-hidden flex-1">
                    <div class="p-4 border-b shrink-0 flex items-center justify-between"
                         style="border-color:color-mix(in srgb,var(--primary) 10%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
                        <h3 class="text-sm font-semibold flex items-center gap-2" style="color:var(--on-surface)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--primary)"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>
                            Recent Activity
                        </h3>
                        <button type="button"
                                onclick="document.getElementById('logsDetails').open=true;document.getElementById('logsDetails').scrollIntoView({behavior:'smooth'})"
                                class="text-xs hover:underline" style="color:var(--primary)">
                            View All
                        </button>
                    </div>
                    <div class="p-4 flex-1 overflow-y-auto space-y-4" style="max-height:320px">
                        <?php $__empty_1 = true; $__currentLoopData = $logs->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                        $rawAction   = strtolower((string) ($log->action ?: $log->adjustment_type));
                        $isError     = in_array($rawAction, ['deduct', 'out', 'production_consume']);
                        $isSuccess   = in_array($rawAction, ['add', 'in']);
                        $dotColor    = $isError ? 'var(--error)' : ($isSuccess ? 'var(--success)' : 'var(--primary)');
                        $textColor   = $isError ? 'var(--error)' : ($isSuccess ? 'var(--success)' : 'var(--primary)');
                        $actionLabel = match ($rawAction) {
                            'add'                => __('ui.inventory.logs.actions.add'),
                            'adjust'             => __('ui.inventory.logs.actions.adjust'),
                            'deduct'             => __('ui.inventory.logs.actions.deduct'),
                            'transfer'           => __('ui.inventory.logs.actions.transfer'),
                            'audit'              => __('ui.inventory.logs.actions.audit'),
                            'production_consume' => __('ui.inventory.logs.actions.production_consume'),
                            'in'                 => __('ui.inventory.logs.actions.in'),
                            'out'                => __('ui.inventory.logs.actions.out'),
                            'set'                => __('ui.inventory.logs.actions.set'),
                            default              => strtoupper((string) ($log->action ?? $log->adjustment_type)),
                        };
                        ?>
                        <div class="relative pl-4 pb-4 last:pb-0 border-l"
                             style="border-color:color-mix(in srgb,var(--primary) 20%,transparent)">
                            <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full border-2"
                                 style="background-color:var(--background);border-color:<?php echo e($dotColor); ?>"></div>
                            <div class="text-[10px] font-mono uppercase mb-1" style="color:var(--on-surface-var)">
                                <?php echo e(($log->occurred_at ?? $log->created_at)?->format('Y-m-d H:i')); ?>

                            </div>
                            <div class="text-sm leading-tight" style="color:var(--on-surface)">
                                <span class="font-medium" style="color:<?php echo e($textColor); ?>"><?php echo e($actionLabel); ?>:</span>
                                <?php echo e(number_format((float) $log->quantity, 3)); ?>

                                <?php echo e($log->ingredient?->unit ? strtoupper((string) $log->ingredient->unit) : ''); ?>

                                of <span style="color:var(--on-surface-var)"><?php echo e($log->ingredient?->name ?? '-'); ?></span>
                            </div>
                            <div class="mt-1 text-xs opacity-70" style="color:var(--on-surface-var)">
                                By <?php echo e($log->user?->name ?? __('ui.inventory.logs.system')); ?>

                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs" style="color:var(--on-surface-var)">No recent activity.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="glass-panel rounded-xl p-4 shrink-0">
                    <h3 class="text-sm font-semibold flex items-center gap-2 mb-1" style="color:var(--on-surface)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="color:var(--tertiary)"><path d="m388-212-56-56 92-92-92-92 56-56 92 92 92-92 56 56-92 92 92 92-56 56-92-92-92 92ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                        Depletion Forecast
                    </h3>
                    <p class="text-[11px] mb-4" style="color:var(--on-surface-var)">Items expiring within 30 days</p>

                    <?php
                    $mergedExpiry = collect();
                    foreach($expiringIngredients as $ing) {
                        $dl = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($ing->expiry_date)->startOfDay(), false);
                        $mergedExpiry->push(['name' => $ing->name, 'expiry' => $ing->expiry_date, 'days_left' => $dl, 'qty' => null, 'unit' => strtoupper($ing->unit ?? '')]);
                    }
                    foreach($expiryAlerts as $alert) {
                        $bName = $alert->ingredient?->name ?? '-';
                        if ($mergedExpiry->where('name', $bName)->isEmpty()) {
                            $dl = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($alert->expiry_date)->startOfDay(), false);
                            $mergedExpiry->push(['name' => $bName, 'expiry' => $alert->expiry_date, 'days_left' => $dl, 'qty' => (float) $alert->remaining_quantity, 'unit' => strtoupper($alert->ingredient?->unit ?? '')]);
                        }
                    }
                    $mergedExpiry = $mergedExpiry->sortBy('days_left')->values();
                    ?>

                    <?php if($mergedExpiry->isNotEmpty()): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $mergedExpiry->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $dl        = $item['days_left'];
                        $isExpired = $dl < 0;
                        $isCrit    = $dl <= 3;
                        $isWarn    = $dl > 3 && $dl <= 7;
                        $barColor  = $isExpired ? 'var(--error)' : ($isCrit ? 'linear-gradient(to right,var(--error),color-mix(in srgb,var(--warning) 90%,var(--error) 10%))' : ($isWarn ? 'linear-gradient(to right,var(--warning),color-mix(in srgb,var(--accent-gold) 70%,var(--warning) 30%))' : 'linear-gradient(to right,var(--accent-gold),var(--success))'));
                        $txtColor  = ($isExpired || $isCrit) ? 'var(--error)' : ($isWarn ? 'var(--warning)' : 'var(--success)');
                        $barPct    = $isExpired ? 100 : max(5, min(100, (30 - $dl) / 30 * 100));
                        $expLbl    = \Carbon\Carbon::parse($item['expiry'])->format('d M Y');
                        $absDl     = abs($dl);
                        ?>
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium truncate pr-2" style="color:var(--on-surface)"><?php echo e($item['name']); ?></span>
                                <span class="whitespace-nowrap font-semibold text-[11px]" style="color:<?php echo e($txtColor); ?>">
                                    <?php if($isExpired): ?>
                                        Expired <?php echo e($absDl); ?> <?php echo e($absDl === 1 ? 'day' : 'days'); ?> ago
                                    <?php elseif($dl === 0): ?>
                                        Expires today!
                                    <?php elseif($dl === 1): ?>
                                        1 day left
                                    <?php else: ?>
                                        <?php echo e($dl); ?> days left
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background-color:var(--surface-container)">
                                    <div class="h-full rounded-full" style="width:<?php echo e($barPct); ?>%;background:<?php echo e($barColor); ?>"></div>
                                </div>
                                <span class="text-[10px] shrink-0" style="color:var(--on-surface-var)"><?php echo e($expLbl); ?></span>
                            </div>
                            <?php if($item['qty'] !== null): ?>
                            <div class="mt-0.5 text-[10px]" style="color:var(--on-surface-var)">Qty: <?php echo e(number_format($item['qty'], 2)); ?> <?php echo e($item['unit']); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[32px] mb-2" style="color:color-mix(in srgb,var(--primary) 30%,transparent)"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                        <p class="text-xs font-medium" style="color:var(--on-surface)">No items expiring soon</p>
                        <p class="text-[11px] mt-1" style="color:var(--on-surface-var)">All materials are within safe expiry range</p>
                    </div>
                    <?php endif; ?>

                    <?php if($smartSuggestions->isNotEmpty()): ?>
                    <div class="mt-4 pt-3 border-t" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <h4 class="text-[11px] font-semibold mb-2 flex items-center gap-1" style="color:var(--on-surface-var)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" style="color:var(--warning)"><path d="M640-240v-80h104L536-526 376-366 80-664l56-56 240 240 160-160 264 264v-104h80v240H640Z"/></svg>
                            Running Low (by usage)
                        </h4>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $smartSuggestions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $dl2 = $s['predicted_days_left'] ?? null; $c2 = $dl2 !== null && $dl2 <= 3; ?>
                            <div class="flex justify-between text-[11px]">
                                <span class="truncate pr-2" style="color:var(--on-surface)"><?php echo e(data_get($s,'ingredient.name','-')); ?></span>
                                <span style="color:<?php echo e($c2 ? 'var(--error)' : 'var(--warning)'); ?>"><?php echo e($dl2 !== null ? 'Est. '.$dl2.'d' : 'No trend'); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        
        <details class="mb-4 group">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 glass-panel rounded-xl transition-all [&::-webkit-details-marker]:hidden hover:bg-[var(--surface-lowest)]">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--secondary)"><path d="M160-200h80v-320h480v320h80v-426L480-754 160-626v426Zm-80 80v-560l400-160 400 160v560H640v-320H320v320H80Zm280 0v-80h80v80h-80Zm80-120v-80h80v80h-80Zm80 120v-80h80v80h-80ZM240-520h480-480Z"/></svg>
                    <div>
                        <p class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.warehouses.title')); ?></p>
                        <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.subtitle')); ?></p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 group-open:rotate-180 transition-transform" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
            </summary>

            <div class="grid gap-6 p-4 mt-2 lg:grid-cols-2">
                
                <div class="glass-panel p-5 rounded-xl">
                    <h4 class="font-semibold mb-1" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.warehouses.create_title')); ?></h4>
                    <p class="text-xs mb-4" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.create_subtitle')); ?></p>
                    <form method="POST" action="<?php echo e(route('inventory.warehouses.store')); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="warehouse_tab" value="<?php echo e($selectedWarehouseTab); ?>">

                        <div>
                            <label class="mb-1 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.fields.name')); ?></label>
                            <input name="name" value="<?php echo e(old('name')); ?>" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.fields.code')); ?></label>
                            <input name="code" value="<?php echo e(old('code')); ?>" placeholder="e.g. CENTRAL"
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm uppercase">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.fields.location')); ?></label>
                            <input name="location" value="<?php echo e(old('location')); ?>"
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.fields.status')); ?></label>
                                <select name="is_active" class="w-full rounded-lg glass-input px-3 py-2 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="1" <?php if(old('is_active','1')==='1'): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.fields.active')); ?></option>
                                    <option value="0" <?php if(old('is_active')==='0'): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.fields.inactive')); ?></option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.fields.default')); ?></label>
                                <select name="is_default" class="w-full rounded-lg glass-input px-3 py-2 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="0" <?php if(old('is_default','0')==='0'): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.warehouses.option_no')); ?></option>
                                    <option value="1" <?php if(old('is_default')==='1'): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.warehouses.option_yes')); ?></option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="glass-button-primary w-full rounded-lg py-2 text-sm font-medium">
                            <?php echo e(__('ui.inventory.warehouses.create_submit')); ?>

                        </button>
                    </form>
                </div>

                
                <div class="glass-panel p-5 rounded-xl overflow-y-auto" style="max-height:500px">
                    <h4 class="font-semibold mb-1" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.warehouses.list_title')); ?></h4>
                    <p class="text-xs mb-4" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.list_subtitle')); ?></p>
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $warehouseDirectory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouseItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <form method="POST" action="<?php echo e(route('inventory.warehouses.update', $warehouseItem)); ?>"
                              class="rounded-xl p-4 bg-[var(--surface-lowest)] border"
                              style="border-color:color-mix(in srgb,var(--primary) 15%,transparent)">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="warehouse_tab" value="<?php echo e($selectedWarehouseTab); ?>">

                            <div class="mb-3 flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold" style="color:var(--on-surface)">
                                    <?php echo e($warehouseItem->name); ?>

                                    <?php if($warehouseItem->is_default): ?>
                                    <span class="ms-2 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                          style="background-color:color-mix(in srgb,var(--primary) 15%,transparent);color:var(--primary)">
                                        <?php echo e(__('ui.inventory.warehouses.default_badge')); ?>

                                    </span>
                                    <?php endif; ?>
                                </p>
                                <span class="text-xs" style="color:var(--on-surface-var)">#<?php echo e($warehouseItem->id); ?></span>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <input name="name" value="<?php echo e($warehouseItem->name); ?>" required class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                                <input name="code" value="<?php echo e($warehouseItem->code); ?>" class="w-full rounded-lg glass-input px-3 py-2 text-sm uppercase">
                                <input name="location" value="<?php echo e($warehouseItem->location); ?>"
                                       placeholder="<?php echo e(__('ui.inventory.warehouses.fields.location')); ?>"
                                       class="w-full rounded-lg glass-input px-3 py-2 text-sm sm:col-span-2">
                                <select name="is_active" class="w-full rounded-lg glass-input px-3 py-2 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="1" <?php if((bool) $warehouseItem->is_active): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.fields.active')); ?></option>
                                    <option value="0" <?php if(! $warehouseItem->is_active): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.fields.inactive')); ?></option>
                                </select>
                                <select name="is_default" class="w-full rounded-lg glass-input px-3 py-2 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <option value="0" <?php if(! $warehouseItem->is_default): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.warehouses.option_no')); ?></option>
                                    <option value="1" <?php if((bool) $warehouseItem->is_default): echo 'selected'; endif; ?>><?php echo e(__('ui.inventory.warehouses.option_yes')); ?></option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="glass-button-secondary w-full rounded-lg py-1.5 text-xs font-medium">
                                    <?php echo e(__('ui.inventory.warehouses.update_submit')); ?>

                                </button>
                            </div>
                        </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.warehouses.none')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </details>

        
        <details id="transferDetails" class="mb-4 group">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 glass-panel rounded-xl transition-all [&::-webkit-details-marker]:hidden hover:bg-[var(--surface-lowest)]">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--primary)"><path d="M280-120 80-320l200-200 57 56-104 104h607v80H233l104 104-57 56Zm400-320-57-56 104-104H120v-80h607L623-784l57-56 200 200-200 200Z"/></svg>
                    <div>
                        <p class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.transfer.title')); ?></p>
                        <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.subtitle')); ?></p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 group-open:rotate-180 transition-transform" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
            </summary>

            <div class="p-4 mt-2">
                <div class="glass-panel p-5 rounded-xl max-w-4xl mx-auto">
                    <?php
                    $defaultTransferFrom = (int) old('from_warehouse_id', $defaultWarehouseId);
                    $defaultTransferTo   = (int) old(
                        'to_warehouse_id',
                        (int) ($warehouses->first(fn($w) => (int) $w->id !== $defaultTransferFrom)?->id ?? $defaultTransferFrom)
                    );
                    ?>
                    <form method="POST" action="<?php echo e(route('inventory.transfer')); ?>" class="grid gap-5 md:grid-cols-2">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="warehouse_tab" value="<?php echo e($selectedWarehouseTab); ?>">

                        <div>
                            <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.from_warehouse')); ?></label>
                            <select name="from_warehouse_id" required class="w-full rounded-lg glass-input px-3 py-2.5 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($warehouse->id); ?>" <?php if((string)$defaultTransferFrom===(string)$warehouse->id): echo 'selected'; endif; ?>>
                                    <?php echo e($warehouse->name); ?><?php if($warehouse->code): ?> (<?php echo e(strtoupper($warehouse->code)); ?>)<?php endif; ?>
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['from_warehouse_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.to_warehouse')); ?></label>
                            <select name="to_warehouse_id" required class="w-full rounded-lg glass-input px-3 py-2.5 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($warehouse->id); ?>" <?php if((string)$defaultTransferTo===(string)$warehouse->id): echo 'selected'; endif; ?>>
                                    <?php echo e($warehouse->name); ?><?php if($warehouse->code): ?> (<?php echo e(strtoupper($warehouse->code)); ?>)<?php endif; ?>
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['to_warehouse_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.material')); ?></label>
                            <select name="ingredient_id" required class="w-full rounded-lg glass-input px-3 py-2.5 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                <?php $__currentLoopData = $auditIngredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($row->id); ?>" <?php if((string)old('ingredient_id')===(string)$row->id): echo 'selected'; endif; ?>><?php echo e($row->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['ingredient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.quantity')); ?></label>
                            <input name="quantity" type="number" min="0.001" step="0.001" value="<?php echo e(old('quantity')); ?>" required
                                   class="w-full rounded-lg glass-input px-3 py-2 text-sm font-mono">
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.transfer.notes')); ?></label>
                            <input name="notes" value="<?php echo e(old('notes')); ?>" class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="md:col-span-2 mt-1">
                            <button type="submit" class="glass-button-primary rounded-lg py-2.5 px-6 text-sm font-medium">
                                <?php echo e(__('ui.inventory.transfer.submit')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </details>

        
        <details class="mb-4 group">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 glass-panel rounded-xl transition-all [&::-webkit-details-marker]:hidden hover:bg-[var(--surface-lowest)]">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--tertiary)"><path d="M160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm40-80h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-40 400v-560 560Z"/></svg>
                    <div>
                        <p class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.simplified.audit_title')); ?></p>
                        <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.audit.subtitle')); ?></p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 group-open:rotate-180 transition-transform" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
            </summary>

            <div class="p-4 mt-2">
                <div class="glass-panel p-5 rounded-xl max-w-4xl mx-auto">
                    <form method="POST" action="<?php echo e(route('inventory.audit')); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="warehouse_tab" value="<?php echo e($selectedWarehouseTab); ?>">

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.audit.warehouse')); ?></label>
                                <select name="warehouse_id" class="w-full rounded-lg glass-input px-3 py-2.5 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($warehouse->id); ?>" <?php if((string)old('warehouse_id',$defaultWarehouseId)===(string)$warehouse->id): echo 'selected'; endif; ?>>
                                        <?php echo e($warehouse->name); ?><?php if($warehouse->code): ?> (<?php echo e(strtoupper($warehouse->code)); ?>)<?php endif; ?>
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['warehouse_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs" style="color:var(--error)"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.audit.material')); ?></label>
                                <select name="ingredient_id" class="w-full rounded-lg glass-input px-3 py-2.5 text-sm [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                                    <?php $__currentLoopData = $auditIngredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($row->id); ?>"><?php echo e($row->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.audit.actual_quantity')); ?></label>
                                <input name="actual_quantity" type="number" min="0" step="0.001"
                                       class="w-full rounded-lg glass-input px-3 py-2 text-sm font-mono">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.audit.notes')); ?></label>
                                <input name="notes" class="w-full rounded-lg glass-input px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="glass-button-primary rounded-lg py-2.5 px-6 text-sm font-medium">
                                <?php echo e(__('ui.inventory.audit.submit')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </details>

        
        <details id="logsDetails" class="group">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 glass-panel rounded-xl transition-all [&::-webkit-details-marker]:hidden hover:bg-[var(--surface-lowest)]">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 " style="color:var(--on-surface-var)"><path d="M348.5-291.5Q360-303 360-320t-11.5-28.5Q337-360 320-360t-28.5 11.5Q280-337 280-320t11.5 28.5Q303-280 320-280t28.5-11.5Zm0-160Q360-463 360-480t-11.5-28.5Q337-520 320-520t-28.5 11.5Q280-497 280-480t11.5 28.5Q303-440 320-440t28.5-11.5Zm0-160Q360-623 360-640t-11.5-28.5Q337-680 320-680t-28.5 11.5Q280-657 280-640t11.5 28.5Q303-600 320-600t28.5-11.5ZM440-280h240v-80H440v80Zm0-160h240v-80H440v80Zm0-160h240v-80H440v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>
                    <div>
                        <p class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.inventory.simplified.logs_title')); ?></p>
                        <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e(__('ui.inventory.logs.subtitle')); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('inventory.logs.pdf', array_filter(['warehouse_id' => request('warehouse_id')]))); ?>"
                       target="_blank"
                       onclick="event.stopPropagation()"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all"
                       style="background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-4 h-4"><path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
                        <?php echo e(__('ui.inventory.logs.download_pdf')); ?>

                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 group-open:rotate-180 transition-transform" style="color:var(--on-surface-var)"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
                </div>
            </summary>

            <div class="p-4 mt-2">
                <div class="glass-panel p-5 rounded-xl overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="border-b" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                                <?php $__currentLoopData = [
                                    __('ui.inventory.logs.headers.material'),
                                    __('ui.inventory.logs.headers.action'),
                                    __('ui.inventory.logs.headers.quantity'),
                                    __('ui.inventory.logs.headers.before'),
                                    __('ui.inventory.logs.headers.after'),
                                    __('ui.inventory.logs.headers.by'),
                                    __('ui.inventory.logs.headers.date'),
                                    __('ui.inventory.logs.headers.note'),
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color:var(--on-surface-var)"><?php echo e($header); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                            $rawAction   = strtolower((string) ($log->action ?: $log->adjustment_type));
                            $actionLabel = match ($rawAction) {
                                'add'                => __('ui.inventory.logs.actions.add'),
                                'adjust'             => __('ui.inventory.logs.actions.adjust'),
                                'deduct'             => __('ui.inventory.logs.actions.deduct'),
                                'transfer'           => __('ui.inventory.logs.actions.transfer'),
                                'audit'              => __('ui.inventory.logs.actions.audit'),
                                'production_consume' => __('ui.inventory.logs.actions.production_consume'),
                                'in'                 => __('ui.inventory.logs.actions.in'),
                                'out'                => __('ui.inventory.logs.actions.out'),
                                'set'                => __('ui.inventory.logs.actions.set'),
                                default              => strtoupper((string) ($log->action ?? $log->adjustment_type)),
                            };

                            $noteText  = trim((string) ($log->note ?? ''));
                            $noteLabel = $noteText;

                            if (strtolower($noteText) === 'initial stock balance') {
                                $noteLabel = __('messages.notes.initial_stock_balance');
                            } elseif (preg_match('/^Received via\s+(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.received_via_purchase', ['purchase_number' => $m[1]]);
                            } elseif (preg_match('/^Consumed by\s+(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.consumed_by_order', ['order_number' => $m[1]]);
                            } elseif (preg_match('/^Transfer out\s*#\s*(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.transfer_out', ['name' => $m[1]]);
                            } elseif (preg_match('/^Transfer in\s*#\s*(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.transfer_in', ['name' => $m[1]]);
                            } elseif (preg_match('/^Stock audit adjustment\s*#\s*(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.stock_audit_adjustment', ['audit_id' => $m[1]]);
                            } elseif (preg_match('/^Consumed by production:\s*(.+)$/i', $noteText, $m)) {
                                $noteLabel = __('messages.notes.production_consumption', ['name' => $m[1]]);
                            }

                            $ingredientName = $log->ingredient?->name ?? '-';
                            $ingredientUnit = $log->ingredient?->unit ? strtoupper((string) $log->ingredient->unit) : '-';
                            ?>
                            <tr class="transition-colors"
                                onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                                onmouseleave="this.style.backgroundColor=''">
                                <td class="px-4 py-3 font-medium" style="color:var(--on-surface)"><?php echo e($ingredientName); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e($actionLabel); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e(number_format((float) $log->quantity, 3)); ?> <?php echo e($ingredientUnit); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e(number_format((float) $log->previous_stock, 3)); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e(number_format((float) $log->new_stock, 3)); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e($log->user?->name ?? __('ui.inventory.logs.system')); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e(($log->occurred_at ?? $log->created_at)?->format('Y-m-d H:i')); ?></td>
                                <td class="px-4 py-3" style="color:var(--on-surface-var)"><?php echo e($noteLabel !== '' ? $noteLabel : '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm" style="color:var(--on-surface-var)">
                                    <?php echo e(__('ui.inventory.logs.none')); ?>

                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-4 pt-3 border-t" style="border-color:color-mix(in srgb,var(--primary) 10%,transparent)">
                        <?php echo e($logs->links()); ?>

                    </div>
                </div>
            </div>
        </details>
    </div>

    <script>
        (function () {
            const panelId    = 'inventoryWarehouseTabPanel';
            const tabSel     = 'a[data-inventory-tab-link="1"]';
            const parser     = new DOMParser();
            let   isLoading  = false;

            async function swapTabContent(url, pushState = true) {
                const currentPanel = document.getElementById(panelId);
                if (!currentPanel || isLoading) return;

                isLoading = true;
                currentPanel.classList.add('opacity-60', 'pointer-events-none');

                try {
                    const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                    if (!res.ok) throw new Error('Failed');
                    const html     = await res.text();
                    const nextDoc  = parser.parseFromString(html, 'text/html');
                    const nextPanel = nextDoc.getElementById(panelId);
                    if (!nextPanel) { window.location.assign(url); return; }
                    currentPanel.replaceWith(nextPanel);
                    if (pushState) window.history.pushState({ inventoryTab: true }, '', url);
                } catch {
                    window.location.assign(url);
                } finally {
                    const active = document.getElementById(panelId);
                    if (active) active.classList.remove('opacity-60', 'pointer-events-none');
                    isLoading = false;
                }
            }

            document.addEventListener('click', function (e) {
                const link = e.target.closest(tabSel);
                if (!link || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                const href = link.getAttribute('href');
                if (!href) return;
                e.preventDefault();
                swapTabContent(href, true);
            });

            window.addEventListener('popstate', function () {
                if (document.getElementById(panelId)) swapTabContent(window.location.href, false);
            });
        })();
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH /var/www/dots-main/resources/views/inventory/index.blade.php ENDPATH**/ ?>