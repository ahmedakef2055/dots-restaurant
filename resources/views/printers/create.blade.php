<x-layouts.app :title="__('ui.printers.add')">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('printers.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-sm font-semibold transition"
            style="border-color:color-mix(in srgb,var(--primary) 25%,transparent);color:var(--primary)">
            ← {{ __('ui.printers.back') }}
        </a>
    </div>

    <x-ui.card :title="__('ui.printers.add')" :subtitle="__('ui.printers.add_subtitle')">
        <form method="POST" action="{{ route('printers.store') }}" class="space-y-4">
            @csrf
            @include('printers._form', ['printer' => null, 'allJobs' => $allJobs])
            <div class="pt-2">
                <x-ui.button type="submit">{{ __('ui.printers.save') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
