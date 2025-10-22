<?php
    namespace App\Repository;
    use Illuminate\Support\Facades\DB;
    

    class UserRepositoryInRD implements UserRepository{
        function Create($user){
            return DB::table('users')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'created_at' => now(),
                'valid' => false 
            ]);
        }
    }
?>