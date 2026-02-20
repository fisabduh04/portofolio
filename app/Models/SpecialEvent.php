<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'start_time',
        'end_time',
        'is_mandatory',
        'bantuan_hadir',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_mandatory' => 'boolean',
    ];

    public function participants()
    {
        return $this->belongsToMany(Pegawai::class, 'special_event_participants');
    }
}
