<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class DashboardRekapController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::where('id', '!=', auth()->user()->kerjasama->client_id);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        $client = $query->paginate(15);

        return view('spv_view.rekap.index', [
            'client' => $client
        ]);
    }

    public function indexOvertimes($id)
    {
        $client = Client::findOrFail($id);
        return view('spv_view.rekap.overtimes.index', [
            'client' => $client
        ]);
    }

    public function indexPersonOut($kerjasama)
    {
        if (auth()->user()->kerjasama_id != 1) abort(403);

        $client = Client::find($kerjasama);

        if (!$client) {
            abort(404);
        }

        return view('spv_view.rekap.person_out.index', compact('client'));
    }
}
