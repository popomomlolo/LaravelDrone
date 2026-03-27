<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formateurs;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signupForm()
    {
        return view('signup', ['titre' => 'Mon premier exemple.']);
    }

    public function signinForm()
    {
        return view('signin', ['titre' => 'Mon premier exemple.']);
    }

    public function signup(Request $request)
    {
        // Vérifier si le login existe déjà
        if (Formateurs::where('login', $request->login)->exists()) {
            return redirect('/signup')->with('error', 'Ce login existe déjà.');
        }

        // Créer un nouvel utilisateur
        $formateur = Formateurs::create([
            'login' => $request->login,
            'mot_de_passe' => Hash::make($request->password),
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);

        return redirect('/signin')->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }

    public function signin(Request $request)
    {
        // Vérifier les identifiants - mapping des champs pour l'authentification
        $credentials = [
            'login' => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/apprentis')->with('success', 'Connexion réussie !');
        }

        return redirect('/signin')->with('error', 'login ou mot de passe incorrect.');
    }

    public function signout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/signin');
    }
}
