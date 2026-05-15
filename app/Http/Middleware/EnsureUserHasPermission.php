<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->hasPermission($permission)) {
            if ($permission === 'dashboard.view') {
                return redirect()->route('pos.index');
            }
            abort(403, __('messages.errors.permission_denied'));
        }

        return $next($request);
    }
}
