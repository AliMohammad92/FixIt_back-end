<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    use ResponseTrait;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signUp(SignUpRequest $request)
    {
        $result = $this->userService->signUp($request->validated());
        if ($result) {
            return $this->successResponse($result, __('messages.otp_sent'), 201);
        } else {
            return $this->errorResponse(__('messages.registration_failed'), 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->userService->login($request->only('login', 'password'));
        if (!$result) {
            return $this->errorResponse(__('messages.invalid_credentials'), 401);
        }
        $result['user'] = new UserResource($result['user']);
        return $this->successResponse($result, __('messages.login_success'), 200);
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $result = $this->userService->refreshToken($request->input('refresh_token'));
        if (!$result) {
            return $this->errorResponse(__('messages.invalid_refresh_token'), 401);
        }

        return $this->successResponse($result, __('messages.token_refreshed'), 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return $this->successResponse([], __('messages.logout_success'));
    }

    public function update(Request $request)
    {
        $user = $this->userService->update(Auth::id(), $request->all());
        if (!$user)
            return $this->errorResponse(__('messages.user_not_found'), 404);

        return $this->successResponse($user, __('messages.user_info_updated'));
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'img' => 'required|file|mimes:jpg,jpeg,png'
        ]);

        $user = Auth::user();
        $user = $this->userService->uploadProfileImage($request->file('img'), $user);

        return $this->successResponse(
            new UserResource($user),
            __('messages.user_info_updated'),
        );
    }

    public function deleteProfileImage()
    {
        $user = Auth::user();
        $status = $this->userService->deleteProfileImage($user) ? true : false;

        if ($status) {
            return $this->successResponse(
                new UserResource($user),
                __('messages.deleted_successfully'),
            );
        } else {
            return $this->errorResponse(
                __('messages.img_not_found'),
            );
        }
    }
}
