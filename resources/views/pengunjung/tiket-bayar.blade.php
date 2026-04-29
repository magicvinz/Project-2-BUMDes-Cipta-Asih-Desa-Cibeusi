@extends('layouts.app')

@section('title', 'Pembayaran Tiket')

@section('content')
<div class="row justify-content-center" data-aos="zoom-in">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header bg-primary text-white d-flex flex-wrap align-items-center justify-content-between p-3 border-0 gap-3">
                <h5 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card"></i> Pembayaran via Midtrans
                </h5>
                <a href="{{ route('pengunjung.tiket.my') }}" class="btn btn-light btn-sm fw-medium text-primary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Tiket Saya
                </a>
            </div>
            <div class="card-body p-4 p-sm-5">
                <div class="row g-4">
                    <div class="col-md-5">
                        <h6 class="text-muted fw-medium mb-3">Ringkasan Pesanan</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0">Wisata</td>
                                    <td class="text-end fw-medium pe-0">{{ $tiket->wisata->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Kode Tiket</td>
                                    <td class="text-end fw-medium pe-0">{{ $tiket->kode_tiket }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Jumlah</td>
                                    <td class="text-end pe-0">{{ $tiket->jumlah }} tiket</td>
                                </tr>
                                @if($tiket->wisata->hasCamping() && $tiket->camping)
                                <tr>
                                    <td class="text-muted ps-0">Keterangan</td>
                                    <td class="text-end pe-0">{{ $tiket->camping === 'Ya' ? 'Camping' : 'Kunjungan' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted ps-0">Tanggal Berkunjung</td>
                                    <td class="text-end pe-0">{{ $tiket->tanggal_berkunjung->format('d F Y') }}</td>
                                </tr>
                                <tr class="border-top border-light">
                                    <td class="pt-2 fw-medium ps-0">Total Pembayaran</td>
                                    <td class="text-end pt-2 fw-semibold text-primary fs-5 pe-0">Rp {{ number_format($tiket->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="small text-muted mt-3">
                            <i class="bi bi-info-circle me-1"></i>Parkir (dibayar di lokasi): Motor Kunjungan Rp 10.000, Motor Camping Rp 15.000, Mobil Kunjungan Rp 15.000, Mobil Camping Rp 25.000
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="text-muted fw-medium mb-3">Pilih Metode Pembayaran</h6>
                        <p class="small text-muted mb-3">Pilih metode pembayaran di bawah (transfer bank, e-wallet, kartu kredit, dll):</p>
                        <div id="snap-container" class="border rounded-3 p-3 bg-light" style="min-height: 560px;"></div>
                        <p class="small text-muted mt-3">
                            <i class="bi bi-shield-check text-success me-1"></i> Transaksi aman diproses oleh Midtrans.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if(!$snap_token)
        <div class="card shadow-sm border-warning">
            <div class="card-body p-4">
                <h6 class="text-warning fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle"></i> Midtrans tidak tersedia
                </h6>
                <p class="small text-muted mt-2 mb-4">Hubungi pengelola untuk mengaktifkan pembayaran online, atau gunakan opsi di bawah.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('pengunjung.tiket.show', $tiket) }}" class="btn btn-outline-primary fw-medium">Lihat Tiket</a>
                    <a href="{{ route('pengunjung.tiket.my') }}" class="btn btn-outline-secondary fw-medium">Kembali ke Tiket Saya</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@if($snap_token)
@push('styles')
<style>
#snap-container { width: 100%; }
#snap-container iframe { width: 100% !important; min-height: 560px; }
</style>
@endpush

@push('scripts')
@php
    $snapJsUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script type="text/javascript">
(function() {
    var snapToken = '{{ $snap_token }}';
    var successUrl = '{!! route("pengunjung.tiket.show", $tiket) !!}';
    var snapJsUrl = '{{ $snapJsUrl }}';
    var clientKey = "{{ config('services.midtrans.client_key') }}";

    function initSnap() {
        if (typeof window.snap === 'undefined') {
            setTimeout(initSnap, 250);
            return;
        }
        
        var container = document.getElementById('snap-container');
        if (!container) return; // container belum siap

        window.snap.embed(snapToken, {
            embedId: 'snap-container',
            onSuccess: function(result) {
                window.location.href = successUrl + '?payment=success';
            },
            onPending: function(result) {
                window.location.href = successUrl + '?payment=pending';
            },
            onError: function(result) {
                alert('Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
            },
            onClose: function() {}
        });
    }

    // Load secara dinamis agar aman dengan Turbo Drive
    if (typeof window.snap === 'undefined') {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = snapJsUrl;
        script.setAttribute('data-client-key', clientKey);
        script.onload = initSnap;
        document.head.appendChild(script);
    } else {
        // Jika sudah pernah load di sesi turbo sebelumnya
        initSnap();
    }
})();
</script>
@endpush
@endif
