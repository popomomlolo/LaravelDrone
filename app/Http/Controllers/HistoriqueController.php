<?php

namespace App\Http\Controllers;

use App\Models\SessionsDrone as Sessions;

/**
 * @brief Contrôleur affichant l'historique de toutes les sessions de vol.
 *
 * Charge toutes les sessions avec
 * les relations nécessaires à l'affichage (apprenti, classe, objectifs).
 *
 * @package App\Http\Controllers
 */
class HistoriqueController extends Controller
{
    /**
     * @brief Affiche la page d'historique de toutes les sessions.
     *
     * - `apprenti.classe` : l'apprenti et sa classe de rattachement
     * - `objectifs`       : les objectifs évalués avec le pivot (reussi, quantités)
     *
     *
     * @route GET /historique
     *

     */
    public function index()
    {
        $sessions = Sessions::with([
            'apprenti.classe',
            'objectifs',
        ])
            ->orderBy('date_heure', 'desc')
            ->get();
        return view('historique', compact('sessions'));
    }
}