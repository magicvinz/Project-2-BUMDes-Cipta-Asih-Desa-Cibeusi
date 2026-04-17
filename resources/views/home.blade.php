@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold text-primary">SI-ASIH</h1>
    <p class="mt-2 fs-5 text-secondary">Sistem Informasi Pemesanan Tiket Wisata Alam<br>BUMDes Cipta Asih Desa Cibeusi</p>
    <div class="mt-4">
        @auth
            <a href="{{ route('pengunjung.dashboard') }}" class="btn btn-primary btn-lg px-4">Pesan Tiket</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4 me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4">Daftar</a>
        @endauth
    </div>
</div>

<h2 class="fs-4 fw-bold text-primary mt-5 mb-4">Tempat Wisata</h2>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @foreach($wisata as $w)
    @php /** @var \App\Models\Wisata $w */ @endphp
    <div class="col">
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

<h2 class="fs-4 fw-bold text-primary mt-5 mb-4">Produk Khas Desa Cibeusi</h2>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    @forelse($produk as $p)
    @php /** @var \App\Models\ProdukKhas $p */ @endphp
    <div class="col">
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
