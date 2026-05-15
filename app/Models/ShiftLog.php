<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_start',
        'shift_end',
        'cash_difference',
    ];

    protected function casts(): array
    {
        return [
            'shift_start' => 'datetime',
            'shift_end' => 'datetime',
            'cash_difference' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
