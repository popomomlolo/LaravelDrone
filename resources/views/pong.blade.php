@extends('layouts.base')
@section('title', 'Bienvenue')

@section('content')
   <h1>PONG</h1>
   <h1>{{$coucou}}</h1>

   <ul>
  @foreach($bdd as $key => $value)
  <li>{{ $key }} : {{ $value }}</li>
  @endforeach
</ul>
@endsection