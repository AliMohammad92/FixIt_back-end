<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReplyResource;
use App\Services\ReplyService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    use ResponseTrait;

    protected $replyService;

    public function __construct(ReplyService $replyService)
    {
        $this->replyService = $replyService;
    }
    public function addReply($complaint_id, Request $request)
    {
        $user = Auth::user();
        $sender = $user->citizen ?? $user->employee;

        $request->validate([
            'content' => 'required|string|max:500',
            'media'   => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096',
        ]);

        $result = $this->replyService->addReply($complaint_id, $sender, $request->all());
        if ($result) {
            return $this->successResponse($result, __('messages.reply_sent'));
        }
        return $this->errorResponse(__('messages.reply_failed'));
    }

    public function read($complaint_id)
    {
        $result = $this->replyService->readReplies($complaint_id);
        if ($result) {
            return $this->successResponse(ReplyResource::collection($result), __('messages.replies_retrieved'));
        }
        return $this->errorResponse(__('messages.complaint_not_found'));
    }

    public function delete($id)
    {
        $reply = $this->replyService->delete($id);
        if ($reply) {
            return $this->successResponse([], __('messages.deleted_successfully'));
        }

        return $this->errorResponse(__('messages.not_found'), 404);
    }
}
