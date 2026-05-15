<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_approval_logs')) {
            return;
        }

        Schema::create('purchase_approval_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->string('action', 20)->index();

            $table->string('previous_approval_status', 30)->nullable();
            $table->string('new_approval_status', 30)->nullable();

            $table->string('previous_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();

            $table->foreignId('previous_approval_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('new_approval_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('previous_approval_at')->nullable();
            $table->timestamp('new_approval_at')->nullable();

            $table->text('previous_approval_comment')->nullable();
            $table->text('new_approval_comment')->nullable();

            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acted_at')->nullable()->index();

            $table->timestamps();

            $table->index(['purchase_id', 'acted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_approval_logs');
    }
};
