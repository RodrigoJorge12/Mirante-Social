<?php

namespace App\Repository;

interface PersonalizedPageRepository
{
    public function findBySlug(string $slug);
}