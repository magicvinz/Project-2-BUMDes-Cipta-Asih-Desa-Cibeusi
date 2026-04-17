@extends('layouts.app')

@section('title', $produkKhas->nama)

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.produk-khas.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk Khas
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <img src="{{ $produkKhas->gambar_url }}" alt="{{ $produkKhas->nama }}" class="w-100 rounded-3 mb-4" style="height: 300px; object-fit: cover;">
                <h4 class="card-title fs-4 fw-semibold">{{ $produkKhas->nama }}</h4>
                <p class="text-secondary small mt-1">Urutan: {{ $produkKhas->urutan }}</p>
                @if($produkKhas->keterangan)
                <p class="card-text text-secondary mt-3">{{ $produkKhas->keterangan }}</p>
                @endif

            </div>
        </div>

        <!-- Galeri Foto Produk -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Galeri Produk</h5>

                <form action="{{ route('pengelola.produk-khas.gallery.store', $produkKhas) }}" method="POST" enctype="multipart/form-data" class="mb-4 bg-light p-3 rounded">
                    @csrf
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="file" name="image" class="form-control" required accept="image/*">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="caption" class="form-control" placeholder="Keterangan foto (opsional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </div>
                    </div>
                </form>

                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @forelse($produkKhas->galleries ?? [] as $index => $gallery)
                    <div class="col">
                        <div class="position-relative border rounded p-1">
                            <img src="{{ Storage::url($gallery['image']) }}" class="img-fluid rounded w-100" style="height: 150px; object-fit: cover;">
                            @if($gallery['caption'])
                                <div class="small text-muted mt-1 text-truncate px-1" title="{{ $gallery['caption'] }}">{{ $gallery['caption'] }}</div>
                            @endif
                            <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus-foto"
                                data-action="{{ route('pengelola.produk-khas.gallery.destroy', ['produkKhas' => $produkKhas->id_produk_khas, 'index' => $index]) }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">Belum ada foto galeri untuk produk khas ini.</div>
                    @endforelse
                </div>
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
                <div class="d-flex gap-2 justify-content-center">
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
