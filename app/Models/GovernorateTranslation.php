<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernorateTranslation extends Model
{
    protected $fillable = ['governorate_id', 'locale', 'name'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
