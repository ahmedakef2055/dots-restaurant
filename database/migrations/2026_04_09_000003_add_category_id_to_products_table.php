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
        if (! Schema::hasTable('products') || Schema::hasColumn('products', 'category_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('category_id')
                ->nullable()
                ->after('image_url')
                ->constrained('categories')
                ->nullOnDelete();
        });

        $this->backfillProductCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'category_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    private function backfillProductCategories(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $categories = DB::table('categories')
            ->select(['id', 'name'])
            ->get();

        if ($categories->isEmpty()) {
            return;
        }

        $products = DB::table('products')
            ->whereNull('category_id')
            ->select(['id', 'name'])
            ->get();

        foreach ($products as $product) {
            $productName = Str::lower((string) $product->name);

            $matchedCategory = $categories->first(function ($category) use ($productName): bool {
                $categoryName = Str::lower(trim((string) $category->name));

                if ($categoryName === '') {
                    return false;
                }

                $singularName = Str::endsWith($categoryName, 's')
                    ? Str::substr($categoryName, 0, -1)
                    : $categoryName;

                return Str::contains($productName, $categoryName)
                    || ($singularName !== '' && Str::contains($productName, $singularName));
            });

            if (! $matchedCategory) {
                continue;
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'category_id' => $matchedCategory->id,
                ]);
        }
    }
};
