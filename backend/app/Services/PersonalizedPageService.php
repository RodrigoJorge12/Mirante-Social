<?php

namespace App\Services;

use App\Repository\PersonalizedPageRepository;
use App\Services\SocialProjectService;
use Illuminate\Support\Facades\Log;
use Exception;

class PersonalizedPageService
{
    public function __construct(
        private PersonalizedPageRepository $personalizedPageRepository,
        private SocialProjectService $socialProjectService
    ) {}

    /**
     * Retorna os dados da página personalizada completa pronta para renderização.
     *
     * @param string $slug
     * @return array
     */
    public function getPageBySlug(string $slug): array
    {
        // Buscar página personalizada
        $page = $this->personalizedPageRepository->findBySlug($slug);

        // Buscar dados completos do projeto social
        $project = $this->socialProjectService->findById($page->social_project_id);

        // Montar o objeto final para o front
        return [
            'page' => [
                'id' => $page->id,
                'url' => $page->url,
                'template' => $page->template,
                'caption' => $page->caption,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'color' => $project->visual_color,
                'address' => $project->address,
                'district' => $project->district,
                'city' => $project->city,
                'state' => $project->state,
                'zip' => $project->zip_code,
                'phone' => $project->phone,
                'email' => $project->contact_email,
                'site' => $project->website_url,
                'area' => $project->activity_area,
                'targets' => $project->target_audiences,
                'needs' => $project->needs,
            ],
        ];
    }
    public function createPersonalizedPage(int $socialProjectId, string $template): void
    {
        // Gerar slug único baseado no nome do projeto e um sufixo aleatório
        $project = $this->socialProjectService->findById($socialProjectId);
        $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $project->name));
        $uniqueSuffix = substr(md5(uniqid(rand(), true)), 0, 6);
        $slug = "{$baseSlug}-{$uniqueSuffix}";
        $caption = "Conheça o projeto {$project->name}";

        // Montar URL completa
        $url = $slug;
        $template = intval(str_replace("template", "", $template));// Extrai o número do template

        // Criar a página personalizada no repositório
        $this->personalizedPageRepository->create([
            'social_project_id' => $socialProjectId,
            'url' => $url,
            'caption' => $caption,
            'template' => $template,
        ]);
    }
    public function getSlugByProjectId(int $projectId)
    {
        return $this->personalizedPageRepository->findByProjectId($projectId);
    }
}