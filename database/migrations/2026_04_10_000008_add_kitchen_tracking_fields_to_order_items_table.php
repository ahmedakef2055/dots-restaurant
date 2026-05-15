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

        if (! Schema::hasColumn('order_items', 'kitchen_status')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->enum('kitchen_status', ['pending', 'preparing', 'ready'])
                    ->default('pending')
                    ->after('notes')
                    ->index();
            });
        }

        if (! Schema::hasColumn('order_items', 'kitchen_batch')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->unsignedInteger('kitchen_batch')
                    ->default(1)
                    ->after('kitchen_status')
                    ->index();
            });
        }

        $this->backfillKitchenStatus();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        if (Schema::hasColumn('order_items', 'kitchen_batch')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->dropColumn('kitchen_batch');
            });
        }

        if (Schema::hasColumn('order_items', 'kitchen_status')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->dropColumn('kitchen_status');
            });
        }
    }

    private function backfillKitchenStatus(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('order_items', 'kitchen_status')) {
            return;
        }

        $hasOrderKitchenStatusColumn = Schema::hasColumn('orders', 'kitchen_status');

        if ($hasOrderKitchenStatusColumn) {
            DB::statement(
                "UPDATE order_items oi
                INNER JOIN orders o ON o.id = oi.order_id
                SET oi.kitchen_status = CASE
                    WHEN o.status = 'cancelled' THEN 'ready'
                    WHEN o.kitchen_status IN ('pending', 'preparing', 'ready') THEN o.kitchen_status
                    ELSE 'pending'
                END",
            );

            return;
        }

        DB::statement(
            "UPDATE order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            SET oi.kitchen_status = CASE
                WHEN o.status = 'cancelled' THEN 'ready'
                ELSE 'pending'
            END",
        );
    }
};
