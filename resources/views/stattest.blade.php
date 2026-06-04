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
        <span style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);">Exporter :</span>
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

    {{-- ════ DEBUG JSON ════ --}}
    <div class="card-dark mb-4" id="debugZone">
        <div class="debug-header">
            <span class="debug-title">🛠 JSON reçus</span>
            <button id="btnClearDebug" class="btn-app btn-app-secondary btn-sm-app">🗑 Vider</button>
        </div>
        <div id="debugLog">
            <p class="msg-info">Aucune requête effectuée pour l'instant.</p>
        </div>
    </div>

    {{-- ════ MODALE DÉTAIL APPRENTI ════ --}}
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">

            <div class="modal-header-bar">
                <div class="modal-identite">
                    <div class="modal-nom"         id="modalNom"></div>
                    <div class="modal-classe-badge" id="modalClasse"></div>
                </div>
                <button class="btn-app btn-app-danger" onclick="fermerModale()">✕ Fermer</button>
            </div>

            {{-- Spinner chargement détail --}}
            <div id="modalSpinner" class="modal-scroll-zone" style="display:none;align-items:center;justify-content:center;min-height:120px;">
                <div class="spinner" style="display:block;"></div>
            </div>

            <div class="modal-scroll-zone" id="modalSessions"></div>

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

    // ── URLs ────────────────────────────────────────────────────────
    const urlFiltrer   = '{{ route('statistique.filtrer') }}';
    const urlDetail    = '{{ route('statistique.detail', ['id' => '__ID__']) }}';
    const urlChartData = '{{ route('statistique.chartData') }}';
    const urlCsv       = '{{ route('statistique.csv') }}';
    const urlPdf       = '{{ route('statistique.pdf') }}';

    // ── État local ──────────────────────────────────────────────────
    let tableInstance = null;
    let tousLesApprentis = [];   // liste légère [{id, nom, prenom, classe, nb_sessions}]

    // ── Helpers labels ──────────────────────────────────────────────
    function labelDrone(v)         { return (v === true || v === 1) ? '🚁 Classique' : '🚁 Assisté'; }
    function labelEnvironnement(v) { return (v === true || v === 1) ? '🌳 Extérieur' : '🏠 Intérieur'; }
    function labelCiel(c)          { return ({0:'☀️ Dégagé',1:'⛅ Nuageux',2:'☁️ Couvert',3:'🌧️ Pluvieux'})[c] ?? '—'; }
    function labelJour(v)          { return (v === true || v === 1) ? '🌞 Jour' : '🌙 Nuit'; }

    function celluleObjectif(etat, qr, qa) {
        if (etat === 'reussi') return '<span class="obj-cell obj-reussi">✓ ' + qr + '/' + qa + '</span>';
        if (etat === 'echoue') return '<span class="obj-cell obj-echoue">✗ ' + qr + '/' + qa + '</span>';
        return '<span class="obj-cell obj-nontente">— n/a</span>';
    }

    function filtresActifs() {
        return { id_classe: $('#selectClasse').val(), id_objectif: $('#selectObjectif').val() };
    }


    // ════════════════════════════════════════════════════════════════
    // DEBUG — Encadré JSON
    // ════════════════════════════════════════════════════════════════
    let debugCount = 0;

    function logDebug(methode, url, params, reponse) {
        debugCount++;
        const now    = new Date().toLocaleTimeString('fr-FR');
        const id     = 'debug-' + debugCount;
        const urlCourte = url.replace(window.location.origin, '');

        const $entry = $('<div class="debug-entry" id="' + id + '">');

        // En-tête cliquable
        $('<div class="debug-entry-header">')
            .append($('<span class="debug-entry-method">').text(methode))
            .append($('<span class="debug-entry-url">').text(urlCourte))
            .on('click', function () { $entry.toggleClass('open'); })
            .appendTo($entry);

        // Corps
        const $body = $('<div class="debug-entry-body">').appendTo($entry);

        if (params && Object.keys(params).length > 0) {
            $body.append($('<div class="debug-entry-label">').text('Paramètres envoyés'));
            $body.append($('<pre class="debug-pre">').text(JSON.stringify(params, null, 2)));
        }

        $body.append($('<div class="debug-entry-label">').text('Réponse reçue'));
        $body.append($('<pre class="debug-pre">').text(JSON.stringify(reponse, null, 2)));

        // Remplace le message "aucune requête" si présent
        $('#debugLog p.msg-info').remove();
        $('#debugLog').prepend($entry);
    }

    // Bouton vider
    $('#btnClearDebug').on('click', function () {
        $('#debugLog').html('<p class="msg-info">Aucune requête effectuée pour l\'instant.</p>');
        debugCount = 0;
    });

    // ════════════════════════════════════════════════════════════════
    // EXPORTS
    // ════════════════════════════════════════════════════════════════
    $('#btnCsv').on('click', function () {
        const qs = $.param(filtresActifs());
        window.location.href = urlCsv + (qs ? '?' + qs : '');
    });
    $('#btnPdf').on('click', function () {
        const qs = $.param(filtresActifs());
        window.open(urlPdf + (qs ? '?' + qs : ''), '_blank');
    });

    // ════════════════════════════════════════════════════════════════
    // FILTRES
    // ════════════════════════════════════════════════════════════════
    $('#selectClasse, #selectObjectif').on('change', function () {
        chargerTableau();
    });

    // ════════════════════════════════════════════════════════════════
    // APPEL 1 — Liste légère pour le tableau
    // JSON : [{id, nom, prenom, classe, nb_sessions}]
    // ════════════════════════════════════════════════════════════════
    function chargerTableau() {
        $('#spinner').show();
        $('#chartZone').hide();

        const filtres = filtresActifs();

        console.group('📤 GET filtrer()');
        console.log('Filtres :', filtres);
        console.groupEnd();

        $.ajax({
            url    : urlFiltrer,
            method : 'GET',
            data   : filtres,
            success: function (data) {
                console.group('📥 Réponse filtrer()');
                console.log('Nombre d\'apprentis :', data.length);
                console.log('JSON complet       :', data);
                console.groupEnd();

                logDebug('GET', urlFiltrer, filtres, data);
                tousLesApprentis = data;
                afficherTableau(data);

                if (data.length > 0) {
                    chargerGraphique();
                }
            },
            error: function (xhr) {
                console.error('❌ filtrer()', xhr.status);
                $('#tableZone').html('<p class="msg-info" style="color:#f87171;">Erreur chargement.</p>');
            },
            complete: function () { $('#spinner').hide(); }
        });
    }

    // ════════════════════════════════════════════════════════════════
    // APPEL 2 — Données agrégées pour le graphique
    // JSON : { total, objectifs: [{libelle, reussi, echoue, non_tente}] }
    // ════════════════════════════════════════════════════════════════
    function chargerGraphique() {
        const filtres = filtresActifs();

        console.group('📤 GET chartData()');
        console.log('Filtres :', filtres);
        console.groupEnd();

        $.ajax({
            url    : urlChartData,
            method : 'GET',
            data   : filtres,
            success: function (data) {
                console.group('📥 Réponse chartData()');
                console.log('Total :', data.total, '| Objectifs :', data.objectifs);
                console.groupEnd();

                logDebug('GET', urlChartData, filtres, data);
                if (data.objectifs && data.objectifs.length > 0) {
                    $('#chartZone').show();
                    setTimeout(() => initChart(data), 100);
                }
            },
            error: function (xhr) {
                console.error('❌ chartData()', xhr.status);
            }
        });
    }

    // ════════════════════════════════════════════════════════════════
    // APPEL 3 — Détail d'un apprenti (au clic sur la ligne)
    // JSON : {id, nom, prenom, classe, sessions:[...]}
    // ════════════════════════════════════════════════════════════════
    function chargerDetail(apprentiId, nom, prenom, classe) {
        // Ouvre la modale immédiatement avec un spinner
        $('#modalNom').text(nom + ' ' + prenom);
        $('#modalClasse').text('📚 ' + classe);
        $('#modalSessions').empty();
        $('#modalSpinner').css('display', 'flex');
        $('#modalOverlay').addClass('active');

        const url = urlDetail.replace('__ID__', apprentiId);

        console.group('📤 GET detail(' + apprentiId + ')');
        console.groupEnd();

        $.ajax({
            url    : url,
            method : 'GET',
            success: function (data) {
                console.group('📥 Réponse detail()');
                console.log('Sessions :', data.sessions.length);
                console.log('JSON     :', data);
                console.groupEnd();

                logDebug('GET', url, { id: apprentiId }, data);
                $('#modalSpinner').hide();
                afficherSessions(data);
            },
            error: function (xhr) {
                console.error('❌ detail()', xhr.status);
                $('#modalSpinner').hide();
                $('#modalSessions').html('<p class="msg-info" style="color:#f87171;">Erreur chargement du détail.</p>');
            }
        });
    }

    // ════════════════════════════════════════════════════════════════
    // DATATABLE
    // ════════════════════════════════════════════════════════════════
    function afficherTableau(data) {
        if (tableInstance) { tableInstance.destroy(); tableInstance = null; }

        if (data.length === 0) {
            $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
            return;
        }

        const $tbody = $('<tbody>');
        $.each(data, function (index, a) {
            $('<tr>').addClass('clickable').attr('data-id', a.id)
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

        // Clic → charge le détail uniquement maintenant
        $('#apprentisTable tbody').on('click', 'tr', function () {
            const id     = $(this).data('id');
            const ligne  = tousLesApprentis.find(a => a.id == id);
            if (ligne) chargerDetail(ligne.id, ligne.nom, ligne.prenom, ligne.classe);
        });
    }

    // ════════════════════════════════════════════════════════════════
    // RENDU DES SESSIONS DANS LA MODALE
    // ════════════════════════════════════════════════════════════════
    function afficherSessions(apprenti) {
        const $container = $('#modalSessions').empty();

        if (!apprenti.sessions || apprenti.sessions.length === 0) {
            $container.append($('<p class="msg-info">').text('Aucune session réalisée.'));
            return;
        }

        // Collecte tous les objectifs présents toutes sessions confondues
        const tousNoms = [];
        $.each(apprenti.sessions, function (i, s) {
            $.each(s.objectifs, function (j, o) {
                if (!tousNoms.includes(o.libelle)) tousNoms.push(o.libelle);
            });
        });

        // En-tête
        const $thead = $('<thead>').append($('<tr>').append($('<th>').text('Session')));
        $.each(tousNoms, function (i, n) { $thead.find('tr').append($('<th>').text(n)); });
        $thead.find('tr')
            .append($('<th>').text('Drone'))
            .append($('<th>').text('Météo'))
            .append($('<th>').text('Environnement'))
            .append($('<th>').text('Jour / Nuit'));

        // Corps
        const $tbody = $('<tbody>');
        $.each(apprenti.sessions, function (i, session) {
            const objMap = {};
            $.each(session.objectifs, function (j, o) {
                objMap[o.libelle] = { reussi: o.reussi, qr: o.quantite_realisee, qa: o.quantite_a_atteindre };
            });

            const $tr = $('<tr>').append(
                $('<td>').html('<strong>S' + (i + 1) + '</strong><br><span style="font-size:0.72rem;color:var(--muted);">' + session.date + '</span>')
            );

            $.each(tousNoms, function (j, nom) {
                const o = objMap[nom];
                $tr.append($('<td>').html(o
                    ? celluleObjectif(o.reussi ? 'reussi' : 'echoue', o.qr, o.qa)
                    : celluleObjectif('nontente', 0, 0)
                ));
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
    }

    // ════════════════════════════════════════════════════════════════
    // MODALE
    // ════════════════════════════════════════════════════════════════
    function fermerModale() { $('#modalOverlay').removeClass('active'); }

    $('#modalOverlay').on('click', function (e) {
        if ($(e.target).is('#modalOverlay')) fermerModale();
    });

    // ── Chargement initial ──
    chargerTableau();

</script>
@endpush