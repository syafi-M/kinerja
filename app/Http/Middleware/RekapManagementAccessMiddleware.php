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

        $jabatans = collect([
            $user->jabatan,
            $user->divisi?->jabatan,
        ])->filter();

        $allowedNames = [
            'MARKETING',
            'SPV',
        ];

        $allowedCodes = [
            'MARKETING',
            'SPV',
            'SVP-P',
            'SPV-A',
        ];

        $hasAccess = $jabatans->contains(function ($jabatan) use ($allowedNames, $allowedCodes): bool {
            $nameJabatan = strtoupper(trim((string) $jabatan->name_jabatan));
            $codeJabatan = strtoupper(trim((string) $jabatan->code_jabatan));

            return in_array($nameJabatan, $allowedNames, true) ||
                in_array($codeJabatan, $allowedCodes, true);
        });

        if (!$hasAccess) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
