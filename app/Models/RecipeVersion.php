<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'is_active',
        'is_semi_finished',
        'waste_percentage',
        'loss_percentage',
        'yield_quantity',
        'total_cost',
        'selling_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_semi_finished' => 'boolean',
            'waste_percentage' => 'decimal:2',
            'loss_percentage' => 'decimal:2',
            'yield_quantity' => 'decimal:3',
            'total_cost' => 'decimal:4',
            'selling_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecipeVersionItem::class);
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }
}
