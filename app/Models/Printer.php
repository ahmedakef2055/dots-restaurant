<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Printer extends Model
{
    protected $fillable = ['name', 'printer_name', 'is_active', 'handles', 'notes'];

    protected $casts = [
        'handles'   => 'array',
        'is_active' => 'boolean',
    ];

    public static array $allJobs = [
        'cashier_receipt' => ['label' => 'إيصال الكاشير',       'label_en' => 'Cashier Receipt',  'printer_type' => 'cashier'],
        'shift_close'     => ['label' => 'إيصال تقفيل الشيفت',  'label_en' => 'Shift Close',       'printer_type' => 'shift_close'],
        'new_order'       => ['label' => 'طلب جديد (مطبخ/بار)', 'label_en' => 'New Order Ticket', 'printer_type' => 'bar'],
        'add_items'       => ['label' => 'أصناف إضافية',         'label_en' => 'Add Items Ticket', 'printer_type' => 'bar'],
        'reprint'         => ['label' => 'إعادة طباعة',          'label_en' => 'Reprint Ticket',   'printer_type' => 'bar'],
    ];

    /** Map printer_type (cashier/bar/shift_close) → the primary job key used to look it up */
    public static array $printerTypeToJob = [
        'cashier'     => 'cashier_receipt',
        'shift_close' => 'shift_close',
        'bar'         => 'new_order',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeHandlesJob(Builder $query, string $job): void
    {
        $query->whereJsonContains('handles', $job);
    }

    public function handles(string $job): bool
    {
        return in_array($job, $this->handles ?? [], true);
    }

    /**
     * Find the Windows printer name for a given printer_type (cashier|bar).
     * Returns null if not configured → JS falls back to QZ default.
     */
    public static function windowsNameFor(string $printerType): ?string
    {
        $jobKey = self::$printerTypeToJob[$printerType] ?? null;
        if (! $jobKey) {
            return null;
        }

        return static::active()
            ->whereJsonContains('handles', $jobKey)
            ->value('printer_name');
    }
}
