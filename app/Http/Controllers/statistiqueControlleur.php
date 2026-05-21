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
        $classes   = Classes::orderBy('libelle_classe')->get();
        $objectifs = Objectifs::orderBy('libelle_objectif')->get();

        return view('statistique', compact('classes', 'objectifs'));
    }

    // ════════════════════════════════════════════════════════════════
    // AJAX — FILTRER
    // ════════════════════════════════════════════════════════════════
    public function filtrer(): JsonResponse
    {
        $idClasse   = request('id_classe')   ?: false;
        $idObjectif = request('id_objectif') ?: false;

        if ($idClasse) {
            $apprentis = $idObjectif
                ? $this->filtrerParClasseEtObjectif($idClasse, $idObjectif)
                : $this->filtrerParClasse($idClasse);
        } else {
            $apprentis = $idObjectif
                ? $this->filtrerParObjectif($idObjectif)
                : $this->filtrerTout();
        }

        $result = $apprentis->map(fn($a) => $this->formaterApprentis($a));

        return response()->json($result);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT CSV
    // ════════════════════════════════════════════════════════════════
    public function exportCsv()
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;

        $export   = new statistiqueExport($idClasse, $idObjectif);
        $fileName = 'resultats_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];

        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV, $headers);
    }

    // ════════════════════════════════════════════════════════════════
    // EXPORT PDF
    // ════════════════════════════════════════════════════════════════
    public function exportPdf()
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;

        $html     = statistiqueExport::genererHtmlPdf($idClasse, $idObjectif);
        $fileName = 'resultats_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true]);

        return $pdf->download($fileName);
    }

    // ════════════════════════════════════════════════════════════════
    // MÉTHODES PRIVÉES DE FILTRAGE
    // Toutes chargent 'sessions.meteo' pour avoir les données météo
    // ════════════════════════════════════════════════════════════════

    /**
     * Cas 2 — filtre par classe uniquement
     */
    private function filtrerParClasse($idClasse)
    {
        return Apprentis::with(['classe', 'sessions.objectifs', 'sessions.meteo'])
            ->where('id_classe', $idClasse)
            ->orderBy('nom')
            ->get();
    }

    /**
     * Cas 3 — filtre par objectif uniquement
     * Retourne les apprentis ayant au moins une session avec cet objectif
     */
    private function filtrerParObjectif($idObjectif)
    {
        return Apprentis::with(['classe', 'sessions.objectifs', 'sessions.meteo'])
            ->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectif', $idObjectif);
            })
            ->orderBy('nom')
            ->get();
    }

    /**
     * Cas 4 — filtre par classe ET par objectif
     */
    private function filtrerParClasseEtObjectif($idClasse, $idObjectif)
    {
        return Apprentis::with(['classe', 'sessions.objectifs', 'sessions.meteo'])
            ->where('id_classe', $idClasse)
            ->whereHas('sessions.objectifs', function ($q) use ($idObjectif) {
                $q->where('objectifs.id_objectif', $idObjectif);
            })
            ->orderBy('nom')
            ->get();
    }

    /**
     * Cas 5 — aucun filtre (toutes les classes + tous les objectifs)
     */
    private function filtrerTout()
    {
        return Apprentis::with(['classe', 'sessions.objectifs', 'sessions.meteo'])
            ->orderBy('nom')
            ->get();
    }

    // ════════════════════════════════════════════════════════════════
    // FORMATAGE JSON
    // ════════════════════════════════════════════════════════════════

    /**
     * Formate un apprenti en tableau pour la réponse JSON.
     * Les champs météo (jour, ciel, vent_norme) viennent de conditions_meteo
     * via la relation session->meteo.
     */
    private function formaterApprentis($apprenti): array
    {
        $sessions = $apprenti->sessions->map(fn($session) => [
            'id'                 => $session->id_session,
            'date'               => \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i'),
            'type_drone'         => (bool) $session->type_drone,
            'type_environnement' => (bool) $session->type_environnement,
            'duree_max'          => $session->duree_max,

            // ── Météo (depuis conditions_meteo) ──────────────────
            'jour'               => $session->meteo ? (bool) $session->meteo->jour       : null,
            'ciel'               => $session->meteo ?        $session->meteo->ciel        : null,
            'vent_norme'         => $session->meteo ?        $session->meteo->vent_norme  : null,

            // ── Objectifs (depuis validations) ───────────────────
            'objectifs'          => $session->objectifs->map(fn($obj) => [
                'libelle'              => $obj->libelle_objectif,
                'reussi'               => (bool) $obj->pivot->reussi,
                'quantite_a_atteindre' => $obj->pivot->quantite_a_atteindre,
                'quantite_realisee'    => $obj->pivot->quantite_realisee,
            ]),
        ]);

        return [
            'id'          => $apprenti->id_apprenti,
            'nom'         => $apprenti->nom,
            'prenom'      => $apprenti->prenom,
            'classe'      => $apprenti->classe->libelle_classe ?? '—',
            'nb_sessions' => $apprenti->sessions->count(),
            'sessions'    => $sessions,
        ];
    }
}