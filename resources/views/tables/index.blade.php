<x-layouts.app :title="__('ui.tables.title')">

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.tables.title') }}</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">{{ __('ui.tables.subtitle') }}</p>
        </div>
        <a href="{{ route('tables.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
            {{ __('ui.tables.new') }}
        </a>
    </div>

    <form method="GET" action="{{ route('tables.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input name="q" value="{{ $filters['q'] }}" placeholder="{{ __('ui.tables.search_placeholder') }}"
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <div class="relative">
            <select name="status" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)]">
                <option value="">{{ __('ui.common.all_statuses') }}</option>
                <option value="available" @selected($filters['status']==='available')>{{ __('ui.common.available') }}</option>
                <option value="reserved"  @selected($filters['status']==='reserved')>{{ __('ui.common.reserved') }}</option>
                <option value="occupied"  @selected($filters['status']==='occupied')>{{ __('ui.common.occupied') }}</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <button type="submit" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">{{ __('ui.common.filter') }}</button>
    </form>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.tables.total', ['count' => $tables->total()]) }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach([__('ui.tables.headers.table'), __('ui.tables.headers.capacity'), __('ui.tables.headers.status'), __('ui.tables.headers.updated'), __('ui.tables.headers.actions')] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y" style="border-color:color-mix(in srgb,var(--primary) 5%,transparent)">
                    @forelse($tables as $tableItem)
                    <tr class="transition-colors"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $tableItem->name }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ __('ui.tables.seats', ['count' => $tableItem->capacity]) }}</td>
                        <td class="px-5 py-3">
                            @if($tableItem->status === 'available')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                  style="background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)">
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color:var(--success)"></span>
                                {{ __('ui.common.available') }}
                            </span>
                            @elseif($tableItem->status === 'reserved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--primary) 15%,var(--surface-lowest) 85%);border:1px solid color-mix(in srgb,var(--primary) 40%,transparent 60%);color:var(--primary)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--primary)"></span>
                                {{ __('ui.common.reserved') }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                  style="background-color:var(--warning-container);border:1px solid color-mix(in srgb,var(--warning) 40%,transparent 60%);color:var(--warning)">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color:var(--warning)"></span>
                                {{ __('ui.common.occupied') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $tableItem->updated_at?->diffForHumans() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('tables.toggle-status', $tableItem) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                        style="{{ $tableItem->status === 'available'
                                            ? 'background-color:color-mix(in srgb,var(--primary) 12%,var(--surface-lowest) 88%);border:1px solid color-mix(in srgb,var(--primary) 35%,transparent 65%);color:var(--primary)'
                                            : ($tableItem->status === 'reserved'
                                                ? 'background-color:var(--warning-container);border:1px solid color-mix(in srgb,var(--warning) 40%,transparent 60%);color:var(--warning)'
                                                : 'background-color:var(--success-container);border:1px solid color-mix(in srgb,var(--success) 35%,transparent 65%);color:var(--success)') }}"
                                        onmouseenter="this.style.opacity='0.75'"
                                        onmouseleave="this.style.opacity='1'">
                                        @if($tableItem->status === 'available')
                                            {{ __('ui.tables.reserve') }}
                                        @elseif($tableItem->status === 'reserved')
                                            {{ __('ui.tables.mark_occupied') }}
                                        @else
                                            {{ __('ui.tables.set_available') }}
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('tables.edit', $tableItem) }}" class="glass-button-secondary rounded-lg px-3 py-1.5 text-xs font-medium">{{ __('ui.common.edit') }}</a>
                                <form method="POST" action="{{ route('tables.destroy', $tableItem) }}"
                                      data-confirm-message="{{ __('ui.tables.delete_confirm') }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">{{ __('ui.common.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.tables.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tables->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">
            {{ $tables->withQueryString()->links() }}
        </div>
        @endif
    </div>

</x-layouts.app>
