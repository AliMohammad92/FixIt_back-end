<?php

namespace App\Services;

use App\DAO\CitizenDAO;
use App\DAO\RefreshTokenDAO;
use App\DAO\UserDAO;
use App\DAO\UserOtpDAO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    protected $userDAO, $otpDAO, $refreshTokenDAO, $citizenDAO;

    public function __construct(
        UserDAO $userDAO,
        UserOtpDAO $otpDAO,
        RefreshTokenDAO $refreshTokenDAO,
        CitizenDAO $citizenDAO
    ) {
        $this->userDAO = $userDAO;
        $this->otpDAO = $otpDAO;
        $this->refreshTokenDAO = $refreshTokenDAO;
        $this->citizenDAO = $citizenDAO;
    }

    public function signUp(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['role'] = 'citizen';
            $user = $this->userDAO->store($data);

            $citizenData = [
                'nationality'   => $data['nationality'],
                'national_id'   => $data['national_id']
            ];

            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(5);

            unset($data['national_id'], $data['nationality']);

            $this->otpDAO->store($user->id, $otp, $expiresAt);

            $user->assignRole('citizen');
            $this->citizenDAO->store($user, $citizenData);

            // Mail::to($user->email)->send(new OtpMail($otp)); // Assuming OtpMail is a Mailable class
            return [
                'user_id' => $user->id,
                'otp_sent' => true,
                'otp_code' => app()->environment('local') ? $otp : null,
            ];
        });
    }

    public function login(array $data)
    {
        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $loginType => $data['login'],
            'password' => $data['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = $this->userDAO->findById(Auth::id());

        if (!$user->status) {
            return false;
        }

        $access_token = $user->createToken('auth_token', ['*']);
        $refresh_token = Str::random(64);
        $access_token->accessToken->update(['expires_at' => now()->addMinutes(15)]);

        $this->refreshTokenDAO->delete($user->id, request()->userAgent());

        $hashedToken = hash('sha256', $refresh_token);

        $this->refreshTokenDAO->store($user->id, $hashedToken, request()->header('User-Agent'));

        $token = [
            'access_token' => $access_token->plainTextToken,
            'refresh_token' => $refresh_token,
        ];

        $permissions = $user->roles
            ->flatMap(function ($role) {
                return $role->permissions->pluck('name');
            })
            ->unique()
            ->values()
            ->toArray();

        $data = ['user' => $user, 'tokens' => $token, 'permissions' => $permissions];
        return $data;
    }

    public function refreshToken(string $refreshToken)
    {
        $hashedToken = hash('sha256', $refreshToken);
        $storedToken = $this->refreshTokenDAO->findByToken($hashedToken);

        if (!$storedToken) {
            return false;
        }

        $user = $this->userDAO->findById($storedToken->user_id);
        $access_token = $user->createToken('auth_token')->plainTextToken;

        $plainRefresh  = Str::random(64);
        $hashedRefresh  = hash('sha256', $plainRefresh);
        $this->refreshTokenDAO->update($storedToken, $hashedRefresh);

        $tokens = [
            'access_token' => $access_token,
            'refresh_token' => $plainRefresh,
        ];

        return $tokens;
    }

    public function update($id, $data)
    {
        $user = $this->userDAO->findById($id);
        if (!$user) {
            return false;
        }
        return $this->userDAO->update($user, $data);
    }
}
