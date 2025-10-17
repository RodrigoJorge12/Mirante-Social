<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

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

Route::get('/users', function (Request $request) {
    $dados = $request->all();
    // return "Cheguei aqui";
    $user = new User();
    // return "Cheguei aqui 1";
    $user->name = $dados['nome'];
    $user->email = $dados['email'];
    $user->password = $dados['senha'];
    // return "Cheguei aqui 2";
    $userRepository = new UserRepositoryInRD();
    // return "Cheguei aqui 3";
    $userController = new UserController($userRepository);
    #return "Cheguei aqui 4";
    return $userController->create($user);
});


