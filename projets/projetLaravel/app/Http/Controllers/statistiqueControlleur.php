<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Apprentis;

class statistiqueControlleur extends Controller
{
    /**
     * Affiche la page statistiques avec la liste des classes.
     * Si une classe est sélectionnée, charge les apprentis de cette classe
     * avec leurs sessions et objectifs validés.
     */
    public function index()
    {
        $classes = Classes::orderBy('libelle_classes')->get();

        $apprentis      = collect();
        $classeSelectee = null;

        if (request()->has('id_classes') && request('id_classes') !== '') {
            $classeSelectee = request('id_classes');

            $apprentis = Apprentis::with([
                'classes',
                'sessions.objectifs', // objectifs via la table valider (pivot)
            ])
            ->where('id_classes', $classeSelectee)
            ->orderBy('nom')
            ->get();
        }

        return view('statistique', compact('classes', 'apprentis', 'classeSelectee'));
    }
}