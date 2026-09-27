<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacePrototypeMatchRequest;
use App\Services\FacePrototypeMatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FacePrototypeController extends Controller
{
    public function access(): JsonResponse
    {
        return response()->json(['authorized' => true, 'attendance_saved' => false]);
    }

    public function index(): View
    {
        $assetVersions = [];
        foreach (['js', 'css'] as $extension) {
            $path = storage_path('app/private/face-prototype-assets/app.'.$extension);
            $assetVersions[$extension] = is_file($path) ? hash_file('sha256', $path) : 'missing';
        }

        return view('absensi.face-prototype', compact('assetVersions'));
    }

    public function store(FacePrototypeMatchRequest $request, FacePrototypeMatcher $matcher): JsonResponse
    {
        $data = $request->validated();
        $started = hrtime(true);
        $result = $matcher->match($data['descriptor'], $data['references'],
            (float) config('face-prototype.threshold'), (float) config('face-prototype.minimum_gap'));
        $matchingMs = (hrtime(true) - $started) / 1e6;
        $serverMs = (hrtime(true) - (float) $request->server('FACE_PROTOTYPE_STARTED_NS', $started)) / 1e6;

        return response()->json([
            ...$result,
            'direction' => 'masuk', 'attendance_saved' => false,
            'server_time' => now()->toIso8601String(),
            'model' => config('face-prototype.model'),
            'reference_count' => count($data['references']),
            'opcode_cache_enabled' => function_exists('opcache_get_status')
                && (opcache_get_status(false)['opcache_enabled'] ?? false),
            'timings' => ['matching_ms' => $matchingMs, 'server_ms' => max($serverMs, $matchingMs), 'storage_ms' => null,
                'bootstrap_ms' => (float) $request->attributes->get('face_prototype_bootstrap_ms', 0),
                'guard_ms' => (float) $request->attributes->get('face_prototype_guard_ms', 0),
                'dispatch_validation_ms' => max(0, ($started - (float) $request->attributes->get('face_prototype_guard_finished_ns', $started)) / 1e6),
            ],
        ])->header('Server-Timing', sprintf('match;dur=%.3f, app;dur=%.3f', $matchingMs, max($serverMs, $matchingMs)));
    }
}
