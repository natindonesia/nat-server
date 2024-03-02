<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'state',
    ];

    protected $casts = [
        'state' => 'array',
    ];


}
