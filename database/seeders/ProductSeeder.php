<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Margherita Pizza', 'sku' => 'PIZ-001', 'price' => 24.90, 'stock' => 120, 'category_name' => 'Pizza'],
            ['name' => 'Pepperoni Pizza', 'sku' => 'PIZ-002', 'price' => 27.50, 'stock' => 100, 'category_name' => 'Pizza'],
            ['name' => 'Classic Burger', 'sku' => 'BRG-001', 'price' => 18.00, 'stock' => 150, 'category_name' => 'Burgers'],
            ['name' => 'Chicken Burger', 'sku' => 'BRG-002', 'price' => 19.50, 'stock' => 130, 'category_name' => 'Burgers'],
            ['name' => 'Pasta Alfredo', 'sku' => 'PST-001', 'price' => 21.00, 'stock' => 90, 'category_name' => 'Pasta'],
            ['name' => 'Caesar Salad', 'sku' => 'SLD-001', 'price' => 14.25, 'stock' => 80, 'category_name' => 'Salads'],
            ['name' => 'Grilled Salmon', 'sku' => 'SEA-001', 'price' => 32.00, 'stock' => 70, 'category_name' => 'Seafood'],
            ['name' => 'Fresh Orange Juice', 'sku' => 'DRK-001', 'price' => 7.50, 'stock' => 200, 'category_name' => 'Drinks'],
            ['name' => 'Chocolate Cake', 'sku' => 'DST-001', 'price' => 10.00, 'stock' => 60, 'category_name' => 'Desserts'],
            ['name' => 'Iced Latte', 'sku' => 'DRK-002', 'price' => 8.25, 'stock' => 180, 'category_name' => 'Drinks'],
        ];

        $categoryIds = collect($products)
            ->pluck('category_name')
            ->unique()
            ->mapWithKeys(function (string $categoryName): array {
                $category = Category::query()->firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'type' => 'main',
                        'parent_id' => null,
                    ]
                );

                return [$categoryName => $category->id];
            });

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'is_active' => true,
                    'category_id' => $categoryIds[$product['category_name']] ?? null,
                ]
            );
        }
    }
}
