<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WaiterController extends Controller
{
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        // Waiter screen requires an active cashier shift
        if (Schema::hasTable('cashier_shifts')) {
            $hasActiveShift = CashierShift::query()
                ->where('status', 'open')
                ->exists();

            if (! $hasActiveShift) {
                return redirect()->route('pos.index')
                    ->with('error', __('messages.errors.cashier_shift_not_open'));
            }
        }

        $categories = Category::query()
            ->with('parent:id,name')
            ->orderByRaw("CASE WHEN type = 'main' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id'])
            ->map(static fn(Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent?->name,
            ])
            ->values();

        $products = Product::query()
            ->with('category:id,name,type,parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'category_id'])
            ->map(static function (Product $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'category_type' => $product->category?->type,
                    'category_parent_id' => $product->category?->parent_id,
                ];
            })
            ->values();

        return view('waiter.index', [
            'products' => $products,
            'categories' => $categories,
            'tables' => $this->buildTablesPayload(),
        ]);
    }

    private function buildTablesPayload(): array
    {
        $activeOrdersByTable = Order::query()
            ->where('order_type', 'dine_in')
            ->whereNotNull('restaurant_table_id')
            ->whereIn('status', Order::activeDineInStatuses())
            ->latest('order_serial')
            ->get(['order_serial', 'order_number', 'restaurant_table_id'])
            ->groupBy('restaurant_table_id')
            ->map(static fn($orders) => $orders->first());

        return RestaurantTable::query()
            ->orderByRaw('CAST(name AS UNSIGNED), name')
            ->get(['id', 'name', 'status'])
            ->map(static function (RestaurantTable $table) use ($activeOrdersByTable): array {
                $activeOrder = $activeOrdersByTable->get((int) $table->id);
                $isOccupied = $table->status === 'occupied' || (bool) $activeOrder;

                return [
                    'id' => $table->id,
                    'name' => $table->name,
                    'status' => $isOccupied ? 'occupied' : 'available',
                    'active_order_id' => $activeOrder?->order_serial,
                    'active_order_number' => $activeOrder?->order_number,
                ];
            })
            ->values()
            ->all();
    }
}
