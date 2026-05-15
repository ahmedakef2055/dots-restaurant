<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $hasRoleIdColumn = Schema::hasColumn('users', 'role_id');
        $hasIsActiveColumn = $this->hasUserColumn('is_active');

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')],
        ]);

        $users = User::query()
            ->with([
                'role:id,name',
                'role.permissions:id,name,slug',
                'roles:id,name',
                'roles.permissions:id,name,slug',
                'permissions:id,name,slug',
            ])
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($validated['role_id'] ?? null, function ($query, int $roleId) use ($hasRoleIdColumn): void {
                $query->where(function ($inner) use ($roleId, $hasRoleIdColumn): void {
                    if ($hasRoleIdColumn) {
                        $inner
                            ->where('role_id', $roleId)
                            ->orWhereHas('roles', fn($rolesQuery) => $rolesQuery->where('roles.id', $roleId));

                        return;
                    }

                    $inner->whereHas('roles', fn($rolesQuery) => $rolesQuery->where('roles.id', $roleId));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'hasIsActiveColumn' => $hasIsActiveColumn,
            'filters' => [
                'q' => $validated['q'] ?? '',
                'role_id' => (string) ($validated['role_id'] ?? ''),
            ],
        ]);
    }

    public function create(): View
    {
        $roles = Role::query()
            ->with('permissions:id,slug')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $permissions = Permission::query()
            ->whereNotIn('slug', $this->legacyPermissionSlugs())
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        $rolePermissionsMap = $roles
            ->mapWithKeys(function (Role $role): array {
                $permissionIds = $role->permissions
                    ->filter(fn(Permission $permission): bool => ! in_array((string) $permission->slug, $this->legacyPermissionSlugs(), true))
                    ->pluck('id')
                    ->map(static fn($id): int => (int) $id)
                    ->values()
                    ->all();

                return [(string) $role->id => $permissionIds];
            })
            ->all();

        return view('users.create', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissionsMap' => $rolePermissionsMap,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'username'     => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'phone'        => ['required', 'string', 'max:30', Rule::unique('users', 'phone')],
            'email'        => ['required', 'email', 'max:160', Rule::unique('users', 'email')],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'      => ['nullable', 'integer', Rule::exists('roles', 'id')],
            'new_role_name'=> ['nullable', 'string', 'max:120'],
            'job_title'    => ['nullable', 'string', 'max:120'],
            'permission_ids'   => ['nullable', 'array'],
            'permission_ids.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $roleId = $this->resolveRoleId($validated);

        $user = User::query()->create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            ...(Schema::hasColumn('users', 'role_id')    ? ['role_id'    => $roleId]                         : []),
            ...(Schema::hasColumn('users', 'job_title')  ? ['job_title'  => $validated['job_title'] ?? null] : []),
        ]);

        if (Schema::hasTable('role_user')) {
            $user->roles()->sync($roleId ? [(int) $roleId] : []);
        }

        if (Schema::hasTable('permissions') && (Schema::hasTable('permission_user') || Schema::hasTable('user_permission'))) {
            $permissionIds = collect($validated['permission_ids'] ?? [])
                ->map(static fn($id): int => (int) $id)
                ->unique()->values();

            if ($permissionIds->isEmpty() && $roleId) {
                $permissionIds = Role::query()->with('permissions:id')
                    ->whereKey((int) $roleId)->first()
                    ?->permissions->pluck('id')
                    ->map(static fn($id): int => (int) $id)
                    ->unique()->values() ?? collect();
            }

            $user->permissions()->sync($this->sanitizePermissionIds($permissionIds)->all());
        }

        return redirect()->route('users.index')
            ->with('success', __('messages.success.user_created'));
    }

    public function show(User $user): View
    {
        $hasIsActiveColumn = $this->hasUserColumn('is_active');
        $hasRoleIdColumn = $this->hasUserColumn('role_id');

        $user->load([
            'role:id,name,slug',
            'role.permissions:id,name,slug,description',
            'roles:id,name,slug',
            'roles.permissions:id,name,slug,description',
            'permissions:id,name,slug,description',
        ]);

        $directPermissions = $user->permissions
            ->sortBy('name')
            ->values();

        $roleBasedPermissions = collect();

        if ($directPermissions->isEmpty()) {
            $roleBasedPermissions = collect();

            if ($hasRoleIdColumn && $user->role) {
                $roleBasedPermissions = $roleBasedPermissions->merge($user->role->permissions);
            }

            $roleBasedPermissions = $roleBasedPermissions
                ->merge($user->roles->flatMap(fn(Role $role) => $role->permissions))
                ->unique('id')
                ->sortBy('name')
                ->values();
        }

        $effectivePermissions = $directPermissions->isNotEmpty()
            ? $directPermissions
            : $roleBasedPermissions;

        return view('users.show', [
            'user' => $user,
            'hasIsActiveColumn' => $hasIsActiveColumn,
            'hasRoleIdColumn' => $hasRoleIdColumn,
            'directPermissions' => $directPermissions,
            'roleBasedPermissions' => $roleBasedPermissions,
            'effectivePermissions' => $effectivePermissions,
        ]);
    }

    public function edit(User $user): View
    {
        $hasRoleIdColumn = $this->hasUserColumn('role_id');

        $user->load([
            'role:id,name,slug',
            'role.permissions:id,slug',
            'roles:id,name,slug',
            'roles.permissions:id,slug',
            'permissions:id,name,slug',
        ]);

        $roles = Role::query()
            ->with('permissions:id,slug')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $permissions = Permission::query()
            ->whereNotIn('slug', $this->legacyPermissionSlugs())
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        $rolePermissionsMap = $roles
            ->mapWithKeys(function (Role $role): array {
                $permissionIds = $role->permissions
                    ->filter(fn(Permission $permission): bool => ! in_array((string) $permission->slug, $this->legacyPermissionSlugs(), true))
                    ->pluck('id')
                    ->map(static fn($id): int => (int) $id)
                    ->values()
                    ->all();

                return [(string) $role->id => $permissionIds];
            })
            ->all();

        $selectedRoleId = null;

        if ($hasRoleIdColumn && $user->role_id !== null) {
            $selectedRoleId = (int) $user->role_id;
        } elseif ($user->roles->isNotEmpty()) {
            $selectedRoleId = (int) $user->roles->first()->id;
        }

        $selectedPermissions = $user->permissions
            ->filter(fn(Permission $permission): bool => ! in_array((string) $permission->slug, $this->legacyPermissionSlugs(), true))
            ->pluck('id')
            ->map(static fn($id): int => (int) $id)
            ->unique()
            ->values();

        if ($selectedPermissions->isEmpty()) {
            $roleBasedPermissions = collect();

            if ($hasRoleIdColumn && $user->role) {
                $roleBasedPermissions = $roleBasedPermissions->merge($user->role->permissions);
            }

            $selectedPermissions = $roleBasedPermissions
                ->merge($user->roles->flatMap(fn(Role $role) => $role->permissions))
                ->filter(fn(Permission $permission): bool => ! in_array((string) $permission->slug, $this->legacyPermissionSlugs(), true))
                ->pluck('id')
                ->map(static fn($id): int => (int) $id)
                ->unique()
                ->values();
        }

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissionsMap' => $rolePermissionsMap,
            'selectedRoleId' => $selectedRoleId,
            'selectedPermissions' => $selectedPermissions->all(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => [
                'required',
                'string',
                'max:60',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id'      => ['nullable', 'integer', Rule::exists('roles', 'id')],
            'new_role_name'=> ['nullable', 'string', 'max:120'],
            'job_title'    => ['nullable', 'string', 'max:120'],
            'permission_ids'   => ['nullable', 'array'],
            'permission_ids.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $roleId = $this->resolveRoleId($validated);

        $payload = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            ...(Schema::hasColumn('users', 'role_id')   ? ['role_id'   => $roleId]                         : []),
            ...(Schema::hasColumn('users', 'job_title') ? ['job_title' => $validated['job_title'] ?? null] : []),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        if (Schema::hasTable('role_user')) {
            $user->roles()->sync($roleId ? [(int) $roleId] : []);
        }

        if (Schema::hasTable('permissions') && (Schema::hasTable('permission_user') || Schema::hasTable('user_permission'))) {
            $permissionIds = collect($validated['permission_ids'] ?? [])
                ->map(static fn($id): int => (int) $id)
                ->unique()->values();

            if ($permissionIds->isEmpty() && $roleId) {
                $permissionIds = Role::query()->with('permissions:id')
                    ->whereKey((int) $roleId)->first()
                    ?->permissions->pluck('id')
                    ->map(static fn($id): int => (int) $id)
                    ->unique()->values() ?? collect();
            }

            $user->permissions()->sync($this->sanitizePermissionIds($permissionIds)->all());
        }

        return redirect()->route('users.index')
            ->with('success', __('messages.success.user_updated'));
    }

    /**
     * If `new_role_name` is provided (custom role typed by user), find or
     * create that role and return its ID. Otherwise return the existing role_id.
     */
    private function resolveRoleId(array $validated): ?int
    {
        if (! empty($validated['role_id'])) {
            return (int) $validated['role_id'];
        }

        $newName = trim((string) ($validated['new_role_name'] ?? ''));
        if ($newName === '') {
            return null;
        }

        $role = Role::query()->firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug($newName)],
            ['name' => $newName, 'description' => '']
        );

        return (int) $role->id;
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if (! $this->hasUserColumn('is_active')) {
            return back()->with('error', __('messages.errors.user_status_column_missing'));
        }

        if ((int) Auth::id() === (int) $user->id) {
            return back()->with('error', __('messages.errors.cannot_disable_current_user'));
        }

        $user->is_active = ! (bool) $user->is_active;
        $user->save();

        $statusLabel = $user->is_active
            ? __('ui.users.status_active')
            : __('ui.users.status_disabled');

        return back()->with('success', __('messages.success.user_status_updated', ['status' => $statusLabel]));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) Auth::id() === (int) $user->id) {
            return back()->with('error', __('messages.errors.cannot_delete_current_user'));
        }

        try {
            if (Schema::hasTable('role_user')) {
                $user->roles()->detach();
            }

            if (Schema::hasTable('permissions') && (Schema::hasTable('permission_user') || Schema::hasTable('user_permission'))) {
                $user->permissions()->detach();
            }

            $user->delete();
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', __('messages.errors.cannot_delete_user_with_records'));
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('messages.success.user_deleted'));
    }

    private function hasUserColumn(string $column): bool
    {
        try {
            return Schema::hasColumn('users', $column);
        } catch (\Throwable) {
            return false;
        }
    }

    private function sanitizePermissionIds(Collection $permissionIds): Collection
    {
        $permissionIds = $permissionIds
            ->map(static fn($id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($permissionIds->isEmpty()) {
            return $permissionIds;
        }

        $permissions = Permission::query()
            ->whereIn('id', $permissionIds->all())
            ->get(['id', 'slug']);

        $legacySlugs = $this->legacyPermissionSlugs();

        $validPermissions = $permissions
            ->filter(fn(Permission $permission): bool => ! in_array((string) $permission->slug, $legacySlugs, true))
            ->values();

        $selectedIds = $validPermissions
            ->pluck('id')
            ->map(static fn($id): int => (int) $id)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            return $selectedIds;
        }

        $viewSlugsToAttach = $validPermissions
            ->map(function (Permission $permission): ?string {
                $slug = (string) ($permission->slug ?? '');
                $parts = explode('.', $slug, 2);

                if (count($parts) !== 2) {
                    return null;
                }

                [$module, $action] = $parts;

                if ($module === '' || $action === 'view') {
                    return null;
                }

                return $module . '.view';
            })
            ->filter(static fn(?string $slug): bool => is_string($slug) && $slug !== '')
            ->unique()
            ->values();

        if ($viewSlugsToAttach->isNotEmpty()) {
            $viewPermissionIds = Permission::query()
                ->whereIn('slug', $viewSlugsToAttach->all())
                ->pluck('id')
                ->map(static fn($id): int => (int) $id)
                ->unique()
                ->values();

            $selectedIds = $selectedIds
                ->merge($viewPermissionIds)
                ->unique()
                ->values();
        }

        return $selectedIds;
    }

    private function legacyPermissionSlugs(): array
    {
        return [
            'orders.manage',
            'inventory.manage',
            'tables.manage',
            'menu.manage',
            'users.manage',
            'employees.manage',
            'marketing.manage',
        ];
    }
}
