<?php

namespace App\Http\Controllers;

use App\Models\Apprenti;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprentisController extends Controller
{
    public function showForm()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        return view('apprenti-form', compact('apprentis'));
    }

    public function getApprentis()
    {
        $apprentis = Apprenti::with('classe')->orderBy('nom')->get()
            ->map(fn($a) => [
                'id_apprentis'    => $a->id_apprentis,
                'nom'             => $a->nom,
                'prenom'          => $a->prenom,
                'id_classes'      => $a->id_classes,
                'libelle_classes' => $a->classe->libelle_classes ?? $a->id_classes,
            ]);
        return response()->json($apprentis);
    }

    public function showApprentis()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes = Classes::pluck('libelle_classes', 'id_classes');
        return view('apprentis', compact('apprentis', 'classes'));
    }

    public function supprimer(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if (!$apprenti) {
            return response()->json(['success' => false, 'message' => 'Apprenti introuvable'], 404);
        }
        DB::table('sessions_drone')->where('id_apprentis', $request->apprenti_id)->delete();
        $apprenti->delete();
        return response()->json(['success' => true]);
    }

    public function modifierForm(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes = Classes::pluck('libelle_classes', 'id_classes');
        return view('apprentis', compact('apprentis', 'apprenti', 'classes'));
    }

    public function update(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if (!$apprenti) {
            return response()->json(['success' => false, 'message' => 'Apprenti introuvable'], 404);
        }
        $apprenti->nom       = $request->nom;
        $apprenti->prenom    = $request->prenom;
        $apprenti->id_classes = $request->id_classes;
        $apprenti->save();

        $libelle = Classes::find($request->id_classes)->libelle_classes ?? $request->id_classes;

        return response()->json([
            'success'         => true,
            'id_apprentis'    => $apprenti->id_apprentis,
            'nom'             => $apprenti->nom,
            'prenom'          => $apprenti->prenom,
            'id_classes'      => $apprenti->id_classes,
            'libelle_classes' => $libelle,
        ]);
    }

    public function ajouter(Request $request)
    {
        Apprenti::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'id_classes' => $request->id_classes,
        ]);
        return redirect('/apprentis')->with('success', 'Apprenti ajouté avec succès');
    }

    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $firstLine = true;
        $count = 0;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }
            if (count($row) >= 3) {
                $libelleClasse = trim($row[2]);

                // Cherche la classe par son libellé, la crée si elle n'existe pas
                $classe = Classes::firstOrCreate(
                    ['libelle_classes' => $libelleClasse]
                );

                Apprenti::create([
                    'nom'        => trim($row[0]),
                    'prenom'     => trim($row[1]),
                    'id_classes' => $classe->id_classes,
                ]);
                $count++;
            }
        }
        fclose($file);
        return redirect('/apprentis')->with('success', $count . ' apprenti(s) importé(s) avec succès');
    }
}
