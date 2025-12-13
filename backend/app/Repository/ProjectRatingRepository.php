<?php

namespace App\Repository;

interface ProjectRatingRepository
{
    public function upsert(array $data);
    public function getByUserAndProject(int $userId, int $projectId);
    public function listByProject(int $projectId, int $limit, int $offset);
    public function getSummary(int $projectId);
}
