<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InventoryStockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'warehouse_id',
        'user_id',
        'adjustment_type',
        'action',
        'quantity',
        'unit_cost',
        'previous_stock',
        'new_stock',
        'note',
        'reference_type',
        'reference_id',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'previous_stock' => 'decimal:3',
            'new_stock' => 'decimal:3',
            'occurred_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
