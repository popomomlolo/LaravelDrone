@extends('layouts.layout')

@section('title', 'TEST — statistiqueControlleur')

@section('content')

<style>
    .test-wrap     { max-width: 900px; padding: 2rem; text-align: left; }
    .test-title    { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
    .test-sub      { color: #636b6f; font-size: 0.85rem; margin-bottom: 2rem; }

    /* Carte de test */
    .test-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .test-card-header {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .test-card-title { font-weight: 700; font-size: 0.9rem; }
    .test-card-desc  { color: #636b6f; font-size: 0.78rem; }
    .test-card-body  { padding: 1rem; }

    /* Formulaire de test */
    .test-form { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .test-form label  { font-size: 0.75rem; font-weight: 600; color: #636b6f; display: block; margin-bottom: 0.3rem; text-transform: uppercase; }
    .test-form select, .test-form input {
        padding: 0.4rem 0.75rem; font-size: 0.85rem;
        border: 1px solid #ccc; border-radius: 6px;
        min-width: 180px; background: #fff;
    }

    /* Boutons */
    .btn-test {
        padding: 0.45rem 1rem; font-size: 0.82rem; font-weight: 600;
        border: none; border-radius: 6px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.2s;
    }
    .btn-test:hover { opacity: 0.8; }
    .btn-run        { background: #2b6cb0; color: #fff; }
    .btn-csv        { background: #22543d; color: #fff; }
    .btn-pdf        { background: #c53030; color: #fff; }

    /* Résultat */
    .result-zone {
        margin-top: 1rem;
        border-top: 1px dashed #e5e7eb;
        padding-top: 0.75rem;
        display: none;
    }

    .result-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .badge-ok    { background: #c6f6d5; color: #22543d; }
    .badge-err   { background: #fed7d7; color: #c53030; }
    .badge-empty { background: #fefcbf; color: #744210; }

    .result-count { font-size: 0.82rem; color: #636b6f; margin-bottom: 0.5rem; }

    /* Tableau résultat */
    .result-table {
        width: 100%; border-collapse: collapse;
        font-size: 0.8rem; margin-top: 0.5rem;
    }
    .result-table th {
        background: #2d3748; color: #fff;
        padding: 5px 8px; text-align: left; font-size: 0.72rem;
    }
    .result-table td {
        padding: 4px 8px; border-bottom: 1px solid #f0f0f0;
    }
    .result-table tr:hover { background: #f7fafc; }

    /* JSON brut */
    .json-raw {
        background: #1a202c; color: #68d391;
        padding: 0.75rem 1rem; border-radius: 6px;
        font-size: 0.75rem; font-family: monospace;
        max-height: 300px; overflow-y: auto;
        white-space: pre-wrap; word-break: break-all;
        margin-top: 0.5rem;
    }

    /* Spinner inline */
    .spin {
        display: none;
        width: 16px; height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #2b6cb0;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-left: 0.5rem;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="test-wrap">

    <div class="test-title">🧪 Tests unitaires — statistiqueControlleur</div>
    <p class="test-sub">
        Vue temporaire de test. Chaque carte teste une méthode du controller.
        Les résultats s'affichent sous chaque carte sans recharger la page.
    </p>

    {{-- ════════════════════════════════════════════════════════════
         TEST 1 — index()
         Vérifie que la page charge bien $classes, $objectifs, $apprentis
    ════════════════════════════════════════════════════════════ --}}
    <div class="test-card">
        <div class="test-card-header">
            <div>
                <div class="test-card-title">TEST 1 — index()</div>
                <div class="test-card-desc">Vérifie que les données de la page (classes, objectifs, apprentis) sont bien chargées</div>
            </div>
            <button class="btn-test btn-run" onclick="testerIndex()">
                ▶ Lancer
                <span class="spin" id="spin1"></span>
            </button>
        </div>
        <div class="test-card-body">
            <ul style="font-size:0.82rem; color:#636b6f; margin:0; padding-left:1.25rem;">
                <li>Classes chargées : <strong id="t1-classes">—</strong></li>
                <li>Objectifs chargés : <strong id="t1-objectifs">—</strong></li>
                <li>Apprentis chargés : <strong id="t1-apprentis">—</strong></li>
            </ul>
            <div class="result-zone" id="res1"></div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         TEST 2 — filtrer() avec différents cas
    ════════════════════════════════════════════════════════════ --}}
    <div class="test-card">
        <div class="test-card-header">
            <div>
                <div class="test-card-title">TEST 2 — filtrer() via AJAX</div>
                <div class="test-card-desc">Teste les 4 cas de filtre — résultat affiché en tableau et JSON brut</div>
            </div>
        </div>
        <div class="test-card-body">

            <div class="test-form">
                <div>
                    <label>Classe</label>
                    <select id="t2-classe">
                        <option value="">-- Aucune --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id_classes }}">{{ $c->libelle_classes }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Objectif</label>
                    <select id="t2-objectif">
                        <option value="">-- Aucun --</option>
                        @foreach ($objectifs as $o)
                            <option value="{{ $o->id_objectifs }}">{{ $o->libelle_objectifs }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-test btn-run" onclick="testerFiltrer()">
                    ▶ Lancer
                    <span class="spin" id="spin2"></span>
                </button>
            </div>

            <div class="result-zone" id="res2"></div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         TEST 3 — exportCsv()
         Déclenche le téléchargement selon les filtres
    ════════════════════════════════════════════════════════════ --}}
    <div class="test-card">
        <div class="test-card-header">
            <div>
                <div class="test-card-title">TEST 3 — exportCsv()</div>
                <div class="test-card-desc">Déclenche le téléchargement du CSV selon les filtres choisis</div>
            </div>
        </div>
        <div class="test-card-body">

            <div class="test-form">
                <div>
                    <label>Classe (optionnel)</label>
                    <select id="t3-classe">
                        <option value="">-- Toutes --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id_classes }}">{{ $c->libelle_classes }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Objectif (optionnel)</label>
                    <select id="t3-objectif">
                        <option value="">-- Tous --</option>
                        @foreach ($objectifs as $o)
                            <option value="{{ $o->id_objectifs }}">{{ $o->libelle_objectifs }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-test btn-csv" onclick="testerCsv()">⬇ Télécharger CSV</button>
            </div>

            <div class="result-zone" id="res3">
                <span class="result-badge badge-ok">CSV déclenché</span>
                <div style="font-size:0.8rem; color:#636b6f;">Vérifiez votre dossier de téléchargements.</div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         TEST 4 — exportPdf()
         Cas 1 (aucun filtre) doit être bloqué
    ════════════════════════════════════════════════════════════ --}}
    <div class="test-card">
        <div class="test-card-header">
            <div>
                <div class="test-card-title">TEST 4 — exportPdf()</div>
                <div class="test-card-desc">Sans filtre → doit retourner 400. Avec filtre → télécharge le PDF.</div>
            </div>
        </div>
        <div class="test-card-body">

            <div class="test-form">
                <div>
                    <label>Classe (optionnel)</label>
                    <select id="t4-classe">
                        <option value="">-- Aucune (→ doit bloquer) --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id_classes }}">{{ $c->libelle_classes }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Objectif (optionnel)</label>
                    <select id="t4-objectif">
                        <option value="">-- Aucun --</option>
                        @foreach ($objectifs as $o)
                            <option value="{{ $o->id_objectifs }}">{{ $o->libelle_objectifs }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-test btn-run" onclick="testerPdfBlocage()">
                    ▶ Tester blocage (aucun filtre)
                    <span class="spin" id="spin4"></span>
                </button>
                <button class="btn-test btn-pdf" onclick="testerPdf()">⬇ Télécharger PDF</button>
            </div>

            <div class="result-zone" id="res4"></div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════
     SCRIPTS DE TEST
════════════════════════════════════════════════════════════ --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    // URLs Laravel → JS
    const urlFiltrer = '{{ route('statistique.filtrer') }}';
    const urlCsv     = '{{ route('statistique.csv') }}';
    const urlPdf     = '{{ route('statistique.pdf') }}';

    // ── Helpers d'affichage ─────────────────────────────────────────

    function afficherOk($zone, message) {
        $zone.html(
            '<span class="result-badge badge-ok">✓ OK</span> ' +
            '<span style="font-size:0.82rem;">' + message + '</span>'
        ).show();
    }

    function afficherErreur($zone, message) {
        $zone.html(
            '<span class="result-badge badge-err">✗ ERREUR</span> ' +
            '<span style="font-size:0.82rem; color:#c53030;">' + message + '</span>'
        ).show();
    }

    function afficherVide($zone) {
        $zone.html(
            '<span class="result-badge badge-empty">⚠ Vide</span> ' +
            '<span style="font-size:0.82rem;">Aucun résultat retourné — vérifiez les filtres.</span>'
        ).show();
    }

    // ── TEST 1 — index() ────────────────────────────────────────────
    // Les données sont déjà dans la page via Blade
    // On vérifie juste qu'elles sont bien présentes
    function testerIndex() {
        $('#spin1').show();

        const nbClasses   = {{ $classes->count() }};
        const nbObjectifs = {{ $objectifs->count() }};
        const nbApprentis = {{ $apprentis->count() }};

        setTimeout(function () {
            $('#t1-classes').text(nbClasses > 0 ? nbClasses + ' ✓' : '0 ✗');
            $('#t1-objectifs').text(nbObjectifs > 0 ? nbObjectifs + ' ✓' : '0 ✗');
            $('#t1-apprentis').text(nbApprentis > 0 ? nbApprentis + ' ✓' : '0 ✗');

            const $res = $('#res1');
            if (nbClasses > 0 && nbObjectifs > 0 && nbApprentis > 0) {
                afficherOk($res, 'index() charge bien les 3 collections.');
            } else {
                afficherErreur($res, 'Une ou plusieurs collections sont vides. Vérifiez la BDD.');
            }

            $('#spin1').hide();
        }, 300);
    }

    // ── TEST 2 — filtrer() ──────────────────────────────────────────
    function testerFiltrer() {
        const idClasse   = $('#t2-classe').val();
        const idObjectif = $('#t2-objectif').val();
        const $res       = $('#res2');
        const $spin      = $('#spin2');

        $spin.show();
        $res.hide();

        $.ajax({
            url   : urlFiltrer,
            method: 'GET',
            data  : { id_classes: idClasse, id_objectifs: idObjectif },

            success: function (data) {

                // Cas : aucun filtre → tableau vide attendu
                if (!idClasse && !idObjectif) {
                    if (data.length === 0) {
                        afficherOk($res, 'Sans filtre → retourne [] correctement.');
                    } else {
                        afficherErreur($res, 'Sans filtre devrait retourner [] mais a retourné ' + data.length + ' résultats.');
                    }
                    return;
                }

                if (data.length === 0) {
                    afficherVide($res);
                    return;
                }

                // Affiche le résultat en tableau
                afficherOk($res, data.length + ' apprenti(s) retourné(s).');

                let $table = $('<table class="result-table">')
                    .append($('<thead>').append(
                        $('<tr>')
                            .append($('<th>').text('Nom'))
                            .append($('<th>').text('Prénom'))
                            .append($('<th>').text('Classe'))
                            .append($('<th>').text('Sessions'))
                            .append($('<th>').text('Objectifs / session'))
                    ));

                let $tbody = $('<tbody>');
                $.each(data, function (i, a) {
                    const nbObj = a.sessions.length > 0 ? a.sessions[0].objectifs.length : 0;
                    $('<tr>')
                        .append($('<td>').text(a.nom))
                        .append($('<td>').text(a.prenom))
                        .append($('<td>').text(a.classe))
                        .append($('<td>').text(a.nb_sessions))
                        .append($('<td>').text(nbObj + ' obj. en session 1'))
                        .appendTo($tbody);
                });

                $table.append($tbody);
                $res.append($table);

                // JSON brut (utile pour déboguer)
                $res.append(
                    $('<details style="margin-top:0.5rem;">').append(
                        $('<summary style="font-size:0.78rem; cursor:pointer; color:#636b6f;">').text('Voir JSON brut'),
                        $('<div class="json-raw">').text(JSON.stringify(data, null, 2))
                    )
                );
            },

            error: function (xhr) {
                afficherErreur($res, 'Erreur HTTP ' + xhr.status + ' — ' + xhr.statusText);
            },

            complete: function () {
                $spin.hide();
            }
        });
    }

    // ── TEST 3 — exportCsv() ────────────────────────────────────────
    function testerCsv() {
        const params = {};
        const idClasse   = $('#t3-classe').val();
        const idObjectif = $('#t3-objectif').val();

        if (idClasse)   params.id_classes   = idClasse;
        if (idObjectif) params.id_objectifs = idObjectif;

        const qs = $.param(params);
        window.location.href = urlCsv + (qs ? '?' + qs : '');

        $('#res3').show();
    }

    // ── TEST 4A — exportPdf() sans filtre (doit retourner 400) ──────
    function testerPdfBlocage() {
        const $res  = $('#res4');
        const $spin = $('#spin4');

        $spin.show();
        $res.hide();

        $.ajax({
            url   : urlPdf,
            method: 'GET',
            data  : {},  // ← aucun filtre volontairement

            success: function () {
                // Ne devrait pas arriver
                afficherErreur($res, 'PROBLÈME : le PDF s\'est généré sans filtre alors qu\'il devrait être bloqué (400).');
            },

            error: function (xhr) {
                if (xhr.status === 400) {
                    afficherOk($res, 'Blocage correct : HTTP 400 retourné quand aucun filtre n\'est sélectionné.');
                } else {
                    afficherErreur($res, 'Erreur inattendue : HTTP ' + xhr.status);
                }
            },

            complete: function () {
                $spin.hide();
            }
        });
    }

    // ── TEST 4B — exportPdf() avec filtre (doit télécharger) ────────
    function testerPdf() {
        const params = {};
        const idClasse   = $('#t4-classe').val();
        const idObjectif = $('#t4-objectif').val();

        if (!idClasse && !idObjectif) {
            $('#res4').html(
                '<span class="result-badge badge-empty">⚠ Attention</span> ' +
                '<span style="font-size:0.82rem;">Sélectionnez au moins une classe ou un objectif pour télécharger le PDF.</span>'
            ).show();
            return;
        }

        if (idClasse)   params.id_classes   = idClasse;
        if (idObjectif) params.id_objectifs = idObjectif;

        window.open(urlPdf + '?' + $.param(params), '_blank');
    }

</script>

@endsection