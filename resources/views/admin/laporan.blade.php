@extends('layouts.app')

@section('title', 'Laporan Penjualan – ' . $wisata->nama)

@section('content')
{{-- ── Header ────────────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fs-4 fw-semibold mb-1">Laporan Penjualan</h4>
        <p class="text-muted mb-0 small">{{ $wisata->nama }}</p>
    </div>
    <div class="d-flex flex-column flex-sm-row gap-2">
        <a href="{{ route('admin.laporan.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak
        </a>
    </div>
</div>

{{-- ── Filter ─────────────────────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form id="form-laporan" method="get" action="{{ route('admin.laporan') }}" class="row g-3 align-items-end">
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

{{-- ── Kartu Ringkasan ────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4" id="summary-cards">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Tiket Online</p>
                <p class="fs-4 fw-bold mb-0 text-primary" id="sum-tiket-online">{{ $totalTiketOnline }}</p>
                <p class="text-muted small mb-0">tiket</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Pendapatan Online</p>
                <p class="fs-5 fw-bold mb-0 text-success" id="sum-pendapatan-online">Rp {{ number_format($totalPendapatanOnline, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Tiket Offline</p>
                <p class="fs-4 fw-bold mb-0 text-warning" id="sum-tiket-offline">{{ $totalTiketOffline }}</p>
                <p class="text-muted small mb-0">tiket</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Pendapatan Offline</p>
                <p class="fs-5 fw-bold mb-0 text-warning" id="sum-pendapatan-offline">Rp {{ number_format($totalPendapatanOffline, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold text-primary">Grand Total (Online + Offline)</span>
                <span class="fs-5 fw-bold text-primary" id="sum-grand-total">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Loading indicator --}}
<div id="laporan-loading" class="text-center py-4 text-muted d-none">
    <div class="spinner-border text-primary me-2" role="status" style="width:1.5rem;height:1.5rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    Memuat laporan...
</div>

<div id="laporan-content">
    {{-- ── Tabel Penjualan Online ─────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-1">
                <i class="bi bi-globe2 text-primary me-2"></i>
                Penjualan Online
                <span id="label-periode" class="text-muted fw-normal fs-6 ms-2">{{ $label }}</span>
            </h5>
            <p class="text-muted small mb-3">Tiket yang dipesan dan dibayar melalui sistem.</p>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary small">Waktu</th>
                            <th class="text-uppercase text-secondary small">Kode Tiket</th>
                            <th class="text-uppercase text-secondary small">Pemesan</th>
                            <th class="text-uppercase text-secondary small text-center">Jumlah</th>
                            <th class="text-uppercase text-secondary small text-end">Total</th>
                            <th class="text-uppercase text-secondary small text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-online">
                        @forelse($dataOnline as $d)
                        <tr>
                            <td class="small text-muted" data-label="Waktu">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-medium text-primary" data-label="Kode Tiket">{{ $d->kode_tiket }}</td>
                            <td data-label="Pemesan">{{ $d->user->name ?? '-' }}</td>
                            <td class="text-center" data-label="Jumlah">{{ $d->jumlah }}</td>
                            <td class="text-end fw-medium" data-label="Total">Rp {{ number_format($d->total_harga, 0, ',', '.') }}</td>
                            <td class="text-center" data-label="Status">
                                @if($d->status === 'used')
                                    <span class="badge bg-success">Digunakan</span>
                                @else
                                    <span class="badge bg-info text-dark">Dibayar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada penjualan online untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Tabel Penjualan Offline ────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-shop text-warning me-2"></i>
                    Penjualan Offline (Loket)
                </h5>
                <a href="{{ route('admin.penjualan-offline.create') }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-plus me-1"></i> Tambah
                </a>
            </div>
            <p class="text-muted small mb-3">Tiket yang terjual langsung di lokasi wisata.</p>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary small">Tanggal</th>
                            <th class="text-uppercase text-secondary small text-center">Tiket Terjual</th>
                            <th class="text-uppercase text-secondary small text-end">Total Pendapatan</th>
                            <th class="text-uppercase text-secondary small">Diinput Oleh</th>
                            <th class="text-uppercase text-secondary small text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-offline">
                        @forelse($dataOffline as $item)
                        <tr>
                            <td class="fw-medium" data-label="Tanggal">{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                            <td class="text-center" data-label="Tiket Terjual">
                                <span class="badge bg-warning text-dark rounded-pill">{{ $item->jumlah_tiket }}</span>
                            </td>
                            <td class="text-end fw-bold text-success" data-label="Total Pendapatan">
                                Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted" data-label="Diinput Oleh">{{ $item->creator->name ?? '-' }}</td>
                            <td class="text-center" data-label="Aksi">
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.penjualan-offline.edit', $item->id_penjualan_offline) }}"
                                       class="btn btn-edit-subtle" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-delete-subtle btn-hapus-offline"
                                        title="Hapus"
                                        data-id="{{ $item->id_penjualan_offline }}"
                                        data-tanggal="{{ $item->tanggal->translatedFormat('d F Y') }}"
                                        data-action="{{ route('admin.penjualan-offline.destroy', $item->id_penjualan_offline) }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada penjualan offline untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Konfirmasi Hapus Offline ────────────────────────────────────── --}}
<div class="modal fade" id="modalHapusOffline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width:64px;height:64px;">
                        <i class="bi bi-trash3 text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Data Offline?</h5>
                <p class="text-muted mb-4">Data penjualan offline tanggal <strong id="tanggalHapus"></strong> akan dihapus secara permanen.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapusOffline" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-medium rounded-pill">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var laporanInterval;
    var form     = document.getElementById('form-laporan');
    var periode  = document.getElementById('periode');
    var tanggal  = document.getElementById('tanggal');
    var loading  = document.getElementById('laporan-loading');
    var content  = document.getElementById('laporan-content');

    function fmt(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function badgeStatus(status) {
        return status === 'used'
            ? '<span class="badge bg-success">Digunakan</span>'
            : '<span class="badge bg-info text-dark">Dibayar</span>';
    }

    function updateLaporan(silent) {
        if (!form) return;
        var url = form.action + '?periode=' + encodeURIComponent(periode.value) + '&tanggal=' + encodeURIComponent(tanggal.value);

        if (!silent) {
            loading.classList.remove('d-none');
            content.style.opacity = '0.4';
        }

        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => {
                if (r.status === 401) {
                    if (laporanInterval) clearInterval(laporanInterval);
                    return;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                // Label
                var labelEl = document.getElementById('label-periode');
                if (labelEl) labelEl.textContent = data.label;

                // Summary cards
                document.getElementById('sum-tiket-online').textContent       = data.totalTiketOnline;
                document.getElementById('sum-pendapatan-online').textContent  = fmt(data.totalPendapatanOnline);
                document.getElementById('sum-tiket-offline').textContent      = data.totalTiketOffline;
                document.getElementById('sum-pendapatan-offline').textContent = fmt(data.totalPendapatanOffline);
                document.getElementById('sum-grand-total').textContent        = fmt(data.grandTotal);

                // Tabel Online
                var tbodyOnline = document.getElementById('tbody-online');
                if (tbodyOnline) {
                    if (data.dataOnline && data.dataOnline.length > 0) {
                        tbodyOnline.innerHTML = data.dataOnline.map(d =>
                            `<tr>
                                <td class="small text-muted" data-label="Waktu">${d.waktu}</td>
                                <td class="fw-medium text-primary" data-label="Kode Tiket">${d.kode_tiket}</td>
                                <td data-label="Pemesan">${d.pemesan}</td>
                                <td class="text-center" data-label="Jumlah">${d.jumlah}</td>
                                <td class="text-end fw-medium" data-label="Total">${fmt(d.total_harga)}</td>
                                <td class="text-center" data-label="Status">${badgeStatus(d.status)}</td>
                            </tr>`
                        ).join('');
                    } else {
                        tbodyOnline.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada penjualan online untuk periode ini.</td></tr>';
                    }
                }

                // Tabel Offline
                var tbodyOffline = document.getElementById('tbody-offline');
                if (tbodyOffline) {
                    if (data.dataOffline && data.dataOffline.length > 0) {
                        tbodyOffline.innerHTML = data.dataOffline.map(d =>
                            `<tr>
                                <td class="fw-medium" data-label="Tanggal">${d.tanggal}</td>
                                <td class="text-center" data-label="Tiket Terjual"><span class="badge bg-warning text-dark rounded-pill">${d.jumlah_tiket}</span></td>
                                <td class="text-end fw-bold text-success" data-label="Total Pendapatan">${fmt(d.total_pendapatan)}</td>
                                <td class="small text-muted" data-label="Diinput Oleh">${d.diinput_oleh}</td>
                                <td class="text-center" data-label="Aksi">
                                    <div class="btn-action-group">
                                        <a href="${d.url_edit}" class="btn btn-edit-subtle" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-delete-subtle btn-hapus-offline"
                                            data-tanggal="${d.tanggal}" data-action="${d.url_destroy}" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`
                        ).join('');
                        bindHapusOffline();
                    } else {
                        tbodyOffline.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada penjualan offline untuk periode ini.</td></tr>';
                    }
                }
            })
            .catch(() => {})
            .finally(() => {
                loading.classList.add('d-none');
                content.style.opacity = '1';
            });
    }

    // ── Bind tombol hapus offline (untuk baris yang di-render via JS) ────────
    function bindHapusOffline() {
        document.querySelectorAll('.btn-hapus-offline').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('tanggalHapus').textContent     = this.dataset.tanggal;
                document.getElementById('formHapusOffline').action      = this.dataset.action;
                new bootstrap.Modal(document.getElementById('modalHapusOffline')).show();
            });
        });
    }

    bindHapusOffline();

    if (periode && tanggal) {
        periode.addEventListener('change', () => updateLaporan(false));
        tanggal.addEventListener('change', () => updateLaporan(false));
        
        if (laporanInterval) clearInterval(laporanInterval);
        laporanInterval = setInterval(() => updateLaporan(true), 1500);
    }

    document.addEventListener("turbo:before-cache", function() {
        if (laporanInterval) clearInterval(laporanInterval);
    }, { once: true });
})();
</script>
@endpush
