<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        $columnsToDrop = [];

        foreach (['last_name', 'email', 'date_of_birth', 'gender', 'city', 'country', 'customer_type'] as $column) {
            if (Schema::hasColumn('customers', $column)) {
                $columnsToDrop[] = $column;
            }
        }

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) use ($columnsToDrop): void {
            $table->dropColumn($columnsToDrop);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        $addLastName = ! Schema::hasColumn('customers', 'last_name');
        $addEmail = ! Schema::hasColumn('customers', 'email');
        $addDateOfBirth = ! Schema::hasColumn('customers', 'date_of_birth');
        $addGender = ! Schema::hasColumn('customers', 'gender');
        $addCity = ! Schema::hasColumn('customers', 'city');
        $addCountry = ! Schema::hasColumn('customers', 'country');
        $addCustomerType = ! Schema::hasColumn('customers', 'customer_type');

        if (! $addLastName && ! $addEmail && ! $addDateOfBirth && ! $addGender && ! $addCity && ! $addCountry && ! $addCustomerType) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) use ($addLastName, $addEmail, $addDateOfBirth, $addGender, $addCity, $addCountry, $addCustomerType): void {
            if ($addLastName) {
                $table->string('last_name', 120)->nullable();
            }

            if ($addEmail) {
                $table->string('email', 190)->nullable()->unique();
            }

            if ($addDateOfBirth) {
                $table->date('date_of_birth')->nullable();
            }

            if ($addGender) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            }

            if ($addCity) {
                $table->string('city', 120)->nullable();
            }

            if ($addCountry) {
                $table->string('country', 120)->nullable();
            }

            if ($addCustomerType) {
                $table->string('customer_type', 20)->default('normal')->index();
            }
        });
    }
};
