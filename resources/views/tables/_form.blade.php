@props(['tableItem' => null])

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.tables.fields.name') }} <span style="color:var(--error)">*</span>
        </label>
        <input name="name" value="{{ old('name', $tableItem?->name) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.tables.fields.capacity') }} <span style="color:var(--error)">*</span>
        </label>
        <input name="capacity" type="number" min="1" max="30"
               value="{{ old('capacity', $tableItem?->capacity ?? 4) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
        @error('capacity')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.tables.fields.status') }}
        </label>
        <div class="relative">
            <select name="status" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="available" @selected(old('status', $tableItem?->status ?? 'available') === 'available')>{{ __('ui.common.available') }}</option>
                <option value="reserved"  @selected(old('status', $tableItem?->status ?? 'available') === 'reserved')>{{ __('ui.common.reserved') }}</option>
                <option value="occupied"  @selected(old('status', $tableItem?->status ?? 'available') === 'occupied')>{{ __('ui.common.occupied') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('status')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>
