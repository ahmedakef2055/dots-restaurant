<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OPEN_SHIFT_GUARD_UNIQUE = 'cashier_shifts_open_shift_guard_unique';

    public function up(): void
    {
        if (Schema::hasTable('cashier_shifts')) {
            return;
        }

        Schema::create('cashier_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('opening_cash', 12, 2);
            $table->timestamp('start_time');
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('open_shift_guard')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->unique('open_shift_guard', self::OPEN_SHIFT_GUARD_UNIQUE);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return;
        }

        Schema::dropIfExists('cashier_shifts');
    }
};
