<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.printers.title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.printers.title'))]); ?>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)"><?php echo e(__('ui.printers.title')); ?></h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)"><?php echo e(__('ui.printers.subtitle')); ?></p>
        </div>
        <a href="<?php echo e(route('printers.create')); ?>" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2 self-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <?php echo e(__('ui.printers.add')); ?>

        </a>
    </div>

    <?php if (isset($component)) { $__componentOriginal62dc05388dc11911998747c2ba8276e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal62dc05388dc11911998747c2ba8276e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.flash-toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.flash-toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal62dc05388dc11911998747c2ba8276e7)): ?>
<?php $attributes = $__attributesOriginal62dc05388dc11911998747c2ba8276e7; ?>
<?php unset($__attributesOriginal62dc05388dc11911998747c2ba8276e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal62dc05388dc11911998747c2ba8276e7)): ?>
<?php $component = $__componentOriginal62dc05388dc11911998747c2ba8276e7; ?>
<?php unset($__componentOriginal62dc05388dc11911998747c2ba8276e7); ?>
<?php endif; ?>

    
    <?php $unassigned = array_diff(array_keys($allJobs), $assignedJobs); ?>
    <?php if(count($unassigned) > 0): ?>
    <div class="mb-5 rounded-xl border border-[var(--warning-container)] bg-[var(--warning-container)] px-4 py-3 text-sm text-[var(--warning)] flex items-start gap-2   ">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
        <div>
            <span class="font-semibold"><?php echo e(__('ui.printers.unassigned_jobs')); ?>:</span>
            <?php $__currentLoopData = $unassigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="mx-1 inline-flex items-center rounded-full bg-[var(--warning-container)] px-2 py-0.5 text-xs font-medium text-[var(--warning)]  "><?php echo e($allJobs[$job]['label']); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($printers->isEmpty()): ?>
    <div class="glass-panel rounded-2xl flex flex-col items-center justify-center py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--on-surface-var);opacity:.3">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
        </svg>
        <p class="font-medium" style="color:var(--on-surface-var)"><?php echo e(__('ui.printers.no_printers')); ?></p>
        <a href="<?php echo e(route('printers.create')); ?>" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium mt-4 inline-flex items-center gap-2">
            <?php echo e(__('ui.printers.add')); ?>

        </a>
    </div>
    <?php else: ?>
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-start" style="color:var(--on-surface-var)">الطابعة</th>
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-start" style="color:var(--on-surface-var)">اسم Windows</th>
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-start" style="color:var(--on-surface-var)">المهام</th>
                    <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-start" style="color:var(--on-surface-var)"><?php echo e(__('ui.common.actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $printers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $printer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b transition hover:bg-[var(--surface-lowest)]" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'h-2 w-2 rounded-full flex-shrink-0',
                                'bg-[var(--success-container)]' => $printer->is_active,
                                'bg-[var(--outline-var)]'  => !$printer->is_active,
                            ]); ?>"></div>
                            <span class="font-medium" style="color:var(--on-surface)"><?php echo e($printer->name); ?></span>
                            <?php if(!$printer->is_active): ?>
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-[var(--surface-lowest)]">معطلة</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <code class="rounded bg-black/5 dark:bg-[var(--surface-lowest)] px-2 py-0.5 font-mono text-xs" style="color:var(--on-surface)">
                                <?php echo e($printer->printer_name ?: '—'); ?>

                            </code>
                            <span class="text-xs" style="color:var(--on-surface-var)"><?php echo e($printer->paper_width); ?>mm</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-wrap gap-1">
                            <?php $__empty_1 = true; $__currentLoopData = $printer->handles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span class="inline-flex items-center rounded-full bg-[var(--primary-container)]  px-2.5 py-0.5 text-xs font-medium text-[var(--primary)] ">
                                    <?php echo e($allJobs[$job]['label'] ?? $job); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span class="text-xs italic" style="color:var(--on-surface-var)">لا مهام</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('printers.edit', $printer)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">
                                تعديل
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

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
<?php /**PATH /var/www/dots-main/resources/views/printers/index.blade.php ENDPATH**/ ?>