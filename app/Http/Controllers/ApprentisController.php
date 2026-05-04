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

    public function apiIndex()
    {
        $apprentis = Apprenti::with('classe')->orderBy('nom')->get()
            ->map(fn($a) => [
                'id_apprenti'    => $a->id_apprenti,
                'nom'            => $a->nom,
                'prenom'         => $a->prenom,
                'id_classe'      => $a->id_classe,
                'libelle_classe' => $a->classe->libelle_classe ?? $a->id_classe,
            ]);
        return response()->json($apprentis);
    }

    public function index()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes = Classes::pluck('libelle_classe', 'id_classe');
        return view('apprentis', compact('apprentis', 'classes'));
    }

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

    public function editForm(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        $apprentis = Apprenti::orderBy('nom')->get();
        $classes = Classes::pluck('libelle_classe', 'id_classe');
        return view('apprentis', compact('apprentis', 'apprenti', 'classes'));
    }

    public function update(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if (!$apprenti) {
            return response()->json(['success' => false, 'message' => 'Apprenti introuvable'], 404);
        }
        $apprenti->nom      = $request->nom;
        $apprenti->prenom   = $request->prenom;
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

    public function store(Request $request)
    {
        Apprenti::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'id_classe' => $request->id_classe,
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
