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
use App\Http\Controllers\JadwalPiketController;
use App\Http\Controllers\KelasSiswaController;
use App\Http\Controllers\Operator\UserProvisioningController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\AttendanceRuleController;
use App\Http\Controllers\AbsensiExportController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PegawaiAttendanceController;




// Override Fortify Password Reset to Block Inactive Users
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('password.email');

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
    Route::get('exporttmapel', [MapelController::class, 'export'])->name('mapel.export');

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
    // Daftar Hadir Siswa (Moved to specific middleware group to prevent conflict)
    // Route::resource('absensi', AbsensiController::class);
    // 1. AKSES UNTUK SEMUA ROLE (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // 2. AKSES REKAPITULASI (Kepala Sekolah, Admin, Operator) - Must be before resource routes
    Route::middleware(['role:kepala,admin,operator'])->group(function () {
        Route::get('/absensi/rekap', [AbsensiReportController::class, 'rekap'])->name('absensi.rekap');
        Route::get('/absensi/rekap-harian', [AbsensiReportController::class, 'rekapHarian'])->name('absensi.rekap-harian');
        Route::get('/absensi/rekap-harian/export', [AbsensiExportController::class, 'exportRekapHarian'])->name('absensi.rekap-harian.export');
        Route::get('/absensi/rekap-bulanan', [AbsensiReportController::class, 'rekapBulanan'])->name('absensi.rekap-bulanan');
        Route::get('/absensi/rekap-bulanan/export', [AbsensiExportController::class, 'exportRekapBulanan'])->name('absensi.rekap-bulanan.export');
        Route::get('/absensi/rekap-tahunan', [AbsensiReportController::class, 'rekapTahunan'])->name('absensi.rekap-tahunan');
        Route::get('/absensi/rekap-tahunan/export', [AbsensiExportController::class, 'exportRekapTahunan'])->name('absensi.rekap-tahunan.export');
        Route::get('/absensi/export-harian', [AbsensiExportController::class, 'exportHarian'])->name('absensi.export-harian');
        Route::get('/absensi/rekap-periode', [AbsensiReportController::class, 'rekapPeriode'])->name('absensi.rekap-periode');
        Route::get('/absensi/export-periode', [AbsensiExportController::class, 'exportRekapPeriode'])->name('absensi.export-periode');
        Route::get('/absensi/export-bulanan', [AbsensiExportController::class, 'exportBulanan'])->name('absensi.export-bulanan');
        Route::get('/absensi/export-tahunan', [AbsensiExportController::class, 'exportRekapTahunan'])->name('absensi.export-tahunan'); // Fixed to point to existing export method
        Route::get('/jadwal/rekap', [JadwalController::class, 'rekap'])->name('jadwal.rekap');
        
        // Piket Routes
        Route::get('/absensi/piket', [AbsensiController::class, 'piket'])->name('absensi.piket');
        Route::post('/absensi/piket/check-in', [AbsensiController::class, 'piketCheckIn'])->name('absensi.piket.check-in');
        Route::post('/absensi/piket/check-out', [AbsensiController::class, 'piketCheckOut'])->name('absensi.piket.check-out');

        // PEGAWAI ATTENDANCE SYSTEM
        Route::resource('attendance/rules', AttendanceRuleController::class, ['as' => 'attendance']);
        Route::get('attendance/dashboard', [\App\Http\Controllers\PegawaiAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/create', [\App\Http\Controllers\PegawaiAttendanceController::class, 'create'])->name('attendance.create');
        Route::post('attendance/store', [\App\Http\Controllers\PegawaiAttendanceController::class, 'store'])->name('attendance.store');
        Route::post('attendance/process', [\App\Http\Controllers\PegawaiAttendanceController::class, 'process'])->name('attendance.process');
        Route::get('attendance/report', [\App\Http\Controllers\PegawaiAttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('attendance.payroll.index');
        Route::get('attendance/payroll/{id}/slip', [\App\Http\Controllers\PayrollController::class, 'slip'])->name('attendance.payroll.slip');




    });

    // 3. AKSES KHUSUS GURU & ADMIN (Presensi) - MOVED UP TO FIX ROUTE PRECEDENCE
    Route::middleware(['role:guru,admin,operator,kepala'])->group(function () {
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/presensi-harian', [JadwalController::class, 'presensiHarianGuru'])->name('jadwal.presensiHarian');
        // Absensi Harian Routes (Guru Piket)
        Route::get('/absensi/harian', [AbsensiController::class, 'indexHarian'])->name('absensi.harian.index');
        Route::get('/absensi/harian/create', [AbsensiController::class, 'createHarian'])->name('absensi.harian.create');
        Route::post('/absensi/harian', [AbsensiController::class, 'storeHarian'])->name('absensi.harian.store');

        Route::resource('absensi', AbsensiController::class);
    });

    // 4. AKSES KHUSUS ADMIN & OPERATOR (Manajemen Data Master)
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
        
        Route::post('/jadwal/import', [JadwalController::class, 'import'])->name('jadwal.import');
        
        // Resource constrained to IDs only (numbers) to prevent conflict with /jadwal/presensi-harian
        Route::resource('jadwal', JadwalController::class)
            ->except(['index'])
            ->whereNumber('jadwal'); 
            
        Route::resource('jadwal-piket', JadwalPiketController::class);
        
        // Hari Libur Routes
        Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
        Route::post('/hari-libur/weekly', [HariLiburController::class, 'updateWeekly'])->name('hari-libur.updateWeekly');
        Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('hari-libur.store');
        Route::put('/hari-libur/{id}', [HariLiburController::class, 'update'])->name('hari-libur.update');
        Route::delete('/hari-libur/{id}', [HariLiburController::class, 'destroy'])->name('hari-libur.destroy');


        // Specific routes for resources that are not full resource controllers
        // Kelas
        Route::get('exportkelas', [KelasController::class, 'export'])->name('exportkelas');
        Route::post('importkelas', [KelasController::class, 'import'])->name('importkelas');
        // Mapel
        Route::post('importmapel', [MapelController::class, 'import'])->name('importmapel');
        Route::get('exporttmapel', [MapelController::class, 'export'])->name('mapel.export');
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
        
        // Sekolah (Data Induk)
        Route::resource('sekolah', \App\Http\Controllers\SekolahController::class)->only(['index', 'store']);
        
        // MANAJEMEN USER (Operator/Admin)
        Route::prefix('users')->name('operator.users.')->group(function () {
             Route::get('/', [UserProvisioningController::class, 'index'])->name('index'); // operator.users.index
             Route::post('/', [UserProvisioningController::class, 'store'])->name('store');
             Route::patch('/{user}/active', [UserProvisioningController::class, 'toggleActive'])->name('toggle-active');
             Route::patch('/{user}/role', [UserProvisioningController::class, 'updateRole'])->name('update-role');
             Route::post('/resend-reset', [UserProvisioningController::class, 'resendReset'])->name('resend-reset');
        });

        // Jadwal specific routes
        // Jadwal routes moved up
    });

});




