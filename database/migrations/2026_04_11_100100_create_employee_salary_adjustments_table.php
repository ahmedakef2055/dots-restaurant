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
        if (Schema::hasTable('employee_salary_adjustments')) {
            return;
        }

        Schema::create('employee_salary_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->date('adjustment_date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'adjustment_date']);
            $table->index(['type', 'adjustment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_adjustments');
    }
};
