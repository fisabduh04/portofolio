<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Services\FaceAttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FaceAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $jadwal = $this->context($request);

        return view('absensi.face', [
            'jadwal' => $jadwal,
            'mode' => $jadwal ? 'mapel' : 'piket',
            'type' => $request->input('type', 'masuk'),
            'date' => now()->toDateString(),
        ]);
    }

    public function store(Request $request, FaceAttendanceService $attendance): JsonResponse
    {
        $startedAt = hrtime(true);
        $jadwal = $this->context($request);
        $data = $request->validate([
            'descriptor' => ['required', 'array', 'list', 'size:128'],
            'descriptor.*' => ['required', 'numeric', 'between:-2,2'],
            'materi' => [$jadwal ? 'required' : 'nullable', 'string', 'max:500'],
        ], ['materi.required' => 'Isi materi pelajaran sebelum memindai wajah.']);

        $result = $attendance->record(
            $request->user(), array_map('floatval', $data['descriptor']), $jadwal,
            $request->input('type', 'masuk'), $data['materi'] ?? '',
        );
        $result['server_ms'] = round((hrtime(true) - $startedAt) / 1_000_000, 2);

        return response()->json($result);
    }

    private function context(Request $request): ?Jadwal
    {
        $data = $request->validate([
            'mode' => ['required', 'in:mapel,piket'],
            'jadwal_id' => ['required_if:mode,mapel', 'nullable', 'integer'],
            'type' => ['sometimes', 'required', 'in:masuk,pulang'],
        ]);
        abort_unless($request->user()->pegawai_id, 403, 'Akun belum terhubung ke data pegawai.');

        if ($data['mode'] === 'piket') {
            abort_unless($request->user()->isPiketToday(), 403, 'Anda bukan guru piket hari ini.');

            return null;
        }

        $jadwal = Jadwal::with(['kelas', 'mapel', 'pegawai'])->findOrFail($data['jadwal_id']);
        Gate::authorize('input-presensi', [$jadwal, 'mapel']);
        abort_unless($jadwal->tahun()->aktif()->exists(), 422, 'Periode jadwal tidak aktif.');
        abort_unless($jadwal->hari === now()->locale('id')->isoFormat('dddd'), 422, 'Jadwal bukan untuk hari ini.');
        abort_unless(now()->format('H:i:s') >= $jadwal->mulai && now()->format('H:i:s') <= $jadwal->akhir, 422, 'Absensi wajah hanya tersedia selama jam pelajaran ini.');

        return $jadwal;
    }
}
