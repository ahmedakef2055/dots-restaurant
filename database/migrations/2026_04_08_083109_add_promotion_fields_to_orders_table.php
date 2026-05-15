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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->after('coupon_id')->constrained()->nullOnDelete();
            $table->string('coupon_code', 50)->nullable()->after('offer_id');
            $table->string('offer_name', 150)->nullable()->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropConstrainedForeignId('offer_id');
            $table->dropColumn(['coupon_code', 'offer_name']);
        });
    }
};
