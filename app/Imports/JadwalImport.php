<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    public function collection(Collection $collection): void
    {
        $periods = Tahun::all();
        $classes = Kelas::all(['id', 'kelas']);
        $subjects = Mapel::all(['id', 'mapel']);
        $employees = Pegawai::all(['id', 'name']);
        $rows = [];
        $identities = [];

        foreach ($collection as $index => $row) {
            $row = $row->map(fn (mixed $value): mixed => is_string($value) ? trim($value) : $value)->all();
            if (collect($row)->every(fn (mixed $value): bool => $value === null || $value === '')) {
                continue;
            }
            $line = $index + 2;
            $row['hari'] = ucfirst(strtolower((string) ($row['hari'] ?? '')));
            $row['mulai'] = $this->time($row['mulai'] ?? null);
            $row['akhir'] = $this->time($row['akhir'] ?? null);
            $validator = Validator::make($row, [
                'id' => ['nullable', 'integer', 'min:1', 'exists:jadwals,id'],
                'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
                'jam' => ['required', 'integer', 'min:1'],
                'mulai' => ['required', 'date_format:H:i:s'],
                'akhir' => ['required', 'date_format:H:i:s', 'after:mulai'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ], [
                'id.exists' => 'ID jadwal tidak ditemukan. Kosongkan ID untuk menambah jadwal baru.',
                'akhir.after' => 'Jam selesai harus lebih dari jam mulai.',
                '*.date_format' => 'Format :attribute harus berupa jam, misalnya 07:00.',
            ]);
            if ($validator->fails()) {
                $this->fail($line, $validator->errors()->first());
            }
            $id = empty($row['id']) ? null : (int) $row['id'];
            if ($id !== null && isset($identities[$id])) {
                $this->fail($line, 'ID jadwal berulang dalam berkas.');
            }
            if ($id !== null) {
                $identities[$id] = true;
            }
            $year = (string) ($row['tahun'] ?? '');
            $semester = (string) ($row['semester'] ?? '');
            $matchingPeriods = $periods->filter(fn (Tahun $period): bool => ($year === (string) $period->tahun && ($semester === '' || strcasecmp($semester, $period->semester) === 0))
                || ($semester === '' && $year === $period->tahun.' - '.$period->semester)
            );
            $yearId = $row['tahun_id'] ?? (ctype_digit($year) ? $year : null);
            $rows[] = ['id' => $id, 'data' => [
                'tahun_id' => $this->reference($yearId !== null && $yearId !== '' ? $periods : $matchingPeriods, $yearId, null, '', $line, 'Tahun / semester'),
                'kelas_id' => $this->reference($classes, $row['kelas_id'] ?? null, $row['kelas'] ?? null, 'kelas', $line, 'Kelas'),
                'mapel_id' => $this->reference($subjects, $row['mapel_id'] ?? null, $row['mapel'] ?? null, 'mapel', $line, 'Mapel'),
                'pegawai_id' => $this->reference($employees, $row['pegawai_id'] ?? null, $row['guru'] ?? null, 'name', $line, 'Guru'),
                'hari' => $row['hari'], 'jam' => $row['jam'],
                'mulai' => $row['mulai'], 'akhir' => $row['akhir'], 'ket' => $row['keterangan'] ?? null,
            ]];
        }

        if ($rows === []) {
            $this->fail(2, 'Berkas tidak berisi data jadwal.');
        }
        foreach ($rows as $row) {
            if ($row['id'] === null) {
                Jadwal::create($row['data']);
                $this->created++;
            } else {
                Jadwal::findOrFail($row['id'])->update($row['data']);
                $this->updated++;
            }
        }
    }

    private function reference(Collection $records, mixed $id, mixed $value, string $column, int $line, string $label): int
    {
        $id = $id !== null && $id !== '' ? $id : (ctype_digit((string) $value) ? $value : null);
        $matches = $records->filter(function (mixed $record) use ($id, $value, $column): bool {
            if ($id !== null) {
                return (string) $record->id === (string) $id;
            }

            return $column === '' || ($value !== null && strcasecmp(trim((string) $record->{$column}), (string) $value) === 0);
        });
        if ($matches->count() !== 1) {
            $this->fail($line, $label.' tidak ditemukan atau cocok dengan lebih dari satu data. Periksa nama atau gunakan kolom ID dari ekspor terbaru.');
        }

        return (int) $matches->first()->id;
    }

    private function time(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value) && (float) $value >= 0 && (float) $value < 1) {
            $seconds = (int) round((float) $value * 86400);

            return sprintf('%02d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
        }
        $value = (string) $value;
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $value, $matches)) {
            return sprintf('%02d:%02d:%02d', $matches[1], $matches[2], $matches[3] ?? 0);
        }

        return $value;
    }

    private function fail(int $line, string $message): never
    {
        throw ValidationException::withMessages(['file' => "Baris {$line}: {$message}"]);
    }
}
