<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
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

    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Login do usuário (autentica e cria sessão)
            $user = $this->userService->login(
                $validator->validated()['email'],
                $validator->validated()['password']
            );

            // 🔥 Garante que o cookie da sessão seja enviado
            $response = response()->json([
                'success' => true,
                'message' => 'Usuário logado com sucesso',
                'data' => [
                    'email' => $user->email,
                    'id' => $user->id,
                    'name' => $user->name
                ]
            ], 200);

            // 👉 Aqui Laravel anexa o cookie de sessão automaticamente
            return $response;

        } catch (\Exception $e) {
            Log::error('Error logging user', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyIfIsLogged(): JsonResponse
    {
        try {
            $result = $this->userService->verifyIfIsLogged();

            if (!$result['authenticated']) {
                return response()->json($result, 401);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao verificar login', ['error' => $e->getMessage()]);
            return response()->json([
                'authenticated' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->userService->logout();
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer logout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
?>