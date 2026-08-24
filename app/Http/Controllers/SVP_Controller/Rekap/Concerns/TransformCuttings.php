<?php

namespace App\Http\Controllers\SVP_Controller\Rekap\Concerns;

trait TransformCuttings
{
    protected function transformCuttings($cuttings)
    {

        return $cuttings->groupBy(function ($item) {
            return $item->user_id . '_' . strtolower($item->type_cut);
        })->map(function ($group) {

            $first = $group->first();
            $typeCuts = strtolower($first->type_cut);

            $tgl = $group
                ->pluck('date_cut')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            return [
                'id' => $first->id,
                'user' => $first->user,
                'date_cut' => $first->date_cut,
                'status' => $first->status,
                'desc' => $first->desc,
                'tgl' => $tgl,
                'count' => $group->count(),
                'createdBy' => $first->createdBy ?? null,
            ];
        })->values();
    }
}
