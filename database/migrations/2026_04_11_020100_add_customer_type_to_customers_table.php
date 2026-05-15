<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers') || Schema::hasColumn('customers', 'customer_type')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table): void {
            $table->string('customer_type', 20)
                ->default('normal')
                ->after('notes')
                ->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customers') || ! Schema::hasColumn('customers', 'customer_type')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn('customer_type');
        });
    }
};
