<?php

namespace App\Repository;

interface ProjectValidationRepository
{
    public function create(array $data);
    public function findActiveByProjectAndChannel(int $projectId, string $channel);
    public function findByProjectChannelAndCode(int $projectId, string $channel, string $code);
    public function markVerified(int $id);
}
