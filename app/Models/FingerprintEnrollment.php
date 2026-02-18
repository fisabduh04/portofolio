<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;
use App\Models\FingerprintMachine;

class FingerprintEnrollment extends Model
{
    protected $fillable = [
        'pegawai_id',
        'fingerprint_machine_id',
        'fingerprint_user_id',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function fingerprintMachine()
    {
        return $this->belongsTo(FingerprintMachine::class);
    }
}
