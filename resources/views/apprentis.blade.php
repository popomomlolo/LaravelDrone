@extends('layouts.base')

@section('title', 'Liste des apprentis')

@section('content')
    <h1>Liste des apprentis</h1>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    {{-- Formulaire de modification --}}
    @if (isset($apprenti))
        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; max-width: 400px;">
            <h3>Modifier l'apprenti</h3>
            <form action="/apprentis/update" method="POST">
                @csrf
                <input type="hidden" name="apprenti_id" value="{{ $apprenti->id_apprentis }}">
                <div style="margin-bottom: 0.5rem;">
                    <label>Nom :</label>
                    <input type="text" name="nom" value="{{ $apprenti->nom }}" required>
                </div>
                <div style="margin-bottom: 0.5rem;">
                    <label>Prénom :</label>
                    <input type="text" name="prenom" value="{{ $apprenti->prenom }}" required>
                </div>
                    <div style="margin-bottom: 0.5rem;">
                        <label>ID Classe :</label>
                        <input type="number" name="id_classes" value="{{ $apprenti->id_classes }}" required>
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

                <div style="margin-bottom: 0.5rem;">
                    <label>ID Classe :</label>
                    <input type="number" name="id_classes" placeholder="ID Classe" required>
                </div>
            <button type="submit" style="background: #28a745; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer;">Ajouter</button>
        </form>
    </div>

    {{-- Import CSV --}}
    <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; max-width: 400px;">
        <h3>Importer plusieurs apprentis (CSV)</h3>
        <p style="font-size: 0.85rem; color: #666;">Format du fichier : <code>nom,prenom,id_classes</code> (avec entête)</p>
        <form action="/apprentis/import-csv" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv_file" accept=".csv,.txt" required>
            <button type="submit" style="background: #007bff; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; margin-top: 0.5rem;">Importer</button>
        </form>
    </div>

    {{-- Tableau des apprentis --}}
    <table id="apprentisTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>ID Classe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apprentis as $a)
                <tr data-id="{{ $a->id_apprentis }}" data-nom="{{ $a->nom }}" data-prenom="{{ $a->prenom }}" data-id_classes="{{ $a->id_classes }}">
                    <td>{{ $a->id_apprentis }}</td>
                    <td>{{ $a->nom }}</td>
                    <td>{{ $a->prenom }}</td>
                    <td>{{ $a->id_classes}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    <div id="apprentiModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; padding:2rem; border-radius:8px; min-width:300px; max-width:400px; margin:auto; position:relative;">
            <span id="closeModal" style="position:absolute; top:8px; right:12px; cursor:pointer; font-size:1.5rem;">&times;</span>
            <h3 id="modalNom"></h3>
            <p><strong>Prénom :</strong> <span id="modalPrenom"></span></p>
            <p><strong>ID Classe :</strong> <span id="modalClasse"></span></p>
            <form id="modalSupprimerForm" action="/apprentis/supprimer" method="POST" style="margin-bottom:1rem;">
                @csrf
                <input type="hidden" name="apprenti_id" id="modalSupprimerId">
                <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:0.4rem 0.8rem; border-radius:4px; cursor:pointer;">Supprimer</button>
            </form>
            <form id="modalModifierForm" action="/apprentis/modifier" method="POST">
                @csrf
                <input type="hidden" name="apprenti_id" id="modalModifierId">
                <button type="submit" style="background:#007bff; color:#fff; border:none; padding:0.4rem 0.8rem; border-radius:4px; cursor:pointer;">Modifier</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#apprentisTable').DataTable();
            $('#apprentisTable tbody').on('click', 'tr', function() {
                var id = $(this).data('id');
                var nom = $(this).data('nom');
                var prenom = $(this).data('prenom');
                var classe = $(this).data('id_classes');
                $('#modalNom').text(nom);
                $('#modalPrenom').text(prenom);
                $('#modalClasse').text(classe);
                $('#modalSupprimerId').val(id);
                $('#modalModifierId').val(id);
                $('#apprentiModal').css('display', 'flex');
            });
            $('#closeModal').on('click', function() {
                $('#apprentiModal').hide();
            });
            $('#apprentiModal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        });
    </script>

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