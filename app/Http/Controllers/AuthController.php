<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        // Créer un nouvel utilisateur
        $utilisateur = Utilisateur::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/signin')->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }

    public function signin(Request $request)
    {
        // Vérifier les identifiants
        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if ($utilisateur && Hash::check($request->password, $utilisateur->password)) {
            return redirect('/todo')->with('success', 'Connexion réussie !');
        }

        return redirect('/signin')->with('error', 'Email ou mot de passe incorrect.');
    }
}

