<?php

namespace App\Http\Controllers\Concerns;

use App\Models\RekapDueDateSetting;

trait LocksRekapByDueDate
{
    protected function isSubmissionLockedByDueDate(): bool
    {
        $setting = RekapDueDateSetting::latest()->first();

        // No global batas waktu → unlocked.
        return $setting !== null && $setting->isLockedAt();
    }

    protected function rekapLockedMessage(): string
    {
        return 'Masa pengajuan rekap bulan ini sudah ditutup. Silakan tunggu bulan berikutnya.';
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse|null
     */
    protected function rejectIfRekapLocked()
    {
        if (!$this->isSubmissionLockedByDueDate()) {
            return null;
        }

        $message = $this->rekapLockedMessage();

        if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => null,
                'error' => 'locked',
            ], 403);
        }

        return back()->with('toast', [
            'type' => 'info',
            'message' => $message,
        ]);
    }
}
