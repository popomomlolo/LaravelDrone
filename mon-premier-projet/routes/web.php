<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PingPongControleur;
use App\Http\Controllers\TestFlashController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckTodo;
use App\Http\Middleware\CheckAuth;



Route::get('/', function () {
    return view('welcome', ['titre' => 'Mon premier exemple.']);
});

Route::get('/ping', function () {
    return view('pong', ['titre' => 'Mon premier exemple.']);
});

Route::get('/pong', function () {
    return view('ping', ['titre' => 'Mon premier exemple.']);
});

Route::get('/flash', function () {
    return view('flash', ['titre' => 'Mon premier exemple.']);
});

Route::get('/inscription', [AuthController::class, 'inscriptionForm'])->name('inscription');
Route::post('/inscription', [AuthController::class, 'inscription']);

Route::get('/connexion', [AuthController::class, 'connexionForm'])->name('login'); // Name login is required by auth middleware defaults
Route::post('/validerconnexion', [AuthController::class, 'connexion']);
Route::get('/deconnexion', [AuthController::class, 'signout']);

Route::get('/ping', [PingPongControleur::class, 'ping']);
Route::get('/pong', [PingPongControleur::class, 'pong']);

Route::get('/todo', [TodoController::class, 'todo'])->middleware('CheckAuth');
Route::get('/todoSupp/{id}', [TodoController::class, 'delTodo'])/*->middleware('CheckAuth')*/;
Route::get('/todoMaj/{id}', [TodoController::class, 'majTodo'])/*->middleware('CheckAuth')*/;

Route::get('/flash', [TestFlashController::class, 'main']);
Route::post('/traitement', [TestFlashController::class, 'traitement']);
Route::post('/traitement2', [TodoController::class, 'addTodo'])->middleware(['auth', CheckTodo::class]);
//Route::post('/traitement2', [TodoController::class, 'delTodo']);