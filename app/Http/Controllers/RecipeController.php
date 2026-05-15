<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\RecipeItem;
use App\Models\RecipeVersion;
use App\Models\RecipeVersionItem;
use App\Support\PdfExportRenderer;
use App\Services\InventoryService;
use App\Services\RecipeAnalyticsService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    private const RECIPES_PDF_LIMIT = 600;

    public function __construct(
        private readonly RecipeAnalyticsService $recipeAnalyticsService,
        private readonly InventoryService $inventoryService,
        private readonly PdfExportRenderer $pdfExportRenderer,
    ) {}

    public function index(): View
    {
        $products = Product::query()
            ->whereHas('recipeVersions', fn($query) => $query
                ->where('is_active', true)
                ->where('is_semi_finished', false))
            ->orderBy('name')
            ->paginate(12);

        $semiFinishedRecipes = RecipeVersion::query()
            ->whereNull('product_id')
            ->where('is_semi_finished', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'total_cost', 'yield_quantity']);

        return view('recipes.index', [
            'products' => $products,
            'semiFinishedRecipes' => $semiFinishedRecipes,
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $scope = strtolower(trim((string) $request->query('scope', 'products')));

        if (! in_array($scope, ['products', 'semi_finished'], true)) {
            $scope = 'products';
        }

        if ($scope === 'semi_finished') {
            $baseQuery = RecipeVersion::query()
                ->whereNull('product_id')
                ->where('is_semi_finished', true)
                ->where('is_active', true)
                ->orderBy('name');

            $totalCount = (clone $baseQuery)->count();

            $rows = $baseQuery
                ->limit(self::RECIPES_PDF_LIMIT)
                ->get(['id', 'name', 'yield_quantity', 'total_cost', 'notes'])
                ->map(static function (RecipeVersion $recipe): array {
                    $yield = max((float) $recipe->yield_quantity, 0.001);
                    $totalCost = (float) $recipe->total_cost;

                    return [
                        'name' => (string) $recipe->name,
                        'yield_quantity' => round((float) $recipe->yield_quantity, 3),
                        'cost_per_unit' => round($totalCost / $yield, 4),
                        'total_cost' => round($totalCost, 4),
                        'notes' => trim((string) ($recipe->notes ?? '')),
                    ];
                })
                ->values()
                ->all();

            $fallbackUrl = route('recipes.index') . '#semi';
        } else {
            $baseQuery = Product::query()
                ->whereHas('recipeVersions', fn($query) => $query
                    ->where('is_active', true)
                    ->where('is_semi_finished', false))
                ->with(['recipeVersions' => fn($query) => $query
                    ->where('is_active', true)
                    ->where('is_semi_finished', false)
                    ->orderByDesc('id')
                    ->select(['id', 'product_id', 'yield_quantity', 'total_cost', 'selling_price'])])
                ->orderBy('name');

            $totalCount = (clone $baseQuery)->count();

            $rows = $baseQuery
                ->limit(self::RECIPES_PDF_LIMIT)
                ->get(['id', 'name', 'price'])
                ->map(static function (Product $product): array {
                    $activeVersion = $product->recipeVersions->first();
                    $yield = max((float) ($activeVersion?->yield_quantity ?? 1), 0.001);
                    $recipeCost = (float) ($activeVersion?->total_cost ?? 0);
                    $sellingPrice = (float) ($activeVersion?->selling_price ?? $product->price);

                    return [
                        'name' => (string) $product->name,
                        'selling_price' => round($sellingPrice, 2),
                        'recipe_cost' => round($recipeCost, 4),
                        'yield_quantity' => round((float) ($activeVersion?->yield_quantity ?? 1), 3),
                        'cost_per_unit' => round($recipeCost / $yield, 4),
                    ];
                })
                ->values()
                ->all();

            $fallbackUrl = route('recipes.index');
        }

        $exportedCount = count($rows);
        $isTruncated = $totalCount > $exportedCount;
        $fileScope = $scope === 'semi_finished' ? 'semi-finished' : 'recipes';

        return $this->pdfExportRenderer->downloadPdfFromView(
            'recipes.exports.recipes-pdf',
            [
                'scope' => $scope,
                'rows' => $rows,
                'totalCount' => $totalCount,
                'exportedCount' => $exportedCount,
                'isTruncated' => $isTruncated,
                'generatedAt' => now(),
            ],
            'recipes-report-' . $fileScope . '-' . now()->format('Ymd_His') . '.pdf',
            $fallbackUrl
        );
    }

    public function create(): View
    {
        $availableProducts = Product::query()
            ->where('is_active', true)
            ->whereDoesntHave('recipeVersions', fn($query) => $query->where('is_semi_finished', false))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('recipes.create', [
            'availableProducts' => $availableProducts,
        ]);
    }

    public function createSemiFinished(): View
    {
        [$ingredients, $semiFinishedOptions] = $this->buildSemiFinishedBuilderData();

        $recipeWarehouseName = (string) (DB::table('warehouses')
            ->where('id', $this->inventoryService->orderConsumptionWarehouseId())
            ->value('name') ?? 'Branch Warehouse');

        return view('recipes.index', [
            'showSemiFinishedCreateForm' => true,
            'isSemiFinishedEdit' => false,
            'semiFinishedRecipe' => null,
            'semiFinishedFormAction' => route('recipes.semi-finished.store'),
            'semiFinishedFormMethod' => null,
            'semiFinishedInitialIngredientRows' => [],
            'semiFinishedInitialComponentRows' => [],
            'ingredients' => $ingredients,
            'semiFinishedOptions' => $semiFinishedOptions,
            'recipeWarehouseName' => $recipeWarehouseName,
        ]);
    }

    public function editSemiFinished(RecipeVersion $recipeVersion): View
    {
        if (! $recipeVersion->is_semi_finished || $recipeVersion->product_id) {
            abort(404);
        }

        $recipeVersion->load('items');

        $initialIngredientRows = $recipeVersion->items
            ->where('item_type', 'ingredient')
            ->filter(fn($item): bool => (int) $item->ingredient_id > 0)
            ->map(fn($item): array => [
                'ingredient_id' => (int) $item->ingredient_id,
                'quantity_required' => (float) $item->quantity_required,
            ])
            ->values()
            ->all();

        $initialComponentRows = $recipeVersion->items
            ->where('item_type', 'recipe')
            ->filter(fn($item): bool => (int) $item->component_recipe_version_id > 0)
            ->map(fn($item): array => [
                'component_recipe_version_id' => (int) $item->component_recipe_version_id,
                'quantity_required' => (float) $item->quantity_required,
            ])
            ->values()
            ->all();

        $existingIngredientIds = collect($initialIngredientRows)
            ->pluck('ingredient_id')
            ->filter()
            ->map(static fn($id): int => (int) $id)
            ->values()
            ->all();

        $existingComponentIds = collect($initialComponentRows)
            ->pluck('component_recipe_version_id')
            ->filter()
            ->map(static fn($id): int => (int) $id)
            ->values()
            ->all();

        [$ingredients, $semiFinishedOptions] = $this->buildSemiFinishedBuilderData(
            existingIngredientIds: $existingIngredientIds,
            existingComponentIds: $existingComponentIds,
            excludedSemiFinishedId: (int) $recipeVersion->id,
        );

        $recipeWarehouseName = (string) (DB::table('warehouses')
            ->where('id', $this->inventoryService->orderConsumptionWarehouseId())
            ->value('name') ?? 'Branch Warehouse');

        return view('recipes.index', [
            'showSemiFinishedCreateForm' => true,
            'isSemiFinishedEdit' => true,
            'semiFinishedRecipe' => $recipeVersion,
            'semiFinishedFormAction' => route('recipes.semi-finished.update', $recipeVersion),
            'semiFinishedFormMethod' => 'PUT',
            'semiFinishedInitialIngredientRows' => $initialIngredientRows,
            'semiFinishedInitialComponentRows' => $initialComponentRows,
            'ingredients' => $ingredients,
            'semiFinishedOptions' => $semiFinishedOptions,
            'recipeWarehouseName' => $recipeWarehouseName,
        ]);
    }

    public function edit(Request $request, Product $product): View
    {
        $currentVersion = RecipeVersion::query()
            ->where('product_id', $product->id)
            ->where('is_semi_finished', false)
            ->latest('id')
            ->with(['items.ingredient:id,name,unit', 'items.componentRecipeVersion:id,name'])
            ->first();

        $existingIngredientRows = collect();
        $existingComponentRows = collect();

        if ($currentVersion) {
            $existingIngredientRows = $currentVersion->items
                ->where('item_type', 'ingredient')
                ->filter(fn($item): bool => (int) $item->ingredient_id > 0)
                ->map(fn($item): array => [
                    'ingredient_id' => (int) $item->ingredient_id,
                    'quantity_required' => (float) $item->quantity_required,
                ]);

            $existingComponentRows = $currentVersion->items
                ->where('item_type', 'recipe')
                ->filter(fn($item): bool => (int) $item->component_recipe_version_id > 0)
                ->map(fn($item): array => [
                    'component_recipe_version_id' => (int) $item->component_recipe_version_id,
                    'quantity_required' => (float) $item->quantity_required,
                ]);
        } else {
            $existingIngredientRows = $product->recipeItems()
                ->get(['ingredient_id', 'quantity_required'])
                ->map(fn($item): array => [
                    'ingredient_id' => (int) $item->ingredient_id,
                    'quantity_required' => (float) $item->quantity_required,
                ]);
        }

        $existingIngredientIds = $existingIngredientRows
            ->pluck('ingredient_id')
            ->filter(static fn($id): bool => (int) $id > 0)
            ->map(static fn($id): int => (int) $id)
            ->values()
            ->all();

        $existingComponentIds = $existingComponentRows
            ->pluck('component_recipe_version_id')
            ->filter(static fn($id): bool => (int) $id > 0)
            ->map(static fn($id): int => (int) $id)
            ->values()
            ->all();

        $recipeWarehouseId = $this->inventoryService->orderConsumptionWarehouseId();

        $recipeWarehouseName = (string) (DB::table('warehouses')
            ->where('id', $recipeWarehouseId)
            ->value('name') ?? 'Branch Warehouse');

        $ingredients = Ingredient::query()
            ->leftJoin('ingredient_warehouse_stocks as stock', function ($join) use ($recipeWarehouseId): void {
                $join->on('stock.ingredient_id', '=', 'ingredients.id')
                    ->where('stock.warehouse_id', '=', $recipeWarehouseId);
            })
            ->where('ingredients.is_active', true)
            ->orderBy('ingredients.name')
            ->get([
                'ingredients.id',
                'ingredients.name',
                'ingredients.unit',
                'ingredients.cost',
                DB::raw('COALESCE(stock.quantity, 0) as quantity'),
            ]);

        $semiFinishedOptions = RecipeVersion::query()
            ->whereNull('product_id')
            ->where('is_semi_finished', true)
            ->where(function ($query) use ($existingComponentIds): void {
                $query->where('is_active', true);

                if (! empty($existingComponentIds)) {
                    $query->orWhereIn('id', $existingComponentIds);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'total_cost', 'yield_quantity']);

        $recipeProductOptions = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $prefillYieldQuantity = max((float) $request->query('yield_quantity', 1), 0.001);
        $prefillWastePercentage = min(max((float) $request->query('waste_percentage', 0), 0), 100);
        $prefillLossPercentage = min(max((float) $request->query('loss_percentage', 0), 0), 100);

        return view('recipes.edit', [
            'product' => $product,
            'ingredients' => $ingredients,
            'semiFinishedOptions' => $semiFinishedOptions,
            'recipeProductOptions' => $recipeProductOptions,
            'currentVersion' => $currentVersion,
            'existingIngredientRows' => $existingIngredientRows->values(),
            'existingComponentRows' => $existingComponentRows->values(),
            'prefillYieldQuantity' => $prefillYieldQuantity,
            'prefillWastePercentage' => $prefillWastePercentage,
            'prefillLossPercentage' => $prefillLossPercentage,
            'recipeWarehouseName' => $recipeWarehouseName,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'is_semi_finished' => ['required', 'boolean'],
            'waste_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'loss_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'yield_quantity' => ['required', 'numeric', 'min:0.001'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'ingredient_rows' => ['nullable', 'array'],
            'ingredient_rows.*.ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'ingredient_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'component_rows' => ['nullable', 'array'],
            'component_rows.*.component_recipe_version_id' => ['nullable', 'integer', 'exists:recipe_versions,id'],
            'component_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'recipe_ingredients' => ['nullable', 'array'],
            'recipe_ingredients.*' => ['nullable', 'numeric', 'min:0'],
            'recipe_components' => ['nullable', 'array'],
            'recipe_components.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredientRows = $this->extractIngredientRows($validated);
        $componentRows = $this->extractComponentRows($validated);

        if (empty($ingredientRows) && empty($componentRows)) {
            throw ValidationException::withMessages([
                'ingredient_rows' => [__('messages.errors.recipe_requires_items')],
            ]);
        }

        $version = DB::transaction(function () use ($product, $validated, $ingredientRows, $componentRows): RecipeVersion {
            Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();

            $version = RecipeVersion::query()
                ->where('product_id', $product->id)
                ->where('is_semi_finished', false)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $version) {
                $version = RecipeVersion::query()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'is_active' => true,
                    'is_semi_finished' => false,
                    'waste_percentage' => 0,
                    'loss_percentage' => 0,
                    'yield_quantity' => 1,
                    'total_cost' => 0,
                    'selling_price' => round((float) $product->price, 2),
                    'notes' => null,
                ]);
            }

            RecipeVersion::query()
                ->where('product_id', $product->id)
                ->where('is_semi_finished', false)
                ->where('id', '!=', $version->id)
                ->update(['is_active' => false]);

            $version->update([
                'name' => $product->name,
                'is_active' => true,
                'is_semi_finished' => false,
                'waste_percentage' => round((float) ($validated['waste_percentage'] ?? 0), 2),
                'loss_percentage' => round((float) ($validated['loss_percentage'] ?? 0), 2),
                'yield_quantity' => round((float) $validated['yield_quantity'], 3),
                'total_cost' => 0,
                'selling_price' => round((float) ($validated['selling_price'] ?? $product->price), 2),
                'notes' => $validated['notes'] ?? null,
            ]);

            $version->items()->delete();

            $rows = [];

            foreach ($ingredientRows as $ingredientId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $version->id,
                    'item_type' => 'ingredient',
                    'ingredient_id' => $ingredientId,
                    'component_recipe_version_id' => null,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach ($componentRows as $componentVersionId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $version->id,
                    'item_type' => 'recipe',
                    'ingredient_id' => null,
                    'component_recipe_version_id' => $componentVersionId,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($rows)) {
                RecipeVersionItem::query()->insert($rows);
            }

            RecipeItem::query()->where('product_id', $product->id)->delete();

            if (! empty($ingredientRows)) {
                $legacyRows = [];

                foreach ($ingredientRows as $ingredientId => $qty) {
                    $legacyRows[] = [
                        'product_id' => $product->id,
                        'ingredient_id' => $ingredientId,
                        'quantity_required' => round($qty, 3),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                RecipeItem::query()->insert($legacyRows);
            }

            return $version;
        });

        $this->recipeAnalyticsService->refreshActiveVersionCost($version->load('product'));

        return redirect()
            ->route('recipes.edit', ['product' => $product])
            ->with('success', __('messages.success.recipe_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $deletedCount = DB::transaction(function () use ($product): int {
            $deletedVersions = (int) RecipeVersion::query()
                ->where('product_id', $product->id)
                ->where('is_semi_finished', false)
                ->delete();

            $deletedLegacy = (int) RecipeItem::query()
                ->where('product_id', $product->id)
                ->delete();

            return $deletedVersions + $deletedLegacy;
        });

        if ($deletedCount <= 0) {
            return back()->with('error', __('messages.errors.recipe_not_found_for_delete'));
        }

        return redirect()
            ->route('recipes.index')
            ->with('success', __('messages.success.recipe_deleted'));
    }

    public function storeSemiFinished(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'waste_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'loss_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'yield_quantity' => ['required', 'numeric', 'min:0.001'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'ingredient_rows' => ['nullable', 'array'],
            'ingredient_rows.*.ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'ingredient_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'component_rows' => ['nullable', 'array'],
            'component_rows.*.component_recipe_version_id' => ['nullable', 'integer', 'exists:recipe_versions,id'],
            'component_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'recipe_ingredients' => ['nullable', 'array'],
            'recipe_ingredients.*' => ['nullable', 'numeric', 'min:0'],
            'recipe_components' => ['nullable', 'array'],
            'recipe_components.*' => ['nullable', 'numeric', 'min:0'],
            'ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'ingredient_qty' => ['nullable', 'numeric', 'min:0'],
            'component_recipe_version_id' => ['nullable', 'integer', 'exists:recipe_versions,id'],
            'component_qty' => ['nullable', 'numeric', 'min:0'],
        ]);

        $name = trim((string) $validated['name']);

        $ingredientRows = $this->extractIngredientRows($validated);
        $componentRows = $this->extractComponentRows($validated);

        if (empty($ingredientRows) && empty($componentRows)) {
            throw ValidationException::withMessages([
                'ingredient_rows' => [__('messages.errors.recipe_requires_items')],
            ]);
        }

        $version = DB::transaction(function () use ($validated, $name, $ingredientRows, $componentRows): RecipeVersion {
            $existingRecipe = RecipeVersion::query()
                ->whereNull('product_id')
                ->where('is_semi_finished', true)
                ->where('name', $name)
                ->lockForUpdate()
                ->first();

            if ($existingRecipe) {
                throw ValidationException::withMessages([
                    'name' => [__('messages.errors.semi_finished_recipe_exists')],
                ]);
            }

            $version = RecipeVersion::query()->create([
                'product_id' => null,
                'name' => $name,
                'is_active' => true,
                'is_semi_finished' => true,
                'waste_percentage' => round((float) ($validated['waste_percentage'] ?? 0), 2),
                'loss_percentage' => round((float) ($validated['loss_percentage'] ?? 0), 2),
                'yield_quantity' => round((float) $validated['yield_quantity'], 3),
                'total_cost' => 0,
                'selling_price' => round((float) ($validated['selling_price'] ?? 0), 2),
                'notes' => $validated['notes'] ?? null,
            ]);

            $rows = [];

            foreach ($ingredientRows as $ingredientId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $version->id,
                    'item_type' => 'ingredient',
                    'ingredient_id' => $ingredientId,
                    'component_recipe_version_id' => null,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach ($componentRows as $componentVersionId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $version->id,
                    'item_type' => 'recipe',
                    'ingredient_id' => null,
                    'component_recipe_version_id' => $componentVersionId,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($rows)) {
                RecipeVersionItem::query()->insert($rows);
            }

            return $version;
        });

        $this->recipeAnalyticsService->refreshActiveVersionCost($version);

        return redirect()
            ->to(route('recipes.index') . '#semi')
            ->with('success', __('messages.success.semi_finished_recipe_created'));
    }

    public function updateSemiFinished(Request $request, RecipeVersion $recipeVersion): RedirectResponse
    {
        if (! $recipeVersion->is_semi_finished || $recipeVersion->product_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'waste_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'loss_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'yield_quantity' => ['required', 'numeric', 'min:0.001'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'ingredient_rows' => ['nullable', 'array'],
            'ingredient_rows.*.ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'ingredient_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'component_rows' => ['nullable', 'array'],
            'component_rows.*.component_recipe_version_id' => ['nullable', 'integer', 'exists:recipe_versions,id'],
            'component_rows.*.quantity_required' => ['nullable', 'numeric', 'min:0'],
            'recipe_ingredients' => ['nullable', 'array'],
            'recipe_ingredients.*' => ['nullable', 'numeric', 'min:0'],
            'recipe_components' => ['nullable', 'array'],
            'recipe_components.*' => ['nullable', 'numeric', 'min:0'],
            'ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'ingredient_qty' => ['nullable', 'numeric', 'min:0'],
            'component_recipe_version_id' => ['nullable', 'integer', 'exists:recipe_versions,id'],
            'component_qty' => ['nullable', 'numeric', 'min:0'],
        ]);

        $name = trim((string) $validated['name']);

        $ingredientRows = $this->extractIngredientRows($validated);
        $componentRows = $this->extractComponentRows($validated);

        if (empty($ingredientRows) && empty($componentRows)) {
            throw ValidationException::withMessages([
                'ingredient_rows' => [__('messages.errors.recipe_requires_items')],
            ]);
        }

        $updatedRecipe = DB::transaction(function () use ($recipeVersion, $validated, $name, $ingredientRows, $componentRows): RecipeVersion {
            $managedRecipe = RecipeVersion::query()
                ->whereKey($recipeVersion->id)
                ->lockForUpdate()
                ->firstOrFail();

            $nameExists = RecipeVersion::query()
                ->whereNull('product_id')
                ->where('is_semi_finished', true)
                ->where('name', $name)
                ->where('id', '!=', $managedRecipe->id)
                ->exists();

            if ($nameExists) {
                throw ValidationException::withMessages([
                    'name' => [__('messages.errors.semi_finished_recipe_exists')],
                ]);
            }

            RecipeVersion::query()
                ->whereNull('product_id')
                ->where('is_semi_finished', true)
                ->where('name', $name)
                ->where('id', '!=', $managedRecipe->id)
                ->update(['is_active' => false]);

            $managedRecipe->update([
                'name' => $name,
                'is_active' => true,
                'is_semi_finished' => true,
                'waste_percentage' => round((float) ($validated['waste_percentage'] ?? 0), 2),
                'loss_percentage' => round((float) ($validated['loss_percentage'] ?? 0), 2),
                'yield_quantity' => round((float) $validated['yield_quantity'], 3),
                'total_cost' => 0,
                'selling_price' => round((float) ($validated['selling_price'] ?? $managedRecipe->selling_price), 2),
                'notes' => $validated['notes'] ?? null,
            ]);

            $managedRecipe->items()->delete();

            $rows = [];

            foreach ($ingredientRows as $ingredientId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $managedRecipe->id,
                    'item_type' => 'ingredient',
                    'ingredient_id' => $ingredientId,
                    'component_recipe_version_id' => null,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach ($componentRows as $componentVersionId => $qty) {
                $rows[] = [
                    'recipe_version_id' => $managedRecipe->id,
                    'item_type' => 'recipe',
                    'ingredient_id' => null,
                    'component_recipe_version_id' => $componentVersionId,
                    'quantity_required' => round($qty, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($rows)) {
                RecipeVersionItem::query()->insert($rows);
            }

            return $managedRecipe;
        });

        $this->recipeAnalyticsService->refreshActiveVersionCost($updatedRecipe);

        return redirect()
            ->to(route('recipes.index') . '#semi')
            ->with('success', __('messages.success.semi_finished_recipe_updated'));
    }

    public function destroySemiFinished(RecipeVersion $recipeVersion): RedirectResponse
    {
        if (! $recipeVersion->is_semi_finished || $recipeVersion->product_id) {
            abort(404);
        }

        try {
            $recipeVersion->delete();
        } catch (QueryException) {
            return back()->with('error', __('messages.errors.cannot_delete_semi_finished_in_use'));
        }

        return redirect()
            ->to(route('recipes.index') . '#semi')
            ->with('success', __('messages.success.semi_finished_recipe_deleted'));
    }

    private function buildSemiFinishedBuilderData(
        array $existingIngredientIds = [],
        array $existingComponentIds = [],
        ?int $excludedSemiFinishedId = null,
    ): array {
        $recipeWarehouseId = $this->inventoryService->orderConsumptionWarehouseId();

        $ingredients = Ingredient::query()
            ->leftJoin('ingredient_warehouse_stocks as stock', function ($join) use ($recipeWarehouseId): void {
                $join->on('stock.ingredient_id', '=', 'ingredients.id')
                    ->where('stock.warehouse_id', '=', $recipeWarehouseId);
            })
            ->where('ingredients.is_active', true)
            ->orderBy('ingredients.name')
            ->get([
                'ingredients.id',
                'ingredients.name',
                'ingredients.unit',
                'ingredients.cost',
                DB::raw('COALESCE(stock.quantity, 0) as quantity'),
            ]);

        $semiFinishedOptions = RecipeVersion::query()
            ->whereNull('product_id')
            ->where('is_semi_finished', true)
            ->when($excludedSemiFinishedId, function ($query, int $id): void {
                $query->where('id', '!=', $id);
            })
            ->where(function ($query) use ($existingComponentIds): void {
                $query->where('is_active', true);

                if (! empty($existingComponentIds)) {
                    $query->orWhereIn('id', $existingComponentIds);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'total_cost', 'yield_quantity']);

        return [$ingredients, $semiFinishedOptions];
    }

    private function extractIngredientRows(array $validated): array
    {
        $rows = [];

        foreach ($validated['ingredient_rows'] ?? [] as $row) {
            $ingredientId = (int) ($row['ingredient_id'] ?? 0);
            $qty = (float) ($row['quantity_required'] ?? 0);

            if ($ingredientId <= 0 || $qty <= 0) {
                continue;
            }

            $rows[$ingredientId] = round(($rows[$ingredientId] ?? 0) + $qty, 3);
        }

        if (! empty($rows)) {
            return $rows;
        }

        foreach ($validated['recipe_ingredients'] ?? [] as $ingredientId => $qty) {
            $ingredientId = (int) $ingredientId;
            $qty = (float) ($qty ?? 0);

            if ($ingredientId <= 0 || $qty <= 0) {
                continue;
            }

            $rows[$ingredientId] = round(($rows[$ingredientId] ?? 0) + $qty, 3);
        }

        if (! empty($rows)) {
            return $rows;
        }

        $legacyIngredientId = (int) ($validated['ingredient_id'] ?? 0);
        $legacyIngredientQty = (float) ($validated['ingredient_qty'] ?? 0);

        if ($legacyIngredientId > 0 && $legacyIngredientQty > 0) {
            $rows[$legacyIngredientId] = round($legacyIngredientQty, 3);
        }

        return $rows;
    }

    private function extractComponentRows(array $validated): array
    {
        $rows = [];

        foreach ($validated['component_rows'] ?? [] as $row) {
            $componentVersionId = (int) ($row['component_recipe_version_id'] ?? 0);
            $qty = (float) ($row['quantity_required'] ?? 0);

            if ($componentVersionId <= 0 || $qty <= 0) {
                continue;
            }

            $rows[$componentVersionId] = round(($rows[$componentVersionId] ?? 0) + $qty, 3);
        }

        if (! empty($rows)) {
            return $rows;
        }

        foreach ($validated['recipe_components'] ?? [] as $componentVersionId => $qty) {
            $componentVersionId = (int) $componentVersionId;
            $qty = (float) ($qty ?? 0);

            if ($componentVersionId <= 0 || $qty <= 0) {
                continue;
            }

            $rows[$componentVersionId] = round(($rows[$componentVersionId] ?? 0) + $qty, 3);
        }

        if (! empty($rows)) {
            return $rows;
        }

        $legacyComponentId = (int) ($validated['component_recipe_version_id'] ?? 0);
        $legacyComponentQty = (float) ($validated['component_qty'] ?? 0);

        if ($legacyComponentId > 0 && $legacyComponentQty > 0) {
            $rows[$legacyComponentId] = round($legacyComponentQty, 3);
        }

        return $rows;
    }
}
