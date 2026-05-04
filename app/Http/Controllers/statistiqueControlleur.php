<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Apprentis;
use App\Models\Objectifs;
use App\Exports\statistiqueExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;

class statistiqueControlleur extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // PAGE PRINCIPALE
    // Charge les listes pour les combobox de la vue
    // ════════════════════════════════════════════════════════════════
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classes')->get();
        $objectifs = Objectifs::orderBy('libelle_objectifs')->get();
        $apprentis = Apprentis::orderBy('nom')->get();

        return view('statistique', compact('classes', 'objectifs', 'apprentis'));
    }

    // ════════════════════════════════════════════════════════════════
    // AJAX — FILTRER
    // Aiguille vers la bonne fonction selon les filtres reçus
    // Un seul return à la fin
    // ════════════════════════════════════════════════════════════════
    public function filtrer(): JsonResponse
    {
        $idClasse   = request('id_classes')   ?: false;
        $idObjectif = request('id_objectifs') ?: false;
/*
si class_
        si objectif
        alors
            filtrerparclasseetobjectif
        sinon
            filtrerparclasse
        finsi
sinon
        si objectif
        alors
            filtrerparobjectif
        finsi
finsi




        */
        // Sélection du cas selon les filtres présents

 if ($idClasse) { 
                if ($idObjectif) {
                    $apprentis = $this->filtrerParClasseEtObjectif($idClasse, $idObjectif);
                } else {
                    $apprentis = $this->filtrerParClasse($idClasse);
                }
                
            } else {
                if ($idObjectif) {
                    $apprentis = $this->filtrerParObjectif($idObjectif);
                } else {
                    $apprentis = collect(); // Cas 1 : aucun filtre
                }
            }

        

        $result = $apprentis->map(fn($a) => $this->formaterApprentis($a));

        return response()->json($result);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT CSV
    // 
    // ════════════════════════════════════════════════════════════════
    public function exportCsv()
    {
        $idClasses   = request('id_classes')   ?: null;
        $idObjectifs = request('id_objectifs') ?: null;

        $export   = new statistiqueExport($idClasses, $idObjectifs);
        $fileName = 'resultats_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];

        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV, $headers);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT PDF
    // abort_if remplace le if + return pour le blocage sans filtre
    // 
    // ════════════════════════════════════════════════════════════════
    public function exportPdf()
    {
        $idClasses   = request('id_classes')   ?: null;
        $idObjectifs = request('id_objectifs') ?: null;

        abort_if(
            !$idClasses && !$idObjectifs,
            400,
            'Sélectionnez au moins un filtre pour exporter en PDF.'
        );

        $html     = statistiqueExport::genererHtmlPdf($idClasses, $idObjectifs);
        $fileName = 'resultats_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true]);

        return $pdf->download($fileName);
    }



    /**
     * Cas 2 — filtre par classe uniquement
     */
    private function filtrerParClasse( $idClasse)
    {
        return Apprentis::with(['classes', 'sessions.objectifs'])
            ->where('id_classes', $idClasse)
            ->orderBy('nom')
            ->get();
    }

    /**
     * Cas 3 — filtre par objectif uniquement
     * Retourne les apprentis ayant au moins une session avec cet objectif
     */
    private function filtrerParObjectif( $idObjectif)
    {
        return Apprentis::with(['classes', 'sessions.objectifs'])
            ->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectifs', $idObjectif);
            })
            ->orderBy('nom')
            ->get();
    }

    /**
     * Cas 4 — filtre par classe ET par objectif
     */
    private function filtrerParClasseEtObjectif( $idClasse,  $idObjectif)
    {
        return Apprentis::with(['classes', 'sessions.objectifs'])
            ->where('id_classes', $idClasse)
            ->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectifs', $idObjectif);
            })
            ->orderBy('nom')
            ->get();
    }

    /**
     * Formate un apprenti en tableau pour la réponse JSON
     * Appelée par filtrer() pour normaliser chaque ligne
     */
    private function formaterApprentis($apprenti): array
    {
        $sessions = $apprenti->sessions->map(fn($session) => [
            'id'                 => $session->id_sessions,
            'date'               => \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i'),
            'type_drone'         => $session->type_drone,
            'type_environnement' => $session->type_environnement,
            'conditions_meteo'   => $session->conditions_meteo,
            'objectifs'          => $session->objectifs->map(fn($obj) => [
                'libelle'              => $obj->libelle_objectifs,
                'reussi'               => (bool) $obj->pivot->reussi,
                'quantite_a_atteindre' => $obj->pivot->quantite_a_atteindre,
                'quantite_realisee'    => $obj->pivot->quantite_realisee,
            ]),
        ]);

        return [
            'id'          => $apprenti->id_apprentis,
            'nom'         => $apprenti->nom,
            'prenom'      => $apprenti->prenom,
            'classe'      => $apprenti->classes->libelle_classes ?? '—',
            'nb_sessions' => $apprenti->sessions->count(),
            'sessions'    => $sessions,
        ];
    }
}