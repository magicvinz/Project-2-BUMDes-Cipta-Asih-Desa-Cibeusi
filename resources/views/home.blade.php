@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="p-5 mb-5 bg-vibrant-hero text-center overflow-hidden position-relative d-flex flex-column justify-content-center align-items-center" style="min-height: 90vh;">
    <!-- Dekorasi Floating "Ramai" (Hanya Air & Hutan) - Anti Hitbox Block -->
    <div class="position-absolute top-0 start-0 translate-middle text-info opacity-50" style="font-size: 6rem; z-index: 0; animation: floating 4s infinite; pointer-events: none;">🌊</div>
    <div class="position-absolute bottom-0 end-0 text-success opacity-50 mb-n4 me-n4" style="font-size: 8rem; z-index: 0; animation: floating 5s infinite 1s; pointer-events: none;">🌴</div>
    <div class="position-absolute top-0 end-0 text-info opacity-50 mt-4 me-5" style="font-size: 4rem; z-index: 0; animation: floating 6s infinite 2s; pointer-events: none;">💧</div>
    <div class="position-absolute bottom-0 start-0 text-success opacity-50 ms-5 mb-2" style="font-size: 5rem; z-index: 0; animation: floating 4.5s infinite 0.5s; pointer-events: none;">🍃</div>

    <div class="position-relative py-4" style="z-index: 10;">
        <span class="badge bg-light text-success mb-4 px-4 py-2 fs-6 rounded-pill shadow-sm" style="animation: floating 3s ease-in-out infinite;">🌿 Bersatu dengan Alam! 💦</span>
        <h1 class="display-3 fw-bolder text-white mb-3" style="text-shadow: 2px 3px 6px rgba(0,0,0,0.2);">Petualangan SI-ASIH</h1>
        <p class="fs-4 text-white mb-4 fw-medium" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">Mulai Petualanganmu Sekarang!</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
            @auth
                <a href="{{ route('pengunjung.dashboard') }}" class="btn btn-light btn-lg px-5 shadow fw-bold text-primary border-0 rounded-pill fs-5">🚀 Pesan Tiket!</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 border-2 fw-bold rounded-pill">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 text-success shadow fw-bold border-0 rounded-pill">🌿 Daftar Sekarang!</a>
            @endauth
        </div>
    </div>
    <!-- Wavy Divider (Gelombang Bawah) -->
    <div class="position-absolute bottom-0 start-0 w-100" style="line-height: 0; pointer-events: none; z-index: 1;">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#f0fdf4"></path>
        </svg>
    </div>
</div>

<div class="text-center mt-5 mb-5 pt-3" data-aos="fade-up">
    <h2 class="display-6 fw-bolder">
        <span class="fs-1 me-2">⛰️</span>
        <span class="text-gradient">Jelajahi Tempat Wisata</span>
        <span class="fs-1 ms-2">🌊</span>
    </h2>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @foreach($wisata as $w)
    @php /** @var \App\Models\Wisata $w */ @endphp
    <div class="col" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <div class="card h-100 shadow-sm border-0">
            <img src="{{ $w->gambar_url }}" class="card-img-top" alt="{{ $w->nama }}" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold text-primary">{{ $w->nama }}</h5>
                <p class="card-text text-muted mb-4">{{ Str::limit(strip_tags($w->deskripsi), 100) }}</p>
                <div class="mt-auto">
                    <span class="badge rounded-pill bg-primary px-3 py-2 fs-6">
                        @if($w->isCurugCibarebeuy())
                            Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }} – Rp {{ number_format(\App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG, 0, ',', '.') }}
                        @else
                            Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="text-center mt-5 mb-5 pt-5" data-aos="fade-up">
    <h2 class="display-6 fw-bolder">
        <span class="fs-1 me-2">🍯</span>
        <span class="text-gradient">Produk Khas Cibeusi</span>
        <span class="fs-1 ms-2">🌾</span>
    </h2>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    @forelse($produk as $p)
    @php /** @var \App\Models\ProdukKhas $p */ @endphp
    <div class="col" data-aos="zoom-in-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <div class="card h-100 shadow-sm border-0">
            <img src="{{ $p->gambar_url }}" class="card-img-top" alt="{{ $p->nama }}" style="height: 180px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold text-primary mb-0">{{ $p->nama }}</h6>
                @if($p->wisata)
                    <p class="text-muted mb-2" style="font-size: 0.7rem;">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $p->wisata->nama }}
                    </p>
                @endif
                <p class="card-text text-muted small mb-0">{{ Str::limit(strip_tags($p->keterangan), 80) }}</p>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <p class="text-muted small">Belum ada produk khas yang ditampilkan.</p>
    </div>
    @endforelse
</div>
@endsection
