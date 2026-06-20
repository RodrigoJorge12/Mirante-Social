<?php

namespace App\Repository;

use App\Models\SocialProject;
use App\Models\PersonalizedPage;

class SocialProjectRepositoryInRD implements SocialProjectRepository
{
    /**
     * Retorna um projeto social pelo ID.
     */
    public function findById(int $id)
    {
        return SocialProject::find($id);
    }

    public function create(array $data)
    {   
        return SocialProject::create($data);
    }
    public function allProjects()
    {
        return SocialProject::all();
    }
    public function projectsByUserId(int $userId)
    {
        $projects = SocialProject::where('user_id', $userId)->get();
        $pageIds = PersonalizedPage::whereIn('social_project_id', $projects->pluck('id'))
            ->pluck('social_project_id')
            ->flip();
        return $projects->map(function ($p) use ($pageIds) {
            $p->has_personalized_page = $pageIds->has($p->id);
            return $p;
        });
    }
    public function deleteProject(int $id)
    {
        $project = SocialProject::find($id);
        if ($project) {
            return $project->delete();
        }
        return false;
    }
    public function updateProject(int $id, array $data)
    {
        $project = SocialProject::find($id);
        if ($project) {
            $project->update($data);
            return $project;
        }
        return null;
    }
}