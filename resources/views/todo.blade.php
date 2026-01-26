@extends('layouts.base')

@section('title', 'Bienvenue')

@section('content')
    <table>
        @foreach ($bdd as $valeur)
            <tr>
                <td>
                    <a href="/todoSupp/{{ $valeur->id }}">supp {{ $valeur->id }}</a>
                </td>

                <td>
                    <a href="/todoMaj/{{ $valeur->id }}">maj {{ $valeur->id }}</a>
                </td>
                <td>{{ $valeur->texte }}</td>
                <td>{{ $valeur->termine ? 'Terminé' : 'En cours' }}</td>
            </tr>
        @endforeach
    </table>

    <form action="/traitement2" method="POST">
        @csrf
        <input type="text" name="texte" placeholder="le texte de la bdd">
        <button name="ajouter" type="submit">Ajouter</button>
    </form>

    <br>
    <a href="/signout">Se déconnecter</a>

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

@endsection
