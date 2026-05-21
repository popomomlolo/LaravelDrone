@extends('layouts.layout')

@section('title', 'Statistiques')

@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        /* ── Curseur pointer sur les lignes du tableau ── */
        #apprentisTable tbody tr {
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
        }
        #apprentisTable tbody tr:hover {
            background: rgba(99, 179, 237, 0.12) !important;
            box-shadow: inset 3px 0 0 #63b3ed;
            transform: translateX(2px);
        }
        #apprentisTable tbody tr:hover td {
            color: #e2e8f0;
        }

        /* ── Modale ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-box {
            background: #1a1a2e;
            color: #e2e8f0;
            border: 1px solid #2a2f3d;
            border-radius: 14px;
            width: 95vw;
            max-width: 1300px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
            overflow: hidden;
        }
        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #2a2f3d;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            background: #12122a;
        }
        .modal-identite {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .modal-nom {
            font-size: 1.2rem;
            font-weight: 700;
            color: #e2e8f0;
        }
        .modal-classe {
            font-size: 0.85rem;
            color: #94a3b8;
            background: #2a2f3d;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
        }
        .modal-close {
            background: #2a2f3d;
            border: none;
            color: #94a3b8;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
        }
        .modal-close:hover {
            background: #ef4444;
            color: #fff;
        }

        /* ── Zone scrollable du tableau de sessions ── */
        .modal-scroll-zone {
            overflow: auto;
            flex: 1;
            padding: 1.25rem 1.5rem;
        }

        /* ── Tableau des sessions ── */
        .sessions-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.82rem;
            white-space: nowrap;
            min-width: 900px;
        }
        .sessions-table thead tr th {
            background: #0d0f1e;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            padding: 0.6rem 0.9rem;
            border-bottom: 2px solid #2a2f3d;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .sessions-table thead tr th:first-child { border-radius: 8px 0 0 0; }
        .sessions-table thead tr th:last-child  { border-radius: 0 8px 0 0; }

        .sessions-table tbody tr {
            transition: background 0.12s;
        }
        .sessions-table tbody tr:nth-child(even) {
            background: rgba(255,255,255,0.02);
        }
        .sessions-table tbody tr:hover {
            background: rgba(99, 179, 237, 0.07);
        }
        .sessions-table tbody td {
            padding: 0.55rem 0.9rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
            color: #cbd5e1;
        }
        .sessions-table tbody td:first-child {
            color: #e2e8f0;
            font-weight: 600;
        }

        /* ── Cellules objectifs ── */
        .obj-cell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 90px;
        }
        .obj-reussi   { background: rgba(34,197,94,0.15);  color: #22c55e; border: 1px solid rgba(34,197,94,0.3);  }
        .obj-echoue   { background: rgba(239,68,68,0.15);  color: #ef4444; border: 1px solid rgba(239,68,68,0.3);  }
        .obj-nontente { background: rgba(148,163,184,0.1); color: #64748b; border: 1px solid rgba(148,163,184,0.2); font-style: italic; }

        /* ── Badges info ── */
        .badge-info {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            font-size: 0.75rem;
            background: rgba(99,179,237,0.1);
            color: #93c5fd;
            border: 1px solid rgba(99,179,237,0.2);
        }
        .badge-empty {
            color: #374151;
            font-style: italic;
            font-size: 0.75rem;
        }

        /* ── Légende ── */
        .modal-legende {
            display: flex;
            gap: 1.2rem;
            padding: 0.75rem 1.5rem;
            border-top: 1px solid #2a2f3d;
            flex-shrink: 0;
            background: #12122a;
            flex-wrap: wrap;
        }
        .legende-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .legende-dot {
            width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0;
        }
        .legende-dot.reussi   { background: #22c55e; }
        .legende-dot.echoue   { background: #ef4444; }
        .legende-dot.nontente { background: #374151; }
    </style>

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
                échoué <span style="color:#ef4444">●</span>
                ou non tenté <span style="color:#64748b">●</span> chaque objectif
            </p>
        </div>

    </div>

    {{-- ════ MODALE DÉTAIL APPRENTI ════ --}}
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">

            {{-- En-tête --}}
            <div class="modal-header">
                <div class="modal-identite">
                    <div class="modal-nom"    id="modalNom"></div>
                    <div class="modal-classe" id="modalClasse"></div>
                </div>
                <button class="modal-close" onclick="fermerModale()">✕</button>
            </div>

            {{-- Tableau scrollable --}}
            <div class="modal-scroll-zone">
                <div id="modalSessions"></div>
            </div>

            {{-- Légende --}}
            <div class="modal-legende">
                <div class="legende-item"><div class="legende-dot reussi"></div> Réussi</div>
                <div class="legende-item"><div class="legende-dot echoue"></div> Échoué</div>
                <div class="legende-item"><div class="legende-dot nontente"></div> Non tenté</div>
            </div>

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

        // ── Variables globales ──────────────────────────────────────
        let tableInstance    = null;
        let tousLesApprentis = [];
        let tousLesObjectifs = @json($objectifs);

        const urlFiltrer = '{{ route('statistique.filtrer') }}';
        const urlCsv     = '{{ route('statistique.csv') }}';
        const urlPdf     = '{{ route('statistique.pdf') }}';

        // ════════════════════════════════════════════════════════════
        // HELPERS
        // ════════════════════════════════════════════════════════════

        function getObjectifNom(idObjectif) {
            if (!idObjectif) return null;
            const obj = tousLesObjectifs.find(o => o.id_objectif == idObjectif);
            return obj ? obj.libelle_objectif : null;
        }

        function labelDrone(v) {
            if (v === true  || v === 1) return '🚁 Classique';
            if (v === false || v === 0) return '🚁 Assisté';
            return '—';
        }

        function labelEnvironnement(v) {
            if (v === true  || v === 1) return '🌳 Extérieur';
            if (v === false || v === 0) return '🏠 Intérieur';
            return '—';
        }

        function labelCiel(code) {
            return { 0:'☀️ Dégagé', 1:'⛅ Nuageux', 2:'☁️ Couvert', 3:'🌧️ Pluvieux' }[code] ?? '—';
        }

        function labelJour(v) {
            if (v === true  || v === 1) return '🌞 Jour';
            if (v === false || v === 0) return '🌙 Nuit';
            return '—';
        }

        // Rendu d'une cellule objectif selon son état
        function celluleObjectif(etat, qr, qa) {
            if (etat === 'reussi') {
                return '<span class="obj-cell obj-reussi">✓ ' + qr + '/' + qa + '</span>';
            }
            if (etat === 'echoue') {
                return '<span class="obj-cell obj-echoue">✗ ' + qr + '/' + qa + '</span>';
            }
            // non tenté
            return '<span class="obj-cell obj-nontente">— n/a</span>';
        }

        // ════════════════════════════════════════════════════════════
        // BOUTONS EXPORT
        // ════════════════════════════════════════════════════════════
        function mettreAJourBoutonsExport() {
            $('#btnCsv, #btnPdf').prop('disabled', false).attr('title', '');
        }

        // ════════════════════════════════════════════════════════════
        // ÉCOUTE DES FILTRES
        // ════════════════════════════════════════════════════════════
        $('#selectClasse, #selectObjectif').on('change', function () {
            mettreAJourBoutonsExport();
            chargerDonnees($('#selectClasse').val(), $('#selectObjectif').val());
        });

        // ════════════════════════════════════════════════════════════
        // EXPORTS
        // ════════════════════════════════════════════════════════════
        $('#btnCsv').on('click', function () {
            const params = {};
            const idClasse = $('#selectClasse').val(), idObjectif = $('#selectObjectif').val();
            if (idClasse)   params.id_classe   = idClasse;
            if (idObjectif) params.id_objectif = idObjectif;
            const qs = $.param(params);
            window.location.href = urlCsv + (qs ? '?' + qs : '');
        });

        $('#btnPdf').on('click', function () {
            if ($(this).prop('disabled')) return;
            const params = {};
            const idClasse = $('#selectClasse').val(), idObjectif = $('#selectObjectif').val();
            if (idClasse)   params.id_classe   = idClasse;
            if (idObjectif) params.id_objectif = idObjectif;
            const qs = $.param(params);
            window.open(urlPdf + (qs ? '?' + qs : ''), '_blank');
        });

        // ════════════════════════════════════════════════════════════
        // AJAX — CHARGE LES DONNÉES
        // ════════════════════════════════════════════════════════════
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

                complete: function () { $('#spinner').hide(); }
            });
        }

        // ════════════════════════════════════════════════════════════
        // DATATABLE
        // ════════════════════════════════════════════════════════════
        function afficherTableau(data) {

            if (tableInstance) { tableInstance.destroy(); tableInstance = null; }

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

        // ════════════════════════════════════════════════════════════
        // MODALE — tableau des sessions
        // ════════════════════════════════════════════════════════════
        function ouvrirModale(apprenti) {

            $('#modalNom').text(apprenti.nom + ' ' + apprenti.prenom);
            $('#modalClasse').text('📚 ' + apprenti.classe);

            let $container = $('#modalSessions').empty();

            if (!apprenti.sessions || apprenti.sessions.length === 0) {
                $container.append($('<p style="color:#94a3b8;padding:1rem;">').text('Aucune session réalisée.'));
                $('#modalOverlay').addClass('active');
                return;
            }

            // ── Récupère la liste complète des objectifs à partir des données ──
            // On parcourt toutes les sessions pour trouver tous les objectifs existants
            const tousObjectifsNoms = [];
            $.each(apprenti.sessions, function (i, session) {
                $.each(session.objectifs, function (j, obj) {
                    if (!tousObjectifsNoms.includes(obj.libelle)) {
                        tousObjectifsNoms.push(obj.libelle);
                    }
                });
            });

            // ── Construction du tableau ──
            let $table = $('<table class="sessions-table">');

            // En-tête : Session | [objectifs...] | Drone | Météo | Environnement | Jour/Nuit
            let $thead = $('<thead>').append(
                $('<tr>')
                    .append($('<th>').text('Session'))
            );
            $.each(tousObjectifsNoms, function (i, nom) {
                $thead.find('tr').append($('<th>').text(nom));
            });
            $thead.find('tr')
                .append($('<th>').text('Drone'))
                .append($('<th>').text('Météo'))
                .append($('<th>').text('Environnement'))
                .append($('<th>').text('Jour / Nuit'));
            $table.append($thead);

            // Corps
            let $tbody = $('<tbody>');
            $.each(apprenti.sessions, function (i, session) {

                // Construit un map libelle → {reussi, qr, qa} pour cette session
                const objMap = {};
                $.each(session.objectifs, function (j, obj) {
                    objMap[obj.libelle] = {
                        reussi: obj.reussi,
                        qr    : obj.quantite_realisee,
                        qa    : obj.quantite_a_atteindre
                    };
                });

                let $tr = $('<tr>');

                // Col 1 — Numéro + date
                $tr.append(
                    $('<td>')
                        .html('<strong>S' + (i + 1) + '</strong><br><span style="font-size:0.72rem;color:#94a3b8;">' + session.date + '</span>')
                );

                // Cols objectifs
                $.each(tousObjectifsNoms, function (j, nom) {
                    if (objMap[nom] !== undefined) {
                        const etat = objMap[nom].reussi ? 'reussi' : 'echoue';
                        $tr.append($('<td>').html(celluleObjectif(etat, objMap[nom].qr, objMap[nom].qa)));
                    } else {
                        $tr.append($('<td>').html(celluleObjectif('nontente', 0, 0)));
                    }
                });

                // Col Drone
                $tr.append($('<td>').html('<span class="badge-info">' + labelDrone(session.type_drone) + '</span>'));

                // Col Météo (vide si intérieur)
                const estInterieur = (session.type_environnement === false || session.type_environnement === 0);
                if (estInterieur) {
                    $tr.append($('<td>').html('<span class="badge-empty">—</span>'));
                } else {
                    $tr.append($('<td>').html('<span class="badge-info">' + labelCiel(session.ciel) + '</span>'));
                }

                // Col Environnement
                $tr.append($('<td>').html('<span class="badge-info">' + labelEnvironnement(session.type_environnement) + '</span>'));

                // Col Jour / Nuit
                $tr.append($('<td>').html('<span class="badge-info">' + labelJour(session.jour) + '</span>'));

                $tbody.append($tr);
            });

            $table.append($tbody);
            $container.append($table);
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