<?php

namespace App\Repository;

use App\Models\Validation;


class ValidationRepositoryInRD implements ValidationRepository
{
    public function create($validation)
    {
        return Validation::create($validation);
    }
    public function findByEmailAndCode($email, $code)
    {
        return Validation::whereIn('user_id', function ($query) use ($email) {
            $query->select('id')->from('users')->where('email', $email);
        })
            ->where('code', $code)
            ->where('time', '>', now())
            ->first();
    }
}
