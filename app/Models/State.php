<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';


    public function metadata(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StateMeta::class, 'metadata_id', 'metadata_id');
    }
}
