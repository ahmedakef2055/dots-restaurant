<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\InventoryBatch;
use App\Models\InventoryStockLog;
use Illuminate\Support\Collection;

class InventoryForecastService
{
    public function buildSmartShortageSuggestions(int $lookbackDays = 30): Collection
    {
        $ingredients = Ingredient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($ingredients->isEmpty()) {
            return collect();
        }

        $usageByIngredient = $this->usageByIngredient($lookbackDays);

        return $ingredients
            ->map(function (Ingredient $ingredient) use ($usageByIngredient, $lookbackDays): array {
                $usage = (float) ($usageByIngredient[$ingredient->id] ?? 0);
                $avgPerDay = $lookbackDays > 0 ? $usage / $lookbackDays : 0;
                $currentQty = (float) $ingredient->quantity;

                $daysLeft = $avgPerDay > 0
                    ? $currentQty / $avgPerDay
                    : null;

                return [
                    'ingredient' => $ingredient,
                    'current_quantity' => $currentQty,
                    'threshold' => (float) $ingredient->threshold,
                    'avg_daily_usage' => round($avgPerDay, 3),
                    'predicted_days_left' => $daysLeft !== null ? round($daysLeft, 1) : null,
                    'is_low_stock' => $currentQty <= (float) $ingredient->threshold,
                    'suggested_reorder_quantity' => $this->suggestReorderQuantity($ingredient, $avgPerDay),
                ];
            })
            ->filter(static fn(array $row): bool => $row['is_low_stock'] || ($row['predicted_days_left'] !== null && $row['predicted_days_left'] <= 7))
            ->values();
    }

    public function buildExpiryAlerts(int $daysAhead = 7): Collection
    {
        $to = now()->addDays($daysAhead)->toDateString();

        return InventoryBatch::query()
            ->with(['ingredient:id,name,unit', 'warehouse:id,name'])
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', $to)
            ->orderBy('expiry_date')
            ->get();
    }

    public function usageByIngredient(int $lookbackDays = 30): Collection
    {
        $from = now()->subDays(max($lookbackDays, 1))->startOfDay();

        return InventoryStockLog::query()
            ->where(function ($query): void {
                $query
                    ->where('adjustment_type', 'out')
                    ->orWhereIn('action', ['deduct', 'production_consume']);
            })
            ->where(function ($query) use ($from): void {
                $query
                    ->where('occurred_at', '>=', $from)
                    ->orWhere(function ($fallback) use ($from): void {
                        $fallback
                            ->whereNull('occurred_at')
                            ->where('created_at', '>=', $from);
                    });
            })
            ->selectRaw('ingredient_id, SUM(quantity) as used_qty')
            ->groupBy('ingredient_id')
            ->pluck('used_qty', 'ingredient_id');
    }

    private function suggestReorderQuantity(Ingredient $ingredient, float $avgPerDay): float
    {
        $targetCoverageDays = 14;
        $targetQty = $avgPerDay > 0
            ? $avgPerDay * $targetCoverageDays
            : (float) $ingredient->threshold;

        $suggested = max($targetQty - (float) $ingredient->quantity, 0);

        return round($suggested, 3);
    }
}
