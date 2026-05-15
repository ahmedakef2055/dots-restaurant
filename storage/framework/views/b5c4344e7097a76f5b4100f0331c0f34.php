<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Offers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Offers']); ?>

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Offers & Promotions</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Manage promotional discounts and auto-apply rules</p>
        </div>
        <a href="<?php echo e(route('offers.create')); ?>" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>Create Offer
        </a>
    </div>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e($offers->total()); ?> offers total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <?php $__currentLoopData = ['Name','Type','Value','Priority','Status','Expires','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)"><?php echo e($h); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <?php $__empty_1 = true; $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-semibold" style="color:var(--primary)"><?php echo e(strtoupper($offer->name)); ?></td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e(ucfirst($offer->discount_type)); ?></td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--on-surface)">
                            <?php echo e($offer->discount_type === 'percentage'
                                ? rtrim(rtrim((string)$offer->discount_value,'0'),'.') . '%'
                                : \App\Support\CurrencyFormatter::format($offer->discount_value)); ?>

                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($offer->priority ?? '-'); ?></td>
                        <td class="px-5 py-3">
                            <?php if($offer->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)] animate-pulse"></span>Active
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                Inactive
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($offer->ends_at?->format('Y-m-d') ?? ($offer->end_date ?? '-')); ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('offers.show', $offer)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">View</a>
                                <a href="<?php echo e(route('offers.edit', $offer)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Edit</a>
                                <form method="POST" action="<?php echo e(route('offers.destroy', $offer)); ?>" class="inline-block">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">No offers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($offers->hasPages()): ?>
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"><?php echo e($offers->withQueryString()->links()); ?></div>
        <?php endif; ?>
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
<?php endif; ?>
<?php /**PATH /var/www/dots-main/resources/views/offers/index.blade.php ENDPATH**/ ?>