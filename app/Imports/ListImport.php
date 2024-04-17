<?php

namespace App\Imports;

use App\Models\ListPekerjaan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ListImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new ListPekerjaan([
            'ruangan_id' => $row['ruangan_id'],
            'name' => $row['name']
        ]);
    }
}
