@extends('layouts.sign')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
<div class="auth-container">
    <h1>Sign In</h1>

    <form action="/signin" method="POST">
        @csrf
        <input type="text" name="login" placeholder="login" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

    <p>Vous n'avez pas de compte ? <a href="/signup">Inscrivez-vous ici</a></p>

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
</div>