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

    public function read()
    {
        $cacheKey = 'all_complaints';
        return Cache::remember($cacheKey, 3600, function () {
            return $this->dao->read();
        });
    }

    public function getByBranch($ministry_branch_id, $user)
    {
        $isAuthorized =
            $user->hasRole('super_admin') ||
            (
                $user->hasRole('employee') &&
                $user->employee->ministry_branch_id == $ministry_branch_id
            );

        if (!$isAuthorized) {
            return false;
        }

        $cacheKey = 'ministry_branch_complaints_' . $ministry_branch_id;
        return Cache::remember($cacheKey, 3600, function () use ($ministry_branch_id) {
            return $this->dao->getByBranch($ministry_branch_id);
        });
    }

    public function getByMinistry($ministry_id, $user)
    {
        $isAuthorized =
            $user->hasRole('super_admin') ||
            (
                $user->hasRole('ministry_manager') &&
                $user->employee->ministry_id == $ministry_id
            );

        if (!$isAuthorized) {
            return false;
        }
        $cacheKey = "ministry_complaints_{$ministry_id}";

        return Cache::remember($cacheKey, 3600, function () use ($ministry_id) {
            $ministryService = app(MinistryService::class);
            $ministry = $ministryService->readOne($ministry_id);

            if (!$ministry) {
                return collect();
            }

            $branchIds = $ministry->branches->pluck('id');

            return $this->dao->getByMinistry($branchIds);
        });
    }

    public function readOne($id)
    {
        $cacheKey = "complaint {$id}";
        return Cache::remember($cacheKey, 3600, function () use ($id) {
            return $this->dao->readOne($id);
        });
    }
}
