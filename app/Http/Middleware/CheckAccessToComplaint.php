<?php

namespace App\Http\Middleware;

use App\Services\ComplaintService;
use App\Services\ReplyService;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessToComplaint
{
    use ResponseTrait;

    protected $complaintService, $replyService;

    public function __construct(
        ComplaintService $complaintService,
        ReplyService $replyService
    ) {
        $this->complaintService = $complaintService;
        $this->replyService = $replyService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $complaintId = $request->route('complaint_id');
        $replyId = $request->route('reply_id');
        $status = false;
        $user = Auth::user();

        if ($complaintId) {
            $complaint = $this->complaintService->readOne($complaintId);

            if (!$complaint) {
                return $this->errorResponse(__('messages.complaint_not_found'), 404);
            }

            if ($this->hasAccess($user, $complaint)) {
                $status = true;
            }
        } else if ($replyId) {
            $reply = $this->replyService->readOne($replyId);

            if (!$reply) {
                return $this->errorResponse(__('messages.reply_not_found'), 404);
            }

            if ($this->hasAccessToReply($user, $reply)) {
                $status = true;
            }
        }

        if ($status)
            return $next($request);
        return $this->errorResponse(__('messages.access_denied'), 403);
    }

    private function hasAccess($user, $complaint): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->citizen && $user->citizen->id === $complaint->citizen_id) {
            return true;
        }

        if ($user->employee) {
            if (
                $user->hasRole('ministry_manager') &&
                $user->employee->ministry_id === $complaint->ministry_id
            ) {
                return true;
            }

            if ($user->employee->ministry_branch_id === $complaint->ministry_branch_id) {
                return true;
            }
        }

        return false;
    }

    private function hasAccessToReply($user, $reply)
    {
        $complaint = $this->complaintService->readOne($reply->complaint_id);
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->citizen && $user->citizen->id === $reply->sender_id) {
            return true;
        }

        if ($user->employee) {
            if (
                $user->hasRole('ministry_manager') &&
                $user->employee->ministry_id === $complaint->ministry_id
            ) {
                return true;
            }

            if ($user->employee->ministry_branch_id === $complaint->ministry_branch_id) {
                return true;
            }
        }

        return false;
    }
}
