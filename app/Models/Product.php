<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'price',
        'stock',
        'is_active',
        'image_url',
        'category_id',
        'preparation_station',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'category_id' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function recipeVersions(): HasMany
    {
        return $this->hasMany(RecipeVersion::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_items')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }
}
