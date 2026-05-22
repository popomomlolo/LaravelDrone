@extends('layouts.layout')

@section('title', 'Liste des apprentis')

@section('content')

    <div class="page-title">Apprentis</div>
    <p class="page-sub">Gérez la liste des apprentis — ajoutez, modifiez ou supprimez</p>

    {{-- ════ Message succès ════ --}}
    @if (session('success'))
        <div class="alert-app alert-app-success mb-3">{{ session('success') }}</div>
    @endif

    {{-- ════ Boutons toggle ════ --}}
    <div class="d-flex gap-2 mb-3">
        <button id="btnAjouter" type="button" class="btn-app btn-app-success">+ Ajouter un apprenti</button>
        <button id="btnImport"  type="button" class="btn-app btn-app-info">⬆ Importer CSV</button>
    </div>

    {{-- ════ Formulaire Ajouter ════ --}}
    <div id="formAjouter" class="card-dark form-panel mb-3" style="display:none;">
        <div class="form-panel-title">Ajouter un apprenti</div>
        <form action="/apprentis/ajouter" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label-dark">Nom</label>
                <input type="text" name="nom" placeholder="Nom" required class="form-control-dark">
            </div>
            <div class="mb-3">
                <label class="form-label-dark">Prénom</label>
                <input type="text" name="prenom" placeholder="Prénom" required class="form-control-dark">
            </div>
            <div class="mb-3">
                <label class="form-label-dark">Classe</label>
                <select name="id_classe" required class="form-select-dark">
                    @foreach ($classes as $id => $libelle)
                        <option value="{{ $id }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit"           class="btn-app btn-app-success">✓ Ajouter</button>
                <button type="button" id="annulerAjouter" class="btn-app btn-app-danger">✕ Annuler</button>
            </div>
        </form>
    </div>

    {{-- ════ Formulaire Import CSV ════ --}}
    <div id="formImport" class="card-dark form-panel mb-3" style="display:none;">
        <div class="form-panel-title">Importer plusieurs apprentis</div>
        <p class="form-panel-hint">Format : <code>nom,prenom,libelle_classe</code> (avec en-tête)</p>
        <form action="/apprentis/import-csv" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="csv_file" accept=".csv,.txt" required class="form-file-dark">
            </div>
            <div class="d-flex gap-2">
                <button type="submit"          class="btn-app btn-app-success">⬆ Importer</button>
                <button type="button" id="annulerImport" class="btn-app btn-app-danger">✕ Annuler</button>
            </div>
        </form>
    </div>

    {{-- ════ Tableau DataTable AJAX ════ --}}
    <div class="card-dark">
        <table id="apprentisTable" class="table dataTable w-100">
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
    </div>

    {{-- ════ Modale Modifier ════ --}}
    <div class="modal-overlay" id="modalModifier">
        <div class="modal-box modal-box-sm">
            <div class="modal-header-bar">
                <span class="modal-nom">✏️ Modifier l'apprenti</span>
                <button id="closeModifier" class="btn-app btn-app-danger">✕ Fermer</button>
            </div>
            <div class="modal-scroll-zone">
                <form id="formModifierModal" action="/apprentis/update" method="POST">
                    @csrf
                    <input type="hidden" name="apprenti_id" id="modifierId">
                    <div class="mb-3">
                        <label class="form-label-dark">Nom</label>
                        <input type="text" name="nom" id="modifierNom" required class="form-control-dark">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-dark">Prénom</label>
                        <input type="text" name="prenom" id="modifierPrenom" required class="form-control-dark">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-dark">Classe</label>
                        <select name="id_classe" id="modifierClasse" required class="form-select-dark">
                            @foreach ($classes as $id => $libelle)
                                <option value="{{ $id }}">{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit"               class="btn-app btn-app-success">✓ Enregistrer</button>
                        <button type="button" id="annulerModifier" class="btn-app btn-app-danger">✕ Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════ Modale Confirmation Suppression ════ --}}
    <div class="modal-overlay" id="modalSupprimer">
        <div class="modal-box modal-box-sm" style="text-align:center;">
            <div class="modal-header-bar">
                <span class="modal-nom">⚠️ Confirmer la suppression</span>
                <button id="closeSupprimer" class="btn-app btn-app-secondary">✕ Fermer</button>
            </div>
            <div class="modal-scroll-zone">
                <p class="modal-confirm-text">
                    Voulez-vous vraiment supprimer<br>
                    <strong id="supprimerNomComplet" class="modal-confirm-name"></strong> ?
                </p>
                <p class="modal-confirm-warning">Cette action est irréversible.</p>
                <input type="hidden" id="supprimerId">
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button id="btnConfirmerSupprimer" class="btn-app btn-app-danger">🗑️ Supprimer</button>
                    <button id="annulerSupprimer"      class="btn-app btn-app-secondary">Annuler</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var csrfToken = '{{ csrf_token() }}';

    $(document).ready(function () {

        // ── DataTable AJAX ───────────────────────────────────────
        var table = $('#apprentisTable').DataTable({
            ajax: {
                url    : '/api/apprentis',
                dataSrc: function (json) {
                    console.group('📥 GET /api/apprentis');
                    console.log('Nombre d\'apprentis :', json.length);
                    if (json.length > 0) {
                        console.log('Structure du 1er apprenti :', json[0]);
                        console.log('Champs disponibles        :', Object.keys(json[0]));
                    }
                    console.log('Données complètes :', json);
                    console.groupEnd();
                    return json;
                },
                error: function (xhr) {
                    console.group('❌ ERREUR GET /api/apprentis');
                    console.error('Status :', xhr.status);
                    console.error('Réponse:', xhr.responseText);
                    console.groupEnd();
                }
            },
            columns: [
                { data: 'nom' },
                { data: 'prenom' },
                {
                    data  : 'libelle_classe',
                    render: function (data) {
                        return '<span class="badge-app badge-app-accent">' + data + '</span>';
                    }
                },
                {
                    data      : null,
                    orderable : false,
                    searchable: false,
                    render    : function (data) {
                        return '<button class="btn-app btn-app-info btn-sm-app btn-modifier me-1" '
                            + 'data-id="'        + data.id_apprenti + '" '
                            + 'data-nom="'       + data.nom         + '" '
                            + 'data-prenom="'    + data.prenom      + '" '
                            + 'data-id_classe="' + data.id_classe   + '">✏️ Modifier</button>'
                            + '<button class="btn-app btn-app-danger btn-sm-app btn-supprimer" '
                            + 'data-id="'     + data.id_apprenti + '" '
                            + 'data-nom="'    + data.nom         + '" '
                            + 'data-prenom="' + data.prenom      + '">🗑️ Supprimer</button>';
                    }
                }
            ],
            language  : { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 10
        });

        // ── Toggle formulaires ───────────────────────────────────
        $('#btnAjouter').on('click', function () {
            $('#formAjouter').toggle();
            $('#formImport').hide();
        });
        $('#btnImport').on('click', function () {
            $('#formImport').toggle();
            $('#formAjouter').hide();
        });
        $('#annulerAjouter').on('click', function () { $('#formAjouter').hide(); });
        $('#annulerImport').on('click',  function () { $('#formImport').hide();  });

        // ── Modale Modifier — ouverture ──────────────────────────
        $('#apprentisTable tbody').on('click', '.btn-modifier', function () {
            console.group('🖊️ Ouverture modale Modifier');
            console.log({ id: $(this).data('id'), nom: $(this).data('nom'), prenom: $(this).data('prenom'), id_classe: $(this).data('id_classe') });
            console.groupEnd();
            $('#modifierId').val($(this).data('id'));
            $('#modifierNom').val($(this).data('nom'));
            $('#modifierPrenom').val($(this).data('prenom'));
            $('#modifierClasse').val($(this).data('id_classe'));
            $('#modalModifier').addClass('active');
        });

        $('#closeModifier, #annulerModifier').on('click', function () {
            $('#modalModifier').removeClass('active');
        });
        $('#modalModifier').on('click', function (e) {
            if (e.target === this) $(this).removeClass('active');
        });

        // ── AJAX Modification ────────────────────────────────────
        $('#formModifierModal').on('submit', function (e) {
            e.preventDefault();
            var formData   = $(this).serialize();
            var formObject = Object.fromEntries(new URLSearchParams(formData));
            console.group('📤 POST /apprentis/update');
            console.log('Données envoyées :', formObject);
            console.groupEnd();
            $.ajax({
                url    : '/apprentis/update',
                method : 'POST',
                data   : formData,
                success: function (res) {
                    console.group('📥 Réponse /apprentis/update');
                    console.log('Succès :', res.success);
                    console.log('Données:', res);
                    console.groupEnd();
                    if (res.success) {
                        $('#modalModifier').removeClass('active');
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    console.group('❌ ERREUR /apprentis/update');
                    console.error('Status :', xhr.status);
                    console.error('Réponse:', xhr.responseJSON);
                    console.groupEnd();
                    alert('Erreur lors de la modification.');
                }
            });
        });

        // ── Modale Suppression — ouverture ───────────────────────
        $('#apprentisTable tbody').on('click', '.btn-supprimer', function () {
            console.group('🗑️ Ouverture modale Supprimer');
            console.log({ id: $(this).data('id'), nom: $(this).data('nom'), prenom: $(this).data('prenom') });
            console.groupEnd();
            $('#supprimerId').val($(this).data('id'));
            $('#supprimerNomComplet').text($(this).data('nom') + ' ' + $(this).data('prenom'));
            $('#modalSupprimer').addClass('active');
        });

        $('#closeSupprimer, #annulerSupprimer').on('click', function () {
            $('#modalSupprimer').removeClass('active');
        });
        $('#modalSupprimer').on('click', function (e) {
            if (e.target === this) $(this).removeClass('active');
        });

        // ── AJAX Suppression ─────────────────────────────────────
        $('#btnConfirmerSupprimer').on('click', function () {
            var id      = $('#supprimerId').val();
            var payload = { _token: csrfToken, apprenti_id: id };
            console.group('📤 POST /apprentis/supprimer');
            console.log('Données envoyées :', payload);
            console.groupEnd();
            $.ajax({
                url    : '/apprentis/supprimer',
                method : 'POST',
                data   : payload,
                success: function (res) {
                    console.group('📥 Réponse /apprentis/supprimer');
                    console.log('Succès :', res.success);
                    console.log('Données:', res);
                    console.groupEnd();
                    if (res.success) {
                        $('#modalSupprimer').removeClass('active');
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    console.group('❌ ERREUR /apprentis/supprimer');
                    console.error('Status :', xhr.status);
                    console.error('Réponse:', xhr.responseJSON);
                    console.groupEnd();
                    alert('Erreur lors de la suppression.');
                }
            });
        });

    });
</script>
@endpush