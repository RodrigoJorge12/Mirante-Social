<?php

namespace App\Repository;

interface UserRepository
{
    function create($user);
    function findByEmail($email);
    function validateUserByEmail($email);
    function validateUserCredentials($email, $password);
    function updatePassword($id, $newPassword);
}
