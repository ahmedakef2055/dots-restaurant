<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('warehouses')) {
            return;
        }

        $now = now();

        $mainWarehouse = DB::table('warehouses')->where('code', 'MAIN')->first();

        if (! $mainWarehouse) {
            $mainWarehouse = DB::table('warehouses')
                ->where('is_default', true)
                ->orderBy('id')
                ->first();
        }

        if (! $mainWarehouse) {
            $mainWarehouse = DB::table('warehouses')->orderBy('id')->first();
        }

        if (! $mainWarehouse) {
            $mainWarehouseId = (int) DB::table('warehouses')->insertGetId([
                'name' => 'Main Warehouse',
                'code' => 'MAIN',
                'location' => null,
                'notes' => null,
                'is_active' => true,
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $mainWarehouseId = (int) $mainWarehouse->id;

            $mainUpdates = [
                'is_active' => true,
                'is_default' => true,
                'updated_at' => $now,
            ];

            if ((string) ($mainWarehouse->code ?? '') === '') {
                $mainCodeInUse = DB::table('warehouses')
                    ->where('code', 'MAIN')
                    ->where('id', '!=', $mainWarehouseId)
                    ->exists();

                if (! $mainCodeInUse) {
                    $mainUpdates['code'] = 'MAIN';
                }
            }

            DB::table('warehouses')
                ->where('id', $mainWarehouseId)
                ->update($mainUpdates);
        }

        DB::table('warehouses')
            ->where('id', '!=', $mainWarehouseId)
            ->update([
                'is_default' => false,
                'updated_at' => $now,
            ]);

        $branchWarehouse = DB::table('warehouses')->where('code', 'BRANCH')->first();

        if (! $branchWarehouse) {
            DB::table('warehouses')->insert([
                'name' => 'Restaurant Branch Warehouse',
                'code' => 'BRANCH',
                'location' => null,
                'notes' => 'Secondary warehouse for restaurant branch operations.',
                'is_active' => true,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('warehouses')
                ->where('id', (int) $branchWarehouse->id)
                ->update([
                    'is_active' => true,
                    'is_default' => false,
                    'updated_at' => $now,
                ]);
        }

        if (Schema::hasTable('ingredients') && Schema::hasColumn('ingredients', 'default_warehouse_id')) {
            DB::table('ingredients')
                ->whereNull('default_warehouse_id')
                ->update(['default_warehouse_id' => $mainWarehouseId]);
        }

        if (
            Schema::hasTable('ingredient_warehouse_stocks') &&
            Schema::hasTable('ingredients') &&
            Schema::hasColumn('ingredients', 'default_warehouse_id')
        ) {
            DB::statement(
                'INSERT INTO ingredient_warehouse_stocks (ingredient_id, warehouse_id, quantity, average_cost, last_purchase_cost, created_at, updated_at)
                 SELECT i.id, i.default_warehouse_id, 0, 0, 0, ?, ?
                 FROM ingredients i
                 LEFT JOIN ingredient_warehouse_stocks s
                   ON s.ingredient_id = i.id
                  AND s.warehouse_id = i.default_warehouse_id
                 WHERE i.default_warehouse_id IS NOT NULL
                   AND s.id IS NULL',
                [$now, $now]
            );
        }
    }

    public function down(): void
    {
        // Intentionally left empty to avoid deleting existing warehouse data.
    }
};
