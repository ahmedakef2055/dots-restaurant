<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('paper_width');
        });
    }

    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->unsignedTinyInteger('paper_width')->default(80)->after('printer_name');
        });
    }
};
