<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal extends Model
{
    use HasFactory;

    protected $fillable=[
        'tahun_id',
        'kelas_id',
        'pegawai_id',
        'mapel_id',
        'hari',
        'jam',
        'mulai',
        'akhir',
        'status',
        'ket',
    ];

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function scopeConflict($query, array $data, $excludeId = null)
    {
    return $query->where('pegawai_id', $data['pegawai_id'])
        ->where('hari', $data['hari'])
        ->where('tahun_id', $data['tahun_id'])
        ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
        ->where(function ($q) use ($data) {
            $q->whereBetween('mulai', [$data['mulai'], $data['akhir']])
              ->orWhereBetween('akhir', [$data['mulai'], $data['akhir']])
              ->orWhere(function ($sub) use ($data) {
                  $sub->where('mulai', '<=', $data['mulai'])
                      ->where('akhir', '>=', $data['akhir']);
              });
        });
    }

    public function scopeBentrokSaatIni($query, int $tahunId)
    {
        // 1. Filter berdasarkan tahun ajaran
        $query->where('jadwals.tahun_id',$tahunId);

        // 2. Lakukan self-join untuk menemukan bentrokan
        return $query->select('jadwals.*')
            ->join('jadwals as j2', function ($join) {
                $join->on('jadwals.id', '>', 'j2.id') // Hindari perbandingan ganda dan dengan diri sendiri
                    ->whereColumn('jadwals.pegawai_id', 'j2.pegawai_id') // Guru yang sama
                    ->whereColumn('jadwals.hari', 'j2.hari') // Hari yang sama
                    ->whereColumn('jadwals.tahun_id', 'j2.tahun_id') // Tahun ajaran yang sama
                    ->where(function ($q) {
                        // Logika tumpang tindih waktu (membandingkan baris dengan baris lain)
                        $q->where(function ($sub) {
                            $sub->whereColumn('jadwals.mulai', '>=', 'j2.mulai')
                                ->whereColumn('jadwals.mulai', '<', 'j2.akhir');
                        })->orWhere(function ($sub) {
                            $sub->whereColumn('jadwals.akhir', '>', 'j2.mulai')
                                ->whereColumn('jadwals.akhir', '<=', 'j2.akhir');
                        })->orWhere(function ($sub) {
                            $sub->whereColumn('jadwals.mulai', '<=', 'j2.mulai')
                                ->whereColumn('jadwals.akhir', '>=', 'j2.akhir');
                        });
                    });
            })
            ->distinct();
    }

    
}
