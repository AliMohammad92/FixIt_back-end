<?php

namespace App\DAO;

use App\Models\Complaint;
use App\Services\MinistryService;

class ComplaintDAO
{
    public function getByBranch($ministry_branch_id)
    {
        return Complaint::where('ministry_branch_id', $ministry_branch_id)->get();
    }

    public function getByMinistry($branchIds)
    {
        return Complaint::whereIn('ministry_branch_id', $branchIds)->get();
    }

    public function getMyComplaints($citizen_id)
    {
        return Complaint::where('citizen_id', $citizen_id)->get();
    }

    public function read()
    {
        return Complaint::all();
    }

    public function readOne($id)
    {
        return Complaint::where('id', $id)->first();
    }
}
