<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientWarehouseStock;
use App\Models\InventoryBatch;
use App\Models\InventoryStockLog;
use App\Models\InventoryStockTransfer;
use App\Models\Order;
use App\Models\ProductionBatch;
use App\Models\ProductionBatchConsumption;
use App\Models\RecipeItem;
use App\Models\RecipeVersion;
use App\Models\RecipeVersionItem;
use App\Models\StockAudit;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(private readonly RecipeAnalyticsService $recipeAnalyticsService) {}

    public function defaultWarehouseId(): int
    {
        $warehouse = Warehouse::query()
            ->where('is_default', true)
            ->orWhere('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();

        if ($warehouse) {
            return (int) $warehouse->id;
        }

        return (int) Warehouse::query()->create([
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
            'is_active' => true,
            'is_default' => true,
        ])->id;
    }

    public function mainWarehouseId(): int
    {
        $mainWarehouse = Warehouse::query()
            ->where('code', 'MAIN')
            ->orderBy('id')
            ->first();

        if ($mainWarehouse) {
            return (int) $mainWarehouse->id;
        }

        $defaultWarehouse = Warehouse::query()
            ->where('is_default', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();

        if ($defaultWarehouse) {
            return (int) $defaultWarehouse->id;
        }

        return $this->defaultWarehouseId();
    }

    public function branchWarehouseId(): int
    {
        $branchWarehouse = Warehouse::query()
            ->where('code', 'BRANCH')
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if (! $branchWarehouse) {
            $branchWarehouse = Warehouse::query()
                ->where('code', 'BRANCH')
                ->orderBy('id')
                ->first();
        }

        if ($branchWarehouse) {
            return (int) $branchWarehouse->id;
        }

        $mainWarehouseId = $this->mainWarehouseId();

        $candidate = Warehouse::query()
            ->where('is_active', true)
            ->where('id', '!=', $mainWarehouseId)
            ->orderBy('id')
            ->first();

        if ($candidate) {
            return (int) $candidate->id;
        }

        return $mainWarehouseId;
    }

    public function orderConsumptionWarehouseId(): int
    {
        return $this->branchWarehouseId();
    }

    public function activeWarehouses(): Collection
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orWhere('is_default', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get(['id', 'name', 'code', 'is_default']);

        if ($warehouses->isNotEmpty()) {
            return $warehouses;
        }

        $this->defaultWarehouseId();

        return Warehouse::query()
            ->where('is_active', true)
            ->orWhere('is_default', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get(['id', 'name', 'code', 'is_default']);
    }

    public function validateOrderStock(array $items, ?int $warehouseId = null, bool $requireRecipe = false): array
    {
        $requirements = $this->buildOrderRequirements($items);

        $issues = [];

        if ($requireRecipe) {
            foreach ($requirements['missing_products'] as $missingProductId) {
                $productName = DB::table('products')
                    ->where('id', (int) $missingProductId)
                    ->value('name');

                $issues[] = __('messages.errors.product_requires_recipe', [
                    'name' => $productName ?: ('#' . (int) $missingProductId),
                ]);
            }
        }

        $stockWarehouseId = $warehouseId ?: null;

        foreach ($requirements['ingredients'] as $ingredientId => $requiredQty) {
            $ingredient = Ingredient::query()->find($ingredientId);

            if (! $ingredient) {
                $issues[] = __('messages.errors.recipe_ingredient_not_found');
                continue;
            }

            $ingredientWarehouseId = $stockWarehouseId
                ? (int) $stockWarehouseId
                : (int) ($ingredient->default_warehouse_id ?: $this->defaultWarehouseId());

            $stock = IngredientWarehouseStock::query()
                ->where('ingredient_id', $ingredientId)
                ->where('warehouse_id', $ingredientWarehouseId)
                ->first();

            $available = (float) ($stock?->quantity ?? 0);

            if ($available + 0.000001 < (float) $requiredQty) {
                $issues[] = __('messages.errors.insufficient_ingredient_stock', [
                    'name' => $ingredient->name,
                ]);
            }
        }

        foreach ($requirements['components'] as $recipeVersionId => $requiredQty) {
            $availableQuery = ProductionBatch::query()
                ->where('recipe_version_id', $recipeVersionId)
                ->where('status', 'active');

            if ($stockWarehouseId) {
                $availableQuery->where('warehouse_id', (int) $stockWarehouseId);
            }

            $available = (float) $availableQuery->sum('remaining_quantity');

            if ($available + 0.000001 < (float) $requiredQty) {
                $component = RecipeVersion::query()->find($recipeVersionId);
                $issues[] = __('messages.errors.insufficient_component_batch_stock', [
                    'name' => $component?->name ?? ('#' . $recipeVersionId),
                ]);
            }
        }

        return $issues;
    }

    public function deductInventoryForOrder(Order $order, bool $strict = true): void
    {
        $runner = function () use ($order, $strict): void {
            $managedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($managedOrder->inventory_deducted_at) {
                return;
            }

            $orderItems = $managedOrder->items()
                ->get(['recipe_version_id', 'product_id', 'quantity'])
                ->map(static fn($item): array => [
                    'recipe_version_id' => $item->recipe_version_id ? (int) $item->recipe_version_id : null,
                    'product_id' => (int) $item->product_id,
                    'quantity' => (int) $item->quantity,
                ])
                ->all();

            $requirements = $this->buildOrderRequirements(
                $orderItems,
            );

            $orderWarehouseId = $this->orderConsumptionWarehouseId();

            $issues = $this->validateOrderStock(
                $orderItems,
                warehouseId: $orderWarehouseId,
                requireRecipe: true,
            );

            if (! empty($issues) && $strict) {
                throw ValidationException::withMessages([
                    'items' => $issues,
                ]);
            }

            foreach ($requirements['ingredients'] as $ingredientId => $requiredQty) {
                $ingredient = Ingredient::query()
                    ->whereKey($ingredientId)
                    ->lockForUpdate()
                    ->first();

                if (! $ingredient) {
                    if ($strict) {
                        throw ValidationException::withMessages([
                            'items' => [__('messages.errors.recipe_ingredient_not_found')],
                        ]);
                    }

                    continue;
                }

                $this->deductIngredientStock(
                    ingredient: $ingredient,
                    quantity: (float) $requiredQty,
                    warehouseId: $orderWarehouseId,
                    action: 'deduct',
                    adjustmentType: 'out',
                    note: __('messages.notes.consumed_by_order', ['order_number' => $managedOrder->order_number]),
                    referenceType: Order::class,
                    referenceId: (int) $managedOrder->getKey(),
                    strict: $strict,
                );
            }

            foreach ($requirements['components'] as $componentVersionId => $requiredQty) {
                $this->consumeComponentBatch(
                    componentRecipeVersionId: (int) $componentVersionId,
                    quantity: (float) $requiredQty,
                    orderId: (int) $managedOrder->getKey(),
                    strict: $strict,
                    warehouseId: $orderWarehouseId,
                );
            }

            $managedOrder->update([
                'inventory_deducted_at' => now(),
            ]);
        };

        if (DB::transactionLevel() > 0) {
            $runner();
            return;
        }

        DB::transaction($runner);
    }

    public function deductInventoryForProductItems(
        array $items,
        string $note,
        ?string $referenceType = null,
        ?int $referenceId = null,
        bool $strict = true,
    ): void {
        $runner = function () use ($items, $note, $referenceType, $referenceId, $strict): void {
            $normalizedItems = collect($items)
                ->map(static function (array $item): array {
                    return [
                        'recipe_version_id' => isset($item['recipe_version_id']) ? (int) $item['recipe_version_id'] : null,
                        'product_id' => (int) ($item['product_id'] ?? 0),
                        'quantity' => (float) ($item['quantity'] ?? 0),
                    ];
                })
                ->filter(static fn(array $item): bool => $item['product_id'] > 0 && $item['quantity'] > 0)
                ->values()
                ->all();

            if (empty($normalizedItems)) {
                return;
            }

            $requirements = $this->buildOrderRequirements($normalizedItems);
            $issues = $this->validateOrderStock($normalizedItems);

            if (! empty($issues) && $strict) {
                throw ValidationException::withMessages([
                    'items' => $issues,
                ]);
            }

            foreach ($requirements['ingredients'] as $ingredientId => $requiredQty) {
                $ingredient = Ingredient::query()
                    ->whereKey((int) $ingredientId)
                    ->lockForUpdate()
                    ->first();

                if (! $ingredient) {
                    if ($strict) {
                        throw ValidationException::withMessages([
                            'items' => [__('messages.errors.recipe_ingredient_not_found')],
                        ]);
                    }

                    continue;
                }

                $warehouseId = (int) ($ingredient->default_warehouse_id ?: $this->defaultWarehouseId());

                $this->deductIngredientStock(
                    ingredient: $ingredient,
                    quantity: (float) $requiredQty,
                    warehouseId: $warehouseId,
                    action: 'deduct',
                    adjustmentType: 'out',
                    note: $note,
                    referenceType: $referenceType,
                    referenceId: $referenceId,
                    strict: $strict,
                );
            }

            foreach ($requirements['components'] as $componentVersionId => $requiredQty) {
                $this->consumeComponentBatch(
                    componentRecipeVersionId: (int) $componentVersionId,
                    quantity: (float) $requiredQty,
                    orderId: null,
                    strict: $strict,
                );
            }
        };

        if (DB::transactionLevel() > 0) {
            $runner();
            return;
        }

        DB::transaction($runner);
    }

    public function addStock(
        Ingredient $ingredient,
        float $quantity,
        float $unitCost,
        ?int $warehouseId = null,
        ?string $expiryDate = null,
        string $action = 'add',
        string $adjustmentType = 'in',
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
    ): void {
        if ($quantity <= 0) {
            return;
        }

        $warehouseId = $warehouseId ?: (int) ($ingredient->default_warehouse_id ?: $this->defaultWarehouseId());

        $runner = function () use (
            $ingredient,
            $quantity,
            $unitCost,
            $warehouseId,
            $expiryDate,
            $action,
            $adjustmentType,
            $note,
            $referenceType,
            $referenceId,
            $userId,
        ): void {
            $managedIngredient = Ingredient::query()
                ->whereKey($ingredient->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stock = $this->lockWarehouseStock($managedIngredient->id, $warehouseId);

            $previousWarehouseQty = (float) $stock->quantity;
            $newWarehouseQty = $previousWarehouseQty + $quantity;

            $previousAvg = (float) $stock->average_cost;
            $newAvg = $newWarehouseQty > 0
                ? (($previousWarehouseQty * $previousAvg) + ($quantity * $unitCost)) / $newWarehouseQty
                : $unitCost;

            $stock->update([
                'quantity' => round($newWarehouseQty, 3),
                'average_cost' => round($newAvg, 4),
                'last_purchase_cost' => round($unitCost, 4),
            ]);

            InventoryBatch::query()->create([
                'ingredient_id' => $managedIngredient->id,
                'warehouse_id' => $warehouseId,
                'purchase_item_id' => null,
                'quantity' => round($quantity, 3),
                'remaining_quantity' => round($quantity, 3),
                'unit_cost' => round($unitCost, 4),
                'total_cost' => round($quantity * $unitCost, 2),
                'expiry_date' => $expiryDate,
                'received_at' => now(),
            ]);

            $aggregate = $this->refreshIngredientAggregate($managedIngredient->id);

            InventoryStockLog::query()->create([
                'ingredient_id' => $managedIngredient->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId ?? Auth::id(),
                'adjustment_type' => $adjustmentType,
                'action' => $action,
                'quantity' => round($quantity, 3),
                'unit_cost' => round($unitCost, 4),
                'previous_stock' => round($aggregate['previous_quantity'], 3),
                'new_stock' => round($aggregate['new_quantity'], 3),
                'note' => $note,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'occurred_at' => now(),
            ]);
        };

        if (DB::transactionLevel() > 0) {
            $runner();
            return;
        }

        DB::transaction($runner);
    }

    public function adjustStock(
        Ingredient $ingredient,
        string $type,
        float $quantity,
        ?int $warehouseId = null,
        ?string $note = null,
        ?int $userId = null,
    ): void {
        $warehouseId = $warehouseId ?: (int) ($ingredient->default_warehouse_id ?: $this->defaultWarehouseId());

        if (! in_array($type, ['in', 'out', 'set'], true)) {
            throw ValidationException::withMessages([
                'adjustment_type' => __('validation.in', ['attribute' => 'adjustment_type']),
            ]);
        }

        if ($type === 'in') {
            $this->addStock(
                ingredient: $ingredient,
                quantity: $quantity,
                unitCost: (float) $ingredient->cost,
                warehouseId: $warehouseId,
                expiryDate: null,
                action: 'adjust',
                adjustmentType: 'in',
                note: $note,
                userId: $userId,
            );

            return;
        }

        if ($type === 'out') {
            $this->deductIngredientStock(
                ingredient: $ingredient,
                quantity: $quantity,
                warehouseId: $warehouseId,
                action: 'adjust',
                adjustmentType: 'out',
                note: $note,
                strict: true,
                userId: $userId,
            );

            return;
        }

        $runner = function () use ($ingredient, $quantity, $warehouseId, $note, $userId): void {
            $managedIngredient = Ingredient::query()
                ->whereKey($ingredient->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stock = $this->lockWarehouseStock($managedIngredient->id, $warehouseId);

            $previousWarehouseQty = (float) $stock->quantity;
            $difference = $quantity - $previousWarehouseQty;

            $stock->update([
                'quantity' => round(max($quantity, 0), 3),
            ]);

            $aggregate = $this->refreshIngredientAggregate($managedIngredient->id);

            InventoryStockLog::query()->create([
                'ingredient_id' => $managedIngredient->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId ?? Auth::id(),
                'adjustment_type' => 'set',
                'action' => 'adjust',
                'quantity' => round(abs($difference), 3),
                'unit_cost' => (float) $stock->average_cost,
                'previous_stock' => round($aggregate['previous_quantity'], 3),
                'new_stock' => round($aggregate['new_quantity'], 3),
                'note' => $note,
                'reference_type' => null,
                'reference_id' => null,
                'occurred_at' => now(),
            ]);
        };

        if (DB::transactionLevel() > 0) {
            $runner();
            return;
        }

        DB::transaction($runner);
    }

    public function transferStock(
        int $fromWarehouseId,
        int $toWarehouseId,
        array $items,
        ?int $userId = null,
        ?string $notes = null,
    ): InventoryStockTransfer {
        if ($fromWarehouseId === $toWarehouseId) {
            throw ValidationException::withMessages([
                'to_warehouse_id' => __('messages.errors.transfer_same_warehouse'),
            ]);
        }

        return DB::transaction(function () use ($fromWarehouseId, $toWarehouseId, $items, $userId, $notes): InventoryStockTransfer {
            $transfer = InventoryStockTransfer::query()->create([
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'user_id' => $userId ?? Auth::id(),
                'status' => 'completed',
                'notes' => $notes,
                'transferred_at' => now(),
            ]);

            foreach ($items as $item) {
                $ingredient = Ingredient::query()->findOrFail((int) $item['ingredient_id']);
                $quantity = (float) $item['quantity'];

                if ($quantity <= 0) {
                    continue;
                }

                $unitCost = $this->deductIngredientStock(
                    ingredient: $ingredient,
                    quantity: $quantity,
                    warehouseId: $fromWarehouseId,
                    action: 'transfer',
                    adjustmentType: 'out',
                    note: __('messages.notes.transfer_out', ['name' => $transfer->id]),
                    referenceType: InventoryStockTransfer::class,
                    referenceId: (int) $transfer->id,
                    strict: true,
                    userId: $userId,
                );

                $this->addStock(
                    ingredient: $ingredient,
                    quantity: $quantity,
                    unitCost: $unitCost,
                    warehouseId: $toWarehouseId,
                    expiryDate: null,
                    action: 'transfer',
                    adjustmentType: 'in',
                    note: __('messages.notes.transfer_in', ['name' => $transfer->id]),
                    referenceType: InventoryStockTransfer::class,
                    referenceId: (int) $transfer->id,
                    userId: $userId,
                );

                $transfer->items()->create([
                    'ingredient_id' => $ingredient->id,
                    'quantity' => round($quantity, 3),
                    'unit_cost' => round($unitCost, 4),
                ]);
            }

            return $transfer->load(['fromWarehouse', 'toWarehouse', 'items.ingredient']);
        });
    }

    public function createStockAudit(int $warehouseId, array $actualQuantitiesByIngredientId, ?int $userId = null, ?string $note = null): StockAudit
    {
        return DB::transaction(function () use ($warehouseId, $actualQuantitiesByIngredientId, $userId, $note): StockAudit {
            $audit = StockAudit::query()->create([
                'warehouse_id' => $warehouseId,
                'user_id' => $userId ?? Auth::id(),
                'audit_date' => now()->toDateString(),
                'status' => 'completed',
                'notes' => $note,
            ]);

            foreach ($actualQuantitiesByIngredientId as $ingredientId => $actualQtyInput) {
                $ingredient = Ingredient::query()->whereKey((int) $ingredientId)->lockForUpdate()->first();

                if (! $ingredient) {
                    continue;
                }

                $stock = $this->lockWarehouseStock($ingredient->id, $warehouseId);

                $actualQty = round(max((float) $actualQtyInput, 0), 3);
                $systemQty = round((float) $stock->quantity, 3);
                $difference = round($actualQty - $systemQty, 3);

                if (abs($difference) > 0) {
                    $stock->update([
                        'quantity' => $actualQty,
                    ]);
                }

                $aggregate = $this->refreshIngredientAggregate($ingredient->id);

                $unitCost = (float) $stock->average_cost;

                $audit->items()->create([
                    'ingredient_id' => $ingredient->id,
                    'system_quantity' => $systemQty,
                    'actual_quantity' => $actualQty,
                    'difference_quantity' => $difference,
                    'unit_cost' => round($unitCost, 4),
                    'impact_cost' => round($difference * $unitCost, 2),
                ]);

                if (abs($difference) > 0) {
                    InventoryStockLog::query()->create([
                        'ingredient_id' => $ingredient->id,
                        'warehouse_id' => $warehouseId,
                        'user_id' => $userId ?? Auth::id(),
                        'adjustment_type' => 'set',
                        'action' => 'audit',
                        'quantity' => abs($difference),
                        'unit_cost' => round($unitCost, 4),
                        'previous_stock' => round($aggregate['previous_quantity'], 3),
                        'new_stock' => round($aggregate['new_quantity'], 3),
                        'note' => __('messages.notes.stock_audit_adjustment', ['audit_id' => $audit->id]),
                        'reference_type' => StockAudit::class,
                        'reference_id' => (int) $audit->id,
                        'occurred_at' => now(),
                    ]);
                }
            }

            return $audit->load(['warehouse', 'items.ingredient']);
        });
    }

    public function produceSemiFinishedBatch(
        RecipeVersion $recipeVersion,
        float $producedQuantity,
        ?int $warehouseId = null,
        ?string $expiryDate = null,
        ?string $notes = null,
        ?int $userId = null,
    ): ProductionBatch {
        if (! $recipeVersion->is_semi_finished) {
            throw ValidationException::withMessages([
                'recipe_version_id' => __('messages.errors.recipe_not_semi_finished'),
            ]);
        }

        if ($producedQuantity <= 0) {
            throw ValidationException::withMessages([
                'produced_quantity' => __('validation.min.numeric', ['attribute' => 'produced_quantity', 'min' => 0.001]),
            ]);
        }

        $warehouseId = $warehouseId ?: $this->defaultWarehouseId();

        return DB::transaction(function () use ($recipeVersion, $producedQuantity, $warehouseId, $expiryDate, $notes, $userId): ProductionBatch {
            $recipeVersion->loadMissing(['items.ingredient', 'items.componentRecipeVersion']);

            $yield = max((float) $recipeVersion->yield_quantity, 1);
            $multiplier = $producedQuantity / $yield;

            foreach ($recipeVersion->items as $item) {
                $requiredQty = (float) $item->quantity_required * $multiplier;

                if ($requiredQty <= 0) {
                    continue;
                }

                if ($item->item_type === 'ingredient' && $item->ingredient) {
                    $this->deductIngredientStock(
                        ingredient: $item->ingredient,
                        quantity: $requiredQty,
                        warehouseId: (int) ($item->ingredient->default_warehouse_id ?: $warehouseId),
                        action: 'production_consume',
                        adjustmentType: 'out',
                        note: __('messages.notes.production_consumption', ['name' => $recipeVersion->name]),
                        strict: true,
                        userId: $userId,
                    );
                }

                if ($item->item_type === 'recipe' && $item->componentRecipeVersion) {
                    $this->consumeComponentBatch(
                        componentRecipeVersionId: (int) $item->componentRecipeVersion->id,
                        quantity: $requiredQty,
                        orderId: null,
                        strict: true,
                        warehouseId: (int) $warehouseId,
                    );
                }
            }

            $versionCost = $this->recipeAnalyticsService->calculateRecipeVersionCost($recipeVersion);
            $totalCost = round($versionCost * $multiplier, 2);
            $unitCost = $producedQuantity > 0 ? round($totalCost / $producedQuantity, 4) : 0;

            $batch = ProductionBatch::query()->create([
                'batch_number' => 'PB-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 4)),
                'recipe_version_id' => $recipeVersion->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId ?? Auth::id(),
                'produced_quantity' => round($producedQuantity, 3),
                'remaining_quantity' => round($producedQuantity, 3),
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'expiry_date' => $expiryDate,
                'status' => 'active',
                'notes' => $notes,
            ]);

            return $batch->load(['recipeVersion', 'warehouse']);
        });
    }

    public function warehouseStockByIngredient(int $ingredientId): Collection
    {
        return IngredientWarehouseStock::query()
            ->with('warehouse:id,name')
            ->where('ingredient_id', $ingredientId)
            ->orderBy('warehouse_id')
            ->get();
    }

    private function buildOrderRequirements(array $items): array
    {
        $ingredients = [];
        $components = [];
        $missingProducts = [];

        foreach ($items as $item) {
            $recipeVersionId = (int) ($item['recipe_version_id'] ?? 0);
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $version = null;

            if ($recipeVersionId > 0) {
                $version = RecipeVersion::query()->find($recipeVersionId);
            }

            if (! $version && $productId > 0) {
                $version = $this->resolveActiveRecipeVersion($productId);
            }

            if ($version) {
                $this->collectRecipeVersionRequirements(
                    version: $version,
                    multiplier: $quantity,
                    ingredientBucket: $ingredients,
                    componentBucket: $components,
                );

                continue;
            }

            if ($productId <= 0) {
                continue;
            }

            $legacyItems = RecipeItem::query()
                ->where('product_id', $productId)
                ->get(['ingredient_id', 'quantity_required']);

            if ($legacyItems->isEmpty()) {
                $missingProducts[$productId] = true;
                continue;
            }

            foreach ($legacyItems as $legacyItem) {
                $ingredientId = (int) $legacyItem->ingredient_id;
                $ingredients[$ingredientId] = ($ingredients[$ingredientId] ?? 0) + ((float) $legacyItem->quantity_required * $quantity);
            }
        }

        return [
            'ingredients' => $ingredients,
            'components' => $components,
            'missing_products' => array_keys($missingProducts),
        ];
    }

    private function collectRecipeVersionRequirements(
        RecipeVersion $version,
        float $multiplier,
        array &$ingredientBucket,
        array &$componentBucket,
        int $depth = 0,
    ): void {
        if ($depth > 8) {
            return;
        }

        $version->loadMissing('items');

        $yield = max((float) $version->yield_quantity, 1);
        $wasteFactor = 1 + (((float) $version->waste_percentage) / 100);
        $lossFactor = 1 + (((float) $version->loss_percentage) / 100);
        $effectiveMultiplier = ($multiplier / $yield) * $wasteFactor * $lossFactor;

        /** @var EloquentCollection<int, RecipeVersionItem> $items */
        $items = $version->items;

        foreach ($items as $versionItem) {
            $required = (float) $versionItem->quantity_required * $effectiveMultiplier;

            if ($required <= 0) {
                continue;
            }

            if ($versionItem->item_type === 'ingredient' && $versionItem->ingredient_id) {
                $ingredientId = (int) $versionItem->ingredient_id;
                $ingredientBucket[$ingredientId] = ($ingredientBucket[$ingredientId] ?? 0) + $required;
            }

            if ($versionItem->item_type === 'recipe' && $versionItem->component_recipe_version_id) {
                $componentId = (int) $versionItem->component_recipe_version_id;
                $componentBucket[$componentId] = ($componentBucket[$componentId] ?? 0) + $required;
            }
        }
    }

    private function resolveActiveRecipeVersion(int $productId): ?RecipeVersion
    {
        return RecipeVersion::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('is_semi_finished', false)
            ->latest('id')
            ->first();
    }

    private function deductIngredientStock(
        Ingredient $ingredient,
        float $quantity,
        int $warehouseId,
        string $action,
        string $adjustmentType,
        ?string $note,
        ?string $referenceType = null,
        ?int $referenceId = null,
        bool $strict = true,
        ?int $userId = null,
    ): float {
        if ($quantity <= 0) {
            return 0;
        }

        $managedIngredient = Ingredient::query()
            ->whereKey($ingredient->id)
            ->lockForUpdate()
            ->firstOrFail();

        $stock = $this->lockWarehouseStock($managedIngredient->id, $warehouseId);

        $availableQty = (float) $stock->quantity;

        if ($availableQty + 0.000001 < $quantity) {
            if ($strict) {
                throw ValidationException::withMessages([
                    'items' => [__('messages.errors.insufficient_ingredient_stock', ['name' => $managedIngredient->name])],
                ]);
            }

            $quantity = $availableQty;
        }

        if ($quantity <= 0) {
            return 0;
        }

        [$consumedQty, $fifoCost] = $this->consumeIngredientBatches(
            ingredientId: (int) $managedIngredient->id,
            warehouseId: $warehouseId,
            quantity: $quantity,
            strict: $strict,
        );

        if ($consumedQty <= 0) {
            return 0;
        }

        $unitCost = $consumedQty > 0
            ? round($fifoCost / $consumedQty, 4)
            : 0;

        $stock->update([
            'quantity' => round(max($availableQty - $consumedQty, 0), 3),
        ]);

        $aggregate = $this->refreshIngredientAggregate($managedIngredient->id);

        InventoryStockLog::query()->create([
            'ingredient_id' => $managedIngredient->id,
            'warehouse_id' => $warehouseId,
            'user_id' => $userId ?? Auth::id(),
            'adjustment_type' => $adjustmentType,
            'action' => $action,
            'quantity' => round($consumedQty, 3),
            'unit_cost' => round($unitCost, 4),
            'previous_stock' => round($aggregate['previous_quantity'], 3),
            'new_stock' => round($aggregate['new_quantity'], 3),
            'note' => $note,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'occurred_at' => now(),
        ]);

        return round($unitCost, 4);
    }

    private function consumeIngredientBatches(int $ingredientId, int $warehouseId, float $quantity, bool $strict = true): array
    {
        $remaining = $quantity;
        $consumed = 0.0;
        $totalCost = 0.0;

        $batches = InventoryBatch::query()
            ->where('ingredient_id', $ingredientId)
            ->where('warehouse_id', $warehouseId)
            ->where('remaining_quantity', '>', 0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $batchRemaining = (float) $batch->remaining_quantity;

            if ($batchRemaining <= 0) {
                continue;
            }

            $take = min($batchRemaining, $remaining);
            $newRemaining = $batchRemaining - $take;

            $batch->update([
                'remaining_quantity' => round($newRemaining, 3),
            ]);

            $remaining -= $take;
            $consumed += $take;
            $totalCost += $take * (float) $batch->unit_cost;
        }

        if ($remaining > 0.000001 && $strict) {
            throw ValidationException::withMessages([
                'items' => [__('messages.errors.insufficient_ingredient_batches')],
            ]);
        }

        return [round($consumed, 3), round($totalCost, 4)];
    }

    private function consumeComponentBatch(
        int $componentRecipeVersionId,
        float $quantity,
        ?int $orderId,
        bool $strict = true,
        ?int $warehouseId = null,
    ): float {
        $remaining = $quantity;
        $consumed = 0.0;

        $batchesQuery = ProductionBatch::query()
            ->where('recipe_version_id', $componentRecipeVersionId)
            ->where('status', 'active')
            ->where('remaining_quantity', '>', 0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expiry_date')
            ->orderBy('id');

        if ($warehouseId) {
            $batchesQuery->where('warehouse_id', (int) $warehouseId);
        }

        $batches = $batchesQuery
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $batchRemaining = (float) $batch->remaining_quantity;

            if ($batchRemaining <= 0) {
                continue;
            }

            $take = min($batchRemaining, $remaining);
            $newRemaining = $batchRemaining - $take;

            $batch->update([
                'remaining_quantity' => round($newRemaining, 3),
                'status' => $newRemaining > 0 ? 'active' : 'consumed',
            ]);

            ProductionBatchConsumption::query()->create([
                'production_batch_id' => $batch->id,
                'consumed_by_recipe_version_id' => null,
                'consumed_by_order_id' => $orderId,
                'quantity' => round($take, 3),
            ]);

            $remaining -= $take;
            $consumed += $take;
        }

        if ($remaining > 0.000001 && $strict) {
            throw ValidationException::withMessages([
                'items' => [__('messages.errors.insufficient_component_batch_stock')],
            ]);
        }

        return round($consumed, 3);
    }

    private function lockWarehouseStock(int $ingredientId, int $warehouseId): IngredientWarehouseStock
    {
        $stock = IngredientWarehouseStock::query()
            ->where('ingredient_id', $ingredientId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            return $stock;
        }

        return IngredientWarehouseStock::query()->create([
            'ingredient_id' => $ingredientId,
            'warehouse_id' => $warehouseId,
            'quantity' => 0,
            'average_cost' => 0,
            'last_purchase_cost' => 0,
        ]);
    }

    private function refreshIngredientAggregate(int $ingredientId): array
    {
        $ingredient = Ingredient::query()
            ->whereKey($ingredientId)
            ->lockForUpdate()
            ->firstOrFail();

        $previous = (float) $ingredient->quantity;

        $stockRows = IngredientWarehouseStock::query()
            ->where('ingredient_id', $ingredientId)
            ->get(['quantity', 'average_cost']);

        $newQuantity = (float) $stockRows->sum('quantity');

        $weightedCostBase = $stockRows->sum(static function ($row): float {
            return (float) $row->quantity * (float) $row->average_cost;
        });

        $newCost = $newQuantity > 0
            ? $weightedCostBase / $newQuantity
            : (float) $ingredient->cost;

        $ingredient->update([
            'quantity' => round($newQuantity, 3),
            'current_stock' => round($newQuantity, 3),
            'cost' => round(max($newCost, 0), 4),
        ]);

        return [
            'previous_quantity' => $previous,
            'new_quantity' => $newQuantity,
        ];
    }
}
