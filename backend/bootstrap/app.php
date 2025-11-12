<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CorsMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Mantém as rotas como você quis
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ❌ Remove o CORS padrão (que envia '*')
        $middleware->remove(\Illuminate\Http\Middleware\HandleCors::class);

        // ✅ Adiciona o seu CORS personalizado
        $middleware->append(CorsMiddleware::class);

        // ✅ ESSENCIAL: garante o funcionamento de sessões e autenticação
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
