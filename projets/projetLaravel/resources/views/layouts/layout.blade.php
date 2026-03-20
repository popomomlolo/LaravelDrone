<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Drone Academy')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent: #e8ff47;
            --dark:   #0d0f14;
            --card:   #161a23;
            --border: #2a2f3d;
            --muted:  #6b7280;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--dark);
            color: #e2e8f0;
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 2rem 1.5rem;
            position: fixed;
            top: 0; left: 0;
        }

        .sidebar .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--accent);
            letter-spacing: -0.5px;
            margin-bottom: 2.5rem;
            display: block;
            text-decoration: none;
        }

        .sidebar .logo span { color: #fff; }

        .nav-link {
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(232, 255, 71, 0.08);
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(232, 255, 71, 0.08);
        }

        /* ── Contenu principal ── */
        .main {
            margin-left: 240px;
            padding: 2.5rem;
        }

        .page-header {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .page-sub {
            color: var(--muted);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        /* ── Card ── */
        .card-dark {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.75rem;
        }

        /* ── DataTables ── */
        .dataTables_wrapper .dt-search input,
        .dataTables_wrapper .dt-length select {
            background: var(--dark);
            border: 1px solid var(--border);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
        }

        .dataTables_wrapper .dt-search input:focus {
            outline: none;
            border-color: var(--accent);
        }

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        table.dataTable thead th {
            background: var(--dark);
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border) !important;
            border-top: none !important;
            padding: 0.85rem 1rem;
        }

        table.dataTable tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        table.dataTable tbody tr:hover { background: rgba(255,255,255,0.03) !important; }

        table.dataTable tbody td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            vertical-align: middle;
            border-top: none !important;
            background: transparent !important;
        }

        /* Badges */
        .badge-classe {
            background: rgba(232, 255, 71, 0.12);
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
        }

        .badge-reussi {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
        }

        .badge-echoue {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
        }

        /* Pagination */
        .dt-paging .dt-paging-button {
            background: var(--dark) !important;
            border: 1px solid var(--border) !important;
            color: #e2e8f0 !important;
            border-radius: 6px !important;
            margin: 0 2px !important;
        }

        .dt-paging .dt-paging-button.current {
            background: var(--accent) !important;
            color: var(--dark) !important;
            border-color: var(--accent) !important;
            font-weight: 700 !important;
        }

        .dt-paging .dt-paging-button:hover:not(.current) {
            background: rgba(232, 255, 71, 0.08) !important;
            color: #fff !important;
        }

        .dt-info { color: var(--muted) !important; font-size: 0.8rem !important; }

        .dataTables_wrapper .dt-search label,
        .dataTables_wrapper .dt-length label {
            color: var(--muted);
            font-size: 0.82rem;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <a class="logo" href="/"><span>Drone</span>Academy</a>
        <nav class="d-flex flex-column gap-1">
            <a href="{{ route('historique.index') }}"
               class="nav-link {{ request()->routeIs('historique.*') ? 'active' : '' }}">
                📋 Historique
            </a>
            <a href="{{ route('apprentis.index') }}"
               class="nav-link {{ request()->routeIs('apprentis.*') ? 'active' : '' }}">
                👤 Apprentis
            </a>
            <a href="#" class="nav-link">🎯 Objectifs</a>
            <a href="#" class="nav-link">🏫 Classes</a>
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