<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('order_daily_number')->default(0)->after('order_number');
            $table->index(['order_daily_number']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_daily_number']);
            $table->dropColumn('order_daily_number');
        });
    }
};
