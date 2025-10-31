<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a new user.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user using service
            $user = $this->userService->createUser($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating user', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
?>