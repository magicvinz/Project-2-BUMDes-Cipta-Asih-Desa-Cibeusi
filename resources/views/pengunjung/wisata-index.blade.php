@extends('layouts.app')

@section('title', 'Pilih Wisata')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Pilih Tempat Wisata</h4>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
    @foreach($wisata as $w)
    <div class="col">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition-all">
            <div class="card-body d-flex flex-column p-4">
                <h5 class="card-title fw-semibold text-dark">{{ $w->nama }}</h5>
                <p class="card-text small text-muted mt-2 mb-4">{{ Str::limit($w->deskripsi, 80) }}</p>
                <div class="mt-auto">
                    <p class="text-primary fw-semibold mb-3">
                        @if($w->isCurugCibarebeuy())
                            Rp {{ number_format($w->harga_tiket, 0, ',', '.') }} – Rp {{ number_format(\App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}
                        @endif
                        <span class="text-muted fw-normal small">/ tiket</span>
                    </p>
                    <a href="{{ route('pengunjung.tiket.create', $w) }}" class="btn btn-primary w-100 fw-medium transition">Pesan Tiket</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

