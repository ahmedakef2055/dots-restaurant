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
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        $hasIndex = static function (string $table, string $indexName) use ($connection, $databaseName): bool {
            if (! $databaseName) {
                return false;
            }

            $result = $connection->select(
                'SELECT COUNT(*) AS count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$databaseName, $table, $indexName]
            );

            $count = (int) (($result[0]->count ?? $result[0]['count'] ?? 0));

            return $count > 0;
        };

        if (Schema::hasTable('recipe_versions')) {
            Schema::table('recipe_versions', function (Blueprint $table) use ($hasIndex): void {
                if ($hasIndex('recipe_versions', 'recipe_version_unique_idx')) {
                    $table->dropUnique('recipe_version_unique_idx');
                }

                // Keep this index because some deployments tie it to foreign key requirements.
            });

            Schema::table('recipe_versions', function (Blueprint $table): void {
                if (Schema::hasColumn('recipe_versions', 'variant_name')) {
                    $table->dropColumn('variant_name');
                }

                if (Schema::hasColumn('recipe_versions', 'version_number')) {
                    $table->dropColumn('version_number');
                }
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'variant_name')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->dropColumn('variant_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recipe_versions')) {
            Schema::table('recipe_versions', function (Blueprint $table): void {
                if (! Schema::hasColumn('recipe_versions', 'variant_name')) {
                    $table->string('variant_name', 60)->default('default')->after('name');
                }

                if (! Schema::hasColumn('recipe_versions', 'version_number')) {
                    $table->unsignedInteger('version_number')->default(1)->after('variant_name');
                }
            });

            Schema::table('recipe_versions', function (Blueprint $table): void {
                try {
                    $table->unique(['product_id', 'variant_name', 'version_number'], 'recipe_version_unique_idx');
                } catch (\Throwable) {
                }

                try {
                    $table->index(['product_id', 'variant_name', 'is_active'], 'recipe_version_active_idx');
                } catch (\Throwable) {
                }
            });
        }

        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'variant_name')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->string('variant_name', 60)->default('default')->after('product_name')->index();
            });
        }
    }
};
