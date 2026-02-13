<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;

class AbsensiExport implements FromCollection
{
    protected $filters;
    protected $type;

    public function __construct($filters = [], $type = 'all')
    {
        $this->filters = $filters;
        $this->type = $type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Absensi::with(['siswa.kelas', 'logbook.jadwal.mapel', 'logbook.jadwal.pegawai']);

        if ($this->type == 'harian') {
            $date = $this->filters['date'] ?? now()->toDateString();
            $query->whereHas('logbook', fn($q) => $q->where('tanggal', $date));
        } elseif ($this->type == 'bulanan') {
            $month = $this->filters['month'] ?? date('m');
            $year = $this->filters['year'] ?? date('Y');
            $query->whereHas('logbook', fn($q) => $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year));
        } elseif ($this->type == 'tahunan') {
            $tahunId = $this->filters['tahun_id'] ?? null;
            if ($tahunId) {
                $query->whereHas('logbook.jadwal', fn($q) => $q->where('tahun_id', $tahunId));
            }
        }

        if (isset($this->filters['kelas_id']) && $this->filters['kelas_id']) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $this->filters['kelas_id']));
        }

        // Filter Type Guru
        $typeGuru = $this->filters['type_guru'] ?? 'mapel';
        $query->whereHas('logbook', function($q) use ($typeGuru) {
            if ($typeGuru === 'mapel') {
                $q->whereIn('kategori', ['mapel', 'piket_sub']);
            } elseif ($typeGuru === 'piket') {
                $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
            }
        });

        return $query->get()->map(function($absensi) {
            return [
                'Tanggal' => $absensi->logbook->tanggal ?? '-',
                'Siswa' => $absensi->siswa->nama ?? '-',
                'Kelas' => $absensi->siswa->kelas->kelas ?? '-',
                'Mapel' => $absensi->logbook->jadwal->mapel->mapel ?? '-',
                'Status' => $absensi->status,
                'Keterangan' => $absensi->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Tanggal', 'Siswa', 'Kelas', 'Mapel', 'Status', 'Keterangan'];
    }
}
