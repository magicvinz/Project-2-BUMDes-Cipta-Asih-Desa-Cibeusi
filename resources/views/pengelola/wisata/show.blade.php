@extends('layouts.app')

@section('title', $wisata->nama)

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.wisata.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Wisata
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @if($wisata->gambar_url)
                <img src="{{ $wisata->gambar_url }}" alt="{{ $wisata->nama }}" class="w-100 rounded-3 mb-4" style="height: 300px; object-fit: cover;">
                @endif

                <h4 class="card-title fs-4 fw-semibold">{{ $wisata->nama }}</h4>
                @if($wisata->isCurugCibarebeuy())
                    <div class="mt-2">
                        <p class="text-primary fw-medium mb-1">Kunjungan: Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} <span class="text-muted fs-6 fw-normal">/ tiket</span></p>
                        <p class="text-primary fw-medium mb-0">Camping: Rp {{ number_format(\App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG, 0, ',', '.') }} <span class="text-muted fs-6 fw-normal">/ tiket</span></p>
                    </div>
                @else
                    <p class="text-primary fw-medium fs-5 mt-2">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} <span class="text-muted fs-6 fw-normal">/ tiket</span></p>
                @endif

                @if($wisata->deskripsi)
                <p class="card-text text-secondary mt-4">{{ $wisata->deskripsi }}</p>
                @endif
            </div>
        </div>

        <!-- Galeri Foto -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Galeri Foto</h5>

                <form action="{{ route('pengelola.wisata.gallery.store', $wisata) }}" method="POST" enctype="multipart/form-data" class="mb-4 bg-light p-3 rounded">
                    @csrf
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="file" name="image" class="form-control" required accept="image/*">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="caption" class="form-control" placeholder="Deskripsi gambar (opsional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </div>
                    </div>
                </form>

                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @forelse($wisata->galleries ?? [] as $index => $gallery)
                    <div class="col">
                        <div class="position-relative border rounded p-1">
                            <img src="{{ Storage::url($gallery['image']) }}" class="img-fluid rounded w-100" style="height: 150px; object-fit: cover;">
                            @if($gallery['caption'])
                                <div class="small text-muted mt-1 text-truncate px-1" title="{{ $gallery['caption'] }}">{{ $gallery['caption'] }}</div>
                            @endif
                            <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus-foto"
                                data-action="{{ route('pengelola.wisata.gallery.destroy', ['wisata' => $wisata->id_wisata, 'index' => $index]) }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">Belum ada foto di galeri wisata ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Ulasan Pengunjung -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
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
                    <div class="card shadow-sm border-0 mb-3 bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">{{ $review->user->name ?? 'Pengunjung' }}</h6>
                                <span class="text-muted small">{{ $review->created_at->format('d/m/Y H:i') }}</span>
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
    </div>
</div>

{{-- Modal Konfirmasi Hapus Foto Galeri --}}
<div class="modal fade" id="modalHapusFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width:64px;height:64px;">
                        <i class="bi bi-image text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Foto?</h5>
                <p class="text-muted mb-4">Foto ini akan dihapus dari galeri secara permanen.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapusFoto" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-medium rounded-pill">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-hapus-foto').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('formHapusFoto').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('modalHapusFoto')).show();
    });
});
</script>
@endpush
@endsection
