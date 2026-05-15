<x-layouts.app :title="__('ui.users.title')">
@php
$currentUser    = auth()->user();
$canCreateUsers = $currentUser?->hasPermission('users.create') ?? false;
$canUpdateUsers = $currentUser?->hasPermission('users.update') ?? false;
$canDeleteUsers = $currentUser?->hasPermission('users.delete') ?? false;
@endphp

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color:var(--on-surface)">{{ __('ui.users.title') }}</h1>
            <p class="text-sm mt-1" style="color:var(--on-surface-var)">{{ __('ui.users.subtitle') }}</p>
        </div>
        @if($canCreateUsers)
        <a href="{{ route('users.create') }}" class="glass-button-primary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q440-607 440-640t-23.5-56.5Q393-720 360-720t-56.5 23.5Q280-673 280-640t23.5 56.5Q327-560 360-560t56.5-23.5ZM360-640Zm0 400Z"/></svg>
            {{ __('ui.users.new') }}
        </a>
        @endif
    </div>

    <form method="GET" action="{{ route('users.index') }}"
          class="glass-panel rounded-xl px-5 py-4 flex flex-wrap items-end gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute left-3 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--on-surface-var)"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}"
                   placeholder="{{ __('ui.users.search_placeholder') }}"
                   class="w-full glass-input rounded-xl pl-9 pr-4 py-2 text-sm">
        </div>
        <div class="relative">
            <select name="role_id" class="glass-input rounded-xl px-4 py-2 text-sm appearance-none pr-8 [&>option]:bg-[var(--surface-lowest)] [&>option]:text-[var(--on-surface)] min-w-40">
                <option value="">{{ __('ui.users.all_roles') }}</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected($filters['role_id'] !== '' && (int)$filters['role_id'] === (int)$role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color:var(--on-surface-var)"><path d="M480-360 280-560h400L480-360Z"/></svg>
        </div>
        <button type="submit" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium">{{ __('ui.common.filter') }}</button>
    </form>

    <div class="glass-panel-elevated rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent);background-color:color-mix(in srgb,var(--surface-highest) 20%,transparent)">
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.users.total', ['count' => $users->total()]) }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:color-mix(in srgb,var(--surface-highest) 30%,transparent);border-bottom:1px solid color-mix(in srgb,var(--primary) 8%,transparent)">
                        @foreach([__('ui.users.headers.name'), __('ui.users.headers.username'), __('ui.users.headers.phone'), __('ui.users.headers.email'), __('ui.users.headers.role'), __('ui.users.headers.permissions'), __('ui.users.headers.status'), __('ui.common.actions')] as $h)
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--on-surface-var)">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($users as $user)
                    @php
                    $primaryRoleName = $user->role?->name ?? $user->roles->first()?->name ?? '-';
                    $effectivePermissions = $user->permissions;
                    if ($effectivePermissions->isEmpty()) {
                        $rolePermissions = collect();
                        if ($user->role) { $rolePermissions = $rolePermissions->merge($user->role->permissions); }
                        $rolePermissions = $rolePermissions->merge($user->roles->flatMap(fn($r) => $r->permissions))->unique('id');
                        $effectivePermissions = $rolePermissions;
                    }
                    @endphp
                    <tr class="transition-colors" style="border-bottom:1px solid color-mix(in srgb,var(--primary) 5%,transparent)"
                        onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                        onmouseleave="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium" style="color:var(--on-surface)">{{ $user->name }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $user->username ?: '-' }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $user->phone ?: '-' }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $user->email }}</td>
                        <td class="px-5 py-3" style="color:var(--on-surface-var)">{{ $primaryRoleName }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)">
                                {{ $effectivePermissions->count() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($hasIsActiveColumn)
                                @if((bool)$user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[color-mix(in_srgb,var(--success)_10%,transparent_90%)] text-[var(--success)] border border-[color-mix(in_srgb,var(--success)_20%,transparent_80%)]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)] animate-pulse"></span>{{ __('ui.users.status_active') }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color:color-mix(in srgb,var(--error) 10%,transparent);border:1px solid color-mix(in srgb,var(--error) 20%,transparent);color:var(--error)">
                                    {{ __('ui.users.status_disabled') }}
                                </span>
                                @endif
                            @else
                            <span class="text-xs" style="color:var(--on-surface-var)">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('users.show', $user) }}" class="glass-button-secondary rounded-lg px-2.5 py-1.5 text-xs font-medium">{{ __('ui.users.view') }}</a>
                                @if($canUpdateUsers)
                                <a href="{{ route('users.edit', $user) }}" class="glass-button-secondary rounded-lg px-2.5 py-1.5 text-xs font-medium">{{ __('ui.common.edit') }}</a>
                                @endif
                                @if($hasIsActiveColumn && $canUpdateUsers)
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--tertiary) 25%,transparent);color:var(--tertiary)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--tertiary) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">
                                        {{ (bool)$user->is_active ? __('ui.users.disable') : __('ui.users.enable') }}
                                    </button>
                                </form>
                                @endif
                                @if($canDeleteUsers)
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition-all"
                                            style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                                            onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                                            onmouseleave="this.style.backgroundColor=''">
                                        {{ __('ui.common.delete') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-sm" style="color:var(--on-surface-var)">{{ __('ui.users.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t" style="border-color:color-mix(in srgb,var(--primary) 8%,transparent)">{{ $users->withQueryString()->links() }}</div>
        @endif
    </div>

</x-layouts.app>
