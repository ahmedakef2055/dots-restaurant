<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'recipe_version_id',
        'warehouse_id',
        'user_id',
        'produced_quantity',
        'remaining_quantity',
        'unit_cost',
        'total_cost',
        'expiry_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'produced_quantity' => 'decimal:3',
            'remaining_quantity' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'total_cost' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function recipeVersion(): BelongsTo
    {
        return $this->belongsTo(RecipeVersion::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(ProductionBatchConsumption::class);
    }
}
