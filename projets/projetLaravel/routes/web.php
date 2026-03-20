<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apprentisControlleur;
use App\Http\Controllers\historiqueControlleur;
use App\Http\Controllers\statistiqueControlleur;

// Redirection racine vers historique
//Route::get('/', fn() => redirect()->route('historique.index'));

Route::get('/', fn() => view('acceuil'));

// ── Historique ──
Route::get('/historique', [historiqueControlleur::class, 'index'])->name('historique.index');

// ── Statistiques ──
Route::get('/statistique', [statistiqueControlleur::class, 'index'])->name('statistique.index');

Route::get('/signin', [AuthController::class, 'signinForm'])->name('login');