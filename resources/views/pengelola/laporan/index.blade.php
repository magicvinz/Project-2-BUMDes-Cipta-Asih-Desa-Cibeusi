@extends('layouts.app')

@section('title', 'Laporan Penjualan Tiket')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Laporan Penjualan Tiket</h1>
            <p class="text-muted mb-0">Rekapitulasi penjualan tiket online dan offline seluruh wisata.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('pengelola.laporan.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary px-4 shadow-sm">
                <i class="bi bi-printer me-2"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('pengelola.laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="jenis" class="form-label fw-semibold text-secondary small">Jenis Penjualan</label>
                    <select class="form-select border-primary bg-light" id="jenis" name="jenis" onchange="this.form.submit()">
                        <option value="semua" {{ $jenis == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="online" {{ $jenis == 'online' ? 'selected' : '' }}>Penjualan Tiket Online</option>
                        <option value="offline" {{ $jenis == 'offline' ? 'selected' : '' }}>Penjualan Tiket Offline</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="periode" class="form-label fw-semibold text-secondary small">Pilih Periode</label>
                    <select class="form-select border-primary bg-light" id="periode" name="periode" onchange="this.form.submit()">
                        <option value="hari" {{ $periode == 'hari' ? 'selected' : '' }}>Harian</option>
                        <option value="minggu" {{ $periode == 'minggu' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulan" {{ $periode == 'bulan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tanggal" class="form-label fw-semibold text-secondary small">
                        Pilih {{ $periode == 'bulan' ? 'Bulan & Tahun' : 'Tanggal' }}
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-primary">
                            <i class="bi bi-calendar-event"></i>
                        </span>
                        @if($periode == 'bulan')
                            <input type="month" class="form-control border-start-0 ps-0" id="tanggal" name="tanggal" 
                                   value="{{ \Carbon\Carbon::parse($tanggal)->format('Y-m') }}" onchange="this.form.submit()">
                        @else
                            <input type="date" class="form-control border-start-0 ps-0" id="tanggal" name="tanggal" 
                                   value="{{ $tanggal }}" onchange="this.form.submit()">
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Badges -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-dark border-start border-4 border-primary ps-2 mb-3">Ringkasan: {{ $label }}</h5>
        </div>
        <!-- Online Stats -->
        @if($jenis == 'semua' || $jenis == 'online')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-gradient text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0 fw-semibold text-white-50">Total Tiket Online</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="bi bi-globe fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($totalTiketOnlineAll, 0, ',', '.') }} <span class="fs-6 fw-normal">Terjual</span></h3>
                    <p class="mb-0 text-white-50 small">Pendapatan: Rp {{ number_format($totalPendapatanOnlineAll, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif
        <!-- Offline Stats -->
        @if($jenis == 'semua' || $jenis == 'offline')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success bg-gradient text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0 fw-semibold text-white-50">Total Tiket Offline</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="bi bi-ticket-perforated fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($totalTiketOfflineAll, 0, ',', '.') }} <span class="fs-6 fw-normal">Terjual</span></h3>
                    <p class="mb-0 text-white-50 small">Pendapatan: Rp {{ number_format($totalPendapatanOfflineAll, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif
        <!-- Grand Total -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-dark bg-gradient text-white position-relative overflow-hidden">
                <!-- Decorative element -->
                <div class="position-absolute opacity-10" style="right: -20px; bottom: -20px; transform: rotate(-15deg);">
                    <i class="bi bi-cash-stack" style="font-size: 8rem;"></i>
                </div>
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0 fw-semibold text-white-50 text-uppercase tracking-wider" style="letter-spacing: 1px; font-size: 0.8rem;">Grand Total Pendapatan</h6>
                    </div>
                    <h2 class="fw-bolder mb-0 text-warning">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</h2>
                    <p class="mb-0 text-white-50 small mt-2"><i class="bi bi-info-circle me-1"></i>Gabungan Online + Offline</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-table text-primary me-2"></i> Rincian Per Wisata</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4 text-secondary fw-semibold py-3 border-bottom-0">Nama Wisata</th>
                            @if($jenis == 'semua' || $jenis == 'online')
                            <th scope="col" class="text-center text-secondary fw-semibold py-3 border-bottom-0">Tiket Online</th>
                            <th scope="col" class="text-end text-secondary fw-semibold pe-4 py-3 border-bottom-0">Pendapatan Online</th>
                            @endif
                            @if($jenis == 'semua' || $jenis == 'offline')
                            <th scope="col" class="text-center text-secondary fw-semibold py-3 border-bottom-0">Tiket Offline</th>
                            <th scope="col" class="text-end text-secondary fw-semibold pe-4 py-3 border-bottom-0">Pendapatan Offline</th>
                            @endif
                            <th scope="col" class="text-end pe-4 fw-bold text-dark py-3 border-bottom-0 bg-light" style="--bs-bg-opacity: .5;">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($laporan as $item)
                        <tr>
                            <td class="ps-4 fw-medium py-3 text-dark">{{ $item->wisata }}</td>
                            
                            <!-- Online -->
                            @if($jenis == 'semua' || $jenis == 'online')
                            <td class="text-center py-3">
                                @if($item->tiket_online > 0)
                                    <span class="fw-semibold text-primary">{{ number_format($item->tiket_online, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted opacity-50">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3 text-secondary">
                                {{ $item->pendapatan_online > 0 ? 'Rp ' . number_format($item->pendapatan_online, 0, ',', '.') : '-' }}
                            </td>
                            @endif
                            
                            <!-- Offline -->
                            @if($jenis == 'semua' || $jenis == 'offline')
                            <td class="text-center py-3">
                                @if($item->tiket_offline > 0)
                                    <span class="fw-semibold text-success">{{ number_format($item->tiket_offline, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted opacity-50">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3 text-secondary">
                                {{ $item->pendapatan_offline > 0 ? 'Rp ' . number_format($item->pendapatan_offline, 0, ',', '.') : '-' }}
                            </td>
                            @endif
                            
                            <!-- Total -->
                            <td class="text-end pe-4 py-3 fw-bold text-dark bg-light" style="--bs-bg-opacity: .3;">
                                Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $jenis == 'semua' ? '6' : '4' }}" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-3 text-secondary opacity-50"></i>
                                Belum ada data penjualan wisata pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-top-0">
                        <tr class="bg-primary text-white" style="--bs-bg-opacity: .05;">
                            <td class="ps-4 py-3 fw-bold text-dark text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Total Keseluruhan</td>
                            @if($jenis == 'semua' || $jenis == 'online')
                            <td class="text-center py-3 fw-bold text-primary fs-5">{{ number_format($totalTiketOnlineAll, 0, ',', '.') }}</td>
                            <td class="text-end pe-4 py-3 fw-bold text-primary">Rp {{ number_format($totalPendapatanOnlineAll, 0, ',', '.') }}</td>
                            @endif
                            @if($jenis == 'semua' || $jenis == 'offline')
                            <td class="text-center py-3 fw-bold text-success fs-5">{{ number_format($totalTiketOfflineAll, 0, ',', '.') }}</td>
                            <td class="text-end pe-4 py-3 fw-bold text-success">Rp {{ number_format($totalPendapatanOfflineAll, 0, ',', '.') }}</td>
                            @endif
                            <td class="text-end pe-4 py-3 fw-bolder text-dark fs-5 bg-primary bg-opacity-10">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
