<?php

namespace App\Console\Commands;

use App\Models\FaceSample;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneInactiveFaceSamples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:prune-inactive-face-samples {--apply : Hapus permanen sampel siswa yang is_active=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pratinjau atau hapus sampel wajah siswa lama yang telah dinonaktifkan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = FaceSample::whereNotNull('siswa_id')->whereNull('pegawai_id')->where('is_active', false);
        if (! $this->option('apply')) {
            $this->info('Sampel nonaktif: '.$query->count().'. Gunakan --apply untuk menghapus. Sampel aktif tetap disimpan.');

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        Log::info('Sampel wajah siswa nonaktif dibersihkan', ['deleted_samples' => $deleted]);
        $this->info("Dihapus: {$deleted} sampel nonaktif.");

        return self::SUCCESS;
    }
}
