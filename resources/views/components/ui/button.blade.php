@props([
'variant' => 'primary',
'type' => 'button',
])

@php
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
@endphp

<button type="{{ $type }}" style="{{ $inlineStyle ?? '' }}" {{ $attributes->merge(['class' => $base.' '.$style]) }}>
    {{ $slot }}
</button>