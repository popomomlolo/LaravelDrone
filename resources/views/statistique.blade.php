@extends('layouts.layout')

@section('title', 'Statistiques')

@section('content')

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
    >

    <style>
        .export-zone {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
            margin-bottom: 2rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
        }

        .export-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .export-group label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #636b6f;
        }

        .export-group select {
            padding: 0.45rem 0.9rem;
            font-size: 0.875rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            min-width: 200px;
            background: #fff;
            cursor: pointer;
        }

        .export-group select:focus {
            outline: none;
            border-color: #636b6f;
        }

        .export-buttons {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .btn-export {
            padding: 0.5rem 1.1rem;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: opacity 0.2s;
        }

        .btn-export:hover {
            opacity: 0.85;
        }

        .btn-csv {
            background: #22543d;
            color: #fff;
        }

        .btn-pdf {
            background: #c53030;
            color: #fff;
        }
    </style>

    <div class="page-wrap">

        <div class="page-title">Statistiques</div>
        <p class="page-sub">Filtrez par classe et/ou par objectif — exportez en CSV ou PDF</p>

        {{-- ── Combobox filtres ── --}}
        <div class="filtres">
            <div class="filtre-group">
                <label for="selectClasse">Classe</label>
                <select id="selectClasse">
                    <option value="">-- Toutes les classes --</option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classes }}">{{ $classe->libelle_classes }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filtre-group">
                <label for="selectObjectif">Objectif</label>
                <select id="selectObjectif">
                    <option value="">-- Tous les objectifs --</option>
                    @foreach ($objectifs as $objectif)
                        <option value="{{ $objectif->id_objectifs }}">{{ $objectif->libelle_objectifs }}</option>
                    @endforeach
                </select>
            </div>

            <div
                class="spinner"
                id="spinner"
            ></div>
        </div>

        {{-- ── Zone export CSV/PDF ── --}}
        <div class="export-zone">

            <div class="export-group">
                <!--<label for="exportNom">Filtrer par nom (CSV)</label>
                    <select id="exportNom">
                        <option value="">-- Tous les apprentis --</option>
                        @foreach ($apprentis as $a)
    <option value="{{ $a->id_apprentis }}">
                                {{ $a->nom }} {{ $a->prenom }}
                            </option>
    @endforeach
                    </select>-->
            </div>

            <div class="export-group">
                <label for="exportClasse">Filtrer par classe (CSV)</label>
                <select id="exportClasse">
                    <option value="">-- Toutes les classes --</option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classes }}">{{ $classe->libelle_classes }}</option>
                    @endforeach
                </select>
            </div>

            <div class="export-buttons">
                <button
                    class="btn-export btn-csv"
                    onclick="exporterCsv()"
                >
                    ⬇ Export CSV
                </button>
                <a
                    href="{{ route('statistique.pdf') }}"
                    class="btn-export btn-pdf"
                    target="_blank"
                >
                    ⬇ Export PDF
                </a>
            </div>

        </div>

        {{-- ── Zone tableau AJAX ── --}}
        <div id="tableZone">
            <p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>
        </div>

        {{-- ── Zone graphique ── --}}
        <div
            id="chartZone"
            style="display:none;"
        >
            <div id="chartContainer"></div>
            <p class="chart-description">
                Nombre d'apprentis ayant réussi <span style="color:#22c55e">●</span>
                ou échoué <span style="color:#ef4444">●</span> chaque objectif
            </p>
        </div>

    </div>

    {{-- ── Modale ── --}}
    <div
        class="modal-overlay"
        id="modalOverlay"
    >
        <div class="modal-box">
            <button
                class="modal-close"
                onclick="fermerModale()"
            >✕</button>
            <div class="modal-identite">
                <div
                    class="modal-nom"
                    id="modalNom"
                ></div>
                <div
                    class="modal-classe"
                    id="modalClasse"
                ></div>
            </div>
            <div id="modalSessions"></div>
        </div>
    </div>

    {{-- ── Scripts ── --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/libs/highcharts.js') }}"></script>
    <script src="{{ asset('js/libs/highcharts-more.js') }}"></script>
    <script src="{{ asset('js/libs/exporting.js') }}"></script>
    <script src="{{ asset('js/libs/accessibility.js') }}"></script>
    <script src="{{ asset('js/libs/Statistiquechart.js') }}"></script>

    <script>
        let tableInstance = null;
        let tousLesApprentis = [];
        const urlFiltrer = '{{ route('statistique.filtrer') }}';
        const urlCsv = '{{ route('statistique.csv') }}';

        // ── Combobox filtres tableau ──
        $('#selectClasse, #selectObjectif').on('change', function() {
            const idClasse = $('#selectClasse').val();
            const idObjectif = $('#selectObjectif').val();

            if (!idClasse && !idObjectif) {
                $('#tableZone').html(
                    '<p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>');
                $('#chartZone').hide();
                if (tableInstance) {
                    tableInstance.destroy();
                    tableInstance = null;
                }
                return;
            }

            chargerDonnees(idClasse, idObjectif);
        });

        // ── AJAX ──
        function chargerDonnees(idClasse, idObjectif) {
            $('#spinner').show();
            $('#chartZone').hide();

            $.ajax({
                url: urlFiltrer,
                method: 'GET',
                data: {
                    id_classes: idClasse,
                    id_objectifs: idObjectif
                },
                success: function(data) {
                    tousLesApprentis = data;
                    afficherTableau(data);

                    if (data.length > 0) {
                        $('#chartZone').show();
                        setTimeout(function() {
                            initChart(data);
                        }, 100);
                    }
                },
                error: function() {
                    $('#tableZone').html(
                        '<p class="msg-info" style="color:red;">Erreur lors du chargement.</p>');
                },
                complete: function() {
                    $('#spinner').hide();
                }
            });
        }

        // ── Export CSV ──
        function exporterCsv() {
            const idApprentis = $('#exportNom').val();
            const idClasses = $('#exportClasse').val();

            let url = urlCsv + '?';
            if (idApprentis) url += 'id_apprentis=' + idApprentis + '&';
            if (idClasses) url += 'id_classes=' + idClasses;

            window.location.href = url;
        }

        // ── DataTable ──
        function afficherTableau(data) {
                $(document).ready(function() {
                    $('#tab').DataTable({
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/2.3.7/i18n/fr-FR.json"
                        }
                    });
                });
                if (tableInstance) {
                    tableInstance.destroy();
                }

                if (data.length === 0) {
                    $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
                    return;
                }

                let rows = '';
                data.forEach(function(a, index) {
                    rows += '<tr data-index="' + index + '">' +
                        '<td>' + a.nom + '</td>' +
                        '<td>' + a.prenom + '</td>' +
                        '<td>' + a.classe + '</td>' +
                        '<td>' + a.nb_sessions + '</td>' +
                        '</tr>';
                });

                $('#tableZone').html(
                    '<table id="apprentisTable" class="display" style="width:100%">' +
                    '<thead><tr><th>Nom</th><th>Prénom</th><th>Classe</th><th>Nb sessions</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody>' +
                    '</table>'
                );

                tableInstance = $('#apprentisTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                    },
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [
                        [0, 'asc']
                    ]
                });

                $('#apprentisTable tbody').on('click', 'tr', function() {
                    ouvrirModale(tousLesApprentis[$(this).data('index')]);
                });
            }

        // ── Modale ──
        function ouvrirModale(apprenti) {
            document.getElementById('modalNom').textContent = apprenti.nom + ' ' + apprenti.prenom;
            document.getElementById('modalClasse').textContent = '📚 ' + apprenti.classe;

            let html = '';

            if (apprenti.sessions.length === 0) {
                html = '<p class="empty-obj">Aucune session réalisée.</p>';
            } else {
                apprenti.sessions.forEach(function(session, i) {
                    const reussis = session.objectifs.filter(function(o) {
                        return o.reussi;
                    });
                    const echoues = session.objectifs.filter(function(o) {
                        return !o.reussi;
                    });

                    const htmlReussis = reussis.length === 0 ?
                        '<p class="empty-obj">Aucun objectif réussi</p>' :
                        reussis.map(function(o) {
                            return '<div class="objectif-item"><span class="dot reussi"></span><span>' + o
                                .libelle + '</span><span class="quantite">' + o.quantite_realisee + ' / ' + o
                                .quantite_a_atteindre + '</span></div>';
                        }).join('');

                    const htmlEchoues = echoues.length === 0 ?
                        '<p class="empty-obj">Aucun objectif échoué</p>' :
                        echoues.map(function(o) {
                            return '<div class="objectif-item"><span class="dot echoue"></span><span>' + o
                                .libelle + '</span><span class="quantite">' + o.quantite_realisee + ' / ' + o
                                .quantite_a_atteindre + '</span></div>';
                        }).join('');

                    html += '<div class="session-block">' +
                        '<div class="session-header">' +
                        '<span class="session-titre">Session ' + (i + 1) + ' — ' + session.date + '</span>' +
                        '<span class="session-meta">' + session.type_drone + ' · ' + session.type_environnement +
                        ' · ' + session.conditions_meteo + '</span>' +
                        '</div>' +
                        '<div class="session-body">' +
                        '<div class="section-label">✓ Objectifs réussis</div>' + htmlReussis +
                        '<div class="section-label">✗ Objectifs échoués</div>' + htmlEchoues +
                        '</div>' +
                        '</div>';
                });
            }

            document.getElementById('modalSessions').innerHTML = html;
            document.getElementById('modalOverlay').classList.add('active');
        }

        function fermerModale() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) fermerModale();
        });
    </script>

@endsection
