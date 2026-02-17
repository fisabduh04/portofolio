<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\Tahun;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KelasSiswa extends Model
{
    use HasFactory;
    protected $table="kelas_siswas";
    protected $fillable=['siswa_id','kelas_id','tahun_id','ket'];

    public function kelas(): BelongsTo
    {
       return $this->belongsTo(Kelas::class);
    }

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }


    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
