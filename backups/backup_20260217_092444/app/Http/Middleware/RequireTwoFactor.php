<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle($request, Closure $next)
{
    if (!config('auth.two_factor_enabled')) {
    return $next($request);
    }

    $user = auth()->user();

    if (! $user) {
        return $next($request);
    }

    // Hanya role sensitif
    if (! in_array($user->role, ['operator','admin','kepala'])) {
        return $next($request);
    }

    // Belum setup 2FA
    if (! $user->two_factor_secret) {
        if (! $request->routeIs('2fa.*')) {
            return redirect()->route('2fa.setup');
        }
        return $next($request);
    }

    // Sudah setup tapi belum lolos challenge di session ini
    if (! session('2fa_passed')) {
        if (! $request->routeIs('2fa.challenge')) {
            return redirect()->route('2fa.challenge');
        }
        return $next($request);
    }

    return $next($request);
}

}
