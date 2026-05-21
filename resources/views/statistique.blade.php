@extends('layouts.layout')

@section('title', 'Statistiques')

@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <div class="page-wrap">

        <div class="page-title">Statistiques</div>
        <p class="page-sub">Filtrez par classe et/ou par objectif (ou affichez tout) — exportez en CSV ou PDF</p>

        {{-- ════ FILTRES ════ --}}
        <div class="filtres">
            <div class="filtre-group">
                <label for="selectClasse">Classe</label>
                <select id="selectClasse">
                    <option value="">-- Toutes les classes --</option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classe }}">{{ $classe->libelle_classe }}</option>
                    @endforeach
                </select>

                <label for="selectObjectif">Objectif</label>
                <select id="selectObjectif">
                    <option value="">-- Tous les objectifs --</option>
                    @foreach ($objectifs as $objectif)
                        <option value="{{ $objectif->id_objectif }}">{{ $objectif->libelle_objectif }}</option>
                    @endforeach
                </select>
            </div>

            <div class="spinner" id="spinner"></div>
        </div>

        {{-- ════ EXPORTS ════ --}}
        <span>Exporter :</span>
        <button id="btnCsv" class="btn-export btn-csv">⬇ Export CSV</button>
        <button id="btnPdf" class="btn-export btn-pdf">⬇ Export PDF</button>
        
        {{-- ════ TABLEAU ════ --}}
        <div id="tableZone">
            <p class="msg-info">Chargement des données…</p>
        </div>

        {{-- ════ GRAPHIQUE ════ --}}
        <div id="chartZone" style="display:none;">
            <div id="chartContainer"></div>
            <p class="chart-description">
                Nombre d'apprentis ayant réussi <span style="color:#22c55e">●</span>
                ou échoué <span style="color:#ef4444">●</span> chaque objectif
            </p>
        </div>

    </div>

    {{-- ════ MODALE DÉTAIL APPRENTI ════ --}}
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

    {{-- ════ SCRIPTS ════ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/libs/highcharts.js') }}"></script>
    <script src="{{ asset('js/libs/highcharts-more.js') }}"></script>
    <script src="{{ asset('js/libs/exporting.js') }}"></script>
    <script src="{{ asset('js/libs/accessibility.js') }}"></script>
    <script src="{{ asset('js/libs/Statistiquechart.js') }}"></script>

    <script>

        // ── Variables globales ──────────────────────────────────
        let tableInstance     = null;
        let tousLesApprentis  = [];
        let tousLesObjectifs  = @json($objectifs);

        const urlFiltrer = '{{ route('statistique.filtrer') }}';
        const urlCsv     = '{{ route('statistique.csv') }}';
        const urlPdf     = '{{ route('statistique.pdf') }}';

        // ── Helpers ─────────────────────────────────────────────

        function getObjectifNom(idObjectif) {
            if (!idObjectif) return null;
            const obj = tousLesObjectifs.find(o => o.id_objectif == idObjectif);
            return obj ? obj.libelle_objectif : null;
        }

        /**
         * Convertit le booléen type_drone en libellé lisible.
         * true  = Drone FPV (ou selon ta convention métier)
         * false = Drone classique
         */
        function labelDrone(valeur) {
            if (valeur === true  || valeur === 1) return '🚁 Drone classique';
            if (valeur === false || valeur === 0) return '🚁 Drone assisté';
            return '—';
        }

        /**
         * Convertit le booléen type_environnement en libellé lisible.
         * true  = Extérieur
         * false = Intérieur
         */
        function labelEnvironnement(valeur) {
            if (valeur === true  || valeur === 1) return '🌳 Extérieur';
            if (valeur === false || valeur === 0) return '🏠 Intérieur';
            return '—';
        }

        /**
         * Convertit le code ciel (0-3) en emoji + libellé.
         */
        function labelCiel(code) {
            const ciels = {
                0: '☀️ Dégagé',
                1: '⛅ Nuageux',
                2: '☁️ Couvert',
                3: '🌧️ Pluvieux'
            };
            return ciels[code] ?? '—';
        }

        /**
         * Convertit le booléen jour en libellé.
         */
        function labelJour(valeur) {
            if (valeur === true  || valeur === 1) return '🌞 Jour';
            if (valeur === false || valeur === 0) return '🌙 Nuit';
            return '—';
        }

        // ════════════════════════════════════════════════════════
        // BOUTONS EXPORT
        // ════════════════════════════════════════════════════════
        function mettreAJourBoutonsExport() {
            $('#btnCsv, #btnPdf').prop('disabled', false).attr('title', '');
        }

        // ════════════════════════════════════════════════════════
        // ÉCOUTE DES FILTRES
        // ════════════════════════════════════════════════════════
        $('#selectClasse, #selectObjectif').on('change', function () {
            mettreAJourBoutonsExport();
            chargerDonnees($('#selectClasse').val(), $('#selectObjectif').val());
        });

        // ════════════════════════════════════════════════════════
        // EXPORT CSV
        // ════════════════════════════════════════════════════════
        $('#btnCsv').on('click', function () {
            const params = {};
            const idClasse   = $('#selectClasse').val();
            const idObjectif = $('#selectObjectif').val();
            if (idClasse)   params.id_classe   = idClasse;
            if (idObjectif) params.id_objectif = idObjectif;
            const qs = $.param(params);
            window.location.href = urlCsv + (qs ? '?' + qs : '');
        });

        // ════════════════════════════════════════════════════════
        // EXPORT PDF
        // ════════════════════════════════════════════════════════
        $('#btnPdf').on('click', function () {
            if ($(this).prop('disabled')) return;
            const params = {};
            const idClasse   = $('#selectClasse').val();
            const idObjectif = $('#selectObjectif').val();
            if (idClasse)   params.id_classe   = idClasse;
            if (idObjectif) params.id_objectif = idObjectif;
            const qs = $.param(params);
            window.open(urlPdf + (qs ? '?' + qs : ''), '_blank');
        });

        // ════════════════════════════════════════════════════════
        // AJAX — CHARGE LES DONNÉES
        // ════════════════════════════════════════════════════════
        function chargerDonnees(idClasse, idObjectif) {

            $('#spinner').show();
            $('#chartZone').hide();

            $.ajax({
                url    : urlFiltrer,
                method : 'GET',
                data   : { id_classe: idClasse, id_objectif: idObjectif },

                success: function (data) {

                    console.group('📡 filtrer() — réponse AJAX');
                    console.log('Nombre d\'apprentis :', data.length);
                    console.log('Filtres actifs     :', { id_classe: idClasse, id_objectif: idObjectif });
                    console.log('JSON complet       :', data);
                    if (data.length > 0) {
                        console.log('Structure session  :', data[0].sessions[0] ?? 'aucune session');
                    }
                    console.groupEnd();

                    tousLesApprentis = data;
                    afficherTableau(data);

                    if (data.length > 0) {
                        $('#chartZone').show();
                        setTimeout(function () {
                            initChart(data, getObjectifNom(idObjectif));
                        }, 100);
                    }
                },

                error: function (xhr) {
                    console.group('❌ ERREUR filtrer()');
                    console.error('Status :', xhr.status);
                    console.error('Réponse:', xhr.responseText);
                    console.groupEnd();
                    $('#tableZone').html('<p class="msg-info" style="color:red;">Erreur lors du chargement des données.</p>');
                },

                complete: function () {
                    $('#spinner').hide();
                }
            });
        }

        // ════════════════════════════════════════════════════════
        // CONSTRUCTION DU DATATABLE
        // ════════════════════════════════════════════════════════
        function afficherTableau(data) {
            
            if (tableInstance) {
                tableInstance.destroy();
                tableInstance = null;
            }

            if (data.length === 0) {
                $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
                return;
            }

            let $tbody = $('<tbody>');
            $.each(data, function (index, a) {
                $('<tr>').attr('data-index', index)
                    .append($('<td>').text(a.nom))
                    .append($('<td>').text(a.prenom))
                    .append($('<td>').text(a.classe))
                    .append($('<td>').text(a.nb_sessions))
                    .appendTo($tbody);
            });

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

            tableInstance = $('#apprentisTable').DataTable({
                language  : { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order     : [[0, 'asc']]
            });

            $('#apprentisTable tbody').on('click', 'tr', function () {
                ouvrirModale(tousLesApprentis[$(this).data('index')]);
            });
        }

        // ════════════════════════════════════════════════════════
        // MODALE — détail sessions + objectifs d'un apprenti
        // ════════════════════════════════════════════════════════
        function ouvrirModale(apprenti) {

            $('#modalNom').text(apprenti.nom + ' ' + apprenti.prenom);
            $('#modalClasse').text('📚 ' + apprenti.classe);

            let $container = $('#modalSessions').empty();

            if (!apprenti.sessions || apprenti.sessions.length === 0) {
                $container.append($('<p class="empty-obj">').text('Aucune session réalisée.'));
                $('#modalOverlay').addClass('active');
                return;
            }

            $.each(apprenti.sessions, function (i, session) {

                const reussis = $.grep(session.objectifs, o => o.reussi);
                const echoues = $.grep(session.objectifs, o => !o.reussi);

                // ── Badges météo ──
                const meteoHtml =
                    '<span class="badge badge-meteo">' + labelJour(session.jour)         + '</span> ' +
                    '<span class="badge badge-meteo">' + labelCiel(session.ciel)          + '</span> ' +
                    '<span class="badge badge-drone">' + labelDrone(session.type_drone)   + '</span> ' +
                    '<span class="badge badge-env">'   + labelEnvironnement(session.type_environnement) + '</span>';

                // ── En-tête de session ──
                let $header = $('<div class="session-header">')
                    .append($('<span class="session-titre">').text('Session ' + (i + 1) + ' — ' + session.date))
                    .append($('<div class="session-badges">').html(meteoHtml));

                // ── Corps : réussis ──
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

                // ── Corps : échoués ──
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

        $('#modalOverlay').on('click', function (e) {
            if ($(e.target).is('#modalOverlay')) fermerModale();
        });

        // ── Chargement initial ──
        mettreAJourBoutonsExport();
        chargerDonnees('', '');

    </script>

@endsection