<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders') || Schema::hasColumn('orders', 'customer_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('user_id')
                ->constrained('customers')
                ->nullOnDelete()
                ->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'customer_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
