<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\FaceSample;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Logbook;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FaceAttendanceService
{
    /**
     * @param  list<float>  $descriptor
     * @return array{student: string, kelas: string, status: string, already_recorded: bool, logbook_id: int, time: string, message: string, matching: array{status: string, distance: ?float, second_distance: ?float, gap: ?float, threshold: float, minimum_gap: float, candidates: list<array{student: string, distance: float}>}}
     */
    public function record(User $actor, array $descriptor, ?Jadwal $schedule, string $type, string $materi): array
    {
        $periods = Tahun::aktif()->pluck('id');
        $placements = KelasSiswa::query()->whereIn('tahun_id', $periods)
            ->where(fn (Builder $query) => $query->whereNull('ket')->orWhere('ket', 'aktif'));
        if ($schedule) {
            $placements->where('kelas_id', $schedule->kelas_id)->where('tahun_id', $schedule->tahun_id);
        }
        $references = [];
        foreach (FaceSample::where('is_active', true)->where('model', config('face-attendance.model'))
            ->whereIn('siswa_id', (clone $placements)->select('siswa_id'))
            ->whereHas('siswa', fn (Builder $query) => $query->where('aktif', 'Aktif'))->cursor() as $sample) {
            $values = $sample->descriptor;
            if (is_array($values) && count($values) === 128) {
                $references[] = ['alias' => (string) $sample->siswa_id, 'descriptor' => $values];
            }
        }
        $match = app(FacePrototypeMatcher::class)->match($descriptor, $references,
            (float) config('face-attendance.threshold'), (float) config('face-attendance.minimum_gap'));
        $candidateNames = Siswa::whereKey(array_column($match['candidates'], 'alias'))->pluck('nama', 'id');
        $candidates = array_map(fn (array $candidate): array => [
            'student' => $candidateNames->get($candidate['alias'], 'Siswa tidak tersedia'),
            'distance' => $candidate['distance'],
        ], $match['candidates']);
        $matching = [
            'candidates' => $candidates,
            'status' => $match['status'],
            'distance' => $match['candidates'][0]['distance'] ?? null,
            'second_distance' => $match['candidates'][1]['distance'] ?? null,
            'gap' => $match['gap'],
            'threshold' => (float) config('face-attendance.threshold'),
            'minimum_gap' => (float) config('face-attendance.minimum_gap'),
        ];
        if ($match['status'] === 'ambiguous') {
            Log::channel('face-attendance')->info('Presensi wajah ambigu', [
                'occurred_at' => now()->toIso8601String(),
                'actor_id' => $actor->id,
                'pegawai_id' => $actor->pegawai_id,
                'mode' => $schedule ? 'mapel' : 'piket',
                'session' => $schedule ? 'mapel' : 'piket_'.$type,
                'jadwal_id' => $schedule?->id,
                'model' => config('face-attendance.model'),
                'threshold' => $matching['threshold'],
                'minimum_gap' => $matching['minimum_gap'],
                'gap' => $match['gap'],
                'candidates' => array_map(fn (array $candidate): array => [
                    'siswa_id' => (int) $candidate['alias'],
                    'student' => $candidateNames->get($candidate['alias'], 'Siswa tidak tersedia'),
                    'distance' => $candidate['distance'],
                ], $match['candidates']),
                'attendance_saved' => false,
            ]);
        }
        if ($match['status'] !== 'candidate') {
            $exception = ValidationException::withMessages(['descriptor' => $match['status'] === 'ambiguous'
                ? 'Wajah mirip dengan lebih dari satu siswa. Kandidat: '.implode(' / ', array_column($candidates, 'student')).'. Identitas belum dipastikan. Gunakan presensi manual.'
                : 'Wajah tidak dikenali dalam daftar siswa yang boleh diabsen.']);
            $exception->response = response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
                'matching' => $matching,
            ], 422);

            throw $exception;
        }
        $studentId = (int) $match['candidates'][0]['alias'];
        $enrollments = (clone $placements)->where('siswa_id', $studentId)->get()->unique(fn (KelasSiswa $placement): string => $placement->kelas_id.':'.$placement->tahun_id);
        if ($enrollments->count() !== 1) {
            throw ValidationException::withMessages(['descriptor' => 'Penempatan kelas siswa belum jelas. Periksa data kelas siswa.']);
        }
        $placement = $enrollments->first();

        return DB::transaction(function () use ($actor, $studentId, $placement, $schedule, $type, $materi, $matching): array {
            $kelas = Kelas::whereKey($placement->kelas_id)->lockForUpdate()->firstOrFail();
            $student = Siswa::findOrFail($studentId);
            $date = now()->toDateString();
            $jadwal = $schedule ?? Jadwal::where('kelas_id', $kelas->id)
                ->whereHas('tahun', fn (Builder $query) => $query->aktif())
                ->where('hari', now()->locale('id')->isoFormat('dddd'))->orderBy('mulai')->orderBy('id')->first();
            if (! $jadwal || (int) $jadwal->tahun_id !== (int) $placement->tahun_id) {
                throw ValidationException::withMessages(['descriptor' => 'Kelas siswa tidak memiliki jadwal hari ini pada periode penempatannya.']);
            }
            $category = $schedule ? 'mapel' : 'piket_'.$type;
            $identity = ['tanggal' => $date, 'kategori' => $category];
            $identity[$schedule ? 'jadwal_id' : 'kelas_id'] = $schedule ? $jadwal->id : $kelas->id;
            $logbook = Logbook::firstOrCreate($identity, [
                'kelas_id' => $kelas->id, 'jadwal_id' => $jadwal->id, 'pegawai_id' => $actor->pegawai_id,
                'materi' => $schedule ? $materi : ($type === 'masuk' ? 'Absensi Masuk' : 'Absensi Pulang'),
            ]);
            $existing = Absensi::where('logbook_id', $logbook->id)->where('siswa_id', $student->id)->first();
            $scanTime = now()->format('H:i:s');
            $attendance = $existing ?? Absensi::create([
                'logbook_id' => $logbook->id, 'siswa_id' => $student->id,
                'status' => 'Hadir',
                'keterangan' => 'Presensi wajah '.$scanTime,
            ]);
            $hasFaceTime = preg_match('/\APresensi wajah ([0-2][0-9]:[0-5][0-9]:[0-5][0-9])\z/', $attendance->keterangan ?? '', $storedTime) === 1;
            $recordedTime = $hasFaceTime ? $storedTime[1] : ($attendance->created_at?->format('H:i:s') ?? '—');
            $timeUpdated = $existing && ! $schedule && $type === 'pulang'
                && $attendance->status === 'Hadir' && $hasFaceTime && $scanTime > $recordedTime;
            if ($timeUpdated) {
                $attendance->update(['keterangan' => 'Presensi wajah '.$scanTime]);
                $recordedTime = $scanTime;
            }

            return [
                'matching' => $matching,
                'student' => $student->nama, 'kelas' => $kelas->kelas, 'status' => $attendance->status,
                'already_recorded' => $existing !== null, 'logbook_id' => $logbook->id,
                'time' => $recordedTime,
                'message' => $timeUpdated ? 'Waktu pulang diperbarui ke pemindaian terakhir.'
                    : ($existing ? 'Sudah tercatat. Catatan sebelumnya tetap dipertahankan.' : 'Presensi berhasil dicatat di logbook.'),
            ];
        });
    }
}
