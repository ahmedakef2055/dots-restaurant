<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionBatchConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_batch_id',
        'consumed_by_recipe_version_id',
        'consumed_by_order_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function consumedByRecipeVersion(): BelongsTo
    {
        return $this->belongsTo(RecipeVersion::class, 'consumed_by_recipe_version_id');
    }

    public function consumedByOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'consumed_by_order_id');
    }
}
