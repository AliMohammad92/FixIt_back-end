<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    use ResponseTrait;
    public function signUp(SignUpRequest $request, AuthService $authService)
    {
        $result = $authService->signUp($request->validated());
        if ($result) {
            return $this->successResponse($result, 'We sent an OTP to your email, please check it', 201);
        } else {
            return $this->errorResponse('Registration failed', 500);
        }
    }

    public function login(Request $request, AuthService $authService)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $authService->login($request->only('login', 'password'));
        if (!$result) {
            return $this->errorResponse('Invalid login credentials', 401);
        }

        return $this->successResponse(['token' => $result], 'Login successful', 200);
    }
}
