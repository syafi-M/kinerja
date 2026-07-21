<?php

namespace App\Http\Controllers\SVP_Controller\Rekap\Concerns;

trait HasAllowedSeeData
{
    protected function allowedSeeData(): array
    {
        $authJabatan = (int) (auth()->user()->jabatan_id ?? 0);

        return match ($authJabatan) {
            4, 5 => [8, 11, 17, 18, 35],
            14 => [9, 10, 12, 20, 22, 30],
            35 => [8, 11, 16, 17, 18],
            20 => [9, 10, 34, 36],
            default => [],
        };
    }
}

