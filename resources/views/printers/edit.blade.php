<x-layouts.app :title="__('ui.printers.edit_title')">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('printers.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-sm font-semibold transition"
            style="border-color:color-mix(in srgb,var(--primary) 25%,transparent);color:var(--primary)">
            ← {{ __('ui.printers.back') }}
        </a>
    </div>

    <x-ui.card :title="$printer->name" :subtitle="__('ui.printers.edit_subtitle')">
        <form method="POST" action="{{ route('printers.update', $printer) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('printers._form', ['printer' => $printer, 'allJobs' => $allJobs])
            <div class="flex items-center justify-between pt-2">
                <button type="button"
                    onclick="document.getElementById('delete-form-{{ $printer->id }}').requestSubmit()"
                    class="rounded-xl border border-[var(--error-container)] bg-[var(--error-container)] px-4 py-2 text-sm font-medium text-[var(--error)] transition hover:brightness-95">
                    {{ __('ui.printers.delete') }}
                </button>
                <x-ui.button type="submit">{{ __('ui.printers.save') }}</x-ui.button>
            </div>
        </form>

        <form id="delete-form-{{ $printer->id }}" method="POST" action="{{ route('printers.destroy', $printer) }}"
            onsubmit="return confirm('{{ __('ui.printers.confirm_delete') }}')">
            @csrf @method('DELETE')
        </form>
    </x-ui.card>
</x-layouts.app>
