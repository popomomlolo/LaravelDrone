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
        $apprentis = Apprenti::select('apprenti_id', 'nom')
                         ->orderBy('nom')
                         ->get();
        return response()->json($apprentis);
    }

    public function showApprentis()
    {
        return view('apprentis'); // Correction du nom de la vue
    }
}
