<?php

namespace App\Http\Controllers;

use App\Models\Monev;
use App\Models\Kerjasama;
use App\Models\Client;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class MonevController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $monev = Monev::all();
        return view('monev.index', compact('monev'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $lokasi = Lokasi::with('client')->get();
        // dd($lokasi);
        return view('monev.create', compact('lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Monev $monev)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monev $monev)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Monev $monev)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Monev $monev)
    {
        //
    }
}
