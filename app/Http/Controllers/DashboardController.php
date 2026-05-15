<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\CurrencyFormatter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalOrders   = Order::count();
        $totalRevenue  = Order::where('status', 'paid')->sum('total');
        $totalCustomers = Customer::count();
        $todaySales    = Order::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $lowStockCount = Product::where('stock', 0)->count();

        $totalTables = \App\Models\RestaurantTable::count();
        $activeTables = \App\Models\RestaurantTable::where('status', '!=', 'available')->count();
        $tablesValue = "{$activeTables}/{$totalTables}";

        $kpis = [
            [
                'label'     => __('ui.dashboard.kpi.total_orders'),
                'value'     => (string) $totalOrders,
                'trend'     => '',
                'trendType' => 'neutral',
                'icon'      => 'receipt_long',
                'color'     => 'primary',
            ],
            [
                'label'     => __('ui.dashboard.kpi.revenue'),
                'value'     => CurrencyFormatter::format($totalRevenue),
                'trend'     => CurrencyFormatter::format($todaySales) . ' ' . __('ui.dashboard.kpi.today'),
                'trendType' => 'positive',
                'icon'      => 'payments',
                'color'     => 'tertiary',
            ],
            [
                'label'     => __('ui.dashboard.kpi.active_tables'),
                'value'     => $tablesValue,
                'trend'     => __('ui.dashboard.kpi.trend_active'),
                'trendType' => 'neutral',
                'icon'      => 'table_restaurant',
                'color'     => 'secondary',
            ],
            [
                'label'     => __('ui.dashboard.kpi.low_stock'),
                'value'     => (string) $lowStockCount,
                'trend'     => __('ui.dashboard.kpi.trend_action'),
                'trendType' => 'negative',
                'icon'      => 'warning',
                'color'     => 'error',
            ],
        ];

        // Sales trend: last 7 days revenue per day
        $salesTrend = Order::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $salesLabels = [];
        $salesValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $salesLabels[] = now()->subDays($i)->locale(app()->getLocale())->isoFormat('ddd');
            $salesValues[] = (float) ($salesTrend[$date] ?? 0);
        }

        // Orders per hour: today's orders grouped by hour
        $hourlyOrders = Order::whereDate('created_at', today())
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('cnt', 'hour');

        $hourLabels = [];
        $hourValues = [];
        for ($h = 10; $h <= 22; $h++) {
            $hourLabels[] = sprintf('%02d:00', $h);
            $hourValues[] = (int) ($hourlyOrders[$h] ?? 0);
        }

        $chartData = [
            'sales' => [
                'datasetLabel' => __('ui.dashboard.charts.sales_dataset'),
                'labels'       => $salesLabels,
                'values'       => $salesValues,
            ],
            'orders' => [
                'datasetLabel' => __('ui.dashboard.charts.orders_dataset'),
                'labels'       => $hourLabels,
                'values'       => $hourValues,
            ],
        ];

        $recentOrders = Order::with('restaurantTable')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (Order $order) {
                $table = $order->order_type === 'delivery'
                    ? __('ui.dashboard.order_types.delivery')
                    : ($order->restaurantTable?->name ?? '-');

                $kitchenStatus = $order->kitchen_status ?? $order->status;

                return [
                    'number'     => $order->order_daily_number ? '#' . $order->order_daily_number : '#' . $order->order_number,
                    'type'       => __('ui.dashboard.order_types.' . $order->order_type, [], null) ?: $order->order_type,
                    'table'      => $table,
                    'status_key' => $kitchenStatus,
                    'status'     => __('ui.dashboard.statuses.' . $kitchenStatus, [], null) ?: $kitchenStatus,
                    'amount'     => CurrencyFormatter::format($order->total),
                    'time'       => $order->created_at->diffForHumans(),
                ];
            })
            ->all();

        $topProducts = OrderItem::join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as sold'),
                DB::raw('SUM(order_items.line_total) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'name'    => $row->name,
                'sold'    => (int) $row->sold,
                'revenue' => CurrencyFormatter::format($row->revenue),
            ])
            ->all();

        return view('dashboard', compact('kpis', 'chartData', 'recentOrders', 'topProducts'));
    }
}
