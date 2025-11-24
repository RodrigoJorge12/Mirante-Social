<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\SocialProjectService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class SocialProjectController extends Controller
{
    private SocialProjectService $userService;

    public function __construct(SocialProjectService $socialProjectService)
    {
        $this->socialProjectService = $socialProjectService;
    }

    /**
     * Create a new user.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Validate request data
            $input = $request->all();

            // Converte JSON string para array real
            if (!empty($input['targetAudiences']) && is_string($input['targetAudiences'])) {
                $input['targetAudiences'] = json_decode($input['targetAudiences'], true);
            }

            $validator = Validator::make($input, [
                'name'             => 'required|string|max:255',
                'description'      => 'required|string',
                'address'          => 'nullable|string|max:255',
                'district'         => 'nullable|string|max:255',
                'city'             => 'nullable|string|max:255',
                'state'            => 'nullable|string|size:2',
                'zipCode'          => 'nullable|string|max:20',
                'phone'            => 'nullable|string|max:30',
                'websiteUrl'       => 'nullable|string|max:255',
                'visualColor'      => 'nullable|string|max:20',
                'activityArea'     => 'nullable|string|max:255',
                'targetAudiences'  => 'nullable|array',
                'targetAudiences.*'=> 'string',

                'image'            => 'nullable|file|image|max:4096', // 4MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user using service
            $socialProject = $this->socialProjectService->createSocialProject($input, $request->file('image'));

            return response()->json([
                'success' => true,
                'message' => 'Projeto Social criado com sucesso',
                'data' => [
                    'name' => $socialProject->name,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
    public function getAllProjects(): JsonResponse
    {
        try {
            $projects = $this->socialProjectService->getAllProjects();

            return response()->json([
                'success' => true,
                'message' => 'Projetos sociais obtidos com sucesso',
                'data' => $projects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
    public function getProjectsByLoggedUser(): JsonResponse
    {
        try {
            $projects = $this->socialProjectService->getProjectsByUserId();

            return response()->json([
                'success' => true,
                'message' => 'Projetos sociais do usuário obtidos com sucesso',
                'data' => $projects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
    public function deleteProject(int $id): JsonResponse
    {
        try {
            $this->socialProjectService->deleteProject($id);

            return response()->json([
                'success' => true,
                'message' => 'Projeto social deletado com sucesso'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
?>