<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintMachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'comkey',
        'location',
        'status',
        'is_scheduler_active',
        'scheduler_start_time',
        'scheduler_end_time',
    ];
}
