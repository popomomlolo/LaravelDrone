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

/**
 * @brief Contrôleur gérant la page de statistiques.
 *
 * - `filtrer()`   → liste pour le tableau (id, nom, prenom, classe, nb_sessions)
 * - `detail()`    → sessions complètes d'un seul apprenti, chargé à la demande (au clic)
 * - `chartData()` → données agrégées via SQL pour le graphique Highcharts
 *
 * @package App\Http\Controllers
 */
class StatistiqueController extends Controller
{
    /**
     * @brief Affiche la page principale des statistiques.
     *
     * Charge les listes de classes et objectifs pour alimenter
     * les combobox de filtrage de la vue.
     *
     * @route GET /statistique
     *
     * @return \Illuminate\View\View Vue `statistique` avec :
     *   - `$classes`   : collection de Classes triées par ordre alphabétique
     *   - `$objectifs` : collection d'Objectifs triés par ordre alphabétique
     */
    public function index()
    {
        $classes   = Classes::orderBy('libelle_classe')->get();
        $objectifs = Objectifs::orderBy('libelle_objectif')->get();
        return view('statistique', compact('classes', 'objectifs'));
    }

    /**
     * @brief Retourne une liste d'apprentis filtrés selon la classe et/ou l'objectif.
     *
     *
     * @route GET /statistique/filtrer
     *
     * @param string|null id_classe   (query) Filtre par identifiant de classe
     * @param string|null id_objectif (query) Filtre par identifiant d'objectif
     *
     * @return JsonResponse Tableau JSON :
     * @code{.json}
     * [
     *   {
     *     "id"          : 1,
     *     "nom"         : "Dupont",
     *     "prenom"      : "Jean",
     *     "classe"      : "BTS Drone 1ère année",
     *     "nb_sessions" : 3
     *   }
     * ]
     * @endcode
     */
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

    /**
     * @brief Retourne le détail complet d'un apprenti avec toutes ses sessions.
     *
     * Chargé uniquement au clic sur une ligne du tableau (lazy loading).
     * Inclut les objectifs évalués et les conditions météo de chaque session.
     *
     * @route GET /statistique/detail/{id}
     *
     * @param int $id Identifiant de l'apprenti
     *
     *
     * @return JsonResponse Objet JSON :
     * @code{.json}
     * {
     *   "id"       : 1,
     *   "nom"      : "Dupont",
     *   "prenom"   : "Jean",
     *   "classe"   : "BTS Drone 1ère année",
     *   "sessions" : [
     *     {
     *       "id"                 : 5,
     *       "date"               : "05/01/2025 08:00",
     *       "type_drone"         : true,
     *       "type_environnement" : true,
     *       "duree_max"          : 30,
     *       "jour"               : true,
     *       "ciel"               : 0,
     *       "vent_norme"         : 2.0,
     *       "objectifs"          : [
     *         {
     *           "libelle"              : "Cerceaux",
     *           "reussi"               : true,
     *           "quantite_a_atteindre" : 3,
     *           "quantite_realisee"    : 3
     *         }
     *       ]
     *     }
     *   ]
     * }
     * @endcode
     */
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

    /**
     * @brief Retourne les données pour le graphique Highcharts.
     *
     * Compte, par objectif, le nombre d'apprentis ayant réussi ou échoué.
     * Le nombre de "non tentés" est calculé par différence avec le total.
     *
     * @route GET /statistique/chart-data
     *
     * @param string|null id_classe   (query) Filtre par identifiant de classe
     * @param string|null id_objectif (query) Filtre par identifiant d'objectif
     *
     * @return JsonResponse Objet JSON :
     * @endcode
     */
    public function chartData(): JsonResponse
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;

        $queryTotal = Apprentis::query();
        if ($idClasse)   $queryTotal->where('id_classe', $idClasse);
        if ($idObjectif) $queryTotal->whereHas('sessions.objectifs', fn($q) => $q->where('objectifs.id_objectif', $idObjectif));
        $total = $queryTotal->count();

        $query = DB::table('validations')
            ->join('objectifs',      'objectifs.id_objectif',    '=', 'validations.id_objectif')
            ->join('sessions_drone', 'sessions_drone.id_session', '=', 'validations.id_session')
            ->join('apprentis',      'apprentis.id_apprenti',    '=', 'sessions_drone.id_apprenti')
            ->select(
                'objectifs.libelle_objectif',
                'validations.reussi',
                DB::raw('COUNT(DISTINCT apprentis.id_apprenti) as nb')
            )
            ->groupBy('objectifs.id_objectif', 'objectifs.libelle_objectif', 'validations.reussi');

        if ($idClasse)   $query->where('apprentis.id_classe', $idClasse);
        if ($idObjectif) $query->where('validations.id_objectif', $idObjectif);

        $rows = $query->get();
        $map  = [];
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

        return response()->json(['total' => $total, 'objectifs' => $result]);
    }

    /**
     * @brief Exporte les résultats filtrés au format CSV.
     *
     * @route GET /statistique/csv
     *
     * @param string|null id_classe   (query) Filtre optionnel par classe
     * @param string|null id_objectif (query) Filtre optionnel par objectif
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Téléchargement CSV
     */
    public function exportCsv()
    {
        $idClasse   = request('id_classe')   ?: null;
        $idObjectif = request('id_objectif') ?: null;
        $export     = new statistiqueExport($idClasse, $idObjectif);
        $fileName   = 'resultats_' . now()->format('Ymd_His') . '.csv';
        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @brief Exporte les résultats filtrés au format PDF.
     *
     *
     * @route GET /statistique/pdf
     *
     * @param string|null id_classe   (query) Filtre optionnel par classe
     * @param string|null id_objectif (query) Filtre optionnel par objectif
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Téléchargement PDF
     */
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