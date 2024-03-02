<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device',
        'friendly_name',
        'attributes',
        'headers',
        'state',
        'ip_address',
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
