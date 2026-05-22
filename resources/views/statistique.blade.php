@extends('layouts.layout')

@section('title', 'Statistiques')

@section('content')

    <div class="page-title">Statistiques</div>
    <p class="page-sub">Filtrez par classe et/ou par objectif — exportez en CSV ou PDF</p>

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
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);">
            Exporter :
        </span>
        <button id="btnCsv" class="btn-app btn-app-export-csv">⬇ CSV</button>
        <button id="btnPdf" class="btn-app btn-app-info">⬇ PDF</button>
    </div>

    {{-- ════ TABLEAU ════ --}}
    <div class="card-dark mb-4" id="tableZone">
        <p class="msg-info">Chargement des données…</p>
    </div>

    {{-- ════ GRAPHIQUE ════ --}}
    <div id="chartZone" style="display:none;" class="card-dark mb-4">
        <div id="chartContainer"></div>
        <p class="chart-description">
            Réussi <span style="color:#22c55e">●</span>
            Échoué <span style="color:#ef4444">●</span>
            Non tenté <span style="color:#475569">●</span>
        </p>
    </div>

    {{-- ════ MODALE DÉTAIL APPRENTI ════ --}}
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">

            <div class="modal-header-bar">
                <div class="modal-identite">
                    <div class="modal-nom"        id="modalNom"></div>
                    <div class="modal-classe-badge" id="modalClasse"></div>
                </div>
                <button class="btn-app btn-app-danger" onclick="fermerModale()">✕ Fermer</button>
            </div>

            <div class="modal-scroll-zone">
                <div id="modalSessions"></div>
            </div>

            <div class="modal-footer-bar">
                <div class="modal-legende-item"><div class="modal-legende-dot success"></div> Réussi</div>
                <div class="modal-legende-item"><div class="modal-legende-dot danger"></div>  Échoué</div>
                <div class="modal-legende-item"><div class="modal-legende-dot muted"></div>   Non tenté</div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/libs/highcharts.js') }}"></script>
<script src="{{ asset('js/libs/highcharts-more.js') }}"></script>
<script src="{{ asset('js/libs/exporting.js') }}"></script>
<script src="{{ asset('js/libs/accessibility.js') }}"></script>
<script src="{{ asset('js/libs/Statistiquechart.js') }}"></script>

<script>
    let tableInstance    = null;
    let tousLesApprentis = [];
    let tousLesObjectifs = @json($objectifs);

    const urlFiltrer = '{{ route('statistique.filtrer') }}';
    const urlCsv     = '{{ route('statistique.csv') }}';
    const urlPdf     = '{{ route('statistique.pdf') }}';

    // ── Helpers ──────────────────────────────────────────────────
    function getObjectifNom(id) {
        if (!id) return null;
        const o = tousLesObjectifs.find(o => o.id_objectif == id);
        return o ? o.libelle_objectif : null;
    }
    function labelDrone(v)         { return (v === true || v === 1) ? '🚁 Classique' : (v === false || v === 0) ? '🚁 Assisté' : '—'; }
    function labelEnvironnement(v) { return (v === true || v === 1) ? '🌳 Extérieur' : (v === false || v === 0) ? '🏠 Intérieur' : '—'; }
    function labelCiel(c)          { return ({0:'☀️ Dégagé',1:'⛅ Nuageux',2:'☁️ Couvert',3:'🌧️ Pluvieux'})[c] ?? '—'; }
    function labelJour(v)          { return (v === true || v === 1) ? '🌞 Jour' : (v === false || v === 0) ? '🌙 Nuit' : '—'; }

    function celluleObjectif(etat, qr, qa) {
        if (etat === 'reussi')   return '<span class="obj-cell obj-reussi">✓ '   + qr + '/' + qa + '</span>';
        if (etat === 'echoue')   return '<span class="obj-cell obj-echoue">✗ '   + qr + '/' + qa + '</span>';
        return '<span class="obj-cell obj-nontente">— n/a</span>';
    }

    // ── Exports ──────────────────────────────────────────────────
    $('#btnCsv').on('click', function () {
        const p = {};
        const c = $('#selectClasse').val(), o = $('#selectObjectif').val();
        if (c) p.id_classe = c; if (o) p.id_objectif = o;
        window.location.href = urlCsv + ($.param(p) ? '?' + $.param(p) : '');
    });
    $('#btnPdf').on('click', function () {
        const p = {};
        const c = $('#selectClasse').val(), o = $('#selectObjectif').val();
        if (c) p.id_classe = c; if (o) p.id_objectif = o;
        window.open(urlPdf + ($.param(p) ? '?' + $.param(p) : ''), '_blank');
    });

    // ── Filtres ───────────────────────────────────────────────────
    $('#selectClasse, #selectObjectif').on('change', function () {
        chargerDonnees($('#selectClasse').val(), $('#selectObjectif').val());
    });

    // ── AJAX ──────────────────────────────────────────────────────
    function chargerDonnees(idClasse, idObjectif) {
        $('#spinner').show();
        $('#chartZone').hide();
        $.ajax({
            url   : urlFiltrer,
            method: 'GET',
            data  : { id_classe: idClasse, id_objectif: idObjectif },
            success: function (data) {
                console.group('📡 filtrer()');
                console.log('Apprentis :', data.length, '| Filtres :', { idClasse, idObjectif });
                console.log('JSON      :', data);
                console.groupEnd();
                tousLesApprentis = data;
                afficherTableau(data);
                if (data.length > 0) {
                    $('#chartZone').show();
                    setTimeout(() => initChart(data, getObjectifNom(idObjectif)), 100);
                }
            },
            error: function (xhr) {
                console.error('❌ Erreur filtrer()', xhr.status, xhr.responseText);
                $('#tableZone').html('<p class="msg-info" style="color:#f87171;">Erreur lors du chargement.</p>');
            },
            complete: function () { $('#spinner').hide(); }
        });
    }

    // ── DataTable ─────────────────────────────────────────────────
    function afficherTableau(data) {
        if (tableInstance) { tableInstance.destroy(); tableInstance = null; }

        if (data.length === 0) {
            $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
            return;
        }

        let $tbody = $('<tbody>');
        $.each(data, function (index, a) {
            $('<tr>').addClass('clickable').attr('data-index', index)
                .append($('<td>').text(a.nom))
                .append($('<td>').text(a.prenom))
                .append($('<td>').html('<span class="badge-app badge-app-accent">' + a.classe + '</span>'))
                .append($('<td>').text(a.nb_sessions))
                .appendTo($tbody);
        });

        const $table = $('<table id="apprentisTable" class="table dataTable w-100">')
            .append($('<thead>').append(
                $('<tr>')
                    .append($('<th>').text('Nom'))
                    .append($('<th>').text('Prénom'))
                    .append($('<th>').text('Classe'))
                    .append($('<th>').text('Sessions'))
            ))
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

    // ── Modale ────────────────────────────────────────────────────
    function ouvrirModale(apprenti) {
        $('#modalNom').text(apprenti.nom + ' ' + apprenti.prenom);
        $('#modalClasse').text('📚 ' + apprenti.classe);

        const $container = $('#modalSessions').empty();

        if (!apprenti.sessions || apprenti.sessions.length === 0) {
            $container.append($('<p class="msg-info">').text('Aucune session réalisée.'));
            $('#modalOverlay').addClass('active');
            return;
        }

        // Collecte tous les objectifs présents toutes sessions confondues
        const tousNoms = [];
        $.each(apprenti.sessions, function (i, s) {
            $.each(s.objectifs, function (j, o) {
                if (!tousNoms.includes(o.libelle)) tousNoms.push(o.libelle);
            });
        });

        // Construction du tableau
        const $thead = $('<thead>').append(
            $('<tr>').append($('<th>').text('Session'))
        );
        $.each(tousNoms, function (i, n) { $thead.find('tr').append($('<th>').text(n)); });
        $thead.find('tr')
            .append($('<th>').text('Drone'))
            .append($('<th>').text('Météo'))
            .append($('<th>').text('Environnement'))
            .append($('<th>').text('Jour / Nuit'));

        const $tbody = $('<tbody>');
        $.each(apprenti.sessions, function (i, session) {
            const objMap = {};
            $.each(session.objectifs, function (j, o) {
                objMap[o.libelle] = { reussi: o.reussi, qr: o.quantite_realisee, qa: o.quantite_a_atteindre };
            });

            const $tr = $('<tr>')
                .append($('<td>').html(
                    '<strong>S' + (i + 1) + '</strong><br>'
                    + '<span style="font-size:0.72rem;color:var(--muted);">' + session.date + '</span>'
                ));

            $.each(tousNoms, function (j, nom) {
                if (objMap[nom] !== undefined) {
                    const etat = objMap[nom].reussi ? 'reussi' : 'echoue';
                    $tr.append($('<td>').html(celluleObjectif(etat, objMap[nom].qr, objMap[nom].qa)));
                } else {
                    $tr.append($('<td>').html(celluleObjectif('nontente', 0, 0)));
                }
            });

            const ext = (session.type_environnement === true || session.type_environnement === 1);

            $tr.append($('<td>').html('<span class="badge-app badge-app-info">' + labelDrone(session.type_drone) + '</span>'))
               .append($('<td>').html(ext
                    ? '<span class="badge-app badge-app-info">' + labelCiel(session.ciel) + '</span>'
                    : '<span style="color:var(--muted);font-size:0.75rem;">—</span>'
               ))
               .append($('<td>').html('<span class="badge-app badge-app-info">' + labelEnvironnement(session.type_environnement) + '</span>'))
               .append($('<td>').html('<span class="badge-app badge-app-info">' + labelJour(session.jour) + '</span>'));

            $tbody.append($tr);
        });

        $container.append($('<table class="sessions-table">').append($thead).append($tbody));
        $('#modalOverlay').addClass('active');
    }

    function fermerModale() { $('#modalOverlay').removeClass('active'); }

    $('#modalOverlay').on('click', function (e) {
        if ($(e.target).is('#modalOverlay')) fermerModale();
    });

    // Chargement initial
    chargerDonnees('', '');
</script>
@endpush