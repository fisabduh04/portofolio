<?php

namespace App\Services;

use App\Models\Absensi;
use Illuminate\Support\Collection;

class DutyAttendanceRecap
{
    /**
     * Build read-only daily results; never create missing attendance records.
     *
     * @param  Collection<int, Absensi>  $records
     * @return Collection<int, Absensi|\stdClass>
     */
    public function summarize(Collection $records): Collection
    {
        [$duty, $other] = $records->partition(fn (Absensi $record): bool => in_array($record->logbook?->kategori, ['piket_masuk', 'piket_pulang'], true));

        $days = $duty->groupBy(fn (Absensi $record): string => $record->siswa_id.':'.$record->logbook->kelas_id.':'.$record->logbook->tanggal);
        $summaries = $days->map(function (Collection $day): \stdClass {
            $first = $day->first();
            $hasEntry = $day->contains(fn (Absensi $record): bool => $record->logbook->kategori === 'piket_masuk');
            $hasExit = $day->contains(fn (Absensi $record): bool => $record->logbook->kategori === 'piket_pulang');
            $status = collect(['Alpha', 'Sakit', 'Izin', 'Pulang'])->first(fn (string $value): bool => $day->contains('status', $value));
            $reason = '';
            if ($status === null && (! $hasEntry || ! $hasExit)) {
                $status = 'Alpha';
                $reason = $hasEntry ? 'Hanya absen masuk; belum absen pulang.' : 'Hanya absen pulang; belum absen masuk.';
            }
            $status ??= $day->contains('status', 'Telat') ? 'Telat' : 'Hadir';

            return (object) [
                'id' => $first->id,
                'siswa_id' => $first->siswa_id,
                'status' => $status,
                'keterangan' => $day->pluck('keterangan')->filter()->unique()->implode('; '),
                'rekap_note' => $reason,
                'duty_summary' => true,
                'logbook' => $first->logbook,
                'siswa' => $first->relationLoaded('siswa') ? $first->siswa : null,
            ];
        });

        return $other->toBase()->concat($summaries)->values();
    }
}
