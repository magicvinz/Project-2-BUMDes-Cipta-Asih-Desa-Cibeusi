@extends('layouts.app')

@section('title', 'Scan / Validasi Tiket')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Scan QR / Cek Kode Tiket</h4>
<p class="text-muted mb-4">Validasi tiket wisata <strong>{{ auth()->user()->wisata->nama }}</strong>. Gunakan kamera untuk scan QR atau ketik kode manual.</p>

<div class="row row-cols-1 row-cols-lg-2 g-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-medium"><i class="bi bi-camera-video me-2"></i> Scan QR dengan Kamera</span>
                <button type="button" id="btn-ganti-kamera" class="btn btn-light btn-sm d-none" title="Ganti kamera depan/belakang">
                    <i class="bi bi-camera-reels me-1"></i> <span id="label-kamera">Kamera Belakang</span>
                </button>
            </div>
            <div class="card-body">
                <div id="qr-permission-prompt" class="text-center py-5 px-3">
                    <i class="bi bi-camera-video-off display-4 text-muted mb-3 d-block"></i>
                    <p class="mb-2">Situs ini membutuhkan akses kamera untuk memindai QR code tiket.</p>
                    <p class="small text-muted mb-4">Klik tombol di bawah dan pilih "Izinkan" saat browser meminta.</p>
                    <button type="button" id="btn-aktifkan-kamera" class="btn btn-primary fw-medium">
                        <i class="bi bi-camera-fill me-2"></i> Izinkan & Aktifkan Kamera
                    </button>
                </div>
                
                <div id="qr-reader-wrap" class="d-none">
                    <div id="qr-reader" class="w-100 rounded" style="min-height: 280px; overflow: hidden;"></div>
                    <div id="qr-reader-loading" class="text-center py-3 small text-muted d-none">Meminta akses kamera...</div>
                </div>
                
                <div id="qr-reader-results" class="alert alert-success mt-3 py-2 px-3 small d-none mb-0">
                    Kode terdeteksi: <strong id="scanned-code"></strong>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-secondary text-white py-3">
                <span class="fw-medium"><i class="bi bi-keyboard me-2"></i> Input Kode Manual</span>
            </div>
            <div class="card-body">
                <form id="form-cari-tiket" action="{{ route('admin.validasi.cari') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="kode" id="input-kode-tiket" class="form-control text-uppercase px-3 py-2" placeholder="Contoh: SI-ABCD1234" value="{{ old('kode') }}" required autofocus>
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Cari</button>
                    </div>
                </form>
                <div class="alert alert-info py-2 px-3 small border-0 bg-info bg-opacity-10 text-info-emphasis mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i> Ketika QR berhasil di-scan, halaman akan otomatis mengecek tiket.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    const inputKode = document.getElementById('input-kode-tiket');
    const formCari = document.getElementById('form-cari-tiket');
    const resultsDiv = document.getElementById('qr-reader-results');
    const scannedCodeEl = document.getElementById('scanned-code');
    const permissionPrompt = document.getElementById('qr-permission-prompt');
    const qrReaderWrap = document.getElementById('qr-reader-wrap');
    const btnAktifkan = document.getElementById('btn-aktifkan-kamera');
    const btnGantiKamera = document.getElementById('btn-ganti-kamera');
    const labelKamera = document.getElementById('label-kamera');
    const qrReaderLoading = document.getElementById('qr-reader-loading');

    var submitted = false;
    var html5QrCode = new Html5Qrcode('qr-reader');
    var config = { fps: 10, qrbox: { width: 250, height: 250 } };
    var currentFacingMode = 'environment';

    function onScanSuccess(decodedText) {
        if (submitted) return;
        var raw = (decodedText || '').trim();
        var kode = raw.split('\n')[0].trim().toUpperCase();
        if (!kode) return;
        submitted = true;
        inputKode.value = kode;
        if (resultsDiv) {
            resultsDiv.classList.remove('d-none');
            if (scannedCodeEl) scannedCodeEl.textContent = kode;
        }
        formCari.submit();
    }

    function onScanFailure() {}

    function showError(message) {
        if (qrReaderLoading) qrReaderLoading.classList.add('d-none');
        qrReaderWrap.classList.remove('d-none');
        var readerEl = qrReaderWrap.querySelector('#qr-reader');
        readerEl.innerHTML = '<div class="alert alert-warning py-2 mb-0">' + message + ' <button type="button" class="btn btn-sm btn-outline-warning ms-2" id="btn-coba-lagi-kamera">Coba lagi</button></div>';
        permissionPrompt.classList.add('d-none');
        if (btnGantiKamera) btnGantiKamera.classList.add('d-none');
        document.getElementById('btn-coba-lagi-kamera').addEventListener('click', function() {
            readerEl.innerHTML = '';
            qrReaderWrap.classList.add('d-none');
            permissionPrompt.classList.remove('d-none');
            btnAktifkan.disabled = false;
            btnAktifkan.innerHTML = '<i class="bi bi-camera-fill me-2"></i> Izinkan & Aktifkan Kamera';
        });
    }

    function getCameraErrorMessage(err) {
        var name = err && err.name;
        if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
            return 'Izin kamera ditolak. Saat browser meminta izin, pilih "Izinkan" atau buka pengaturan situs untuk mengizinkan kamera.';
        }
        if (name === 'NotFoundError') {
            return 'Tidak ada kamera yang terdeteksi.';
        }
        if (name === 'SecurityError' || name === 'NotSupportedError') {
            return 'Akses kamera memerlukan koneksi aman (HTTPS). Buka situs dengan https:// atau gunakan localhost.';
        }
        return 'Tidak dapat mengakses kamera. Izinkan kamera di pengaturan browser atau gunakan input kode manual.';
    }

    function startCamera(facingMode) {
        currentFacingMode = facingMode;
        permissionPrompt.classList.add('d-none');
        qrReaderWrap.classList.remove('d-none');
        if (qrReaderLoading) qrReaderLoading.classList.remove('d-none');
        var readerEl = qrReaderWrap.querySelector('#qr-reader');
        if (!readerEl.innerHTML.trim()) readerEl.innerHTML = '';
        html5QrCode.start(
            { facingMode: facingMode },
            config,
            onScanSuccess,
            onScanFailure
        ).then(function() {
            if (qrReaderLoading) qrReaderLoading.classList.add('d-none');
            if (btnGantiKamera) btnGantiKamera.classList.remove('d-none');
            if (labelKamera) labelKamera.textContent = currentFacingMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        }).catch(function(err) {
            console.warn('Camera error:', err);
            showError(getCameraErrorMessage(err));
            if (btnGantiKamera) btnGantiKamera.classList.add('d-none');
        });
    }

    function switchCamera() {
        html5QrCode.stop().then(function() {
            var nextMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCamera(nextMode);
            if (labelKamera) labelKamera.textContent = nextMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        }).catch(function() {
            var nextMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCamera(nextMode);
            if (labelKamera) labelKamera.textContent = nextMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        });
    }

    btnAktifkan.addEventListener('click', function() {
        btnAktifkan.disabled = true;
        btnAktifkan.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Meminta izin...';
        startCamera('environment');
    });

    if (btnGantiKamera) {
        btnGantiKamera.addEventListener('click', function() {
            btnGantiKamera.disabled = true;
            switchCamera();
            setTimeout(function() { btnGantiKamera.disabled = false; }, 1500);
        });
    }
})();
</script>
@endpush
