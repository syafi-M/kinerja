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
        return new PekerjaanCp([
            'user_id' => $row['user_id'],
            'divisi_id' => $row['divisi_id'],
            'kerjasama_id' => $row['kerjasama_id'],
            'name' => $row['name'],
            'type_check' => $row['type_check']
        ]);
    }
}
