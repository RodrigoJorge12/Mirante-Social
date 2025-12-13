<?php

namespace App\Repository;

use App\Models\SocialProjectRating;
use Illuminate\Support\Facades\DB;

class ProjectRatingRepositoryInRD implements ProjectRatingRepository
{
    public function upsert(array $data)
    {
        $existing = SocialProjectRating::where('social_project_id', $data['social_project_id'])
            ->where('user_id', $data['user_id'])->first();
        if ($existing) {
            $existing->update([
                'rating' => $data['rating'],
                'feedback_text' => $data['feedback_text'] ?? null,
            ]);
            return $existing;
        }
        return SocialProjectRating::create([
            'social_project_id' => $data['social_project_id'],
            'user_id' => $data['user_id'],
            'rating' => $data['rating'],
            'feedback_text' => $data['feedback_text'] ?? null,
        ]);
    }
    public function getByUserAndProject(int $userId, int $projectId)
    {
        return SocialProjectRating::where('social_project_id', $projectId)
            ->where('user_id', $userId)->first();
    }
    public function listByProject(int $projectId, int $limit, int $offset)
    {
        return SocialProjectRating::where('social_project_id', $projectId)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
    public function getSummary(int $projectId)
    {
        $row = SocialProjectRating::select(DB::raw('AVG(rating) as avg'), DB::raw('COUNT(*) as count'))
            ->where('social_project_id', $projectId)
            ->first();
        return [
            'avg' => (float) number_format((float) ($row->avg ?? 0), 2),
            'count' => (int) ($row->count ?? 0),
        ];
    }
}
