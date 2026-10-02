<?php

namespace App\Http\Controllers;

use App\Models\FaceSample;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiswaFaceController extends Controller
{
    public function store(Request $request, Siswa $siswa): JsonResponse
    {
        $data = $request->validate([
            'samples' => ['required', 'array', 'list', 'size:3'],
            'samples.*' => ['required', 'array', 'list', 'size:128'],
            'samples.*.*' => ['required', 'numeric', 'between:-2,2'],
        ], [
            'samples.required' => 'Ambil tiga sampel wajah terlebih dahulu.',
            'samples.size' => 'Diperlukan tepat tiga sampel wajah.',
            'samples.*.size' => 'Sampel wajah tidak lengkap. Silakan ambil ulang.',
            'samples.*.*.numeric' => 'Sampel wajah tidak valid. Silakan ambil ulang.',
            'samples.*.*.between' => 'Sampel wajah di luar batas. Silakan ambil ulang.',
        ]);

        foreach ($data['samples'] as $descriptor) {
            abort_if(array_sum(array_map(fn (int|float|string $value): float => (float) $value ** 2, $descriptor)) < 0.01, 422, 'Sampel wajah kosong. Silakan ambil ulang.');
        }

        DB::transaction(function () use ($data, $siswa, $request): void {
            Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();
            FaceSample::where('siswa_id', $siswa->id)->delete();

            foreach (['front', 'left', 'right'] as $index => $label) {
                FaceSample::create([
                    'siswa_id' => $siswa->id,
                    'label' => $label,
                    'model' => 'face-api-1.7.15-tiny-landmark68-recognition128',
                    'descriptor' => array_map('floatval', $data['samples'][$index]),
                    'is_active' => true,
                    'created_by' => $request->user()->id,
                ]);
            }
        });

        return response()->json(['message' => 'Tiga sampel wajah berhasil disimpan.', 'count' => 3]);
    }

    public function destroy(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate(['confirm_delete' => ['required', 'accepted']]);

        $deleted = DB::transaction(function () use ($siswa): int {
            Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();

            return FaceSample::where('siswa_id', $siswa->id)->delete();
        });
        Log::info('Data wajah siswa dihapus', ['siswa_id' => $siswa->id, 'actor_id' => $request->user()->id, 'deleted_samples' => $deleted]);

        return redirect()->route('siswa.show', $siswa)->with('message', 'Data wajah berhasil dihapus. Profil dan riwayat absensi tetap tersimpan.')->with('type', 'success');
    }
}
