<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (() => {
            const theme = localStorage.getItem('kavya-theme');
            if (theme === 'dark') {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet" integrity="sha384-rWj9FmWWt3OMqd9vBkWRhFavvVUYalYqGPoMdL1brs/qvvqz88gvLShYa4hKNyqb" crossorigin="anonymous">
    <link href="{{ asset('css/kavya.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">Kavya</a>
            <div class="d-none d-lg-flex align-items-center gap-3 me-auto ms-4">
                <a class="text-white text-decoration-none small" href="{{ route('explore') }}">Explore</a>
                <a class="text-white text-decoration-none small" href="{{ route('categories') }}">Categories</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-light btn-sm" id="theme-toggle" type="button" aria-label="Switch to dark mode">
                    <i class="fa-solid fa-moon" aria-hidden="true"></i>
                </button>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-light btn-sm" type="submit">Logout</button>
                    </form>
                @else
                    <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-light btn-sm" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container kavya-main py-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    <nav class="navbar fixed-bottom d-lg-none bg-white border-top shadow-sm reader-bottom-nav" aria-label="Reader navigation">
        <div class="container-fluid justify-content-around">
            <a class="nav-link text-center small {{ request()->routeIs('home') ? 'text-dark fw-semibold' : 'text-muted' }}" href="{{ route('home') }}">
                <i class="fa-solid fa-house d-block mb-1" aria-hidden="true"></i>Home
            </a>
            <a class="nav-link text-center small {{ request()->routeIs('explore') ? 'text-dark fw-semibold' : 'text-muted' }}" href="{{ route('explore') }}">
                <i class="fa-solid fa-compass d-block mb-1" aria-hidden="true"></i>Explore
            </a>
            <a class="nav-link text-center small {{ request()->routeIs('categories*') ? 'text-dark fw-semibold' : 'text-muted' }}" href="{{ route('categories') }}">
                <i class="fa-solid fa-layer-group d-block mb-1" aria-hidden="true"></i>Categories
            </a>
            @auth
                <a class="nav-link text-center small text-muted" href="{{ route('favorites') }}">
                    <i class="fa-solid fa-bookmark d-block mb-1" aria-hidden="true"></i>Saved
                </a>
                <a class="nav-link text-center small text-muted" href="{{ route('history') }}">
                    <i class="fa-solid fa-clock-rotate-left d-block mb-1" aria-hidden="true"></i>History
                </a>
            @else
                <a class="nav-link text-center small text-muted" href="{{ route('login') }}">
                    <i class="fa-solid fa-user d-block mb-1" aria-hidden="true"></i>Login
                </a>
            @endauth
        </div>
    </nav>
    <footer class="kavya-footer mt-5 py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-7">
                    <a class="navbar-brand text-white d-inline-block mb-2" href="{{ route('home') }}">Kavya</a>
                    <p class="mb-0 small">A quiet place for poems, stories, and every feeling between the lines.</p>
                </div>
                <div class="col-6 col-md-2">
                    <h2 class="h6 text-white">Discover</h2>
                    <a class="d-block small text-decoration-none mb-2" href="{{ route('explore') }}">Explore</a>
                    <a class="d-block small text-decoration-none" href="{{ route('categories') }}">Categories</a>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="h6 text-white">Account</h2>
                    @auth
                        <a class="d-block small text-decoration-none mb-2" href="{{ route('favorites') }}">Saved writings</a>
                        <a class="d-block small text-decoration-none" href="{{ route('history') }}">Reading history</a>
                    @else
                        <a class="d-block small text-decoration-none mb-2" href="{{ route('login') }}">Login</a>
                        <a class="d-block small text-decoration-none" href="{{ route('register') }}">Create account</a>
                    @endauth
                </div>
            </div>
            <hr class="border-secondary my-4">
            <p class="small mb-0">© {{ date('Y') }} Kavya. Every word has a feeling.</p>
        </div>
    </footer>
    @stack('scripts')
    <script>
        (() => {
            const toggle = document.getElementById('theme-toggle');
            if (!toggle) {
                return;
            }

            const icon = toggle.querySelector('i');
            const updateThemeButton = () => {
                const isDark = document.documentElement.dataset.theme === 'dark';
                icon.className = `fa-solid fa-${isDark ? 'sun' : 'moon'}`;
                toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            };

            toggle.addEventListener('click', () => {
                const isDark = document.documentElement.dataset.theme === 'dark';
                if (isDark) {
                    delete document.documentElement.dataset.theme;
                    localStorage.setItem('kavya-theme', 'light');
                } else {
                    document.documentElement.dataset.theme = 'dark';
                    localStorage.setItem('kavya-theme', 'dark');
                }
                updateThemeButton();
            });

            updateThemeButton();
        })();
    </script>
</body>
</html>
