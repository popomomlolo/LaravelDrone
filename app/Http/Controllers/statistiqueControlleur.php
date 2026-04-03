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
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classes')->get();
        $objectifs = Objectifs::orderBy('libelle_objectifs')->get();
        $apprentis = Apprentis::orderBy('nom')->get();

        return view('statistique', compact('classes', 'objectifs', 'apprentis'));
    }

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

        $apprentis = $query->orderBy('nom')->get();

        $result = $apprentis->map(function ($apprenti) {
            $sessions = $apprenti->sessions->map(function ($session) {
                $objectifs = $session->objectifs->map(function ($obj) {
                    return [
                        'libelle'              => $obj->libelle_objectifs,
                        'reussi'               => (bool) $obj->pivot->reussi,
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

    /**
     * Export CSV — filtré par apprentis et/ou classe
     */
    public function exportCsv()
    {
        $idApprentis = request('id_apprentis');
        $idClasses   = request('id_classes');

        return Excel::download(
            new statistiqueExport($idApprentis, $idClasses),
            'resultats_' . now()->format('Ymd_His') . '.csv',
            \Maatwebsite\Excel\Excel::CSV,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    /**
     * Export PDF — une page par classe
     */
    public function exportPdf()
    {
        $html = statistiqueExport::genererHtmlPdf();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true]);

        return $pdf->download('resultats_classes_' . now()->format('Ymd_His') . '.pdf');
    }
}