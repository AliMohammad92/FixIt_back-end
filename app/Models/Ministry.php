<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $fillable = [
        'ministry_name',
        'abbreviation',
        'description',
        'status',
        'manager_id',
    ];
    public function branches()
    {
        return $this->hasMany(MinistryBranch::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
