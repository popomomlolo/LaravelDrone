<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
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
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/todo')->with('success', 'Connexion réussie !');
        }

        return redirect('/signin')->with('error', 'Email ou mot de passe incorrect.');
    }

    public function signout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); 
        return redirect('/signin');
    }
}

