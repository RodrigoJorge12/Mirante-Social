<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::options('/{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users', [UserController::class, 'create']);


