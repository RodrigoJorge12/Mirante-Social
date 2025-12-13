<?php

namespace App\Services;

use App\Repository\ProjectRatingRepository;
use App\Repository\SocialProjectRepository;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProjectRatingService
{
    public function __construct(
        private ProjectRatingRepository $ratings,
        private SocialProjectRepository $projects
    ) {}

    public function upsert(int $projectId, int $rating, ?string $feedbackText)
    {
        $userId = Auth::id();
        if (!$userId) throw new Exception('not_authenticated');
        if ($rating < 0 || $rating > 5) throw new Exception('invalid_rating');
        $project = $this->projects->findById($projectId);
        if (!$project) throw new Exception('project_not_found');

        $saved = $this->ratings->upsert([
            'social_project_id' => $projectId,
            'user_id' => $userId,
            'rating' => $rating,
            'feedback_text' => $feedbackText,
        ]);

        $summary = $this->ratings->getSummary($projectId);
        $this->projects->updateProject($projectId, [
            'rating_avg' => $summary['avg'],
            'rating_count' => $summary['count'],
        ]);

        return $saved;
    }

    public function getSummary(int $projectId)
    {
        return $this->ratings->getSummary($projectId);
    }
    public function getMine(int $projectId)
    {
        $userId = Auth::id();
        if (!$userId) throw new Exception('not_authenticated');
        return $this->ratings->getByUserAndProject($userId, $projectId);
    }
    public function list(int $projectId, int $page, int $pageSize)
    {
        $offset = max(0, ($page - 1) * $pageSize);
        return $this->ratings->listByProject($projectId, $pageSize, $offset);
    }
}
