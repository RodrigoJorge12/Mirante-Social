<?php
    namespace App\Repository;
    interface UserRepository{
        function create($user);
        function findByEmail($email);
    }
?>