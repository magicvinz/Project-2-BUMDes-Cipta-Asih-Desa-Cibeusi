@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Dashboard Admin - {{ $wisata->nama }}</h4>

<div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted small fw-medium text-uppercase">Tiket Hari Ini</h6>
                <h3 class="display-6 fw-bold mt-1 mb-0 text-primary" id="admin-hari-ini">{{ $hariIni }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted small fw-medium text-uppercase">Tiket Bulan Ini</h6>
                <h3 class="display-6 fw-bold mt-1 mb-0 text-success" id="admin-bulan-ini">{{ $bulanIni }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted small fw-medium text-uppercase">Pendapatan Bulan Ini</h6>
                <h3 class="display-6 fw-bold mt-1 mb-0 text-info" id="admin-pendapatan">Rp {{ number_format($totalPendapatanBulan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Aksi Cepat</h5>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <a href="{{ route('admin.validasi.index') }}" class="btn btn-primary d-inline-flex align-items-center fw-medium">
                <i class="bi bi-qr-code-scan me-2"></i> Scan QR Tiket
            </a>
            <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary d-inline-flex align-items-center fw-medium">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Lihat Laporan
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-0 p-lg-4">
        <h5 class="card-title fw-semibold mb-3 px-3 px-lg-0 pt-3 pt-lg-0">Riwayat Validasi (10 Terakhir)</h5>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-3">Kode Tiket</th>
                        <th class="text-uppercase text-secondary small px-3">Pengunjung</th>
                        <th class="text-uppercase text-secondary small px-3">Waktu Validasi</th>
                    </tr>
                </thead>
                <tbody id="admin-riwayat-tbody" class="border-top-0">
                    @forelse($riwayatValidasi as $riwayat)
                    <tr>
                        <td class="px-3 fw-medium font-monospace">{{ $riwayat->kode_tiket }}</td>
                        <td class="px-3">{{ $riwayat->user->name ?? 'Pengunjung' }}</td>
                        <td class="px-3 text-muted">{{ \Carbon\Carbon::parse($riwayat->updated_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted px-3 py-4">Belum ada tiket yang divalidasi hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var REALTIME_INTERVAL = 1500;
    var url = '{{ route("admin.dashboard") }}';
    function fmtRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID', { maximumFractionDigits: 0 }); }
    function fmtTgl(isoStr) { 
        if(!isoStr) return '-';
        var d = new Date(isoStr);
        return d.toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    }

    function refresh() {
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var el = document.getElementById('admin-hari-ini');
                if (el) el.textContent = data.hariIni;
                el = document.getElementById('admin-bulan-ini');
                if (el) el.textContent = data.bulanIni;
                el = document.getElementById('admin-pendapatan');
                if (el) el.textContent = fmtRp(data.totalPendapatanBulan);

                var tbody = document.getElementById('admin-riwayat-tbody');
                if (tbody && data.riwayatValidasi) {
                    var html = '';
                    if (data.riwayatValidasi.length === 0) {
                        html = '<tr><td colspan="3" class="text-center text-muted px-3 py-4">Belum ada tiket yang divalidasi hari ini.</td></tr>';
                    } else {
                        data.riwayatValidasi.forEach(function(row) {
                            var visitorName = row.user ? row.user.name : 'Pengunjung';
                            html += '<tr>' +
                                '<td class="px-3 fw-medium font-monospace">' + row.kode_tiket + '</td>' +
                                '<td class="px-3">' + visitorName + '</td>' +
                                '<td class="px-3 text-muted">' + fmtTgl(row.updated_at) + '</td>' +
                            '</tr>';
                        });
                    }
                    tbody.innerHTML = html;
                }
            })
            .catch(function() {});
    }
    setInterval(refresh, REALTIME_INTERVAL);
})();
</script>
@endpush
