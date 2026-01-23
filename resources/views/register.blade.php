@extends('layouts.base')

@section('title', 'Inscription')

@section('content')
<h1>Inscription</h1>
@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="color: red;">{{ session('error') }}</div>
@endif
<form action="{{ url('/traitementRegister') }}" method="POST">
    @csrf
    <label>Nom :</label>
    <input type="text" name="name" required><br>
    <label>Email :</label>
    <input type="email" name="email" required><br>
    <label>Mot de passe :</label>
    <input type="password" name="password" required><br>
    <button type="submit">S'inscrire</button>
</form>
<p>Déjà inscrit ? <a href="{{ url('/login') }}">Se connecter</a></p>
@endsection
