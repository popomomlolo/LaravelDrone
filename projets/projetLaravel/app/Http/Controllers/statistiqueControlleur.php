<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Apprentis;
use App\Models\Objectifs;

class statistiqueControlleur extends Controller
{
    /**
     * Page principale — envoie juste les listes des combobox
     */
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classes')->get();    //recupere les classes pour la combobox
        $objectifs = Objectifs::orderBy('libelle_objectifs')->get(); //recupere les objectifs pour la combobox

        return view('statistique', compact('classes', 'objectifs')); //retourne les 2 val a la vue
    }

    /**
     * Route AJAX — retourne les apprentis filtrés en JSON
     */
    public function filtrer()
    {
        $idClasse   = request('id_classes');  // Récupère les paramètres de filtrage classe
        $idObjectif = request('id_objectifs'); // Récupère les paramètres de filtrage objectif

        // Au moins un filtre doit être renseigné
        if (!$idClasse && !$idObjectif) {
            return response()->json([]); //Envoie le json vide si aucun filtre n'est renseigné
        }

        $query = Apprentis::with([// Requête de base avec les relations nécessaires
            'classes',            //nom de la classe
            'sessions.objectifs', //recupere la table valider a l'aide d'objectif
        ]);

        // Filtre par classe
        if ($idClasse) {
            $query->where('id_classes', $idClasse); //Mets idClasse de la combobox dans le filtre
        }

        // Filtre par objectif : ne garde que les apprentis
        // ayant au moins une session avec cet objectif
        if ($idObjectif) {
            $query->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectifs', $idObjectif); //Mets idObjectif de la combobox dans le filtre
            });
        }

        $apprentis = $query->orderBy('nom')->get(); // Recupere les apprentis par ordre alphabetique

        // Construction du tableau de résultats
        $result = $apprentis->map(function ($apprenti) {
            // Sessions avec leurs objectifs
            $sessions = $apprenti->sessions->map(function ($session) {
                $objectifs = $session->objectifs->map(function ($obj) {
                    return [
                        'libelle' => $obj->libelle_objectifs,
                        'reussi'  => (bool) $obj->pivot->reussi,
                        'quantite_a_atteindre' => $obj->pivot->quantite_a_atteindre,
                        'quantite_realisee'    => $obj->pivot->quantite_realisee,
                    ];
                });

                return [
                    'id'                 => $session->id_sessions,
                    'date'               => \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i'),
                    'type_drone'         => $session->type_drone,
                    'type_environnement' => $session->type_environnement,
                    'conditions_meteo'   => $session->conditions_meteo,
                    'objectifs'          => $objectifs,
                ];
            });

            return [
                'id'          => $apprenti->id_apprentis,
                'nom'         => $apprenti->nom,
                'prenom'      => $apprenti->prenom,
                'classe'      => $apprenti->classes->libelle_classes ?? '—',
                'nb_sessions' => $apprenti->sessions->count(),
                'sessions'    => $sessions,
            ];
        });

        return response()->json($result);
    }
}