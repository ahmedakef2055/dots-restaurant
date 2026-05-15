<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('restaurant_tables')) {
            return;
        }

        DB::statement("ALTER TABLE restaurant_tables MODIFY status ENUM('available','occupied','reserved') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("UPDATE restaurant_tables SET status = 'available' WHERE status = 'reserved'");
        DB::statement("ALTER TABLE restaurant_tables MODIFY status ENUM('available','occupied') NOT NULL DEFAULT 'available'");
    }
};
