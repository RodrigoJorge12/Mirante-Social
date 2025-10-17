<?php
namespace App\Http\Controllers;
class UserController{
    private $userRepository;

    public function __construct($userRepository){
        $this->userRepository = $userRepository;
    }

    public function create($user){
        return $this->userRepository->Create($user);
    }
}
?>