<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'printer',
        'print_type',
        'status',
        'error_message',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_serial');
    }
}
