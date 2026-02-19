<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RekapManagementAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(401);
        }

        $user = auth()->user();

        if ((int) $user->role_id === 2) {
            return $next($request);
        }

        $nameJabatan = strtoupper(trim((string) optional($user->jabatan)->name_jabatan));
        $codeJabatan = strtoupper(trim((string) optional($user->jabatan)->code_jabatan));

        $allowedNames = ['MARKETING', 'SPV'];
        $allowedCodes = ['MARKETING', 'SPV', 'SVP-P', 'SPV-A'];

        if (in_array($nameJabatan, $allowedNames, true) || in_array($codeJabatan, $allowedCodes, true)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
