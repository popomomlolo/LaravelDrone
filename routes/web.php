<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprentisController;
use App\Http\Controllers\historiqueControlleur;
use App\Http\Controllers\statistiqueControlleur;
use App\Http\Controllers\AuthController;

// ── Auth (public) ──
Route::get('/signin', [AuthController::class, 'signinForm'])->name('login');
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/signup', [AuthController::class, 'signupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

// ── Routes protégées (login obligatoire) ──
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('historique.index'));

    // ── Historique ──
    Route::get('/historique', [historiqueControlleur::class, 'index'])->name('historique.index');

    // ── Statistiques ──
//    Route::get('/statistique',         [statistiqueControlleur::class, 'index'])->name('statistique.index');
//    Route::get('/statistique/filtrer', [statistiqueControlleur::class, 'filtrer'])->name('statistique.filtrer');
//    Route::get('/statistique/csv',     [statistiqueControlleur::class, 'exportCsv'])->name('statistique.csv');
//    Route::get('/statistique/pdf',     [statistiqueControlleur::class, 'exportPdf'])->name('statistique.pdf');

    // ── Apprentis ──
    Route::get('/apprentis',                [ApprentisController::class, 'showApprentis'])->name('apprentis.index');
    Route::post('/apprentis/supprimer',     [ApprentisController::class, 'supprimer']);
    Route::post('/apprentis/modifier',      [ApprentisController::class, 'modifierForm']);
    Route::post('/apprentis/update',        [ApprentisController::class, 'update']);
    Route::post('/apprentis/ajouter',       [ApprentisController::class, 'ajouter']);
    Route::post('/apprentis/import-csv',    [ApprentisController::class, 'importCsv']);
    Route::get('/api/apprentis',            [ApprentisController::class, 'getApprentis']);

    // ── Déconnexion ──
    Route::post('/signout', [AuthController::class, 'signout'])->name('signout');
});
    Route::get('/statistique',         [statistiqueControlleur::class, 'index'])->name('statistique.index');
    Route::get('/statistique/filtrer', [statistiqueControlleur::class, 'filtrer'])->name('statistique.filtrer');
    Route::get('/statistique/csv',     [statistiqueControlleur::class, 'exportCsv'])->name('statistique.csv');
    Route::get('/statistique/pdf',     [statistiqueControlleur::class, 'exportPdf'])->name('statistique.pdf');

        Route::get('/testStat',         [statistiqueControlleur::class, 'index'])->name('testStat.index');
    Route::get('/testStat/filtrer', [statistiqueControlleur::class, 'filtrer'])->name('testStat.filtrer');
    Route::get('/testStat/csv',     [statistiqueControlleur::class, 'exportCsv'])->name('testStat.csv');
    Route::get('/testStat/pdf',     [statistiqueControlleur::class, 'exportPdf'])->name('testStat.pdf');
