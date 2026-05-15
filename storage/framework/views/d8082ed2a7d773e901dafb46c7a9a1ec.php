<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Customers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Customers']); ?>
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Customers</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">CRM & customer management</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('customers.pdf', request()->query())); ?>" target="_blank" class="glass-button-secondary rounded-xl py-2 px-3 sm:px-4 text-sm font-medium flex items-center gap-2" title="Export PDF">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]"><path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg><span class="hidden sm:inline">Export PDF</span>
            </a>
            <a href="<?php echo e(route('customers.create')); ?>" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q440-607 440-640t-23.5-56.5Q393-720 360-720t-56.5 23.5Q280-673 280-640t23.5 56.5Q327-560 360-560t56.5-23.5ZM360-640Zm0 400Z"/></svg>Add Customer
            </a>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('customers.index')); ?>"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input type="text" name="q" value="<?php echo e($filters['q']); ?>" placeholder="Search customers..."
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <button type="submit" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Search</button>
        <?php if($filters['q']): ?>
        <a href="<?php echo e(route('customers.index')); ?>" class="glass-button-secondary rounded-xl py-2 px-3 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
        </a>
        <?php endif; ?>
    </form>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)"><?php echo e($customers->total()); ?> customers total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <?php $__currentLoopData = ['Name','Phone','Type','Orders','Total Spent','Last Order','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)"><?php echo e($h); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)"><?php echo e($customer->full_name); ?></td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($customer->phone); ?></td>
                        <td class="px-5 py-3">
                            <?php if(($customer->customer_type ?? 'normal') === 'vip'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--tertiary) 30%,var(--surface-lowest) 70%);border:1px solid color-mix(in srgb,var(--tertiary) 50%,transparent 50%);color:var(--on-surface)">VIP</span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--surface-low);border:1px solid color-mix(in srgb,var(--outline) 30%,transparent 70%);color:var(--on-surface-var)">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e((int)($customer->orders_count ?? 0)); ?></td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--on-surface)"><?php echo e(\App\Support\CurrencyFormatter::format((float)($customer->total_spent ?? 0))); ?></td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($customer->last_order_at ? \Illuminate\Support\Carbon::parse($customer->last_order_at)->format('Y-m-d') : '-'); ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('customers.show', $customer)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Profile</a>
                                <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">Edit</a>
                                <form method="POST" action="<?php echo e(route('customers.destroy', $customer)); ?>" class="inline-block">
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
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">No customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($customers->hasPages()): ?>
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"><?php echo e($customers->withQueryString()->links()); ?></div>
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
<?php /**PATH /var/www/dots-main/resources/views/customers/index.blade.php ENDPATH**/ ?>