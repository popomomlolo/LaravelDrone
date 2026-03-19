@extends('layouts.base')

@section('title', 'Liste des apprentis')

@section('content')
    <h1>Liste des apprentis</h1>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    {{-- Sélection d'un apprenti --}}
    <div style="margin-bottom: 1rem;">
        <label for="apprenti">Sélectionner un apprenti :</label>
        <select id="apprenti_select">
            @foreach ($apprentis as $a)
                <option value="{{ $a->id }}" data-nom="{{ $a->nom }}" data-prenom="{{ $a->prenom }}"
                    {{ isset($apprenti) && $apprenti->id == $a->id ? 'selected' : '' }}>
                    {{ $a->nom }} {{ $a->prenom }}
                </option>
            @endforeach
        </select>

        {{-- Bouton Modifier --}}
        <form action="/apprentis/modifier" method="POST" style="display:inline-block;">
            @csrf
            <input type="hidden" name="apprenti_id" id="modifier_id">
            <button type="submit">Modifier</button>
        </form>

        {{-- Bouton Supprimer --}}
        <form action="/apprentis/supprimer" method="POST" style="display:inline-block; margin-left: 0.5rem;">
            @csrf
            <input type="hidden" name="apprenti_id" id="supprimer_id">
            <button type="submit" style="background: #dc3545; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer;">Supprimer</button>
        </form>
    </div>

    {{-- Formulaire de modification --}}
    @if (isset($apprenti))
        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; max-width: 400px;">
            <h3>Modifier l'apprenti</h3>
            <form action="/apprentis/update" method="POST">
                @csrf
                <input type="hidden" name="apprenti_id" value="{{ $apprenti->id }}">
                <div style="margin-bottom: 0.5rem;">
                    <label>Nom :</label>
                    <input type="text" name="nom" value="{{ $apprenti->nom }}" required>
                </div>
                <div style="margin-bottom: 0.5rem;">
                    <label>Prénom :</label>
                    <input type="text" name="prenom" value="{{ $apprenti->prenom }}" required>
                </div>
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    @endif

    {{-- Formulaire d'ajout --}}
    <div style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; max-width: 400px;">
        <h3>Ajouter un apprenti</h3>
        <form action="/apprentis/ajouter" method="POST">
            @csrf
            <div style="margin-bottom: 0.5rem;">
                <label>Nom :</label>
                <input type="text" name="nom" placeholder="Nom" required>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <label>Prénom :</label>
                <input type="text" name="prenom" placeholder="Prénom" required>
            </div>
            <button type="submit" style="background: #28a745; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer;">Ajouter</button>
        </form>
    </div>

    {{-- Import CSV --}}
    <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; max-width: 400px;">
        <h3>Importer plusieurs apprentis (CSV)</h3>
        <p style="font-size: 0.85rem; color: #666;">Format du fichier : <code>nom,prenom</code> (avec entête)</p>
        <form action="/apprentis/import-csv" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv_file" accept=".csv,.txt" required>
            <button type="submit" style="background: #007bff; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; margin-top: 0.5rem;">Importer</button>
        </form>
    </div>

    {{-- Tableau des apprentis --}}
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apprentis as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->nom }}</td>
                    <td>{{ $a->prenom }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var select = document.getElementById('apprenti_select');
            var modifierId = document.getElementById('modifier_id');
            var supprimerId = document.getElementById('supprimer_id');

            function sync() {
                modifierId.value = select.value;
                supprimerId.value = select.value;
            }

            select.addEventListener('change', sync);
            sync();
        });
    </script>
@endsection