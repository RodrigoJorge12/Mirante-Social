<?php

namespace App\Repository;

use App\Models\Report;

class ReportRepositoryInRD implements ReportRepository
{
    public function create(array $data)
    {
        return Report::create($data);
    }
    public function findOpenByUserAndProject(int $userId, int $projectId)
    {
        return Report::where('reporter_user_id', $userId)
            ->where('social_project_id', $projectId)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();
    }
    public function getMyReports(int $userId)
    {
        return Report::where('reporter_user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }
    public function getPendingReports()
    {
        return Report::where('status', 'pending')
            ->orderBy('id')
            ->get();
    }
    public function findById(int $id)
    {
        return Report::find($id);
    }
    public function updateById(int $id, array $data)
    {
        $report = Report::find($id);
        if ($report) {
            $report->update($data);
            return $report;
        }
        return null;
    }
}
