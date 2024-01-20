<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateMeta extends Model
{
    protected $table = 'states_meta';


    public function states(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(State::class, 'metadata_id', 'metadata_id');
    }
}
