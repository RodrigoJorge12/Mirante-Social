<?php
namespace App\Repository;

interface SocialProjectRepository
{
    public function findById(int $id);
    public function create(array $data);
    public function allProjects();
    public function projectsByUserId(int $userId);
    public function deleteProject(int $id);
}