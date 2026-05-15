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
        if (! Schema::hasTable('order_items')) {
            return;
        }

        if (! Schema::hasColumn('order_items', 'preparation_station')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->enum('preparation_station', ['kitchen', 'bar'])
                    ->default('kitchen')
                    ->after('kitchen_batch')
                    ->index();
            });
        }

        $this->backfillPreparationStation();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('order_items') || ! Schema::hasColumn('order_items', 'preparation_station')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn('preparation_station');
        });
    }

    private function backfillPreparationStation(): void
    {
        if (! Schema::hasTable('order_items') || ! Schema::hasColumn('order_items', 'preparation_station')) {
            return;
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'preparation_station')) {
            DB::statement(
                "UPDATE order_items oi
                LEFT JOIN products p ON p.id = oi.product_id
                SET oi.preparation_station = CASE
                    WHEN p.preparation_station = 'bar' THEN 'bar'
                    ELSE 'kitchen'
                END",
            );

            return;
        }

        DB::table('order_items')->update([
            'preparation_station' => 'kitchen',
        ]);
    }
};
