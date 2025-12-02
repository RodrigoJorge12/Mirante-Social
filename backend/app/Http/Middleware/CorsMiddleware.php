<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🔥 define aqui antes de tudo
        $headers = [
            'Access-Control-Allow-Origin' => 'https://frontend-cool-wildflower-2471.fly.dev',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Se for preflight (OPTIONS), retorna direto com headers
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders($headers);
        }

        // Caso contrário, processa a requisição normalmente
        $response = $next($request);

        // E adiciona os headers à resposta final
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
