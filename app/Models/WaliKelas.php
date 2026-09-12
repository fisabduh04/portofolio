<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaliKelas extends Model
{
    /** @use HasFactory<\Database\Factories\WaliKelasFactory> */
    use HasFactory;

    protected $table = 'wali_kelas';

    protected $fillable = ['tahun_id', 'kelas_id', 'pegawai_id', 'is_active', 'keterangan'];

    protected $hidden = ['active_slot'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tahun(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function kelas(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pegawai(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
