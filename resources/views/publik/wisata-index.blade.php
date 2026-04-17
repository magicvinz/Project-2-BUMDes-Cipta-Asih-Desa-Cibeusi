@extends('layouts.app')

@section('title', 'Destinasi Wisata')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-primary">Destinasi Wisata SIASIH</h1>
            <p class="text-muted">Jelajahi keindahan alam di Desa Cibeusi</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($wisata as $w)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <img src="{{ $w->gambar_url }}" class="card-img-top" alt="{{ $w->nama }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-primary">{{ $w->nama }}</h5>
                        <p class="card-text text-muted mb-4">{{ Str::limit(strip_tags($w->keterangan), 100) }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('public.wisata.show', $w) }}" class="btn btn-outline-primary w-100">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-0">Belum ada data destinasi wisata.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
