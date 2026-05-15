<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'ingredient_id',
        'warehouse_id',
        'ingredient_name',
        'unit_cost',
        'quantity',
        'expiry_date',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'quantity' => 'decimal:3',
            'expiry_date' => 'date',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
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
