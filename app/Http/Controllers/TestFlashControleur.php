<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo2;

class TestFlashController extends Controller
{
    public function main()
    {
        return view('flash', []);
    }
    public function todo()
    {
        return view('todo', []);
    }
    public function traitement(Request $request)
    {
        if ($request->texte == '') {
            return redirect()->back()->with('error', 'Le champ texte ne peut pas être vide');
        }

        return redirect()->back()->with('success', 'Le champ texte est bien rempli');
    }

   /* public function addTodo(Request $request)
    {

        if ($request->texte == '') {
            return redirect()->back()->with('error', 'Le champ texte ne peut pas être vide');
        }
        Todo2::create($request->all());

        return redirect("/todo")->with('success', 'Todo ajouté avec succès');
    }*/
}
