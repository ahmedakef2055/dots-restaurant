<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'base_salary',
        'attendance_deduction',
        'bonus_amount',
        'other_deduction',
        'gross_amount',
        'net_amount',
        'paid_amount',
        'payment_date',
        'status',
        'note',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_date' => 'date',
            'base_salary' => 'decimal:2',
            'attendance_deduction' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'other_deduction' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
