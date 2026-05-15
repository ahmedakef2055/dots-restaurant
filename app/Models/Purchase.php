<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'request_type',
        'supplier_id',
        'warehouse_id',
        'user_id',
        'purchase_date',
        'expense_title',
        'expense_invoice_reference',
        'expense_amount',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'status',
        'approval_status',
        'approval_comment',
        'approval_user_id',
        'completed_by_user_id',
        'approval_at',
        'completed_at',
        'inventory_applied_at',
        'payment_method',
        'invoice_file_path',
        'invoice_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'expense_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'approval_at' => 'datetime',
            'completed_at' => 'datetime',
            'inventory_applied_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(PurchaseApprovalLog::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
