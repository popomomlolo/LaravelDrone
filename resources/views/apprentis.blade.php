@extends('layouts.layout')

@section('title', 'Liste des apprentis')

@section('content')

    <div class="page-title">Apprentis</div>
    <p class="page-sub">Gérez la liste des apprentis — ajoutez, modifiez ou supprimez</p>

    @if (session('success'))
        <div class="alert-app alert-app-success mb-3">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-app alert-app-danger mb-3">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="d-flex gap-2 mb-3">
        <button
            id="btnAjouter"
            type="button"
            class="btn-app btn-app-success"
        >+ Ajouter un apprenti</button>
        <button
            id="btnImport"
            type="button"
            class="btn-app btn-app-info"
        >⬆ Importer CSV</button>
        <button
            id="btnAjouterClasse"
            type="button"
            class="btn-app btn-app-secondary"
        >🏫 Ajouter une classe</button>
        <button
            id="btnSupprimerClasse"
            type="button"
            class="btn-app btn-app-danger"
        >🗑️ Supprimer une classe</button>
    </div>

    <div
        id="formAjouter"
        class="card-dark form-panel mb-3"
        style="display:none;"
    >
        <div class="form-panel-title">Ajouter un apprenti</div>
        <form
            action="/apprentis/ajouter"
            method="POST"
        >
            @csrf
            <div class="mb-3">
                <label class="form-label-dark">Nom</label>
                <input
                    type="text"
                    name="nom"
                    placeholder="Nom"
                    required
                    class="form-control-dark"
                >
            </div>
            <div class="mb-3">
                <label class="form-label-dark">Prénom</label>
                <input
                    type="text"
                    name="prenom"
                    placeholder="Prénom"
                    required
                    class="form-control-dark"
                >
            </div>
            <div class="mb-3">
                <label class="form-label-dark">Classe</label>
                <select
                    name="id_classe"
                    required
                    class="form-select-dark"
                >
                    @foreach ($classes as $id => $libelle)
                        <option value="{{ $id }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button
                    type="submit"
                    class="btn-app btn-app-success"
                >✓ Ajouter</button>
                <button
                    type="button"
                    id="annulerAjouter"
                    class="btn-app btn-app-danger"
                >✕ Annuler</button>
            </div>
        </form>
    </div>

    <div
        id="formImport"
        class="card-dark form-panel mb-3"
        style="display:none;"
    >
        <div class="form-panel-title">Importer plusieurs apprentis</div>
        <p class="form-panel-hint">Format : <code>nom,prenom,libelle_classe</code> (avec en-tête)</p>
        <form
            action="/apprentis/import-csv"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            <div class="mb-3">
                <input
                    type="file"
                    name="csv_file"
                    accept=".csv,.txt"
                    required
                    class="form-file-dark"
                >
            </div>
            <div class="d-flex gap-2">
                <button
                    type="submit"
                    class="btn-app btn-app-success"
                >⬆ Importer</button>
                <button
                    type="button"
                    id="annulerImport"
                    class="btn-app btn-app-danger"
                >✕ Annuler</button>
            </div>
        </form>
    </div>

    {{-- Formulaire Ajouter une classe --}}
    <div
        id="formAjouterClasse"
        class="card-dark form-panel mb-3"
        style="display:none;"
    >
        <div class="form-panel-title">Ajouter une classe</div>
        <p class="form-panel-hint">Le nom sera automatiquement formaté en MAJUSCULES sans espaces superflus.</p>
        <form
            action="/classes/ajouter"
            method="POST"
        >
            @csrf
            <div class="mb-3">
                <label class="form-label-dark">Nom de la classe</label>
                <input
                    type="text"
                    name="libelle_classe"
                    id="inputNomClasse"
                    placeholder="Ex: BTS SN 1"
                    required
                    class="form-control-dark"
                >
            </div>
            <div class="d-flex gap-2">
                <button
                    type="submit"
                    class="btn-app btn-app-success"
                >✓ Créer</button>
                <button
                    type="button"
                    id="annulerAjouterClasse"
                    class="btn-app btn-app-danger"
                >✕ Annuler</button>
            </div>
        </form>
    </div>

    {{-- Formulaire Supprimer une classe --}}
    <div
        id="formSupprimerClasse"
        class="card-dark form-panel mb-3"
        style="display:none;"
    >
        <div class="form-panel-title">Supprimer une classe</div>
        <p class="form-panel-hint">⚠️ Tous les apprentis de cette classe et leurs sessions seront supprimés définitivement.</p>
        <form
            action="/classes/supprimer"
            method="POST"
        >
            @csrf
            <div class="mb-3">
                <label class="form-label-dark">Classe à supprimer</label>
                <select
                    name="id_classe"
                    required
                    class="form-select-dark"
                >
                    @foreach ($classes as $id => $libelle)
                        <option value="{{ $id }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button
                    type="submit"
                    class="btn-app btn-app-danger"
                >🗑️ Supprimer</button>
                <button
                    type="button"
                    id="annulerSupprimerClasse"
                    class="btn-app btn-app-secondary"
                >✕ Annuler</button>
            </div>
        </form>
    </div>

    {{-- Filtre par classe --}}
    <div class="filtres mb-3">
        <div class="filtre-group">
            <label for="selectClasse">Classe</label>
            <select id="selectClasse">
                <option value="">-- Toutes les classes --</option>
                @foreach ($classes as $id => $libelle)
                    <option value="{{ $id }}">{{ $libelle }}</option>
                @endforeach
            </select>
        </div>
        <div
            class="spinner"
            id="spinner"
        ></div>
    </div>

    {{-- Barre sélection --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <button
            id="btnSelectAll"
            class="btn-app btn-app-secondary"
        >☑ Tout sélectionner</button>
        <button
            id="btnDeleteSel"
            class="btn-app btn-app-danger"
            style="display:none;"
        >
            🗑️ Supprimer la sélection (<span id="selCount">0</span>)
        </button>
    </div>

    <div class="card-dark">
        <table
            id="apprentisTable"
            class="table dataTable w-100"
        >
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    {{-- Modale Modifier --}}
    <div
        class="modal-overlay"
        id="modalModifier"
    >
        <div class="modal-box modal-box-sm">
            <div class="modal-header-bar">
                <span class="modal-nom">✏️ Modifier l'apprenti</span>
                <button
                    id="closeModifier"
                    class="btn-app btn-app-danger"
                >✕ Fermer</button>
            </div>
            <div class="modal-scroll-zone">
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
                    <div class="mb-3">
                        <label class="form-label-dark">Nom</label>
                        <input
                            type="text"
                            name="nom"
                            id="modifierNom"
                            required
                            class="form-control-dark"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label-dark">Prénom</label>
                        <input
                            type="text"
                            name="prenom"
                            id="modifierPrenom"
                            required
                            class="form-control-dark"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label-dark">Classe</label>
                        <select
                            name="id_classe"
                            id="modifierClasse"
                            required
                            class="form-select-dark"
                        >
                            @foreach ($classes as $id => $libelle)
                                <option value="{{ $id }}">{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button
                            type="submit"
                            class="btn-app btn-app-success"
                        >✓ Enregistrer</button>
                        <button
                            type="button"
                            id="annulerModifier"
                            class="btn-app btn-app-danger"
                        >✕ Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modale Suppression unitaire --}}
    <div
        class="modal-overlay"
        id="modalSupprimer"
    >
        <div
            class="modal-box modal-box-sm"
            style="text-align:center;"
        >
            <div class="modal-header-bar">
                <span class="modal-nom">⚠️ Confirmer la suppression</span>
                <button
                    id="closeSupprimer"
                    class="btn-app btn-app-secondary"
                >✕ Fermer</button>
            </div>
            <div class="modal-scroll-zone">
                <p class="modal-confirm-text">
                    Voulez-vous vraiment supprimer<br>
                    <strong
                        id="supprimerNomComplet"
                        class="modal-confirm-name"
                    ></strong> ?
                </p>
                <p class="modal-confirm-warning">Cette action est irréversible.</p>
                <input
                    type="hidden"
                    id="supprimerId"
                >
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button
                        id="btnConfirmerSupprimer"
                        class="btn-app btn-app-danger"
                    >🗑️ Supprimer</button>
                    <button
                        id="annulerSupprimer"
                        class="btn-app btn-app-secondary"
                    >Annuler</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modale Suppression sélection --}}
    <div
        class="modal-overlay"
        id="modalSupprimerSelection"
    >
        <div
            class="modal-box modal-box-sm"
            style="text-align:center;"
        >
            <div class="modal-header-bar">
                <span class="modal-nom">⚠️ Supprimer la sélection</span>
                <button
                    id="closeSupprimerSelection"
                    class="btn-app btn-app-secondary"
                >✕ Fermer</button>
            </div>
            <div class="modal-scroll-zone">
                <p class="modal-confirm-text">
                    Voulez-vous vraiment supprimer les<br>
                    <strong
                        id="selectionCount"
                        class="modal-confirm-name"
                    ></strong> apprenti(s) sélectionné(s) ?
                </p>
                <p class="modal-confirm-warning">Cette action est irréversible et supprimera aussi leurs sessions.</p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button
                        id="btnConfirmerSupprimerSelection"
                        class="btn-app btn-app-danger"
                    >🗑️ Supprimer</button>
                    <button
                        id="annulerSupprimerSelection"
                        class="btn-app btn-app-secondary"
                    >Annuler</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        var csrfToken = '{{ csrf_token() }}';

        $(document).ready(function() {

            // Boutons Ajouter / Importer
            $('#btnAjouter').on('click', function() {
                $('#formImport').hide();
                $('#formAjouterClasse').hide();
                $('#formAjouter').toggle();
            });
            $('#annulerAjouter').on('click', function() {
                $('#formAjouter').hide();
            });
            $('#btnImport').on('click', function() {
                $('#formAjouter').hide();
                $('#formAjouterClasse').hide();
                $('#formImport').toggle();
            });
            $('#annulerImport').on('click', function() {
                $('#formImport').hide();
            });
            $('#btnAjouterClasse').on('click', function() {
                $('#formAjouter').hide();
                $('#formImport').hide();
                $('#formSupprimerClasse').hide();
                $('#formAjouterClasse').toggle();
            });
            $('#annulerAjouterClasse').on('click', function() {
                $('#formAjouterClasse').hide();
            });
            $('#btnSupprimerClasse').on('click', function() {
                $('#formAjouter').hide();
                $('#formImport').hide();
                $('#formAjouterClasse').hide();
                $('#formSupprimerClasse').toggle();
            });
            $('#annulerSupprimerClasse').on('click', function() {
                $('#formSupprimerClasse').hide();
            });

            // Normalisation MAJUSCULES sans espaces superflus sur le champ nom de classe
            function normalizeClasse(val) {
                return val.toUpperCase().replace(/\s+/g, ' ').trim();
            }
            $('#inputNomClasse').on('input', function() {
                var pos = this.selectionStart;
                // Supprimer les caractères spéciaux : garder uniquement lettres, chiffres, espaces et tirets
                var cleaned = $(this).val().replace(/[^a-zA-ZÀ-ÿ0-9 \-]/g, '').toUpperCase();
                $(this).val(cleaned);
                this.setSelectionRange(pos, pos);
            });
            $('form[action="/classes/ajouter"]').on('submit', function(e) {
                var val = normalizeClasse($('#inputNomClasse').val());
                if (/[^a-zA-ZÀ-ÿ0-9 \-]/.test(val)) {
                    e.preventDefault();
                    alert('Le nom de la classe ne doit pas contenir de caractères spéciaux.');
                    return;
                }
                $('#inputNomClasse').val(val);
            });

            var table = $('#apprentisTable').DataTable({
                ajax: {
                    url: '/api/apprentis',
                    dataSrc: '',
                    error: function(xhr) {
                        console.error('Erreur chargement apprentis', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'id_apprenti',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            return '<input type="checkbox" class="cb-apprenti" data-id="' + id +
                                '" style="width:16px;height:16px;cursor:pointer;">';
                        }
                    },
                    {
                        data: 'nom'
                    },
                    {
                        data: 'prenom'
                    },
                    {
                        data: 'libelle_classe',
                        render: function(data) {
                            return '<span class="badge-app badge-app-accent">' + data + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<button class="btn-app btn-app-info btn-sm-app btn-modifier me-1" ' +
                                'data-id="' + data.id_apprenti + '" ' +
                                'data-nom="' + data.nom + '" ' +
                                'data-prenom="' + data.prenom + '" ' +
                                'data-id_classe="' + data.id_classe + '">✏️ Modifier</button>' +
                                '<button class="btn-app btn-app-danger btn-sm-app btn-supprimer" ' +
                                'data-id="' + data.id_apprenti + '" ' +
                                'data-nom="' + data.nom + '" ' +
                                'data-prenom="' + data.prenom + '">🗑️ Supprimer</button>';
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 10
            });

            // Gestion sélection — persistée dans un Set pour survivre aux rechargements
            var selectedIds = new Set();
            var allSelected = false;

            function getSelectedIds() {
                return Array.from(selectedIds);
            }

            function updateSelectionBar() {
                var n = selectedIds.size;
                $('#selCount').text(n);
                n > 0 ? $('#btnDeleteSel').show() : $('#btnDeleteSel').hide();
            }

            // Filtre par classe
            $('#selectClasse').on('change', function() {
                $('#spinner').show();
                selectedIds.clear();
                allSelected = false;
                $('#btnSelectAll').text('☑ Tout sélectionner');
                var idClasse = $(this).val();
                var url = '/api/apprentis' + (idClasse ? '?id_classe=' + encodeURIComponent(idClasse) : '');
                table.ajax.url(url).load(function() {
                    $('#spinner').hide();
                    updateSelectionBar();
                });
            });

            // Coche / décoche individuelle
            $('#apprentisTable tbody').on('change', '.cb-apprenti', function() {
                var id = parseInt($(this).data('id'));
                if ($(this).is(':checked')) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
                updateSelectionBar();
            });

            // Après chaque redraw : réappliquer l'état coché depuis le Set
            table.on('draw', function() {
                $('#apprentisTable tbody .cb-apprenti').each(function() {
                    $(this).prop('checked', selectedIds.has(parseInt($(this).data('id'))));
                });
                updateSelectionBar();
            });

            // Bouton "Tout sélectionner / Désélectionner tout"
            $('#btnSelectAll').on('click', function() {
                allSelected = !allSelected;
                if (allSelected) {
                    var $btn = $(this).prop('disabled', true).text('⏳ Chargement...');
                    // Récupérer TOUS les IDs (toutes pages) via l'API avec le filtre actif
                    $.getJSON('/api/apprentis', {
                        id_classe: $('#selectClasse').val()
                    }, function(data) {
                        data.forEach(function(a) {
                            selectedIds.add(a.id_apprenti);
                        });
                        // Cocher les cases visibles sur la page courante
                        $('#apprentisTable tbody .cb-apprenti').each(function() {
                            if (selectedIds.has(parseInt($(this).data('id')))) {
                                $(this).prop('checked', true);
                            }
                        });
                        $btn.prop('disabled', false).text('☐ Désélectionner tout');
                        updateSelectionBar();
                    });
                } else {
                    // Vider toute la sélection
                    selectedIds.clear();
                    $('#apprentisTable tbody .cb-apprenti').prop('checked', false);
                    $(this).text('☑ Tout sélectionner');
                    updateSelectionBar();
                }
            });

            // Bouton supprimer sélection
            $('#btnDeleteSel').on('click', function() {
                $('#selectionCount').text(getSelectedIds().length);
                $('#modalSupprimerSelection').addClass('active');
            });

            $('#closeSupprimerSelection, #annulerSupprimerSelection').on('click', function() {
                $('#modalSupprimerSelection').removeClass('active');
            });
            $('#modalSupprimerSelection').on('click', function(e) {
                if (e.target === this) $(this).removeClass('active');
            });

            $('#btnConfirmerSupprimerSelection').on('click', function() {
                var ids = getSelectedIds();
                $.ajax({
                    url: '/apprentis/supprimer-selection',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        ids: ids
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#modalSupprimerSelection').removeClass('active');
                            selectedIds.clear();
                            allSelected = false;
                            $('#btnSelectAll').text('☑ Tout sélectionner');
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression.');
                    }
                });
            });

            // Modale Modifier
            $('#apprentisTable tbody').on('click', '.btn-modifier', function() {
                $('#modifierId').val($(this).data('id'));
                $('#modifierNom').val($(this).data('nom'));
                $('#modifierPrenom').val($(this).data('prenom'));
                $('#modifierClasse').val($(this).data('id_classe'));
                $('#modalModifier').addClass('active');
            });

            $('#closeModifier, #annulerModifier').on('click', function() {
                $('#modalModifier').removeClass('active');
            });
            $('#modalModifier').on('click', function(e) {
                if (e.target === this) $(this).removeClass('active');
            });

            $('#formModifierModal').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/apprentis/update',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#modalModifier').removeClass('active');
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la modification.');
                    }
                });
            });

            // Modale Suppression unitaire
            $('#apprentisTable tbody').on('click', '.btn-supprimer', function() {
                $('#supprimerId').val($(this).data('id'));
                $('#supprimerNomComplet').text($(this).data('nom') + ' ' + $(this).data('prenom'));
                $('#modalSupprimer').addClass('active');
            });

            $('#closeSupprimer, #annulerSupprimer').on('click', function() {
                $('#modalSupprimer').removeClass('active');
            });
            $('#modalSupprimer').on('click', function(e) {
                if (e.target === this) $(this).removeClass('active');
            });

            $('#btnConfirmerSupprimer').on('click', function() {
                $.ajax({
                    url: '/apprentis/supprimer',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        apprenti_id: $('#supprimerId').val()
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#modalSupprimer').removeClass('active');
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression.');
                    }
                });
            });

        });
    </script>
@endpush
