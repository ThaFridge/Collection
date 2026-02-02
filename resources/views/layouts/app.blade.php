<!DOCTYPE html>
<html lang="nl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GameVault')</title>
    <style>
        :root {
            --bg: #1a1a2e;
            --bg-card: #16213e;
            --bg-input: #0f3460;
            --text: #e6e6e6;
            --text-muted: #a0a0b0;
            --accent: #e94560;
            --accent-hover: #ff6b81;
            --success: #2ecc71;
            --warning: #f39c12;
            --border: #2a2a4a;
            --shadow: rgba(0,0,0,0.3);
        }
        [data-theme="light"] {
            --bg: #f5f5f5;
            --bg-card: #ffffff;
            --bg-input: #e8e8e8;
            --text: #1a1a2e;
            --text-muted: #666;
            --accent: #e94560;
            --accent-hover: #c0392b;
            --success: #27ae60;
            --warning: #e67e22;
            --border: #ddd;
            --shadow: rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .navbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
        }
        .navbar-nav {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            align-items: center;
            flex-wrap: wrap;
        }
        .navbar-nav a {
            color: var(--text);
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: background 0.2s;
            font-size: 0.9rem;
        }
        .navbar-nav a:hover, .navbar-nav a.active {
            background: var(--bg-input);
            color: var(--accent);
        }
        .nav-dropdown {
            position: relative;
        }
        .nav-dropdown-toggle {
            color: var(--text);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: background 0.2s;
            font-size: 0.9rem;
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .nav-dropdown-toggle:hover, .nav-dropdown.open .nav-dropdown-toggle {
            background: var(--bg-input);
            color: var(--accent);
        }
        .nav-dropdown-toggle.active { color: var(--accent); }
        .nav-dropdown-toggle::after {
            content: '';
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid currentColor;
        }
        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.35rem 0;
            min-width: 180px;
            box-shadow: 0 8px 25px var(--shadow);
            z-index: 200;
        }
        .nav-dropdown.open .nav-dropdown-menu { display: block; }
        .nav-dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: var(--text);
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.15s;
            border-radius: 0;
        }
        .nav-dropdown-menu a:hover { background: var(--bg-input); color: var(--accent); }
        .nav-dropdown-menu a.active { color: var(--accent); }
        .theme-toggle {
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.4rem 0.7rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        .container { max-width: 1400px; margin: 0 auto; padding: 1.5rem; }
        .flash {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .flash-success { background: rgba(46,204,113,0.15); color: var(--success); border: 1px solid var(--success); }
        .flash-error { background: rgba(233,69,96,0.15); color: var(--accent); border: 1px solid var(--accent); }

        .stats-bar {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .stat-item {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            min-width: 140px;
        }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--accent); }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); }

        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
        }
        .game-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: var(--text);
            display: flex;
            flex-direction: column;
        }
        .game-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px var(--shadow);
        }
        .game-card-cover {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            background: var(--bg-input);
        }
        .game-card-cover-placeholder {
            width: 100%;
            aspect-ratio: 3/4;
            background: var(--bg-input);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--text-muted);
        }
        .game-card-body { padding: 0.75rem; flex: 1; }
        .game-card-title { font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .game-card-meta { font-size: 0.75rem; color: var(--text-muted); }
        .badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-platform { background: var(--accent); color: #fff; }
        .badge-format { background: var(--bg-input); color: var(--text-muted); border: 1px solid var(--border); }
        .badge-completion { font-size: 0.65rem; }
        .badge-not_played { background: var(--bg-input); color: var(--text-muted); }
        .badge-playing { background: rgba(243,156,18,0.2); color: var(--warning); }
        .badge-completed { background: rgba(46,204,113,0.2); color: var(--success); }
        .badge-platinum { background: rgba(52,152,219,0.2); color: #3498db; }
        .badge-built { background: rgba(46,204,113,0.2); color: var(--success); }
        .badge-displayed { background: rgba(52,152,219,0.2); color: #3498db; }
        .badge-in_progress { background: rgba(243,156,18,0.2); color: var(--warning); }
        .badge-not_built { background: var(--bg-input); color: var(--text-muted); }
        .badge-theme { background: rgba(155,89,182,0.2); color: #9b59b6; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-secondary { background: var(--bg-input); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { border-color: var(--accent); }
        .btn-danger { background: #c0392b; color: #fff; }
        .btn-danger:hover { background: #e74c3c; }
        .btn-sm { padding: 0.3rem 0.7rem; font-size: 0.8rem; }

        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.3rem; font-size: 0.85rem; font-weight: 500; }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
        }
        .form-control:focus { outline: none; border-color: var(--accent); }
        select.form-control { appearance: auto; }
        textarea.form-control { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-error { color: var(--accent); font-size: 0.8rem; margin-top: 0.2rem; }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .toolbar h1 { font-size: 1.5rem; }
        .filters { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .filters select, .filters input {
            padding: 0.4rem 0.7rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.85rem;
        }

        .detail-header { display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .detail-cover { width: 300px; border-radius: 12px; flex-shrink: 0; }
        .detail-cover img { width: 100%; border-radius: 12px; }
        .detail-info { flex: 1; }
        .detail-title { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .detail-meta { display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1rem; margin-top: 1rem; }
        .detail-meta dt { color: var(--text-muted); font-size: 0.85rem; }
        .detail-meta dd { font-size: 0.9rem; }
        .detail-actions { display: flex; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap; }

        .build-tracker {
            display: flex;
            gap: 0.25rem;
            margin-top: 1rem;
        }
        .build-step {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            background: var(--bg-input);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .build-step.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .build-step.done {
            background: rgba(46,204,113,0.2);
            color: var(--success);
            border-color: var(--success);
        }

        .pagination { display: flex; gap: 0.25rem; list-style: none; margin-top: 2rem; justify-content: center; }
        .pagination li a, .pagination li span {
            padding: 0.4rem 0.75rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.85rem;
        }
        .pagination li.active span { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .toast {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px var(--shadow);
            animation: toastIn 0.3s ease, toastOut 0.3s ease 3.7s forwards;
            cursor: pointer;
            max-width: 400px;
        }
        .toast-success { background: var(--bg-card); color: var(--success); border: 1px solid var(--success); }
        .toast-error { background: var(--bg-card); color: var(--accent); border: 1px solid var(--accent); }
        @keyframes toastIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(-10px); } }

        /* Confirm modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        .modal-box h3 { margin-bottom: 0.5rem; }
        .modal-box p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem; }
        .modal-actions { display: flex; gap: 0.75rem; justify-content: center; }

        /* Spinner */
        .spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Form validation */
        .form-control:invalid:not(:placeholder-shown):not(:focus) { border-color: var(--accent); }

        @media (max-width: 768px) {
            .game-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
            .form-row { grid-template-columns: 1fr; }
            .detail-header { flex-direction: column; }
            .detail-cover { width: 100%; max-width: 300px; }
            .navbar { flex-wrap: wrap; gap: 0.5rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/" class="navbar-brand">GameVault</a>
        <ul class="navbar-nav">
            <li class="nav-dropdown">
                <button class="nav-dropdown-toggle {{ request()->routeIs('games.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">Games</button>
                <div class="nav-dropdown-menu">
                    <a href="{{ route('games.index') }}" class="{{ request()->routeIs('games.index') ? 'active' : '' }}">Collectie</a>
                    <a href="{{ route('games.wishlist') }}" class="{{ request()->routeIs('games.wishlist') ? 'active' : '' }}">Wishlist</a>
                    <a href="{{ route('games.create') }}" class="{{ request()->routeIs('games.create') ? 'active' : '' }}">+ Game toevoegen</a>
                </div>
            </li>
            <li class="nav-dropdown">
                <button class="nav-dropdown-toggle {{ request()->routeIs('lego.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">LEGO</button>
                <div class="nav-dropdown-menu">
                    <a href="{{ route('lego.index') }}" class="{{ request()->routeIs('lego.index') ? 'active' : '' }}">Collectie</a>
                    <a href="{{ route('lego.wishlist') }}" class="{{ request()->routeIs('lego.wishlist') ? 'active' : '' }}">Wishlist</a>
                    <a href="{{ route('lego.create') }}" class="{{ request()->routeIs('lego.create') ? 'active' : '' }}">+ Set toevoegen</a>
                </div>
            </li>
            <li class="nav-dropdown">
                <button class="nav-dropdown-toggle {{ request()->routeIs('magazines.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">Magazines</button>
                <div class="nav-dropdown-menu">
                    <a href="{{ route('magazines.index') }}" class="{{ request()->routeIs('magazines.index') ? 'active' : '' }}">Overzicht</a>
                    <a href="{{ route('magazines.create') }}" class="{{ request()->routeIs('magazines.create') ? 'active' : '' }}">+ Uploaden</a>
                </div>
            </li>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('tags.index') }}" class="{{ request()->routeIs('tags.*') ? 'active' : '' }}">Tags</a></li>
            <li><a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a></li>
            <li><button class="theme-toggle" onclick="toggleTheme()">&#127763;</button></li>
        </ul>
    </nav>

    <div class="toast-container" id="toast-container">
        @if(session('success'))
            <div class="toast toast-success" onclick="this.remove()">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="toast toast-error" onclick="this.remove()">{{ session('error') }}</div>
        @endif
    </div>

    <div class="modal-overlay" id="confirm-modal">
        <div class="modal-box">
            <h3 id="confirm-title">Weet je het zeker?</h3>
            <p id="confirm-message">Dit kan niet ongedaan worden gemaakt.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeConfirm()">Annuleren</button>
                <button class="btn btn-danger" id="confirm-btn" onclick="doConfirm()">Verwijderen</button>
            </div>
        </div>
    </div>

    <div class="container">
        @yield('content')
    </div>

    <script>
        function toggleDropdown(btn) {
            var dd = btn.parentElement;
            var wasOpen = dd.classList.contains('open');
            document.querySelectorAll('.nav-dropdown.open').forEach(function(el) { el.classList.remove('open'); });
            if (!wasOpen) dd.classList.add('open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-dropdown')) {
                document.querySelectorAll('.nav-dropdown.open').forEach(function(el) { el.classList.remove('open'); });
            }
        });
        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved) document.documentElement.setAttribute('data-theme', saved);
        })();

        // Auto-remove toasts
        setTimeout(function() {
            document.querySelectorAll('.toast').forEach(function(t) { t.remove(); });
        }, 4000);

        // Confirm modal
        var confirmForm = null;
        function confirmDelete(form, title, message) {
            confirmForm = form;
            if (title) document.getElementById('confirm-title').textContent = title;
            if (message) document.getElementById('confirm-message').textContent = message;
            document.getElementById('confirm-modal').classList.add('open');
            return false;
        }
        function closeConfirm() {
            document.getElementById('confirm-modal').classList.remove('open');
            confirmForm = null;
        }
        function doConfirm() {
            if (confirmForm) confirmForm.submit();
        }
        document.getElementById('confirm-modal').addEventListener('click', function(e) {
            if (e.target === this) closeConfirm();
        });

        // Lazy load images
        if ('IntersectionObserver' in window) {
            var imgObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
                        imgObserver.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });
            document.querySelectorAll('img[data-src]').forEach(function(img) { imgObserver.observe(img); });
        }
    </script>
    @stack('scripts')
</body>
</html>
