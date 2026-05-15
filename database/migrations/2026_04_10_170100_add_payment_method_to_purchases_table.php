<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchases') || Schema::hasColumn('purchases', 'payment_method')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            $table->string('payment_method', 20)
                ->default('cash')
                ->after('status')
                ->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchases') || ! Schema::hasColumn('purchases', 'payment_method')) {
            return;
        }

        Schema::table('purchases', function (Blueprint $table): void {
            $table->dropColumn('payment_method');
        });
    }
};
