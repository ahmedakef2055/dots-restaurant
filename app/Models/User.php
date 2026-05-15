<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

#[Fillable(['name', 'username', 'phone', 'email', 'password', 'role_id', 'job_title', 'employee_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    private static ?string $directPermissionPivotTable = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, $this->resolveDirectPermissionPivotTable())->withTimestamps();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        try {
            if (! Schema::hasTable('permissions')) {
                return false;
            }

            $hasDirectPermissionMappingTable = Schema::hasTable('permission_user') || Schema::hasTable('user_permission');
            $hasRolePermissionMappingTable = Schema::hasTable('role_permissions') || Schema::hasTable('permission_role');

            if (! $hasDirectPermissionMappingTable && ! $hasRolePermissionMappingTable) {
                return false;
            }

            if ($hasDirectPermissionMappingTable) {
                $hasAnyDirectPermissionAssigned = $this->permissions()->exists();

                if ($hasAnyDirectPermissionAssigned) {
                    return $this->permissions()
                        ->where('slug', $permissionSlug)
                        ->exists();
                }
            }

            if (! $hasRolePermissionMappingTable) {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }

        $hasPermissionViaPivotRole = $this->roles()
            ->whereHas('permissions', fn($query) => $query->where('slug', $permissionSlug))
            ->exists();

        if ($hasPermissionViaPivotRole) {
            return true;
        }

        if ($this->role_id === null) {
            return false;
        }

        return $this->role()
            ->whereHas('permissions', fn($query) => $query->where('slug', $permissionSlug))
            ->exists();
    }

    private function resolveDirectPermissionPivotTable(): string
    {
        if (self::$directPermissionPivotTable !== null) {
            return self::$directPermissionPivotTable;
        }

        try {
            if (Schema::hasTable('permission_user')) {
                return self::$directPermissionPivotTable = 'permission_user';
            }

            if (Schema::hasTable('user_permission')) {
                return self::$directPermissionPivotTable = 'user_permission';
            }
        } catch (\Throwable) {
            // Fall back to default pivot table name when schema checks are unavailable.
        }

        return self::$directPermissionPivotTable = 'permission_user';
    }
}
