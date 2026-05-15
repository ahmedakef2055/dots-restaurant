<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipe_version_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'line_total',
        'notes',
        'kitchen_status',
        'kitchen_batch',
        'preparation_station',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'kitchen_batch' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recipeVersion(): BelongsTo
    {
        return $this->belongsTo(RecipeVersion::class);
    }
}
