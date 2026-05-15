<x-layouts.app :title="__('ui.inventory.adjust.title')">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← {{ __('ui.inventory.back') }}</a>
    </div>

    <x-ui.card :title="$ingredient->name" :subtitle="__('ui.inventory.adjust.subtitle')">
        <div class="mb-4 rounded-lg bg-[var(--surface-low)] px-4 py-3 text-sm ">
            <p>{{ __('ui.inventory.adjust.total_quantity') }}: <span class="font-semibold">{{ number_format((float) $ingredient->quantity, 3) }} {{ strtoupper($ingredient->unit) }}</span></p>
        </div>

        <div class="mb-4 overflow-hidden rounded-xl border border-[var(--outline-var)] dark:border-[var(--outline-var)]">
            <table class="min-w-full text-sm">
                <thead class="bg-[var(--surface-low)] text-[var(--on-surface-var)]  dark:text-[var(--on-surface-var)]">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('ui.inventory.adjust.warehouse') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('ui.inventory.adjust.quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouseStocks as $stock)
                    <tr class="border-t border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                        <td class="px-3 py-2">{{ $stock->warehouse?->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $stock->quantity, 3) }} {{ strtoupper($ingredient->unit) }}</td>
                    </tr>
                    @empty
                    <tr class="border-t border-[var(--outline-var)] dark:border-[var(--outline-var)]">
                        <td colspan="2" class="px-3 py-3 text-[var(--on-surface-var)] dark:text-[var(--outline)]">{{ __('ui.inventory.adjust.warehouse_stock_rows') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('inventory.adjust.stock', $ingredient) }}" class="grid gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('ui.inventory.adjust.warehouse') }}</label>
                <select name="warehouse_id" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                    @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $defaultWarehouseId)===(string) $warehouse->id)>
                        {{ $warehouse->name }}@if($warehouse->code) ({{ strtoupper($warehouse->code) }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('warehouse_id')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('ui.inventory.adjust.adjustment_type') }}</label>
                <select name="adjustment_type" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                    <option value="in" @selected(old('adjustment_type')==='in' )>{{ __('ui.inventory.adjust.adjustment_in') }}</option>
                    <option value="out" @selected(old('adjustment_type')==='out' )>{{ __('ui.inventory.adjust.adjustment_out') }}</option>
                    <option value="set" @selected(old('adjustment_type')==='set' )>{{ __('ui.inventory.adjust.adjustment_set') }}</option>
                </select>
                @error('adjustment_type')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('ui.inventory.adjust.quantity') }}</label>
                <input name="quantity" type="number" min="0.001" step="0.001" value="{{ old('quantity') }}" required class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]">
                @error('quantity')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium">{{ __('ui.inventory.adjust.note_optional') }}</label>
                <input name="note" value="{{ old('note') }}" class="w-full rounded-lg border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-2 text-sm dark:border-[var(--outline-var)] dark:bg-[var(--background)]" placeholder="{{ __('ui.inventory.adjust.note_placeholder') }}">
                @error('note')<p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <x-ui.button type="submit">{{ __('ui.inventory.adjust.submit') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>