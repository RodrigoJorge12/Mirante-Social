<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\PersonalizedPageController;
use App\Http\Controllers\SocialProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
        return response()->json([
        'success' => true,
        'message' => 'API Mirante Social rodando 🚀'
    ]);
});

// ⚙️ Rotas que precisam gravar e ler sessão — força o middleware 'web'
Route::middleware(['web'])->group(function () {
    Route::post('/api/login', [UserController::class, 'login']);
});