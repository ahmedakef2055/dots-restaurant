<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (! Schema::hasColumn('products', 'preparation_station')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->enum('preparation_station', ['kitchen', 'bar'])
                    ->default('kitchen')
                    ->after('category_id')
                    ->index();
            });

            $this->backfillBarProductsByCategoryName();
        }

        if (! Schema::hasColumn('products', 'description')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->text('description')->nullable()->after('preparation_station');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasColumn('products', 'description')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('products', 'preparation_station')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropColumn('preparation_station');
            });
        }
    }

    private function backfillBarProductsByCategoryName(): void
    {
        if (! Schema::hasTable('categories') || ! Schema::hasColumn('products', 'category_id')) {
            return;
        }

        $keywords = ['drink', 'drinks', 'beverage', 'beverages', 'juice', 'juices', 'coffee', 'tea', 'soda', 'bar', 'مشروب', 'مشروبات', 'عصير', 'عصائر', 'قهوة', 'شاي'];

        $barCategoryIds = DB::table('categories')
            ->select(['id', 'name'])
            ->get()
            ->filter(function ($category) use ($keywords): bool {
                $name = Str::lower((string) $category->name);

                foreach ($keywords as $keyword) {
                    if (Str::contains($name, Str::lower($keyword))) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('id')
            ->values();

        if ($barCategoryIds->isEmpty()) {
            return;
        }

        DB::table('products')
            ->whereIn('category_id', $barCategoryIds->all())
            ->update(['preparation_station' => 'bar']);
    }
};
