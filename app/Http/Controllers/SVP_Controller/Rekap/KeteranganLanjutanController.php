<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\KeteranganLanjutan;
use Illuminate\Http\Request;

class KeteranganLanjutanController extends Controller
{
    public function index()
    {
        $keteranganLanjutans = KeteranganLanjutan::with('user:id,nama_lengkap,kerjasama_id')
            ->whereHas('user', function ($q) {
                $q->whereHas('jabatan', function ($jabatanQuery) {
                    $jabatanQuery->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
                })->when($this->selectedClientId() > 0, fn($userQuery) => $userQuery->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        
        return response()->json([
            'success' => true,
            'data' => $keteranganLanjutans,
            'message' => 'Data personil masuk berhasil diambil'
        ]);
    }
}
