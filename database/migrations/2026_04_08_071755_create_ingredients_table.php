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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit', 30);
            $table->decimal('current_stock', 12, 3)->unsigned()->default(0);
            $table->decimal('reorder_level', 12, 3)->unsigned()->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['name', 'unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
