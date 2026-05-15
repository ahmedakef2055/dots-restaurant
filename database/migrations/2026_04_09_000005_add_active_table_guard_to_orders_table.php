<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'orders_active_table_guard_unique';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (! Schema::hasColumn('orders', 'active_table_guard')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedBigInteger('active_table_guard')
                    ->nullable()
                    ->after('restaurant_table_id');
            });
        }

        $duplicateActiveTableIds = DB::table('orders')
            ->where('order_type', 'dine_in')
            ->whereNotNull('restaurant_table_id')
            ->whereIn('status', ['pending', 'in_progress', 'open'])
            ->groupBy('restaurant_table_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('restaurant_table_id')
            ->map(static fn($id): int => (int) $id)
            ->all();

        if (! empty($duplicateActiveTableIds)) {
            throw new RuntimeException(
                'Cannot add unique active-table guard because duplicate active dine-in orders exist for table IDs: '
                    . implode(', ', $duplicateActiveTableIds)
                    . '. Resolve duplicates first, then run migration again.'
            );
        }

        DB::table('orders')->update([
            'active_table_guard' => DB::raw("CASE
                WHEN order_type = 'dine_in'
                    AND restaurant_table_id IS NOT NULL
                    AND status IN ('pending', 'in_progress', 'open')
                THEN restaurant_table_id
                ELSE NULL
            END"),
        ]);

        if (! $this->hasIndex(self::INDEX_NAME)) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unique('active_table_guard', self::INDEX_NAME);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if ($this->hasIndex(self::INDEX_NAME)) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropUnique(self::INDEX_NAME);
            });
        }

        if (Schema::hasColumn('orders', 'active_table_guard')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropColumn('active_table_guard');
            });
        }
    }

    private function hasIndex(string $indexName): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return false;
        }

        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?',
            [DB::getDatabaseName(), 'orders', $indexName]
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }
};
