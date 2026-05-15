<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersistFilters
{
    private const FILTER_ROUTES = [
        'orders.index'   => ['q', 'status', 'order_type', 'from', 'to'],
        'products.index' => ['q', 'category_id', 'preparation_station'],
        'customers.index'=> ['q'],
        'suppliers.index'=> ['q', 'status'],
        'employees.index'=> ['q', 'status'],
        'users.index'    => ['q'],
        'categories.index'=> ['q', 'type'],
        'coupons.index'  => ['q', 'status'],
        'offers.index'   => ['q', 'status'],
        'tables.index'   => ['q', 'status'],
        'purchases.index'=> ['q', 'supplier_id', 'request_type', 'status', 'from', 'to'],
        'attendance.index'=> ['employee_id', 'status', 'from', 'to'],
        'salaries.index' => ['employee_id', 'status', 'from', 'to'],
        'reports.shift-logs'=> ['from', 'to', 'cashier_id'],
        'financial.index'=> ['from', 'to', 'type', 'payment_method'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();

        if (! isset(self::FILTER_ROUTES[$routeName])) {
            return $next($request);
        }

        $filterKeys = self::FILTER_ROUTES[$routeName];
        $sessionKey = "filters.{$routeName}";

        // If no filter params in URL, restore from session
        if (! $request->hasAny($filterKeys)) {
            $saved = $request->session()->get($sessionKey, []);
            if (! empty($saved)) {
                $request->merge($saved);
            }
        } else {
            // Save current filter to session
            $request->session()->put(
                $sessionKey,
                array_filter(
                    $request->only($filterKeys),
                    fn($v) => $v !== null && $v !== ''
                )
            );
        }

        return $next($request);
    }
}
