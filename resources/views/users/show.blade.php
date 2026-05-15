<x-layouts.app :title="$user->name">
@php
$currentUser    = auth()->user();
$canUpdateUsers = $currentUser?->hasPermission('users.update') ?? false;
$canDeleteUsers = $currentUser?->hasPermission('users.delete') ?? false;
@endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('users.index') }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>{{ __('ui.users.back') }}
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight" style="color:var(--on-surface)">{{ $user->name }}</h1>
                <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.users.details_subtitle') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($canUpdateUsers)
            <a href="{{ route('users.edit', $user) }}" class="glass-button-secondary rounded-xl py-2 px-4 text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[18px]" ><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>{{ __('ui.common.edit') }}
            </a>
            @endif
            @if($hasIsActiveColumn && $canUpdateUsers)
            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline-block">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-xl py-2 px-4 text-sm font-medium transition-all"
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
                <button type="submit" class="rounded-xl py-2 px-4 text-sm font-medium transition-all"
                        style="border:1px solid color-mix(in srgb,var(--error) 25%,transparent);color:var(--error)"
                        onmouseenter="this.style.backgroundColor='color-mix(in srgb,var(--error) 8%,transparent)'"
                        onmouseleave="this.style.backgroundColor=''">
                    {{ __('ui.common.delete') }}
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- User details --}}
    <div class="glass-panel-elevated rounded-2xl p-6 mb-6 max-w-2xl">
        <h2 class="text-base font-semibold flex items-center gap-2 mb-5" style="color:var(--on-surface)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M287-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80Zm96.5-143.5Q760-287 760-320t-23.5-56.5Q713-400 680-400t-56.5 23.5Q600-353 600-320t23.5 56.5Q647-240 680-240t56.5-23.5Zm-280-320Q480-607 480-640t-23.5-56.5Q433-720 400-720t-56.5 23.5Q320-673 320-640t23.5 56.5Q367-560 400-560t56.5-23.5ZM400-640Zm12 400Z"/></svg>
            {{ __('ui.users.details_title') }}
        </h2>
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.fields.name') }}</dt>
                <dd class="font-medium" style="color:var(--on-surface)">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.fields.username') }}</dt>
                <dd class="font-medium" style="color:var(--on-surface)">{{ $user->username ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.fields.phone') }}</dt>
                <dd class="font-medium" style="color:var(--on-surface)">{{ $user->phone ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.fields.email') }}</dt>
                <dd class="font-medium" style="color:var(--on-surface)">{{ $user->email ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.fields.role') }}</dt>
                <dd class="font-medium" style="color:var(--on-surface)">
                    @if($hasRoleIdColumn)
                    {{ $user->role?->name ?? $user->roles->first()?->name ?? '-' }}
                    @else
                    {{ $user->roles->first()?->name ?? '-' }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--on-surface-var)">{{ __('ui.users.account_status') }}</dt>
                <dd>
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
                </dd>
            </div>
        </dl>
    </div>

    {{-- Permissions --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="glass-panel rounded-2xl p-6">
            <h3 class="text-base font-semibold flex items-center gap-2 mb-4" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--secondary)"><path d="M223.5-423.5Q200-447 200-480t23.5-56.5Q247-560 280-560t56.5 23.5Q360-513 360-480t-23.5 56.5Q313-400 280-400t-56.5-23.5ZM280-240q-100 0-170-70T40-480q0-100 70-170t170-70q67 0 121.5 33t86.5 87h352l120 120-180 180-80-60-80 60-85-60h-47q-32 54-86.5 87T280-240Zm0-80q56 0 98.5-34t56.5-86h125l58 41 82-61 71 55 75-75-40-40H435q-14-52-56.5-86T280-640q-66 0-113 47t-47 113q0 66 47 113t113 47Z"/></svg>
                {{ __('ui.users.direct_permissions_title') }}
            </h3>
            <p class="text-xs mb-3" style="color:var(--on-surface-var)">{{ __('ui.users.direct_permissions_subtitle') }}</p>
            @if($directPermissions->isEmpty())
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.users.no_direct_permissions') }}</p>
            @else
            <div class="flex flex-wrap gap-2">
                @foreach($directPermissions as $permission)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color:color-mix(in srgb,var(--secondary) 10%,transparent);border:1px solid color-mix(in srgb,var(--secondary) 20%,transparent);color:var(--secondary)">
                    {{ $permission->name }}
                </span>
                @endforeach
            </div>
            @endif
        </div>

        <div class="glass-panel rounded-2xl p-6">
            <h3 class="text-base font-semibold flex items-center gap-2 mb-4" style="color:var(--on-surface)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="w-[1.25em] h-[1.25em] inline-flex items-center justify-center align-middle shrink-0 text-[20px]" style="color:var(--primary)"><path d="M480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v244q0 152-90.5 276.5T480-80Zm0-84q104-33 172-132t68-220v-189l-240-90-240 90v189q0 121 68 220t172 132Zm0-316Z"/></svg>
                {{ __('ui.users.effective_permissions_title') }}
            </h3>
            <p class="text-xs mb-3" style="color:var(--on-surface-var)">{{ __('ui.users.effective_permissions_subtitle') }}</p>
            @if($effectivePermissions->isEmpty())
            <p class="text-sm" style="color:var(--on-surface-var)">{{ __('ui.users.no_effective_permissions') }}</p>
            @else
            <div class="flex flex-wrap gap-2">
                @foreach($effectivePermissions as $permission)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 20%,transparent);color:var(--primary)">
                    {{ $permission->name }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</x-layouts.app>
