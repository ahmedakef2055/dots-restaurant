<x-layouts.app :title="__('ui.users.edit_title')">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[var(--outline-var)] bg-[var(--surface-lowest)] px-3 py-1.5 text-sm font-semibold text-[var(--on-surface-var)] transition hover:border-[var(--primary)] hover:bg-[color-mix(in_srgb,var(--primary)_8%,transparent_92%)] hover:text-[var(--primary)]">← {{ __('ui.users.back') }}</a>
    </div>

    <x-ui.card :title="__('ui.users.edit_card_title', ['name' => $user->name])" :subtitle="__('ui.users.edit_subtitle')">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('users._form', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissionsMap' => $rolePermissionsMap,
            'user' => $user,
            'selectedRoleId' => $selectedRoleId,
            'selectedPermissions' => $selectedPermissions,
            'isEdit' => true,
            'autoApplyRolePermissions' => false,
            ])

            <div>
                <x-ui.button type="submit">{{ __('ui.users.update') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>