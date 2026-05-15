<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierShift extends Model
{
    use HasFactory;

    private const OPEN_STATUS = 'open';

    protected $fillable = [
        'user_id',
        'opening_cash',
        'start_time',
        'end_time',
        'total_sales',
        'expected_cash',
        'actual_cash',
        'tips',
        'difference',
        'status',
        'open_shift_guard',
    ];

    protected function casts(): array
    {
        return [
            'opening_cash' => 'decimal:2',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'total_sales' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'tips' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $shift): void {
            $shift->open_shift_guard = $shift->isOpen()
                ? (int) $shift->user_id
                : null;
        });
    }

    public function isOpen(): bool
    {
        return (string) $this->status === self::OPEN_STATUS;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
