@extends('layouts.base')

@section('title', 'Bienvenue')

@section('content')
   <h1>PONG</h1>
<h1>{{ $word }}</h1>
@if($word == 'PING')
<p>La page est en mode PING ({{ time() }})</p>
@else
<p>La page est en mode PONG ({{ time() }})</p>
@endif

@endsection