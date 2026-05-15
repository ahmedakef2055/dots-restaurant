<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Permission extends Model
{
    use HasFactory;

    private static ?string $rolePivotTable = null;
    private static ?string $userPivotTable = null;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, $this->resolveRolePivotTable())->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, $this->resolveUserPivotTable())->withTimestamps();
    }

    private function resolveRolePivotTable(): string
    {
        if (self::$rolePivotTable !== null) {
            return self::$rolePivotTable;
        }

        try {
            if (Schema::hasTable('role_permissions')) {
                return self::$rolePivotTable = 'role_permissions';
            }

            if (Schema::hasTable('permission_role')) {
                return self::$rolePivotTable = 'permission_role';
            }
        } catch (\Throwable) {
            // Fall back to legacy pivot table name when schema checks are unavailable.
        }

        return self::$rolePivotTable = 'permission_role';
    }

    private function resolveUserPivotTable(): string
    {
        if (self::$userPivotTable !== null) {
            return self::$userPivotTable;
        }

        try {
            if (Schema::hasTable('permission_user')) {
                return self::$userPivotTable = 'permission_user';
            }

            if (Schema::hasTable('user_permission')) {
                return self::$userPivotTable = 'user_permission';
            }
        } catch (\Throwable) {
            // Fall back to default pivot table name when schema checks are unavailable.
        }

        return self::$userPivotTable = 'permission_user';
    }
}
