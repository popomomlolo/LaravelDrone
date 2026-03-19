<?php

namespace App\Http\Controllers;
use App\Models\Apprenti;
use Illuminate\Http\Request;

class ApprentisController extends Controller
{
    public function showForm()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        return view('apprenti-form', compact('apprentis'));
    }

    public function getApprentis()
    {
        $apprentis = Apprenti::select('id', 'nom', 'prenom')
                         ->orderBy('nom')
                         ->get();
        return response()->json($apprentis);
    }

    public function showApprentis()
    {
        $apprentis = Apprenti::orderBy('nom')->get();
        return view('apprentis', compact('apprentis'));
    }

    public function supprimer(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if ($apprenti) {
            $apprenti->delete();
        }
        return redirect('/apprentis');
    }

    public function modifierForm(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        $apprentis = Apprenti::orderBy('nom')->get();
        return view('apprentis', compact('apprentis', 'apprenti'));
    }

    public function update(Request $request)
    {
        $apprenti = Apprenti::find($request->apprenti_id);
        if ($apprenti) {
            $apprenti->nom = $request->nom;
            $apprenti->prenom = $request->prenom;
            $apprenti->save();
        }
        return redirect('/apprentis')->with('success', 'Apprenti modifié avec succès');
    }

    public function ajouter(Request $request)
    {
        Apprenti::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);
        return redirect('/apprentis')->with('success', 'Apprenti ajouté avec succès');
    }

    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $firstLine = true;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($firstLine) { $firstLine = false; continue; } // skip header
            if (count($row) >= 2) {
                Apprenti::create([
                    'nom' => trim($row[0]),
                    'prenom' => trim($row[1]),
                ]);
            }
        }
        fclose($file);
        return redirect('/apprentis')->with('success', 'Apprentis importés avec succès');
    }
}
