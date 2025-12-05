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

// Route::post('/api/users', [UserController::class, 'create']);
// Route::post('/api/verifyEmail', [ValidationController::class, 'verifyCode']);
// Route::get('/api/personalized-page/{slug}', [PersonalizedPageController::class, 'getPersonalizedPage']);
// Route::get('/api/socialProjects', [SocialProjectController::class, 'getAllProjects']);
// Route::post('/api/sendPasswordResetEmail', [UserController::class, 'sendPasswordResetEmail']);
// Route::post('/api/resetPassword', [UserController::class, 'resetPassword']);
// Route::get('/api/personalized-page/slug-by-project/{projectId}', [PersonalizedPageController::class, 'getSlugByProjectId']);
// ⚙️ Rotas que precisam gravar e ler sessão — força o middleware 'web'
Route::middleware(['web'])->group(function () {
    // Route::post('/api/socialProject', [SocialProjectController::class, 'create']);
    Route::post('/api/login', [UserController::class, 'login']);
    // Route::get('/api/verifyIfIsLogged', [UserController::class, 'verifyIfIsLogged']);
    // Route::post('/api/logout', [UserController::class, 'logout']);
    // Route::get('/api/socialProjectsByLoggedUser', [SocialProjectController::class, 'getProjectsByLoggedUser']);
    // Route::delete('api/socialProject/{id}', [SocialProjectController::class, 'deleteProject']);
    // Route::get('/api/socialProject/{id}', [SocialProjectController::class, 'getProjectById']);
    // Route::put('/api/socialProject/{id}', [SocialProjectController::class, 'updateProject']);
});
