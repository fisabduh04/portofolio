<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, biarkan middleware lain yang menangani
        if (! auth()->check()) {
            return $next($request);
        }

        // Jika akun NONAKTIF
        if ((int) auth()->user()->is_active === 0) {
            auth()->logout();

            // 🔐 Penting: matikan session lama
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Jika request AJAX / JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda dinonaktifkan oleh operator.',
                ], 403);
            }

            // Request normal (web)
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda dinonaktifkan oleh operator.',
                ]);
        }

        // Jika akun AKTIF → lanjutkan request
        return $next($request);
    }
}
