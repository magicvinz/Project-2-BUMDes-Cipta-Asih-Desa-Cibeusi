@extends('layouts.app')

@section('title', 'Input Penjualan Offline')

@section('content')
<div class="mb-4">
    <h4 class="fs-4 fw-semibold mb-1">Input Penjualan Offline</h4>
    <p class="text-muted mb-0 small">Wisata: <strong>{{ $wisata->nama }}</strong>
        @if($wisata->hasCamping())
            | <strong>Kunjungan</strong> Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} · <strong>Camping</strong> Rp {{ number_format($wisata->harga_camping_efektif, 0, ',', '.') }} <span class="text-muted">per tiket</span>
        @else
            | Harga tiket: <strong>Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</strong>
        @endif
    </p>
</div>

<div class="card shadow-sm border-0" style="max-width: 600px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.penjualan-offline.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="tanggal" class="form-label fw-medium">Tanggal Penjualan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                    value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                @error('tanggal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_tiket" class="form-label fw-medium">Jumlah Tiket Terjual <span class="text-danger">*</span></label>
                <input type="number" name="jumlah_tiket" id="jumlah_tiket"
                       class="form-control @error('jumlah_tiket') is-invalid @enderror"
                       value="{{ old('jumlah_tiket', 1) }}" min="1" required>
                @error('jumlah_tiket')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($wisata->hasCamping())
            <div class="mb-3">
                <label for="keterangan" class="form-label fw-medium">Keterangan</label>
                <select name="keterangan" id="keterangan" class="form-select">
                    <option value="">Kunjungan</option>
                    <option value="camping" {{ old('keterangan') === 'camping' ? 'selected' : '' }}>Camping</option>
                </select>
            </div>
            @endif

            <div class="alert alert-info alert-permanent py-2 px-3 small border-0 bg-info bg-opacity-10 text-info-emphasis mb-3" id="preview-total">
                <i class="bi bi-info-circle me-1"></i>
                Total pendapatan akan dihitung otomatis berdasarkan jumlah tiket × harga.
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@php $hargaCampingVal = $wisata->harga_camping_efektif; @endphp
<script>
(function() {
    function initCreateOffline() {
        var jumlahInput = document.getElementById('jumlah_tiket');
        var keteranganInput = document.getElementById('keterangan');
        var previewEl = document.getElementById('preview-total');
        var hargaDasar = parseInt("{{ $wisata->harga_tiket }}", 10) || 0;
        var hargaCamping = parseInt("{{ $hargaCampingVal }}", 10) || 0;

        if (!jumlahInput || !previewEl) return;

        function updatePreview() {
            var jumlah = parseInt(jumlahInput.value) || 0;
            var harga = hargaDasar;
            if (keteranganInput && keteranganInput.value === 'camping') {
                harga = hargaCamping;
            }
            var total = jumlah * harga;
            previewEl.innerHTML = '<i class="bi bi-calculator me-1"></i> Estimasi total: <strong>Rp ' +
                total.toLocaleString('id-ID') + '</strong> (' + jumlah + ' tiket × Rp ' + harga.toLocaleString('id-ID') + ')';
        }

        jumlahInput.addEventListener('input', updatePreview);
        if (keteranganInput) keteranganInput.addEventListener('change', updatePreview);
        updatePreview();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('turbo:load', initCreateOffline);
    } else {
        document.addEventListener('turbo:load', initCreateOffline);
        initCreateOffline();
    }
})();
</script>
@endpush
