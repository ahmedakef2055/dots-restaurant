<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeVersionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_version_id',
        'item_type',
        'ingredient_id',
        'component_recipe_version_id',
        'quantity_required',
    ];

    protected function casts(): array
    {
        return [
            'quantity_required' => 'decimal:3',
        ];
    }

    public function recipeVersion(): BelongsTo
    {
        return $this->belongsTo(RecipeVersion::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function componentRecipeVersion(): BelongsTo
    {
        return $this->belongsTo(RecipeVersion::class, 'component_recipe_version_id');
    }
}
