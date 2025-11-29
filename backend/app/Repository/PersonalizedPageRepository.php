<?php

namespace App\Repository;

interface PersonalizedPageRepository
{
    public function findBySlug(string $slug);
    public function create(array $data);
    public function findByProjectId(int $projectId);
}