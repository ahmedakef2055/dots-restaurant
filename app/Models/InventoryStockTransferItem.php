<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_stock_transfer_id',
        'ingredient_id',
        'quantity',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_cost' => 'decimal:4',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(InventoryStockTransfer::class, 'inventory_stock_transfer_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
