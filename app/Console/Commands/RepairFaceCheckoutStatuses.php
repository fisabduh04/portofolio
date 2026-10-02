<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepairFaceCheckoutStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:repair-face-checkouts {--id=* : ID absensi yang sudah diperiksa} {--apply : Terapkan koreksi pada ID yang dipilih}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pratinjau dan koreksi status Pulang yang berasal dari scan wajah sesi pulang';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ids = $this->option('id');
        if (collect($ids)->contains(fn ($id): bool => ! ctype_digit((string) $id) || (int) $id < 1) || ($this->option('apply') && $ids === [])) {
            $this->error('Gunakan --id dengan ID absensi yang telah diperiksa sebelum --apply.');

            return self::FAILURE;
        }
        $query = Absensi::where('status', 'Pulang')
            ->where('keterangan', 'REGEXP', '^Presensi wajah [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$')
            ->whereHas('logbook', fn (Builder $query): Builder => $query->where('kategori', 'piket_pulang'));
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }
        $this->table(['ID', 'Siswa ID', 'Tanggal', 'Keterangan'], (clone $query)->with('logbook')->get()->map(fn (Absensi $record): array => [
            $record->id, $record->siswa_id, $record->logbook->tanggal, $record->keterangan,
        ])->all());
        if (! $this->option('apply')) {
            $this->info('Pratinjau saja. Periksa asal catatan; gunakan --id=ID --apply untuk memperbaiki.');

            return self::SUCCESS;
        }
        $count = DB::transaction(function () use ($query): int {
            $records = $query->lockForUpdate()->get();
            foreach ($records as $record) {
                $record->update(['status' => 'Hadir']);
                Log::info('Koreksi status scan wajah pulang', ['absensi_id' => $record->id, 'before' => 'Pulang', 'after' => 'Hadir']);
            }

            return $records->count();
        });
        $this->info("Diperbaiki: {$count} catatan.");

        return self::SUCCESS;
    }
}
