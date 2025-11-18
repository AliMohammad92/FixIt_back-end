<?php

namespace App\DAO;

use App\Models\Complaint;

class ComplaintDAO
{
    public function getComplaints($ministry_branch_id)
    {
        return Complaint::where('ministry_branch_id', $ministry_branch_id)->get();
    }

    public function getMyComplaints($citizen_id)
    {
        return Complaint::where('citizen_id', $citizen_id)->get();
    }

    public function getAllComplaints() {}

    public function findById($complaint_id)
    {
        return Complaint::find($complaint_id);
    }
}
