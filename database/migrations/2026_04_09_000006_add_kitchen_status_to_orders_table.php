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
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'kitchen_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('kitchen_status', ['pending', 'preparing', 'ready'])
                    ->default('pending')
                    ->after('status')
                    ->index();
            });

            DB::table('orders')
                ->where('status', 'paid')
                ->update(['kitchen_status' => 'ready']);

            DB::table('orders')
                ->where('status', 'cancelled')
                ->update(['kitchen_status' => 'ready']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'kitchen_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('kitchen_status');
            });
        }
    }
};
