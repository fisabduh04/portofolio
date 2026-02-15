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

        // 2. Optimasi: Hanya jalankan query jika aplikasi TIDAK sedang berjalan di terminal (CLI/Migration)
        // Ini mencegah error saat Anda menjalankan 'php artisan migrate' di server baru
        if (!$this->app->runningInConsole()) {
            
            // 3. Gunakan Cache agar tidak membebani database di SETIAP refresh halaman
            $sekolah = Cache::rememberForever('global_sekolah_data', function () {
                try {
                    return Sekolah::first();
                } catch (\Exception $e) {
                    return null;
                }
            });

            // 4. Logika Logo: Jika ada di DB pakai DB, jika tidak pakai default
            $logo = ($sekolah && $sekolah->logo) 
                    ? asset('storage/' . $sekolah->logo) 
                    : asset('img/logo.png');

            View::share('sekolah', $sekolah);
            View::share('logo', $logo);
        }
    }
        
}
