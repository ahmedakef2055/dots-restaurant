<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_audit_id',
        'ingredient_id',
        'system_quantity',
        'actual_quantity',
        'difference_quantity',
        'unit_cost',
        'impact_cost',
    ];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'decimal:3',
            'actual_quantity' => 'decimal:3',
            'difference_quantity' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'impact_cost' => 'decimal:2',
        ];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
