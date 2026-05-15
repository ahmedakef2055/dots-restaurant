@props([
'id',
'title' => 'Modal',
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 backdrop-blur-[2px]" style="background-color:color-mix(in srgb,var(--background) 55%,transparent 45%)" data-modal-close="{{ $id }}"></div>
    <div class="relative mx-auto mt-24 w-[92%] max-w-lg rounded-2xl border p-6 shadow-2xl backdrop-blur-sm" style="border-color:var(--outline-var);background-color:var(--surface-lowest)">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold tracking-[-0.01em]" style="color:var(--on-surface)">{{ $title }}</h3>
            <button type="button" data-modal-close="{{ $id }}" class="rounded-md p-2 transition" style="color:var(--on-surface-var)" onmouseover="this.style.backgroundColor='color-mix(in srgb,var(--outline-var) 30%,transparent 70%)'" onmouseout="this.style.backgroundColor='transparent'">✕</button>
        </div>

        <div class="text-sm leading-7" style="color:var(--on-surface-var)">
            {{ $slot }}
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <x-ui.button variant="secondary" data-modal-close="{{ $id }}">Cancel</x-ui.button>
            <x-ui.button>Confirm</x-ui.button>
        </div>
    </div>
</div>