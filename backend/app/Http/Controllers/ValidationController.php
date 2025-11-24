<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    private ValidationService $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Verificando um codigo e um email.
     */
    public function verifyCode(Request $request): JsonResponse
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|min:6|max:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // verifing validation using service
            $validation = $this->validationService->verifyCode(
                $validator->validated()['email'],
                $validator->validated()['code']
            );

            return response()->json([
                'success' => true,
                'message' => 'Email verificado com sucesso',
                'data' => [
                    'email' => $validation->email
                ]
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
