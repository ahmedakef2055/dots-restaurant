<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'order_type',
        'starts_at',
        'ends_at',
        'priority',
        'stackable_with_coupon',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'stackable_with_coupon' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
