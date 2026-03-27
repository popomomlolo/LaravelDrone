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
        padding: 0 25px;<a href="{{ route('apprentis.index') }}"
                class="nav-link {{ request()->routeIs('apprentis.*') ? 'active' : '' }}">
                  👨‍🎓 Apprentis
              </a><br>
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

        .sidebar .logo span { color: #000000; }

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
            <a href="{{ route('apprentis.index') }}"
                class="nav-link {{ request()->routeIs('apprentis.*') ? 'active' : '' }}">
                  👨‍🎓 Apprentis
              </a><br>

        </nav>
    </aside>


  </body>
</html>