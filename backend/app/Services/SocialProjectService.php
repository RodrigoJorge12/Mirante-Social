<?php

namespace App\Services;

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
}