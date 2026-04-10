<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>@yield('title', 'Drone Academy')</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- DataTables + Bootstrap 5 -->
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.min.css"
    >

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet"
    >

    <style>
        :root {
            --accent: #e8ff47;
            --dark: #0d0f14;
            --card: #161a23;
            --border: #2a2f3d;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

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
            top: 0;
            left: 0;
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

        .sidebar .logo span {
            color: #fff;
        }

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

        table.dataTable tbody tr:hover {
            background: rgba(255, 255, 255, 0.03) !important;
        }

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

        .dt-info {
            color: var(--muted) !important;
            font-size: 0.8rem !important;
        }

        .dataTables_wrapper .dt-search label,
        .dataTables_wrapper .dt-length label {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #000000;
            border-radius: 12px;
            padding: 2rem;
            width: 500px;
            max-width: 90vw;
            box-shadow: 0 20px 60px rgba(255, 0, 0, 1);
            position: relative;
            font-family: 'Raleway', sans-serif;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.25rem;
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: #636b6f;
            line-height: 1;
        }

        .modal-close:hover {
            color: #000;
        }

        .modal-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .modal-classe {
            color: #636b6f;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .modal-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #636b6f;
            margin-bottom: 0.5rem;
            margin-top: 1rem;
        }

        .objectif-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            padding: 0.3rem 0;
            border-bottom: 1px solid #ffffff;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot.reussi {
            background: #22c55e;
        }

        .dot.echoue {
            background: #ef4444;
        }

        .empty-msg {
            color: #636b6f;
            font-size: 0.875rem;
            font-style: italic;
        }

        .export-zone {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            color: rgb(0, 0, 0);
        }


    .export-zone span {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #636b6f;
    }

    .btn-export {
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: opacity 0.2s;
    }

    .btn-export:hover               { opacity: 0.82; }
    .btn-csv                        { background: #22543d; color: #fff; }
    .btn-pdf                        { background: #c53030; color: #fff; }
    .btn-export:disabled            { opacity: 0.35; cursor: not-allowed; }
    .btn-export[disabled]           { opacity: 0.35; cursor: not-allowed; }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <a
            class="logo"
            href="/"
        ><span>LARAVEL DRONE</span></a>
        <nav class="d-flex flex-column gap-1">
            <a
                href="{{ route('historique.index') }}"
                class="nav-link {{ request()->routeIs('historique.*') ? 'active' : '' }}"
            >
                📋 Historique
            </a><br>
            <a
                href="{{ route('statistique.index') }}"
                class="nav-link {{ request()->routeIs('statistique.*') ? 'active' : '' }}"
            >
                📊 Statistique
            </a><br>
            <a
                href="{{ route('apprentis.index') }}"
                class="nav-link {{ request()->routeIs('apprentis.*') ? 'active' : '' }}"
            >
                👨‍🎓 Apprentis
            </a><br>
            @auth
                <form
                    action="{{ route('signout') }}"
                    method="POST"
                    style="margin-top: 1rem;"
                >
                    @csrf
                    <button
                        type="submit"
                        class="nav-link"
                        style="width:100%; text-align:left; background:none; border:none; cursor:pointer; color:#f87171;"
                    >
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
