<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=3">
    <link rel="icon" type="image/png" href="/favicon.png?v=3">
    <link rel="apple-touch-icon" href="/favicon.png?v=3">

    <title>@yield('title', 'Tableau de bord') — {{ config('app.name', 'OFCA') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    <div class="app-wrapper">

        {{-- ── Sidebar ────────────────────────────────── --}}
        <aside class="app-sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('assets/img/logo-ofca.png') }}" alt="OFCA Logo"
                    style="height: 48px; width: auto; margin-right: 10px;">

            </a>

            {{-- Main menu --}}
            <div class="menu-section">Navigation</div>
            <ul style="list-style:none;padding:0;margin:0;">
                <li class="menu-item">
                    <a href="{{ route('dashboard') }}"
                        class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"
                                style="width:18px;height:18px"></i></span>
                        Tableau de bord
                    </a>
                </li>
            </ul>

            {{-- Géographie --}}
            <div class="menu-section">Géographie</div>
            <ul style="list-style:none;padding:0;margin:0;">
                <li class="menu-item">
                    <a href="{{ route('areas.regions.index') }}"
                        class="menu-link {{ request()->routeIs('areas.regions.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="globe" style="width:18px;height:18px"></i></span>
                        Régions
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('areas.prefectures.index') }}"
                        class="menu-link {{ request()->routeIs('areas.prefectures.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="building-2" style="width:18px;height:18px"></i></span>
                        Préfectures
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('areas.communes.index') }}"
                        class="menu-link {{ request()->routeIs('areas.communes.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="map" style="width:18px;height:18px"></i></span>
                        Communes
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('areas.cantons.index') }}"
                        class="menu-link {{ request()->routeIs('areas.cantons.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="map-pin" style="width:18px;height:18px"></i></span>
                        Cantons
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('areas.villages.index') }}"
                        class="menu-link {{ request()->routeIs('areas.villages.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="home" style="width:18px;height:18px"></i></span>
                        Villages
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('areas.zones.index') }}"
                        class="menu-link {{ request()->routeIs('areas.zones.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="target" style="width:18px;height:18px"></i></span>
                        Zones
                    </a>
                </li>
            </ul>

            {{-- Production --}}
            <div class="menu-section">Production</div>
            <ul style="list-style:none;padding:0;margin:0;">
                <li class="menu-item">
                    <a href="{{ route('organisations.index') }}"
                        class="menu-link {{ request()->routeIs('organisations.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="users" style="width:18px;height:18px"></i></span>
                        Organisations
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('producteurs.index') }}"
                        class="menu-link {{ request()->routeIs('producteurs.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="user" style="width:18px;height:18px"></i></span>
                        Producteurs
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('cultures.index') }}"
                        class="menu-link {{ request()->routeIs('cultures.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="sprout" style="width:18px;height:18px"></i></span>
                        Cultures
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('parcelles.index') }}"
                        class="menu-link {{ request()->routeIs('parcelles.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="land-plot" style="width:18px;height:18px"></i></span>
                        Parcelles
                    </a>
                </li>
            </ul>

            {{-- Certification --}}
            <div class="menu-section">Certification</div>
            <ul style="list-style:none;padding:0;margin:0;">
                <li class="menu-item">
                    <a href="{{ route('identifications.index') }}"
                        class="menu-link {{ request()->routeIs('identifications.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="file-check" style="width:18px;height:18px"></i></span>
                        Identifications
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('controles.index') }}"
                        class="menu-link {{ request()->routeIs('controles.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="clipboard-check"
                                style="width:18px;height:18px"></i></span>
                        Contrôles
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('carte.index') }}"
                        class="menu-link {{ request()->routeIs('carte.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="map" style="width:18px;height:18px"></i></span>
                        Carte
                    </a>
                </li>
            </ul>

            {{-- Admin --}}
            @role('admin')
            <div class="menu-section">Administration</div>
            <ul style="list-style:none;padding:0;margin:0;">
                <li class="menu-item">
                    <a href="{{ route('users.index') }}"
                        class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="users-round" style="width:18px;height:18px"></i></span>
                        Utilisateurs
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('parametres.index') }}"
                        class="menu-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i data-lucide="settings" style="width:18px;height:18px"></i></span>
                        Paramètres
                    </a>
                </li>
            </ul>
            @endrole
        </aside>

        {{-- ── Overlay for mobile ─────────────────────── --}}
        <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        {{-- ── Main ───────────────────────────────────── --}}
        <div class="app-main">

            {{-- Topbar --}}
            <header class="app-topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
                    <nav class="breadcrumb">
                        @yield('breadcrumb')
                    </nav>
                </div>
                <div class="topbar-actions">
                    <div class="user-info">
                        <div>
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ auth()->user()->getRoleNames()->first() ?? 'N/A' }}</div>
                        </div>
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-icon-sm btn-icon-danger" title="Déconnexion"
                            style="cursor:pointer">
                            <i data-lucide="log-out" style="width:16px;height:16px"></i>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="app-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="flash-success" id="flash-success">
                        <i data-lucide="check-circle" style="width:20px;height:20px"></i>
                        {{ session('success') }}
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash-error" id="flash-error">
                        <i data-lucide="alert-circle" style="width:20px;height:20px"></i>
                        {{ session('error') }}
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Init icons
        lucide.createIcons();

        // Sidebar toggle for mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('show');
        }

        // Auto-dismiss flash messages
        setTimeout(() => {
            document.querySelectorAll('.flash-success, .flash-error').forEach(el => {
                el.style.opacity = '0'; el.style.transition = 'opacity 0.5s';
                setTimeout(() => el.remove(), 500);
            });
        }, 6000);
    </script>

    @stack('scripts')
</body>

</html>