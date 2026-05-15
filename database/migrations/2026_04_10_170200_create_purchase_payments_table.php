<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_payments')) {
            return;
        }

        Schema::create('purchase_payments', function (Blueprint $table): void {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('payment_date')->index();
            $table->decimal('amount', 12, 2)->unsigned();
            $table->string('method', 30)->default('cash')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'payment_date'], 'purchase_payments_supplier_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
