<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'first_name',
        'national_id',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'hire_date',
        'base_salary',
        'work_hours_per_day',
        'attendance_days_per_week',
        'shift_start',
        'shift_end',
        'hourly_rate',
        'employment_type',
        'status',
        'address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'base_salary' => 'decimal:2',
            'work_hours_per_day' => 'decimal:2',
            'attendance_days_per_week' => 'integer',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function salaryAdjustments(): HasMany
    {
        return $this->hasMany(EmployeeSalaryAdjustment::class);
    }

    public function deliveredOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_employee_id');
    }

    public function deliverySettlements(): HasMany
    {
        return $this->hasMany(EmployeeDeliverySettlement::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getNationalIdDisplayAttribute(): ?string
    {
        $directNationalId = $this->attributes['national_id'] ?? null;

        if (is_string($directNationalId) && trim($directNationalId) !== '') {
            return trim($directNationalId);
        }

        $notes = (string) ($this->attributes['notes'] ?? '');

        if (preg_match('/\[NID:([^\]]+)\]/', $notes, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    public function getNotesWithoutNationalIdAttribute(): ?string
    {
        $notes = (string) ($this->attributes['notes'] ?? '');
        $cleaned = preg_replace('/\s*\[NID:[^\]]+\]\s*/', ' ', $notes);

        $value = trim((string) $cleaned);

        return $value === '' ? null : $value;
    }

    public function getDailySalaryAttribute(): float
    {
        $baseSalary = (float) $this->base_salary;
        $attendanceDays = (int) ($this->attendance_days_per_week ?: 6);

        if ($baseSalary <= 0 || $attendanceDays <= 0) {
            return 0;
        }

        $estimatedMonthlyWorkDays = $attendanceDays * 4.33;

        if ($estimatedMonthlyWorkDays <= 0) {
            return 0;
        }

        return round($baseSalary / $estimatedMonthlyWorkDays, 2);
    }

    public function getShiftStartHmAttribute(): ?string
    {
        return $this->normalizeTimeString($this->shift_start);
    }

    public function getShiftEndHmAttribute(): ?string
    {
        return $this->normalizeTimeString($this->shift_end);
    }

    private function normalizeTimeString(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return strlen($value) >= 5 ? substr($value, 0, 5) : $value;
    }
}
