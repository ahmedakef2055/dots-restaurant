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

        if (Schema::hasColumn('orders', 'shift_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table): void {
            $table->unsignedBigInteger('shift_id')
                ->nullable()
                ->after('user_id');

            $table->index('shift_id', 'orders_shift_id_index');
        });

        if (Schema::hasTable('cashier_shifts')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreign('shift_id', 'orders_shift_id_foreign')
                    ->references('id')
                    ->on('cashier_shifts')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'shift_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table): void {
            if ($this->foreignKeyExists('orders', 'orders_shift_id_foreign')) {
                $table->dropForeign('orders_shift_id_foreign');
            }

            if ($this->indexExists('orders', 'orders_shift_id_index')) {
                $table->dropIndex('orders_shift_id_index');
            }

            $table->dropColumn('shift_id');
        });
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
