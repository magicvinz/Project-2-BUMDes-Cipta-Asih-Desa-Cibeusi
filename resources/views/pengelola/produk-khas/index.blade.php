@extends('layouts.app')

@section('title', 'Kelola Produk Khas')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <h4 class="fs-4 fw-semibold mb-0">Kelola Produk Khas</h4>
    <a href="{{ route('pengelola.produk-khas.create') }}" class="btn btn-primary d-inline-flex align-items-center fw-medium">
        <i class="bi bi-plus-lg me-2"></i> Tambah Produk Khas
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-4 py-3">Produk</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Deskripsi</th>
                        <th class="text-uppercase text-secondary small px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($produk as $p)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-medium text-dark">{{ $p->nama }}</div>
                        </td>
                        <td class="px-4 py-3 small text-muted">{{ Str::limit($p->keterangan, 50) }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('pengelola.produk-khas.show', $p) }}" class="btn btn-sm btn-outline-info fw-medium me-1">Detail</a>
                            <a href="{{ route('pengelola.produk-khas.edit', $p) }}" class="btn btn-sm btn-outline-primary fw-medium me-1">Ubah</a>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger fw-medium btn-hapus"
                                data-nama="{{ $p->nama }}"
                                data-action="{{ route('pengelola.produk-khas.destroy', $p) }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-5 text-center text-muted">
                            Belum ada produk khas. <a href="{{ route('pengelola.produk-khas.create') }}" class="text-primary text-decoration-none">Tambah produk</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
                <h5 class="fw-bold mb-1">Hapus Produk Khas?</h5>
                <p class="text-muted mb-4">Data produk <strong id="namaProduk"></strong> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
                <div class="d-flex gap-2 justify-content-center">
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
        document.getElementById('namaProduk').textContent = this.dataset.nama;
        document.getElementById('formHapus').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});
</script>
@endpush
@endsection
