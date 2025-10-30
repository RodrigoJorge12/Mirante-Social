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
        function FindByEmail($email){
            return DB::table('users')->where('email', $email)->first();
        }
        function CreateValidation($validationData){
            return DB::table('validations')->insert([
                'type' => $validationData['type'],
                'user_id' => $validationData['user_id'],
                'code' => $validationData['code'],
                'time' => $validationData['time'],
                'created_at' => $validationData['created_at']
            ]); 
        }
    }
?>