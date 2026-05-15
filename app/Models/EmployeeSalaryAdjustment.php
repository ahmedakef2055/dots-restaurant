<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'product_id',
        'quantity',
        'unit_price',
        'amount',
        'adjustment_date',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'adjustment_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'manual_deduction' => 'Manual deduction',
            'product_charge' => 'Product charge',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
