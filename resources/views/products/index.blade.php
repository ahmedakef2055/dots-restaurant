<x-layouts.app :title="__('ui.products.title')">
<div x-data="{ showDeleteModal: false, deleteAction: '', deleteProductName: '' }">

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.products.title') }}</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">{{ __('ui.products.subtitle') }}</p>
        </div>
        <a href="{{ route('products.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
            {{ __('ui.products.new') }}
        </a>
    </div>

    <form method="GET" action="{{ route('products.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input name="q" value="{{ $filters['q'] }}" placeholder="{{ __('ui.products.search_placeholder') }}"
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <div class="relative">
            <select name="category_id" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-44">
                <option value="">{{ __('ui.products.all_categories') }}</option>
                @foreach($categories as $category)
                @php
                $lbl = $category->type === 'sub' && $category->parent
                     ? $category->parent->name . ' / ' . $category->name
                     : $category->name;
                @endphp
                <option value="{{ $category->id }}" @selected($filters['category_id']===(string)$category->id)>{{ $lbl }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <div class="relative">
            <select name="preparation_station" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="">{{ __('ui.products.all_stations') }}</option>
                <option value="kitchen" @selected($filters['preparation_station']==='kitchen')>{{ __('ui.products.stations.kitchen') }}</option>
                <option value="bar"     @selected($filters['preparation_station']==='bar')>{{ __('ui.products.stations.bar') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <button type="submit" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">{{ __('ui.common.filter') }}</button>
    </form>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.products.total', ['count' => $products->total()]) }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach(['#', __('ui.products.headers.name'), __('ui.products.headers.category'), __('ui.products.headers.station'), __('ui.products.headers.price'), __('ui.products.headers.actions')] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($products as $index => $product)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 text-xs font-mono" style="color:var(--on-surface-var)">{{ $products->firstItem() + $index }}</td>
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $product->name }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">
                            @if($product->category)
                                @if($product->category->type === 'sub' && $product->category->parent)
                                {{ $product->category->parent->name }} / {{ $product->category->name }}
                                @else
                                {{ $product->category->name }}
                                @endif
                            @else -@endif
                        </td>
                        <td class="px-5 py-3">
                            @if($product->preparation_station === 'bar')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--primary) 15%,var(--surface-lowest) 85%);border:1px solid color-mix(in srgb,var(--primary) 40%,transparent 60%);color:var(--primary)">
                                {{ __('ui.products.stations.bar') }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)">
                                {{ __('ui.products.stations.kitchen') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-semibold font-mono" style="color:var(--primary)">
                            {{ \App\Support\CurrencyFormatter::format($product->price) }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">{{ __('ui.common.edit') }}</a>
                                <button type="button"
                                        @click="deleteAction = @js(route('products.destroy', $product)); deleteProductName = @js($product->name); showDeleteModal = true"
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                        style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                        onmouseleave="this.style.backgroundColor=''">{{ __('ui.common.delete') }}</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.products.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
            {{ $products->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Delete Modal --}}
    <div x-cloak x-show="showDeleteModal"
         class="fixed inset-0 z-80 flex items-center justify-center p-4"
         role="dialog" aria-modal="true">
        <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.6)" @click="showDeleteModal = false"></div>
        <div x-show="showDeleteModal"
             x-transition:enter="transform ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="glass-panel-elevated relative w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     style="background-color:color-mix(in srgb,var(--error) 12%,transparent)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--error)"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                </div>
                <h3 class="text-lg font-semibold" style="color:var(--on-surface)">{{ __('ui.products.delete_modal_title') }}</h3>
            </div>
            <p class="text-sm mb-6" style="color:var(--on-surface-var)">
                {{ __('ui.products.delete_modal_message_prefix') }}
                <span class="font-semibold" style="color:var(--on-surface)" x-text="deleteProductName"></span>{{ __('ui.products.delete_modal_question_mark') }}
            </p>
            <form :action="deleteAction" method="POST" class="flex justify-end gap-3">
                @csrf @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="glass-button-secondary rounded-xl py-2.5 px-6 text-sm font-medium">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="rounded-xl py-2.5 px-6 text-sm font-semibold"
                        style="background-color:color-mix(in srgb,var(--error) 15%,transparent);border:1px solid color-mix(in srgb,var(--error) 30%,transparent);color:var(--error)">
                    {{ __('ui.common.delete') }}
                </button>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>
