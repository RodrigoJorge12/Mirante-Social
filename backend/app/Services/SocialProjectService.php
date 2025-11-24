<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialProject;
use App\Repository\SocialProjectRepository;
use Exception;

class SocialProjectService
{
    private SocialProjectRepository $repository;

    public function __construct(SocialProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna um projeto social pelo ID.
     *
     * @param int $id
     * @return SocialProject
     */
    public function findById(int $id): SocialProject
    {
        $project = $this->repository->findById($id);

        return $project;
    }
    public function createSocialProject(array $data, $image = null)
    {
        // Tratamento da imagem
        if ($image) {
            $path = $image->store('projectsImages', 'public');
            $data['image_path'] = $path;
        }



        // Normalizar nomes de campos para o Banco
        $normalized = [
            'user_id'          => auth()->id(),
            'name'             => $data['name'],
            'description'      => $data['description'],
            'address'          => $data['address'] ?? null,
            'district'         => $data['district'] ?? null,
            'city'             => $data['city'] ?? null,
            'state'            => $data['state'] ?? null,
            'zip_code'         => $data['zipCode'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'website_url'      => $data['websiteUrl'] ?? null,
            'visual_color'     => $data['visualColor'] ?? null,
            'activity_area'    => $data['activityArea'] ?? null,
            'target_audiences' => json_encode($data['targetAudiences'] ?? []),
            'image_path'       => $data['image_path'] ?? null,
        ];

        // Envia para o repositório
        return $this->repository->create($normalized);
    }
    public function getAllProjects()
    {
        return $this->repository->allProjects();
    }
    public function getProjectsByUserId()
    {
        $userId = auth()->id();
        return $this->repository->projectsByUserId($userId);
    }
    public function deleteProject(int $id)
    {
        $project = $this->repository->findById($id);

        if (!$project) {
            throw new Exception("Projeto não encontrado.");
        }

        // Verifica se o projeto pertence ao usuário autenticado
        if ($project->user_id !== auth()->id()) {
            throw new Exception("Ação não autorizada.");
        }

        // Deleta o projeto
        $deleted = $this->repository->deleteProject($id);

        return true;
    }
}