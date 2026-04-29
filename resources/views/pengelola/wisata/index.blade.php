@extends('layouts.app')

@section('title', 'Kelola Tempat Wisata')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <h4 class="fs-4 fw-semibold mb-0">Kelola Tempat Wisata &amp; Harga Tiket</h4>
    <a href="{{ route('pengelola.wisata.create') }}" class="btn btn-primary d-inline-flex align-items-center fw-medium">
        <i class="bi bi-plus-lg me-2"></i> Tambah Wisata
    </a>
</div>

{{-- Tampilan Desktop (Tabel) --}}
<div class="card shadow-sm border-0 d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-4 py-3">Wisata</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Harga Tiket</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Deskripsi</th>
                        <th class="text-uppercase text-secondary small px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($wisata as $w)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-medium text-dark">{{ $w->nama }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($w->hasCamping())
                                <div class="small">Kunjungan: Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}</div>
                                <div class="small">Camping: Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}</div>
                            @else
                                Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 small text-muted">{{ Str::limit($w->deskripsi, 50) }}</td>
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('pengelola.wisata.show', $w) }}" class="btn btn-sm btn-outline-info fw-medium">Detail</a>
                                <a href="{{ route('pengelola.wisata.edit', $w) }}" class="btn btn-sm btn-outline-primary fw-medium">Ubah</a>
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger fw-medium btn-hapus"
                                    data-nama="{{ $w->nama }}"
                                    data-action="{{ route('pengelola.wisata.destroy', $w) }}">
                                    Hapus
                                </button>
                            </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                         <td colspan="4" class="px-4 py-5 text-center text-muted">
                             Belum ada data wisata. <a href="{{ route('pengelola.wisata.create') }}" class="text-primary text-decoration-none">Tambah wisata</a>
                         </td>
                     </tr>
                     @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tampilan Mobile (Card) --}}
<div class="d-md-none">
    @forelse($wisata as $w)
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <h5 class="card-title fw-bold text-dark mb-2">{{ $w->nama }}</h5>
            
            <div class="mb-2">
                @if($w->hasCamping())
                    <span class="badge bg-light text-dark border me-1 mb-1 fw-normal">Kunjungan: Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}</span>
                    <span class="badge bg-light text-dark border mb-1 fw-normal">Camping: Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}</span>
                @else
                    <span class="badge bg-light text-dark border mb-1 fw-normal">Tiket: Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}</span>
                @endif
            </div>

            <p class="card-text small text-muted mb-3">{{ Str::limit($w->deskripsi, 80) }}</p>
            
            <div class="d-flex gap-2">
                <a href="{{ route('pengelola.wisata.show', $w) }}" class="btn btn-sm btn-outline-info flex-fill fw-medium">Detail</a>
                <a href="{{ route('pengelola.wisata.edit', $w) }}" class="btn btn-sm btn-outline-primary flex-fill fw-medium">Ubah</a>
                <button type="button"
                    class="btn btn-sm btn-outline-danger flex-fill fw-medium btn-hapus"
                    data-nama="{{ $w->nama }}"
                    data-action="{{ route('pengelola.wisata.destroy', $w) }}">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="card shadow-sm border-0">
        <div class="card-body text-center text-muted py-5">
            Belum ada data wisata. <br><a href="{{ route('pengelola.wisata.create') }}" class="text-primary text-decoration-none mt-2 d-inline-block">Tambah wisata</a>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width:64px;height:64px;">
                        <i class="bi bi-trash3 text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Wisata?</h5>
                <p class="text-muted mb-4">Data wisata <strong id="namaWisata"></strong> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapus" method="POST" class="d-inline">
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
document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('namaWisata').textContent = this.dataset.nama;
        document.getElementById('formHapus').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});
</script>
@endpush
@endsection
