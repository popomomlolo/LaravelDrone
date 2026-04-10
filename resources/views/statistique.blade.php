@extends('layouts.layout')

@section('title', 'Statistiques')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
    
</style>

<div class="page-wrap">

    <div class="page-title">Statistiques</div>
    <p class="page-sub">Filtrez par classe et/ou par objectif — exportez en CSV ou PDF</p>

    {{-- ════════════════════════════════════
         COMBOBOX — filtrent le tableau ET les exports
    ════════════════════════════════════ --}}
    <div class="filtres">

        <div class="filtre-group">
            <label for="selectClasse">Classe</label>
            <select id="selectClasse">
                <option value="">-- Toutes les classes --</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classes }}">
                        {{ $classe->libelle_classes }}
                    </option>
                @endforeach
            </select>
        

        
            <label for="selectObjectif">Objectif</label>
            <select id="selectObjectif">
                <option value="">-- Tous les objectifs --</option>
                @foreach ($objectifs as $objectif)
                    <option value="{{ $objectif->id_objectifs }}">
                        {{ $objectif->libelle_objectifs }}
                    </option>
                @endforeach
            </select>
        
        </div>

        <div class="spinner" id="spinner"></div>

    </div>

    {{-- ── Zone tableau AJAX ── --}}
    <div id="tableZone">
        <p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>
    </div>

    {{-- ════════════════════════════════════
         BOUTONS EXPORT
         Leur état (actif/désactivé) change selon
         les filtres sélectionnés (4 cas)
    ════════════════════════════════════ --}}
    <!--<div class="export-zone">-->
        <span>Exporter :</span>

        <button id="btnCsv" class="btn-export btn-csv">
            ⬇ Export CSV
        </button>

        <button id="btnPdf" class="btn-export btn-pdf" disabled>
            ⬇ Export PDF
        </button>
    <!--</div>-->

    

    {{-- ── Zone graphique (cachée par défaut) ── --}}
    <div id="chartZone" style="display:none;">
        <div id="chartContainer"></div>
        <p class="chart-description">
            Nombre d'apprentis ayant réussi <span style="color:#22c55e">●</span>
            ou échoué <span style="color:#ef4444">●</span> chaque objectif
        </p>
    </div>

</div>

{{-- ── Modale ── --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <button class="modal-close" onclick="fermerModale()">✕</button>
        <div class="modal-identite">
            <div class="modal-nom"    id="modalNom"></div>
            <div class="modal-classe" id="modalClasse"></div>
        </div>
        <div id="modalSessions"></div>
    </div>
</div>

{{-- ════════════════════════════════════
     SCRIPTS
════════════════════════════════════ --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('js/libs/highcharts.js') }}"></script>
<script src="{{ asset('js/libs/highcharts-more.js') }}"></script>
<script src="{{ asset('js/libs/exporting.js') }}"></script>
<script src="{{ asset('js/libs/accessibility.js') }}"></script>
<script src="{{ asset('js/libs/Statistiquechart.js') }}"></script>


<script>

    // ── Variables globales ──────────────────────────────────────────
    let tableInstance    = null;
    let tousLesApprentis = [];

    const urlFiltrer = '{{ route('statistique.filtrer') }}';
    const urlCsv     = '{{ route('statistique.csv') }}';
    const urlPdf     = '{{ route('statistique.pdf') }}';

    // ════════════════════════════════════════════════════════════════
    // GESTION DES BOUTONS D'EXPORT SELON LES 4 CAS
    //
    // Cas 1 : aucun filtre   → CSV toutes classes, PDF désactivé
    // Cas 2 : classe seule   → CSV classe filtrée, PDF activé
    // Cas 3 : objectif seul  → CSV personnes ayant fait cet objectif, PDF activé
    // Cas 4 : classe+objectif→ CSV classe+objectif filtrés, PDF activé
    // ════════════════════════════════════════════════════════════════
    function mettreAJourBoutonsExport() {

        const idClasse   = $('#selectClasse').val();
        const idObjectif = $('#selectObjectif').val();

        // CSV toujours disponible (exporte selon les filtres actifs)
        $('#btnCsv').prop('disabled', false);

        // PDF désactivé uniquement si aucun filtre (cas 1)
        if (!idClasse && !idObjectif) {
            $('#btnPdf').prop('disabled', true).attr('title', 'Sélectionnez au moins un filtre pour exporter en PDF');
        } else {
            $('#btnPdf').prop('disabled', false).attr('title', '');
        }
    }

    // ════════════════════════════════════════════════════════════════
    // ÉCOUTE DES COMBOBOX
    // Déclenche le tableau AJAX + met à jour les boutons export
    // ════════════════════════════════════════════════════════════════
    $('#selectClasse, #selectObjectif').on('change', function () {

        const idClasse   = $('#selectClasse').val();
        const idObjectif = $('#selectObjectif').val();

        // Met à jour l'état des boutons export
        mettreAJourBoutonsExport();

        // Cas 1 : aucun filtre → remet l'état vide
        if (!idClasse && !idObjectif) {
            $('#tableZone').html(
                '<p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>'
            );
            $('#chartZone').hide();
            if (tableInstance) {
                tableInstance.destroy();
                tableInstance = null;
            }
            return;
        }

        // Cas 2, 3 ou 4 → charge les données
        chargerDonnees(idClasse, idObjectif);
    });

    // ════════════════════════════════════════════════════════════════
    // EXPORT CSV
    // Passe les filtres actifs en paramètres GET
    // Le controller et l'export gèrent les 4 cas côté PHP
    // ════════════════════════════════════════════════════════════════
    $('#btnCsv').on('click', function () {

        const params = {};
        const idClasse   = $('#selectClasse').val();
        const idObjectif = $('#selectObjectif').val();

        if (idClasse)   params.id_classes   = idClasse;
        if (idObjectif) params.id_objectifs = idObjectif;

        // $.param() construit proprement la query string
        const qs = $.param(params);
        window.location.href = urlCsv + (qs ? '?' + qs : '');
    });

    // ════════════════════════════════════════════════════════════════
    // EXPORT PDF
    // Passe les mêmes filtres → le controller génère le bon PDF
    // ════════════════════════════════════════════════════════════════
    $('#btnPdf').on('click', function () {

        if ($(this).prop('disabled')) return;

        const params = {};
        const idClasse   = $('#selectClasse').val();
        const idObjectif = $('#selectObjectif').val();

        if (idClasse)   params.id_classes   = idClasse;
        if (idObjectif) params.id_objectifs = idObjectif;

        const qs = $.param(params);
        window.open(urlPdf + (qs ? '?' + qs : ''), '_blank');
    });

    // ════════════════════════════════════════════════════════════════
    // APPEL AJAX — charge les apprentis selon les filtres
    // ════════════════════════════════════════════════════════════════
    function chargerDonnees(idClasse, idObjectif) {

        $('#spinner').show();
        $('#chartZone').hide();

        $.ajax({
            url   : urlFiltrer,
            method: 'GET',
            data  : { id_classes: idClasse, id_objectifs: idObjectif },

            success: function (data) {
                tousLesApprentis = data;
                afficherTableau(data);

                if (data.length > 0) {
                    $('#chartZone').show();
                    setTimeout(function () { initChart(data); }, 100);
                }
            },

            error: function () {
                $('#tableZone').html(
                    '<p class="msg-info" style="color:red;">Erreur lors du chargement des données.</p>'
                );
            },

            complete: function () {
                $('#spinner').hide();
            }
        });
    }

    // ════════════════════════════════════════════════════════════════
    // CONSTRUCTION DU DATATABLE avec jQuery DOM
    // ════════════════════════════════════════════════════════════════
    function afficherTableau(data) {
        
        if (tableInstance) {
            tableInstance.destroy();
            tableInstance = null;
        }

        if (data.length === 0) {
            $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
            return;
        }

        // Construction des lignes
        let $tbody = $('<tbody>');
        $.each(data, function (index, a) {
            $('<tr>').attr('data-index', index)
                .append($('<td>').text(a.nom))
                .append($('<td>').text(a.prenom))
                .append($('<td>').text(a.classe))
                .append($('<td>').text(a.nb_sessions))
                .appendTo($tbody);
        });

        // Construction du tableau complet
        let $table = $('<table id="apprentisTable" class="display" style="width:100%">')
            .append(
                $('<thead>').append(
                    $('<tr>')
                        .append($('<th>').text('Nom'))
                        .append($('<th>').text('Prénom'))
                        .append($('<th>').text('Classe'))
                        .append($('<th>').text('Nb sessions'))
                )
            )
            .append($tbody);
        
        $('#tableZone').empty().append($table);
                            
        // Initialisation DataTables
        tableInstance = $('#apprentisTable').DataTable({
            language : { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order    : [[0, 'asc']]
        });

        // Clic sur une ligne → modale
        $('#apprentisTable tbody').on('click', 'tr', function () {
            ouvrirModale(tousLesApprentis[$(this).data('index')]);
        });
        
    }

    // ════════════════════════════════════════════════════════════════
    // MODALE — sessions et objectifs d'un apprenti
    // ════════════════════════════════════════════════════════════════
    function ouvrirModale(apprenti) {

        $('#modalNom').text(apprenti.nom + ' ' + apprenti.prenom);
        $('#modalClasse').text('📚 ' + apprenti.classe);

        let $container = $('#modalSessions').empty();

        if (apprenti.sessions.length === 0) {
            $container.append($('<p class="empty-obj">').text('Aucune session réalisée.'));
            $('#modalOverlay').addClass('active');
            return;
        }

        $.each(apprenti.sessions, function (i, session) {

            const reussis = $.grep(session.objectifs, function (o) { return  o.reussi; });
            const echoues = $.grep(session.objectifs, function (o) { return !o.reussi; });

            // En-tête de session
            let $header = $('<div class="session-header">')
                .append($('<span class="session-titre">').text('Session ' + (i + 1) + ' — ' + session.date))
                .append($('<span class="session-meta">').text(
                    session.type_drone + ' · ' + session.type_environnement + ' · ' + session.conditions_meteo
                ));

            // Corps : réussis
            let $body = $('<div class="session-body">')
                .append($('<div class="section-label">').text('✓ Objectifs réussis'));

            if (reussis.length === 0) {
                $body.append($('<p class="empty-obj">').text('Aucun objectif réussi'));
            } else {
                $.each(reussis, function (j, o) {
                    $body.append(
                        $('<div class="objectif-item">')
                            .append($('<span class="dot reussi">'))
                            .append($('<span>').text(o.libelle))
                            .append($('<span class="quantite">').text(o.quantite_realisee + ' / ' + o.quantite_a_atteindre))
                    );
                });
            }

            // Corps : échoués
            $body.append($('<div class="section-label">').text('✗ Objectifs échoués'));

            if (echoues.length === 0) {
                $body.append($('<p class="empty-obj">').text('Aucun objectif échoué'));
            } else {
                $.each(echoues, function (j, o) {
                    $body.append(
                        $('<div class="objectif-item">')
                            .append($('<span class="dot echoue">'))
                            .append($('<span>').text(o.libelle))
                            .append($('<span class="quantite">').text(o.quantite_realisee + ' / ' + o.quantite_a_atteindre))
                    );
                });
            }

            $container.append(
                $('<div class="session-block">').append($header).append($body)
            );
        });

        $('#modalOverlay').addClass('active');
    }

    function fermerModale() {
        $('#modalOverlay').removeClass('active');
    }

    // Ferme en cliquant sur le fond
    $('#modalOverlay').on('click', function (e) {
        if ($(e.target).is('#modalOverlay')) fermerModale();
    });

    // État initial des boutons au chargement (cas 1)
    mettreAJourBoutonsExport();

</script>

@endsection