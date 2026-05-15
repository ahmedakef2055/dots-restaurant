<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
'variant' => 'primary',
'type' => 'button',
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
'variant' => 'primary',
'type' => 'button',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$base = 'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent';

$variants = [
'primary'   => 'border border-transparent text-white shadow-sm hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[var(--accent-gold)]',
'gold'      => 'border border-transparent text-[var(--on-surface)] shadow-sm hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[var(--primary)]',
'secondary' => 'border text-[var(--on-surface-var)] hover:text-[var(--on-surface)] focus-visible:ring-[var(--accent-gold)]',
'danger'    => 'border text-white hover:brightness-90 focus-visible:ring-[var(--error)]',
'dark'      => 'border border-[var(--outline-var)] bg-[var(--surface-container)] text-[var(--on-surface)] hover:bg-[var(--surface-high)] focus-visible:ring-[var(--outline)]',
];

$inlineStyle = match($variant) {
    'primary'   => 'background:linear-gradient(135deg,var(--primary),color-mix(in srgb,var(--primary) 70%,var(--accent-gold) 30%));box-shadow:0 4px 14px color-mix(in srgb,var(--primary) 30%,transparent 70%);',
    'gold'      => 'background:linear-gradient(135deg,var(--accent-gold),color-mix(in srgb,var(--accent-gold) 80%,var(--secondary) 20%));box-shadow:0 4px 14px color-mix(in srgb,var(--accent-gold) 30%,transparent 70%);',
    'secondary' => 'border-color:var(--outline-var);background-color:var(--surface-lowest);',
    'danger'    => 'border-color:color-mix(in srgb,var(--error) 40%,transparent 60%);background-color:var(--error);',
    default     => '',
};

$style = $variants[$variant] ?? $variants['primary'];
?>

<button type="<?php echo e($type); ?>" style="<?php echo e($inlineStyle ?? ''); ?>" <?php echo e($attributes->merge(['class' => $base.' '.$style])); ?>>
    <?php echo e($slot); ?>

</button><?php /**PATH /var/www/dots/resources/views/components/ui/button.blade.php ENDPATH**/ ?>