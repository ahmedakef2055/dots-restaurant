<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees', 'work_hours_per_day')) {
                $table->decimal('work_hours_per_day', 5, 2)->default(8)->after('base_salary');
            }

            if (! Schema::hasColumn('employees', 'attendance_days_per_week')) {
                $table->unsignedTinyInteger('attendance_days_per_week')->default(6)->after('work_hours_per_day');
            }

            if (! Schema::hasColumn('employees', 'shift_start')) {
                $table->time('shift_start')->default('09:00:00')->after('attendance_days_per_week');
            }

            if (! Schema::hasColumn('employees', 'shift_end')) {
                $table->time('shift_end')->default('17:00:00')->after('shift_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $columns = [
                'work_hours_per_day',
                'attendance_days_per_week',
                'shift_start',
                'shift_end',
            ];

            $existingColumns = array_filter($columns, static fn(string $column): bool => Schema::hasColumn('employees', $column));

            if (! empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
