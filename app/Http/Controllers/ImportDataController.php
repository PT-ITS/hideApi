<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DataImportHotel;
use App\Imports\DataImportHiburan;
use App\Imports\DataImportFnb;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Hotel;
use App\Models\KaryawanHotel;
use App\Models\Hiburan;
use App\Models\KaryawanHiburan;
use App\Models\Fnb;
use App\Models\KaryawanFnb;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ImportDataController extends Controller
{
    // public function importDataHotel(Request $request) 
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls',
    //     ]);
    
    //     try {
    //         // Simpan data yang akan diimpor
    //         $importedData = Excel::toArray(new DataImportHotel, $request->file('file'))[0];
    
    //         // Inisialisasi variabel hitungan
    //         $successDataCount = 0;
    //         $failDataCount = 0;
    //         $failedRows = [];
    //         $errors = [];

    //         $password = bcrypt("12345678");
    
    //         foreach ($importedData as $index => $data) {
    //             try {
    //                 // Validasi dan konversi format tanggal
    //                 $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

    //                 // Lakukan validasi atau manipulasi data sesuai kebutuhan
    //                 $userData = new User();
    //                 $userData->name = $data['namapj'];
    //                 $userData->email = $data['emailpj'];
    //                 $userData->password = $password;
    //                 $userData->alamat = $data['alamat'];
    //                 $userData->noHP = $data['teleponpj'];
    //                 $userData->level = '2';
    //                 $userData->status = '1';
                    
                    
    //                 // Coba simpan hotel ke database
    //                 if ($userData->save()) {
    //                     $hotel = new Hotel([
    //                         'nib'           => $data['nib'],
    //                         'namaHotel'     => $data['namahotel'],
    //                         'bintangHotel'  => $data['bintanghotel'],
    //                         'kamarVip'      => $data['kamarvip'],
    //                         'kamarStandart' => $data['kamarstandart'],
    //                         'resiko'        => $data['resiko'],
    //                         'skalaUsaha'    => $data['skalausaha'],
    //                         'alamat'        => $data['alamat'],
    //                         'koordinat'     => $data['koordinat'],
    //                         'namaPj'        => $data['namapj'],
    //                         'nikPj'         => $data['nikpj'],
    //                         'pendidikanPj'  => $data['pendidikanpj'],
    //                         'teleponPj'     => $data['teleponpj'],
    //                         'wargaNegaraPj' => $data['warganegarapj'],
    //                         'emailPj'       => $data['emailpj'],
    //                         'passwordPj'    => $data['passwordpj'],
    //                         'surveyor_id'   => $data['surveyor_id'], 
    //                         'pj_id'         => $userData->id, 
    //                         'created_at'    => $created_at,
    //                     ]);
    //                     $hotel->save();
    //                     // Jika berhasil, tambahkan ke hitungan data yang berhasil
    //                     $successDataCount++;
    //                 } else {
    //                     // Jika gagal disimpan ke database, tambahkan ke hitungan data yang gagal
    //                     $failDataCount++;
    //                     $failedRows[] = $index + 1; // Catat baris yang gagal
    //                     $errors[] = "Gagal menyimpan data di baris " . ($index + 1);
    //                 }
    //             } catch (\Exception $e) {
    //                 // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
    //                 $failDataCount++;
    //                 $failedRows[] = $index + 1; // Catat baris yang gagal
    //                 $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
    //             }
    //         }
    
    //         return response()->json([
    //             'message' => 'Data berhasil diimpor.',
    //             'success_data_count' => $successDataCount,
    //             'fail_data_count' => $failDataCount,
    //             'failed_rows' => $failedRows,
    //             'errors' => $errors,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function importDataHotel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportHotel, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
    
            $defaultPassword = bcrypt("12345678");
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal dari Excel
                    $createdDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');
    
                    // Generate jam, menit, dan detik secara acak
                    $randomHour = rand(0, 23);
                    $randomMinute = rand(0, 59);
                    $randomSecond = rand(0, 59);
    
                    // Gabungkan tanggal dengan waktu acak
                    $created_at = Carbon::parse($createdDate)
                        ->setTime($randomHour, $randomMinute, $randomSecond)
                        ->format('Y-m-d H:i:s');
    
                    // Cek apakah email sudah ada di tabel User
                    $existingUser = User::where('email', $data['emailpj'])->first();
    
                    if ($existingUser) {
                        // Jika pengguna sudah ada, gunakan ID pengguna tersebut sebagai pj_id
                        $userId = $existingUser->id;
                    } else {
                        // Jika pengguna tidak ada, buat pengguna baru
                        $userData = new User([
                            'name' => $data['namapj'],
                            'email' => $data['emailpj'],
                            'password' => $defaultPassword,
                            'alamat' => $data['alamat'],
                            'noHP' => $data['teleponpj'],
                            'level' => '2',
                            'status' => '1',
                        ]);
                        $userData->save();
    
                        // Dapatkan ID pengguna yang baru dibuat
                        $userId = $userData->id;
                    }
    
                    // Simpan data hotel dengan menggunakan ID pengguna sebagai pj_id
                    $hotel = new Hotel([
                        'nib'           => $data['nib'],
                        'namaHotel'     => $data['namahotel'],
                        'bintangHotel'  => $data['bintanghotel'],
                        'kamarVip'      => $data['kamarvip'],
                        'kamarStandart' => $data['kamarstandart'],
                        'resiko'        => $data['resiko'],
                        'skalaUsaha'    => $data['skalausaha'],
                        'alamat'        => $data['alamat'],
                        'koordinat'     => $data['koordinat'],
                        'namaPj'        => $data['namapj'],
                        'nikPj'         => $data['nikpj'],
                        'pendidikanPj'  => $data['pendidikanpj'],
                        'teleponPj'     => $data['teleponpj'],
                        'wargaNegaraPj' => $data['warganegarapj'],
                        'emailPj'       => $data['emailpj'],
                        'passwordPj'    => $data['passwordpj'],
                        'surveyor_id'   => $data['surveyor_id'],
                        'pj_id'         => $userId,
                        'created_at'    => $created_at,
                    ]);
    
                    if ($hotel->save()) {
                        $successDataCount++;
                    } else {
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data hotel di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    


    public function importDataKaryawanHotel(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportHotel, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    // $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    $dataHotel = Hotel::find($data['hotel_id']);

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    
                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'jenisKelamin'        => $data['jeniskelamin'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'jenisKelamin'        => $data['jeniskelamin'],
                        'surveyor_id'         => $dataHotel->surveyor_id,
                        'created_at'          => $dataHotel->created_at,
                    ]);
                    
                    // Coba simpan hotel ke database
                    if ($karyawan->save()) {
                        $karyawanHotel = new KaryawanHotel([
                            'karyawan_id'   => $data['karyawan_id'],
                            'hotel_id'      => $data['hotel_id'],
                            'created_at'    => $dataHotel->created_at,
                        ]);
                        $karyawanHotel->save();
                        // Jika berhasil, tambahkan ke hitungan data yang berhasil
                        $successDataCount++;
                    } else {
                        // Jika gagal disimpan ke database, tambahkan ke hitungan data yang gagal
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }

    public function importDataHiburan(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportHiburan, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
            $defaultPassword = bcrypt("12345678");
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    $createdDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');
    
                    $randomHour = rand(0, 23);
                    $randomMinute = rand(0, 59);
                    $randomSecond = rand(0, 59);
    
                    // Gabungkan tanggal dengan waktu acak
                    $created_at = Carbon::parse($createdDate)
                        ->setTime($randomHour, $randomMinute, $randomSecond)
                        ->format('Y-m-d H:i:s');

                    // Cek apakah email sudah ada di tabel User
                    $existingUser = User::where('email', $data['emailpj'])->first();
    
                    if ($existingUser) {
                        // Jika pengguna sudah ada, gunakan ID pengguna tersebut sebagai pj_id
                        $userId = $existingUser->id;
                    } else {
                        // Jika pengguna tidak ada, buat pengguna baru
                        $userData = new User([
                            'name' => $data['namapj'],
                            'email' => $data['emailpj'],
                            'password' => $defaultPassword,
                            'alamat' => $data['alamat'],
                            'noHP' => $data['teleponpj'],
                            'level' => '2',
                            'status' => '1',
                        ]);
                        $userData->save();
    
                        // Dapatkan ID pengguna yang baru dibuat
                        $userId = $userData->id;
                    }
    
                    // Simpan data hiburan dengan menggunakan ID pengguna sebagai pj_id
                    $hiburan = new Hiburan([
                        'nib'           => $data['nib'],
                        'namaHiburan'   => $data['namahiburan'],
                        'resiko'        => $data['resiko'],
                        'skalaUsaha'    => $data['skalausaha'],
                        'alamat'        => $data['alamat'],
                        'koordinat'     => $data['koordinat'],
                        'namaPj'        => $data['namapj'],
                        'nikPj'         => $data['nikpj'],
                        'pendidikanPj'  => $data['pendidikanpj'],
                        'teleponPj'     => $data['teleponpj'],
                        'wargaNegaraPj' => $data['warganegarapj'],
                        'emailPj'       => $data['emailpj'],
                        'passwordPj'    => $data['passwordpj'],
                        'surveyor_id'   => $data['surveyor_id'],
                        'pj_id'         => $userId, // Gunakan ID pengguna sebagai pj_id
                        'created_at'    => $created_at,
                    ]);
    
                    if ($hiburan->save()) {
                        $successDataCount++;
                    } else {
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data hiburan di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }
    

    public function importDataKaryawanHiburan(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportHiburan, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    // $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    $dataHiburan = Hiburan::find($data['hiburan_id']);
                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    
                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'jenisKelamin'        => $data['jeniskelamin'],
                        'surveyor_id'         => $dataHiburan->surveyor_id,
                        'created_at'          => $dataHiburan->created_at,
                    ]);
                    
                    // Coba simpan hotel ke database
                    if ($karyawan->save()) {
                        $karyawanHiburan = new KaryawanHiburan([
                            'karyawan_id'        => $data['karyawan_id'],
                            'hiburan_id'         => $data['hiburan_id'],
                            'created_at'          => $dataHiburan->created_at,
                        ]);
                        $karyawanHiburan->save();
                        // Jika berhasil, tambahkan ke hitungan data yang berhasil
                        $successDataCount++;
                    } else {
                        // Jika gagal disimpan ke database, tambahkan ke hitungan data yang gagal
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }

    public function importDataFnb(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportFnb, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
            $defaultPassword = bcrypt("12345678");
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    $createdDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    $randomHour = rand(0, 23);
                    $randomMinute = rand(0, 59);
                    $randomSecond = rand(0, 59);
    
                    // Gabungkan tanggal dengan waktu acak
                    $created_at = Carbon::parse($createdDate)
                        ->setTime($randomHour, $randomMinute, $randomSecond)
                        ->format('Y-m-d H:i:s');
    
                    // Cek apakah email sudah ada di tabel User
                    $existingUser = User::where('email', $data['emailpj'])->first();
    
                    if ($existingUser) {
                        // Jika pengguna sudah ada, gunakan ID pengguna tersebut sebagai pj_id
                        $userId = $existingUser->id;
                    } else {
                        // Jika pengguna tidak ada, buat pengguna baru
                        $userData = new User([
                            'name' => $data['namapj'],
                            'email' => $data['emailpj'],
                            'password' => $defaultPassword,
                            'alamat' => $data['alamat'],
                            'noHP' => $data['teleponpj'],
                            'level' => '2',
                            'status' => '1',
                        ]);
                        $userData->save();
    
                        // Dapatkan ID pengguna yang baru dibuat
                        $userId = $userData->id;
                    }
    
                    // Simpan data F&B dengan menggunakan ID pengguna sebagai pj_id
                    $fnb = new Fnb([
                        'nib'           => $data['nib'],
                        'namaFnb'       => $data['namafnb'],
                        'resiko'        => $data['resiko'],
                        'skalaUsaha'    => $data['skalausaha'],
                        'alamat'        => $data['alamat'],
                        'koordinat'     => $data['koordinat'],
                        'namaPj'        => $data['namapj'],
                        'nikPj'         => $data['nikpj'],
                        'pendidikanPj'  => $data['pendidikanpj'],
                        'teleponPj'     => $data['teleponpj'],
                        'wargaNegaraPj' => $data['warganegarapj'],
                        'emailPj'       => $data['emailpj'],
                        'passwordPj'    => $data['passwordpj'],
                        'surveyor_id'   => $data['surveyor_id'],
                        'pj_id'         => $userId,
                        'created_at'    => $created_at,
                    ]);
    
                    if ($fnb->save()) {
                        $successDataCount++;
                    } else {
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data F&B di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }
    
    

    public function importDataKaryawanFnb(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Simpan data yang akan diimpor
            $importedData = Excel::toArray(new DataImportHiburan, $request->file('file'))[0];
    
            // Inisialisasi variabel hitungan
            $successDataCount = 0;
            $failDataCount = 0;
            $failedRows = [];
            $errors = [];
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    // $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    $dataFnb = Fnb::find($data['fnb_id']);

                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'jenisKelamin'        => $data['jeniskelamin'],
                        'surveyor_id'         => $dataFnb->surveyor_id,
                        'created_at'          => $dataFnb->created_at,
                    ]);
                    
                    // Coba simpan hotel ke database
                    if ($karyawan->save()) {
                        // Jika berhasil, tambahkan ke hitungan data yang berhasil
                        $karyawanFnb = new KaryawanFnb([
                            'karyawan_id'  => $karyawan->id,
                            'fnb_id'       => $data['fnb_id'],
                            'created_at'   => $dataFnb->created_at,
                        ]);
                        $karyawanFnb->save();
                        $successDataCount++;
                    } else {
                        // Jika gagal disimpan ke database, tambahkan ke hitungan data yang gagal
                        $failDataCount++;
                        $failedRows[] = $index + 1; // Catat baris yang gagal
                        $errors[] = "Gagal menyimpan data di baris " . ($index + 1);
                    }
                } catch (\Exception $e) {
                    // Jika ada kesalahan saat menyimpan data, tambahkan ke hitungan data yang gagal
                    $failDataCount++;
                    $failedRows[] = $index + 1; // Catat baris yang gagal
                    $errors[] = "Kesalahan di baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            return response()->json([
                'message' => 'Data berhasil diimpor.',
                'success_data_count' => $successDataCount,
                'fail_data_count' => $failDataCount,
                'failed_rows' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.', 'error' => $e->getMessage()], 500);
        }
    }
}
