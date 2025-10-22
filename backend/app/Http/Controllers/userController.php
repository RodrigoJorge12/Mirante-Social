<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
class UserController{
    private $userRepository;

    public function __construct($userRepository){
        $this->userRepository = $userRepository;
    }

    public function create($user){
        $user = $this->userRepository->Create($user);
        Log::info('User created: ', (array)json_decode($user));
        return $user;
    }
}
?>