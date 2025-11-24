<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\PersonalizedPageController;
use App\Http\Controllers\SocialProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users', [UserController::class, 'create']);
Route::post('/verifyEmail', [ValidationController::class, 'verifyCode']);
Route::get('/personalized-page/{slug}', [PersonalizedPageController::class, 'getPersonalizedPage']);
Route::get('/socialProjects', [SocialProjectController::class, 'getAllProjects']);

// ⚙️ Rotas que precisam gravar e ler sessão — força o middleware 'web'
Route::middleware(['web'])->group(function () {
    Route::post('/api/socialProject', [SocialProjectController::class, 'create']);
    Route::post('/api/login', [UserController::class, 'login']);
    Route::get('/api/verifyIfIsLogged', [UserController::class, 'verifyIfIsLogged']);
    Route::post('/api/logout', [UserController::class, 'logout']);
    Route::get('api/socialProjectsByLoggedUser', [SocialProjectController::class, 'getProjectsByLoggedUser']);
    Route::delete('api/socialProject/{id}', [SocialProjectController::class, 'deleteProject']);
});
