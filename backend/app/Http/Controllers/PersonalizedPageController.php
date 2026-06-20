<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PersonalizedPageService;
use App\Models\PersonalizedPage;
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

    /**
     * Atualiza as imagens da galeria de uma página personalizada.
     * Aceita até 6 imagens enviadas como array 'images[]'.
     */
    public function updateGallery(Request $request, int $pageId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'images'   => 'nullable|array|max:6',
                'images.*' => 'file|image|max:4096',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $page = PersonalizedPage::findOrFail($pageId);

            $paths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('galleryImages', 'public');
                }
            }

            $page->gallery_images = json_encode($paths);
            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Galeria atualizada com sucesso',
                'data'    => [
                    'gallery_images' => $paths,
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
