<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_delivery_settlements')) {
            return;
        }

        Schema::create('employee_delivery_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order_count')->default(0);
            $table->decimal('gross_total', 12, 2)->default(0);
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('restaurant_share_amount', 12, 2)->default(0);
            $table->dateTime('settled_at');
            $table->foreignId('settled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'settled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_delivery_settlements');
    }
};
