<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PingPongControleur;
use App\Http\Controllers\TestFlashController;
use App\Http\Controllers\TodoControlleur;
use App\Http\Controllers\AuthentificationControleur;


Route::get('/', function () {
    return view('welcome', ['titre' => 'Mon premier exemple.']);
});

Route::get('/ping', function () {
    return view('pong', ['titre' => 'Mon premier exemple.']);
});

Route::get('/pong', function () {
    return view('ping', ['titre' => 'Mon premier exemple.']);
});
Route::get('/todo', function () {
    return view('todo', ['titre' => 'Mon premier exemple.']);
});

Route::get('/ping', [PingPongControleur::class, 'ping']);
Route::get('/pong', [PingPongControleur::class, 'pong'])->name('pong');
Route::get('/flash', [TestFlashController::class, 'main']);
Route::post('/traitement', [TestFlashController::class, 'traitement']);
Route::post('/traitement2', [PingPongControleur::class, 'addtodo'])->middleware(\App\Http\Middleware\CheckTodo::class);
Route::post('/todomaj/{id}', [TodoControlleur::class, 'todomaj'])->name('todomaj');
Route::post('/todosup/{id}', [TodoControlleur::class, 'todosup'])->name('todosup');

Route::get('/login', [AuthentificationControleur::class, 'login']);
Route::post('/traitementLogin', [AuthentificationControleur::class, 'traitementLogin']);
Route::get('/register', [AuthentificationControleur::class, 'register']);
Route::post('/traitementRegister', [AuthentificationControleur::class, 'traitementRegister']);
