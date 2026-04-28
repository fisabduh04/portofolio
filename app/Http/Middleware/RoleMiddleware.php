<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // 1. Pastikan sudah login
        if (! $user) {
            abort(403, 'Anda belum login.');
        }

        // 2. Pastikan akun aktif (harus bernilai 1)
        if ((int) ($user->is_active ?? 0) !== 1) {
            abort(403, 'Akun Anda belum aktif.');
        }

        // 3. Normalisasi role
        $userRole = $user->role->value;
        $allowedRoles = array_map(
            fn ($role) => strtolower((string) $role),
            $roles
        );

        // 4. Cek apakah role user termasuk yang diizinkan
        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
