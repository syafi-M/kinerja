<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Concerns\LocksRekapByDueDate;
use App\Http\Controllers\Controller;
use App\Models\RekapDueDateSetting;

class DataRekapController extends Controller
{
    use LocksRekapByDueDate;

    public function index()
    {
        $dueDate = RekapDueDateSetting::latest()->first();
        $isAfterDueDate = $this->isSubmissionLockedByDueDate();

        return view('leader_view.data_rekap.index', [
            'dueDate' => $dueDate,
            'isAfterDueDate' => $isAfterDueDate,
            'isSubmissionLocked' => $isAfterDueDate,
        ]);
    }
}
