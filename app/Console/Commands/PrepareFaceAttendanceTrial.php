<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrepareFaceAttendanceTrial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:prepare-face-trial {siswa} {kelas} {tahun} {pegawai} {mapel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Siapkan penempatan siswa dan jadwal uji wajah dua jam mulai sekarang, tanpa membuat presensi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $student = Siswa::findOrFail($this->argument('siswa'));
        $kelas = Kelas::findOrFail($this->argument('kelas'));
        $year = Tahun::aktif()->findOrFail($this->argument('tahun'));
        $teacher = Pegawai::findOrFail($this->argument('pegawai'));
        $subject = Mapel::findOrFail($this->argument('mapel'));
        $start = now()->startOfMinute();
        $end = $start->copy()->addHours(2)->min($start->copy()->endOfDay());
        $data = [
            'kelas_id' => $kelas->id, 'tahun_id' => $year->id, 'pegawai_id' => $teacher->id,
            'mapel_id' => $subject->id, 'hari' => $start->locale('id')->isoFormat('dddd'),
            'jam' => 1, 'mulai' => $start->format('H:i:s'), 'akhir' => $end->format('H:i:s'),
            'status' => 'Aktif', 'ket' => 'Uji absensi wajah '.$start->toDateString(),
        ];
        if (KelasSiswa::where('siswa_id', $student->id)->whereHas('tahun', fn (Builder $query) => $query->aktif())->exists()) {
            $this->error('Siswa sudah memiliki penempatan aktif. Tidak ada perubahan dibuat.');

            return self::FAILURE;
        }
        if (Jadwal::conflict($data)->exists()) {
            $this->error('Jadwal uji bertabrakan dengan jadwal kelas/guru. Tidak ada perubahan dibuat.');

            return self::FAILURE;
        }
        $schedule = DB::transaction(function () use ($student, $data): Jadwal {
            KelasSiswa::create(['siswa_id' => $student->id, 'kelas_id' => $data['kelas_id'], 'tahun_id' => $data['tahun_id'], 'ket' => 'aktif']);
            JadwalPiket::firstOrCreate(['pegawai_id' => $data['pegawai_id'], 'tahun_id' => $data['tahun_id'], 'hari' => $data['hari']]);

            return Jadwal::create($data);
        });
        $this->info('Jadwal #'.$schedule->id.' | '.$student->nama.' | '.$kelas->kelas.' | '.$teacher->name);
        $this->info($start->format('Y-m-d H:i').' - '.$end->format('H:i').' '.config('app.timezone'));

        return self::SUCCESS;
    }
}
