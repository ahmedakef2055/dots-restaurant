<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.navigation.dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.navigation.dashboard'))]); ?>

<?php
    $isArabic = app()->getLocale() === 'ar';
?>


<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="page-title"><?php echo e(__('ui.navigation.dashboard')); ?></h2>
        <p class="page-subtitle">
            <span class="lang-en-only"><?php echo e(__('ui.dashboard.subtitle', [], 'en')); ?> — <?php echo e(now()->locale('en')->translatedFormat('l, j M')); ?></span>
            <span class="lang-ar-only" dir="rtl"><?php echo e(__('ui.dashboard.subtitle', [], 'ar')); ?> — <?php echo e(now()->locale('ar')->translatedFormat('l, j M')); ?></span>
        </p>
    </div>
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        
        <div class="absolute ltr:-right-6 rtl:-left-6 -top-6 w-28 h-28 rounded-full blur-3xl opacity-20 transition-all duration-500"
             style="background-color:var(--<?php echo e($kpi['color'] ?? 'primary'); ?>-container)"></div>

        <div class="flex justify-between items-start mb-2 relative z-10">
            <div class="p-2.5 rounded-xl flex items-center justify-center"
                 style="background-color:color-mix(in srgb, var(--<?php echo e($kpi['color'] ?? 'primary'); ?>) 15%, transparent 85%); color:var(--<?php echo e($kpi['color'] ?? 'primary'); ?>)">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($kpi['icon'] ?? 'bar_chart').'','class' => 'text-[22px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($kpi['icon'] ?? 'bar_chart').'','class' => 'text-[22px]']); ?>
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
            </div>

            <?php if(isset($kpi['trendType']) && $kpi['trendType'] === 'positive'): ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="color:var(--<?php echo e($kpi['color'] ?? 'primary'); ?>); background-color:color-mix(in srgb, var(--<?php echo e($kpi['color'] ?? 'primary'); ?>) 15%, transparent 85%)">
                    <?php echo e($kpi['trend']); ?>

                </span>
            <?php elseif(isset($kpi['trendType']) && $kpi['trendType'] === 'negative'): ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="color:var(--error); background-color:color-mix(in srgb, var(--error) 15%, transparent 85%)">
                    <?php echo e($kpi['trend']); ?>

                </span>
            <?php else: ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="color:var(--on-surface-var); background-color:color-mix(in srgb, var(--surface-container) 80%, transparent 20%)">
                    <?php echo e($kpi['trend'] ?? ''); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="relative z-10">
            <p class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'text-[13px] font-semibold mb-1',
                'uppercase tracking-widest' => !$isArabic,
            ]); ?>" style="color:var(--on-surface-var)"><?php echo e($kpi['label']); ?></p>
            <p class="text-3xl font-extrabold tracking-tight" style="color:var(--on-surface)"><?php echo e($kpi['value']); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">

    
    <div class="chart-card p-6 h-80">
        <div class="flex justify-between items-center mb-5">
            <div>
                <h3 class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.charts.sales_trend')); ?></h3>
                <p class="text-xs mt-0.5" style="color:var(--on-surface-var)"><?php echo e(__('ui.dashboard.charts.last_7_days')); ?></p>
            </div>
        </div>
        <div class="flex-1 h-52">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    
    <div class="chart-card p-6 h-80">
        <div class="flex justify-between items-center mb-5">
            <div>
                <h3 class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.charts.orders_per_hour')); ?></h3>
                <p class="text-xs mt-0.5" style="color:var(--on-surface-var)"><?php echo e(__('ui.dashboard.charts.today_distribution')); ?></p>
            </div>

        </div>
        <div class="flex-1 h-52">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    
    <div class="chart-card xl:col-span-2 overflow-hidden flex flex-col">
        <div class="flex justify-between items-center px-6 py-4"
             style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent 70%)">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.title')); ?></h3>
            <a href="<?php echo e(route('orders.index')); ?>"
               class="flex items-center gap-1 text-sm font-medium transition-colors hover:opacity-80"
               style="color:var(--primary)">
                <?php echo e(__('ui.dashboard.recent_orders.view_all')); ?>

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[15px]" ><?php echo $isArabic ? '<path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>' : '<path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>'; ?></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" style="color:var(--on-surface-var)">
                <thead style="background-color:color-mix(in srgb,var(--surface-container) 40%,transparent 60%);
                              border-bottom:1px solid color-mix(in srgb,var(--outline-var) 20%,transparent 80%)">
                    <tr>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.order_number')); ?></th>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.type')); ?></th>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.table')); ?></th>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.status')); ?></th>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.amount')); ?></th>
                        <th class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-6 py-3 text-left text-xs font-semibold',
                            'uppercase tracking-wide' => !$isArabic,
                        ]); ?>" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.recent_orders.headers.time')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="transition-colors group"
                        style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 15%,transparent 85%)"
                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--surface-container) 30%,transparent 70%)'"
                        onmouseleave="this.style.backgroundColor='transparent'">
                        <td class="px-6 py-3.5 font-semibold" style="color:var(--on-surface)"><?php echo e($order['number']); ?></td>
                        <td class="px-6 py-3.5"><?php echo e($order['type']); ?></td>
                        <td class="px-6 py-3.5"><?php echo e($order['table']); ?></td>
                        <td class="px-6 py-3.5">
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'badge-ok'      => ($order['status_key'] ?? null) === 'completed',
                                'badge-info'    => ($order['status_key'] ?? null) === 'preparing',
                                'badge-warn'    => ($order['status_key'] ?? null) === 'out_for_delivery',
                                'badge-neutral' => ($order['status_key'] ?? null) === 'pending',
                                'badge-danger'  => ($order['status_key'] ?? null) === 'cancelled',
                            ]); ?>"><?php echo e($order['status']); ?></span>
                        </td>
                        <td class="px-6 py-3.5 font-medium" style="color:var(--on-surface)"><?php echo e($order['amount']); ?></td>
                        <td class="px-6 py-3.5"><?php echo e($order['time']); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="chart-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-base font-semibold" style="color:var(--on-surface)"><?php echo e(__('ui.dashboard.top_products.title')); ?></h3>
        </div>

        <div class="flex flex-col gap-3 flex-1">
            <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3 p-2 rounded-xl transition-colors cursor-pointer"
                 onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--surface-container) 40%,transparent 60%)'"
                 onmouseleave="this.style.backgroundColor='transparent'">
                <div class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0 text-lg font-bold"
                     style="background-color:color-mix(in srgb,var(--primary) 10%,var(--surface-low) 90%);
                            color:var(--primary)">
                    <?php echo e(mb_substr($product['name'], 0, 1)); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color:var(--on-surface)"><?php echo e($product['name']); ?></p>
                    <p class="text-xs" style="color:var(--on-surface-var)"><?php echo e(__('ui.dashboard.top_products.orders_count', ['count' => $product['sold']])); ?></p>
                </div>
                <p class="text-sm font-semibold shrink-0" style="color:var(--primary)"><?php echo e($product['revenue']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<script id="dashboard-chart-data" type="application/json">
    <?php echo json_encode($chartData, 15, 512) ?>
</script>

<script>
    // Refresh the dashboard every 30 seconds to keep data real-time
    setTimeout(function() {
        window.location.reload();
    }, 30000);
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
<?php endif; ?><?php /**PATH /var/www/dots-main/resources/views/dashboard.blade.php ENDPATH**/ ?>