<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'contact_person',
        'address',
        'city',
        'country',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function ingredients(): BelongsToMany|HasMany
    {
        if (! Schema::hasTable('ingredient_supplier')) {
            return $this->hasMany(Ingredient::class);
        }

        return $this->belongsToMany(Ingredient::class, 'ingredient_supplier')
            ->withTimestamps();
    }

    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
