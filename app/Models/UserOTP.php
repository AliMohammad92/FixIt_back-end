<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOTP extends Model
{
    protected $fillable = [
        'user_id',
        'otp_code',
        'expires_at'
    ];

    protected $table = 'user_otps';
}
