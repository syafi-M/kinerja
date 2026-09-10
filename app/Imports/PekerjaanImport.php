<?php

namespace App\Imports;

use App\Models\PekerjaanCp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PekerjaanImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $userId = $this->normalize($row['user_id'] ?? null);
        $divisiId = $this->normalize($row['divisi_id'] ?? null);
        $kerjasamaId = $this->normalize($row['kerjasama_id'] ?? null);
        $name = $this->normalize($row['name'] ?? null);
        $typeCheck = $this->normalize($row['type_check'] ?? null);
        return PekerjaanCp::updateOrCreate(
            [
                'id' => $row['id']
            ],
            [
                'user_id' => $userId,
                'divisi_id' => $divisiId,
                'kerjasama_id' => $kerjasamaId,
                'name' => $name,
                'type_check' => $typeCheck,
            ]
        );
    }

    private function normalize($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || $value === '-' || $value === 'Kosong') {
            return null;
        }

        return $value;
    }
}
