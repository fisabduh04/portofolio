<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
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
        return $query->where('hari', $data['hari'])
            ->where('tahun_id', $data['tahun_id'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($data) {
                // Logic: Same Teacher OR Same Class
                $q->where('pegawai_id', $data['pegawai_id'])
                  ->orWhere('kelas_id', $data['kelas_id']);
            })
            ->where(function ($q) use ($data) {
                // Standard Time Overlap: (StartA < EndB) && (EndA > StartB)
                $q->where('mulai', '<', $data['akhir'])
                  ->where('akhir', '>', $data['mulai']);
            });
    }

    public function scopeBentrokSaatIni($query, int $tahunId)
    {
        // Filter Current Year
        $query->where('jadwals.tahun_id', $tahunId);

        // Find records where ANOTHER record exists that conflicts with it
        return $query->whereExists(function ($sub) {
            $sub->select('j2.id')
                ->from('jadwals as j2')
                ->whereColumn('j2.hari', 'jadwals.hari')             // Same Day
                ->whereColumn('j2.tahun_id', 'jadwals.tahun_id')     // Same Year
                ->whereColumn('j2.id', '!=', 'jadwals.id')           // Not the same record
                
                // Logic: Same Teacher OR Same Class
                ->where(function ($w) {
                    $w->whereColumn('j2.pegawai_id', 'jadwals.pegawai_id') 
                      ->orWhereColumn('j2.kelas_id', 'jadwals.kelas_id');
                })

                ->where(function ($q) {
                    // Standard Time Overlap: (StartA < EndB) && (EndA > StartB)
                    $q->whereColumn('jadwals.mulai', '<', 'j2.akhir')
                      ->whereColumn('jadwals.akhir', '>', 'j2.mulai');
                });
        });
    }

    
}
