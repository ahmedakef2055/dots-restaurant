<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'action',
        'previous_approval_status',
        'new_approval_status',
        'previous_status',
        'new_status',
        'previous_approval_user_id',
        'new_approval_user_id',
        'previous_approval_at',
        'new_approval_at',
        'previous_approval_comment',
        'new_approval_comment',
        'acted_by_user_id',
        'acted_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_approval_at' => 'datetime',
            'new_approval_at' => 'datetime',
            'acted_at' => 'datetime',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }

    public function previousApprovalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'previous_approval_user_id');
    }

    public function newApprovalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_approval_user_id');
    }
}
