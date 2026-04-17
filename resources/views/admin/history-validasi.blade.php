@extends('layouts.app')

@section('title', 'History Validasi Tiket')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">History Validasi Tiket – {{ $wisata->nama }}</h4>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form id="form-history" method="get" action="{{ route('admin.history-validasi') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-medium">Periode</label>
                <select name="periode" id="periode" class="form-select">
                    <option value="hari"   {{ $periode === 'hari'   ? 'selected' : '' }}>Harian</option>
                    <option value="minggu" {{ $periode === 'minggu' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulan"  {{ $periode === 'bulan'  ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 p-lg-4">
        <div id="history-loading" class="text-center py-5 text-muted d-none">
            <div class="spinner-border text-primary me-2" role="status" style="width: 1.5rem; height: 1.5rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            Memuat data...
        </div>
        <div id="history-content" class="px-3 px-lg-0 py-3 py-lg-0">
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 px-4 py-3">
                    <div class="small fw-medium mb-1">Periode</div>
                    <div class="fw-bold fs-6" id="history-label">{{ $label }}</div>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 px-4 py-3">
                    <div class="small fw-medium mb-1">Total Tiket Divalidasi</div>
                    <div class="fw-bold fs-5" id="history-total-tiket">{{ $totalTiket }}</div>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 px-4 py-3">
                    <div class="small fw-medium mb-1">Jumlah Transaksi</div>
                    <div class="fw-bold fs-5" id="history-total-validasi">{{ $totalValidasi }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary small">Waktu Validasi</th>
                            <th class="text-uppercase text-secondary small">Kode Tiket</th>
                            <th class="text-uppercase text-secondary small">Pemesan</th>
                            <th class="text-uppercase text-secondary small text-center">Jumlah</th>
                            <th class="text-uppercase text-secondary small">Tgl Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody">
                        @forelse($data as $d)
                        <tr>
                            <td>{{ $d->used_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="fw-medium text-primary">{{ $d->kode_tiket }}</td>
                            <td>{{ $d->user->name ?? '-' }}</td>
                            <td class="text-center">{{ $d->jumlah }}</td>
                            <td>{{ $d->tanggal_berkunjung?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada tiket yang divalidasi pada periode ini.</td>
                        </tr>
                        @endforelse
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
    function initHistoryValidasi() {
        var form      = document.getElementById('form-history');
        var periode   = document.getElementById('periode');
        var tanggal   = document.getElementById('tanggal');
        var loadingEl = document.getElementById('history-loading');
        var contentEl = document.getElementById('history-content');
        var labelEl   = document.getElementById('history-label');
        var totalTiketEl    = document.getElementById('history-total-tiket');
        var totalValidasiEl = document.getElementById('history-total-validasi');
        var tbodyEl   = document.getElementById('history-tbody');

        if (!form) return;

        function updateHistory(skipLoading) {
            var url = form.action + '?periode=' + encodeURIComponent(periode.value) + '&tanggal=' + encodeURIComponent(tanggal.value);
            if (!skipLoading) {
                loadingEl.classList.remove('d-none');
                contentEl.style.opacity = '0.5';
            }
            fetch(url, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (labelEl) labelEl.textContent = res.label;
                if (totalTiketEl) totalTiketEl.textContent = res.totalTiket;
                if (totalValidasiEl) totalValidasiEl.textContent = res.totalValidasi;
                if (tbodyEl) {
                    if (res.data && res.data.length > 0) {
                        tbodyEl.innerHTML = res.data.map(function(d) {
                            return '<tr>' +
                                '<td>' + d.waktu_validasi + '</td>' +
                                '<td class="fw-medium text-primary">' + d.kode_tiket + '</td>' +
                                '<td>' + d.pemesan + '</td>' +
                                '<td class="text-center">' + d.jumlah + '</td>' +
                                '<td>' + d.tanggal_kunjungan + '</td>' +
                            '</tr>';
                        }).join('');
                    } else {
                        tbodyEl.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada tiket yang divalidasi pada periode ini.</td></tr>';
                    }
                }
            })
            .catch(function() {})
            .finally(function() {
                loadingEl.classList.add('d-none');
                contentEl.style.opacity = '1';
            });
        }

        periode.addEventListener('change', function() { updateHistory(false); });
        tanggal.addEventListener('change', function() { updateHistory(false); });
        var ivId = setInterval(function() { updateHistory(true); }, 5000);
    }
    document.addEventListener("turbo:load", initHistoryValidasi);
})();
</script>
@endpush
