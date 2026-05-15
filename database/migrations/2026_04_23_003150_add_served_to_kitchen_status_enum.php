<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN kitchen_status ENUM('pending','preparing','ready','served') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Update any served rows back to ready before reverting
        DB::table('orders')->where('kitchen_status', 'served')->update(['kitchen_status' => 'ready']);
        DB::statement("ALTER TABLE orders MODIFY COLUMN kitchen_status ENUM('pending','preparing','ready') NOT NULL DEFAULT 'pending'");
    }
};
