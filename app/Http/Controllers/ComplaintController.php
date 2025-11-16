<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Citizen;
use App\Models\Complaint;
use App\Models\MinistryBranch;
use App\Models\User;
use App\Services\ComplaintService;
use App\Services\FileManagerService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    use ResponseTrait;
    public function submit(SubmitComplaintRequest $request, ComplaintService $complaintService)
    {
        $data = $request->validated();
        $complaint = $complaintService->submitComplaint($data, new FileManagerService());

        return $this->successResponse(
            ['complaint' => new ComplaintResource($complaint)],
            __('messages.complaint_submitted'),
            201
        );
    }

    public function getComplaints($ministry_branch_id)
    {
        $user = User::findOrFail(Auth::id());
        if (!$user->hasRole('super_admin')) {
            return $this->errorResponse(
                __('messages.unauthorized'),
                403
            );
        }

        $complaints = Complaint::where('ministry_branch_id', $ministry_branch_id)->get();
        $complaints = ComplaintResource::collection($complaints);
        return $this->successResponse(
            ['complaints' => $complaints],
            __('messages.complaints_retrieved')
        );
    }

    public function getMyComplaints()
    {
        $user = User::with('citizen')->findOrFail(Auth::id());
        $citizen_id = $user->citizen->id;
        $citizen = Citizen::findOrFail($citizen_id);
        if ($citizen->user->id != Auth::id()) {
            return $this->errorResponse(
                [],
                __('messages.unauthorized'),
                403
            );
        }

        $cacheKey = 'citizen_complaints_' . $citizen_id;
        $complaints = Cache::remember($cacheKey, 3600, function () use ($citizen_id) {
            return Complaint::where('citizen_id', $citizen_id)->get();
        });
        $complaints = ComplaintResource::collection($complaints);

        return $this->successResponse(
            ['complaints' => $complaints],
            __('messages.complaints_retrieved')
        );
    }

    public function getComplaint($complaint_id)
    {
        $complaint = Complaint::find($complaint_id);
        if (!$complaint) {
            return $this->errorResponse(
                [],
                __('messages.complaint_not_found'),
                404
            );
        }
        $cacheKey = 'complaint_' . $complaint_id;

        $complaint = Cache::remember($cacheKey, 3600, function () use ($complaint_id) {
            return Complaint::find($complaint_id);
        });
        $complaint = new ComplaintResource($complaint);
        return $this->successResponse(
            ['complaint' => $complaint],
            __('messages.complaint_retrieved'),
        );
    }
}
