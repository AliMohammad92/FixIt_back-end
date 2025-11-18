<?php

namespace App\Services;

use App\DAO\ComplaintDAO;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\MinistryBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new ComplaintDAO();
    }


    public function submitComplaint(array $data, FileManagerService $fileManagerService)
    {
        $media = $data['media'] ?? null;
        unset($data['media']);

        $data['citizen_id'] = Auth::user()->citizen->id;

        $ministryBranch = MinistryBranch::with('ministry')->findOrFail($data['ministry_branch_id']);
        $ministryAbbr = $ministryBranch->ministry->abbreviation;

        $governorateCode = DB::table('governorates')
            ->where('id', $data['governorate_id'])
            ->value('code');

        $data['reference_number'] = sprintf(
            '%s_%s_%s',
            $ministryAbbr,
            $governorateCode,
            Str::random(8)
        );

        $complaint = Complaint::create($data);

        $fileManagerService->storeComplaintMedia(
            $complaint,
            $media,
            $ministryAbbr,
            $governorateCode,
            $data['reference_number']
        );
        return $complaint;
    }

    public function getMyComplaints($citizen_id)
    {
        $cacheKey = 'citizen_complaints_' . $citizen_id;
        return Cache::remember($cacheKey, 3600, function () use ($citizen_id) {
            return $this->dao->getMyComplaints($citizen_id);
        });
    }

    public function getComplaints($ministry_branch_id)
    {
        $cacheKey = 'ministry_branch_complaints_' . $ministry_branch_id;
        return Cache::remember($cacheKey, 3600, function () use ($ministry_branch_id) {
            return $this->dao->getComplaints($ministry_branch_id);
        });
    }
}
