<?php

namespace App\Services;

use App\Repository\ReportRepository;
use App\Repository\SocialProjectRepository;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    public function __construct(
        private ReportRepository $reports,
        private SocialProjectRepository $projects
    ) {}

    public function create(array $data)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \Exception('not_authenticated');
        }

        $projectId = (int)($data['social_project_id'] ?? 0);
        $category = (string)($data['category'] ?? '');
        $reason = (string)($data['reason'] ?? '');

        if (!$projectId || !$category || !$reason) {
            throw new \Exception('invalid_payload');
        }

        $project = $this->projects->findById($projectId);
        if (!$project) {
            throw new \Exception('project_not_found');
        }

        $existing = $this->reports->findOpenByUserAndProject($userId, $projectId);
        if ($existing) {
            throw new \Exception('already_reported');
        }

        return $this->reports->create([
            'social_project_id' => $projectId,
            'reporter_user_id' => $userId,
            'category' => $category,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    public function getMyReports()
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \Exception('not_authenticated');
        }
        return $this->reports->getMyReports($userId);
    }

    public function listPending()
    {
        return $this->reports->getPendingReports();
    }

    public function resolve(int $id, string $resolution, ?string $notes, ?string $projectAction)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \Exception('not_authenticated');
        }

        $report = $this->reports->findById($id);
        if (!$report) {
            throw new \Exception('report_not_found');
        }

        $update = [
            'status' => $resolution,
            'resolution_notes' => $notes,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ];

        $updated = $this->reports->updateById($id, $update);

        if ($projectAction === 'suspend') {
            $this->projects->updateProject($report->social_project_id, ['status' => 'suspended']);
        } elseif ($projectAction === 'remove') {
            $this->projects->updateProject($report->social_project_id, ['status' => 'removed']);
        }

        return $updated;
    }
}
