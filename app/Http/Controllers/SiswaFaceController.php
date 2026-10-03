<?php

namespace App\Http\Controllers;

use App\Models\FaceSample;
use App\Models\Siswa;
use App\Services\FaceEnrollmentSimilarity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SiswaFaceController extends Controller
{
    public function store(Request $request, Siswa $siswa, FaceEnrollmentSimilarity $similarity): JsonResponse
    {
        $data = $request->validate([
            'samples' => ['required', 'array', 'list', 'size:3'],
            'samples.*' => ['required', 'array', 'list', 'size:128'],
            'samples.*.*' => ['required', 'numeric', 'between:-2,2'],
            'similarity_confirmation' => ['nullable', 'string', 'size:40'],
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

        $positions = ['depan', 'kiri', 'kanan'];
        $maximumDistance = (float) config('face-enrollment.maximum_sample_distance');
        $errors = [];
        for ($first = 0; $first < 2; $first++) {
            for ($second = $first + 1; $second < 3; $second++) {
                $sum = 0.0;
                foreach ($data['samples'][$first] as $index => $value) {
                    $sum += ((float) $value - (float) $data['samples'][$second][$index]) ** 2;
                }
                $distance = sqrt($sum);
                if ($distance > $maximumDistance) {
                    $message = sprintf('Sampel %s dan %s belum konsisten (jarak %.4f, maksimum %.4f). Periksa kedua foto dan ambil ulang posisi yang berbeda; pastikan siswa yang sama.',
                        $positions[$first], $positions[$second], $distance, $maximumDistance);
                    $errors['samples.'.$first][] = $message;
                    $errors['samples.'.$second][] = $message;
                }
            }
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $data['samples'] = array_map(fn (array $sample): array => array_map('floatval', $sample), $data['samples']);
        $threshold = (float) config('face-enrollment.similarity_threshold');
        $candidates = $similarity->candidates($siswa, $data['samples'], $threshold);
        $reviewKey = 'face_enrollment_review.'.$siswa->id;
        if ($candidates !== []) {
            $fingerprint = hash('sha256', json_encode([$request->user()->id, $siswa->id, $data['samples'], $candidates, $threshold], JSON_THROW_ON_ERROR));
            $review = $request->session()->get($reviewKey);
            $confirmed = is_array($review)
                && $review['expires_at'] > now()->timestamp
                && hash_equals($review['fingerprint'], $fingerprint)
                && hash_equals($review['token'], $data['similarity_confirmation'] ?? '');
            if (! $confirmed) {
                $token = Str::random(40);
                $request->session()->put($reviewKey, [
                    'fingerprint' => $fingerprint, 'token' => $token, 'expires_at' => now()->addMinutes(10)->timestamp,
                ]);

                return response()->json([
                    'code' => 'face_similarity_review',
                    'message' => 'Sampel mirip dengan wajah siswa lain. Periksa identitas dan foto sebelum melanjutkan. Kemiripan bukan bukti bahwa siswa adalah orang yang sama.',
                    'candidates' => $candidates, 'threshold' => $threshold, 'confirmation_token' => $token,
                ], 409);
            }
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
        $request->session()->forget($reviewKey);
        if ($candidates !== []) {
            Log::channel('face-attendance')->info('Perekaman wajah mirip dikonfirmasi operator', [
                'actor_id' => $request->user()->id, 'siswa_id' => $siswa->id,
                'candidates' => $candidates, 'threshold' => $threshold,
            ]);
        }

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
