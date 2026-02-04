<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);

        Paginator::useTailwind();
        
        // Definisikan path logo global dan data Sekolah
        $logo = asset('img/logo.png');
        try {
            $sekolah = \App\Models\Sekolah::first();
        } catch (\Exception $e) {
            $sekolah = null;
        }

        // Bagikan variabel ke semua view
        View::share('logo', $logo);
        View::share('sekolah', $sekolah);
    }
}
