<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Services\ComplaintService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    use ResponseTrait;

    protected $service;

    public function __construct(ComplaintService $complaintService)
    {
        $this->service = $complaintService;
    }

    public function submit(SubmitComplaintRequest $request)
    {
        $data = $request->validated();
        $complaint = $this->service->submitComplaint($data);

        return $this->successResponse(
            ['complaint' => new ComplaintResource($complaint)],
            __('messages.complaint_submitted'),
            201
        );
    }

    public function read()
    {
        $complaints = $this->service->read();
        if ($complaints->isEmpty()) {
            return $this->successResponse([], __('messages.empty'));
        }
        return $this->successResponse(ComplaintResource::collection($complaints), __('messages.complaints_retrieved'));
    }

    public function getMyComplaints()
    {
        $user = Auth::user();
        $citizen_id = $user->citizen->id;

        $complaints = $this->service->getMyComplaints($citizen_id);

        return $this->successResponse(
            ['complaints' => ComplaintResource::collection($complaints)],
            __('messages.complaints_retrieved')
        );
    }

    public function getByMinistry($id)
    {
        $user = Auth::user();
        $complaints = $this->service->getByMinistry($id, $user);
        if (!$complaints) {
            return $this->errorResponse(__('messages.unauthorized'), 401);
        }

        if ($complaints->isEmpty()) {
            return $this->successResponse([], __('messages.empty'));
        }

        return $this->successResponse(ComplaintResource::collection($complaints), __('messages.complaints_retrieved'));
    }

    public function getByBranch($id)
    {
        $user = Auth::user();
        $complaints = $this->service->getByBranch($id, $user);
        if (!$complaints) {
            return $this->errorResponse(__('messages.unauthorized'), 401);
        }

        if ($complaints->isEmpty()) {
            return $this->successResponse([], __('messages.empty'));
        }

        return $this->successResponse(ComplaintResource::collection($complaints), __('messages.complaints_retrieved'));
    }

    public function readOne($id)
    {
        $complaint = $this->service->readOne($id);
        if (!$complaint) {
            return $this->errorResponse(
                __('messages.complaint_not_found'),
                404
            );
        }

        return $this->successResponse(
            new ComplaintResource($complaint),
            __('messages.complaint_retrieved'),
        );
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:resolved,rejected',
            'reason' => 'nullable|string|max:500'
        ]);

        $result = $this->service->updateStatus($id, $request->status, $request->reason, Auth::id());
        if ($result)
            return $this->successResponse([], __('messages.complaint_status_updated'));

        return $this->errorResponse(__('messages.complaint_locked_by_other'));
    }

    public function startProcessing($id)
    {
        $result = $this->service->startProcessing($id, Auth::id());

        if (!$result['status']) {
            return $this->errorResponse(__("messages.{$result['reason']}"), 401);
        }

        return $this->successResponse([], __('messages.complaint_started_processing'));
    }

    public function delete($id)
    {
        $result = $this->service->delete($id);
        if (!$result) {
            return $this->errorResponse(__('messages.not_found'), 404);
        }
        return $this->successResponse([], __('messages.deleted_successfully'));
    }
}
