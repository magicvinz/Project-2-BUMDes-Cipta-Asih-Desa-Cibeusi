@extends('layouts.app')

@section('title', $produk_khas->nama)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.produk-khas.index') }}" class="text-decoration-none">Produk Khas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $produk_khas->nama }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 mb-4">
            <img src="{{ $produk_khas->gambar_url }}" class="img-fluid rounded shadow-sm w-100" alt="{{ $produk_khas->nama }}" style="max-height: 400px; object-fit: cover;">
        </div>
        
        <div class="col-md-7">
            <h1 class="fw-bold text-primary mb-3">{{ $produk_khas->nama }}</h1>
            
            <div class="bg-white p-4 rounded shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Deskripsi Produk</h5>
                <div class="text-muted lh-lg">
                    {!! nl2br(e($produk_khas->keterangan)) !!}
                </div>
            </div>

            <!-- Galeri Foto Produk -->
            @if($produk_khas->galleries && count($produk_khas->galleries) > 0)
                <h5 class="fw-bold text-primary mt-4 mb-3">Galeri Produk</h5>
                <div class="row row-cols-3 row-cols-lg-4 g-3 mb-4">
                    @foreach($produk_khas->galleries as $gallery)
                    <div class="col">
                        <a href="{{ Storage::url($gallery['image']) }}" target="_blank">
                            <img src="{{ Storage::url($gallery['image']) }}" class="img-fluid rounded shadow-sm w-100" style="height: 100px; object-fit: cover;" alt="{{ $gallery['caption'] ?? $produk_khas->nama }}">
                        </a>
                        @if($gallery['caption'])
                            <div class="text-center small text-muted mt-1" style="font-size: 0.75rem;">{{ Str::limit($gallery['caption'], 20) }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
