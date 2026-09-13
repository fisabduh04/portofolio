<?php

namespace App\Services;

use App\Models\Tahun;
use App\Models\WaliKelas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SaveWaliKelasAssignments
{
    /** @param array<int, array{kelas_id: int|string, pegawai_id: int|string, is_active: bool|int|string, keterangan?: ?string}> $rows */
    public function save(int $tahunId, array $rows): void
    {
        DB::transaction(function () use ($tahunId, $rows): void {
            Tahun::whereKey($tahunId)->lockForUpdate()->firstOrFail();
            foreach ($rows as $row) {
                $assignment = WaliKelas::firstOrNew([
                    'tahun_id' => $tahunId, 'kelas_id' => $row['kelas_id'], 'pegawai_id' => $row['pegawai_id'],
                ]);
                if ((bool) $row['is_active']) {
                    WaliKelas::where('tahun_id', $tahunId)->where('kelas_id', $row['kelas_id'])
                        ->when($assignment->exists, fn (Builder $query): Builder => $query->whereKeyNot($assignment->id))
                        ->where('is_active', true)->update(['is_active' => false]);
                }
                $assignment->fill($row)->save();
            }
        }, 3);
    }
}
