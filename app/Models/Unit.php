<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'family',
        'base_factor',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_factor' => 'decimal:6',
            'is_active' => 'boolean',
        ];
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
