<?php

namespace App\Http\Controllers;

use App\Models\Sessions;

class historiqueControlleur extends Controller
{
    /**
     * Affiche l'historique de toutes les sessions
     * avec l'apprenti, sa classe et ses objectifs validés.
     */
    public function index()
    {
        $sessions = Sessions::with([
            'apprenti.classe', // Apprenti + sa Classe
            'objectifs',       // Objectifs avec le pivot (reussi, quantites)
        ])
            ->orderBy('date_heure', 'desc')
            ->get();

        return view('historique', compact('sessions'));
    }
}
