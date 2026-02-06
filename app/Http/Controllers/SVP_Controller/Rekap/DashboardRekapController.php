<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Kerjasama;
use Illuminate\Http\Request;

class DashboardRekapController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()
            ->unreadNotifications
            ->groupBy('data.kerjasama_id');
        // $query = Client::where('id', '!=', auth()->user()->kerjasama->client_id);
        $query = Kerjasama::with('client')
            ->where('client_id', '!=', auth()->user()->kerjasama->client_id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('client', function ($c) use ($searchTerm) {
                    $c->where('name', 'like', '%' . $searchTerm . '%')->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $client = $query->paginate(15);

        return view('spv_view.rekap.index', [
            'client' => $client,
            'notifications' => $notifications,
        ]);
    }

    public function indexOvertimes($id)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $client = Client::findOrFail($kerjasama->client_id);
        return view('spv_view.rekap.overtimes.index', [
            'client' => $client
        ]);
    }

    public function indexPersonOut($kerjasama)
    {
        if (auth()->user()->kerjasama_id != 1) abort(403);

        $kerja = Kerjasama::findOrFail($kerjasama);
        $client = Client::findOrFail($kerja->client_id);

        if (!$client) {
            abort(404);
        }

        return view('spv_view.rekap.person_out.index', compact('client'));
    }
}
