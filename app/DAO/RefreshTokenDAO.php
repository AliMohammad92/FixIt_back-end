<?php

namespace App\DAO;

use App\Models\RefreshToken;
use App\Models\User;

class RefreshTokenDAO
{
    public function findByToken($token)
    {
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function store($user_id, $token, $device_name)
    {
        return RefreshToken::create([
            'user_id' => $user_id,
            'token' => $token,
            'device_name' => $device_name ?? 'unknown',
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function update($storedToken, $new_refresh_token)
    {
        $storedToken->update([
            'token' => $new_refresh_token,
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function delete($user_id, $device_name)
    {
        RefreshToken::where('user_id', $user_id)
            ->where('device_name', $device_name)
            ->delete();
    }
}
