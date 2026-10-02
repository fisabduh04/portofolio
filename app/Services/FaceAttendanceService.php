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
use Illuminate\Validation\ValidationException;

class FaceAttendanceService
{
    /**
     * @param  list<float>  $descriptor
     * @return array{student: string, kelas: string, status: string, already_recorded: bool, logbook_id: int, time: string, message: string}
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
        if ($match['status'] !== 'candidate') {
            throw ValidationException::withMessages(['descriptor' => $match['status'] === 'ambiguous'
                ? 'Wajah mirip dengan lebih dari satu siswa. Gunakan presensi manual.'
                : 'Wajah tidak dikenali dalam daftar siswa yang boleh diabsen.']);
        }
        $studentId = (int) $match['candidates'][0]['alias'];
        $enrollments = (clone $placements)->where('siswa_id', $studentId)->get()->unique(fn (KelasSiswa $placement): string => $placement->kelas_id.':'.$placement->tahun_id);
        if ($enrollments->count() !== 1) {
            throw ValidationException::withMessages(['descriptor' => 'Penempatan kelas siswa belum jelas. Periksa data kelas siswa.']);
        }
        $placement = $enrollments->first();

        return DB::transaction(function () use ($actor, $studentId, $placement, $schedule, $type, $materi): array {
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
            $attendance = $existing ?? Absensi::create([
                'logbook_id' => $logbook->id, 'siswa_id' => $student->id,
                'status' => 'Hadir',
                'keterangan' => 'Presensi wajah '.now()->format('H:i:s'),
            ]);

            return [
                'student' => $student->nama, 'kelas' => $kelas->kelas, 'status' => $attendance->status,
                'already_recorded' => $existing !== null, 'logbook_id' => $logbook->id,
                'time' => now()->format('H:i:s'),
                'message' => $existing ? 'Sudah tercatat. Status sebelumnya tetap dipertahankan.' : 'Presensi berhasil dicatat di logbook.',
            ];
        });
    }
}
