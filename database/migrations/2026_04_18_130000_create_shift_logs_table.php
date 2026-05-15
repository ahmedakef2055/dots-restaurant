<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shift_logs')) {
            return;
        }

        Schema::create('shift_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('shift_start');
            $table->timestamp('shift_end')->nullable();
            $table->decimal('cash_difference', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'shift_start']);
            $table->index('shift_end');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('shift_logs')) {
            return;
        }

        Schema::dropIfExists('shift_logs');
    }
};
