<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity_required',
    ];

    protected function casts(): array
    {
        return [
            'quantity_required' => 'decimal:3',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
