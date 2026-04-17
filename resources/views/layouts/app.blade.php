<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SI-ASIH') - BUMDes Cipta Asih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; }
        .nav-link { font-weight: 500; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">SI-ASIH</a>
                @elseif(auth()->user()->isPengelolaBumdes())
                    <a class="navbar-brand" href="{{ route('pengelola.dashboard') }}">SI-ASIH</a>
                @else
                    <a class="navbar-brand" href="{{ route('home') }}">SI-ASIH</a>
                @endif
            @else
                <a class="navbar-brand" href="{{ route('home') }}">SI-ASIH</a>
            @endauth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-lg-3 align-items-center">
                    @if(!auth()->check() || auth()->user()->isPengunjung())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.wisata.index') }}">Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.produk-khas.index') }}">Produk Khas</a>
                    </li>
                    @endif

                    @guest
                    <li class="nav-item text-center mt-2 mt-lg-0 ms-lg-4">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary px-4">Login</a>
                    </li>
                    <li class="nav-item text-center mt-2 mt-lg-0 ms-lg-2">
                        <a href="{{ route('register') }}" class="btn btn-primary px-4">Daftar</a>
                    </li>
                    @endguest
                </ul>

                @auth
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start ms-lg-3 mt-3 mt-lg-0">
                    <div class="nav-item dropdown text-center">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-center text-lg-start" aria-labelledby="navbarDropdown">
                            @if(auth()->user()->isPengunjung())
                            <li><a class="dropdown-item" href="{{ route('pengunjung.dashboard') }}">Pesan Tiket</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengunjung.tiket.my') }}">Tiket Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengunjung.profil.index') }}">Profil Saya</a></li>
                            @elseif(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.validasi.index') }}">Scan QR</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.history-validasi') }}">History Validasi</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.laporan') }}">Laporan Penjualan Tiket</a></li>
                            @elseif(auth()->user()->isPengelolaBumdes())
                            <li><a class="dropdown-item" href="{{ route('pengelola.dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengelola.wisata.index') }}">Wisata</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengelola.produk-khas.index') }}">Produk Khas</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengelola.laporan.index') }}">Laporan Penjualan Tiket</a></li>

                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('password.edit') }}">Ubah Kata Sandi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </main>

    <footer class="bg-white border-top py-4 mt-4">
        <div class="container text-center text-muted small">
            SI-ASIH &copy; {{ date('Y') }} BUMDes Cipta Asih Desa Cibeusi
        </div>
    </footer>

    <script>
        document.addEventListener("turbo:load", function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    if (typeof bootstrap !== 'undefined') {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    } else {
                        alert.classList.remove('show');
                        setTimeout(function() { alert.remove(); }, 150);
                    }
                });
            }, 10000); // 10 detik
        });
    </script>

    @stack('scripts')
</body>
</html>
