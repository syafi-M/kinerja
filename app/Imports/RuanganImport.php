<?php

namespace App\Imports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuanganImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
     public function model(array $row)
    {
        return new Ruangan([
            'kerjasama_id' => $row['kerjasama_id'],
            'nama_ruangan' => $row['nama_ruangan'],
        ]);
    }
}
