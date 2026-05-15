<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_returns')) {
            return;
        }

        Schema::create('purchase_returns', function (Blueprint $table): void {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('return_date')->index();
            $table->decimal('amount', 12, 2)->unsigned();
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'return_date'], 'purchase_returns_supplier_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
