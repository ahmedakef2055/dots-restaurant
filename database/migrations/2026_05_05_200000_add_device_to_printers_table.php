<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->string('device')->default('/dev/usb/lp0')
                ->after('printer_name')
                ->comment('Linux device path for server-side direct printing');
        });
    }

    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('device');
        });
    }
};
