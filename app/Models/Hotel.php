<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nib',
        'namaHotel',
        'bintangHotel',
        'kamarVip',
        'kamarStandart',
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
        'created_at'
    ];

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_hotels');
    }
}
