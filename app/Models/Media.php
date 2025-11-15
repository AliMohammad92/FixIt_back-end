<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'type',
        'path',
        'mediable_id',
        'mediable_type',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}
