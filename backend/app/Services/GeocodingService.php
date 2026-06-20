<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function geocode(?string $zipCode): ?array
    {
        if (!$zipCode) return null;

        $cep = preg_replace('/\D/', '', $zipCode);
        if (strlen($cep) !== 8) return null;

        // 1. BrasilAPI v2 — tem coords em alguns CEPs, resposta imediata
        $result = $this->fromBrasilApi($cep);
        if ($result) return $result;

        // 2. ViaCEP → endereço limpo → Photon (sem rate limit, sem API key)
        $result = $this->fromViaCepThenPhoton($cep);
        if ($result) return $result;

        return null;
    }

    private function fromBrasilApi(string $cep): ?array
    {
        try {
            $response = Http::timeout(5)
                ->get("https://brasilapi.com.br/api/cep/v2/{$cep}");

            if ($response->successful()) {
                $data = $response->json();
                $lat = $data['location']['coordinates']['latitude'] ?? null;
                $lng = $data['location']['coordinates']['longitude'] ?? null;
                if ($lat && $lng) {
                    return ['lat' => (float) $lat, 'lng' => (float) $lng];
                }
            }
        } catch (\Exception $e) {
            Log::warning('BrasilAPI falhou: ' . $e->getMessage(), ['cep' => $cep]);
        }

        return null;
    }

    private function fromViaCepThenPhoton(string $cep): ?array
    {
        try {
            $response = Http::timeout(5)
                ->get("https://viacep.com.br/ws/{$cep}/json/");

            if (!$response->successful()) return null;

            $data = $response->json();
            if (!empty($data['erro'])) return null;

            $parts = array_filter([
                $data['logradouro'] ?? null,
                $data['bairro'] ?? null,
                $data['localidade'] ?? null,
                $data['uf'] ?? null,
                'Brasil',
            ]);

            if (count($parts) < 2) return null;

            $query = implode(', ', $parts);
            return $this->photon($query);

        } catch (\Exception $e) {
            Log::warning('ViaCEP falhou: ' . $e->getMessage(), ['cep' => $cep]);
        }

        return null;
    }

    private function photon(string $query): ?array
    {
        try {
            // Laravel Http re-codifica a URL — usa curl diretamente para preservar vírgulas
            // urlencode encodes commas as %2C which Photon rejects — replace them back
            $url = 'https://photon.komoot.io/api/?q=' . str_replace('%2C', ',', urlencode($query)) . '&limit=1&lang=default';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'MiranteSocial/1.0');
            $body = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status === 200 && $body) {
                $data = json_decode($body, true);
                $feature = $data['features'][0] ?? null;
                if ($feature) {
                    return [
                        'lat' => (float) $feature['geometry']['coordinates'][1],
                        'lng' => (float) $feature['geometry']['coordinates'][0],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Photon falhou: ' . $e->getMessage(), ['query' => $query]);
        }

        return null;
    }
}
