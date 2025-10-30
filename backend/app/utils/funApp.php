<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

function sendMailerooEmail(string $toEmail, string $toName, string $subject, string $plainBody): bool
{   
    $htmlBody = '
        <html>
        <body>
            <h1> Teste email mirante social <h1>
            <script type="text/javascript">
            </script>

        </body>
        </html>
    ';
    $apiKey = '8779e33bda5a168f7156fc158d122af7bbdc3366d779326d49846e128739920b';
    $apiUrl = 'https://smtp.maileroo.com/api/v2/emails';
    $fromAddress = 'validarEmail.mirantesocial.com';
    $fromName = "Mirante Social";

    $payload = [
        'from' => [
            'address'      => $fromAddress,
            'display_name' => $fromName,
        ],
        'to' => [
            [
                'address'      => $toEmail,
                'display_name' => $toName,
            ]
        ],
        'subject' => $subject,
        'html'    => $htmlBody,
        'plain'   => $plainBody,
    ];

    try {
        $response = Http::withToken($apiKey)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($apiUrl, $payload);

        if ($response->successful()) {
            // opcional: Log::info('Maileroo response', $response->json());
            return true;
        }

        // Loga erro pra debugar
        Log::error('Maileroo error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;

    } catch (\Exception $e) {
        Log::error('Maileroo request failed', ['exception' => $e->getMessage()]);
        return false;
    }
}

?>