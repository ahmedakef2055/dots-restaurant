<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'supplier_id',
        'unit',
        'unit_id',
        'default_warehouse_id',
        'cost',
        'quantity',
        'threshold',
        'cost_method',
        'expiry_date',
        'expiry_alert_days',
        'current_stock',
        'reorder_level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:4',
            'quantity' => 'decimal:3',
            'threshold' => 'decimal:3',
            'current_stock' => 'decimal:3',
            'reorder_level' => 'decimal:3',
            'expiry_date' => 'date',
            'expiry_alert_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unitModel(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function defaultWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(InventoryStockLog::class);
    }

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(IngredientWarehouseStock::class);
    }

    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'recipe_items')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'ingredient_supplier')
            ->withTimestamps();
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
