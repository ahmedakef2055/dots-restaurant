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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery'])->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('stackable_with_coupon')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_type', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
