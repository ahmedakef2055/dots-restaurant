<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.orders.title', ['default' => 'Orders'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.orders.title', ['default' => 'Orders']))]); ?>
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteOrderNumber: '' }">

        
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%);box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 35%,transparent)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-white text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                    </div>
                    <h1 class="text-2xl font-extrabold text-[var(--on-surface)]  tracking-tight">
                        <?php echo e(__('ui.orders.title', ['default' => 'Orders'])); ?>

                    </h1>
                </div>
                <p class="text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)] ml-12">
                    <?php echo e(__('ui.orders.subtitle', ['default' => 'Manage and track all restaurant orders'])); ?>

                </p>
            </div>
        </div>

        
        <div class="mb-6 rounded-2xl border border-[var(--outline-var)] dark:border-[var(--outline-var)] bg-[var(--surface-lowest)]  shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--outline-var)] dark:border-[var(--outline-var)] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px] text-[var(--outline)]" ><path d="M400-240v-80h160v80H400ZM240-440v-80h480v80H240ZM120-640v-80h720v80H120Z"/></svg>
                <span class="text-xs font-bold text-[var(--on-surface-var)] dark:text-[var(--outline)] uppercase tracking-wider">Filters</span>
            </div>
            <div class="p-5">
                <form method="GET" action="<?php echo e(route('orders.index')); ?>" class="flex flex-wrap items-end gap-3">
                    
                    <div class="flex-1 min-w-[160px] max-w-xs">
                        <label class="block text-[10px] font-bold text-[var(--outline)] dark:text-[var(--on-surface-var)] mb-1.5 uppercase tracking-widest">
                            <?php echo e(__('ui.orders.filter.search', ['default' => 'Search'])); ?>

                        </label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[15px] text-[var(--outline)]" ><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                            <input type="text" name="q" value="<?php echo e($filters['q']); ?>"
                                   placeholder="<?php echo e(__('ui.orders.filter.search_placeholder', ['default' => 'Order number…'])); ?>"
                                   class="w-full h-9 pl-8 pr-3 bg-[var(--surface-low)]  border border-[var(--outline-var)] dark:border-[var(--outline-var)] rounded-lg text-sm text-[var(--on-surface)]  transition-all placeholder:text-[var(--outline)] outline-none focus:ring-2"
                                   style="--tw-ring-color:var(--primary)" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor=''">
                        </div>
                    </div>

                    
                    <div class="w-36">
                        <label class="block text-[10px] font-bold text-[var(--outline)] dark:text-[var(--on-surface-var)] mb-1.5 uppercase tracking-widest">
                            <?php echo e(__('ui.orders.filter.status', ['default' => 'Status'])); ?>

                        </label>
                        <select name="status" class="w-full h-9 px-3 bg-[var(--surface-low)]  border border-[var(--outline-var)] dark:border-[var(--outline-var)] rounded-lg text-sm text-[var(--on-surface)]  transition-all outline-none" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor=''">
                            <option value=""><?php echo e(__('ui.orders.filter.all_statuses', ['default' => 'All Statuses'])); ?></option>
                            <option value="pending" <?php if($filters['status']==='pending'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.status.pending', ['default' => 'Pending'])); ?></option>
                            <option value="paid" <?php if($filters['status']==='paid'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.status.paid', ['default' => 'Paid'])); ?></option>
                            <option value="cancelled" <?php if($filters['status']==='cancelled'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.status.cancelled', ['default' => 'Cancelled'])); ?></option>
                        </select>
                    </div>

                    
                    <div class="w-36">
                        <label class="block text-[10px] font-bold text-[var(--outline)] dark:text-[var(--on-surface-var)] mb-1.5 uppercase tracking-widest">
                            <?php echo e(__('ui.orders.filter.type', ['default' => 'Type'])); ?>

                        </label>
                        <select name="order_type" class="w-full h-9 px-3 bg-[var(--surface-low)]  border border-[var(--outline-var)] dark:border-[var(--outline-var)] rounded-lg text-sm text-[var(--on-surface)]  transition-all outline-none" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor=''">
                            <option value=""><?php echo e(__('ui.orders.filter.all_types', ['default' => 'All Types'])); ?></option>
                            <option value="dine_in" <?php if($filters['order_type']==='dine_in'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.type.dine_in', ['default' => 'Dine In'])); ?></option>
                            <option value="takeaway" <?php if($filters['order_type']==='takeaway'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.type.takeaway', ['default' => 'Takeaway'])); ?></option>
                            <option value="delivery" <?php if($filters['order_type']==='delivery'): echo 'selected'; endif; ?>><?php echo e(__('ui.orders.type.delivery', ['default' => 'Delivery'])); ?></option>
                        </select>
                    </div>

                    
                    <div class="w-36">
                        <label class="block text-[10px] font-bold text-[var(--outline)] dark:text-[var(--on-surface-var)] mb-1.5 uppercase tracking-widest">
                            <?php echo e(__('ui.orders.filter.from', ['default' => 'From Date'])); ?>

                        </label>
                        <input type="date" name="from" value="<?php echo e($filters['from']); ?>"
                               class="w-full h-9 px-3 bg-[var(--surface-low)]  border border-[var(--outline-var)] dark:border-[var(--outline-var)] rounded-lg text-sm text-[var(--on-surface)]  transition-all outline-none" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor=''">
                    </div>

                    
                    <div class="w-36">
                        <label class="block text-[10px] font-bold text-[var(--outline)] dark:text-[var(--on-surface-var)] mb-1.5 uppercase tracking-widest">
                            <?php echo e(__('ui.orders.filter.to', ['default' => 'To Date'])); ?>

                        </label>
                        <input type="date" name="to" value="<?php echo e($filters['to']); ?>"
                               class="w-full h-9 px-3 bg-[var(--surface-low)]  border border-[var(--outline-var)] dark:border-[var(--outline-var)] rounded-lg text-sm text-[var(--on-surface)]  transition-all outline-none" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor=''">
                    </div>

                    
                    <div class="flex items-center gap-2 ml-auto">
                        <?php if(array_filter($filters)): ?>
                        <a href="<?php echo e(route('orders.index')); ?>"
                           class="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg border border-[var(--outline-var)] dark:border-[var(--outline-var)] text-[var(--on-surface-var)] hover:text-[var(--on-surface)] dark:hover:text-[var(--on-surface)] hover:bg-[var(--surface-low)]  transition-all text-xs font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
                            Clear
                        </a>
                        <?php endif; ?>
                        <button type="submit"
                                class="h-9 px-5 inline-flex items-center gap-2 rounded-lg text-white text-xs font-bold transition-all hover:opacity-90"
                                style="background:linear-gradient(135deg,var(--primary),var(--secondary));box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                            <?php echo e(__('ui.orders.filter.apply', ['default' => 'Apply Filter'])); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="rounded-2xl border border-[var(--outline-var)] dark:border-[var(--outline-var)] bg-[var(--surface-lowest)]  shadow-sm overflow-hidden">

            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr style="background:linear-gradient(to right,color-mix(in srgb,var(--primary) 5%,transparent),transparent)">
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.number', ['default' => 'Order #'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.type', ['default' => 'Type'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.table', ['default' => 'Table'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.status', ['default' => 'Status'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.items', ['default' => 'Items'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.total', ['default' => 'Total'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                                <?php echo e(__('ui.orders.col.created', ['default' => 'Date'])); ?>

                            </th>
                            <th class="px-5 py-3.5 text-[10px] font-extrabold text-[var(--outline)] dark:text-[var(--on-surface-var)] uppercase tracking-widest border-b border-[var(--outline-var)] dark:border-[var(--outline-var)] text-right">
                                <?php echo e(__('ui.orders.col.actions', ['default' => 'Actions'])); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--outline-var)] /60">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="group transition-all duration-150" style="cursor:pointer"
                            onclick="window.location='<?php echo e(route('orders.show', $order)); ?>'"
                            onmouseenter="this.style.background='color-mix(in srgb,var(--primary) 5%,transparent)';this.style.boxShadow='inset 3px 0 0 var(--primary)'"
                            onmouseleave="this.style.background='';this.style.boxShadow=''">

                            
                            <td class="px-5 py-4">
                                <a href="<?php echo e(route('orders.show', $order)); ?>"
                                   class="inline-flex items-center gap-2 font-bold text-[var(--on-surface)]  transition-colors"
                                   style="" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color=''">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[11px] font-extrabold"
                                          style="background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                                        #
                                    </span>
                                    <span class="flex flex-col leading-tight">
                                        <span>
                                            <?php if($order->order_daily_number): ?>
                                                <?php echo e(__('ui.orders.col.number', ['default' => 'Order #'])); ?><?php echo e($order->order_daily_number); ?>

                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-[10px] font-mono font-normal text-[var(--outline)] dark:text-[var(--on-surface-var)]"><?php echo e($order->order_number); ?></span>
                                    </span>
                                </a>
                            </td>

                            
                            <td class="px-5 py-4">
                                <?php
                                    $typeIcons = ['dine_in' => 'restaurant', 'takeaway' => 'takeout_dining', 'delivery' => 'delivery_dining'];
                                    $typeIcon = $typeIcons[$order->order_type] ?? 'receipt_long';
                                ?>
                                <div class="inline-flex items-center gap-1.5 text-sm text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($typeIcon).'','class' => 'text-[15px] text-[var(--outline)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($typeIcon).'','class' => 'text-[15px] text-[var(--outline)]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                    <?php echo e(str($order->order_type)->replace('_', ' ')->title()); ?>

                                </div>
                            </td>

                            
                            <td class="px-5 py-4">
                                <?php if($order->restaurantTable): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold"
                                          style="background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-3 h-3"><path d="M80-160v-80h80v-560h560v560h80v80H80Zm240-240h80v-320h-80v320Zm160 0h80v-320h-80v320Z"/></svg>
                                        <?php echo e($order->restaurantTable->name); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-[var(--outline)] text-xs">—</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-5 py-4">
                                <?php if($order->status === 'paid'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide"
                                          style="background:color-mix(in srgb,var(--success) 10%,transparent 90%);color:var(--success)">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary-container)]0"></span>
                                        Paid
                                    </span>
                                <?php elseif($order->status === 'cancelled'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide"
                                          style="background:color-mix(in srgb,var(--error) 10%,transparent 90%);color:var(--error)">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--error)]"></span>
                                        Cancelled
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide"
                                          style="background:color-mix(in srgb,var(--warning) 10%,transparent 90%);color:var(--warning)">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--warning)] animate-pulse"></span>
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px] text-[var(--outline)]" ><path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                                    <?php echo e($order->items_count); ?>

                                </span>
                            </td>

                            
                            <td class="px-5 py-4">
                                <span class="text-sm font-extrabold text-[var(--on-surface)] ">
                                    <?php echo e(\App\Support\CurrencyFormatter::format($order->total)); ?>

                                </span>
                            </td>

                            
                            <td class="px-5 py-4 text-sm text-[var(--on-surface-var)] dark:text-[var(--outline)]">
                                <div class="flex flex-col">
                                    <span class="font-medium text-[var(--on-surface)] dark:text-[var(--on-surface-var)]"><?php echo e($order->created_at->format('M d, Y')); ?></span>
                                    <span class="text-[11px]"><?php echo e($order->created_at->format('g:i A')); ?></span>
                                </div>
                            </td>

                            
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                                    <?php if($order->status === 'paid'): ?>
                                    <a href="<?php echo e(route('orders.invoice', $order)); ?>" target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[var(--outline-var)] dark:border-[var(--outline-var)] text-[11px] font-bold text-[var(--on-surface-var)] dark:text-[var(--on-surface-var)] hover:bg-[var(--surface-low)]  hover:border-[var(--outline-var)] transition-all uppercase tracking-wider">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]" ><path d="M640-640v-120H320v120h-80v-200h480v200h-80Zm-480 80h640-640Zm560 100q17 0 28.5-11.5T760-500q0-17-11.5-28.5T720-540q-17 0-28.5 11.5T680-500q0 17 11.5 28.5T720-460Zm-80 260v-160H320v160h320Zm80 80H240v-160H80v-240q0-51 35-85.5t85-34.5h560q51 0 85.5 34.5T880-520v240H720v160Zm80-240v-160q0-17-11.5-28.5T760-560H200q-17 0-28.5 11.5T160-520v160h80v-80h480v80h80Z"/></svg>
                                        Print
                                    </a>
                                    <?php endif; ?>
                                    <button type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[var(--error-container)]  text-[11px] font-bold text-[var(--error)] dark:text-[var(--error)] hover:bg-[var(--error-container)]  transition-all uppercase tracking-wider"
                                            @click="deleteAction = '<?php echo e(route('orders.destroy', $order)); ?>'; deleteOrderNumber = '<?php echo e($order->order_number); ?>'; showDeleteModal = true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[13px]" ><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                        Delete
                                    </button>
                            </div>
                        </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                                         style="background:color-mix(in srgb,var(--primary) 10%,transparent)">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-4xl" style="color:var(--primary);font-variation-settings:'FILL' 0"><path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v40q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-[var(--on-surface-var)] dark:text-[var(--outline)]">
                                        <?php echo e(__('ui.orders.empty', ['default' => 'No orders found.'])); ?>

                                    </p>
                                    <p class="text-xs text-[var(--outline)] dark:text-[var(--on-surface-var)]">Try adjusting your filters to see more results.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($orders->hasPages()): ?>
            <div class="px-5 py-4 border-t border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                <?php echo e($orders->links()); ?>

            </div>
            <?php endif; ?>

        </div>

        
        <template x-teleport="body">
            <div x-cloak x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] flex items-center justify-center"
                 style="backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);background:rgba(0,0,0,.6)">
                <div class="absolute inset-0" @click="showDeleteModal = false"></div>
                <div x-show="showDeleteModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     class="relative rounded-2xl w-full max-w-sm mx-4 p-8 text-center"
                     style="background:var(--surface-lowest);border:1px solid color-mix(in srgb,var(--outline-var) 40%,transparent);box-shadow:0 32px 80px rgba(0,0,0,.5)">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
                         style="background:color-mix(in srgb,var(--error) 12%,transparent 88%)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-3xl text-[var(--error)]" style="font-variation-settings:'FILL' 1"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color:var(--on-surface)">
                        <?php echo e(__('ui.orders.delete_modal.title', ['default' => 'Delete Order'])); ?>

                    </h3>
                    <p class="text-sm mb-6" style="color:var(--on-surface-var)">
                        <?php echo e(__('ui.orders.delete_modal.message_prefix', ['default' => 'Are you sure you want to delete order'])); ?>

                        <strong x-text="deleteOrderNumber" class="font-mono" style="color:var(--on-surface)"></strong><?php echo e(__('ui.orders.delete_modal.question_mark', ['default' => '?'])); ?>

                        <br><span class="text-xs text-[var(--error)] font-medium">This action cannot be undone.</span>
                    </p>
                    <div class="flex gap-3">
                        <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all hover:opacity-80"
                                style="border-color:color-mix(in srgb,var(--outline-var) 60%,transparent);background:var(--surface-low);color:var(--on-surface-var)"
                                @click="showDeleteModal = false">
                            <?php echo e(__('ui.common.cancel', ['default' => 'Cancel'])); ?>

                        </button>
                        <form :action="deleteAction" method="POST" data-delete-confirm-skip="true" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-white text-sm transition-all hover:opacity-90 active:scale-95"
                                    style="background:linear-gradient(135deg,var(--error),var(--error));box-shadow:0 4px 16px color-mix(in srgb,var(--error) 35%,transparent 65%)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" style="font-variation-settings:'FILL' 1"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                <?php echo e(__('ui.common.delete', ['default' => 'Delete'])); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

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
<?php endif; ?><?php /**PATH /var/www/dots-main/resources/views/orders/index.blade.php ENDPATH**/ ?>