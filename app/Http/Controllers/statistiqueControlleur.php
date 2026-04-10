<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Apprentis;
use App\Models\Objectifs;
use App\Exports\statistiqueExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class statistiqueControlleur extends Controller
{
    // ================================================================
    // PAGE PRINCIPALE - Charge les 2 combo box (classes + objectifs)
    // ================================================================
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classes')->get();
        $objectifs = Objectifs::orderBy('libelle_objectifs')->get();
        //$apprentis = Apprentis::orderBy('nom')->get();

        return view('statistique', compact('classes', 'objectifs', /*'apprentis'*/));
    }

    // ================================================================
    // AJAX — retourne les apprentis filtrés en JSON
    // ================================================================
    public function filtrer()
    {
        $idClasse   = request('id_classes');
        $idObjectif = request('id_objectifs');

        if (!$idClasse && !$idObjectif) {
            return response()->json([]);
        }

        $query = Apprentis::with(['classes', 'sessions.objectifs']);

        if ($idClasse) {
            $query->where('id_classes', $idClasse);
        }

        if ($idObjectif) {
            $query->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectifs', $idObjectif);
            });
        }

        $result = $query->orderBy('nom')->get()->map(function ($apprenti) {

            $sessions = $apprenti->sessions->map(function ($session) {
                return [
                    'id'                 => $session->id_sessions,
                    'date'               => \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i'),
                    'type_drone'         => $session->type_drone,
                    'type_environnement' => $session->type_environnement,
                    'conditions_meteo'   => $session->conditions_meteo,
                    'objectifs'          => $session->objectifs->map(function ($obj) {
                        return [
                            'libelle'              => $obj->libelle_objectifs,
                            'reussi'               => (bool) $obj->pivot->reussi,
                            'quantite_a_atteindre' => $obj->pivot->quantite_a_atteindre,
                            'quantite_realisee'    => $obj->pivot->quantite_realisee,
                        ];
                    }),
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

    // ================================================================
    // EXPORT CSV — passe les filtres à statistiqueExport
    //
    // Cas 1 : aucun filtre     → toutes les classes
    // Cas 2 : classe seule     → classe filtrée
    // Cas 3 : objectif seul    → personnes ayant fait cet objectif
    // Cas 4 : classe+objectif  → classe filtrée + objectif filtré
    // ================================================================
    public function exportCsv()
    {
        $idClasses   = request('id_classes')   ?: null;
        $idObjectifs = request('id_objectifs') ?: null;

        return Excel::download(
            new statistiqueExport($idClasses, $idObjectifs),
            'resultats_' . now()->format('Ymd_His') . '.csv',
            \Maatwebsite\Excel\Excel::CSV,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    // ================================================================
    // EXPORT PDF — désactivé côté JS si aucun filtre (cas 1)
    //
    // Cas 2 : classe seule     → 1 page pour la classe
    // Cas 3 : objectif seul    → toutes les classes, objectif mis en avant
    // Cas 4 : classe+objectif  → 1 page pour la classe + objectif mis en avant
    // ================================================================
    public function exportPdf()
    {
        $idClasses   = request('id_classes')   ?: null;
        $idObjectifs = request('id_objectifs') ?: null;

        // Sécurité : bloque le PDF si aucun filtre (cas 1)
        if (!$idClasses && !$idObjectifs) {
            abort(400, 'Sélectionnez au moins un filtre pour exporter en PDF.');
        }

        $html = statistiqueExport::genererHtmlPdf($idClasses, $idObjectifs);

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true]);

        return $pdf->download('resultats_' . now()->format('Ymd_His') . '.pdf');
    }
}