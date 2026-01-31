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

// Rute Publik (Redirect ke Login jika belum auth)
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Grup Keamanan
Route::middleware(['auth', 'active'])->group(function () {

    Route::resource('tahun', TahunController::class);
    Route::resource('dashboard', DashboardController::class);

    // Route kelas
    Route::resource('kelas', KelasController::class);
    Route::get('exportkelas', [KelasController::class, 'export'])->name('exportkelas');
    Route::post('importkelas', [KelasController::class, 'import'])->name('importkelas');

    // Route jurusan
    Route::resource('jurusan', JurusanController::class);

    // Route mata pelajaran
    Route::resource('mapel', MapelController::class);
    Route::post('importmapel', [MapelController::class, 'import'])->name('importmapel');
    Route::get('exporttmapel', [MapelController::class, 'export'])->name('exportportmapel');

    // Route pegawai
    Route::resource('pegawai', PegawaiController::class);
    Route::post('importpegawai', [PegawaiController::class, 'import'])->name('importpegawai');
    Route::get('exportpegawai', [PegawaiController::class, 'export'])->name('exportpegawai');

    // Route siswa
    Route::resource('siswa', SiswaController::class);
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('exportsiswa', [SiswaController::class, 'export'])->name('exportsiswa');
    Route::post('importsiswa', [SiswaController::class, 'import'])->name('importsiswa');

    // Route Kelas Siswa
    Route::resource('kelassiswa', KelasSiswaController::class);
    Route::get('/kelas-siswa-export', [KelasSiswaController::class, 'export'])->name('kelas-siswa-export');
    Route::post('kelas-siswa-import', [KelasSiswaController::class, 'import'])->name('kelas-siswa-import');

    // Daftar Hadir Siswa
    Route::resource('absensi', AbsensiController::class);
    // 1. AKSES UNTUK SEMUA ROLE (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // 2. AKSES KHUSUS ADMIN & OPERATOR (Manajemen Data Master)
    Route::middleware(['role:admin,operator'])->group(function () {
        Route::resource('tahun', TahunController::class);
        Route::resource('jurusan', JurusanController::class);
        Route::resource('pegawai', PegawaiController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('mapel', MapelController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('kelassiswa', KelasSiswaController::class);
        // Jadwal Specific Routes (Must be before resource to avoid ID conflict)
        Route::get('/jadwal/data', [JadwalController::class, 'getJadwalJson']);
        Route::delete('/jadwal/bulk-delete', [JadwalController::class, 'bulkDelete'])->name('jadwal.bulkDelete');
        Route::post('/jadwal/update-all', [JadwalController::class, 'updateAll'])->name('jadwal.updateAll');
        Route::get('/jadwal/export', [JadwalController::class, 'export'])->name('jadwal.export');
        Route::post('/jadwal/import', [JadwalController::class, 'import'])->name('jadwal.import');
        
        Route::resource('jadwal', JadwalController::class); // Generic resource last
        Route::get('/users', [CobaController::class, 'tableView'])->name('users.index');

        // Specific routes for resources that are not full resource controllers
        // Kelas
        Route::get('exportkelas', [KelasController::class, 'export'])->name('exportkelas');
        Route::post('importkelas', [KelasController::class, 'import'])->name('importkelas');
        // Mapel
        Route::post('importmapel', [MapelController::class, 'import'])->name('importmapel');
        Route::get('exporttmapel', [MapelController::class, 'export'])->name('exportportmapel');
        // Pegawai
        Route::post('importpegawai', [PegawaiController::class, 'import'])->name('importpegawai');
        Route::get('exportpegawai', [PegawaiController::class, 'export'])->name('exportpegawai');
        // Siswa
        Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDelete'])->name('siswa.bulkDelete');
        Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
        Route::get('exportsiswa', [SiswaController::class, 'export'])->name('exportsiswa');
        Route::post('importsiswa', [SiswaController::class, 'import'])->name('importsiswa');
        // Kelas Siswa
        Route::get('/kelas-siswa-export', [KelasSiswaController::class, 'export'])->name('kelas-siswa-export');
        Route::post('kelas-siswa-import', [KelasSiswaController::class, 'import'])->name('kelas-siswa-import');
        // Jadwal specific routes
        // Jadwal routes moved up
    });

    // 3. AKSES KHUSUS GURU & ADMIN (Presensi)
    Route::middleware(['role:guru,admin,operator'])->group(function () {
        Route::resource('absensi', AbsensiController::class);
    });

    // 4. AKSES REKAPITULASI (Kepala Sekolah, Admin, Operator)
    Route::middleware(['role:kepala,admin,operator'])->group(function () {
        Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
        Route::get('/absensi/rekap-bulanan', [AbsensiController::class, 'rekapBulanan'])->name('absensi.rekap-bulanan');
        Route::get('/absensi/rekap-tahunan', [AbsensiController::class, 'rekapTahunan'])->name('absensi.rekap-tahunan');
        Route::get('/jadwal/rekap', [JadwalController::class, 'rekap'])->name('jadwal.rekap');
    });
});

// RUTE UJI COBA (Hanya untuk Developer/Tanpa Auth jika diperlukan)
Route::get('/coba', [CobaController::class, 'index']);
Route::get('/coba3', [CobaController::class, 'tampil3']);
Route::post('/users', [CobaController::class, 'store']);
Route::put('/users/{id}', [CobaController::class, 'update']);
Route::delete('/users/{id}', [CobaController::class, 'destroy']);
Route::get('/coba2', [CobaController::class, 'tampil2']);
