<?php

namespace App\Listeners;

use App\Events\ComplaintStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendComplaintStatusNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ComplaintStatusChanged $event): void
    {
        //
    }
}
