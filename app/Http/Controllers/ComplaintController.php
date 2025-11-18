<?php

namespace App\Http\Controllers;

use App\DAO\CitizenDAO;
use App\DAO\ComplaintDAO;
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

    protected $complaintDAO, $citizenDAO;

    public function __construct()
    {
        $this->complaintDAO = new ComplaintDAO();
        $this->citizenDAO = new CitizenDAO();
    }

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
        if (!$user->hasRole('super_admin') || !($user->hasRole('employee') && $user->employee->ministry_branch_id == $ministry_branch_id)) {
            return $this->errorResponse(
                __('messages.unauthorized'),
                403
            );
        }

        $complaints = $this->complaintDAO->getComplaints($ministry_branch_id);
        $complaints = ComplaintResource::collection($complaints);
        return $this->successResponse(
            ['complaints' => $complaints],
            __('messages.complaints_retrieved')
        );
    }

    public function getMyComplaints(ComplaintService $complaintService)
    {
        $user = Auth::user();
        $citizen_id = $user->citizen->id;

        $complaints = $complaintService->getMyComplaints($citizen_id);

        return $this->successResponse(
            ['complaints' => $complaints],
            __('messages.complaints_retrieved')
        );
    }

    public function getComplaint($complaint_id)
    {
        $complaint = $this->complaintDAO->findById($complaint_id);
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
