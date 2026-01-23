@extends('layouts.base')

@section('title', 'Bienvenue')

@section('content')
   <h1>PING</h1>
   <h1>{{ $words }}</h1>
   <p>@foreach ($todos as $key => $value)
       {{ $value->texte }} - {{ $value->termine ? 'Terminée' : 'Non terminée' }}
       <form action="{{ route('todomaj', ['id' => $value->id]) }}" method="POST" style="display:inline">
           @csrf
           <button type="submit">Changer d'état</button>
       </form>
       <form action="{{ route('todosup', ['id' => $value->id]) }}" method="POST" style="display:inline" onsubmit="return confirm('Supprimer cette tâche ?');">
           @csrf
           <button type="submit" style="color:red">Supprimer</button>
       </form>
       <br>
   @endforeach</p>
   @if($words == 'PING')
<p>La page est en mode PING ({{ time() }})</p>
@else
<p>La page est en mode PONG ({{ time() }})</p>
@endif
@endsection
