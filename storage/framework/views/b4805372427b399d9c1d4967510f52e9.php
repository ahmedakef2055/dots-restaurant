<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
'title'    => null,
'subtitle' => null,
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
'title'    => null,
'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'chart-card p-5'])); ?>>
    <?php if($title): ?>
    <h3 class="text-base font-semibold tracking-tight mb-1" style="color:var(--on-surface)"><?php echo e($title); ?></h3>
    <?php endif; ?>

    <?php if($subtitle): ?>
    <p class="text-sm mb-3" style="color:var(--on-surface-var)"><?php echo e($subtitle); ?></p>
    <?php endif; ?>

    <div class="<?php echo e($title || $subtitle ? 'mt-4' : ''); ?>">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH /var/www/dots-main/resources/views/components/ui/card.blade.php ENDPATH**/ ?>