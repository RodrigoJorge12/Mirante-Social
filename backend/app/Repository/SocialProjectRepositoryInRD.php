<?php

namespace App\Repository;

use App\Models\SocialProject;

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
        return SocialProject::where('user_id', $userId)->get();
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
    public function getProjectsNear(float $latitude, float $longitude, int $radiusKm) {
        $haversine = "
            6371 * acos(
                cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(latitude))
            )
        ";

        return SocialProject::query()
            ->select('*')
            ->fromSub(
                SocialProject::selectRaw("
                    social_projects.*,
                    ($haversine) AS distance
                ", [$latitude, $longitude, $latitude]),
                'social_projects'
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->get();
    }
}