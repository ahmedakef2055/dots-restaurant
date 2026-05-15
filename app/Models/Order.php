<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_serial';

    private const ACTIVE_DINE_IN_STATUSES = ['pending', 'in_progress', 'open'];
    private const KITCHEN_STATUSES = ['pending', 'preparing', 'ready', 'served'];

    protected $fillable = [
        'order_number',
        'order_daily_number',
        'user_id',
        'shift_id',
        'customer_id',
        'delivery_employee_id',
        'delivery_settlement_id',
        'coupon_id',
        'offer_id',
        'coupon_code',
        'offer_name',
        'order_type',
        'restaurant_table_id',
        'active_table_guard',
        'discount_type',
        'discount_value',
        'subtotal',
        'discount_amount',
        'total',
        'status',
        'payment_method',
        'inventory_deducted_at',
        'kitchen_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'inventory_deducted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $order): void {
            $order->active_table_guard = $order->isActiveDineIn()
                ? (int) $order->restaurant_table_id
                : null;
        });
    }

    public static function activeDineInStatuses(): array
    {
        return self::ACTIVE_DINE_IN_STATUSES;
    }

    public static function kitchenStatuses(): array
    {
        return self::KITCHEN_STATUSES;
    }

    public function isActiveDineIn(): bool
    {
        return $this->order_type === 'dine_in'
            && ! empty($this->restaurant_table_id)
            && in_array((string) $this->status, self::activeDineInStatuses(), true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'delivery_employee_id');
    }

    public function deliverySettlement(): BelongsTo
    {
        return $this->belongsTo(EmployeeDeliverySettlement::class, 'delivery_settlement_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
