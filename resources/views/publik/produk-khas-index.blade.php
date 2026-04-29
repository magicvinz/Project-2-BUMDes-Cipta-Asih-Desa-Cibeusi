@extends('layouts.app')

@section('title', 'Produk Khas')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center" data-aos="fade-down">
            <h1 class="fw-bold text-primary">Produk Khas Cibeusi</h1>
            <p class="text-muted">Temukan kerajinan dan kuliner otentik dari warga lokal</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($produk as $p)
            <div class="col" data-aos="zoom-in-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="card h-100 shadow-sm border-0">
                    <img src="{{ $p->gambar_url }}" class="card-img-top" alt="{{ $p->nama }}" style="height: 180px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-bold text-primary">{{ $p->nama }}</h6>
                        <p class="card-text text-muted small mb-4">{{ Str::limit(strip_tags($p->keterangan), 80) }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('public.produk-khas.show', $p) }}" class="btn btn-outline-primary btn-sm w-100">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-0">Belum ada data produk khas.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
