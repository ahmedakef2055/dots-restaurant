<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedPageHasPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $route = $request->route();

        if (! $route) {
            return $next($request);
        }

        $hasPermissionMiddleware = collect($route->gatherMiddleware())
            ->contains(fn(string $middleware): bool => str_starts_with($middleware, 'permission:'));

        if (! $hasPermissionMiddleware) {
            abort(403, __('messages.errors.permission_not_configured'));
        }

        return $next($request);
    }
}
