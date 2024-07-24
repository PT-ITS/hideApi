<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportDataController;

Route::post('import-hotel', [ImportDataController::class, 'importDataHotel']);
Route::post('import-karyawan-hotel', [ImportDataController::class, 'importDataKaryawanHotel']);

Route::post('import-hiburan', [ImportDataController::class, 'importDataHiburan']);
Route::post('import-karyawan-hiburan', [ImportDataController::class, 'importDataKaryawanHiburan']);

Route::post('import-fnb', [ImportDataController::class, 'importDataFnb']);
Route::post('import-karyawan-fnb', [ImportDataController::class, 'importDataKaryawanFnb']);
