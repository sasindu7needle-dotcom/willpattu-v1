<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · Wilpattu Park Admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script defer src="{{ asset('js/admin.js') }}"></script>
</head>
<body class="admin-app">
    <button class="sidebar-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" data-sidebar-toggle>☰</button>
    <div class="sidebar-backdrop" data-sidebar-backdrop></div>

    <aside class="admin-sidebar" data-sidebar>
        <a class="admin-brand sidebar-brand" href="{{ route('admin.dashboard') }}">
            <span class="admin-crest">❧</span>
            <span>WILPATTU PARK<small>ADMINISTRATION</small></span>
        </a>

        <nav class="sidebar-nav" aria-label="Admin navigation">
            <span class="sidebar-label">OVERVIEW</span>
            <a class="sidebar-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}"><span class="nav-icon">⌂</span> Dashboard</a>

            <span class="sidebar-label settings-label">SETTINGS</span>
            <div class="settings-heading"><span>Page settings</span><small>{{ count(config('content.pages')) + 1 }}</small></div>
            <div class="page-nav">
                <a @class(['active' => request()->routeIs('admin.pages.home.*')]) href="{{ route('admin.pages.home.edit') }}"><span>Home page</span><small>→</small></a>
                @foreach (config('content.pages') as $slug => $page)
                    <a @class(['active' => in_array($slug, config('park.customizable_pages')) && request()->routeIs('admin.pages.'.$slug.'.*')]) href="{{ in_array($slug, config('park.customizable_pages')) ? route('admin.pages.'.$slug.'.edit') : route('admin.dashboard').'#page-'.$slug }}"><span>{{ ucwords(str_replace('-', ' ', $slug)) }}</span><small>→</small></a>
                @endforeach
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('home') }}" target="_blank" rel="noopener">View live website <span>↗</span></a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Sign out <span>→</span></button>
            </form>
        </div>
    </aside>

    <div class="admin-content-shell">
        <header class="admin-topbar">
            <div>
                <span class="admin-eyebrow">WILPATTU PARK</span>
                <p>Content administration</p>
            </div>
            <div class="admin-profile">
                <span class="profile-mark">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span>{{ auth()->user()->name }}<small>Administrator</small></span>
            </div>
        </header>

        <main class="admin-main">
            @yield('content')
        </main>
    </div>

    <dialog class="admin-confirm-dialog" aria-labelledby="confirm-dialog-title" aria-describedby="confirm-dialog-description" data-confirm-dialog>
        <div class="confirm-dialog-content">
            <button class="confirm-dialog-close" type="button" aria-label="Close confirmation" data-confirm-cancel>×</button>
            <span class="confirm-dialog-icon" aria-hidden="true">!</span>
            <span class="admin-eyebrow">CONFIRM DELETION</span>
            <h2 id="confirm-dialog-title">Delete this section?</h2>
            <p id="confirm-dialog-description">You are about to permanently delete <strong data-confirm-name></strong>. This action cannot be undone.</p>
            <div class="confirm-dialog-actions">
                <button class="confirm-cancel-button" type="button" data-confirm-cancel>Keep section</button>
                <button class="confirm-delete-button" type="button" data-confirm-accept>Delete section</button>
            </div>
        </div>
    </dialog>
</body>
</html>
