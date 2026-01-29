@extends('layouts.base')
@section('title', 'uiiii')

@section('content')
    <h1>{{ $word }}</h1>
    <h2>coucou</h2>
    <h1>PING</h1>
    <p>Uiiiiii</p>

    @if ($word == 'PING')
        <p>La page est en mode PING ({{ time() }}</p>
    @else
        <p>La page est en mode PONG ({{ time() }}</p>
    @endif
@endsection
