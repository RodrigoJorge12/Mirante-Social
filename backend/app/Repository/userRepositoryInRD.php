<?php
    namespace App\Repository;
    use Illuminate\Support\Facades\DB;
    

    class UserRepositoryInRD implements UserRepository{
        function Create($user){
            return DB::table('users')->insert([
                'nome' => $user->name,
                'email' => $user->email,
                'senha' => $user->password
            ]);
        }
    }
?>