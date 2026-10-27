<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Auth::provider('pegawai', fn (Application $app, array $config): PegawaiUserProvider => new PegawaiUserProvider($app['hash'], $config['model']));

        // LOGIN VIEW
        Fortify::loginView(fn () => view('login.login'));

        // FORGOT PASSWORD VIEW
        Fortify::requestPasswordResetLinkView(
            fn () => view('login.forgotPassword')
        );

        Fortify::authenticateUsing(function (Request $request) {
            $user = Auth::createUserProvider(config('auth.guards.'.config('fortify.guard').'.provider'))
                ->retrieveByCredentials($request->only('email', 'password'));

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if (! $user->is_active) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda belum aktif. Silakan hubungi operator.',
                ]);
            }

            return $user;
        });

        // RESET PASSWORD VIEW
        Fortify::resetPasswordView(
            fn (Request $request) => view('login.resetPassword', ['request' => $request])
        );

        // 🔑 INI YANG PENTING UNTUK RESET PASSWORD
        Fortify::resetUserPasswordsUsing(
            \App\Actions\Fortify\ResetUserPassword::class
        );

        Fortify::redirectUserForTwoFactorAuthenticationUsing(
            RedirectIfTwoFactorAuthenticatable::class
        );

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())).'|'.$request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->session()->get('login.id')
            );
        });
    }
}
