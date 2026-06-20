<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SocialProject;
use App\Services\GeocodingService;

class GeocodeExistingProjects extends Command
{
    protected $signature = 'projects:geocode';
    protected $description = 'Geocodifica projetos existentes que não têm lat/lng';

    public function handle()
    {
        $projects = SocialProject::whereNull('lat')->orWhereNull('lng')->get();

        if ($projects->isEmpty()) {
            $this->info('Todos os projetos já têm coordenadas.');
            return;
        }

        $this->info("Geocodificando {$projects->count()} projeto(s)...");
        $geocoder = new GeocodingService();
        $success = 0;
        $failed = 0;

        foreach ($projects as $project) {
            if (!$project->zip_code) {
                $this->warn("  - [{$project->id}] {$project->name} → sem CEP cadastrado");
                $failed++;
                continue;
            }

            $coords = $geocoder->geocode($project->zip_code);

            if ($coords) {
                $project->update(['lat' => $coords['lat'], 'lng' => $coords['lng']]);
                $this->line("  ✓ [{$project->id}] {$project->name} ({$project->zip_code}) → {$coords['lat']}, {$coords['lng']}");
                $success++;
            } else {
                $this->warn("  ✗ [{$project->id}] {$project->name} ({$project->zip_code}) → CEP não encontrado na BrasilAPI");
                $failed++;
            }
        }

        $this->info("Concluído: {$success} geocodificados, {$failed} sem resultado.");
    }
}
