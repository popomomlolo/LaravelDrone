@extends('layouts.layout')

@section('title', 'Liste des apprentis')

@section('content')
    <h1>Liste des apprentis</h1>

    @if (session('success'))
        <div style="color:green;margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Boutons formulaires --}}
    <div style="margin-bottom:1.5rem;">
        <button
            id="btnAjouter"
            type="button"
            style="background:#28a745;color:#fff;border:none;padding:0.5rem 1rem;border-radius:4px;cursor:pointer;margin-right:0.5rem;"
        >+ Ajouter un apprenti</button>
        <button
            id="btnImport"
            type="button"
            style="background:#007bff;color:#fff;border:none;padding:0.5rem 1rem;border-radius:4px;cursor:pointer;"
        >Importer CSV</button>
    </div>

    {{-- Formulaire d'ajout --}}
    <div
        id="formAjouter"
        style="display:none;margin-bottom:1.5rem;padding:1rem;border:1px solid #ddd;border-radius:8px;max-width:400px;"
    >
        <h3>Ajouter un apprenti</h3>
        <form
            action="/apprentis/ajouter"
            method="POST"
        >
            @csrf
            <div style="margin-bottom:0.5rem;"><label>Nom :</label><input
                    type="text"
                    name="nom"
                    placeholder="Nom"
                    required
                    style="width:100%;padding:0.3rem;"
                ></div>
            <div style="margin-bottom:0.5rem;"><label>Prénom :</label><input
                    type="text"
                    name="prenom"
                    placeholder="Prénom"
                    required
                    style="width:100%;padding:0.3rem;"
                ></div>
            <div style="margin-bottom:0.5rem;">
                <label>Classe :</label>
                <select
                    name="id_classe"
                    required
                    style="width:100%;padding:0.3rem;"
                >
                    @foreach ($classes as $id => $libelle)
                        <option value="{{ $id }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <button
                type="submit"
                style="background:#28a745;color:#fff;border:none;padding:0.4rem 0.8rem;border-radius:4px;cursor:pointer;"
            >Ajouter</button>
        </form>
    </div>

    {{-- Import CSV --}}
    <div
        id="formImport"
        style="display:none;margin-bottom:2rem;padding:1rem;border:1px solid #ddd;border-radius:8px;max-width:400px;"
    >
        <h3>Importer plusieurs apprentis (CSV)</h3>
        <p style="font-size:0.85rem;color:#666;">Format : <code>nom,prenom,libelle_classe</code> (avec entête)</p>
        <form
            action="/apprentis/import-csv"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            <input
                type="file"
                name="csv_file"
                accept=".csv,.txt"
                required
            >
            <button
                type="submit"
                style="background:#007bff;color:#fff;border:none;padding:0.4rem 0.8rem;border-radius:4px;cursor:pointer;margin-top:0.5rem;"
            >Importer</button>
        </form>
    </div>

    {{-- Tableau --}}
    <table
        id="apprentisTable"
        class="display"
        style="width:100%"
    >
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Classe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    {{-- Modale Modifier --}}
    <div
        id="modalModifier"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;"
    >
        <div
            style="background:#1a1a2e;color:#e2e8f0;padding:2rem;border-radius:12px;width:420px;max-width:90vw;position:relative;border:1px solid #2a2f3d;">
            <button
                id="closeModifier"
                style="position:absolute;top:0.75rem;right:1rem;background:none;border:none;font-size:1.5rem;cursor:pointer;color:#aaa;"
            >&times;</button>
            <h3 style="margin-bottom:1.5rem;">✏️ Modifier l'apprenti</h3>
            <form
                id="formModifierModal"
                action="/apprentis/update"
                method="POST"
            >
                @csrf
                <input
                    type="hidden"
                    name="apprenti_id"
                    id="modifierId"
                >
                <div style="margin-bottom:0.75rem;">
                    <label style="display:block;margin-bottom:0.25rem;">Nom :</label>
                    <input
                        type="text"
                        name="nom"
                        id="modifierNom"
                        required
                        style="width:100%;padding:0.5rem;background:#0d0f14;border:1px solid #2a2f3d;border-radius:6px;color:#e2e8f0;"
                    >
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label style="display:block;margin-bottom:0.25rem;">Prénom :</label>
                    <input
                        type="text"
                        name="prenom"
                        id="modifierPrenom"
                        required
                        style="width:100%;padding:0.5rem;background:#0d0f14;border:1px solid #2a2f3d;border-radius:6px;color:#e2e8f0;"
                    >
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.25rem;">Classe :</label>
                    <select
                        name="id_classe"
                        id="modifierClasse"
                        required
                        style="width:100%;padding:0.5rem;background:#0d0f14;border:1px solid #2a2f3d;border-radius:6px;color:#e2e8f0;"
                    >
                        @foreach ($classes as $id => $libelle)
                            <option value="{{ $id }}">{{ $libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <button
                        type="submit"
                        style="background:#007bff;color:#fff;border:none;padding:0.5rem 1.2rem;border-radius:6px;cursor:pointer;font-weight:600;"
                    >Enregistrer</button>
                    <button
                        type="button"
                        id="annulerModifier"
                        style="background:#444;color:#fff;border:none;padding:0.5rem 1.2rem;border-radius:6px;cursor:pointer;"
                    >Annuler</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modale Confirmation Suppression --}}
    <div
        id="modalSupprimer"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;"
    >
        <div
            style="background:#1a1a2e;color:#e2e8f0;padding:2rem;border-radius:12px;width:380px;max-width:90vw;position:relative;border:1px solid #2a2f3d;text-align:center;">
            <div style="font-size:3rem;margin-bottom:0.5rem;">⚠️</div>
            <h3 style="margin-bottom:0.5rem;">Confirmer la suppression</h3>
            <p style="color:#aaa;margin-bottom:1.5rem;">Voulez-vous vraiment supprimer <strong
                    id="supprimerNomComplet"></strong> ?<br>Cette action est irréversible.</p>
            <input
                type="hidden"
                id="supprimerId"
            >
            <div style="display:flex;gap:0.75rem;justify-content:center;">
                <button
                    id="btnConfirmerSupprimer"
                    style="background:#dc3545;color:#fff;border:none;padding:0.5rem 1.5rem;border-radius:6px;cursor:pointer;font-weight:600;"
                >Supprimer</button>
                <button
                    type="button"
                    id="annulerSupprimer"
                    style="background:#444;color:#fff;border:none;padding:0.5rem 1.5rem;border-radius:6px;cursor:pointer;"
                >Annuler</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
    >
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        var csrfToken = '{{ csrf_token() }}';

        $(document).ready(function() {
            var table = $('#apprentisTable').DataTable({
                ajax: {
                    url: '/api/apprentis',
                    dataSrc: ''
                },
                columns: [{
                        data: 'nom'
                    },
                    {
                        data: 'prenom'
                    },
                    {
                        data: 'libelle_classe'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<button class="btn-modifier" ' +
                                'data-id="' + data.id_apprenti + '" ' +
                                'data-nom="' + data.nom + '" ' +
                                'data-prenom="' + data.prenom + '" ' +
                                'data-id_classe="' + data.id_classe + '" ' +
                                'style="background:#007bff;color:#fff;border:none;padding:0.3rem 0.7rem;border-radius:4px;cursor:pointer;margin-right:0.4rem;">✏️ Modifier</button>' +
                                '<button class="btn-supprimer" ' +
                                'data-id="' + data.id_apprenti + '" ' +
                                'data-nom="' + data.nom + '" ' +
                                'data-prenom="' + data.prenom + '" ' +
                                'style="background:#dc3545;color:#fff;border:none;padding:0.3rem 0.7rem;border-radius:4px;cursor:pointer;">🗑️ Supprimer</button>';
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 10
            });

            // ── Modale Modifier ──
            $('#apprentisTable tbody').on('click', '.btn-modifier', function() {
                $('#modifierId').val($(this).data('id'));
                $('#modifierNom').val($(this).data('nom'));
                $('#modifierPrenom').val($(this).data('prenom'));
                $('#modifierClasse').val($(this).data('id_classe'));
                $('#modalModifier').css('display', 'flex');
            });

            $('#closeModifier, #annulerModifier').on('click', function() {
                $('#modalModifier').hide();
            });
            $('#modalModifier').on('click', function(e) {
                if (e.target === this) $(this).hide();
            });

            // Soumission AJAX modification
            $('#formModifierModal').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/apprentis/update',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#modalModifier').hide();
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la modification.');
                    }
                });
            });

            // ── Modale Suppression ──
            $('#apprentisTable tbody').on('click', '.btn-supprimer', function() {
                $('#supprimerId').val($(this).data('id'));
                $('#supprimerNomComplet').text($(this).data('nom') + ' ' + $(this).data('prenom'));
                $('#modalSupprimer').css('display', 'flex');
            });

            $('#annulerSupprimer').on('click', function() {
                $('#modalSupprimer').hide();
            });
            $('#modalSupprimer').on('click', function(e) {
                if (e.target === this) $(this).hide();
            });

            // Confirmation suppression AJAX
            $('#btnConfirmerSupprimer').on('click', function() {
                var id = $('#supprimerId').val();
                $.ajax({
                    url: '/apprentis/supprimer',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        apprenti_id: id
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#modalSupprimer').hide();
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression.');
                    }
                });
            });

            // ── Toggle formulaires ──
            $('#btnAjouter').on('click', function() {
                $('#formAjouter').toggle();
                $('#formImport').hide();
            });
            $('#btnImport').on('click', function() {
                $('#formImport').toggle();
                $('#formAjouter').hide();
            });
        });
    </script>
@endsection
