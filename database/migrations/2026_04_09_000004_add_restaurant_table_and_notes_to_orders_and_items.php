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
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'restaurant_table_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('restaurant_table_id')
                    ->nullable()
                    ->after('order_type')
                    ->constrained('restaurant_tables')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'notes')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('line_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'restaurant_table_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('restaurant_table_id');
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'notes')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
};
