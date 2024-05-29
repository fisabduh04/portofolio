<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\kelas;
use App\Models\tahun;
use App\Models\siswa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KelasSiswa extends Model
{
    use HasFactory;
    protected $table="kelas_siswas";
    protected $fillable=['siswa_id','kelas_id','tahun_id','ket'];

    public function kelas(): BelongsTo
    {
       return $this->belongsTo(kelas::class);
    }

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(tahun::class);
    }


    public function siswa(): BelongsTo
    {
        return $this->belongsTo(siswa::class);
    }
}
