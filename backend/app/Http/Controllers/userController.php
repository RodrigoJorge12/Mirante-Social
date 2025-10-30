<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller{
    private $userRepository;

    public function __construct($userRepository){
        $this->userRepository = $userRepository;
    }
    public function validateEmailUser($user){
        $userFound = $this->userRepository->FindByEmail($user->email);
        if($userFound){
            $code = random_int(100000, 999999);
            $validationData = [
                'type' => 'email_validation',
                'user_id' => $userFound->id,
                'code' => $code,
                'time' => now()->addMinutes(30),
                'created_at' => now()
            ];
            $this->userRepository->CreateValidation($validationData);
            Mail::raw("Olá {$user->name}, bem-vindo ao Mirante Social! O seu codigo de validação é $code. ele é valido por apenas 30 minutos", function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Bem-vindo ao Mirante Social');
        });
        }
    }
    public function SendEmailUser($user){
        
    }

    public function create($user){
        $userCreated = $this->userRepository->Create($user);
        if ($userCreated){
            $this->validateEmailUser($user);
        }
        Log::info('User created: ', (array)json_decode($userCreated));
        return $user;
    }
}
?>