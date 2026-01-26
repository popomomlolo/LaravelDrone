<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class PingPongControleur extends Controller
{
    //
    public function ping()
    {
        return view('ping', ['word' => 'PING']);
    }

    public function pong()
    {
        $valeursEnBase = Todo::all();
        return view('pong', ['coucou' => 'K-423', 'bdd' => $valeursEnBase]);
    }
}

