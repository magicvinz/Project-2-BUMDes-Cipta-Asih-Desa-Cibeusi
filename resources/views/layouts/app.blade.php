<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SI-ASIH') - BUMDes Cipta Asih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; letter-spacing: -0.5px; }
        .nav-link { font-weight: 500; }
        
        /* Bouncy Nav Item */
        .nav-item-bouncy .nav-link {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-block;
        }
        .nav-item-bouncy .nav-link:hover {
            transform: translateY(-3px) scale(1.1);
            text-shadow: 0 4px 15px rgba(255,255,255,0.9) !important;
        }

        /* Mobile Navbar Cleanup */
        .navbar-toggler { border: none; padding: 0.25rem 0.5rem; }
        .navbar-toggler:focus { box-shadow: none; outline: none; }
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(0, 0, 0, 0.15);
                border-radius: 15px;
                padding: 1rem;
                margin-top: 10px;
            }
            .dropdown-menu { 
                position: static !important; 
                float: none; 
                background: transparent !important; 
                border: none !important; 
                box-shadow: none !important; 
                margin-top: 0;
                padding-top: 0;
            }
            .dropdown-item {
                color: white !important;
                font-weight: 600;
            }
            .dropdown-item:hover, .dropdown-item:focus {
                background: rgba(255,255,255,0.1) !important;
                color: white !important;
                border-radius: 10px;
            }
            .dropdown-item.text-danger {
                color: #ffb3c6 !important;
            }
            .dropdown-divider {
                border-color: rgba(255,255,255,0.2) !important;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark shadow" style="background: linear-gradient(90deg, #00b4d8 0%, #2d6a4f 100%); border-bottom: 2px solid #52b788;">
        <div class="container">
            @php
                $brandUrl = route('home');
                if(auth()->check()) {
                    if(auth()->user()->isAdmin()) $brandUrl = route('admin.dashboard');
                    elseif(auth()->user()->isPengelolaBumdes()) $brandUrl = route('pengelola.dashboard');
                }
            @endphp
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ $brandUrl }}">
                <img src="{{ asset('images/logo-cibeusi.png') }}" onerror="this.style.display='none';" alt="Logo Cibeusi" width="45" height="45" class="rounded-circle object-fit-cover shadow-sm border border-2 border-white bg-white">
                <span class="fs-4 fw-bolder text-white" style="letter-spacing: 1.5px; font-weight: 900; text-shadow: 2px 2px 5px rgba(0,0,0,0.2);">SI-ASIH</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-lg-3 align-items-center">
                    @if(!auth()->check() || auth()->user()->isPengunjung())
                    <li class="nav-item nav-item-bouncy">
                        <a class="nav-link text-white opacity-100 fs-6 px-3" style="font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item nav-item-bouncy">
                        <a class="nav-link text-white opacity-100 fs-6 px-3" style="font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);" href="{{ route('public.wisata.index') }}">Wisata</a>
                    </li>
                    <li class="nav-item nav-item-bouncy">
                        <a class="nav-link text-white opacity-100 fs-6 px-3" style="font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);" href="{{ route('public.produk-khas.index') }}">Produk Khas</a>
                    </li>
                    @endif

                    @guest
                    <li class="nav-item text-center mt-2 mt-lg-0 ms-lg-4">
                        <a href="{{ route('login') }}" class="btn btn-outline-light px-4 rounded-pill fw-bold" style="border-width: 2px;">Login</a>
                    </li>
                    <li class="nav-item text-center mt-2 mt-lg-0 ms-lg-2">
                        <a href="{{ route('register') }}" class="btn btn-light px-4 rounded-pill fw-bold shadow-sm" style="color: #2d6a4f;">Daftar</a>
                    </li>
                    @endguest
                </ul>

                    @auth
                    <li class="nav-item dropdown text-center ms-lg-3 mt-3 mt-lg-0">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-center text-lg-start border-0 shadow-sm rounded-4 mt-2" aria-labelledby="navbarDropdown">
                            @if(auth()->user()->isPengunjung())
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengunjung.profil.index') }}">Profil</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengunjung.tiket.my') }}">Tiket saya</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengunjung.dashboard') }}">Pesan tiket</a></li>
                            @elseif(auth()->user()->isAdmin())
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('admin.validasi.index') }}">Scan QR</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('admin.history-validasi') }}">History Validasi</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('admin.laporan') }}">Laporan Penjualan Tiket</a></li>
                            @elseif(auth()->user()->isPengelolaBumdes())
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengelola.dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengelola.wisata.index') }}">Wisata</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengelola.produk-khas.index') }}">Produk Khas</a></li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('pengelola.laporan.index') }}">Laporan Penjualan Tiket</a></li>
                            @endif

                            <li><hr class="dropdown-divider opacity-25 mx-3"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item px-4 py-2 text-danger fw-bold">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
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

    <footer class="py-4 mt-auto shadow-lg" style="background: linear-gradient(90deg, #2d6a4f 0%, #00b4d8 100%);">
        <div class="container text-center text-white">
            <span class="fs-6 fw-bold">SI-ASIH &copy; {{ date('Y') }}</span> <br>
            <small style="color: #e0fbfc;">BUMDes Cipta Asih Desa Cibeusi</small>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("turbo:load", function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                });
            }
            
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
