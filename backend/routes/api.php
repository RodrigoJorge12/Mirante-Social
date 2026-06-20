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
Route::post('sendPasswordResetEmail', [UserController::class, 'sendPasswordResetEmail']);
Route::post('resetPassword', [UserController::class, 'resetPassword']);
Route::get('personalized-page/slug-by-project/{projectId}', [PersonalizedPageController::class, 'getSlugByProjectId']);
Route::middleware(['api'])->group(function () {
    Route::get('/verifyIfIsLogged', [UserController::class, 'verifyIfIsLogged']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/socialProjectsByLoggedUser', [SocialProjectController::class, 'getProjectsByLoggedUser']);
    Route::delete('/socialProject/{id}', [SocialProjectController::class, 'deleteProject']);
    Route::get('/socialProject/{id}', [SocialProjectController::class, 'getProjectById']);
    Route::put('/socialProject/{id}', [SocialProjectController::class, 'updateProject']);
    Route::post('/socialProject', [SocialProjectController::class, 'create']);
    Route::post('/personalized-page/{pageId}/gallery', [PersonalizedPageController::class, 'updateGallery']);
});
// ⚙️ Rotas que precisam gravar e ler sessão — força o middleware 'web'
// Route::middleware(['web'])->group(function () {
//     Route::post('/login', [UserController::class, 'login']);

// });
