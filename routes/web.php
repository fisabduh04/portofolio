<?php

use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\CobaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasSiswaController;

Route::get('/', function () {
    return view('kelas.index');
    // return view('welcome');
});



Route::resource('tahun', TahunController::class);
Route::resource('dashboard', DashboardController::class);


//route kelas
Route::resource('kelas', KelasController::class);
Route::get('exportkelas', [KelasController::class, 'export'])->name('exportkelas');
Route::post('importkelas', [KelasController::class, 'import'])->name('importkelas');


// route jurusan
Route::resource('jurusan', JurusanController::class);

// route mata pelajaran
Route::resource('mapel', MapelController::class);
Route::post('importmapel', [MapelController::class, 'import'])->name('importmapel');
Route::get('exporttmapel', [MapelController::class, 'export'])->name('exportportmapel');

// route pegawai
Route::resource('pegawai', PegawaiController::class);
Route::post('importpegawai', [PegawaiController::class, 'import'])->name('importpegawai');
Route::get('exportpegawai', [PegawaiController::class, 'export'])->name('exportpegawai');

// route siswa
Route::resource('siswa', SiswaController::class);
Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
Route::get('exportsiswa', [SiswaController::class, 'export'])->name('exportsiswa');
Route::post('importsiswa', [SiswaController::class, 'import'])->name('importsiswa');

//Route Kelas Siswa
Route::resource('kelassiswa', KelasSiswaController::class);
Route::get('/kelas-siswa-export', [KelasSiswaController::class, 'export'])->name('kelas-siswa-export');
Route::post('kelas-siswa-import', [KelasSiswaController::class, 'import'])->name('kelas-siswa-import');

// Daftar Hadir Siswa
Route::resource('absensi', AbsensiController::class);

//Jadwal mata pelajaran
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
Route::get('/jadwal/data', [JadwalController::class, 'getJadwalJson']);
Route::post('/jadwal', [JadwalController::class, 'store']);
Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
// Route::delete('/jadwal/bulk-delete', [JadwalController::class, 'bulkDelete']);
// Route::delete('/jadwal/delete', [JadwalController::class, 'destroy'])->name('jadwal.delete');

// Route untuk menghapus satu jadwal (Individual Delete)
Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
Route::post('/jadwal/update-all', [JadwalController::class, 'updateAll'])->name('jadwal.updateAll');




// Route uji coba

Route::get('/users', [CobaController::class, 'tableView']); // Tampilkan halaman Blade
Route::get('/coba', [CobaController::class, 'index']);       // Untuk fetch data JSON
Route::get('/coba3', [CobaController::class, 'tampil3']);       // Untuk fetch data JSON
Route::post('/users', [CobaController::class, 'store']);     // Tambah user
Route::put('/users/{id}', [CobaController::class, 'update']); // Edit user
Route::delete('/users/{id}', [CobaController::class, 'destroy']); // Hapus user
Route::get('/coba2', [CobaController::class, 'tampil2']); // Tampilkan halaman Blade untuk coba2
