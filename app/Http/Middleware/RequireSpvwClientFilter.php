<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSpvwClientFilter
{
    public function handle(Request $request, Closure $next): Response
    {
        $selectedClientId = (int) $request->input('client_id', (int) $request->session()->get('spvw.selected_client_id', 0));

        if ($selectedClientId <= 0) {
            return redirect()
                ->route('spvw.rekap.index')
                ->with('toast', [
                    'type' => 'info',
                    'message' => 'Pilih client terlebih dahulu sebelum mengakses menu rekap SPV-W.',
                ]);
        }

        return $next($request);
    }
}
