<?php

namespace App\Imports;

use App\Models\Fnb;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImportFnb implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Fnb|null
     */
    public function model(array $row)
    {
        return new Fnb([
            'nib'           => $row['nib'],
            'namaFnb'       => $row['namaFnb'],
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