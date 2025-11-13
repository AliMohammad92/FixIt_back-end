<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOTP;
use App\Services\OTPService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class UserOTPController extends Controller
{
    use ResponseTrait;

    public function verifyOTP(Request $request, OTPService $otpService)
    {
        $request->validate([
            'otp_code' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $otpCode = $request->input('otp_code');
        $user_id = $request->input('user_id');
        $status = $otpService->verifyOTP($otpCode);

        $user_id = UserOTP::where('otp_code', $otpCode)->where('user_id', $user_id)->first()->user_id;
        $user = User::find($user_id);
        $user->status = true;
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($status) {
            return $this->successResponse(['token' => $token], 'OTP verified successfully', 200);
        } else {
            return $this->errorResponse('Invalid or expired OTP', 400);
        }
    }

    public function resendOTP(OTPService $otpService, Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $user_id = $request->input('user_id');
        $data = $otpService->resendOTP($user_id);

        if ($data) {
            return $this->successResponse($data, 'A new OTP has been sent to your email', 200);
        } else {
            return $this->errorResponse('Failed to resend OTP', 500);
        }
    }
}
