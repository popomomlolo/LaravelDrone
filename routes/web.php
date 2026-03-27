<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprentisController;
use App\Http\Controllers\historiqueControlleur;
use App\Http\Controllers\statistiqueControlleur;
use App\Http\Controllers\AuthController;

// Redirection racine vers historique
//Route::get('/', fn() => redirect()->route('historique.index'));

Route::get('/', fn() => view('acceuil'));

// ── Historique ──
Route::get('/historique', [historiqueControlleur::class, 'index'])->name('historique.index');

// ── Statistiques ──
Route::get('/statistique', [statistiqueControlleur::class, 'index'])->name('statistique.index');

Route::get('/signin', [AuthController::class, 'signinForm'])->name('login');

Route::get('/apprentis', [ApprentisController::class, 'showApprentis'])->name('apprentis.index');
Route::post('/apprentis/supprimer', [ApprentisController::class, 'supprimer']);
Route::post('/apprentis/modifier', [ApprentisController::class, 'modifierForm']);
Route::post('/apprentis/update', [ApprentisController::class, 'update']);
Route::post('/apprentis/ajouter', [ApprentisController::class, 'ajouter']);
Route::post('/apprentis/import-csv', [ApprentisController::class, 'importCsv']);

Route::get('/api/apprentis', [ApprentisController::class, 'getApprentis']);

Route::get('/signup', [AuthController::class, 'signupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('/signin', [AuthController::class, 'signinForm'])->name('login'); // Name login is required by auth middleware defaults
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/signout', [AuthController::class, 'signout']);
