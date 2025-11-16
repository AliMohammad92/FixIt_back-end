<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\MinistryBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintService
{
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
}
