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
        $start = Carbon::today()->setTime(15, 20);
        $end = Carbon::today()->setTime(16, 30);

        if (Carbon::now()->format('H:i:s') > '11:20:00') {
            $absensis = Absensi::with('shift')
                ->whereIn('shift_id', [1, 2])
                ->get();

            foreach ($absensis as $absen) {
                $randomTimestamp = mt_rand($start->timestamp, $end->timestamp);
                $randomTime = Carbon::createFromTimestamp($randomTimestamp)->format('H:i:s');

                $absen->absensi_type_pulang = $randomTime;
                $absen->save();
            }
        }


        return $next($request);
    }
}
