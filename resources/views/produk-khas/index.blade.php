@extends('layouts.app')

@section('title', 'Produk Khas Desa Cibeusi')

@section('content')
<div class="mb-4">
    <h1 class="fs-3 fw-bold text-primary">Produk Khas Desa Cibeusi</h1>
    <p class="text-muted mt-1">Kenali produk unggulan dan khas dari Desa Cibeusi</p>
</div>

@if($produk->isEmpty())
    <div class="alert alert-info" role="alert">Belum ada produk khas yang ditampilkan.</div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($produk as $p)
        <div class="col">
            <div class="card shadow-sm border-0 h-100 transition hover-shadow">
                <div class="bg-light" style="aspect-ratio: 16/9; overflow: hidden;">
                    <img src="{{ $p->gambar_url }}" alt="{{ $p->nama }}" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-semibold text-primary">{{ $p->nama }}</h5>
                    <p class="card-text small text-muted mt-2 mb-0">{{ $p->keterangan }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
