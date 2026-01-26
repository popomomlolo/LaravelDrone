<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNumeric;

class TodoController extends Controller
{
    public function todo()
    {
        $valeursEnBase = Todo::all();
        return view('todo', ['bdd' => $valeursEnBase]);
    }
    //
    public function addTodo(Request $request)
    {
        if ($request->texte == '') {
            return redirect()->back()->with('error', 'Le champ texte ne peut pas être vide');
        }
        Todo::create($request->all());
        return redirect("/todo")->with('success', 'Todo ajouté avec succès');
    }

    public function majTodo($id)
    {
        $todo = Todo::find($id);
        $todo->termine = !$todo->termine;
        $todo->save();
        return redirect("/todo")->with('success', 'Todo mis à jour avec succès');
    }

    public function delTodo($id)
    {
        if (isNumeric($id)) {
            $todo = Todo::destroy($id);
            if ($todo) {
                return redirect("/todo")->with('success', 'Todo supprimé avec succès');
            }
        }
    }
}
