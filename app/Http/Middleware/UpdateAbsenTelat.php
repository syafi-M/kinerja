<?php

namespace App\Http\Middleware;

use App\Models\Absensi;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateAbsenTelat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Carbon::now()->format('H:i:s') > '11:20:00') {
        //     Absensi::with('shift')->whereIn('shift_id', [1, 2])
        //         ->update(['absensi_type_pulang' => 'Tidak Absen Pulang']); // ✅ 1 DB query
        // }


        return $next($request);
    }
}
