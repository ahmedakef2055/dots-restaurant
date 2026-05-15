<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table): void {
                $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['role_id', 'permission_id']);
            });
        }

        if (! Schema::hasTable('permission_role')) {
            return;
        }

        $now = now();

        DB::table('permission_role')
            ->select(['role_id', 'permission_id', 'created_at', 'updated_at'])
            ->orderBy('role_id')
            ->orderBy('permission_id')
            ->chunk(500, function ($rows) use ($now): void {
                $payload = collect($rows)->map(function ($row) use ($now): array {
                    $createdAt = $row->created_at ?? $now;
                    $updatedAt = $row->updated_at ?? $now;

                    return [
                        'role_id' => (int) $row->role_id,
                        'permission_id' => (int) $row->permission_id,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ];
                })->all();

                if ($payload === []) {
                    return;
                }

                DB::table('role_permissions')->upsert(
                    $payload,
                    ['role_id', 'permission_id'],
                    ['updated_at']
                );
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep role/permission mappings intact to avoid permission data loss.
    }
};
