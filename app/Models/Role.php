<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Role extends Model
{
    use HasFactory;

    private static ?string $permissionPivotTable = null;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function primaryUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, $this->resolvePermissionPivotTable())->withTimestamps();
    }

    private function resolvePermissionPivotTable(): string
    {
        if (self::$permissionPivotTable !== null) {
            return self::$permissionPivotTable;
        }

        try {
            if (Schema::hasTable('role_permissions')) {
                return self::$permissionPivotTable = 'role_permissions';
            }

            if (Schema::hasTable('permission_role')) {
                return self::$permissionPivotTable = 'permission_role';
            }
        } catch (\Throwable) {
            // Fall back to legacy pivot table name when schema checks are unavailable.
        }

        return self::$permissionPivotTable = 'permission_role';
    }
}
