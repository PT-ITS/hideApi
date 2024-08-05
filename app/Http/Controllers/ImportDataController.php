<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DataImportHotel;
use App\Imports\DataImportHiburan;
use App\Imports\DataImportFnb;
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
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
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
                        'created_at'    => $created_at,
                    ]);
                    // Coba simpan hotel ke database
                    if ($hotel->save()) {
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
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    
                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'surveyor_id'         => $data['surveyor_id'],
                        'created_at'          => $created_at,
                    ]);
                    $karyawanHotel = new KaryawanHotel([
                        'karyawan_id'        => $data['karyawan_id'],
                        'hotel_id'         => $data['hotel_id'],
                        'created_at'          => $created_at,
                    ]);
    
                    // Coba simpan hotel ke database
                    if ($karyawan->save() && $karyawanHotel->save()) {
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
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
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
                        'surveyor_id'   => $data['surveyor_id'],
                        'emailPj'       => $data['emailpj'],
                        'passwordPj'    => $data['passwordpj'],
                        'created_at'    => $created_at,
                    ]);
                    // Coba simpan hiburan ke database
                    if ($hiburan->save()) {
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
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    
                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'surveyor_id'         => $data['surveyor_id'],
                        'created_at'          => $created_at,
                    ]);
                    $karyawanHiburan = new KaryawanHiburan([
                        'karyawan_id'        => $data['karyawan_id'],
                        'hotel_id'         => $data['hotel_id'],
                        'created_at'          => $created_at,
                    ]);
    
                    // Coba simpan hotel ke database
                    if ($karyawan->save() && $karyawanHiburan->save()) {
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
    
            foreach ($importedData as $index => $data) {
                try {
                    // Validasi dan konversi format tanggal
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
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
                        'surveyor_id'   => $data['surveyor_id'],
                        'emailPj'       => $data['emailpj'],
                        'passwordPj'    => $data['passwordpj'],
                        'created_at'    => $created_at,
                    ]);
                    // Coba simpan hiburan ke database
                    if ($fnb->save()) {
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
                    $created_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['created_at']))->format('Y-m-d');

                    // Lakukan validasi atau manipulasi data sesuai kebutuhan
                    
                    $karyawan = new Karyawan([
                        'namaKaryawan'        => $data['namakaryawan'],
                        'pendidikanKaryawan'  => $data['pendidikankaryawan'],
                        'jabatanKaryawan'     => $data['jabatankaryawan'],
                        'alamatKaryawan'      => $data['alamatkaryawan'],
                        'sertifikasiKaryawan' => $data['sertifikasikaryawan'],
                        'wargaNegara'         => $data['warganegara'],
                        'surveyor_id'         => $data['surveyor_id'],
                        'created_at'          => $created_at,
                    ]);
                    $karyawanFnb = new KaryawanFnb([
                        'karyawan_id'        => $data['karyawan_id'],
                        'hotel_id'         => $data['hotel_id'],
                        'created_at'          => $created_at,
                    ]);
    
                    // Coba simpan hotel ke database
                    if ($karyawan->save() && $karyawanFnb->save()) {
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
}
