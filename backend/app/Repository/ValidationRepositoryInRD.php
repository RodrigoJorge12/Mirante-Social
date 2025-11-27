<?php

namespace App\Repository;

use App\Models\Validation;


class ValidationRepositoryInRD implements ValidationRepository
{
    public function create($validation)
    {
        return Validation::create($validation);
    }
    public function findByEmailAndCode($email, $code, $type = 'email_validation')
    {
        return Validation::whereIn('user_id', function ($query) use ($email) {
            $query->select('id')->from('users')->where('email', $email);
        })
            ->where('code', $code)
            ->where('type', $type)
            ->where('time', '>', now())
            ->first();
    }
}
