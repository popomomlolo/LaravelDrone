@extends('layouts.base')

@section('title', 'Connexion')

@section('content')
<h1>Connexion</h1>
@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="color: red;">{{ session('error') }}</div>
@endif
<form action="{{ url('/traitementLogin') }}" method="POST">
    @csrf
    <label>Email :</label>
    <input type="email" name="email" required><br>
    <label>Mot de passe :</label>
    <input type="password" name="password" required><br>
    <button type="submit">Se connecter</button>
</form>
<p>Pas encore de compte ? <a href="{{ url('/register') }}">S'inscrire</a></p>
@endsection
