<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiExportController;
use App\Http\Controllers\AbsensiPegawaiReportController;
use App\Http\Controllers\AbsensiReportController;
use App\Http\Controllers\AttendanceRuleController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaceAttendanceController;
use App\Http\Controllers\FingerprintMachineController;
use App\Http\Controllers\GuruAbsensiController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalPiketController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasSiswaController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\Operator\UserProvisioningController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PegawaiAttendanceController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PegawaiIzinController;
use App\Http\Controllers\PegawaiWajibHadirController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaFaceController;
use App\Http\Controllers\SpecialEventController;
use App\Http\Controllers\TahunController;
use Illuminate\Support\Facades\Route;

// Override Fortify Password Reset to Block Inactive Users
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('password.email');

// Rute Publik (Redirect ke Login jika belum auth)
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Grup Keamanan
Route::middleware(['auth', 'active', 'treasurer-access'])->group(function () {

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

    // Existing Routes
    Route::resource('pegawai', PegawaiController::class);
    Route::post('importpegawai', [PegawaiController::class, 'import'])->name('importpegawai');
    Route::get('exportpegawai', [PegawaiController::class, 'export'])->name('exportpegawai');

    // Route siswa (Akses umum untuk index/show bisa di sini jika diperlukan, tapi sebaiknya dikonsolidasikan)
    // Route::resource('siswa', SiswaController::class);

    // Route Kelas Siswa
    Route::delete('/kelassiswa/bulk-delete', [KelasSiswaController::class, 'destroy'])->name('kelassiswa.bulkDelete');
    Route::resource('kelassiswa', KelasSiswaController::class);
    Route::get('/kelas-siswa-export', [KelasSiswaController::class, 'export'])->name('kelas-siswa-export');
    Route::post('kelas-siswa-import', [KelasSiswaController::class, 'import'])->name('kelas-siswa-import');

    // Daftar Hadir Siswa
    // Daftar Hadir Siswa (Moved to specific middleware group to prevent conflict)
    // Route::resource('absensi', AbsensiController::class);
    // 1. AKSES UNTUK SEMUA ROLE (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Rekapitulasi presensi siswa, termasuk akses guru.
    Route::middleware(['role:kepala,admin,operator,staff,guru'])->group(function () {
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
        Route::get('/absensi/export-tahunan', [AbsensiExportController::class, 'exportRekapTahunan'])->name('absensi.export-tahunan');
    });

    // Rekap jam mengajar.
    Route::middleware(['role:kepala,admin,operator,staff'])->group(function () {
        Route::get('/jadwal/rekap', [JadwalController::class, 'rekap'])->name('jadwal.rekap');
    });

    Route::middleware(['can:manage-payroll'])->prefix('attendance')->name('attendance.')->group(function () {
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{id}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');
    });

    // Pengaturan kepegawaian khusus kepala sekolah dan administrator.
    Route::middleware(['can:view-kepegawaian'])->prefix('attendance')->name('attendance.')->group(function () {
        Route::resource('overrides', \App\Http\Controllers\ScheduleOverrideController::class);
        Route::get('mandatory', [\App\Http\Controllers\MandatoryScheduleController::class, 'index'])->name('mandatory.index');
        Route::post('mandatory', [\App\Http\Controllers\MandatoryScheduleController::class, 'store'])->name('mandatory.store');

        Route::resource('rules', AttendanceRuleController::class)->parameters(['rules' => 'attendanceRule']);
        Route::post('fingerprint/{fingerprint}/pull', [FingerprintMachineController::class, 'pull'])->name('fingerprint.pull');
        Route::resource('fingerprint', FingerprintMachineController::class);
        Route::get('create', [GuruAbsensiController::class, 'create'])->name('create');
        Route::post('store', [GuruAbsensiController::class, 'store'])->name('store');
        Route::get('rekap-guru', [GuruAbsensiController::class, 'report'])->name('rekap-guru');
        Route::get('rekap-guru/export', [GuruAbsensiController::class, 'export'])->name('rekap-guru.export');
        Route::post('process', [PegawaiAttendanceController::class, 'process'])->name('process');

        Route::get('dashboard', [PegawaiAttendanceController::class, 'index'])->name('index');
        Route::get('rekap-pegawai', [PegawaiAttendanceController::class, 'rekapPegawai'])->name('rekap-pegawai');
        Route::get('report', [PegawaiAttendanceController::class, 'report'])->name('report');
        Route::get('report/employee', [AbsensiPegawaiReportController::class, 'index'])->name('report.employee');
        Route::get('report/employee/export', [AbsensiPegawaiReportController::class, 'export'])->name('report.employee.export');
        Route::get('setting', [PegawaiAttendanceController::class, 'setting'])->name('setting');
        Route::post('setting', [PegawaiAttendanceController::class, 'updateSetting'])->name('updateSetting');

        // Wajib Hadir Routes
        Route::get('wajib-hadir', [PegawaiWajibHadirController::class, 'index'])->name('wajib-hadir.index');
        Route::post('wajib-hadir', [PegawaiWajibHadirController::class, 'store'])->name('wajib-hadir.store');

        // Special Events
        Route::resource('events', SpecialEventController::class);

        // Perizinan (Izin/Sakit/Cuti)
        Route::resource('izin', PegawaiIzinController::class);
    });

    // 3. AKSES KHUSUS GURU & ADMIN (Presensi) - MOVED UP TO FIX ROUTE PRECEDENCE
    Route::middleware(['role:guru,admin,operator,kepala'])->group(function () {
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/presensi-harian', [JadwalController::class, 'presensiHarianGuru'])->name('jadwal.presensiHarian');

        // Piket Routes (Akses khusus hari piket)
        Route::get('/absensi/piket', [AbsensiController::class, 'piket'])->name('absensi.piket');
        Route::post('/absensi/piket/check-in', [AbsensiController::class, 'piketCheckIn'])->name('absensi.piket.check-in');
        Route::post('/absensi/piket/check-out', [AbsensiController::class, 'piketCheckOut'])->name('absensi.piket.check-out');

        // Absensi Harian Routes
        Route::get('/absensi/harian', [AbsensiController::class, 'indexHarian'])->name('absensi.harian.index');
        Route::get('/absensi/harian/create', [AbsensiController::class, 'createHarian'])->name('absensi.harian.create');
        Route::post('/absensi/harian', [AbsensiController::class, 'storeHarian'])->name('absensi.harian.store');

        Route::resource('absensi', AbsensiController::class);
        Route::get('/presensi-wajah', [FaceAttendanceController::class, 'index'])->name('face-attendance.index');
        Route::post('/presensi-wajah', [FaceAttendanceController::class, 'store'])->middleware('throttle:120,1,face-attendance:')->name('face-attendance.store');
    });

    // 4. AKSES KHUSUS ADMIN & OPERATOR (Manajemen Data Master)
    Route::middleware(['role:admin,operator', 'can:manage-data-master'])->group(function () {
        Route::get('/walikelas/export', [\App\Http\Controllers\WaliKelasController::class, 'export'])->name('walikelas.export');
        Route::post('/walikelas/import', [\App\Http\Controllers\WaliKelasController::class, 'import'])->name('walikelas.import');
        Route::delete('/walikelas/bulk-delete', [\App\Http\Controllers\WaliKelasController::class, 'destroy'])->name('walikelas.bulkDelete');
        Route::resource('walikelas', \App\Http\Controllers\WaliKelasController::class)
            ->parameters(['walikelas' => 'walikelas'])
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('tahun', TahunController::class);
        Route::resource('jurusan', JurusanController::class);
        Route::resource('pegawai', PegawaiController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('mapel', MapelController::class);
        // Siswa Specific Routes (Must be before resource)
        Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDelete'])->name('siswa.bulkDelete');
        Route::get('exportsiswa', [SiswaController::class, 'export'])->name('exportsiswa');
        Route::post('importsiswa', [SiswaController::class, 'import'])->name('importsiswa');
        Route::resource('siswa', SiswaController::class);
        Route::post('siswa/{siswa}/wajah', [SiswaFaceController::class, 'store'])->middleware('throttle:10,1')->name('siswa.face.store');
        Route::delete('siswa/{siswa}/wajah', [SiswaFaceController::class, 'destroy'])->middleware('throttle:10,1')->name('siswa.face.destroy');
        Route::resource('kelassiswa', KelasSiswaController::class);
        // Jadwal Specific Routes (Must be before resource to avoid ID conflict)
        Route::get('/jadwal/data', [JadwalController::class, 'getJadwalJson']);
        Route::delete('/jadwal/bulk-delete', [JadwalController::class, 'bulkDelete'])->name('jadwal.bulkDelete');
        Route::post('/jadwal/update-all', [JadwalController::class, 'updateAll'])->name('jadwal.updateAll');
        Route::get('/jadwal/export', [JadwalController::class, 'export'])->name('jadwal.export');
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
        // Kelas Siswa
        Route::get('/kelas-siswa-export', [KelasSiswaController::class, 'export'])->name('kelas-siswa-export');
        Route::post('kelas-siswa-import', [KelasSiswaController::class, 'import'])->name('kelas-siswa-import');

        // Sekolah (Data Induk)
        Route::resource('sekolah', SekolahController::class)->only(['index', 'store']);

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
