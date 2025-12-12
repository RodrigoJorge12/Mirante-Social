<?php

namespace App\Repository;

interface ReportRepository
{
    public function create(array $data);
    public function findOpenByUserAndProject(int $userId, int $projectId);
    public function getMyReports(int $userId);
    public function getPendingReports();
    public function findById(int $id);
    public function updateById(int $id, array $data);
}
