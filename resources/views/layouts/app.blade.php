<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Vivero') }}</title>
    <style>
        :root { color-scheme: light; }
        body { margin:0; font-family: Arial, Helvetica, sans-serif; background:#f5f1ea; color:#2b2118; }
        a { color:#7a4f1d; text-decoration:none; }
        .topbar { background:#2c1f16; color:#fff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
        .topbar nav { display:flex; gap:12px; flex-wrap:wrap; }
        .topbar a { color:#fff; opacity:.92; }
        .container { max-width:1200px; margin:0 auto; padding:20px; }
        .grid { display:grid; gap:16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .card { background:#fff; border:1px solid #e8ddd1; border-radius:16px; padding:16px; box-shadow:0 4px 18px rgba(69,44,20,.05); }
        .muted { color:#7b6a58; }
        .stat { font-size:32px; font-weight:700; margin:6px 0 0; }
        .btn { display:inline-block; padding:10px 14px; border-radius:12px; border:1px solid #d9c7b6; background:#fff; color:#2b2118; cursor:pointer; }
        .btn.primary { background:#7a4f1d; color:#fff; border-color:#7a4f1d; }
        .btn.danger { background:#a53a2b; color:#fff; border-color:#a53a2b; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid #eee2d8; vertical-align:top; }
        th { color:#60452d; font-size:12px; text-transform:uppercase; letter-spacing:.04em; }
        input, select, textarea { width:100%; padding:10px 12px; border:1px solid #d8c7b5; border-radius:10px; box-sizing:border-box; background:#fff; }
        textarea { min-height:110px; }
        .field { margin-bottom:14px; }
        .field label { display:block; font-size:13px; margin-bottom:6px; font-weight:700; }
        .flash { padding:12px 14px; border-radius:12px; margin-bottom:16px; background:#eef8ef; border:1px solid #c8e7ca; }
        .error { color:#b3261e; font-size:13px; margin-top:6px; }
        .pill { display:inline-block; padding:4px 10px; border-radius:999px; background:#efe6dc; font-size:12px; }
        .timeline { display:grid; gap:12px; }
        .timeline-item { padding:12px; border:1px solid #eadfd3; border-radius:14px; background:#fff; }
        .small { font-size:13px; }
        .actions { display:flex; gap:8px; flex-wrap:wrap; }
        @media (max-width: 900px) {
            .grid.cols-2, .grid.cols-3 { grid-template-columns: 1fr; }
            .topbar { align-items:flex-start; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <strong>{{ config('app.name', 'Vivero') }}</strong>
        <nav>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('plantas.index') }}">Plantas</a>
                <a href="{{ route('captura.create', 'mediciones') }}">Captura</a>
                <a href="{{ route('catalogos.index', 'variedades') }}">Catalogos</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('seleccion.index') }}">Seleccion</a>
                    <a href="{{ route('etiquetas.create') }}">Etiquetas</a>
                @endif
            @endauth
        </nav>
        <div>
            @auth
                <span class="pill">{{ auth()->user()->name }} | {{ auth()->user()->rol }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button class="btn" type="submit">Salir</button>
                </form>
            @endauth
        </div>
    </header>

    <main class="container">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
