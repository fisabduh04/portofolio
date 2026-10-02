<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Jadwal;
use App\Models\Sekolah;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Tetap jalankan registrasi policy dan paginator
        Gate::policy(User::class, UserPolicy::class);
        Paginator::useTailwind();

        // --- Definisi Akses Menu (Gates) ---
        Gate::define('view-kepegawaian', function (User $user): bool {
            return $user->is_active && $user->role->canViewKepegawaian();
        });

        Gate::define('access-school-modules', function (User $user): bool {
            return $user->is_active && ! $user->role->isPayrollOnly();
        });

        Gate::define('manage-payroll', function (User $user): bool {
            return $user->canManagePayroll();
        });

        Gate::define('view-rekapitulasi', function (User $user): bool {
            return $user->is_active && $user->role->canViewRekapitulasi();
        });

        Gate::define('manage-jadwal', function (User $user): bool {
            return $user->is_active && $user->isManagement();
        });

        Gate::define('manage-data-master', function (User $user): bool {
            return $user->is_active && $user->isManagement();
        });

        Gate::define('manage-sekolah', function (User $user): bool {
            return $user->is_active && $user->isManagement();
        });

        Gate::define('is-guru', function (User $user): bool {
            return $user->is_active && $user->role === UserRole::Guru;
        });

        Gate::define('input-presensi', function (User $user, Jadwal $jadwal, string $kategori): bool {
            if (! $user->is_active || ! in_array($kategori, ['mapel', 'piket_sub'], true)) {
                return false;
            }

            if ($user->isManagement()) {
                return true;
            }

            if ($user->role !== UserRole::Guru || ! $user->pegawai_id) {
                return false;
            }

            if ($kategori === 'piket_sub') {
                return $user->isPiketToday();
            }

            return (string) $user->pegawai_id === (string) $jadwal->pegawai_id
                || $user->isPiketToday();
        });

        // ------------------------------------

        // 2. Optimasi: Hanya jalankan query jika aplikasi TIDAK sedang berjalan di terminal (CLI/Migration)
        // Ini mencegah error saat Anda menjalankan 'php artisan migrate' di server baru
        if (! $this->app->runningInConsole()) {


            // 3. Gunakan Cache agar tidak membebani database di SETIAP refresh halaman
            $sekolah = Cache::remember('global_sekolah_data', now()->addHours(4), function () {
                try {
                    return Sekolah::first();
                } catch (\Exception $e) {
                    return null;
                }
            });

            if (! $sekolah) {
                Cache::forget('global_sekolah_data');
                $sekolah = new Sekolah;
            }

            // 4. Logika Logo: Jika ada di DB pakai DB, jika tidak pakai default
            $logo = $sekolah->logo_url;

            View::share('sekolah', $sekolah);
        }
    }
}
