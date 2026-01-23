<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoControlleur extends Controller
{
    public function todomaj($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->termine = !$todo->termine;
        $todo->save();
        return redirect()->route('pong')->with('success', 'État de la tâche mis à jour !');
    }

    public function todosup($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return redirect()->route('pong')->with('success', 'Tâche supprimée avec succès !');
    }
}
