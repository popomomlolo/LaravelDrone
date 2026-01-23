<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class PingPongControleur extends Controller
{
        public function ping()
    {
        return view('ping', ['word' => 'PING', 'serverInfo' => $_SERVER]);
    }

    public function pong()
    {
        $valeursEnBase = Todo::all();
        return view('pong', ['words'=>'PONG', 'todos' => $valeursEnBase]);
    }

    public function addTodo(Request $request){
        // $request contient l'ensemble des données envoyées par le formulaire
        // request()->all() retourne un tableau associatif avec l'ensemble des données
        if ($request->texte == '') {
            return redirect()->back()->with('error', 'Le champ texte ne peut pas être vide');
        } 
        Todo::create($request->all());
        return redirect("/todo")->with('success', 'La tâche a été ajoutée avec succès');
    }
}
