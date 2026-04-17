@extends('layouts.app')

@section('title', 'Dashboard Pengelola')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Dashboard Pengelola BUMDes</h4>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form id="form-dashboard" method="get" action="{{ route('pengelola.dashboard') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-medium">Periode</label>
                <select name="periode" id="periode" class="form-select">
                    <option value="hari" {{ $periode === 'hari' ? 'selected' : '' }}>Harian</option>
                    <option value="minggu" {{ $periode === 'minggu' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
        </form>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted small fw-medium text-uppercase text-truncate">Total Tiket <span class="label-waktu">{{ $labelWaktu }}</span> (Semua Wisata)</h6>
                <h3 class="display-6 fw-bold mt-1 mb-0 text-primary" id="dashboard-total-tiket">{{ number_format($totalTiketBulan) }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted small fw-medium text-uppercase text-truncate">Total Pendapatan <span class="label-waktu">{{ $labelWaktu }}</span></h6>
                <h3 class="display-6 fw-bold mt-1 mb-0 text-success" id="dashboard-total-pendapatan">Rp {{ number_format($totalPendapatanBulan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Penjualan Tiket per Wisata</h5>
                <div style="height: 300px;">
                    <canvas id="tiketChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Pendapatan per Wisata</h5>
                <div style="height: 300px;">
                    <canvas id="pendapatanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 p-lg-4">
        <h5 class="card-title fw-semibold mb-3 px-3 px-lg-0 pt-3 pt-lg-0">Rekap per Wisata (<span class="label-waktu">{{ $labelWaktu }}</span>)</h5>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-3">Wisata</th>
                        <th class="text-uppercase text-secondary small px-3">Jumlah Tiket</th>
                        <th class="text-uppercase text-secondary small px-3">Pendapatan</th>
                    </tr>
                </thead>
                <tbody id="dashboard-wisata-tbody" class="border-top-0">
                    @foreach($wisata as $w)
                    <tr>
                        <td class="px-3 fw-medium">{{ $w->nama }}</td>
                        <td class="px-3 wisata-tiket">{{ ($w->tiket_online ?? 0) + ($w->tiket_offline ?? 0) }}</td>
                        <td class="px-3 wisata-pendapatan fw-medium text-success">Rp {{ number_format(($w->pendapatan_online ?? 0) + ($w->pendapatan_offline ?? 0), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-3 px-lg-0 pb-3 pb-lg-0">
            <a href="{{ route('pengelola.laporan.index') }}" class="btn btn-primary mt-3 fw-medium">Lihat Laporan Lengkap</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    var rawData = {!! json_encode($chartData) !!};

    var tiketCtx = document.getElementById('tiketChart');
    var pendapatanCtx = document.getElementById('pendapatanChart');

    var tiketChart, pendapatanChart;

    if (tiketCtx) {
        tiketChart = new Chart(tiketCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: rawData.labels,
                datasets: [{
                    label: 'Tiket Terjual',
                    data: rawData.tiket_terjual,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'],
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
    }

    if (pendapatanCtx) {
        pendapatanChart = new Chart(pendapatanCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: rawData.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: rawData.pendapatan,
                    backgroundColor: '#198754',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    var REALTIME_INTERVAL = 1500;
    var base_url = '{{ route("pengelola.dashboard") }}';
    var form = document.getElementById('form-dashboard');
    var periodeInput = document.getElementById('periode');

    function fmtRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID', { maximumFractionDigits: 0 }); }
    
    function refresh(forceUpdate) {
        var url = base_url;
        if (periodeInput) {
            url += '?periode=' + encodeURIComponent(periodeInput.value);
        }

        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var el = document.getElementById('dashboard-total-tiket');
                if (el) el.textContent = Number(data.totalTiketBulan).toLocaleString('id-ID');
                el = document.getElementById('dashboard-total-pendapatan');
                if (el) el.textContent = fmtRp(data.totalPendapatanBulan);

                if (data.labelWaktu) {
                    document.querySelectorAll('.label-waktu').forEach(function(lbl) {
                        lbl.textContent = data.labelWaktu;
                    });
                }
                
                var tbody = document.getElementById('dashboard-wisata-tbody');
                if (tbody && data.wisata && data.wisata.length) {
                    var rows = tbody.querySelectorAll('tr');
                    
                    var newLabels = [];
                    var newTiket = [];
                    var newPendapatan = [];

                    data.wisata.forEach(function(w, i) {
                        newLabels.push(w.nama);
                        newTiket.push(w.tiket_bulan_ini);
                        newPendapatan.push(w.pendapatan_bulan_ini);

                        if (rows[i]) {
                            var tiketCell = rows[i].querySelector('.wisata-tiket');
                            var pendapatanCell = rows[i].querySelector('.wisata-pendapatan');
                            if (tiketCell) tiketCell.textContent = w.tiket_bulan_ini;
                            if (pendapatanCell) pendapatanCell.textContent = fmtRp(w.pendapatan_bulan_ini);
                        }
                    });

                    // Update charts
                    if (tiketChart) {
                        tiketChart.data.labels = newLabels;
                        tiketChart.data.datasets[0].data = newTiket;
                        tiketChart.update();
                    }
                    if (pendapatanChart) {
                        pendapatanChart.data.labels = newLabels;
                        pendapatanChart.data.datasets[0].data = newPendapatan;
                        pendapatanChart.update();
                    }
                }
            })
            .catch(function() {});
    }

    if (periodeInput) periodeInput.addEventListener('change', function() { refresh(true); });

    setInterval(function() { refresh(false); }, REALTIME_INTERVAL);
})();
</script>
@endpush
