@extends('layouts.app')

@section('title', 'Tiket Saya')

@section('content')
<h4 class="fs-4 fw-semibold mb-4" data-aos="fade-down">Tiket Saya</h4>
@if($tiket->isEmpty())
    <div class="alert alert-info text-center" role="alert">
        Anda belum memiliki tiket. <a href="{{ route('pengunjung.dashboard') }}" class="alert-link">Pesan tiket</a>
    </div>
@else
    <div class="card shadow-sm border-0 mb-4" data-aos="fade-up">
        <div class="card-body p-0">
            <!-- DESKTOP TABLE VIEW -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary small px-4 py-3">Kode</th>
                            <th class="text-uppercase text-secondary small px-4 py-3">Wisata</th>
                            <th class="text-uppercase text-secondary small px-4 py-3 text-center">Jumlah</th>
                            <th class="text-uppercase text-secondary small px-4 py-3">Tanggal Berkunjung</th>
                            <th class="text-uppercase text-secondary small px-4 py-3">Keterangan</th>
                            <th class="text-uppercase text-secondary small px-4 py-3 text-center">Status</th>
                            <th class="text-uppercase text-secondary small px-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="my-tickets-tbody" class="border-top-0">
                        @foreach($tiket as $t)
                        <tr>
                            <td class="px-4 py-3 fw-medium">{{ $t->kode_tiket }}</td>
                            <td class="px-4 py-3">{{ $t->wisata->nama }}</td>
                            <td class="px-4 py-3 text-center">{{ $t->jumlah }}</td>
                            <td class="px-4 py-3">{{ $t->tanggal_berkunjung->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @if($t->wisata->hasCamping() && $t->camping)
                                    {{ $t->camping === 'Ya' ? 'Camping' : 'Kunjungan' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center status-cell-ticket-{{ $t->id }}">
                                @if($t->status === 'pending')
                                    <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                @elseif($t->status === 'paid')
                                    <span class="badge rounded-pill bg-success text-white">Paid</span>
                                @elseif($t->status === 'used')
                                    <span class="badge rounded-pill bg-secondary text-white">Used</span>
                                @elseif($t->status === 'cancelled')
                                    <span class="badge rounded-pill bg-danger text-white">Cancelled</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary text-white">{{ ucfirst($t->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('pengunjung.tiket.show', $t) }}" class="btn btn-sm btn-primary fw-medium">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
            
            <!-- MOBILE CARD VIEW -->
            <div class="d-md-none">
                @foreach($tiket as $t)
                <div class="p-3 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light fw-bold text-primary border border-primary-subtle mb-1">{{ $t->kode_tiket }}</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $t->wisata->nama }}</h6>
                        </div>
                        <div class="status-cell-ticket-{{ $t->id }}">
                            @if($t->status === 'pending')
                                <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                            @elseif($t->status === 'paid')
                                <span class="badge rounded-pill bg-success text-white">Paid</span>
                            @elseif($t->status === 'used')
                                <span class="badge rounded-pill bg-secondary text-white">Used</span>
                            @elseif($t->status === 'cancelled')
                                <span class="badge rounded-pill bg-danger text-white">Cancelled</span>
                            @else
                                <span class="badge rounded-pill bg-secondary text-white">{{ ucfirst($t->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="small text-muted mb-3 d-flex justify-content-between">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ $t->tanggal_berkunjung->format('d/m/Y') }}</span>
                        <span><i class="bi bi-people-fill me-1"></i> {{ $t->jumlah }} tiket
                            @if($t->wisata->hasCamping() && $t->camping)
                                · {{ $t->camping === 'Ya' ? 'Camp' : 'Kunj' }}
                            @endif
                        </span>
                    </div>
                    <a href="{{ route('pengunjung.tiket.show', $t) }}" class="btn btn-sm btn-outline-primary w-100 fw-medium">Lihat Detail</a>
                </div>
                @endforeach
            </div>

        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $tiket->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection

@push('scripts')
@if(!$tiket->isEmpty())
<script>
(function() {
    var REALTIME_INTERVAL = 1500;
    var tbody = document.getElementById('my-tickets-tbody');
    if (!tbody) return;
    var currentPage = parseInt('{{ $tiket->currentPage() }}', 10);
    var url = '{{ route("pengunjung.tiket.my") }}?page=' + currentPage;
    function statusBadge(s) {
        if (s === 'pending') return '<span class="badge rounded-pill bg-warning text-dark">Pending</span>';
        if (s === 'paid') return '<span class="badge rounded-pill bg-success text-white">Paid</span>';
        if (s === 'used') return '<span class="badge rounded-pill bg-secondary text-white">Used</span>';
        if (s === 'cancelled') return '<span class="badge rounded-pill bg-danger text-white">Cancelled</span>';
        return '<span class="badge rounded-pill bg-secondary text-white">' + s + '</span>';
    }
    function refresh() {
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.data || !res.data.length) return;
                res.data.forEach(function(item) {
                    var cells = document.querySelectorAll('.status-cell-ticket-' + item.id);
                    cells.forEach(function(cell) {
                        cell.innerHTML = statusBadge(item.status);
                    });
                });
            })
            .catch(function() {});
    }
    setInterval(refresh, REALTIME_INTERVAL);
})();
</script>
@endif
@endpush
