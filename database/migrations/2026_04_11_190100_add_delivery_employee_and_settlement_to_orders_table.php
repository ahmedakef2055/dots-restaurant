<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (! Schema::hasColumn('orders', 'delivery_employee_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedBigInteger('delivery_employee_id')
                    ->nullable()
                    ->after('user_id');
            });
        }

        if (! Schema::hasColumn('orders', 'delivery_settlement_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedBigInteger('delivery_settlement_id')
                    ->nullable()
                    ->after('delivery_employee_id');
            });
        }

        if (! $this->indexExists('orders', 'orders_delivery_employee_id_index')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->index('delivery_employee_id', 'orders_delivery_employee_id_index');
            });
        }

        if (! $this->indexExists('orders', 'orders_delivery_settlement_id_index')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->index('delivery_settlement_id', 'orders_delivery_settlement_id_index');
            });
        }

        if (! $this->foreignKeyExists('orders', 'orders_delivery_employee_id_foreign')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreign('delivery_employee_id', 'orders_delivery_employee_id_foreign')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();
            });
        }

        if (! $this->foreignKeyExists('orders', 'orders_delivery_settlement_id_foreign')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreign('delivery_settlement_id', 'orders_delivery_settlement_id_foreign')
                    ->references('id')
                    ->on('employee_delivery_settlements')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (Schema::hasColumn('orders', 'delivery_settlement_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                if ($this->foreignKeyExists('orders', 'orders_delivery_settlement_id_foreign')) {
                    $table->dropForeign('orders_delivery_settlement_id_foreign');
                }

                if ($this->indexExists('orders', 'orders_delivery_settlement_id_index')) {
                    $table->dropIndex('orders_delivery_settlement_id_index');
                }

                $table->dropColumn('delivery_settlement_id');
            });
        }

        if (Schema::hasColumn('orders', 'delivery_employee_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                if ($this->foreignKeyExists('orders', 'orders_delivery_employee_id_foreign')) {
                    $table->dropForeign('orders_delivery_employee_id_foreign');
                }

                if ($this->indexExists('orders', 'orders_delivery_employee_id_index')) {
                    $table->dropIndex('orders_delivery_employee_id_index');
                }

                $table->dropColumn('delivery_employee_id');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = (string) DB::connection()->getDatabaseName();

        if ($database === '') {
            return false;
        }

        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName],
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $database = (string) DB::connection()->getDatabaseName();

        if ($database === '') {
            return false;
        }

        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.table_constraints WHERE constraint_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = ? ',
            [$database, $table, $constraintName, 'FOREIGN KEY'],
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }
};
