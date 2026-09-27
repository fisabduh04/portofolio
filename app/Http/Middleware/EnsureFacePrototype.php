<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacePrototype
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guardStarted = hrtime(true);
        abort_unless(app()->environment('testing') && config('face-prototype.isolated') === true, 404);
        abort_unless(config('database.default') === 'sqlite'
            && config('database.connections.sqlite.database') === ':memory:'
            && ! config('database.connections.sqlite.url')
            && array_keys(config('database.connections')) === ['sqlite'], 503, 'Database uji tidak aman.');

        $connection = \Illuminate\Support\Facades\DB::connection();
        abort_unless($connection->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
            && $connection->getDatabaseName() === ':memory:', 503, 'Database uji tidak aman.');

        if ($request->isMethod('POST')) {
            abort_if(strlen($request->getContent()) > 524288, 413);
            $token = (string) config('face-prototype.token');
            abort_unless(strlen($token) >= 32, 503,
                'Kode operator belum diterima server. Hentikan server dengan Ctrl+C, lalu jalankan kembali npm run face:start.');
            abort_unless(hash_equals($token, (string) $request->bearerToken()), 401);
            abort_unless($request->isJson(), 415);
        }

        $guardFinished = hrtime(true);
        $request->attributes->set('face_prototype_guard_finished_ns', $guardFinished);
        $request->attributes->set('face_prototype_guard_ms', ($guardFinished - $guardStarted) / 1e6);
        $request->attributes->set('face_prototype_bootstrap_ms', max(0,
            ($guardStarted - (float) $request->server('FACE_PROTOTYPE_STARTED_NS', $guardStarted)) / 1e6));
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=()');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; media-src 'self' blob:; object-src 'none'; frame-ancestors 'none'; base-uri 'none'");

        return $response;
    }
}
