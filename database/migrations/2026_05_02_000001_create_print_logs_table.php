<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('printer', 20);     // 'cashier' | 'bar'
            $table->string('print_type', 30);  // 'new_order' | 'add_items' | 'cashier_receipt' | 'reprint'
            $table->string('status', 10);      // 'success' | 'failed'
            $table->text('error_message')->nullable();
            $table->timestamp('printed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};
