@extends('layouts.base')

@section('title', 'Sélectionner un apprenti')

@section('content')
    <h1>Sélectionner un apprenti</h1>
    <form action="/apprentis/modifier" method="POST">
        @csrf
        <label for="apprenti">Nom :</label>
        <select name="apprenti_id" id="apprenti">
            @foreach ($apprentis as $apprenti)
                <option value="{{ $apprenti->id }}">{{ $apprenti->nom }} {{ $apprenti->prenom }}</option>
            @endforeach
        </select>
        <button type="submit">Valider</button>
    </form>
@endsection
