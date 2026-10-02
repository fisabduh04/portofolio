<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RestrictTreasurerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::allows('access-school-modules')) {
            return $next($request);
        }

        if ($request->routeIs('dashboard.index')) {
            return redirect()->route('attendance.payroll.index');
        }

        abort_unless($request->routeIs('attendance.payroll.index', 'attendance.payroll.slip'), 403);

        return $next($request);
    }
}
