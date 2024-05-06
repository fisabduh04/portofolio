<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\CobaController;

Route::get('/', function () {
    return view('tampilan.main');
});

Route::resource('tahun', TahunController::class);


//route kelas
Route::resource('kelas', KelasController::class);
Route::get('exportkelas', [KelasController::class,'export'])->name('exportkelas');
Route::post('importkelas', [KelasController::class,'import'])->name('importkelas');


// route jurusan
Route::resource('jurusan', JurusanController::class);

// route mata pelajaran
Route::resource('mapel', MapelController::class);
Route::post('importmapel', [MapelController::class,'import'])->name('importmapel');
Route::get('exporttmapel', [MapelController::class,'export'])->name('exportportmapel');

// route pegawai
Route::resource('pegawai',PegawaiController::class);
Route::get('Addpegawai', [PegawaiController::class, 'Addpegawai'])->name('tambahpegawai');
Route::get('pegawai/{id}', [PegawaiController::class, 'edit'])->name('editpegawai');
Route::post('importpegawai', [PegawaiController::class,'import'])->name('importpegawai');
Route::get('exportpegawai', [PegawaiController::class,'export'])->name('exportpegawai');

// route siswa
Route::resource('siswa', SiswaController::class);
Route::delete('/siswa/{siswa}', [SiswaController::class,'destroy'])->name('siswa.destroy');
Route::get('exportsiswa', [SiswaController::class,'export'])->name('exportsiswa');
Route::post('importsiswa', [SiswaController::class,'import'])->name('importsiswa');
Route::get('kelassiswa', [SiswaController::class,'kelassiswa'])->name('kelassiswa');






// Route uji coba
Route::get('/coba', [CobaController::class, 'index']);
Route::get('/tabel', [CobaController::class, 'tabel']);




Route::get('/tutorial', function () {
  return view('coba.tutorial');
});

Route::get('/tampilan', function () {
  return view('coba.tabel');
});

Route::get('/employe', function () {
    return view('welcome');
  });

// Route::get('tutorial', Tutorial::class);
