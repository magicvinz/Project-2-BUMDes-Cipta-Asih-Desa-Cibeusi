@extends('layouts.app')

@section('title', 'Laporan Gabungan')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Laporan Penjualan Semua Wisata</h4>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form id="form-laporan" method="get" action="{{ route('pengelola.laporan.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-medium">Periode</label>
                <select name="periode" id="periode" class="form-select">
                    <option value="hari" {{ $periode === 'hari' ? 'selected' : '' }}>Harian</option>
                    <option value="minggu" {{ $periode === 'minggu' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm border-0" id="card-laporan">
    <div class="card-body p-0 p-lg-4">
        <div id="laporan-loading" class="text-center py-5 text-muted d-none">
            <div class="spinner-border text-primary me-2" role="status" style="width: 1.5rem; height: 1.5rem;">
                <span class="visually-hidden">Loading...</span>
            </div> 
            Memuat laporan...
        </div>
        <div id="laporan-content" class="px-3 px-lg-0 py-3 py-lg-0">
            <h5 id="laporan-label" class="card-title fw-semibold">{{ $label }}</h5>
            <p class="text-muted mt-1 mb-4">Total tiket: <strong id="laporan-total-tiket">{{ $totalTiket }}</strong> | Total pendapatan: <strong id="laporan-total-pendapatan" class="text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></p>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary small">Wisata</th>
                            <th class="text-uppercase text-secondary small text-center">Transaksi</th>
                            <th class="text-uppercase text-secondary small text-center">Jumlah Tiket</th>
                            <th class="text-uppercase text-secondary small text-end">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="laporan-tbody" class="border-top-0">
                        @foreach($rekap as $r)
                        <tr>
                            <td class="fw-medium text-primary">{{ $r['wisata']->nama }}</td>
                            <td class="text-center">{{ $r['transaksi'] }}</td>
                            <td class="text-center">{{ $r['jumlah_tiket'] }}</td>
                            <td class="text-end fw-medium text-success">Rp {{ number_format($r['pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var form = document.getElementById('form-laporan');
    var periode = document.getElementById('periode');
    var tanggal = document.getElementById('tanggal');
    var loadingEl = document.getElementById('laporan-loading');
    var contentEl = document.getElementById('laporan-content');
    var labelEl = document.getElementById('laporan-label');
    var totalTiketEl = document.getElementById('laporan-total-tiket');
    var totalPendapatanEl = document.getElementById('laporan-total-pendapatan');
    var tbodyEl = document.getElementById('laporan-tbody');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function updateLaporan(skipLoading) {
        if (!form || !loadingEl || !contentEl) return;
        var url = form.action + '?periode=' + encodeURIComponent(periode.value) + '&tanggal=' + encodeURIComponent(tanggal.value);
        if (!skipLoading) {
            loadingEl.classList.remove('hidden');
            contentEl.style.opacity = '0.5';
        }

        fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (labelEl) labelEl.textContent = data.label;
            if (totalTiketEl) totalTiketEl.textContent = data.totalTiket;
            if (totalPendapatanEl) totalPendapatanEl.textContent = formatRupiah(data.totalPendapatan);
            if (tbodyEl && data.rekap) {
                tbodyEl.innerHTML = data.rekap.map(function(r) {
                    return '<tr class="hover:bg-gray-50"><td class="px-4 py-3">' + (r.wisata_nama || '') + '</td><td class="px-4 py-3">' + r.transaksi + '</td><td class="px-4 py-3">' + r.jumlah_tiket + '</td><td class="px-4 py-3">' + formatRupiah(r.pendapatan) + '</td></tr>';
                }).join('');
            }
        })
        .catch(function() {
            if (totalTiketEl) totalTiketEl.textContent = '—';
            if (totalPendapatanEl) totalPendapatanEl.textContent = '—';
            if (tbodyEl) tbodyEl.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Gagal memuat data. Coba lagi.</td></tr>';
        })
        .finally(function() {
            loadingEl.classList.add('hidden');
            contentEl.style.opacity = '';
        });
    }

    if (form && periode && tanggal) {
        periode.addEventListener('change', function() { updateLaporan(false); });
        tanggal.addEventListener('change', function() { updateLaporan(false); });
        setInterval(function() { updateLaporan(true); }, 1500);
    }
})();
</script>
@endpush
