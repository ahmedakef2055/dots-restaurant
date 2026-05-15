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
        $this->createUnitsTable();
        $this->createWarehousesTable();
        $this->createIngredientWarehouseStocksTable();
        $this->createInventoryBatchesTable();
        $this->createInventoryStockTransfersTables();
        $this->createStockAuditTables();
        $this->createRecipeVersionTables();
        $this->createProductionBatchTables();

        $this->upgradeIngredientsTable();
        $this->upgradePurchasesTables();
        $this->upgradeInventoryLogsTable();
        $this->upgradeOrdersTables();

        $this->seedUnits();
        $defaultWarehouseId = $this->seedDefaultWarehouse();

        $this->backfillIngredientMetadata($defaultWarehouseId);
        $this->backfillWarehouseStocks($defaultWarehouseId);
        $this->backfillOpeningInventoryBatches($defaultWarehouseId);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_items')) {
            if (Schema::hasColumn('order_items', 'recipe_version_id')) {
                Schema::table('order_items', function (Blueprint $table): void {
                    $table->dropConstrainedForeignId('recipe_version_id');
                });
            }

            if (Schema::hasColumn('order_items', 'variant_name')) {
                Schema::table('order_items', function (Blueprint $table): void {
                    $table->dropColumn('variant_name');
                });
            }
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'inventory_deducted_at')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropColumn('inventory_deducted_at');
            });
        }

        if (Schema::hasTable('inventory_stock_logs')) {
            Schema::table('inventory_stock_logs', function (Blueprint $table): void {
                if (Schema::hasColumn('inventory_stock_logs', 'warehouse_id')) {
                    $table->dropConstrainedForeignId('warehouse_id');
                }
                if (Schema::hasColumn('inventory_stock_logs', 'action')) {
                    $table->dropColumn('action');
                }
                if (Schema::hasColumn('inventory_stock_logs', 'unit_cost')) {
                    $table->dropColumn('unit_cost');
                }
                if (Schema::hasColumn('inventory_stock_logs', 'reference_type')) {
                    $table->dropColumn('reference_type');
                }
                if (Schema::hasColumn('inventory_stock_logs', 'reference_id')) {
                    $table->dropColumn('reference_id');
                }
                if (Schema::hasColumn('inventory_stock_logs', 'occurred_at')) {
                    $table->dropColumn('occurred_at');
                }
            });
        }

        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table): void {
                if (Schema::hasColumn('purchase_items', 'warehouse_id')) {
                    $table->dropConstrainedForeignId('warehouse_id');
                }
                if (Schema::hasColumn('purchase_items', 'expiry_date')) {
                    $table->dropColumn('expiry_date');
                }
            });
        }

        if (Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'warehouse_id')) {
            Schema::table('purchases', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('warehouse_id');
            });
        }

        if (Schema::hasTable('ingredients')) {
            Schema::table('ingredients', function (Blueprint $table): void {
                if (Schema::hasColumn('ingredients', 'unit_id')) {
                    $table->dropConstrainedForeignId('unit_id');
                }
                if (Schema::hasColumn('ingredients', 'supplier_id')) {
                    $table->dropConstrainedForeignId('supplier_id');
                }
                if (Schema::hasColumn('ingredients', 'default_warehouse_id')) {
                    $table->dropConstrainedForeignId('default_warehouse_id');
                }
                if (Schema::hasColumn('ingredients', 'cost')) {
                    $table->dropColumn('cost');
                }
                if (Schema::hasColumn('ingredients', 'quantity')) {
                    $table->dropColumn('quantity');
                }
                if (Schema::hasColumn('ingredients', 'threshold')) {
                    $table->dropColumn('threshold');
                }
                if (Schema::hasColumn('ingredients', 'cost_method')) {
                    $table->dropColumn('cost_method');
                }
                if (Schema::hasColumn('ingredients', 'expiry_date')) {
                    $table->dropColumn('expiry_date');
                }
                if (Schema::hasColumn('ingredients', 'expiry_alert_days')) {
                    $table->dropColumn('expiry_alert_days');
                }
            });
        }

        Schema::dropIfExists('production_batch_consumptions');
        Schema::dropIfExists('production_batches');
        Schema::dropIfExists('recipe_version_items');
        Schema::dropIfExists('recipe_versions');
        Schema::dropIfExists('stock_audit_items');
        Schema::dropIfExists('stock_audits');
        Schema::dropIfExists('inventory_stock_transfer_items');
        Schema::dropIfExists('inventory_stock_transfers');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('ingredient_warehouse_stocks');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('units');
    }

    private function createUnitsTable(): void
    {
        if (Schema::hasTable('units')) {
            return;
        }

        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('family', 30)->index();
            $table->decimal('base_factor', 14, 6)->unsigned()->default(1);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    private function createWarehousesTable(): void
    {
        if (Schema::hasTable('warehouses')) {
            return;
        }

        Schema::create('warehouses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->nullable()->unique();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });
    }

    private function createIngredientWarehouseStocksTable(): void
    {
        if (Schema::hasTable('ingredient_warehouse_stocks')) {
            return;
        }

        Schema::create('ingredient_warehouse_stocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 14, 3)->unsigned()->default(0);
            $table->decimal('average_cost', 14, 4)->unsigned()->default(0);
            $table->decimal('last_purchase_cost', 14, 4)->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['ingredient_id', 'warehouse_id'], 'ingredient_warehouse_unique');
            $table->index(['warehouse_id', 'ingredient_id']);
        });
    }

    private function createInventoryBatchesTable(): void
    {
        if (Schema::hasTable('inventory_batches')) {
            return;
        }

        Schema::create('inventory_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', 14, 3)->unsigned();
            $table->decimal('remaining_quantity', 14, 3)->unsigned();
            $table->decimal('unit_cost', 14, 4)->unsigned()->default(0);
            $table->decimal('total_cost', 14, 2)->unsigned()->default(0);
            $table->date('expiry_date')->nullable()->index();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->index(['ingredient_id', 'warehouse_id', 'created_at'], 'inventory_batch_fifo_idx');
        });
    }

    private function createInventoryStockTransfersTables(): void
    {
        if (! Schema::hasTable('inventory_stock_transfers')) {
            Schema::create('inventory_stock_transfers', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('from_warehouse_id')->constrained('warehouses')->restrictOnDelete();
                $table->foreignId('to_warehouse_id')->constrained('warehouses')->restrictOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('status', ['completed', 'cancelled'])->default('completed')->index();
                $table->text('notes')->nullable();
                $table->timestamp('transferred_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('inventory_stock_transfer_items')) {
            return;
        }

        Schema::create('inventory_stock_transfer_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('inventory_stock_transfer_id');
            $table->foreign('inventory_stock_transfer_id', 'inv_transfer_item_transfer_fk')
                ->references('id')
                ->on('inventory_stock_transfers')
                ->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 14, 3)->unsigned();
            $table->decimal('unit_cost', 14, 4)->unsigned()->default(0);
            $table->timestamps();

            $table->index(['inventory_stock_transfer_id', 'ingredient_id'], 'transfer_item_lookup_idx');
        });
    }

    private function createStockAuditTables(): void
    {
        if (! Schema::hasTable('stock_audits')) {
            Schema::create('stock_audits', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->date('audit_date')->index();
                $table->enum('status', ['draft', 'completed'])->default('completed')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('stock_audit_items')) {
            return;
        }

        Schema::create('stock_audit_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('stock_audit_id')->constrained('stock_audits')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->decimal('system_quantity', 14, 3)->default(0);
            $table->decimal('actual_quantity', 14, 3)->default(0);
            $table->decimal('difference_quantity', 14, 3)->default(0);
            $table->decimal('unit_cost', 14, 4)->unsigned()->default(0);
            $table->decimal('impact_cost', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['stock_audit_id', 'ingredient_id'], 'stock_audit_item_lookup_idx');
        });
    }

    private function createRecipeVersionTables(): void
    {
        if (! Schema::hasTable('recipe_versions')) {
            Schema::create('recipe_versions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('variant_name', 60)->default('default');
                $table->unsignedInteger('version_number')->default(1);
                $table->boolean('is_active')->default(true)->index();
                $table->boolean('is_semi_finished')->default(false)->index();
                $table->decimal('waste_percentage', 5, 2)->unsigned()->default(0);
                $table->decimal('loss_percentage', 5, 2)->unsigned()->default(0);
                $table->decimal('yield_quantity', 14, 3)->unsigned()->default(1);
                $table->decimal('total_cost', 14, 4)->unsigned()->default(0);
                $table->decimal('selling_price', 14, 2)->unsigned()->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['product_id', 'variant_name', 'version_number'], 'recipe_version_unique_idx');
                $table->index(['product_id', 'variant_name', 'is_active'], 'recipe_version_active_idx');
                $table->index(['is_semi_finished', 'is_active'], 'recipe_semifinished_active_idx');
            });
        }

        if (Schema::hasTable('recipe_version_items')) {
            return;
        }

        Schema::create('recipe_version_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipe_version_id')->constrained('recipe_versions')->cascadeOnDelete();
            $table->enum('item_type', ['ingredient', 'recipe'])->default('ingredient')->index();
            $table->foreignId('ingredient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('component_recipe_version_id')
                ->nullable()
                ->constrained('recipe_versions')
                ->nullOnDelete();
            $table->decimal('quantity_required', 14, 3)->unsigned();
            $table->timestamps();

            $table->index(['recipe_version_id', 'item_type'], 'recipe_item_type_idx');
        });
    }

    private function createProductionBatchTables(): void
    {
        if (! Schema::hasTable('production_batches')) {
            Schema::create('production_batches', function (Blueprint $table): void {
                $table->id();
                $table->string('batch_number')->unique();
                $table->foreignId('recipe_version_id')->constrained('recipe_versions')->restrictOnDelete();
                $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('produced_quantity', 14, 3)->unsigned();
                $table->decimal('remaining_quantity', 14, 3)->unsigned();
                $table->decimal('unit_cost', 14, 4)->unsigned()->default(0);
                $table->decimal('total_cost', 14, 2)->unsigned()->default(0);
                $table->date('expiry_date')->nullable()->index();
                $table->enum('status', ['active', 'consumed', 'cancelled'])->default('active')->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['recipe_version_id', 'warehouse_id', 'status'], 'production_batch_recipe_idx');
            });
        }

        if (Schema::hasTable('production_batch_consumptions')) {
            return;
        }

        Schema::create('production_batch_consumptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->unsignedBigInteger('consumed_by_recipe_version_id')->nullable();
            $table->foreign('consumed_by_recipe_version_id', 'prod_batch_cons_recipe_ver_fk')
                ->references('id')
                ->on('recipe_versions')
                ->nullOnDelete();
            $table->foreignId('consumed_by_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('quantity', 14, 3)->unsigned();
            $table->timestamps();

            $table->index(['production_batch_id', 'created_at'], 'production_batch_consumption_idx');
        });
    }

    private function upgradeIngredientsTable(): void
    {
        if (! Schema::hasTable('ingredients')) {
            return;
        }

        $hasUnitId = Schema::hasColumn('ingredients', 'unit_id');
        $hasSupplierId = Schema::hasColumn('ingredients', 'supplier_id');
        $hasCost = Schema::hasColumn('ingredients', 'cost');
        $hasQuantity = Schema::hasColumn('ingredients', 'quantity');
        $hasThreshold = Schema::hasColumn('ingredients', 'threshold');
        $hasDefaultWarehouseId = Schema::hasColumn('ingredients', 'default_warehouse_id');
        $hasCostMethod = Schema::hasColumn('ingredients', 'cost_method');
        $hasExpiryDate = Schema::hasColumn('ingredients', 'expiry_date');
        $hasExpiryAlertDays = Schema::hasColumn('ingredients', 'expiry_alert_days');

        if (
            $hasUnitId &&
            $hasSupplierId &&
            $hasCost &&
            $hasQuantity &&
            $hasThreshold &&
            $hasDefaultWarehouseId &&
            $hasCostMethod &&
            $hasExpiryDate &&
            $hasExpiryAlertDays
        ) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) use (
            $hasUnitId,
            $hasSupplierId,
            $hasCost,
            $hasQuantity,
            $hasThreshold,
            $hasDefaultWarehouseId,
            $hasCostMethod,
            $hasExpiryDate,
            $hasExpiryAlertDays,
        ): void {
            if (! $hasUnitId && Schema::hasTable('units')) {
                $table->foreignId('unit_id')->nullable()->after('unit')->constrained()->nullOnDelete();
            }

            if (! $hasSupplierId && Schema::hasTable('suppliers')) {
                $table->foreignId('supplier_id')->nullable()->after('name')->constrained()->nullOnDelete();
            }

            if (! $hasCost) {
                $table->decimal('cost', 14, 4)->unsigned()->default(0)->after('reorder_level');
            }

            if (! $hasQuantity) {
                $table->decimal('quantity', 14, 3)->unsigned()->default(0)->after('current_stock');
            }

            if (! $hasThreshold) {
                $table->decimal('threshold', 14, 3)->unsigned()->default(0)->after('reorder_level');
            }

            if (! $hasDefaultWarehouseId && Schema::hasTable('warehouses')) {
                $table->foreignId('default_warehouse_id')->nullable()->after('supplier_id')->constrained('warehouses')->nullOnDelete();
            }

            if (! $hasCostMethod) {
                $table->enum('cost_method', ['fifo', 'average'])->default('average')->after('cost');
            }

            if (! $hasExpiryDate) {
                $table->date('expiry_date')->nullable()->after('threshold');
            }

            if (! $hasExpiryAlertDays) {
                $table->unsignedInteger('expiry_alert_days')->default(7)->after('expiry_date');
            }
        });
    }

    private function upgradePurchasesTables(): void
    {
        if (Schema::hasTable('purchases') && ! Schema::hasColumn('purchases', 'warehouse_id') && Schema::hasTable('warehouses')) {
            Schema::table('purchases', function (Blueprint $table): void {
                $table->foreignId('warehouse_id')->nullable()->after('supplier_id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasTable('purchase_items')) {
            return;
        }

        $hasWarehouse = Schema::hasColumn('purchase_items', 'warehouse_id');
        $hasExpiryDate = Schema::hasColumn('purchase_items', 'expiry_date');

        if ($hasWarehouse && $hasExpiryDate) {
            return;
        }

        Schema::table('purchase_items', function (Blueprint $table) use ($hasWarehouse, $hasExpiryDate): void {
            if (! $hasWarehouse && Schema::hasTable('warehouses')) {
                $table->foreignId('warehouse_id')->nullable()->after('ingredient_id')->constrained()->nullOnDelete();
            }

            if (! $hasExpiryDate) {
                $table->date('expiry_date')->nullable()->after('quantity');
            }
        });
    }

    private function upgradeInventoryLogsTable(): void
    {
        if (! Schema::hasTable('inventory_stock_logs')) {
            return;
        }

        $hasWarehouseId = Schema::hasColumn('inventory_stock_logs', 'warehouse_id');
        $hasAction = Schema::hasColumn('inventory_stock_logs', 'action');
        $hasUnitCost = Schema::hasColumn('inventory_stock_logs', 'unit_cost');
        $hasReferenceType = Schema::hasColumn('inventory_stock_logs', 'reference_type');
        $hasReferenceId = Schema::hasColumn('inventory_stock_logs', 'reference_id');
        $hasOccurredAt = Schema::hasColumn('inventory_stock_logs', 'occurred_at');

        if ($hasWarehouseId && $hasAction && $hasUnitCost && $hasReferenceType && $hasReferenceId && $hasOccurredAt) {
            return;
        }

        Schema::table('inventory_stock_logs', function (Blueprint $table) use (
            $hasWarehouseId,
            $hasAction,
            $hasUnitCost,
            $hasReferenceType,
            $hasReferenceId,
            $hasOccurredAt,
        ): void {
            if (! $hasWarehouseId && Schema::hasTable('warehouses')) {
                $table->foreignId('warehouse_id')->nullable()->after('ingredient_id')->constrained()->nullOnDelete();
            }

            if (! $hasAction) {
                $table->string('action', 40)->default('adjust')->after('adjustment_type')->index();
            }

            if (! $hasUnitCost) {
                $table->decimal('unit_cost', 14, 4)->unsigned()->nullable()->after('quantity');
            }

            if (! $hasReferenceType) {
                $table->string('reference_type', 50)->nullable()->after('note');
            }

            if (! $hasReferenceId) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }

            if (! $hasOccurredAt) {
                $table->timestamp('occurred_at')->nullable()->after('created_at')->index();
            }
        });
    }

    private function upgradeOrdersTables(): void
    {
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'inventory_deducted_at')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->timestamp('inventory_deducted_at')->nullable()->after('status')->index();
            });
        }

        if (! Schema::hasTable('order_items')) {
            return;
        }

        $hasVariant = Schema::hasColumn('order_items', 'variant_name');
        $hasRecipeVersion = Schema::hasColumn('order_items', 'recipe_version_id');

        if ($hasVariant && $hasRecipeVersion) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) use ($hasVariant, $hasRecipeVersion): void {
            if (! $hasVariant) {
                $table->string('variant_name', 60)->default('default')->after('product_name')->index();
            }

            if (! $hasRecipeVersion && Schema::hasTable('recipe_versions')) {
                $table->foreignId('recipe_version_id')->nullable()->after('product_id')->constrained('recipe_versions')->nullOnDelete();
            }
        });
    }

    private function seedUnits(): void
    {
        if (! Schema::hasTable('units')) {
            return;
        }

        $now = now();
        $units = [
            ['name' => 'Kilogram', 'code' => 'kg', 'family' => 'weight', 'base_factor' => 1000],
            ['name' => 'Gram', 'code' => 'gram', 'family' => 'weight', 'base_factor' => 1],
            ['name' => 'Liter', 'code' => 'liter', 'family' => 'volume', 'base_factor' => 1000],
            ['name' => 'Milliliter', 'code' => 'ml', 'family' => 'volume', 'base_factor' => 1],
            ['name' => 'Piece', 'code' => 'piece', 'family' => 'count', 'base_factor' => 1],
        ];

        foreach ($units as $unit) {
            $exists = DB::table('units')->where('code', $unit['code'])->exists();

            if ($exists) {
                continue;
            }

            DB::table('units')->insert([
                'name' => $unit['name'],
                'code' => $unit['code'],
                'family' => $unit['family'],
                'base_factor' => $unit['base_factor'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedDefaultWarehouse(): ?int
    {
        if (! Schema::hasTable('warehouses')) {
            return null;
        }

        $default = DB::table('warehouses')
            ->where('is_default', true)
            ->first();

        if ($default) {
            return (int) $default->id;
        }

        $existing = DB::table('warehouses')->orderBy('id')->first();

        if ($existing) {
            DB::table('warehouses')->where('id', $existing->id)->update([
                'is_default' => true,
                'updated_at' => now(),
            ]);

            return (int) $existing->id;
        }

        $id = DB::table('warehouses')->insertGetId([
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
            'location' => null,
            'notes' => null,
            'is_active' => true,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (int) $id;
    }

    private function backfillIngredientMetadata(?int $defaultWarehouseId): void
    {
        if (! Schema::hasTable('ingredients')) {
            return;
        }

        if (Schema::hasColumn('ingredients', 'quantity') && Schema::hasColumn('ingredients', 'current_stock')) {
            DB::table('ingredients')
                ->where(function ($query): void {
                    $query->whereNull('quantity')->orWhere('quantity', 0);
                })
                ->update(['quantity' => DB::raw('current_stock')]);
        }

        if (Schema::hasColumn('ingredients', 'threshold') && Schema::hasColumn('ingredients', 'reorder_level')) {
            DB::table('ingredients')
                ->where(function ($query): void {
                    $query->whereNull('threshold')->orWhere('threshold', 0);
                })
                ->update(['threshold' => DB::raw('reorder_level')]);
        }

        if ($defaultWarehouseId && Schema::hasColumn('ingredients', 'default_warehouse_id')) {
            DB::table('ingredients')
                ->whereNull('default_warehouse_id')
                ->update(['default_warehouse_id' => $defaultWarehouseId]);
        }

        if (! Schema::hasColumn('ingredients', 'unit_id') || ! Schema::hasColumn('ingredients', 'unit')) {
            return;
        }

        $unitIdByCode = DB::table('units')->pluck('id', 'code');

        $unitAliases = [
            'kg' => ['kg', 'kilogram', 'kilograms'],
            'gram' => ['g', 'gram', 'grams'],
            'liter' => ['l', 'liter', 'liters'],
            'ml' => ['ml', 'milliliter', 'milliliters'],
            'piece' => ['pcs', 'pc', 'piece', 'pieces'],
        ];

        foreach ($unitAliases as $code => $aliases) {
            $unitId = $unitIdByCode[$code] ?? null;

            if (! $unitId) {
                continue;
            }

            DB::table('ingredients')
                ->whereNull('unit_id')
                ->whereIn(DB::raw('LOWER(unit)'), $aliases)
                ->update(['unit_id' => $unitId]);
        }

        $pieceId = $unitIdByCode['piece'] ?? null;

        if ($pieceId) {
            DB::table('ingredients')
                ->whereNull('unit_id')
                ->update(['unit_id' => $pieceId]);
        }
    }

    private function backfillWarehouseStocks(?int $defaultWarehouseId): void
    {
        if (! $defaultWarehouseId || ! Schema::hasTable('ingredient_warehouse_stocks') || ! Schema::hasTable('ingredients')) {
            return;
        }

        $ingredients = DB::table('ingredients')
            ->select(['id', 'current_stock', 'cost'])
            ->get();

        foreach ($ingredients as $ingredient) {
            $exists = DB::table('ingredient_warehouse_stocks')
                ->where('ingredient_id', $ingredient->id)
                ->where('warehouse_id', $defaultWarehouseId)
                ->exists();

            if ($exists) {
                continue;
            }

            $quantity = round((float) ($ingredient->current_stock ?? 0), 3);
            $cost = round((float) ($ingredient->cost ?? 0), 4);

            DB::table('ingredient_warehouse_stocks')->insert([
                'ingredient_id' => $ingredient->id,
                'warehouse_id' => $defaultWarehouseId,
                'quantity' => $quantity,
                'average_cost' => $cost,
                'last_purchase_cost' => $cost,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function backfillOpeningInventoryBatches(?int $defaultWarehouseId): void
    {
        if (! $defaultWarehouseId || ! Schema::hasTable('inventory_batches') || ! Schema::hasTable('ingredients')) {
            return;
        }

        $hasAnyBatch = DB::table('inventory_batches')->exists();

        if ($hasAnyBatch) {
            return;
        }

        $ingredients = DB::table('ingredients')
            ->select(['id', 'current_stock', 'cost', 'expiry_date'])
            ->get();

        foreach ($ingredients as $ingredient) {
            $quantity = round((float) ($ingredient->current_stock ?? 0), 3);

            if ($quantity <= 0) {
                continue;
            }

            $unitCost = round((float) ($ingredient->cost ?? 0), 4);

            DB::table('inventory_batches')->insert([
                'ingredient_id' => $ingredient->id,
                'warehouse_id' => $defaultWarehouseId,
                'purchase_item_id' => null,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => round($quantity * $unitCost, 2),
                'expiry_date' => $ingredient->expiry_date,
                'received_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
