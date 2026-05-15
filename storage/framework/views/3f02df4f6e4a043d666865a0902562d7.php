<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Shift Logs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Shift Logs']); ?>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Shift Logs</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">Cashier shift history &amp; settlement records</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('reports.shift-logs.exportPdf', request()->query())); ?>" class="glass-button-secondary rounded-xl py-2 px-3 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z"/></svg>
                Export PDF
            </a>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('reports.shift-logs')); ?>"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-52">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">Cashier</label>
            <div class="relative">
                <select name="user_id" class="w-full glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                    <option value="">All Cashiers</option>
                    <?php $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cashier->id); ?>" <?php if((int) $filters['user_id'] === (int) $cashier->id): echo 'selected'; endif; ?>><?php echo e($cashier->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
            </div>
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">From</label>
            <input type="date" name="from" value="<?php echo e($filters['from']); ?>" class="w-full glass-input rounded-xl px-4 py-2 text-sm">
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--on-surface-var)">To</label>
            <input type="date" name="to" value="<?php echo e($filters['to']); ?>" class="w-full glass-input rounded-xl px-4 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2 pb-px">
            <button type="submit" class="glass-button-primary rounded-xl py-2 px-5 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/></svg>Filter
            </button>
            <a href="<?php echo e(route('reports.shift-logs')); ?>" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">Reset</a>
        </div>
    </form>

    
    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm font-medium" style="color:var(--on-surface-var)"><?php echo e($shiftLogs->total()); ?> shift logs</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        <?php $__currentLoopData = ['ID', 'Cashier', 'Shift Start', 'Shift End', 'Duration', 'Cash Diff', 'Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)"><?php echo e($h); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    <?php $__empty_1 = true; $__currentLoopData = $shiftLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-mono text-xs" style="color:var(--on-surface-var)">#<?php echo e($log->id); ?></td>
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                                      style="background-color:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary)">
                                    <?php echo e(mb_strtoupper(mb_substr($log->user?->name ?? '?', 0, 1))); ?>

                                </span>
                                <?php echo e($log->user?->name ?? '-'); ?>

                            </div>
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($log->shift_start?->format('Y-m-d g:i A') ?? '-'); ?></td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)"><?php echo e($log->shift_end?->format('Y-m-d g:i A') ?? '-'); ?></td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            <?php if($log->shift_start && $log->shift_end): ?>
                                <?php $mins = $log->shift_start->diffInMinutes($log->shift_end); ?>
                                <?php echo e(intdiv($mins, 60)); ?>h <?php echo e($mins % 60); ?>m
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <?php $diff = $log->cash_difference; ?>
                            <?php if($diff !== null): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    <?php echo e($diff == 0 ? '' : ($diff > 0 ? 'bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]' : 'bg-[color-mix(in_srgb,var(--error)_10%,transparent_90%)] text-[var(--error)] border border-[color-mix(in_srgb,var(--error)_20%,transparent_80%)]')); ?>"
                                    <?php if($diff == 0): ?> style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary);border:1px solid color-mix(in srgb,var(--primary) 20%,transparent)" <?php endif; ?>>
                                    <?php echo e($diff >= 0 ? '+' : ''); ?><?php echo e(\App\Support\CurrencyFormatter::format($diff)); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-xs" style="color:var(--on-surface-var)">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <?php if($canViewShiftLogProfile): ?>
                            <a href="<?php echo e(route('reports.shift-logs.profile', $log)); ?>" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[14px]" ><path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM214-281.5Q94-363 40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200q-146 0-266-81.5ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/></svg>View
                            </a>
                            <?php else: ?>
                            <span class="text-xs" style="color:var(--on-surface-var)">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] block mb-2" style="color:var(--outline)"><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/></svg>
                            <p class="text-sm font-medium" style="color:var(--on-surface-var)">No shift logs found</p>
                            <p class="text-xs mt-1" style="color:var(--outline)">Shift logs will appear here when cashiers open/close shifts</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($shiftLogs->hasPages()): ?>
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)"><?php echo e($shiftLogs->links()); ?></div>
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
<?php /**PATH /var/www/dots-main/resources/views/reports/shift-logs.blade.php ENDPATH**/ ?>