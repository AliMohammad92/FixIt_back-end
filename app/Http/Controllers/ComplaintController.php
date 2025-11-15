<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitComplaintRequest;
use App\Models\Complaint;
use App\Models\MinistryBranch;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    use ResponseTrait;
    public function submit(SubmitComplaintRequest $request)
    {
        $data = $request->validated();
        unset($data['media']);
        $user = User::where('id', Auth::id())->first();
        $data['citizen_id'] = $user->citizen->id;
        $ministryBranch = MinistryBranch::where('id', $data['ministry_branch_id'])->first();
        $data['reference_number'] = $ministryBranch->ministry->abbreviation . '_' . Str::ulid();
        return $data['reference_number'];
        $complaint = Complaint::create($data);

        $paths = [];
        foreach ($request->file('media', []) as $file) {
            $path = $file->store('complaints', 'public');
            $paths[] = $path;

            $complaint->media()->create([
                'path' => $path,
                'type' => in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx']) ? 'file' : 'img',
            ]);
        }

        return $this->successResponse(__('messages.complaint_submitted'), ['complaint' => $complaint], 201);
    }
}
