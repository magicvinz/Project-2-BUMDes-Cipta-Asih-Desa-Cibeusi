@extends('layouts.app')

@section('title', $wisata->nama)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.wisata.index') }}" class="text-decoration-none">Wisata</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $wisata->nama }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-bold text-primary mb-3">{{ $wisata->nama }}</h1>
            <img src="{{ $wisata->gambar_url }}" class="img-fluid rounded shadow-sm mb-4 w-100" alt="{{ $wisata->nama }}" style="max-height: 400px; object-fit: cover;">
            
            <div class="bg-white p-4 rounded shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Tentang Wisata</h5>
                <div class="text-muted lh-lg">
                    {!! nl2br(e($wisata->deskripsi)) !!}
                </div>
            </div>

            <!-- Galeri Foto -->
            @if($wisata->galleries && count($wisata->galleries) > 0)
                <h5 class="fw-bold text-primary mt-5 mb-3">Galeri Foto</h5>
                <div class="row row-cols-2 row-cols-md-3 g-3 mb-4">
                    @foreach($wisata->galleries as $gallery)
                    <div class="col">
                        <a href="{{ Storage::url($gallery['image']) }}" target="_blank">
                            <img src="{{ Storage::url($gallery['image']) }}" class="img-fluid rounded shadow-sm w-100" style="height: 150px; object-fit: cover;" alt="{{ $gallery['caption'] ?? $wisata->nama }}">
                        </a>
                        @if($gallery['caption'])
                            <div class="text-center small text-muted mt-1">{{ $gallery['caption'] }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif

            <!-- Ulasan Pengunjung -->
            <div class="mt-5 mb-4">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="fw-bold mb-0 me-3">Ulasan Pengunjung</h5>
                    @if($wisata->reviews->count() > 0)
                        <div class="badge bg-warning text-dark px-2 py-1 fs-6 rounded">
                            <i class="bi bi-star-fill text-dark me-1"></i> {{ number_format($wisata->average_rating, 1) }}
                        </div>
                        <span class="text-muted ms-2 small">({{ $wisata->reviews->count() }} ulasan)</span>
                    @endif
                </div>

                @forelse($wisata->reviews as $review)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">{{ $review->user->name ?? 'Pengunjung' }}</h6>
                                <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-warning mb-2 small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-2 text-secondary">{{ $review->comment }}</p>
                            @if($review->foto_url)
                                <a href="{{ $review->foto_url }}" target="_blank">
                                    <img src="{{ $review->foto_url }}" alt="Foto ulasan {{ $review->user->name }}" class="img-fluid rounded border mt-2" style="max-height: 120px; object-fit: cover;">
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border text-center text-muted">
                        Belum ada ulasan untuk wisata ini.
                    </div>
                @endforelse
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">Informasi Tiket</h5>
                    <hr>
                    @if($wisata->isCurugCibarebeuy())
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="text-muted">Kunjungan</span>
                            <span class="fw-bold">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                            <span class="text-muted">Camping</span>
                            <span class="fw-bold">Rp {{ number_format(\App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Harga per tiket</span>
                            <span class="fw-bold fs-5">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->isPengunjung())
                        <a href="{{ route('pengunjung.tiket.create', $wisata) }}" class="btn btn-primary w-100 mb-2">Pesan Tiket Sekarang</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">Login untuk Memesan</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
