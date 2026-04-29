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

<div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Penjualan Tiket (Bulan Ini)</h5>
                <div style="height: 300px;">
                    <canvas id="tiketChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Pendapatan (Bulan Ini)</h5>
                <div style="height: 300px;">
                    <canvas id="pendapatanChart"></canvas>
                </div>
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
                        <td class="px-3 fw-medium font-monospace" data-label="Kode Tiket">{{ $riwayat->kode_tiket }}</td>
                        <td class="px-3" data-label="Pengunjung">{{ $riwayat->user->name ?? 'Pengunjung' }}</td>
                        <td class="px-3 text-muted" data-label="Waktu Validasi">{{ \Carbon\Carbon::parse($riwayat->updated_at)->format('d/m/Y H:i') }}</td>
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
<div id="chart-data-container" data-chart='@json($chartData)' style="display:none;"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    let adminDashboardInterval;

    function initAdminDashboard() {
        // Fix for "Chart is not defined" race condition
        if (typeof Chart === 'undefined') {
            setTimeout(initAdminDashboard, 100);
            return;
        }

        const dataContainer = document.getElementById('chart-data-container');
        if (!dataContainer) return;
        
        var rawData;
        try {
            rawData = JSON.parse(dataContainer.getAttribute('data-chart'));
        } catch (e) {
            console.error('Failed to parse chart data', e);
            return;
        }

        var tiketCtx = document.getElementById('tiketChart');
        var pendapatanCtx = document.getElementById('pendapatanChart');

        if (!tiketCtx || !pendapatanCtx) return;

        var tiketChart, pendapatanChart;

        tiketChart = new Chart(tiketCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: rawData.labels,
                datasets: [{
                    label: 'Tiket Terjual',
                    data: rawData.tiket,
                    backgroundColor: ['#0d6efd', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        pendapatanChart = new Chart(pendapatanCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: rawData.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: rawData.pendapatan,
                    backgroundColor: ['#198754', '#fd7e14'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var value = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

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
                .then(function(r) { 
                    if (r.status === 401) {
                        if (adminDashboardInterval) clearInterval(adminDashboardInterval);
                        return;
                    }
                    return r.json(); 
                })
                .then(function(data) {
                    if (!data) return;
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
                                    '<td class="px-3 fw-medium font-monospace" data-label="Kode Tiket">' + row.kode_tiket + '</td>' +
                                    '<td class="px-3" data-label="Pengunjung">' + visitorName + '</td>' +
                                    '<td class="px-3 text-muted" data-label="Waktu Validasi">' + fmtTgl(row.updated_at) + '</td>' +
                                '</tr>';
                            });
                        }
                        tbody.innerHTML = html;
                    }
                    
                    if (data.chartData) {
                        if (tiketChart) {
                            tiketChart.data.labels = data.chartData.labels;
                            tiketChart.data.datasets[0].data = data.chartData.tiket;
                            tiketChart.update('none');
                        }
                        if (pendapatanChart) {
                            pendapatanChart.data.labels = data.chartData.labels;
                            pendapatanChart.data.datasets[0].data = data.chartData.pendapatan;
                            pendapatanChart.update('none');
                        }
                    }
                })
                .catch(function() {});
        }

        if (adminDashboardInterval) clearInterval(adminDashboardInterval);
        adminDashboardInterval = setInterval(refresh, REALTIME_INTERVAL);
    }

    document.addEventListener("turbo:load", initAdminDashboard);

    document.addEventListener("turbo:before-cache", function() {
        if (adminDashboardInterval) clearInterval(adminDashboardInterval);
    }, { once: true });
})();
</script>
@endpush
