<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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
        Gate::define('view-kepegawaian', function (User $user) {
            return in_array($user->role->value, ['kepala', 'admin', 'operator', 'staff', 'bendahara']);
        });

        Gate::define('view-rekapitulasi', function (User $user) {
            return in_array($user->role->value, ['kepala', 'admin', 'operator', 'staff']);
        });

        Gate::define('manage-jadwal', function (User $user) {
            return in_array($user->role->value, ['admin', 'operator']);
        });

        Gate::define('manage-data-master', function (User $user) {
            return in_array($user->role->value, ['admin', 'operator']);
        });

        Gate::define('manage-sekolah', function (User $user) {
            return in_array($user->role->value, ['admin', 'kepala', 'operator']);
        });

        Gate::define('is-guru', function (User $user) {
            return $user->role->value === 'guru';
        });

        // ------------------------------------

        // 2. Optimasi: Hanya jalankan query jika aplikasi TIDAK sedang berjalan di terminal (CLI/Migration)
        // Ini mencegah error saat Anda menjalankan 'php artisan migrate' di server baru
        if (!$this->app->runningInConsole()) {
            
            // 3. Gunakan Cache agar tidak membebani database di SETIAP refresh halaman
            $sekolah = Cache::remember('global_sekolah_data', now()->addHours(4), function () {
                try {
                    return Sekolah::first();
                } catch (\Exception $e) {
                    return null;
                }
            });

            if (!$sekolah) {
                Cache::forget('global_sekolah_data');
                $sekolah = new Sekolah();
            }

            // 4. Logika Logo: Jika ada di DB pakai DB, jika tidak pakai default
            $logo = $sekolah->logo_url;

            View::share('sekolah', $sekolah);
        }
    }
        
}
