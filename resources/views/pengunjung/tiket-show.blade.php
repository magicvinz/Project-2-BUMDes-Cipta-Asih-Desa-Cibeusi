@extends('layouts.app')

@section('title', 'Tiket ' . $tiket->kode_tiket)

@section('content')
@if(request('payment') === 'success')
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span><i class="bi bi-check-circle me-2"></i>Pembayaran berhasil! Tiket Anda sudah aktif.</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(request('payment') === 'pending')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <span><i class="bi bi-hourglass-split me-2"></i>Menunggu pembayaran. Silakan selesaikan pembayaran Anda.</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row justify-content-center" data-aos="zoom-in">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-sm-5 text-center">
                <h5 class="text-primary fw-semibold mb-2">Tiket Wisata</h5>
                <p class="mb-1 fw-bold fs-5">{{ $tiket->wisata->nama }}</p>
                <p class="mb-2 text-muted small">Kode: <strong>{{ $tiket->kode_tiket }}</strong></p>
                <p class="mb-2">Jumlah: {{ $tiket->jumlah }} pengunjung</p>
                <p class="mb-2">Tanggal berkunjung: {{ $tiket->tanggal_berkunjung->format('d F Y') }}</p>
                @if($tiket->wisata->isCurugCibarebeuy() && $tiket->camping)
                <p class="mb-2">Keterangan: <strong>{{ $tiket->camping === 'Ya' ? 'Camping' : 'Kunjungan' }}</strong></p>
                @endif
                
                <div class="mb-4 text-muted small text-start bg-light p-3 rounded-3 mt-3">
                    <strong class="text-dark d-block mb-1">Keterangan parkir (dibayar di lokasi):</strong>
                    <ul class="mb-0 ps-3">
                        <li>Motor Kunjungan Rp 10.000 (include pentitipan helm & barang)</li>
                        <li>Motor Camping Rp 15.000 (include pentitipan helm & barang)</li>
                        <li>Mobil Kunjungan Rp 15.000</li>
                        <li>Mobil Camping Rp 25.000</li>
                    </ul>
                </div>

                <div class="mb-4" id="tiket-status-wrap">
                    @if($tiket->status === 'pending')
                        <div class="mb-4">
                            <span class="badge rounded-pill bg-warning text-dark fs-6 px-3 py-2" id="tiket-status-badge">Menunggu Pembayaran</span>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                            <a href="{{ route('pengunjung.tiket.bayar', $tiket) }}" class="btn btn-primary px-4 fw-medium">Bayar Sekarang</a>
                        </div>
                    @elseif($tiket->status === 'paid')
                        <span class="badge rounded-pill bg-success fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Dibayar</span>
                    @elseif($tiket->status === 'used')
                        <span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Terpakai</span>
                    @else
                        <span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">{{ ucfirst($tiket->status) }}</span>
                    @endif
                </div>

                @if($tiket->status === 'paid')
                <div class="border rounded-3 p-4 bg-light d-inline-block mb-4">
                    <p class="small fw-medium text-dark mb-3">Tunjukkan QR ini di lokasi wisata</p>
                    @php
                        $tiket->load('wisata');
                        $qrContent = $tiket->qr_content;
                        $qrUrl = route('pengunjung.tiket.qrcode', $tiket);
                        $fallbackUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrContent);
                        $downloadUrl = $qrUrl . '?download=1';
                    @endphp
                    <div id="qr-container">
                        <img id="qr-image"
                             src="{{ $qrUrl }}"
                             alt="QR Tiket {{ $tiket->kode_tiket }}"
                             class="img-fluid mx-auto d-block"
                             style="max-width: 200px"
                             width="200"
                             height="200"
                             onerror="handleQRError(this, '{{ $fallbackUrl }}');">
                        <div id="qr-loading" class="text-muted small mt-2 d-none">Memuat QR code...</div>
                        <div id="qr-error" class="text-danger small mt-2 d-none">Gagal memuat QR. <a href="{{ $fallbackUrl }}" target="_blank" class="text-danger text-decoration-underline">Buka QR di tab baru</a></div>
                        <div class="mt-4">
                            <a href="{{ $downloadUrl }}" class="btn btn-primary fw-medium">
                                <i class="bi bi-download me-2"></i> Download QR
                            </a>
                        </div>
                    </div>
                </div>
                @elseif($tiket->status === 'used')
                <div class="border rounded-3 p-4 bg-light text-start mb-4">
                    <h6 class="fw-semibold text-dark"><i class="bi bi-star-fill text-warning me-2"></i>Ulasan Kehadiran Anda</h6>
                    @if($tiket->review)
                        <div class="mt-3">
                            <div class="d-flex align-items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $tiket->review->rating ? 'text-warning' : 'text-secondary opacity-25' }} me-1"></i>
                                @endfor
                            </div>
                            <p class="mb-0 text-muted fst-italic">"{{ $tiket->review->comment ?? 'Tidak ada komentar.' }}"</p>
                        </div>
                    @else
                        <p class="small text-muted mb-3">Bagaimana pengalaman wisata Anda? Berikan penilaian agar kami dapat memberikan pelayanan yang lebih baik.</p>
                        <form action="{{ route('pengunjung.review.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id_tiket" value="{{ $tiket->id }}">
                            <input type="hidden" name="id_wisata" value="{{ $tiket->id_wisata }}">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Penilaian Rating (1-5)</label>
                                <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 - Sangat Puas</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Puas</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Cukup</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Kurang</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Kecewa</option>
                                </select>
                                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Komentar Singkat</label>
                                <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="3" placeholder="Tulis pengalaman Anda di sini...">{{ old('comment') }}</textarea>
                                @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Foto (Opsional)</label>
                                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                                @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">Kirim Ulasan</button>
                            </div>
                        </form>
                    @endif
                </div>
                @endif
                
                <div>
                    <a href="{{ route('pengunjung.tiket.my') }}" class="btn btn-outline-primary px-4 fw-medium">{{ request('payment') === 'success' ? 'Kembali ke Tiket Saya' : 'Ke Daftar Tiket Saya' }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var REALTIME_INTERVAL = 1500;
    var statusWrap = document.getElementById('tiket-status-wrap');
    var showUrl = '{{ route("pengunjung.tiket.show", $tiket) }}';
    if (statusWrap && showUrl) {
        var currentStatus = '{{ $tiket->status }}';
        setInterval(function() {
            fetch(showUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status && data.status !== currentStatus) {
                        currentStatus = data.status;
                        var badge = document.getElementById('tiket-status-badge');
                        if (data.status === 'paid') {
                            if (badge) badge.outerHTML = '<span class="badge rounded-pill bg-success fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Dibayar</span>';
                            location.reload();
                        } else if (data.status === 'used') {
                            if (badge) badge.outerHTML = '<span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Terpakai</span>';
                            location.reload();
                        } else if (badge) {
                            badge.className = 'badge rounded-pill bg-warning text-dark fs-6 px-3 py-2';
                            badge.textContent = data.status === 'pending' ? 'Menunggu Pembayaran' : data.status;
                        }
                    }
                })
                .catch(function() {});
        }, REALTIME_INTERVAL);
    }
})();
function handleQRError(img, fallbackUrl) {
    var qrError = document.getElementById('qr-error');
    var qrLoading = document.getElementById('qr-loading');

    if (qrLoading) qrLoading.classList.add('d-none');

    if (img.src !== fallbackUrl) {
        img.src = fallbackUrl;
        if (qrLoading) qrLoading.classList.remove('d-none');
        img.onerror = function() {
            if (qrLoading) qrLoading.classList.add('d-none');
            if (qrError) { qrError.classList.remove('d-none'); }
            img.style.display = 'none';
        };
        img.onload = function() {
            if (qrLoading) qrLoading.classList.add('d-none');
            if (qrError) { qrError.classList.add('d-none'); }
        };
    } else {
        if (qrLoading) qrLoading.classList.add('d-none');
        if (qrError) { qrError.classList.remove('d-none'); }
        img.style.display = 'none';
    }
}

document.addEventListener("turbo:load", function() {
    var qrImg = document.getElementById('qr-image');
    var qrLoading = document.getElementById('qr-loading');
    if (qrImg && qrLoading) {
        qrImg.onload = function() {
            qrLoading.classList.add('d-none');
        };
        qrImg.onerror = function() {
            qrLoading.classList.add('d-none');
        };
    }
});
</script>
@endpush
