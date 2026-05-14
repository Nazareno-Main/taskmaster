<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskMaster') — Student Task Manager</title>

    {{-- Google Fonts: Syne (display) + DM Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- React via CDN (used for the status toggle component) --}}
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <style>
        /* ═══════════════════════════════════════════════════
           CSS DESIGN SYSTEM — TaskMaster
           Color palette: Deep navy + electric indigo + warm accents
        ═══════════════════════════════════════════════════ */
        :root {
            --bg:         #0f1117;
            --bg2:        #181c27;
            --bg3:        #1e2333;
            --border:     #2a3048;
            --border2:    #333d5a;
            --text:       #e8eaf2;
            --text2:      #9aa0bb;
            --text3:      #5c6380;
            --accent:     #6366f1;
            --accent2:    #818cf8;
            --accent-glow:rgba(99,102,241,0.25);
            --green:      #10b981;
            --yellow:     #f59e0b;
            --red:        #ef4444;
            --pink:       #ec4899;
            --radius:     12px;
            --radius-sm:  8px;
            --shadow:     0 4px 24px rgba(0,0,0,0.4);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }

        /* ── Typography ── */
        h1,h2,h3,h4 { font-family: 'Syne', sans-serif; font-weight: 700; line-height: 1.2; }

        /* ── Layout ── */
        .app-wrapper { display: flex; min-height: 100vh; }

        /* ══ SIDEBAR ═══════════════════════════════════════ */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 28px 0;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }
        .sidebar-logo {
            padding: 0 24px 28px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }
        .sidebar-logo span {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        .sidebar-logo span em {
            color: var(--accent2);
            font-style: normal;
        }
        .sidebar-logo p { font-size: 12px; color: var(--text3); margin-top: 2px; }
        .nav-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text3);
            padding: 8px 24px 4px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            color: var(--text2);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .nav-link:hover { color: var(--text); background: var(--bg3); }
        .nav-link.active { color: var(--accent2); border-left-color: var(--accent); background: rgba(99,102,241,0.08); }
        .nav-link .icon { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 24px;
            border-top: 1px solid var(--border);
        }
        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }
        .user-info p { font-size: 13px; font-weight: 600; color: var(--text); }
        .user-info span { font-size: 11px; color: var(--text3); }
        .btn-logout {
            display: block;
            margin-top: 10px;
            text-align: center;
            padding: 7px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border2);
            color: var(--text3);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            background: none;
            width: 100%;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-logout:hover { color: var(--red); border-color: var(--red); background: rgba(239,68,68,0.08); }

        /* ══ MAIN CONTENT ══════════════════════════════════ */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 36px 40px;
            max-width: calc(100vw - 240px);
        }
        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 26px; color: var(--text); }
        .page-header p { color: var(--text2); margin-top: 4px; font-size: 14px; }

        /* ══ BUTTONS ════════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--accent);
            color: white;
            box-shadow: 0 0 0 0 var(--accent-glow);
        }
        .btn-primary:hover {
            background: var(--accent2);
            box-shadow: 0 0 16px var(--accent-glow);
            transform: translateY(-1px);
        }
        .btn-ghost {
            background: var(--bg3);
            color: var(--text2);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { color: var(--text); border-color: var(--border2); }
        .btn-danger {
            background: rgba(239,68,68,0.12);
            color: var(--red);
            border: 1px solid rgba(239,68,68,0.25);
        }
        .btn-danger:hover { background: rgba(239,68,68,0.2); }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ══ CARDS ══════════════════════════════════════════ */
        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
        }

        /* ══ FORMS ══════════════════════════════════════════ */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text2);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        .form-control::placeholder { color: var(--text3); }
        select.form-control option { background: var(--bg2); }
        .form-error { color: var(--red); font-size: 12px; margin-top: 4px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* ══ BADGES ═════════════════════════════════════════ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .badge-high    { background: rgba(239,68,68,0.15);   color: #f87171; }
        .badge-medium  { background: rgba(245,158,11,0.15);  color: #fbbf24; }
        .badge-low     { background: rgba(16,185,129,0.15);  color: #34d399; }
        .status-pending  { background: rgba(99,102,241,0.15); color: #a5b4fc; }
        .status-progress { background: rgba(245,158,11,0.15); color: #fcd34d; }
        .status-done     { background: rgba(16,185,129,0.15); color: #6ee7b7; }

        /* ══ ALERTS ═════════════════════════════════════════ */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
        .alert-error   { background: rgba(239,68,68,0.12);  color: #f87171; border: 1px solid rgba(239,68,68,0.2); }

        /* ══ TABLE ══════════════════════════════════════════ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text3);
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }
        .task-title { font-weight: 500; color: var(--text); }
        .task-title.done { text-decoration: line-through; color: var(--text3); }
        .task-desc { font-size: 12px; color: var(--text3); margin-top: 2px; }
        .overdue-row td:first-child { border-left: 3px solid var(--red); }

        /* ══ STAT CARDS ═════════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            transition: transform 0.2s, border-color 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); border-color: var(--border2); }
        .stat-card .num {
            font-family: 'Syne', sans-serif;
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-card .lbl { font-size: 12px; color: var(--text3); font-weight: 500; }
        .stat-total   .num { color: var(--accent2); }
        .stat-pending .num { color: #a5b4fc; }
        .stat-progress .num { color: var(--yellow); }
        .stat-done    .num { color: var(--green); }
        .stat-overdue .num { color: var(--red); }

        /* ══ FILTER BAR ═════════════════════════════════════ */
        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            align-items: center;
        }
        .filter-bar .form-control { width: auto; }
        .filter-bar input[type="search"] { min-width: 200px; }

        /* ══ EMPTY STATE ════════════════════════════════════ */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text3);
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; opacity: 0.4; }
        .empty-state p { font-size: 15px; }

        /* ══ RESPONSIVE ═════════════════════════════════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; padding: 20px 16px; max-width: 100vw; }
            .form-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ══ ANIMATIONS ═════════════════════════════════════ */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .main { animation: fadeIn 0.3s ease; }
    </style>
</head>
<body>
<div class="app-wrapper">

    @auth
    {{-- ── Sidebar Navigation ────────────────────────────── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span>Task<em>Master</em></span>
            <p>Student Planner</p>
        </div>

        <span class="nav-label">Menu</span>
        <a href="{{ route('tasks.index') }}"
           class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
            <span class="icon">📋</span> Dashboard
        </a>
        <a href="{{ route('tasks.create') }}"
           class="nav-link {{ request()->routeIs('tasks.create') ? 'active' : '' }}">
            <span class="icon">➕</span> Add Task
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <p>{{ Auth::user()->name }}</p>
                    <span>Student</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Sign Out</button>
            </form>
        </div>
    </aside>
    @endauth

    {{-- ── Main Content ──────────────────────────────────── --}}
    <main class="main">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                ✕ {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@yield('scripts')
</body>
</html>
