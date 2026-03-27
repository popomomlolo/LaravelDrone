<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Laravel - @yield('title')</title>

    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Raleway:100,600"
      rel="stylesheet"
      type="text/css"
    />

    <!-- Styles -->
    <style>
      html,
      body {
        background-color: #fff;
        color: #000000;
        font-family: "Raleway", sans-serif;
        font-weight: 100;
        height: 100vh;
        margin: 0;
      }
      .full-height {
        height: 100vh;
      }
      .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
      }
      .position-ref {
        position: relative;
      }
      .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
      }
      .content {
        text-align: center;
      }
      .title {
        font-size: 84px;
      }
      .links > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.1rem;
        text-decoration: none;
        text-transform: uppercase;
      }
      .m-b-md {
        margin-bottom: 30px;
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
            .page-wrap {
        text-align: left;
        width: 100%;
        max-width: 960px;
        padding: 2rem;
    }

    .page-title {
        font-family: 'Raleway', sans-serif;
        font-weight: 600;
        font-size: 1.8rem;
        margin-bottom: 0.25rem;
    }

    .page-sub {
        color: #636b6f;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    /* Combobox */
    .select-wrap {
        margin-bottom: 2rem;
    }

    .select-wrap label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #636b6f;
        margin-bottom: 0.5rem;
    }

    .select-wrap select {
        padding: 0.5rem 1rem;
        font-family: 'Raleway', sans-serif;
        font-size: 0.95rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        min-width: 280px;
        cursor: pointer;
    }

    /* Tableau */
    #apprentisTable tbody tr {
        cursor: pointer;
    }

    #apprentisTable tbody tr:hover {
        background: #f5f5f5;
    }

    /* Modale */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        width: 500px;
        max-width: 90vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
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

    .modal-close:hover { color: #000; }

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
        border-bottom: 1px solid #f0f0f0;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot.reussi  { background: #22c55e; }
    .dot.echoue  { background: #ef4444; }

    .empty-msg {
        color: #636b6f;
        font-size: 0.875rem;
        font-style: italic;
    }

        .page-wrap {
        text-align: left;
        width: 100%;
        max-width: 960px;
        padding: 2rem;
        margin: 0 auto;
    }

    .page-title { font-weight: 600; font-size: 1.8rem; margin-bottom: 0.25rem; }
    .page-sub   { color: #636b6f; font-size: 0.875rem; margin-bottom: 1.5rem; }

    /* Combobox */
    .filtres {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        align-items: flex-end;
    }

    .filtre-group { display: flex; flex-direction: column; gap: 0.4rem; }

    .filtre-group label {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #636b6f;
    }

    .filtre-group select {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        min-width: 240px;
        cursor: pointer;
        background: #fff;
    }

    .filtre-group select:focus {
        outline: none;
        border-color: #636b6f;
    }

    /* Message vide */
    .msg-info {
        color: #636b6f;
        font-size: 0.9rem;
        font-style: italic;
        padding: 1rem 0;
    }

    /* Tableau */
    #apprentisTable tbody tr { cursor: pointer; }
    #apprentisTable tbody tr:hover { background: #f5f5f5 !important; }

    /* Modale */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        width: 580px;
        max-width: 95vw;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        position: relative;
        font-family: 'Raleway', sans-serif;
    }

    .modal-close {
        position: sticky;
        top: 0;
        float: right;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #636b6f;
        line-height: 1;
        z-index: 10;
    }
    .modal-close:hover { color: #000; }

    .modal-identite {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }
    .modal-nom    { font-size: 1.4rem; font-weight: 700; }
    .modal-classe { color: #636b6f; font-size: 0.85rem; margin-top: 0.2rem; }

    /* Sessions dans la modale */
    .session-block {
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }

    .session-header {
        background: #f8f9fa;
        padding: 0.65rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }

    .session-titre {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .session-meta {
        color: #636b6f;
        font-size: 0.78rem;
    }

    .session-body { padding: 0.75rem 1rem; }

    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #636b6f;
        margin-bottom: 0.4rem;
        margin-top: 0.75rem;
    }

    .section-label:first-child { margin-top: 0; }

    .objectif-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        padding: 0.3rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .objectif-item:last-child { border-bottom: none; }

    .dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .dot.reussi { background: #22c55e; }
    .dot.echoue { background: #ef4444; }

    .quantite {
        margin-left: auto;
        font-size: 0.78rem;
        color: #636b6f;
        white-space: nowrap;
    }

    .empty-obj { color: #636b6f; font-size: 0.85rem; font-style: italic; padding: 0.25rem 0; }

    /* Spinner */
    .spinner {
        display: none;
        width: 20px; height: 20px;
        border: 2px solid #e5e7eb;
        border-top-color: #636b6f;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-left: 0.75rem;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>
  </head>
  <body>
    <div class="flex-center position-ref full-height">
      @if (Route::has('login'))
      <div class="top-right links">
        @auth
        <a href="{{ url('/home') }}">Home</a>
        @else
        <a href="{{ route('login') }}">Login</a>

        @endauth
      </div>
      @endif

      <div class="content">@yield('content')</div>
    </div>

        <!-- Sidebar -->
    <aside class="sidebar">
        <a class="logo" href="/"><span>LARAVEL DRONE</span></a>
        <nav class="d-flex flex-column gap-1">
            <a href="{{ route('historique.index') }}"
               class="nav-link {{ request()->routeIs('historique.*') ? 'active' : '' }}">
                📋 Historique
            </a><br>
            <a href="{{ route('statistique.index') }}"
               class="nav-link {{ request()->routeIs('statistique.*') ? 'active' : '' }}">
                📊 Statistique
            </a><br>

        </nav>
    </aside>

  </body>
</html>