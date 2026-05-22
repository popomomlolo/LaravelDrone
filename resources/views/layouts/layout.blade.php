<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Drone Academy')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* ══ Variables ══════════════════════════════════════════════ */
        :root {
            --accent : #ffa65d;
            --dark   : #0d0f14;
            --card   : #161a23;
            --border : #ff7300;
            --muted  : #6b7280;
        }

        * { box-sizing: border-box; }

        body {
            background : var(--dark);
            color      : #e2e8f0;
            font-family: 'DM Sans', sans-serif;
            min-height : 100vh;
        }

        /* ══ Sidebar ════════════════════════════════════════════════ */
        .sidebar {
            width       : 240px;
            min-height  : 100vh;
            background  : var(--card);
            border-right: 1px solid var(--border);
            padding     : 2rem 1.5rem;
            position    : fixed;
            top: 0; left: 0;
            z-index     : 100;
        }

        .sidebar .logo {
            font-family : 'Syne', sans-serif;
            font-weight : 800;
            font-size   : 1.4rem;
            color       : var(--accent);
            letter-spacing: -0.5px;
            margin-bottom : 2.5rem;
            display     : block;
            text-decoration: none;
        }
        .sidebar .logo span { color: #fff; }

        .sidebar .nav-link {
            color      : var(--muted);
            font-size  : 0.875rem;
            font-weight: 500;
            padding    : 0.6rem 0.8rem;
            border-radius: 8px;
            transition : all 0.2s;
            display    : flex;
            align-items: center;
            gap        : 0.6rem;
            text-decoration: none;
        }
        .sidebar .nav-link:hover           { color: #fff; background: rgba(232,255,71,0.08); }
        .sidebar .nav-link.active          { color: var(--accent); background: rgba(232,255,71,0.08); }
        .sidebar .nav-link.nav-link-danger { color: #f87171; }
        .sidebar .nav-link.nav-link-danger:hover { background: rgba(239,68,68,0.1); color: #fca5a5; }

        /* ══ Main ═══════════════════════════════════════════════════ */
        .main {
            margin-left: 240px;
            padding    : 2.5rem;
        }

        /* ══ Page header ════════════════════════════════════════════ */
        .page-title {
            font-family : 'Syne', sans-serif;
            font-weight : 800;
            font-size   : 2rem;
            color       : #fff;
            margin-bottom: 0.25rem;
        }
        .page-sub {
            color        : var(--muted);
            font-size    : 0.875rem;
            margin-bottom: 2rem;
        }

        /* ══ Card ═══════════════════════════════════════════════════ */
        .card-dark {
            background   : var(--card);
            border       : 1px solid var(--border);
            border-radius: 16px;
            padding      : 1.75rem;
        }

        /* ══ Boutons — convention couleur ═══════════════════════════
         *
         *  btn-app-primary   → Jaune accent   — action principale de la page
         *  btn-app-success   → Vert           — valider, enregistrer, ajouter, confirmer, importer
         *  btn-app-danger    → Rouge          — supprimer, fermer, annuler
         *  btn-app-info      → Bleu           — modifier, exporter PDF, naviguer
         *  btn-app-secondary → Gris foncé     — actions neutres / secondaires
         *  btn-app-export-csv→ Vert foncé     — export CSV (variante export)
         *
         * ═════════════════════════════════════════════════════════ */
        .btn-app {
            display     : inline-flex;
            align-items : center;
            gap         : 0.4rem;
            font-family : 'DM Sans', sans-serif;
            font-size   : 0.85rem;
            font-weight : 600;
            padding     : 0.45rem 1.1rem;
            border-radius: 8px;
            border      : none;
            cursor      : pointer;
            transition  : opacity 0.18s, transform 0.12s;
            text-decoration: none;
            line-height : 1.4;
            white-space : nowrap;
        }
        .btn-app:hover  { opacity: 0.85; transform: translateY(-1px); }
        .btn-app:active { opacity: 1;    transform: translateY(0); }
        .btn-app:disabled, .btn-app[disabled] { opacity: 0.38; cursor: not-allowed; transform: none; }

        /* Variantes */
        .btn-app-primary   { background: var(--accent);  color: var(--dark); }
        .btn-app-success   { background: #16a34a;        color: #fff; }
        .btn-app-danger    { background: #dc2626;        color: #fff; }
        .btn-app-info      { background: #2563eb;        color: #fff; }
        .btn-app-secondary { background: #374151;        color: #e2e8f0; }
        .btn-app-export-csv{ background: #14532d;        color: #fff; }

        /* ══ Formulaires ════════════════════════════════════════════ */
        .form-control-dark,
        .form-select-dark {
            background   : var(--dark);
            border       : 1px solid var(--border);
            color        : #e2e8f0;
            border-radius: 8px;
            padding      : 0.45rem 0.85rem;
            font-size    : 0.875rem;
            font-family  : 'DM Sans', sans-serif;
            transition   : border-color 0.15s;
            width        : 100%;
        }
        .form-control-dark:focus,
        .form-select-dark:focus {
            outline     : none;
            border-color: var(--accent);
            box-shadow  : 0 0 0 3px rgba(232,255,71,0.12);
            background  : var(--dark);
            color       : #e2e8f0;
        }
        .form-label-dark {
            font-size  : 0.8rem;
            font-weight: 600;
            color      : var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom : 0.35rem;
            display    : block;
        }

        /* ══ DataTables ═════════════════════════════════════════════ */
        .dataTables_wrapper .dt-search input,
        .dataTables_wrapper .dt-length select {
            background   : var(--dark);
            border       : 1px solid var(--border);
            color        : #e2e8f0;
            border-radius: 8px;
            padding      : 0.4rem 0.75rem;
            font-size    : 0.85rem;
        }
        .dataTables_wrapper .dt-search input:focus { outline: none; border-color: var(--accent); }
        .dataTables_wrapper .dt-search label,
        .dataTables_wrapper .dt-length label { color: var(--muted); font-size: 0.82rem; }

        table.dataTable { border-collapse: separate !important; border-spacing: 0 !important; }

        table.dataTable thead th {
            background    : var(--dark);
            color         : var(--muted);
            font-size     : 0.75rem;
            font-weight   : 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom : 1px solid var(--border) !important;
            border-top    : none !important;
            padding       : 0.85rem 1rem;
        }
        table.dataTable tbody tr {
            border-bottom: 1px solid var(--border);
            transition   : background 0.15s, box-shadow 0.15s, transform 0.1s;
            cursor       : default;
        }
        table.dataTable tbody tr.clickable { cursor: pointer; }
        table.dataTable tbody tr.clickable:hover {
            background  : rgba(232,255,71,0.04) !important;
            box-shadow  : inset 3px 0 0 var(--accent);
            transform   : translateX(2px);
        }
        table.dataTable tbody tr:not(.clickable):hover {
            background: rgba(255,255,255,0.03) !important;
        }
        table.dataTable tbody td {
            padding       : 0.85rem 1rem;
            font-size     : 0.875rem;
            vertical-align: middle;
            border-top    : none !important;
            background    : transparent !important;
            color         : #e2e8f0 !important;
        }

        /* Pagination */
        .dt-paging .dt-paging-button {
            background: var(--dark)   !important;
            border    : 1px solid var(--border) !important;
            color     : #e2e8f0      !important;
            border-radius: 6px       !important;
            margin    : 0 2px        !important;
        }
        .dt-paging .dt-paging-button.current {
            background: var(--accent) !important;
            color     : var(--dark)  !important;
            border-color: var(--accent) !important;
            font-weight: 700         !important;
        }
        .dt-paging .dt-paging-button:hover:not(.current) {
            background: rgba(232,255,71,0.08) !important;
            color     : #fff !important;
        }
        .dt-info { color: var(--muted) !important; font-size: 0.8rem !important; }

        /* ══ Badges ═════════════════════════════════════════════════ */
        .badge-app {
            display      : inline-block;
            padding      : 0.22rem 0.65rem;
            border-radius: 20px;
            font-size    : 0.73rem;
            font-weight  : 600;
        }
        .badge-app-accent   { background: rgba(232,255,71,0.12); color: var(--accent); }
        .badge-app-success  { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .badge-app-danger   { background: rgba(239,68,68,0.15);  color: #f87171; }
        .badge-app-muted    { background: rgba(148,163,184,0.1); color: #64748b; font-style: italic; }
        .badge-app-info     { background: rgba(99,179,237,0.12); color: #93c5fd; border: 1px solid rgba(99,179,237,0.2); }

        /* ══ Spinner ════════════════════════════════════════════════ */
        .spinner {
            display   : none;
            width     : 20px;
            height    : 20px;
            border    : 2px solid var(--border);
            border-top: 2px solid var(--accent);
            border-radius: 50%;
            animation : spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ══ Filtres ════════════════════════════════════════════════ */
        .filtres {
            display    : flex;
            align-items: center;
            gap        : 1rem;
            flex-wrap  : wrap;
            margin-bottom: 1.5rem;
        }
        .filtre-group {
            display    : flex;
            align-items: center;
            gap        : 0.75rem;
            flex-wrap  : wrap;
        }
        .filtre-group label {
            font-size  : 0.8rem;
            font-weight: 600;
            color      : var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        .filtre-group select {
            background   : var(--dark);
            border       : 1px solid var(--border);
            color        : #e2e8f0;
            border-radius: 8px;
            padding      : 0.42rem 0.85rem;
            font-size    : 0.875rem;
            font-family  : 'DM Sans', sans-serif;
            transition   : border-color 0.15s;
            min-width    : 200px;
        }
        .filtre-group select:focus {
            outline     : none;
            border-color: var(--accent);
            box-shadow  : 0 0 0 3px rgba(232,255,71,0.1);
        }

        /* ══ Msg info ═══════════════════════════════════════════════ */
        .msg-info {
            color      : var(--muted);
            font-size  : 0.875rem;
            font-style : italic;
            padding    : 1rem 0;
        }

        /* ══ Modale ═════════════════════════════════════════════════ */
        .modal-overlay {
            display    : none;
            position   : fixed;
            inset      : 0;
            background : rgba(0,0,0,0.65);
            z-index    : 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active { display: flex; }

        .modal-box {
            background   : var(--card);
            color        : #e2e8f0;
            border       : 1px solid var(--border);
            border-radius: 14px;
            width        : 95vw;
            max-width    : 1300px;
            max-height   : 88vh;
            display      : flex;
            flex-direction: column;
            position     : relative;
            box-shadow   : 0 25px 60px rgba(0,0,0,0.6);
            overflow     : hidden;
        }

        .modal-header-bar {
            padding      : 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display      : flex;
            align-items  : center;
            justify-content: space-between;
            flex-shrink  : 0;
            background   : var(--dark);
        }
        .modal-identite      { display: flex; align-items: center; gap: 1rem; }
        .modal-nom           { font-family: 'Syne', sans-serif; font-size: 1.15rem; font-weight: 700; color: #fff; }
        .modal-classe-badge  {
            font-size    : 0.8rem;
            color        : var(--accent);
            background   : rgba(232,255,71,0.1);
            padding      : 0.2rem 0.75rem;
            border-radius: 20px;
            font-weight  : 600;
        }

        .modal-scroll-zone {
            overflow: auto;
            flex    : 1;
            padding : 1.25rem 1.5rem;
        }

        .modal-footer-bar {
            display      : flex;
            align-items  : center;
            gap          : 1.2rem;
            padding      : 0.75rem 1.5rem;
            border-top   : 1px solid var(--border);
            flex-shrink  : 0;
            background   : var(--dark);
            flex-wrap    : wrap;
        }
        .modal-legende-item  { display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--muted); }
        .modal-legende-dot   { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .modal-legende-dot.success  { background: #22c55e; }
        .modal-legende-dot.danger   { background: #ef4444; }
        .modal-legende-dot.muted    { background: #374151; border: 1px solid #4b5563; }

        /* ══ Tableau sessions (dans modale) ═════════════════════════ */
        .sessions-table {
            width           : 100%;
            border-collapse : separate;
            border-spacing  : 0;
            font-size       : 0.82rem;
            white-space     : nowrap;
            min-width       : 900px;
        }
        .sessions-table thead th {
            background    : var(--dark);
            color         : var(--muted);
            font-weight   : 600;
            text-transform: uppercase;
            font-size     : 0.7rem;
            letter-spacing: 0.06em;
            padding       : 0.6rem 0.9rem;
            border-bottom : 2px solid var(--border);
            position      : sticky;
            top           : 0;
            z-index       : 2;
        }
        .sessions-table tbody tr { transition: background 0.12s; }
        .sessions-table tbody tr:nth-child(even) { background: rgba(255,255,255,0.02); }
        .sessions-table tbody tr:hover           { background: rgba(232,255,71,0.04); }
        .sessions-table tbody td {
            padding      : 0.55rem 0.9rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
            color        : #cbd5e1;
        }
        .sessions-table tbody td:first-child { color: #e2e8f0; font-weight: 600; }

        /* Cellules objectifs dans la modale */
        .obj-cell {
            display        : inline-flex;
            align-items    : center;
            justify-content: center;
            gap            : 0.3rem;
            padding        : 0.25rem 0.6rem;
            border-radius  : 6px;
            font-size      : 0.75rem;
            font-weight    : 600;
            min-width      : 70px;
        }
        .obj-reussi   { background: rgba(34,197,94,0.15);  color: #22c55e; border: 1px solid rgba(34,197,94,0.3); }
        .obj-echoue   { background: rgba(239,68,68,0.15);  color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
        .obj-nontente { background: rgba(148,163,184,0.08); color: #475569; border: 1px solid rgba(148,163,184,0.15); font-style: italic; }

        /* ══ Chart zone ═════════════════════════════════════════════ */
        #chartContainer { min-height: 380px; }
        .chart-description {
            font-size  : 0.8rem;
            color      : var(--muted);
            text-align : center;
            margin-top : 0.5rem;
        }

        /* ══ Résultat Historique ════════════════════════════════════ */
        .result-success { color: #4ade80; font-weight: 600; }
        .result-failure { color: #f87171; font-weight: 600; }
        .result-none    { color: var(--muted); font-style: italic; }

        /* ══ Alerte succès / erreur ═════════════════════════════════ */
        .alert-app {
            padding      : 0.75rem 1.1rem;
            border-radius: 8px;
            font-size    : 0.875rem;
            font-weight  : 500;
        }
        .alert-app-success {
            background: rgba(34,197,94,0.12);
            border    : 1px solid rgba(34,197,94,0.3);
            color     : #4ade80;
        }
        .alert-app-danger {
            background: rgba(239,68,68,0.12);
            border    : 1px solid rgba(239,68,68,0.3);
            color     : #f87171;
        }

        /* ══ Formulaire panel (ajouter / importer) ══════════════════ */
        .form-panel       { max-width: 480px; }
        .form-panel-title {
            font-family  : 'Syne', sans-serif;
            font-weight  : 700;
            font-size    : 1rem;
            color        : #fff;
            margin-bottom: 1.25rem;
        }
        .form-panel-hint {
            font-size    : 0.8rem;
            color        : var(--muted);
            margin-bottom: 1rem;
        }
        .form-panel-hint code {
            background   : var(--border);
            color        : var(--accent);
            padding      : 0.1rem 0.4rem;
            border-radius: 4px;
            font-size    : 0.78rem;
        }

        /* ══ Input file ═════════════════════════════════════════════ */
        .form-file-dark {
            display      : block;
            width        : 100%;
            font-size    : 0.875rem;
            color        : #e2e8f0;
            background   : var(--dark);
            border       : 1px solid var(--border);
            border-radius: 8px;
            padding      : 0.45rem 0.85rem;
            cursor       : pointer;
            transition   : border-color 0.15s;
        }
        .form-file-dark:focus        { outline: none; border-color: var(--accent); }
        .form-file-dark::file-selector-button {
            background   : var(--border);
            color        : #e2e8f0;
            border       : none;
            border-radius: 6px;
            padding      : 0.3rem 0.8rem;
            font-size    : 0.8rem;
            font-weight  : 600;
            cursor       : pointer;
            margin-right : 0.75rem;
            transition   : background 0.15s;
        }
        .form-file-dark::file-selector-button:hover { background: #4b5563; }

        /* ══ Bouton taille réduite (dans les cellules du tableau) ═══ */
        .btn-sm-app {
            padding  : 0.28rem 0.65rem;
            font-size: 0.78rem;
        }

        /* ══ Modale taille réduite (formulaires apprentis) ══════════ */
        .modal-box-sm {
            max-width: 480px;
            max-height: 80vh;
        }

        /* ══ Texte de confirmation dans la modale suppression ═══════ */
        .modal-confirm-text {
            font-size    : 1rem;
            color        : #e2e8f0;
            margin-bottom: 0.5rem;
            line-height  : 1.6;
        }
        .modal-confirm-name    { color: var(--accent); font-size: 1.05rem; }
        .modal-confirm-warning {
            font-size  : 0.8rem;
            color      : #f87171;
            font-style : italic;
        }

    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <a class="logo" href="/"><span>LARAVEL DRONE</span></a>
        <nav class="d-flex flex-column gap-1">
            <a href="{{ route('historique.index') }}"
               class="nav-link {{ request()->routeIs('historique.*') ? 'active' : '' }}">
                📋 Historique
            </a>
            <a href="{{ route('statistique.index') }}"
               class="nav-link {{ request()->routeIs('statistique.*') ? 'active' : '' }}">
                📊 Statistiques
            </a>
            <a href="{{ route('apprentis.index') }}"
               class="nav-link {{ request()->routeIs('apprentis.*') ? 'active' : '' }}">
                👨‍🎓 Apprentis
            </a>
            @auth
                <form action="{{ route('signout') }}" method="POST" style="margin-top:1rem;">
                    @csrf
                    <button type="submit" class="nav-link nav-link-danger"
                            style="width:100%;text-align:left;background:none;border:none;cursor:pointer;">
                        🚪 Déconnexion
                    </button>
                </form>
            @endauth
        </nav>
    </aside>

    <!-- Contenu -->
    <main class="main">
        @yield('content')
    </main>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.min.js"></script>

    @stack('scripts')
</body>
</html>