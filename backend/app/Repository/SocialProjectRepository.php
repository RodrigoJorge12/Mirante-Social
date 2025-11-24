<?php
namespace App\Repository;

interface SocialProjectRepository
{
    public function findById(int $id);
    public function create(array $data);
}