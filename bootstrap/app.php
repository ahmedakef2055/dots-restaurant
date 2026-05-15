<?php

use App\Http\Middleware\AuthenticateUser;
use App\Http\Middleware\EnsureAuthenticatedPageHasPermission;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\PersistFilters;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            PersistFilters::class,
        ]);

        $middleware->alias([
            'auth.custom' => AuthenticateUser::class,
            'permission.required' => EnsureAuthenticatedPageHasPermission::class,
            'permission' => EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
