<?php

namespace App\DAO;

use App\Models\UserOTP;

class UserOtpDAO
{
    public function store($user_id, $otp, $expiresAt)
    {
        return UserOTP::create([
            'user_id' => $user_id,
            'otp_code' => $otp,
            'expires_at' => $expiresAt,
        ]);
    }
}
