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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('purchase_date')->index();
            $table->decimal('subtotal', 12, 2)->unsigned();
            $table->decimal('tax_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('discount_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('total', 12, 2)->unsigned();
            $table->string('status')->default('completed')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
