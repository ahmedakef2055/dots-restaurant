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
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->after('remember_token');
                $table->index('is_active');
            });
        }

        DB::table('users')
            ->whereNull('is_active')
            ->update(['is_active' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep account status field to avoid accidental auth behavior regression.
    }
};
