<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['en', 'ar']);
        $fallbackLocale = config('app.fallback_locale', 'en');

        $sessionLocale = $request->session()->get('locale');
        $cookieLocale = $request->cookie('locale');

        $locale = $sessionLocale ?? $cookieLocale ?? $fallbackLocale;

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $fallbackLocale;
        }

        App::setLocale($locale);

        if ($request->session()->get('locale') !== $locale) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
