<?php

namespace App\Services;

use App\Models\FaceSample;
use App\Models\Siswa;

class FaceEnrollmentSimilarity
{
    /**
     * @param  list<list<float>>  $samples
     * @return list<array{siswa_id: int, student: string, distance: float}>
     */
    public function candidates(Siswa $student, array $samples, float $threshold): array
    {
        $distances = [];
        foreach (FaceSample::query()->where('is_active', true)
            ->where('model', config('face-attendance.model'))
            ->whereNotNull('siswa_id')->where('siswa_id', '!=', $student->id)
            ->whereHas('siswa')->cursor() as $reference) {
            $descriptor = $reference->descriptor;
            if (! is_array($descriptor) || count($descriptor) !== 128 || ! array_is_list($descriptor)) {
                continue;
            }
            foreach ($samples as $sample) {
                $sum = 0.0;
                foreach ($sample as $index => $value) {
                    if (! is_numeric($descriptor[$index]) || ! is_finite((float) $descriptor[$index])) {
                        continue 2;
                    }
                    $sum += ($value - (float) $descriptor[$index]) ** 2;
                }
                $distance = sqrt($sum);
                if ($distance <= $threshold) {
                    $distances[$reference->siswa_id] = min($distances[$reference->siswa_id] ?? INF, $distance);
                }
            }
        }
        asort($distances, SORT_NUMERIC);
        $names = Siswa::whereKey(array_keys($distances))->pluck('nama', 'id');
        $candidates = [];
        foreach ($distances as $id => $distance) {
            $candidates[] = ['siswa_id' => (int) $id, 'student' => $names->get($id, 'Siswa tidak tersedia'), 'distance' => $distance];
        }

        return $candidates;
    }
}
