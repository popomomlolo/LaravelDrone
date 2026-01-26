<?php

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

Route::get('/signin', function () {
    return view('signin', ['titre' => 'Mon premier exemple.']);
});
Route::post('/signin', [AuthController::class, 'signin']);

Route::get('/signup', function () {
    return view('signup', ['titre' => 'Mon premier exemple.']);
});
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('/ping', [PingPongControleur::class, 'ping']);
Route::get('/pong', [PingPongControleur::class, 'pong']);
Route::get('/todo', [TodoController::class, 'todo']);
Route::get('/todoSupp/{id}', [TodoController::class, 'delTodo']);
Route::get('/todoMaj/{id}', [TodoController::class, 'majTodo']);

Route::get('/flash', [TestFlashController::class, 'main']);
Route::post('/traitement', [TestFlashController::class, 'traitement']);
Route::post('/traitement2', [TodoController::class, 'addTodo'])->middleware(CheckTodo::class);
//Route::post('/traitement2', [TodoController::class, 'delTodo']);