<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use App\Models\User;


class UserRepositoryInRD implements UserRepository
{
    public function create($userData)
    {
        return User::create($userData); // Eloquent já cuida do resto
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }
    public function validateUserByEmail($email)
    {
        return User::where('email', $email)
            ->update(['valid' => true]);
    }
    public function validateUserCredentials($email, $password)
    {
        $user = User::where('email', $email)->first();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
