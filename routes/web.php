<?php

use App\Http\Controllers\ApprentisController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PingPongControleur;
use App\Http\Controllers\TestFlashController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckTodo;


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

//Route::get('/*', [ApprentisController::class, 'show*']);

Route::get('/apprenti-form', [ApprentisController::class, 'showForm']);
Route::post('/apprentis', [ApprentisController::class, 'submitForm']);

Route::get('/apprentis', [ApprentisController::class, 'showApprentis']);
Route::post('/apprentis', [ApprentisController::class, 'submitForm']);

Route::get('/api/apprentis', [ApprentisController::class, 'getApprentis']);

Route::get('/signup', [AuthController::class, 'signupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('/signin', [AuthController::class, 'signinForm'])->name('login'); // Name login is required by auth middleware defaults
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/signout', [AuthController::class, 'signout']);

Route::get('/ping', [PingPongControleur::class, 'ping']);
Route::get('/pong', [PingPongControleur::class, 'pong']);

Route::get('/todo', [TodoController::class, 'todo'])->middleware('auth');
Route::get('/todoSupp/{id}', [TodoController::class, 'delTodo'])->middleware('auth');
Route::get('/todoMaj/{id}', [TodoController::class, 'majTodo'])->middleware('auth');

Route::get('/flash', [TestFlashController::class, 'main']);
Route::post('/traitement', [TestFlashController::class, 'traitement']);
Route::post('/traitement2', [TodoController::class, 'addTodo'])->middleware(['auth', CheckTodo::class]);
//Route::post('/traitement2', [TodoController::class, 'delTodo']);
