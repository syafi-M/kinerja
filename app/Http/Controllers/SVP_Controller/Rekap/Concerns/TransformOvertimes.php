<?php

namespace App\Http\Controllers\SVP_Controller\Rekap\Concerns;

trait TransformOvertimes
{
    protected function transformOvertimes($overtimes)
    {
        return $overtimes->groupBy(function ($item) {
            return $item->user_id . '_' . strtolower($item->type_overtime);
        })->map(function ($group) {

            $first = $group->first();
            $typeOvertime = strtolower($first->type_overtime);

            if (in_array($typeOvertime, ['jam', 'lainnya'])) {

                $totalJam = 0;
                $totalRupiah = 0;

                foreach ($group as $overtime) {

                    $value = $this->parseOvertimeValue($overtime->type_overtime_manual);

                    if ($value['type'] == 'jam') {
                        $totalJam += $value['value'];
                    } elseif ($value['type'] == 'rupiah') {
                        $totalRupiah += $value['value'];
                    }
                }

                $formattedOvertime = '';

                if ($totalJam > 0 && $totalRupiah > 0) {
                    $formattedOvertime = $totalJam . ' Jam + Rp ' . number_format($totalRupiah, 0, ',', '.');
                } elseif ($totalJam > 0) {
                    $formattedOvertime = $totalJam . ' Jam';
                } elseif ($totalRupiah > 0) {
                    $formattedOvertime = 'Rp ' . number_format($totalRupiah, 0, ',', '.');
                }

                return [
                    'id' => $first->id,
                    'user' => $first->user,
                    'date_overtime' => $first->date_overtime,
                    'type_overtime' => $first->type_overtime,
                    'type_overtime_manual' => $formattedOvertime,
                    'total_jam' => $totalJam,
                    'total_rupiah' => $totalRupiah,
                    'status' => $first->status,
                    'desc' => $first->desc,
                    'count' => $group->count(),
                    'createdBy' => $first->createdBy ?? null,
                ];
            }

            return [
                'id' => $first->id,
                'user' => $first->user,
                'date_overtime' => $first->date_overtime,
                'type_overtime' => $first->type_overtime,
                'type_overtime_manual' => $first->type_overtime_manual,
                'status' => $first->status,
                'desc' => $first->desc,
                'count' => $group->count(),
                'createdBy' => $first->createdBy ?? null,
            ];
        })->values();
    }

    private function parseOvertimeValue($text)
    {
        if (!$text || !is_string($text)) {
            return ['type' => 'none', 'value' => 0];
        }

        $clean = preg_replace('/[^0-9]/', '', $text);

        if ($clean === '') {
            return ['type' => 'none', 'value' => 0];
        }

        $value = intval($clean);

        if ($value >= 1 && $value <= 500) {
            return ['type' => 'jam', 'value' => $value];
        }

        if ($value >= 1000) {
            return ['type' => 'rupiah', 'value' => $value];
        }

        return ['type' => 'none', 'value' => 0];
    }
}
