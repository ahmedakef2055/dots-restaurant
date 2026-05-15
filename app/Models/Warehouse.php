<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'notes',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function ingredientStocks(): HasMany
    {
        return $this->hasMany(IngredientWarehouseStock::class);
    }

    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(InventoryStockLog::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(InventoryStockTransfer::class, 'from_warehouse_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(InventoryStockTransfer::class, 'to_warehouse_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(StockAudit::class);
    }
}
