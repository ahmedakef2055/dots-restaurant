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
            if (! Schema::hasColumn('purchases', 'invoice_number')) {
                $table->string('invoice_number', 120)->nullable()->after('invoice_file_path');
            }

            if (! Schema::hasColumn('purchases', 'completed_by_user_id') && Schema::hasTable('users')) {
                $table->foreignId('completed_by_user_id')->nullable()->after('approval_user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchases', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('approval_at')->index();
            }
        });

        DB::table('purchases')
            ->whereIn('status', ['completed', 'paid'])
            ->update([
                'completed_at' => DB::raw('COALESCE(completed_at, inventory_applied_at, updated_at, created_at)'),
                'completed_by_user_id' => DB::raw('COALESCE(completed_by_user_id, user_id)'),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchases')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            if (Schema::hasColumn('purchases', 'completed_at')) {
                $table->dropColumn('completed_at');
            }

            if (Schema::hasColumn('purchases', 'completed_by_user_id')) {
                $table->dropConstrainedForeignId('completed_by_user_id');
            }

            if (Schema::hasColumn('purchases', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
        });
    }
};
