@extends('layouts.app')

@section('title', 'Beranda')

@push('hero')
{{-- ===================== HERO SECTION (Full-Width, di luar container) ===================== --}}
<style>
    .hero-section {
        min-height: 92vh;
        background: url("{{ asset('images/background-cibeusi-dekstop.png') }}") center/cover no-repeat;
    }
    @media (max-width: 767.98px) {
        .hero-section {
            background: url("{{ asset('images/background-cibeusi-mobile.png') }}") center/cover no-repeat;
        }
        .hero-buttons {
            bottom: 22% !important;
        }
    }
</style>

<section class="hero-section position-relative overflow-hidden">

    {{-- Gradient Overlay --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(180deg, rgba(0,20,15,0.05) 0%, rgba(0,40,25,0.18) 60%, rgba(0,20,15,0.38) 100%); z-index: 1;"></div>

    {{-- Tombol di bawah layar --}}
    <div class="hero-buttons position-absolute start-0 w-100 d-flex flex-wrap justify-content-center gap-4" style="z-index: 2; bottom: 12%;">
        @auth
            <a href="{{ route('pengunjung.dashboard') }}"
                class="btn fw-bold rounded-pill shadow-lg"
                style="background: linear-gradient(135deg, #52e8a5, #00b4d8); color: #003d2b; font-size: 1.4rem; padding: 1rem 3.5rem; border: none; transition: transform 0.2s, box-shadow 0.2s;"
                onmouseover="this.style.transform='scale(1.07)'; this.style.boxShadow='0 12px 40px rgba(82,232,165,0.5)'"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''">
                🚀 Pesan Tiket Sekarang!
            </a>
        @else
            <a href="{{ route('login') }}"
                class="btn fw-bold rounded-pill"
                style="background: rgba(255,255,255,0.15); color: #fff; border: 2.5px solid rgba(255,255,255,0.75); backdrop-filter: blur(12px); font-size: 1.4rem; padding: 1rem 3.5rem; transition: all 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.28)'; this.style.transform='scale(1.05)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='scale(1)'">
                Masuk
            </a>
            <a href="{{ route('register') }}"
                class="btn fw-bold rounded-pill shadow-lg"
                style="background: linear-gradient(135deg, #52e8a5, #00b4d8); color: #003d2b; font-size: 1.4rem; padding: 1rem 3.5rem; border: none; transition: transform 0.2s, box-shadow 0.2s;"
                onmouseover="this.style.transform='scale(1.07)'; this.style.boxShadow='0 12px 40px rgba(82,232,165,0.5)'"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''">
                🌿 Daftar Sekarang!
            </a>
        @endauth
    </div>
</section>
@endpush

@section('content')
{{-- ===================== WISATA SECTION ===================== --}}
<div class="text-center mt-4 mb-4 pt-2" data-aos="fade-up">
    <p class="text-success fw-semibold mb-1 text-uppercase" style="letter-spacing: 2px; font-size: 0.82rem;">Destinasi Pilihan</p>
    <h2 class="display-6 fw-bolder mb-0">
        <span class="text-gradient">Jelajahi Tempat Wisata</span>
    </h2>
    <div class="mx-auto mt-2" style="width: 60px; height: 4px; background: linear-gradient(90deg, #00b4d8, #52b788); border-radius: 99px;"></div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @foreach($wisata as $w)
    @php /** @var \App\Models\Wisata $w */ @endphp
    <div class="col" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <a href="{{ route('public.wisata.show', $w) }}" class="text-decoration-none text-dark d-block h-100">
            <div class="card h-100 border-0 overflow-hidden"
                style="border-radius: 18px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.08);"
                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 16px 40px rgba(0,0,0,0.16)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                <div class="position-relative">
                    <img src="{{ $w->gambar_url }}" class="card-img-top" alt="{{ $w->nama }}" style="height: 210px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.45) 100%); border-radius: 18px 18px 0 0;"></div>
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold text-dark mb-2">{{ $w->nama }}</h5>
                    <p class="card-text text-muted mb-4 small">{{ Str::limit(strip_tags($w->deskripsi), 100) }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="badge px-3 py-2 fw-semibold rounded-pill" style="background: linear-gradient(135deg, #00b4d8, #52b788); color: white; font-size: 0.82rem;">
                            @if($w->hasCamping())
                                Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }} – Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}
                            @else
                                Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }}
                            @endif
                        </span>
                        <span class="fw-bold small" style="color: #00b4d8;">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- ===================== PRODUK KHAS SECTION ===================== --}}
<div class="text-center mt-5 mb-4 pt-4" data-aos="fade-up">
    <p class="text-success fw-semibold mb-1 text-uppercase" style="letter-spacing: 2px; font-size: 0.82rem;">Oleh-Oleh Unggulan</p>
    <h2 class="display-6 fw-bolder mb-0">
        <span class="text-gradient">Produk Khas Cibeusi</span>
    </h2>
    <div class="mx-auto mt-2" style="width: 60px; height: 4px; background: linear-gradient(90deg, #52b788, #00b4d8); border-radius: 99px;"></div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
    @forelse($produk as $p)
    @php /** @var \App\Models\ProdukKhas $p */ @endphp
    <div class="col" data-aos="zoom-in-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <a href="{{ route('public.produk-khas.show', $p) }}" class="text-decoration-none text-dark d-block h-100">
            <div class="card h-100 border-0 overflow-hidden"
                style="border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.08);"
                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 16px 40px rgba(0,0,0,0.16)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                <img src="{{ $p->gambar_url }}" class="card-img-top" alt="{{ $p->nama }}" style="height: 180px; object-fit: cover;">
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">{{ $p->nama }}</h6>
                    @if($p->wisata)
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">
                            <i class="bi bi-geo-alt-fill me-1 text-success"></i>{{ $p->wisata->nama }}
                        </p>
                    @endif
                    <p class="card-text text-muted small mb-3">{{ Str::limit(strip_tags($p->keterangan), 80) }}</p>
                    <div class="mt-auto d-flex justify-content-end">
                        <span class="fw-bold small" style="color: #00b4d8;">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <p class="text-muted small text-center">Belum ada produk khas yang ditampilkan.</p>
    </div>
    @endforelse
</div>

{{-- ===================== PETA & CUACA SECTION ===================== --}}
<div class="text-center mt-5 mb-4 pt-4" data-aos="fade-up">
    <p class="text-success fw-semibold mb-1 text-uppercase" style="letter-spacing: 2px; font-size: 0.82rem;">Informasi Pengunjung</p>
    <h2 class="display-6 fw-bolder mb-0">
        <span class="text-gradient">Lokasi & Cuaca</span>
    </h2>
    <div class="mx-auto mt-2" style="width: 60px; height: 4px; background: linear-gradient(90deg, #00b4d8, #52b788); border-radius: 99px;"></div>
</div>

<div class="row g-4 mb-5 pb-4">
    <!-- Peta Lokasi -->
    <div class="col-lg-7" data-aos="fade-right">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-radius: 18px;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Peta Lokasi</h5>
                <p class="text-muted small mt-1">Parkiran Curug Cibareubeuy, Cibeusi, Kec. Ciater, Subang</p>
            </div>
            <div class="card-body p-0" style="min-height: 350px;">
                <iframe 
                    width="100%" 
                    height="100%" 
                    style="border:0; min-height: 350px;" 
                    src="https://maps.google.com/maps?q=Parkiran%20Curug%20Cibareubeuy,%20Cibeusi,%20Ciater,%20Subang&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
    
    <!-- Info Cuaca -->
    <div class="col-lg-5" data-aos="fade-left">
        <div class="card border-0 shadow h-100 position-relative overflow-hidden" style="border-radius: 24px; background: linear-gradient(145deg, #ffffff, #f0f8ff);">
            <!-- Dekorasi latar belakang (lingkaran blur) -->
            <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(0, 180, 216, 0.1); filter: blur(40px); top: -50px; right: -50px;"></div>
            <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(82, 183, 136, 0.1); filter: blur(40px); bottom: -50px; left: -50px;"></div>
            
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 position-relative z-1 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bolder mb-0" style="color: #2b2d42; letter-spacing: -0.5px;">Cuaca Desa Cibeusi</h5>
                    <p class="text-primary small mt-1 mb-0 fw-bold">
                        <i class="bi bi-clock-fill me-1"></i><span id="realtime-clock">--:--:--</span> WIB
                    </p>
                </div>
                <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-cloud-check text-primary fs-4"></i>
                </div>
            </div>
            
            <div class="card-body p-4 d-flex flex-column justify-content-center position-relative z-1">
                <div id="bmkg-weather-container" class="text-center w-100">
                    <!-- Loader -->
                    <div id="bmkg-loader" class="py-5">
                        <div class="spinner-grow text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted fw-medium mb-0">Mensinkronisasi data cuaca...</p>
                    </div>
                    
                    <!-- Data Cuaca (Hidden initially) -->
                    <div id="bmkg-data" class="d-none">
                        <div class="d-flex justify-content-center align-items-center gap-4 my-3">
                            <div class="bg-white shadow-sm rounded-4 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i id="bmkg-icon" class="bi bi-cloud-sun text-info" style="font-size: 3.5rem;"></i>
                            </div>
                            <div class="text-start">
                                <h1 id="bmkg-suhu" class="display-2 fw-black mb-0" style="color: #2b2d42; letter-spacing: -2px; font-weight: 900;">--°</h1>
                                <p id="bmkg-kondisi" class="fs-5 fw-bold text-info mb-0" style="letter-spacing: -0.5px;">Kondisi</p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3 mt-4 pt-2">
                            <div class="bg-white shadow-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-droplet-half text-primary fs-5"></i>
                                <div class="text-start">
                                    <span class="d-block small text-muted" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;">Kelembaban</span>
                                    <span id="bmkg-kelembaban" class="fw-bold text-dark d-block" style="line-height: 1;">--%</span>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-wind text-success fs-5"></i>
                                <div class="text-start">
                                    <span class="d-block small text-muted" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;">Angin</span>
                                    <span id="bmkg-angin" class="fw-bold text-dark d-block" style="line-height: 1;">-- km/h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Message (Hidden initially) -->
                    <div id="bmkg-error" class="d-none text-center py-5">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-x-octagon-fill fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Gagal Memuat Cuaca</h6>
                        <p class="text-muted small mb-0" id="bmkg-error-msg">Silakan coba beberapa saat lagi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("turbo:load", function() {
    var container = document.getElementById('bmkg-weather-container');
    if (!container) return;
    
    // Real-time Clock
    function updateClock() {
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        var clockEl = document.getElementById('realtime-clock');
        if (clockEl) {
            clockEl.textContent = hours + ':' + minutes + ':' + seconds;
        }
    }
    updateClock(); // Initial call
    var clockInterval = setInterval(updateClock, 1000);
    
    // Clear interval when leaving page
    document.addEventListener("turbo:before-cache", function() {
        clearInterval(clockInterval);
    }, { once: true });
    
    var loader = document.getElementById('bmkg-loader');
    var dataDiv = document.getElementById('bmkg-data');
    var errorDiv = document.getElementById('bmkg-error');
    var errorMsg = document.getElementById('bmkg-error-msg');
    
    fetch('{{ route("api.cuaca-bmkg") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(result => {
        loader.classList.add('d-none');
        if (result.success && result.data) {
            document.getElementById('bmkg-suhu').textContent = result.data.suhu;
            document.getElementById('bmkg-kondisi').textContent = result.data.kondisi;
            document.getElementById('bmkg-kelembaban').textContent = result.data.kelembaban;
            document.getElementById('bmkg-angin').textContent = result.data.angin;
            
            var iconEl = document.getElementById('bmkg-icon');
            iconEl.className = 'display-1 ' + result.data.icon + ' text-warning';
            
            dataDiv.classList.remove('d-none');
        } else {
            errorDiv.classList.remove('d-none');
            if(result.message) errorMsg.textContent = result.message;
        }
    })
    .catch(error => {
        loader.classList.add('d-none');
        errorDiv.classList.remove('d-none');
        console.error('BMKG Fetch Error:', error);
    });
});
</script>
@endpush
