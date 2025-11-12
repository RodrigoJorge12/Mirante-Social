<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users', [UserController::class, 'create']);
Route::post('/verifyEmail', [ValidationController::class, 'verifyCode']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/verifyIfIsLogged', [UserController::class, 'verifyIfIsLogged']);
