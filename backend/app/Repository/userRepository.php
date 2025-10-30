<?php
    namespace App\Repository;
    interface UserRepository{
        function Create($user);
        function FindByEmail($email);
        function CreateValidation($validationData);
    }
?>