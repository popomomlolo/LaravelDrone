<form action="/public/traitement2" method="post">
    @csrf
    <input type="text" name="texte" placeholder="Texte de la tâche">
    <input type="checkbox" name="termine" value="1"> Terminée
    <button type="submit">Ajouter</button>
</form>
@if(session('error'))
<div style="color: red;">{{ session('error') }}</div>
@endif
@if(session('success'))
<div style="color: green;">{{ session('success') }}</div>
<p>
    {{--
@foreach ($todos as $key => $value)
    {{ $value->texte }} - {{ $value->termine ? 'Terminée' : 'Non terminée' }} <br>
@endforeach

--}}
</p>
@endif
