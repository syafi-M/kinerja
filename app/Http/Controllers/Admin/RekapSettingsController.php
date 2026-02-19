<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekapDueDateSetting;
use Illuminate\Http\Request;

class RekapSettingsController extends Controller
{
    public function index()
    {
        $setting = RekapDueDateSetting::with('updater')->latest()->first();

        return view('admin.rekap.settings', [
            'setting' => $setting,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'due_date' => ['required', 'date'],
        ]);

        RekapDueDateSetting::create([
            'due_date' => $validated['due_date'],
            'updated_by' => auth()->id(),
        ]);

        toastr()->success('Due date rekap berhasil diperbarui.', 'success');
        return back();
    }
}
