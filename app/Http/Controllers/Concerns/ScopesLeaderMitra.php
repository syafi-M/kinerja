<?php

namespace App\Http\Controllers\Concerns;

trait ScopesLeaderMitra
{
    private function leaderClientIds(): array
    {
        return auth()->id() == 157 ? [18, 21, 32] : [auth()->user()->kerjasama->client_id];
    }

    private function scopeLeaderUser($q): void
    {
        $q->when(
            auth()->id() == 157,
            fn($q) => $q->whereHas('kerjasama', fn($q) => $q->whereIn('client_id', $this->leaderClientIds())),
            fn($q) => $q->where('kerjasama_id', auth()->user()->kerjasama_id)
        )->whereHas('jabatan', function ($q) {
            $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
        });
    }
}
