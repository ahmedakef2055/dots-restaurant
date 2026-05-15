<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecipeVersion;

class RecipeAnalyticsService
{
    public function calculateRecipeVersionCost(RecipeVersion $version, int $depth = 0): float
    {
        if ($depth > 8) {
            return 0.0;
        }

        $version->loadMissing(['items.ingredient:id,cost', 'items.componentRecipeVersion']);

        $baseCost = 0.0;

        foreach ($version->items as $item) {
            $required = (float) $item->quantity_required;

            if ($required <= 0) {
                continue;
            }

            if ($item->item_type === 'ingredient' && $item->ingredient) {
                $baseCost += $required * (float) $item->ingredient->cost;
                continue;
            }

            if ($item->item_type === 'recipe' && $item->componentRecipeVersion) {
                $componentCost = $this->calculateRecipeVersionCost($item->componentRecipeVersion, $depth + 1);
                $componentYield = max((float) $item->componentRecipeVersion->yield_quantity, 1);
                $baseCost += $required * ($componentCost / $componentYield);
            }
        }

        $wasteFactor = 1 + (((float) $version->waste_percentage) / 100);
        $lossFactor = 1 + (((float) $version->loss_percentage) / 100);

        return round($baseCost * $wasteFactor * $lossFactor, 4);
    }

    public function refreshActiveVersionCost(RecipeVersion $version): RecipeVersion
    {
        $cost = $this->calculateRecipeVersionCost($version);

        $sellingPrice = $version->selling_price;

        if (! $sellingPrice && $version->product) {
            $sellingPrice = $version->product->price;
        }

        $version->update([
            'total_cost' => $cost,
            'selling_price' => round((float) $sellingPrice, 2),
        ]);

        return $version->fresh();
    }

    public function profitAnalysisByProduct(Product $product): array
    {
        $versions = $product->recipeVersions()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rows = $versions->map(function (RecipeVersion $version): array {
            $cost = $this->calculateRecipeVersionCost($version);
            $selling = (float) ($version->selling_price ?: $version->product?->price ?: 0);
            $profit = $selling - $cost;

            return [
                'recipe_version_id' => $version->id,
                'name' => $version->name,
                'cost' => round($cost, 4),
                'selling_price' => round($selling, 2),
                'profit' => round($profit, 2),
                'margin_percent' => $selling > 0 ? round(($profit / $selling) * 100, 2) : 0,
            ];
        })->values()->all();

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'recipes' => $rows,
        ];
    }
}
