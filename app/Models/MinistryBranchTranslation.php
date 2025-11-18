<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinistryBranchTranslation extends Model
{
    protected $fillable = [
        'ministry_branch_id',
        'locale',
        'name'
    ];

    public function ministryBranch()
    {
        return $this->belongsTo(MinistryBranch::class);
    }
}
