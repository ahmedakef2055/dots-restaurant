<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            return;
        }

        Schema::table('users', function (Blueprint $table) use ($hasRolesTable, $hasEmployeesTable): void {
            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable();
            }

            if (! Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable();
            }

            if (! Schema::hasColumn('users', 'role_id')) {
                if ($hasRolesTable) {
                    $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('role_id')->nullable();
                }
            }

            if (! Schema::hasColumn('users', 'employee_id')) {
                if ($hasEmployeesTable) {
                    $table->foreignId('employee_id')->nullable()->unique()->constrained()->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('employee_id')->nullable()->unique();
                }
            }

            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }

            if (! Schema::hasColumn('users', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep user account schema changes to avoid accidental account data loss on rollback.
    }
};
