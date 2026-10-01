<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceSample extends Model
{
    protected $fillable = [
        'siswa_id', 'pegawai_id', 'label', 'model', 'descriptor', 'is_active', 'created_by',
    ];

    protected $hidden = ['descriptor'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['descriptor' => 'encrypted:array', 'is_active' => 'boolean'];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
