<?php

namespace App\Repository;

use App\Models\PersonalizedPage;

class PersonalizedPageRepositoryInRD implements PersonalizedPageRepository
{
    public function findBySlug(string $slug)
    {
        return PersonalizedPage::where('url', $slug)->first();
    }
}
