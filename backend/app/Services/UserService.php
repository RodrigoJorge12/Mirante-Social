<?php

namespace App\Services;

use App\Models\User;
use App\Models\Validation;
use App\Repository\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private ValidationService $validationService
    ) {}

    /**
     * Create a new user and send email validation.
     */
    public function createUser(array $userData): User
    {
        try {
            // Create user
            $user = $this->userRepository->create($userData);
            $validation = $this->validationService->createValidation($user);
            $this->validationService->sendEmail(
                $user->email,
                $user->name,
                "Olá {$user->name}, bem-vindo ao Mirante Social! O seu codigo de validação é {$validation->code}. ele é valido por apenas 30 minutos",
                "Bem-vindo ao Mirante Social!"
            );

            return $user;
        } catch (Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'email' => $userData['email'] ?? 'unknown'
            ]);
            throw $e;
        }
    }
    public function validateUserByEmail($email)
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new Exception("Usuário não encontrado");
        }
        $validateUserByEmail = $this->userRepository->validateUserByEmail($email);
        return $user;
    }
    public function login($email, $password)
    {
        $user = $this->userRepository->validateUserCredentials($email, $password);
        if (!$user) {
            throw new Exception("Credenciais inválidas");
        }
        Auth::guard('web')->login($user);
        return $user;
    }
    public function verifyIfIsLogged(): array
    {
        if (!Auth::check()) {
            return [
                'authenticated' => false,
                'message' => 'Usuário não está logado'
            ];
        }

        $user = Auth::user();

        return [
            'authenticated' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ];
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
    
}
