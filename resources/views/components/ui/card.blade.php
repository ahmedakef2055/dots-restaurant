@props([
'title'    => null,
'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'chart-card p-5']) }}>
    @if($title)
    <h3 class="text-base font-semibold tracking-tight mb-1" style="color:var(--on-surface)">{{ $title }}</h3>
    @endif

    @if($subtitle)
    <p class="text-sm mb-3" style="color:var(--on-surface-var)">{{ $subtitle }}</p>
    @endif

    <div class="{{ $title || $subtitle ? 'mt-4' : '' }}">
        {{ $slot }}
    </div>
</div>