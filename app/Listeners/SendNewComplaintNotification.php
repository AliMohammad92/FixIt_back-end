<?php

namespace App\Listeners;

use App\Events\ComplaintCreated;
use App\Models\Employee;
use App\Notifications\NewComplaintNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewComplaintNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ComplaintCreated $event)
    {
        $branchId = $event->complaint->ministry_branch_id;

        $employees = Employee::where('ministry_branch_id', $branchId)->get();

        foreach ($employees as $employee) {
            $employee->notify(new NewComplaintNotification($event->complaint));
        }
    }
}
