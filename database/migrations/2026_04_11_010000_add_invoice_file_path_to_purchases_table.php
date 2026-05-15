<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchases') || Schema::hasColumn('purchases', 'invoice_file_path')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            $table->string('invoice_file_path')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchases') || ! Schema::hasColumn('purchases', 'invoice_file_path')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            $table->dropColumn('invoice_file_path');
        });
    }
};
