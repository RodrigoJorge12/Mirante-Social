<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Repository\ValidationRepository;

class ValidationService
{
    public function __construct(
        private ValidationRepository $validationRepository
    ) {}

    public function sendEmail($toEmail, $toName, $message, $subject)
    {
        try {
            Mail::raw($message, function ($message) use ($toEmail, $toName, $subject) {
                $message->to($toEmail, $toName)
                    ->subject($subject);
            });
        } catch (Exception $e) {
            Log::error('Error sending email', [
                'error' => $e->getMessage(),
                'email' => $toEmail
            ]);
            throw $e;
        }
    }

    public function createValidation($user)
    {
        $code = random_int(100000, 999999);
        $validationData = [
            'type' => 'email_validation',
            'user_id' => $user->id,
            'code' => $code,
            'time' => now()->addMinutes(30),
            'created_at' => now()
        ];
        Log::info('sending email', [
            'data' => $validationData,
        ]);
        return $this->validationRepository->create($validationData);
    }
}
