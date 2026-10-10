<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login · Wilpattu Park</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-login-page">
    <main class="login-shell">
        <section class="login-visual" aria-label="Wilpattu National Park">
            <img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_6.jpg') }}" alt="Sri Lankan leopard on a forest trail">
            <div class="login-visual-shade"></div>
            <a class="admin-brand" href="{{ route('home') }}">
                <span class="admin-crest">❧</span>
                <span>WILPATTU PARK<small>MAKARA RESORTS</small></span>
            </a>
            <div class="login-visual-copy">
                <span class="admin-eyebrow">THE WILDERNESS, THOUGHTFULLY CURATED</span>
                <h1>Shape every<br><em>guest journey.</em></h1>
                <p>Manage the stories and details that welcome guests to Wilpattu.</p>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-form-wrap">
                <a class="back-link" href="{{ route('home') }}">← Return to website</a>
                <span class="admin-eyebrow">ADMINISTRATION</span>
                <h2>Welcome <em>back.</em></h2>
                <p class="login-intro">Sign in to access the Wilpattu Park content dashboard.</p>

                @if ($errors->any())
                    <div class="admin-alert" role="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required autofocus>

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>

                    <button class="admin-button" type="submit">Enter dashboard <span>↗</span></button>
                </form>

                <p class="secure-note"><span>◇</span> Protected administrator access</p>
            </div>
        </section>
    </main>
</body>
</html>
