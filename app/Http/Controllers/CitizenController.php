<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Services\SignUpService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    use ResponseTrait;
    public function signUp(SignUpRequest $request, SignUpService $signUpService)
    {
        $data = $signUpService->signUp($request);
        if ($data) {
            return $this->successResponse($data, 'We sent an OTP to your email, please check it', 201);
        } else {
            return $this->errorResponse('Registration failed', 500);
        }
    }
}
