<?php

namespace App\Imports;

use App\Models\Hiburan;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImportHiburan implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Hiburan|null
     */
    public function model(array $row)
    {
        return new Hiburan([
            'nib'           => $row['nib'],
            'namaHiburan'   => $row['namaHiburan'],
            'resiko'        => $row['resiko'],
            'skalaUsaha'    => $row['skalaUsaha'],
            'alamat'        => $row['alamat'],
            'koordinat'     => $row['koordinat'],
            'namaPj'        => $row['namaPj'],
            'nikPj'         => $row['nikPj'],
            'pendidikanPj'  => $row['pendidikanPj'],
            'teleponPj'     => $row['teleponPj'],
            'wargaNegaraPj' => $row['wargaNegaraPj'],
            'emailPj'       => $row['emailPj'],
            'passwordPj'    => $row['passwordPj'],
            'surveyor_id'   => $row['surveyor_id'],
            'created_at'    => $row['created_at'],
        ]);
    }
}