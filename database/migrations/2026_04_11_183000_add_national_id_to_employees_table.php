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
        if (! Schema::hasColumn('employees', 'national_id')) {
            Schema::table('employees', function (Blueprint $table): void {
                $table->string('national_id', 30)->nullable()->unique()->after('first_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('employees', 'national_id')) {
            Schema::table('employees', function (Blueprint $table): void {
                $table->dropUnique(['national_id']);
                $table->dropColumn('national_id');
            });
        }
    }
};
