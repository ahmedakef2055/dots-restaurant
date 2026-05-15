<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissionIdBySlug = $this->loadPermissionIdBySlug();

        if ($permissionIdBySlug === []) {
            return;
        }

        $now = now();

        foreach ($this->legacyPermissionMap() as $legacySlug => $replacementSlugs) {
            $legacyPermissionId = $permissionIdBySlug[$legacySlug] ?? null;

            if (! is_int($legacyPermissionId)) {
                continue;
            }

            $replacementPermissionIds = collect($replacementSlugs)
                ->map(static fn(string $slug): ?int => $permissionIdBySlug[$slug] ?? null)
                ->filter(static fn(?int $id): bool => is_int($id))
                ->map(static fn(int $id): int => (int) $id)
                ->unique()
                ->values();

            if ($replacementPermissionIds->isNotEmpty()) {
                $this->copyRoleAssignments($legacyPermissionId, $replacementPermissionIds, $now);
                $this->copyUserAssignments($legacyPermissionId, $replacementPermissionIds, $now);
            }

            $this->deleteRoleAssignments($legacyPermissionId);
            $this->deleteUserAssignments($legacyPermissionId);

            DB::table('permissions')
                ->where('id', $legacyPermissionId)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This cleanup migration is intentionally non-reversible to avoid
        // reintroducing deprecated coarse-grained permission slugs.
    }

    private function legacyPermissionMap(): array
    {
        return [
            'orders.manage' => [
                'orders.view',
                'orders.create',
                'orders.update',
                'orders.delete',
            ],
            'inventory.manage' => [
                'inventory.view',
                'inventory.create',
                'inventory.update',
                'inventory.delete',
                'inventory.adjust',
                'inventory.audit',
                'recipes.view',
                'recipes.create',
                'recipes.update',
                'recipes.delete',
                'suppliers.view',
                'suppliers.create',
                'suppliers.update',
                'suppliers.delete',
                'purchases.view',
                'purchases.create',
                'purchases.update',
            ],
            'tables.manage' => [
                'tables.view',
                'tables.create',
                'tables.update',
                'tables.delete',
            ],
            'menu.manage' => [
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
                'products.view',
                'products.create',
                'products.update',
                'products.delete',
            ],
            'users.manage' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'customers.view',
                'customers.create',
                'customers.update',
                'customers.delete',
            ],
            'employees.manage' => [
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.delete',
                'attendance.view',
                'attendance.create',
                'attendance.update',
                'attendance.delete',
                'salaries.view',
                'salaries.create',
                'salaries.update',
            ],
            'marketing.manage' => [
                'offers.view',
                'offers.create',
                'offers.update',
                'offers.delete',
                'coupons.view',
                'coupons.create',
                'coupons.update',
                'coupons.delete',
            ],
        ];
    }

    private function loadPermissionIdBySlug(): array
    {
        return DB::table('permissions')
            ->select(['id', 'slug'])
            ->get()
            ->mapWithKeys(static function (object $permission): array {
                return [(string) $permission->slug => (int) $permission->id];
            })
            ->all();
    }

    private function copyRoleAssignments(int $legacyPermissionId, Collection $replacementPermissionIds, mixed $now): void
    {
        foreach ($this->rolePivotTables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $roleIds = DB::table($table)
                ->where('permission_id', $legacyPermissionId)
                ->pluck('role_id')
                ->map(static fn($id): int => (int) $id)
                ->filter(static fn(int $id): bool => $id > 0)
                ->unique()
                ->values();

            if ($roleIds->isEmpty()) {
                continue;
            }

            $hasCreatedAt = Schema::hasColumn($table, 'created_at');
            $hasUpdatedAt = Schema::hasColumn($table, 'updated_at');

            $payload = [];

            foreach ($roleIds as $roleId) {
                foreach ($replacementPermissionIds as $permissionId) {
                    $row = [
                        'role_id' => (int) $roleId,
                        'permission_id' => (int) $permissionId,
                    ];

                    if ($hasCreatedAt) {
                        $row['created_at'] = $now;
                    }

                    if ($hasUpdatedAt) {
                        $row['updated_at'] = $now;
                    }

                    $payload[] = $row;
                }
            }

            if ($payload !== []) {
                DB::table($table)->insertOrIgnore($payload);
            }
        }
    }

    private function copyUserAssignments(int $legacyPermissionId, Collection $replacementPermissionIds, mixed $now): void
    {
        foreach ($this->userPivotTables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $userIds = DB::table($table)
                ->where('permission_id', $legacyPermissionId)
                ->pluck('user_id')
                ->map(static fn($id): int => (int) $id)
                ->filter(static fn(int $id): bool => $id > 0)
                ->unique()
                ->values();

            if ($userIds->isEmpty()) {
                continue;
            }

            $hasCreatedAt = Schema::hasColumn($table, 'created_at');
            $hasUpdatedAt = Schema::hasColumn($table, 'updated_at');

            $payload = [];

            foreach ($userIds as $userId) {
                foreach ($replacementPermissionIds as $permissionId) {
                    $row = [
                        'permission_id' => (int) $permissionId,
                        'user_id' => (int) $userId,
                    ];

                    if ($hasCreatedAt) {
                        $row['created_at'] = $now;
                    }

                    if ($hasUpdatedAt) {
                        $row['updated_at'] = $now;
                    }

                    $payload[] = $row;
                }
            }

            if ($payload !== []) {
                DB::table($table)->insertOrIgnore($payload);
            }
        }
    }

    private function deleteRoleAssignments(int $permissionId): void
    {
        foreach ($this->rolePivotTables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->where('permission_id', $permissionId)
                ->delete();
        }
    }

    private function deleteUserAssignments(int $permissionId): void
    {
        foreach ($this->userPivotTables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->where('permission_id', $permissionId)
                ->delete();
        }
    }

    private function rolePivotTables(): array
    {
        return ['role_permissions', 'permission_role'];
    }

    private function userPivotTables(): array
    {
        return ['permission_user', 'user_permission'];
    }
};
