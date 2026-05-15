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
        $hasRolesTable = Schema::hasTable('roles');
        $hasEmployeesTable = Schema::hasTable('employees');

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) use ($hasRolesTable, $hasEmployeesTable): void {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('phone', 30)->nullable()->unique();
                $table->string('email')->unique();
                $table->string('password');

                if ($hasRolesTable) {
                    $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('role_id')->nullable();
                }

                if ($hasEmployeesTable) {
                    $table->foreignId('employee_id')->nullable()->unique()->constrained()->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('employee_id')->nullable()->unique();
                }

                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'username')) {
                    $table->string('username')->nullable()->unique()->after('name');
                }

                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 30)->nullable()->unique()->after('username');
                }
            });
        }

        if (! Schema::hasTable('permission_user')) {
            Schema::create('permission_user', function (Blueprint $table): void {
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['permission_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('user_permission')) {
            return;
        }

        $now = now();

        DB::table('user_permission')
            ->select(['permission_id', 'user_id', 'created_at', 'updated_at'])
            ->orderBy('permission_id')
            ->orderBy('user_id')
            ->chunk(500, function ($rows) use ($now): void {
                $payload = collect($rows)->map(function ($row) use ($now): array {
                    return [
                        'permission_id' => (int) $row->permission_id,
                        'user_id' => (int) $row->user_id,
                        'created_at' => $row->created_at ?? $now,
                        'updated_at' => $row->updated_at ?? $now,
                    ];
                })->all();

                if ($payload === []) {
                    return;
                }

                DB::table('permission_user')->upsert(
                    $payload,
                    ['permission_id', 'user_id'],
                    ['updated_at']
                );
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preserve user profile and permission data on rollback.
    }
};
