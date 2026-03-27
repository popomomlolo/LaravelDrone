<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apprentisControlleur;
use App\Http\Controllers\historiqueControlleur;
use App\Http\Controllers\statistiqueControlleur;

Route::get('/', fn() => redirect()->route('historique.index'));

// Historique
Route::get('/historique', [historiqueControlleur::class, 'index'])->name('historique.index');

// Apprentis
Route::get('/apprentis',      [apprentisControlleur::class, 'index'])->name('apprentis.index');
Route::get('/apprentis/{id}', [apprentisControlleur::class, 'show'])->name('apprentis.show');

// Statistiques
Route::get('/statistique',         [statistiqueControlleur::class, 'index'])->name('statistique.index');
Route::get('/statistique/filtrer', [statistiqueControlleur::class, 'filtrer'])->name('statistique.filtrer');