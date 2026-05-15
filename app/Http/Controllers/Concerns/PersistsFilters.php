<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait PersistsFilters
{
    /**
     * Restore saved filter from session if no filter params are in the current request.
     * Then save the active filter back to session.
     *
     * @param  string[]  $keys  The request parameter names that count as "filter params"
     */
    protected function handleFilters(Request $request, string $sessionKey, array $keys): void
    {
        if (! $request->hasAny($keys)) {
            $saved = $request->session()->get("filters.{$sessionKey}", []);
            if (! empty($saved)) {
                $request->merge($saved);
            }
        }
    }

    protected function saveFilters(Request $request, string $sessionKey, array $values): void
    {
        $request->session()->put("filters.{$sessionKey}", array_filter(
            $values,
            fn($v) => $v !== null && $v !== ''
        ));
    }
}
