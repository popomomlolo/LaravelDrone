<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestFlashController extends Controller
{
    
    public function main()
    {
        return view('flash', []);
        
    }
    public function traitement(Request $request)
    {
        if ($request->texte == '') {
            return redirect()->back()->with('error', 'Le champ texte ne peut pas être vide');
        }

        return redirect()->back()->with('success', 'Le champ texte est bien rempli');
    }
}