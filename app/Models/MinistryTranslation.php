<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinistryTranslation extends Model
{
    protected $fillable = [
        'ministry_id',
        'locale',
        'name',
        'description',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }
}
