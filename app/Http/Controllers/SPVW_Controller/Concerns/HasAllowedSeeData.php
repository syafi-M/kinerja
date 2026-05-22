<?php

namespace App\Http\Controllers\SPVW_Controller\Concerns;

trait HasAllowedSeeData
{
    protected function allowedSeeData(): array
    {
        $authJabatan = (int) (auth()->user()->jabatan_id ?? 0);

        return match ($authJabatan) {
            4, 5, 35 => [8, 11, 17, 18, 35],
            14, 20 => [9, 10, 12, 20, 22, 30],
            default => [],
        };
    }
}
