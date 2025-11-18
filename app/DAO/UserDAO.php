<?php

namespace App\DAO;

use App\Models\User;

class UserDAO
{
    public function findById($user_id)
    {
        return User::where('id', $user_id)->get();
    }
}
