<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return;
        }

        if (! Schema::hasColumn('cashier_shifts', 'end_time')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->timestamp('end_time')->nullable()->after('start_time');
            });
        }

        if (! Schema::hasColumn('cashier_shifts', 'total_sales')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->decimal('total_sales', 12, 2)->nullable()->after('end_time');
            });
        }

        if (! Schema::hasColumn('cashier_shifts', 'expected_cash')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->decimal('expected_cash', 12, 2)->nullable()->after('total_sales');
            });
        }

        if (! Schema::hasColumn('cashier_shifts', 'actual_cash')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->decimal('actual_cash', 12, 2)->nullable()->after('expected_cash');
            });
        }

        if (! Schema::hasColumn('cashier_shifts', 'tips')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->decimal('tips', 12, 2)->nullable()->after('actual_cash');
            });
        }

        if (! Schema::hasColumn('cashier_shifts', 'difference')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->decimal('difference', 12, 2)->nullable()->after('tips');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return;
        }

        if (Schema::hasColumn('cashier_shifts', 'difference')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('difference');
            });
        }

        if (Schema::hasColumn('cashier_shifts', 'tips')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('tips');
            });
        }

        if (Schema::hasColumn('cashier_shifts', 'actual_cash')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('actual_cash');
            });
        }

        if (Schema::hasColumn('cashier_shifts', 'expected_cash')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('expected_cash');
            });
        }

        if (Schema::hasColumn('cashier_shifts', 'total_sales')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('total_sales');
            });
        }

        if (Schema::hasColumn('cashier_shifts', 'end_time')) {
            Schema::table('cashier_shifts', function (Blueprint $table): void {
                $table->dropColumn('end_time');
            });
        }
    }
};
