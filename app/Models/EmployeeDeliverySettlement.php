<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeDeliverySettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'order_count',
        'gross_total',
        'commission_percentage',
        'commission_amount',
        'restaurant_share_amount',
        'settled_at',
        'settled_by',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'order_count' => 'integer',
            'gross_total' => 'decimal:2',
            'commission_percentage' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'restaurant_share_amount' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_settlement_id');
    }
}
