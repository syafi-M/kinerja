<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersistSpvwClientFilter
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionKey = 'spvw.selected_client_id';

        if ($request->has('reset_filter') && $request->boolean('reset_filter')) {
            $request->session()->forget($sessionKey);
        }

        if ($request->has('client_id')) {
            $clientId = max((int) $request->query('client_id', 0), 0);

            if ($clientId > 0) {
                $request->session()->put($sessionKey, $clientId);
            } else {
                $request->session()->forget($sessionKey);
            }
        }

        return $next($request);
    }
}
