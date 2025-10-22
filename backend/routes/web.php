<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

use App\Models\User;
use App\Repository\UserRepositoryInRD;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::options('/{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users', function (Request $request) {
    $dados = $request->all();
    $user = new User();
    $user->name = $dados['nome'];
    $user->email = $dados['email'];
    $user->password = $dados['senha'];
    $userRepository = new UserRepositoryInRD();
    $userController = new UserController($userRepository);
    return $userController->create($user);
});


