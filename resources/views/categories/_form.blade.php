@props(['category' => null, 'mainCategories' => collect()])

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.categories.fields.name') }} <span style="color:var(--error)">*</span>
        </label>
        <input name="name" value="{{ old('name', $category?->name) }}" required
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.categories.fields.type') }}
        </label>
        <div class="relative">
            <select name="type" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="main" @selected(old('type', $category?->type ?? 'main') === 'main')>{{ __('ui.categories.type_main') }}</option>
                <option value="sub"  @selected(old('type', $category?->type ?? 'main') === 'sub')>{{ __('ui.categories.type_sub') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('type')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.categories.fields.parent') }}
        </label>
        <div class="relative">
            <select name="parent_id" class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="" @selected((string) old('parent_id', $category?->parent_id) === '')>{{ __('ui.categories.no_parent') }}</option>
                @foreach($mainCategories as $mainCategory)
                <option value="{{ $mainCategory->id }}" @selected((string) old('parent_id', $category?->parent_id) === (string) $mainCategory->id)>{{ $mainCategory->name }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <p class="mt-1.5 text-xs" style="color:var(--on-surface-var)">{{ __('ui.categories.parent_hint') }}</p>
        @error('parent_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>
</div>
