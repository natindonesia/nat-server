<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'ip_address',
        'headers',
        'state',
    ];

    protected $hidden = [
        'id',
        'headers',
        'updated_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'state' => 'array',
    ];


}
