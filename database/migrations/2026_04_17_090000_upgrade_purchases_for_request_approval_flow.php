<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchases')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchases', 'request_type')) {
                $table->string('request_type', 40)->default('inventory')->after('purchase_number')->index();
            }

            if (! Schema::hasColumn('purchases', 'expense_title')) {
                $table->string('expense_title', 180)->nullable()->after('purchase_date');
            }

            if (! Schema::hasColumn('purchases', 'expense_invoice_reference')) {
                $table->string('expense_invoice_reference', 120)->nullable()->after('expense_title');
            }

            if (! Schema::hasColumn('purchases', 'expense_amount')) {
                $table->decimal('expense_amount', 12, 2)->unsigned()->nullable()->after('expense_invoice_reference');
            }

            if (! Schema::hasColumn('purchases', 'approval_status')) {
                $table->string('approval_status', 30)->default('pending')->after('status')->index();
            }

            if (! Schema::hasColumn('purchases', 'approval_comment')) {
                $table->text('approval_comment')->nullable()->after('approval_status');
            }

            if (! Schema::hasColumn('purchases', 'approval_user_id') && Schema::hasTable('users')) {
                $table->foreignId('approval_user_id')->nullable()->after('approval_comment')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchases', 'approval_at')) {
                $table->timestamp('approval_at')->nullable()->after('approval_user_id')->index();
            }

            if (! Schema::hasColumn('purchases', 'inventory_applied_at')) {
                $table->timestamp('inventory_applied_at')->nullable()->after('approval_at')->index();
            }
        });

        if (Schema::hasColumn('purchases', 'supplier_id')) {
            try {
                Schema::table('purchases', function (Blueprint $table): void {
                    $table->foreignId('supplier_id')->nullable()->change();
                });
            } catch (\Throwable) {
                // Keep migration resilient when column changes are unsupported by the current driver.
            }
        }

        DB::table('purchases')
            ->whereIn('status', ['completed', 'paid', 'cancelled'])
            ->update([
                'approval_status' => 'approved',
                'approval_at' => DB::raw('COALESCE(approval_at, updated_at, created_at)'),
                'inventory_applied_at' => DB::raw("CASE WHEN request_type = 'inventory' THEN COALESCE(inventory_applied_at, updated_at, created_at) ELSE inventory_applied_at END"),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchases')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            if (Schema::hasColumn('purchases', 'inventory_applied_at')) {
                $table->dropColumn('inventory_applied_at');
            }

            if (Schema::hasColumn('purchases', 'approval_at')) {
                $table->dropColumn('approval_at');
            }

            if (Schema::hasColumn('purchases', 'approval_user_id')) {
                $table->dropConstrainedForeignId('approval_user_id');
            }

            if (Schema::hasColumn('purchases', 'approval_comment')) {
                $table->dropColumn('approval_comment');
            }

            if (Schema::hasColumn('purchases', 'approval_status')) {
                $table->dropColumn('approval_status');
            }

            if (Schema::hasColumn('purchases', 'expense_amount')) {
                $table->dropColumn('expense_amount');
            }

            if (Schema::hasColumn('purchases', 'expense_invoice_reference')) {
                $table->dropColumn('expense_invoice_reference');
            }

            if (Schema::hasColumn('purchases', 'expense_title')) {
                $table->dropColumn('expense_title');
            }

            if (Schema::hasColumn('purchases', 'request_type')) {
                $table->dropColumn('request_type');
            }
        });
    }
};
