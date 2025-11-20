<?php

namespace App\DAO;

use App\Models\Citizen;

class CitizenDAO
{
    public function findById($citizen_id)
    {
        return Citizen::where('id', $citizen_id)->get();
    }

    public function completeInfo(array $data) {}
}
