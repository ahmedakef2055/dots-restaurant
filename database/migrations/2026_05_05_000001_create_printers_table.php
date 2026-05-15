<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('device')->default('/dev/usb/lp0');
            $table->unsignedTinyInteger('paper_width')->default(80)->comment('58 or 80 mm');
            $table->boolean('is_active')->default(true);
            $table->json('handles')->default('[]')->comment('Array of print job types this printer handles');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
