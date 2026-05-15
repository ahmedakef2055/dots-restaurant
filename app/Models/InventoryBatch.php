<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'warehouse_id',
        'purchase_item_id',
        'quantity',
        'remaining_quantity',
        'unit_cost',
        'total_cost',
        'expiry_date',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'remaining_quantity' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'total_cost' => 'decimal:2',
            'expiry_date' => 'date',
            'received_at' => 'datetime',
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

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}
