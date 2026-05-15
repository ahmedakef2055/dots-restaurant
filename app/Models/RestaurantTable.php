<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}
