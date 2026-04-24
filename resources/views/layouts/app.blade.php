@php
    $isLoggedIn = session()->has('user_id');
    $currentUser = session('currentUser');

    $hotPath = public_path('hot');
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath)
        ? json_decode(file_get_contents($manifestPath), true)
        : [];
    $builtCss = $manifest['resources/css/app.css']['file'] ?? null;
    $builtJs = $manifest['resources/js/app.js']['file'] ?? null;

    $hour = now()->setTimezone('Asia/Kolkata')->format('H');
    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Good day';
    } else {
        $greeting = 'Good evening';
    }

    $initials = collect(explode(' ', trim($currentUser->name ?? 'User')))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $avatarPhotoUrl = !empty($currentUser?->photo_path)
        ? asset('storage/' . ltrim($currentUser->photo_path, '/'))
        : null;

    $showTopBar = request()->routeIs('admin.dashboard') || request()->routeIs('teacher.dashboard');
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1220">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Alor Disha">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <title>@yield('title', 'Alor Disha Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <script>
        (() => {
            const key = 'alordisha-theme';
            const saved = localStorage.getItem(key);
            const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = saved === 'dark' || saved === 'light' ? saved : system;
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
        })();
    </script>

    @if (file_exists($hotPath))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @if ($builtCss)
            <link rel="stylesheet" href="{{ asset('build/' . $builtCss) }}">
        @endif
        @if ($builtJs)
            <script type="module" src="{{ asset('build/' . $builtJs) }}"></script>
        @endif
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>

<body>
    <div class="app-root" x-data="appUiState()" @keydown.escape.window="closeOverlays()">
        @if ($isLoggedIn)
            <div
                class="mobile-overlay"
                x-show="fabOpen || moreOpen"
                x-transition.opacity
                @click="closeOverlays()"
                x-cloak
            ></div>

            <div class="app-layout">
                @include('components.sidebar')

                <main class="main-shell {{ $showTopBar ? '' : 'non-home-shell' }}">
                    @if ($showTopBar)
                        <header class="glass-card top-bar">
                            <div>
                                <p>{{ $greeting }},</p>
                                <h1>{{ $currentUser->name ?? 'User' }}</h1>
                            </div>

                            <div class="header-actions">
                                <button type="button" class="theme-toggle-btn" data-theme-toggle aria-label="Toggle theme">
                                    <span class="theme-switch-track">
                                        <span class="theme-switch-stars"></span>
                                        <span class="theme-switch-cloud"></span>
                                        <span class="theme-switch-thumb">
                                            <svg class="theme-switch-icon sun" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 3V5M12 19V21M5.63604 5.63604L7.05025 7.05025M16.9497 16.9497L18.364 18.364M3 12H5M19 12H21M5.63604 18.364L7.05025 16.9497M16.9497 7.05025L18.364 5.63604M16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <svg class="theme-switch-icon moon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 12.79C20.8427 14.4922 20.2039 16.1144 19.1582 17.4677C18.1125 18.821 16.7025 19.8485 15.0945 20.4297C13.4865 21.0108 11.7461 21.1228 10.0777 20.7522C8.40929 20.3817 6.87997 19.5435 5.67013 18.3354C4.46029 17.1273 3.62042 15.5992 3.24763 13.9313C2.87484 12.2635 2.98446 10.523 3.56329 8.91426C4.14212 7.3055 5.16767 5.8941 6.51957 4.84666C7.87147 3.79922 9.49278 3.15805 11.1948 2.99803C10.1981 4.34631 9.71828 6.00748 9.8413 7.68005C9.96432 9.35263 10.6821 10.9276 11.8666 12.1121C13.0511 13.2966 14.626 14.0144 16.2986 14.1374C17.9712 14.2605 19.6323 13.7806 21 12.79Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </span>
                                </button>

                                <div class="avatar-chip" aria-hidden="true">
                                    @if ($avatarPhotoUrl)
                                        <img src="{{ $avatarPhotoUrl }}" alt="{{ $currentUser->name ?? 'User' }}" class="avatar-chip-image">
                                    @else
                                        {{ $initials ?: 'U' }}
                                    @endif
                                </div>

                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button class="logout-btn" type="submit">Logout</button>
                                </form>
                            </div>
                        </header>
                    @endif

                    @yield('content')
                </main>
            </div>

            @include('components.fab-menu')
            @include('components.bottomnav')
        @else
            @yield('content')
        @endif
    </div>

    @stack('scripts')
</body>

</html>
