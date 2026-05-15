<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
'headers' => [],
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
'headers' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="overflow-hidden rounded-2xl border shadow-sm backdrop-blur-sm" style="border-color:var(--outline-var);background-color:var(--surface-lowest)">
    <div class="overflow-x-auto">
        <table <?php echo e($attributes->merge(['class' => 'min-w-full divide-y'])); ?> style="border-color:var(--outline-var)">
            <thead style="background-color:color-mix(in srgb,var(--surface-low) 90%,transparent 10%)">
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.12em]" style="color:var(--on-surface-var)"><?php echo e($header); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody class="divide-y text-sm" style="border-color:var(--outline-var);background-color:var(--surface-lowest);color:var(--on-surface)">
                <?php echo e($slot); ?>

            </tbody>
        </table>
    </div>
</div><?php /**PATH /var/www/dots-main/resources/views/components/ui/table.blade.php ENDPATH**/ ?>