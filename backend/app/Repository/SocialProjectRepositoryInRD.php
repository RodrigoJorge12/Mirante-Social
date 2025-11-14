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
}