<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FnB extends Model
{
    use HasFactory;

    protected $fillable = [
        'nib',
        'namaFnb',
        'resiko',
        'skalaUsaha',
        'alamat',
        'koordinat',
        'namaPj',
        'nikPj',
        'pendidikanPj',
        'teleponPj',
        'wargaNegaraPj',
        'surveyor_id',
        'emailPj',
        'passwordPj',
        'pj_id',
        'created_at'
    ];
}
