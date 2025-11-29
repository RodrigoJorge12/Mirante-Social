<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PersonalizedPageService;
use Illuminate\Support\Facades\Validator;

class PersonalizedPageController extends Controller
{
    private PersonalizedPageService $personalizedPageService;

    public function __construct(PersonalizedPageService $personalizedPageService)
    {
        $this->personalizedPageService = $personalizedPageService;
    }

    /**
     * obtendo dados da pagina personalizada.
     */
    public function getPersonalizedPage(string $slug): JsonResponse
    {
        try {
            $pageData = $this->personalizedPageService->getPageBySlug($slug);
            if (!$pageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Página personalizada não encontrada'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Página personalizada obtida com sucesso',
                'data' => $pageData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
    public function getSlugByProjectId(int $projectId): JsonResponse
    {
        try {
            $page = $this->personalizedPageService->getSlugByProjectId($projectId);
            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Página personalizada não encontrada para o projeto fornecido'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Slug da página personalizada obtido com sucesso',
                'data' => [
                    'slug' => $page->url
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
