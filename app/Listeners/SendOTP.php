<?php

namespace App\Listeners;

use App\Events\OTPEvent;
use App\Mail\OtpMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOTP implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OTPEvent $event): void
    {
        $email = $event->email;
        $otp = $event->otp;

        Mail::to($email)->send(new OtpMail($otp));
    }
}
