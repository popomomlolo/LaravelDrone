<?php

namespace App\Http\Controllers;

use App\Models\Apprentis as Apprenti;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @brief Contrôleur gérant les opérations CRUD sur les apprentis.
 *
 * Fournit les endpoints pour lister, créer, modifier, supprimer
 * des apprentis ainsi qu'un import en masse via fichier CSV.
 * Expose également une API JSON utilisée par le DataTable AJAX.
 *
 * @package App\Http\Controllers
 */
class ApprentisController extends Controller
{
    /**
     * @brief Affiche le formulaire de gestion des apprentis (vue legacy).
     *
     * @deprecated Remplacé par index() + DataTable AJAX.
     *
     * @return \Illuminate\View\View Vue `apprenti-form` avec la liste des apprentis
     */
    public function showForm()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        return view('apprenti-form', compact('apprentis'));
    }

    /**
     * @brief Retourne la liste complète des apprentis au format JSON.
     *
     * Endpoint consommé par le DataTable AJAX de la vue `apprentis.blade.php`.
     *
     * @route GET /api/apprentis
     *
     * @return \Illuminate\Http\JsonResponse Tableau JSON :
     * @code{.json}
     * [
     *   {
     *     "id_apprenti"    : 1,
     *     "nom"            : "Dupont",
     *     "prenom"         : "Jean",
     *     "id_classe"      : 1,
     *     "libelle_classe" : "BTS Drone 1ère année"
     *   }
     * ]
     * @endcode
     */
    public function apiIndex()
    {
        $idClasse = request('id_classe') ?: null;

        $query = Apprenti::with('classe')->orderBy('nom');
        if ($idClasse) {
            $query->where('id_classe', $idClasse);
        }

        $apprentis = $query->get()->map(fn($a) => [
            'id_apprenti'    => $a->id_apprenti,
            'nom'            => $a->nom,
            'prenom'         => $a->prenom,
            'id_classe'      => $a->id_classe,
            'libelle_classe' => $a->classe->libelle_classe ?? $a->id_classe,
        ]);
        return response()->json($apprentis);
    }

    /**
     * @brief Supprime plusieurs apprentis et leurs sessions en une seule requête.
     *
     * @route POST /apprentis/supprimer-selection
     *
     * @param Request $request Requête contenant `ids` (array d'int)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySelection(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun apprenti sélectionné'], 422);
        }
        DB::table('sessions_drone')->whereIn('id_apprenti', $ids)->delete();
        $deleted = Apprenti::whereIn('id_apprenti', $ids)->delete();
        return response()->json(['success' => true, 'deleted' => $deleted]);
    }

    /**
     * @brief Affiche la page principale de gestion des apprentis.
     *
     * Charge la liste des apprentis et des classes pour alimenter
     * le formulaire d'ajout et le select de la modale modification.
     *
     * @route GET /apprentis
     *
     * @return \Illuminate\View\View Vue `apprentis` avec :
     *   - `$apprentis` : collection de tous les apprentis triés par nom
     *   - `$classes`   : tableau associatif [id_classe => libelle_classe]
     */
    public function index()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes   = Classes::pluck('libelle_classe', 'id_classe');
        return view('apprentis', compact('apprentis', 'classes'));
    }

    /**
     * @brief Supprime un apprenti et ses sessions associées.
     *
     * Supprime en cascade les sessions de l'apprenti avant de le supprimer,
     * car la contrainte de clé étrangère est en `RESTRICT`.
     *
     * @route POST /apprentis/supprimer
     *
     * @param Request $request Requête contenant `apprenti_id` (int)
     *
     * @return \Illuminate\Http\JsonResponse
     *   - Succès : `{"success": true}`
     *   - Échec  : `{"success": false, "message": "Apprenti introuvable"}` (HTTP 404)
     */
    public function destroy(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if (!$apprenti) {
            return response()->json(['success' => false, 'message' => 'Apprenti introuvable'], 404);
        }
        DB::table('sessions_drone')->where('id_apprenti', $request->apprenti_id)->delete();
        $apprenti->delete();
        return response()->json(['success' => true]);
    }

    /**
     * @brief Affiche la vue apprentis avec un apprenti présélectionné pour modification.
     *
     * @route POST /apprentis/modifier
     *
     * @param Request $request Requête contenant `apprenti_id` (int)
     *
     * @return \Illuminate\View\View Vue `apprentis` avec :
     *   - `$apprenti`  : l'apprenti à modifier
     *   - `$apprentis` : liste complète des apprentis
     *   - `$classes`   : tableau associatif [id_classe => libelle_classe]
     */
    public function editForm(Request $request)
    {
        $apprenti  = Apprenti::find($request->apprenti_id);
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes   = Classes::pluck('libelle_classe', 'id_classe');
        return view('apprentis', compact('apprentis', 'apprenti', 'classes'));
    }

    /**
     * @brief Met à jour les informations d'un apprenti via AJAX.
     *
     * @route POST /apprentis/update
     *
     * @param Request $request Requête contenant :
     *   - `apprenti_id` (int)    : identifiant de l'apprenti
     *   - `nom`         (string) : nouveau nom
     *   - `prenom`      (string) : nouveau prénom
     *   - `id_classe`   (int)    : nouvelle classe
     *
     * @return \Illuminate\Http\JsonResponse
     *   - Succès : `{"success": true, "id_apprenti": 1, "nom": "...", "prenom": "...",
     *               "id_classe": 1, "libelle_classe": "..."}`
     *   - Échec  : `{"success": false, "message": "Apprenti introuvable"}` (HTTP 404)
     */
    public function update(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if (!$apprenti) {
            return response()->json(['success' => false, 'message' => 'Apprenti introuvable'], 404);
        }
        $apprenti->nom       = $request->nom;
        $apprenti->prenom    = $request->prenom;
        $apprenti->id_classe = $request->id_classe;
        $apprenti->save();
        $libelle = Classes::find($request->id_classe)->libelle_classe ?? $request->id_classe;
        return response()->json([
            'success'        => true,
            'id_apprenti'    => $apprenti->id_apprenti,
            'nom'            => $apprenti->nom,
            'prenom'         => $apprenti->prenom,
            'id_classe'      => $apprenti->id_classe,
            'libelle_classe' => $libelle,
        ]);
    }

    /**
     * @brief Crée un nouvel apprenti et redirige vers la liste.
     *
     * @route POST /apprentis/ajouter
     *
     * @param Request $request Requête contenant :
     *   - `nom`       (string) : nom de l'apprenti
     *   - `prenom`    (string) : prénom de l'apprenti
     *   - `id_classe` (int)    : identifiant de la classe
     *
     * @return \Illuminate\Http\RedirectResponse Redirection vers /apprentis
     *   avec message flash `success`
     */
    public function store(Request $request)
    {
        Apprenti::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'id_classe' => $request->id_classe,
        ]);
        return redirect('/apprentis')->with('success', 'Apprenti ajouté avec succès');
    }

    /**
     * @brief Importe des apprentis en masse depuis un fichier CSV.
     *
     * Le fichier CSV doit avoir une ligne d'en-tête et le format suivant :
     * `nom,prenom,libelle_classe`
     *
     * Si une classe n'existe pas encore, elle est créée automatiquement
     * via `firstOrCreate`.
     *
     * @route POST /apprentis/import-csv
     *
     * @param Request $request Requête contenant :
     *   - `csv_file` (file) : fichier CSV ou TXT, requis
     *
     * @return \Illuminate\Http\RedirectResponse Redirection vers /apprentis
     *   avec message flash indiquant le nombre d'apprentis importés
     */
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file      = fopen($request->file('csv_file')->getRealPath(), 'r');
        $firstLine = true;
        $count     = 0;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($firstLine) { $firstLine = false; continue; }
            if (count($row) >= 3) {
                $libelleClasse = trim($row[2]);
                $classe = Classes::firstOrCreate(['libelle_classe' => $libelleClasse]);
                Apprenti::create([
                    'nom'       => trim($row[0]),
                    'prenom'    => trim($row[1]),
                    'id_classe' => $classe->id_classe,
                ]);
                $count++;
            }
        }
        fclose($file);
        return redirect('/apprentis')->with('success', $count . ' apprenti(s) importé(s) avec succès');
    }
}