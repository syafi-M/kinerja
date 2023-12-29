<?php

namespace App\Imports;

use App\Models\JadwalUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalImport implements ToModel, WithHeadingRow
{
     /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new JadwalUser([
            'user_id' => $row['user_id'],
            'shift_id' => $row['shift_id'],
            'tanggal' => $row['tanggal'],
            'area_id' => $row['area_id'],
            'status' => $row['status'],
        ]);
    }
}
