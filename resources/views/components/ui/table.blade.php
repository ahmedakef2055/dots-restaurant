@props([
'headers' => [],
])

<div class="overflow-hidden rounded-2xl border shadow-sm backdrop-blur-sm" style="border-color:var(--outline-var);background-color:var(--surface-lowest)">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y']) }} style="border-color:var(--outline-var)">
            <thead style="background-color:color-mix(in srgb,var(--surface-low) 90%,transparent 10%)">
                <tr>
                    @foreach($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.12em]" style="color:var(--on-surface-var)">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y text-sm" style="border-color:var(--outline-var);background-color:var(--surface-lowest);color:var(--on-surface)">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>