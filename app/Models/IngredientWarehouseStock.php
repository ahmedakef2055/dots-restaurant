<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientWarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'warehouse_id',
        'quantity',
        'average_cost',
        'last_purchase_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'average_cost' => 'decimal:4',
            'last_purchase_cost' => 'decimal:4',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
