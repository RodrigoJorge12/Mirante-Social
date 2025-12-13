<?php

namespace App\Services;

use App\Repository\ProjectValidationRepository;
use App\Repository\SocialProjectRepository;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProjectVerificationService
{
    public function __construct(
        private ProjectValidationRepository $validations,
        private SocialProjectRepository $projects
    ) {}

    public function start(int $projectId, string $channel)
    {
        $project = $this->projects->findById($projectId);
        if (!$project) throw new Exception('project_not_found');
        if ($project->user_id !== Auth::id()) throw new Exception('forbidden');

        $destination = $channel === 'email' ? ($project->contact_email ?? null) : ($project->phone ?? null);
        if (!$destination) throw new Exception('destination_missing');

        $existing = $this->validations->findActiveByProjectAndChannel($projectId, $channel);
        if ($existing) return $existing;

        $code = (string) random_int(100000, 999999);
        $pv = $this->validations->create([
            'social_project_id' => $projectId,
            'channel' => $channel,
            'destination' => $destination,
            'code' => $code,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
            'attempts' => 0,
        ]);

        if ($channel === 'email') {
            $subject = 'Verificação de e-mail do projeto - Mirante Social';
            $message = "Seu código de verificação é {$code}. Válido por 30 minutos.";
            app(ValidationService::class)->sendEmail($destination, $project->name, $message, $subject);
        }

        return $pv;
    }

    public function confirm(int $projectId, string $channel, string $code)
    {
        $project = $this->projects->findById($projectId);
        if (!$project) throw new Exception('project_not_found');
        if ($project->user_id !== Auth::id()) throw new Exception('forbidden');

        $pv = $this->validations->findByProjectChannelAndCode($projectId, $channel, $code);
        if (!$pv) throw new Exception('invalid_code');
        if ($pv->status !== 'pending') throw new Exception('already_used');
        if ($pv->expires_at && $pv->expires_at->isPast()) throw new Exception('expired');

        $this->validations->markVerified($pv->id);

        $emailOk = (bool) $this->validations->findActiveByProjectAndChannel($projectId, 'email');
        $phoneOk = (bool) $this->validations->findActiveByProjectAndChannel($projectId, 'phone');

        $emailVerified = $this->isChannelVerified($projectId, 'email');
        $phoneVerified = $this->isChannelVerified($projectId, 'phone');

        if ($emailVerified && $phoneVerified) {
            $this->projects->updateProject($projectId, [
                'verified' => true,
                'verified_at' => now(),
                'badge' => 'verified',
            ]);
        }

        return $pv;
    }

    private function isChannelVerified(int $projectId, string $channel): bool
    {
        $last = $this->validations->findActiveByProjectAndChannel($projectId, $channel);
        if ($last && $last->status === 'verified') return true;
        // also consider latest verified even if not active
        $pv = $this->validations->findByProjectChannelAndCode($projectId, $channel, $last?->code ?? '');
        return $pv?->status === 'verified';
    }
}
