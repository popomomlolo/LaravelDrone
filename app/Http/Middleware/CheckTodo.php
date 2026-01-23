<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTodo
{

    public function handle(Request $request, Closure $next)
{
    if (strpos($request->texte, 'twitter') !== false) {
        return redirect()->back()->with('error', 'Le mot twitter est interdit');
    }

    return $next($request);
}
}
