<?php

namespace App\DAO;

use App\Models\User;

class UserDAO
{
    public function findById($user_id)
    {
        return User::where('id', $user_id)->first();
    }

    public function store(array $data)
    {
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        return $user->update([
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name'  => $data['last_name']  ?? $user->last_name,
            'email'      => $data['email']      ?? $user->email,
            'phone'      => $data['phone']      ?? $user->phone,
            'address'    => $data['address']    ?? $user->address,
        ]);
    }
}
