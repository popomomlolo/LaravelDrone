<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthentificationControleur extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function traitementLogin(Request $request)
    {
        // Traitement de la connexion (à compléter selon la logique d'authentification)
        // Exemple : validation simple
        $credentials = $request->only('email', 'password');
        // Auth::attempt($credentials) ...
        return redirect('/')->with('success', 'Connexion réussie !');
    }

    public function register()
    {
        return view('register');
    }

    public function traitementRegister(Request $request)
    {
        // Traitement de l'inscription (à compléter selon la logique d'inscription)
        // Exemple : validation simple
        $data = $request->only('name', 'email', 'password');
        // User::create([...]) ...
        return redirect('/login')->with('success', 'Inscription réussie !');
    }
}
