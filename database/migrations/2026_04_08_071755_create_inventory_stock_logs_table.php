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
        Schema::create('inventory_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('adjustment_type', ['in', 'out', 'set']);
            $table->decimal('quantity', 12, 3)->unsigned();
            $table->decimal('previous_stock', 12, 3)->unsigned();
            $table->decimal('new_stock', 12, 3)->unsigned();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['ingredient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_logs');
    }
};
