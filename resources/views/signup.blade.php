@extends('layouts.sign')
<link
    rel="stylesheet"
    href="{{ asset('css/auth.css') }}"
>
<div class="auth-container">
    <h1>Sign Up</h1>

    <form
        action="/signup"
        method="POST"
    >
        @csrf
        <input
            type="text"
            name="nom"
            placeholder="Nom"
            required
        >
        <input
            type="text"
            name="prenom"
            placeholder="Prénom"
            required
        >
        <input
            type="text"
            name="login"
            placeholder="Login"
            required
        >
        <input
            type="password"
            name="password"
            placeholder="Mot de passe"
            required
        >
        <button type="submit">S'inscrire</button>
    </form>

    <p>Already have an account? <a href="/signin">Login here</a></p>

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
</div>
