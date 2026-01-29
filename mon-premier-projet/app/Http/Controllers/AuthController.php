<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Environment\Console;


class AuthController extends Controller
{
    public function inscriptionForm()
    {
        return view('inscription', ['titre' => 'Mon premier exemple.']);
    }

    public function connexionForm()
    {
        return view('connexion', ['titre' => 'Mon premier exemple.']);
    }

    public function inscription(Request $request)
    {
        // Créer un nouvel utilisateur
        $utilisateur = Utilisateur::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        return redirect('/connexion')->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }

    public function connexion(Request $request)
    {
        // Vérifier les identifiants
        $mdp = $request->input('password');
        $email = $request->input('email');
        $utilisateur = Utilisateur::where('email', $email)->first();
        


        //$estValide = password_verify($mdp, $utilisateur->password);

        if ($utilisateur!=null && password_verify($mdp, $utilisateur->password)  ) {
            $request->session()->put('user', $utilisateur);
            return redirect('/todo');
        } else {
            return redirect('/connexion')->with('error', 'Identifiants incorrects');
        }
    }

    public function signout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/connexion');
    }
}
