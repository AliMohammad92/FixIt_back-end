<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinistryBranch extends Model
{
    protected $fillable = [
        'ministry_id',
        'name',
        'manager_id',
        'location',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
