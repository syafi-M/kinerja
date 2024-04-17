<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TempBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) : Response
    {
        // $user = Auth::user()->id;
        $direksi = User::where('name', 'DIREKSI')->first();
        $direksiID = $direksi->id;
        $week = Carbon::now()->subWeek();
        
        $unapprovedReportExists = DB::table('check_points')
            ->where('approve_status', '!=', 'accept')
            ->where('created_at', '>', $week)
            ->exists();
            
        if ($unapprovedReportExists) {
            $direksiID->update(['temp_ban' => 'true']);
        }

        
        return $next($request);
    }
}
