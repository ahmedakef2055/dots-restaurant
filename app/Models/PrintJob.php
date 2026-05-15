<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PrintJob extends Model
{
    protected $fillable = [
        'printer_type', 'payload', 'payload_type',
        'status', 'error', 'printable_type', 'printable_id',
    ];

    public function printable(): MorphTo
    {
        return $this->morphTo();
    }
}
