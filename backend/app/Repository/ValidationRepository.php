<?php

namespace App\Repository;

interface ValidationRepository
{
    function create($validation);
    function findByEmailAndCode($email, $code);
}
