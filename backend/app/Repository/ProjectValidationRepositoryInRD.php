<?php

namespace App\Repository;

use App\Models\ProjectValidation;

class ProjectValidationRepositoryInRD implements ProjectValidationRepository
{
    public function create(array $data)
    {
        return ProjectValidation::create($data);
    }
    public function findActiveByProjectAndChannel(int $projectId, string $channel)
    {
        return ProjectValidation::where('social_project_id', $projectId)
            ->where('channel', $channel)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
    }
    public function findByProjectChannelAndCode(int $projectId, string $channel, string $code)
    {
        return ProjectValidation::where('social_project_id', $projectId)
            ->where('channel', $channel)
            ->where('code', $code)
            ->first();
    }
    public function markVerified(int $id)
    {
        $pv = ProjectValidation::find($id);
        if ($pv) {
            $pv->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);
        }
        return $pv;
    }
}
