<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('ui.suppliers.title', [], 'Suppliers')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.suppliers.title', [], 'Suppliers'))]); ?>

<?php
$totalSuppliers  = $suppliers->total();
$activeCount     = \App\Models\Supplier::where('is_active', true)->count();
$totalPurchases  = (int) \Illuminate\Support\Facades\DB::table('purchases')->count();
?>


<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">Suppliers Directory</h1>
    <p class="text-sm mt-1 max-w-2xl" style="color:var(--on-surface-var)">
        Manage vendor relationships, monitor active orders, and track total expenditures across all registered suppliers.
    </p>
</div>


<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

    
    <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group transition-shadow duration-500"
         style="box-shadow:0 0 30px color-mix(in srgb,var(--primary) 2%,transparent 98%)">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[80px]" style="color:var(--primary)"><path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border"
                     style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 20%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary);font-variation-settings:'FILL' 1"><path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/></svg>
                </div>
                <h3 class="text-sm font-medium" style="color:var(--on-surface-var)">Total Suppliers</h3>
            </div>
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-bold tracking-tight" style="color:var(--on-surface)"><?php echo e($totalSuppliers); ?></span>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full border"
                      style="background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border-color:color-mix(in srgb,var(--secondary) 20%,transparent);color:var(--secondary)">
                    In directory
                </span>
            </div>
        </div>
    </div>

    
    <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group transition-shadow duration-500"
         style="box-shadow:0 0 30px color-mix(in srgb,var(--primary) 2%,transparent 98%)">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[80px] text-[var(--success)]" ><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px] text-[var(--success)]" style="font-variation-settings:'FILL' 1"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                </div>
                <h3 class="text-sm font-medium" style="color:var(--on-surface-var)">Active Suppliers</h3>
            </div>
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-bold tracking-tight" style="color:var(--on-surface)"><?php echo e($activeCount); ?></span>
                <span class="text-xs font-medium text-[var(--success)]">Currently active</span>
            </div>
        </div>
    </div>

    
    <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group transition-shadow duration-500"
         style="box-shadow:0 0 30px color-mix(in srgb,var(--primary) 2%,transparent 98%)">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[80px]" style="color:var(--tertiary)"><path d="M440-183v-274L200-596v274l240 139Zm80 0 240-139v-274L520-457v274Zm-80 92L160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v318q0 22-10.5 40T800-252L520-91q-19 11-40 11t-40-11Zm200-528 77-44-237-137-78 45 238 136Zm-160 93 78-45-237-137-78 45 237 137Z"/></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border"
                     style="background-color:color-mix(in srgb,var(--tertiary) 10%,transparent);border-color:color-mix(in srgb,var(--tertiary) 20%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--tertiary);font-variation-settings:'FILL' 1"><path d="M440-183v-274L200-596v274l240 139Zm80 0 240-139v-274L520-457v274Zm-80 92L160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v318q0 22-10.5 40T800-252L520-91q-19 11-40 11t-40-11Zm200-528 77-44-237-137-78 45 238 136Zm-160 93 78-45-237-137-78 45 237 137Z"/></svg>
                </div>
                <h3 class="text-sm font-medium" style="color:var(--on-surface-var)">Total Purchase Orders</h3>
            </div>
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-bold tracking-tight" style="color:var(--on-surface)"><?php echo e($totalPurchases); ?></span>
                <span class="text-xs font-medium" style="color:var(--on-surface-var)">All time</span>
            </div>
        </div>
    </div>

</div>


<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-5">

    
    <form method="GET" action="<?php echo e(route('suppliers.index')); ?>" class="flex items-center gap-3 w-full sm:w-auto">
        <div class="relative flex-1 sm:w-80 group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors"
                 style="color:var(--on-surface-var)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" ><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </div>
            <input name="q" value="<?php echo e($filters['q']); ?>"
                   placeholder="Search suppliers, contacts..."
                   class="w-full glass-input rounded-xl pl-10 pr-4 py-2.5 text-sm">
        </div>
        <?php if($filters['q']): ?>
        <a href="<?php echo e(route('suppliers.index')); ?>"
           class="glass-button-secondary rounded-xl px-3 py-2.5 text-sm font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[16px]" ><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            Clear
        </a>
        <?php endif; ?>
    </form>

    <div class="flex items-center gap-2 w-full sm:w-auto">
        <a href="<?php echo e(route('suppliers.pdf', request()->query())); ?>" target="_blank"
           class="glass-button-secondary w-full sm:w-auto rounded-xl px-4 py-2.5 text-sm font-medium flex items-center justify-center gap-2" title="Export PDF">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]"><path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg>
            <span class="hidden sm:inline">Export PDF</span>
        </a>
        <a href="<?php echo e(route('suppliers.create')); ?>"
           class="glass-button-primary w-full sm:w-auto rounded-xl px-5 py-2.5 text-sm font-medium flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" style="font-variation-settings:'FILL' 1"><path d="M720-40v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM80-160v-240H40v-80l40-200h600l40 200v80h-40v120h-80v-120H440v240H80Zm80-80h200v-160H160v160Zm-38-240h516-516ZM80-720v-80h600v80H80Zm42 240h516l-24-120H146l-24 120Z"/></svg>
            Add Supplier
        </a>
    </div>
</div>


<div class="glass-panel-elevated rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr style="border-bottom:1px solid color-mix(in srgb,var(--primary) 6%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent)">
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">Supplier Name</th>
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">Primary Contact</th>
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">Location</th>
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-right" style="color:var(--on-surface-var)">Purchases</th>
                    <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-center" style="color:var(--on-surface-var)">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                $initials = collect(explode(' ', trim($supplier->name)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                $location = trim(($supplier->city ?: '') . ' ' . ($supplier->country ?: ''));
                ?>
                <tr class="transition-colors group"
                    onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                    onmouseleave="this.style.backgroundColor=''"
                    style="<?php echo e(!$supplier->is_active ? 'opacity:0.75' : ''); ?>">

                    
                    <td class="py-4 px-6 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center border shrink-0 text-sm font-bold"
                                 style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border-color:color-mix(in srgb,var(--primary) 15%,transparent);color:var(--primary)">
                                <?php echo e($initials); ?>

                            </div>
                            <div>
                                <a href="<?php echo e(route('suppliers.show', $supplier)); ?>"
                                   class="font-medium text-sm transition-colors hover:underline"
                                   style="color:var(--on-surface)"
                                   onmouseenter="this.style.color='var(--primary)'"
                                   onmouseleave="this.style.color='var(--on-surface)'">
                                    <?php echo e($supplier->name); ?>

                                </a>
                                <div class="text-xs mt-0.5" style="color:var(--on-surface-var)">ID #<?php echo e($supplier->id); ?></div>
                            </div>
                        </div>
                    </td>

                    
                    <td class="py-4 px-6 whitespace-nowrap">
                        <div class="text-sm" style="color:var(--on-surface)"><?php echo e($supplier->contact_person ?: '-'); ?></div>
                        <?php if($supplier->email || $supplier->phone): ?>
                        <div class="text-xs flex items-center gap-1 mt-0.5" style="color:var(--on-surface-var)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[12px]" ><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg>
                            <?php echo e($supplier->email ?: $supplier->phone ?: '-'); ?>

                        </div>
                        <?php endif; ?>
                    </td>

                    
                    <td class="py-4 px-6 whitespace-nowrap text-sm" style="color:var(--on-surface-var)">
                        <?php echo e($location ?: '-'); ?>

                    </td>

                    
                    <td class="py-4 px-6 whitespace-nowrap">
                        <?php if($supplier->is_active): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                              style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary);box-shadow:0 0 10px color-mix(in srgb,var(--primary) 10%,transparent)">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color:var(--primary)"></span>
                            Active
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                              style="background-color:color-mix(in srgb,var(--outline-var) 12%,transparent);border:1px solid color-mix(in srgb,var(--outline-var) 30%,transparent);color:var(--on-surface-var)">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--outline-var)"></span>
                            Inactive
                        </span>
                        <?php endif; ?>
                    </td>

                    
                    <td class="py-4 px-6 whitespace-nowrap text-right font-medium text-sm" style="color:var(--on-surface)">
                        <?php echo e($supplier->purchases_count); ?>

                    </td>

                    
                    <td class="py-4 px-6 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="<?php echo e(route('suppliers.show', $supplier)); ?>"
                               class="p-1.5 rounded-lg border border-transparent transition-colors"
                               style="color:var(--on-surface-var)"
                               title="View Profile"
                               onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 10%,transparent)';this.style.borderColor='color-mix(in srgb,var(--primary) 20%,transparent)';this.style.color='var(--primary)'"
                               onmouseleave="this.style.backgroundColor='';this.style.borderColor='transparent';this.style.color='var(--on-surface-var)'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>
                            </a>
                            <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>"
                               class="p-1.5 rounded-lg border border-transparent transition-colors"
                               style="color:var(--on-surface-var)"
                               title="Edit"
                               onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--primary) 10%,transparent)';this.style.borderColor='color-mix(in srgb,var(--primary) 20%,transparent)';this.style.color='var(--primary)'"
                               onmouseleave="this.style.backgroundColor='';this.style.borderColor='transparent';this.style.color='var(--on-surface-var)'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>
                            </a>
                            <form method="POST" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" class="inline-block">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        class="p-1.5 rounded-lg border border-transparent transition-colors"
                                        style="color:var(--on-surface-var)"
                                        title="Delete"
                                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 10%,transparent)';this.style.borderColor='color-mix(in srgb,var(--error) 20%,transparent)';this.style.color='var(--error)'"
                                        onmouseleave="this.style.backgroundColor='';this.style.borderColor='transparent';this.style.color='var(--on-surface-var)'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="py-12 text-center text-sm" style="color:var(--on-surface-var)">
                        <div class="flex flex-col items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[40px] opacity-30" ><path d="M155-195q-35-35-35-85H40v-440q0-33 23.5-56.5T120-800h560v160h120l120 160v200h-80q0 50-35 85t-85 35q-50 0-85-35t-35-85H360q0 50-35 85t-85 35q-50 0-85-35Zm113.5-56.5Q280-263 280-280t-11.5-28.5Q257-320 240-320t-28.5 11.5Q200-297 200-280t11.5 28.5Q223-240 240-240t28.5-11.5ZM120-360h32q17-18 39-29t49-11q27 0 49 11t39 29h272v-360H120v360Zm628.5 108.5Q760-263 760-280t-11.5-28.5Q737-320 720-320t-28.5 11.5Q680-297 680-280t11.5 28.5Q703-240 720-240t28.5-11.5ZM680-440h170l-90-120h-80v120ZM360-540Z"/></svg>
                            <span>No suppliers found<?php echo e($filters['q'] ? ' for "' . $filters['q'] . '"' : '.'); ?></span>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($suppliers->hasPages()): ?>
    <div class="px-6 py-4 border-t flex items-center justify-between"
         style="border-color:color-mix(in srgb,var(--primary) 6%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
        <span class="text-xs" style="color:var(--on-surface-var)">
            Showing <?php echo e($suppliers->firstItem()); ?>–<?php echo e($suppliers->lastItem()); ?> of <?php echo e($suppliers->total()); ?>

        </span>
        <div><?php echo e($suppliers->withQueryString()->links()); ?></div>
    </div>
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
<?php /**PATH /var/www/dots-main/resources/views/suppliers/index.blade.php ENDPATH**/ ?>