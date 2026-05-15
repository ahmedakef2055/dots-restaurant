@props(['product' => null, 'categories' => collect()])

<div class="space-y-5">
    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.products.fields.name') }} <span style="color:var(--error)">*</span>
        </label>
        <input name="name" value="{{ old('name', $product?->name) }}" required
               placeholder="{{ __('ui.products.fields.name_placeholder') }}"
               class="w-full rounded-xl glass-input px-4 py-2.5 text-sm">
        @error('name')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                {{ __('ui.products.fields.price') }} <span style="color:var(--error)">*</span>
            </label>
            <input name="price" value="{{ old('price', $product?->price) }}" required
                   min="0" step="0.01" type="number" placeholder="0.00"
                   class="w-full rounded-xl glass-input px-4 py-2.5 text-sm font-mono">
            @error('price')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                {{ __('ui.products.fields.category') }} <span style="color:var(--error)">*</span>
            </label>
            <div class="relative">
                <select name="category_id" required
                        class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                    <option value="" disabled @selected((string)old('category_id', $product?->category_id) === '')>{{ __('ui.products.fields.select_category') }}</option>
                    @foreach($categories as $category)
                    @php
                    $lbl = $category->type === 'sub' && $category->parent
                         ? $category->parent->name . ' / ' . $category->name
                         : $category->name;
                    @endphp
                    <option value="{{ $category->id }}" @selected((string)old('category_id', $product?->category_id)===(string)$category->id)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
            </div>
            @error('category_id')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.products.fields.station') }} <span style="color:var(--error)">*</span>
        </label>
        <div class="relative">
            <select name="preparation_station" required
                    class="w-full rounded-xl glass-input px-4 py-2.5 text-sm appearance-none [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="kitchen" @selected(old('preparation_station', $product?->preparation_station ?? 'kitchen') === 'kitchen')>{{ __('ui.products.stations.kitchen') }}</option>
                <option value="bar"     @selected(old('preparation_station', $product?->preparation_station ?? 'kitchen') === 'bar')>{{ __('ui.products.stations.bar') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        @error('preparation_station')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
            {{ __('ui.products.fields.description') }}
        </label>
        <textarea name="description" rows="4"
                  placeholder="{{ __('ui.products.fields.description_placeholder') }}"
                  class="w-full rounded-xl glass-input px-4 py-3 text-sm resize-none">{{ old('description', $product?->description) }}</textarea>
        @error('description')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
    </div>

    {{-- ══ Barcode Section ══ --}}
    <div class="rounded-2xl p-4 space-y-3" style="background:color-mix(in srgb,var(--primary) 4%,transparent);border:1px solid color-mix(in srgb,var(--primary) 12%,transparent)">
        <div class="flex items-center gap-2 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-5 h-5 shrink-0" style="color:var(--primary)"><path d="M40-120v-720h80v720H40Zm120 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h120v720H400Zm160 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h80v720h-80Z"/></svg>
            <h3 class="text-sm font-semibold" style="color:var(--on-surface)">{{ __('ui.products.barcode.section_title') }}</h3>
        </div>
        <p class="text-xs" style="color:var(--on-surface-var)">{{ __('ui.products.barcode.section_subtitle') }}</p>
        <div>
            <label class="mb-1.5 block text-sm font-medium" style="color:var(--on-surface-var)">
                {{ __('ui.products.barcode.field_label') }}
            </label>
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M40-120v-720h80v720H40Zm120 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h120v720H400Zm160 0v-720h40v720h-40Zm120 0v-720h80v720h-80Zm120 0v-720h80v720h-80Z"/></svg>
                <input name="barcode"
                       value="{{ old('barcode', $product?->barcode) }}"
                       type="text"
                       placeholder="{{ __('ui.products.barcode.field_placeholder') }}"
                       class="w-full rounded-xl glass-input pl-10 pr-4 py-2.5 text-sm font-mono tracking-widest">
            </div>
            @error('barcode')<p class="mt-1.5 text-xs" style="color:var(--error)">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
