<?php

namespace App\Http\Controllers;

use App\Models\Apprentis;

class apprentisControlleur extends Controller
{
    /**
     * Liste de tous les apprentis avec leur classe.
     */
    public function index()
    {
        $apprentis = Apprentis::with('classes')->get();

        return view('apprentis', compact('apprentis'));
    }

    /**
     * Détail d'un apprenti.
     */
    public function show(string $id)
    {
        $apprenti = Apprentis::with('classes', 'sessions')->findOrFail($id);

        return view('apprentis_detail', compact('apprenti'));
    }
}