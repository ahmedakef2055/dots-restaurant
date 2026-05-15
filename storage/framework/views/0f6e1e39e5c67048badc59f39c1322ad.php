<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.financial.title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.financial.title'))]); ?>

<?php $isArabic = app()->getLocale() === 'ar'; ?>


<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="page-title"><?php echo e(__('ui.financial.title')); ?></h2>
        <p class="page-subtitle" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.subtitle')); ?></p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        
        <a href="<?php echo e(route('financial.export.pdf', request()->query())); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
           style="background:color-mix(in srgb,var(--error) 12%,transparent 88%);color:var(--error);border:1px solid color-mix(in srgb,var(--error) 25%,transparent)">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'picture_as_pdf','class' => 'text-base']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'picture_as_pdf','class' => 'text-base']); ?>
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
            <?php echo e(__('ui.financial.actions.export_pdf')); ?>

        </a>
        
        <a href="<?php echo e(route('financial.export.excel', request()->query())); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
           style="background:color-mix(in srgb,var(--tertiary) 12%,transparent 88%);color:var(--tertiary);border:1px solid color-mix(in srgb,var(--tertiary) 25%,transparent)">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'table_view','class' => 'text-base']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'table_view','class' => 'text-base']); ?>
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
            <?php echo e(__('ui.financial.actions.export_excel')); ?>

        </a>
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
            <?php if(isset($kpi['profit_sign'])): ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="color:var(--<?php echo e($kpi['profit_sign'] === 'positive' ? 'tertiary' : 'error'); ?>);
                             background-color:color-mix(in srgb,var(--<?php echo e($kpi['profit_sign'] === 'positive' ? 'tertiary' : 'error'); ?>) 15%,transparent 85%)">
                    <?php echo e($kpi['profit_sign'] === 'positive' ? '▲' : '▼'); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="relative z-10">
            <p class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-[13px] font-semibold mb-1','uppercase tracking-widest' => !$isArabic]); ?>"
               style="color:var(--on-surface-var)"><?php echo e($kpi['label']); ?></p>
            <p class="text-3xl font-extrabold tracking-tight" style="color:var(--on-surface)"><?php echo e($kpi['value']); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="chart-card p-5 mb-6">
    <form method="GET" action="<?php echo e(route('financial.index')); ?>" id="fin-filter-form">
        <input type="hidden" name="period" id="fin-period" value="<?php echo e($period); ?>">
        <div class="flex flex-wrap items-end gap-3">

            
            <div class="flex items-center gap-1 p-1 rounded-xl" style="background:color-mix(in srgb,var(--surface-container) 60%,transparent)">
                <?php $__currentLoopData = ['today','week','month']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                        onclick="document.getElementById('fin-from').value='';document.getElementById('fin-to').value='';document.getElementById('fin-period').value='<?php echo e($p); ?>';document.getElementById('fin-filter-form').submit();"
                        class="<?php echo \Illuminate\Support\Arr::toCssClasses(['fin-period-btn', 'active' => $period === $p]); ?>">
                    <?php echo e(__('ui.financial.filters.'.$p)); ?>

                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="flex flex-col gap-0.5" id="fin-month-wrap" style="<?php echo e($period !== 'month' ? 'display:none' : ''); ?>">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.filters.month')); ?></label>
                <input type="month" name="month_year" id="fin-month-year"
                       value="<?php echo e($monthYear ?? now()->format('Y-m')); ?>"
                       class="fin-input"
                       onchange="document.getElementById('fin-period').value='month';document.getElementById('fin-from').value='';document.getElementById('fin-to').value='';document.getElementById('fin-filter-form').submit()">
            </div>
            <script>
            (function(){
                var wrap = document.getElementById('fin-month-wrap');
                document.querySelectorAll('.fin-period-btn').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var p = btn.getAttribute('onclick')||'';
                        var isMonth = p.indexOf("'month'") !== -1;
                        if(wrap) wrap.style.display = isMonth ? '' : 'none';
                    });
                });
            })();
            </script>

            
            <input type="hidden" name="from" id="fin-from" value="">
            <input type="hidden" name="to" id="fin-to" value="">

            
            <div class="flex flex-col gap-0.5">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.filters.payment_method')); ?></label>
                <select name="payment_method" class="fin-input" onchange="this.form.submit()">
                    <option value=""><?php echo e(__('ui.financial.filters.all_methods')); ?></option>
                    <?php $__currentLoopData = ['cash','card','credit','bank_transfer','wallet','visa','instapay']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php if($paymentMethodFilter === $m): echo 'selected'; endif; ?>><?php echo e(__('ui.financial.filters.'.$m)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="flex flex-col gap-0.5">
                <label class="text-xs font-medium" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.filters.type')); ?></label>
                <select name="type" class="fin-input" onchange="this.form.submit()">
                    <option value=""><?php echo e(__('ui.financial.filters.all_types')); ?></option>
                    <?php $__currentLoopData = ['order','purchase','salary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($t); ?>" <?php if($typeFilter === $t): echo 'selected'; endif; ?>><?php echo e(__('ui.financial.filters.'.$t)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <a href="<?php echo e(route('financial.index')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all self-end"
               style="color:var(--on-surface-var);background:color-mix(in srgb,var(--surface-container) 50%,transparent)">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'refresh','class' => 'text-base']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'refresh','class' => 'text-base']); ?>
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
                <?php echo e(__('ui.financial.filters.reset')); ?>

            </a>
        </div>
    </form>
</div>


<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <?php
        $formatter = \App\Support\CurrencyFormatter::class;
    ?>
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,var(--tertiary) 10%,var(--surface-low) 90%);border:1px solid color-mix(in srgb,var(--tertiary) 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,var(--tertiary) 20%,transparent);color:var(--tertiary)">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow_upward','class' => 'text-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow_upward','class' => 'text-xl']); ?>
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
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.summary.total_income')); ?></p>
            <p class="text-xl font-extrabold" style="color:var(--tertiary)" dir="ltr"><?php echo e($formatter::format($totalRevenue)); ?></p>
        </div>
    </div>
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,var(--error) 10%,var(--surface-low) 90%);border:1px solid color-mix(in srgb,var(--error) 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow_downward','class' => 'text-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow_downward','class' => 'text-xl']); ?>
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
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.summary.total_expenses')); ?></p>
            <p class="text-xl font-extrabold" style="color:var(--error)" dir="ltr"><?php echo e($formatter::format($totalExpenses)); ?></p>
        </div>
    </div>
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:color-mix(in srgb,<?php echo e($netProfit >= 0 ? 'var(--primary)' : 'var(--error)'); ?> 10%,var(--surface-low) 90%);
                border:1px solid color-mix(in srgb,<?php echo e($netProfit >= 0 ? 'var(--primary)' : 'var(--error)'); ?> 20%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             style="background:color-mix(in srgb,<?php echo e($netProfit >= 0 ? 'var(--primary)' : 'var(--error)'); ?> 20%,transparent);
                    color:<?php echo e($netProfit >= 0 ? 'var(--primary)' : 'var(--error)'); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($netProfit >= 0 ? 'trending_up' : 'trending_down').'','class' => 'text-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($netProfit >= 0 ? 'trending_up' : 'trending_down').'','class' => 'text-xl']); ?>
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
        <div>
            <p class="text-xs font-semibold" style="color:var(--on-surface-var)"><?php echo e(__('ui.financial.summary.net')); ?></p>
            <p class="text-xl font-extrabold"
               style="color:<?php echo e($netProfit >= 0 ? 'var(--primary)' : 'var(--error)'); ?>" dir="ltr">
                <?php echo e($formatter::format($netProfit)); ?>

            </p>
        </div>
    </div>
</div>


<div class="chart-card overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4"
         style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent 70%)">
        <div>
            <h3 class="text-base font-semibold" style="color:var(--on-surface)">
                <?php echo e($isArabic ? 'سجل المعاملات المالية' : 'Financial Transactions'); ?>

            </h3>
            <p class="text-xs mt-0.5" style="color:var(--on-surface-var)">
                <?php echo e($transactions->count()); ?> <?php echo e($isArabic ? 'معاملة' : 'transactions'); ?>

                &nbsp;·&nbsp; <?php echo e($from); ?> → <?php echo e($to); ?>

            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="color:var(--on-surface-var)">
            <thead style="background:color-mix(in srgb,var(--surface-container) 40%,transparent);
                          border-bottom:1px solid color-mix(in srgb,var(--outline-var) 20%,transparent)">
                <tr>
                    <?php $__currentLoopData = ['type','reference','description','payment_method','amount','remaining','actor','status','date']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="px-4 py-3 text-start text-xs font-semibold <?php if(!$isArabic): ?> uppercase tracking-wide <?php endif; ?>"
                        style="color:var(--on-surface)">
                        <?php echo e(__('ui.financial.table.'.$col)); ?>

                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="transition-colors"
                    style="border-bottom:1px solid color-mix(in srgb,var(--outline-var) 10%,transparent)"
                    onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--surface-container) 30%,transparent)'"
                    onmouseleave="this.style.backgroundColor='transparent'">

                    
                    <td class="px-4 py-3">
                        <span class="fin-type-badge fin-type-<?php echo e($row['type']); ?>">
                            <?php echo e($row['type_label']); ?>

                        </span>
                    </td>

                    
                    <td class="px-4 py-3 font-semibold tabular-nums" style="color:var(--on-surface)" dir="ltr">
                        <?php echo e($row['reference']); ?>

                    </td>

                    
                    <td class="px-4 py-3 max-w-[180px] truncate"><?php echo e($row['description']); ?></td>

                    
                    <td class="px-4 py-3">
                        <span class="fin-pay-badge fin-pay-<?php echo e($row['payment_method']); ?>">
                            <?php echo e($row['payment_method_label']); ?>

                        </span>
                    </td>

                    
                    <td class="px-4 py-3 font-bold tabular-nums"
                        style="color:<?php echo e($row['category'] === 'income' ? 'var(--tertiary)' : 'var(--error)'); ?>" dir="ltr">
                        <?php echo e($row['category'] === 'income' ? '+' : '−'); ?><?php echo e($row['amount']); ?>

                    </td>

                    
                    <td class="px-4 py-3 tabular-nums" style="color:var(--error)">
                        <?php echo e($row['remaining'] ?? '—'); ?>

                    </td>

                    
                    <td class="px-4 py-3 text-xs"><?php echo e($row['actor']); ?></td>

                    
                    <td class="px-4 py-3">
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'badge-ok'      => $row['status'] === 'paid',
                            'badge-warn'    => $row['status'] === 'partial',
                            'badge-neutral' => !in_array($row['status'],['paid','partial']),
                        ]); ?>"><?php echo e($row['status_label']); ?></span>
                    </td>

                    
                    <td class="px-4 py-3 text-xs tabular-nums" dir="ltr"><?php echo e($row['date']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center" style="color:var(--on-surface-var)">
                        <div class="flex flex-col items-center gap-3">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'receipt_long','class' => 'text-5xl opacity-30']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'receipt_long','class' => 'text-5xl opacity-30']); ?>
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
                            <p class="text-sm"><?php echo e(__('ui.financial.table.no_results')); ?></p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<style>
/* Period buttons */
.fin-period-btn {
    padding: 6px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--on-surface-var);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all .18s;
}
.fin-period-btn.active,
.fin-period-btn:hover {
    background: var(--primary);
    color: var(--on-primary);
}

/* Inputs */
.fin-input {
    padding: 7px 10px;
    border-radius: 10px;
    font-size: 13px;
    background: color-mix(in srgb, var(--surface-container) 60%, transparent);
    color: var(--on-surface);
    border: 1px solid color-mix(in srgb, var(--outline-var) 40%, transparent);
    outline: none;
    transition: border-color .15s;
}
.fin-input:focus { border-color: var(--primary); }

/* Type badges */
.fin-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.fin-type-order    { background: color-mix(in srgb,var(--success) 15%,transparent); color: var(--success); border: 1px solid color-mix(in srgb,var(--success) 35%,transparent); }
.fin-type-purchase { background: color-mix(in srgb,var(--error) 15%,transparent); color: var(--error); border: 1px solid color-mix(in srgb,var(--error) 35%,transparent); }
.fin-type-salary   { background: color-mix(in srgb,var(--warning) 15%,transparent); color: var(--warning); border: 1px solid color-mix(in srgb,var(--warning) 35%,transparent); }

/* Payment method badges */
.fin-pay-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.fin-pay-cash          { background: color-mix(in srgb,var(--success) 12%,transparent); color: var(--success); border: 1px solid color-mix(in srgb,var(--success) 35%,transparent); }
.fin-pay-card          { background: color-mix(in srgb,var(--primary) 12%,transparent); color: var(--primary); border: 1px solid color-mix(in srgb,var(--primary) 35%,transparent); }
.fin-pay-credit        { background: color-mix(in srgb,var(--warning) 12%,transparent); color: var(--warning); border: 1px solid color-mix(in srgb,var(--warning) 35%,transparent); }
.fin-pay-bank_transfer { background: color-mix(in srgb,var(--secondary) 20%,transparent); color: var(--on-surface-var); border: 1px solid color-mix(in srgb,var(--secondary) 50%,transparent); }
.fin-pay-wallet        { background: color-mix(in srgb,var(--accent-gold) 15%,transparent); color: var(--on-surface-var); border: 1px solid color-mix(in srgb,var(--accent-gold) 40%,transparent); }
.fin-pay-visa          { background: color-mix(in srgb,var(--primary) 12%,transparent); color: var(--primary); border: 1px solid color-mix(in srgb,var(--primary) 35%,transparent); }
.fin-pay-instapay      { background: color-mix(in srgb,var(--error) 12%,transparent); color: var(--error); border: 1px solid color-mix(in srgb,var(--error) 35%,transparent); }

@media print {
    .page-header, nav, aside, .fin-period-btn, form { display: none !important; }
}
</style>

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
<?php /**PATH /var/www/dots-main/resources/views/financial/index.blade.php ENDPATH**/ ?>