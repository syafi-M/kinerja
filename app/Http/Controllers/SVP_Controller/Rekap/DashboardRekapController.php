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
            ->sortByDesc('created_at')
            ->groupBy('data.kerjasama_id');
        $query = Kerjasama::with('client');

        if ((int) auth()->user()->role_id !== 2) {
            $query->where('client_id', '!=', auth()->user()->kerjasama->client_id);
        }

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
        $kerja = Kerjasama::findOrFail($kerjasama);
        $client = Client::findOrFail($kerja->client_id);

        if (!$client) {
            abort(404);
        }

        return view('spv_view.rekap.person_out.index', compact('client'));
    }

    public function indexPersonIn($kerjasama)
    {
        $kerja = Kerjasama::findOrFail($kerjasama);
        $client = Client::findOrFail($kerja->client_id);

        return view('spv_view.rekap.person_in.index', compact('client'));
    }

    public function indexCutting($kerjasama)
    {
        $kerja = Kerjasama::findOrFail($kerjasama);
        $client = Client::findOrFail($kerja->client_id);

        return view('spv_view.rekap.cutting.index', compact('client'));
    }

    public function indexFinishedTraining($kerjasama)
    {
        $kerja = Kerjasama::findOrFail($kerjasama);
        $client = Client::findOrFail($kerja->client_id);

        return view('spv_view.rekap.finished_training.index', compact('client'));
    }
}
