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
        if (Schema::hasTable('salary_payments')) {
            return;
        }

        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('base_salary', 12, 2);
            $table->decimal('attendance_deduction', 12, 2)->default(0);
            $table->decimal('bonus_amount', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('status', 20)->default('unpaid');
            $table->text('note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
