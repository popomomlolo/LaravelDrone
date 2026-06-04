<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Objectifs;
use App\Models\Apprentis;
use App\Exports\statistiqueExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class statistiqueControlleur extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // PAGE PRINCIPALE
    // ════════════════════════════════════════════════════════════════
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classe')->get();
        $objectifs = Objectifs::orderBy('libelle_objectif')->get();
        return view('statistique', compact('classes', 'objectifs'));
    }

    // ════════════════════════════════════════════════════════════════
    // AJAX — LISTE LÉGÈRE DES APPRENTIS SELON LES FILTRES
    // ════════════════════════════════════════════════════════════════
    public function filtrer(): JsonResponse
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;

        $query = Apprentis::select('apprentis.id_apprenti', 'apprentis.nom', 'apprentis.prenom', 'apprentis.id_classe')
            ->with('classe:id_classe,libelle_classe')
            ->withCount('sessions');

        if ($idClasse) {
            $query->where('id_classe', $idClasse);
        }

        if ($idObjectif) {
            $query->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectif', $idObjectif);
            });
        }

        $apprentis = $query->orderBy('nom')->get();

        $result = $apprentis->map(fn($a) => [
            'id'          => $a->id_apprenti,
            'nom'         => $a->nom,
            'prenom'      => $a->prenom,
            'classe'      => $a->classe->libelle_classe ?? '—',
            'nb_sessions' => $a->sessions_count,
        ]);

        return response()->json($result);
    }

    // ════════════════════════════════════════════════════════════════
    // AJAX — DÉTAIL D'UN SEUL APPRENTI (au clic sur la ligne)
    // ════════════════════════════════════════════════════════════════
    public function detail(int $id): JsonResponse
    {
        $apprenti = Apprentis::with([
            'classe:id_classe,libelle_classe',
            'sessions.objectifs',
            'sessions.meteo',
        ])->findOrFail($id);

        $sessions = $apprenti->sessions->map(fn($session) => [
            'id'                 => $session->id_session,
            'date'               => \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i'),
            'type_drone'         => (bool) $session->type_drone,
            'type_environnement' => (bool) $session->type_environnement,
            'duree_max'          => $session->duree_max,
            'jour'               => $session->meteo ? (bool) $session->meteo->jour      : null,
            'ciel'               => $session->meteo ?        $session->meteo->ciel       : null,
            'vent_norme'         => $session->meteo ?        $session->meteo->vent_norme : null,
            'objectifs'          => $session->objectifs->map(fn($obj) => [
                'libelle'              => $obj->libelle_objectif,
                'reussi'               => (bool) $obj->pivot->reussi,
                'quantite_a_atteindre' => $obj->pivot->quantite_a_atteindre,
                'quantite_realisee'    => $obj->pivot->quantite_realisee,
            ]),
        ]);

        return response()->json([
            'id'       => $apprenti->id_apprenti,
            'nom'      => $apprenti->nom,
            'prenom'   => $apprenti->prenom,
            'classe'   => $apprenti->classe->libelle_classe ?? '—',
            'sessions' => $sessions,
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // AJAX — DONNÉES GRAPHIQUE
    // ════════════════════════════════════════════════════════════════
    public function chartData(): JsonResponse
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;

        // Total apprentis concernés par les filtres
        $queryTotal = Apprentis::query();
        if ($idClasse)   $queryTotal->where('id_classe', $idClasse);
        if ($idObjectif) $queryTotal->whereHas('sessions.objectifs', fn($q) => $q->where('objectifs.id_objectif', $idObjectif));
        $total = $queryTotal->count();

        // Agrégation : par objectif → nb d'apprentis ayant réussi / échoué
        $query = DB::table('validations')
            ->join('objectifs',      'objectifs.id_objectif',   '=', 'validations.id_objectif')
            ->join('sessions_drone', 'sessions_drone.id_session','=', 'validations.id_session')
            ->join('apprentis',      'apprentis.id_apprenti',   '=', 'sessions_drone.id_apprenti')
            ->select(
                'objectifs.libelle_objectif',
                'validations.reussi',
                DB::raw('COUNT(DISTINCT apprentis.id_apprenti) as nb')
            )
            ->groupBy('objectifs.id_objectif', 'objectifs.libelle_objectif', 'validations.reussi');

        if ($idClasse)   $query->where('apprentis.id_classe', $idClasse);
        if ($idObjectif) $query->where('validations.id_objectif', $idObjectif);

        $rows = $query->get();

        // Restructuration : libelle → { reussi, echoue }
        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row->libelle_objectif])) {
                $map[$row->libelle_objectif] = ['reussi' => 0, 'echoue' => 0];
            }
            if ($row->reussi) {
                $map[$row->libelle_objectif]['reussi'] = (int) $row->nb;
            } else {
                $map[$row->libelle_objectif]['echoue'] = (int) $row->nb;
            }
        }

        $result = [];
        foreach ($map as $libelle => $counts) {
            $result[] = [
                'libelle'   => $libelle,
                'reussi'    => $counts['reussi'],
                'echoue'    => $counts['echoue'],
                'non_tente' => max(0, $total - $counts['reussi'] - $counts['echoue']),
            ];
        }

        return response()->json([
            'total'     => $total,
            'objectifs' => $result,
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT CSV
    // ════════════════════════════════════════════════════════════════
    public function exportCsv()
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;
        $export     = new statistiqueExport($idClasse, $idObjectif);
        $fileName   = 'resultats_' . now()->format('Ymd_His') . '.csv';
        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT PDF
    // ════════════════════════════════════════════════════════════════
    public function exportPdf()
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;
        $html       = statistiqueExport::genererHtmlPdf($idClasse, $idObjectif);
        $fileName   = 'resultats_' . now()->format('Ymd_His') . '.pdf';
        return Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true])
            ->download($fileName);
    }
}