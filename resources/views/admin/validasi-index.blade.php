@extends('layouts.app')

@section('title', 'Scan / Validasi Tiket')

@section('content')
<h4 class="fs-4 fw-semibold mb-1">Scan QR / Cek Kode Tiket</h4>
<p class="text-muted mb-4">Validasi tiket wisata <strong>{{ auth()->user()->wisata->nama }}</strong>. Kamera akan aktif otomatis, atau ketik kode manual.</p>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row row-cols-1 row-cols-lg-2 g-4">

    {{-- KOLOM KIRI: Kamera QR --}}
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-medium"><i class="bi bi-camera-video me-2"></i> Scan QR dengan Kamera</span>
                <button type="button" id="btn-ganti-kamera" class="btn btn-light btn-sm d-none" title="Ganti kamera">
                    <i class="bi bi-camera-reels me-1"></i> <span id="label-kamera">Kamera Belakang</span>
                </button>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">

                {{-- Status kamera --}}
                <div id="qr-status-msg" class="text-center py-3 text-muted small">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Mengaktifkan kamera...
                </div>

                {{-- Area reader --}}
                <div id="qr-reader" class="w-100 rounded" style="min-height: 260px; overflow: hidden;"></div>

                {{-- Hasil scan --}}
                <div id="qr-reader-results" class="alert alert-success mt-3 py-2 px-3 small d-none w-100 mb-0">
                    Kode terdeteksi: <strong id="scanned-code"></strong>
                </div>

                {{-- Tombol coba lagi (muncul jika error) --}}
                <button type="button" id="btn-retry-kamera" class="btn btn-outline-primary mt-3 d-none">
                    <i class="bi bi-arrow-clockwise me-1"></i> Coba Aktifkan Kamera Lagi
                </button>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Input Manual --}}
    <div class="col">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-secondary text-white py-3">
                <span class="fw-medium"><i class="bi bi-keyboard me-2"></i> Input Kode Manual</span>
            </div>
            <div class="card-body">
                {{-- 
                    PENTING: data-turbo="false" agar Turbo/Hotwire tidak intercept submit form ini.
                    Tanpa ini, form POST dikirim via Turbo dan redirect response-nya bisa gagal.
                --}}
                <form id="form-cari-tiket"
                      action="{{ route('admin.validasi.cari') }}"
                      method="POST"
                      data-turbo="false">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text"
                               name="kode"
                               id="input-kode-tiket"
                               class="form-control text-uppercase px-3 py-2"
                               placeholder="Contoh: SI-ABCD1234"
                               value="{{ old('kode') }}"
                               autocomplete="off"
                               required
                               autofocus>
                        <button type="submit" class="btn btn-primary px-4 fw-medium">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                    <div class="alert alert-info py-2 px-3 small border-0 bg-info bg-opacity-10 text-info-emphasis mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Ketik kode tiket lalu klik <strong>Cari</strong> atau tekan <kbd>Enter</kbd>.
                        Ketika QR berhasil di-scan, kode otomatis terisi.
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Library html5-qrcode dari CDN, dimuat sebelum script kita --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
/**
 * Validasi QR Scanner — kompatibel dengan Hotwire Turbo.
 * - Kamera aktif otomatis saat halaman dibuka.
 * - Input manual menggunakan data-turbo="false" (native form submit).
 * - Gunakan turbo:load bukan DOMContentLoaded agar bekerja saat navigasi Turbo.
 */
(function () {
    var html5QrCode = null;
    var currentFacingMode = 'environment';
    var scanSubmitted = false;

    // ── Elemen DOM ────────────────────────────────────────────────────────────
    function getEl(id) { return document.getElementById(id); }

    function setStatusMsg(html, hide) {
        var el = getEl('qr-status-msg');
        if (!el) return;
        el.innerHTML = html;
        el.classList.toggle('d-none', !!hide);
    }

    // ── Callback scan sukses ──────────────────────────────────────────────────
    function onScanSuccess(decodedText) {
        if (scanSubmitted) return;
        var kode = (decodedText || '').trim().split('\n')[0].trim().toUpperCase();
        if (!kode) return;
        scanSubmitted = true;

        // Tampilkan kode terdeteksi
        var resDiv = getEl('qr-reader-results');
        var codeEl = getEl('scanned-code');
        if (resDiv) resDiv.classList.remove('d-none');
        if (codeEl) codeEl.textContent = kode;

        // Isi input dan submit form
        var inputKode = getEl('input-kode-tiket');
        var form = getEl('form-cari-tiket');
        if (inputKode) inputKode.value = kode;
        if (form) {
            // Stop scanner sebelum submit agar kamera bebas
            stopScanner(function () { form.submit(); });
        }
    }

    function onScanFailure() { /* diabaikan */ }

    // ── Stop scanner ──────────────────────────────────────────────────────────
    function stopScanner(callback) {
        if (html5QrCode) {
            html5QrCode.stop().then(function () {
                html5QrCode.clear();
                if (callback) callback();
            }).catch(function () {
                if (callback) callback();
            });
        } else {
            if (callback) callback();
        }
    }

    // ── Pesan error kamera ────────────────────────────────────────────────────
    function getCameraErrorMsg(err) {
        var name = (err && err.name) || '';
        if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
            return '<i class="bi bi-shield-lock me-2 text-warning"></i>Izin kamera ditolak. Klik ikon kunci/kamera di address bar browser dan pilih <strong>Izinkan</strong>, lalu muat ulang halaman.';
        }
        if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
            return '<i class="bi bi-camera-video-off me-2 text-danger"></i>Tidak ada kamera yang terdeteksi di perangkat ini.';
        }
        if (name === 'SecurityError' || name === 'NotSupportedError') {
            return '<i class="bi bi-lock me-2 text-warning"></i>Kamera memerlukan koneksi HTTPS. Gunakan <code>localhost</code> atau buka via <code>https://</code>.';
        }
        return '<i class="bi bi-exclamation-triangle me-2 text-warning"></i>Gagal mengakses kamera. Gunakan input kode manual di sebelah kanan.';
    }

    // ── Tampilkan error kamera ────────────────────────────────────────────────
    function showCameraError(err) {
        setStatusMsg('<div class="alert alert-warning py-2 px-3 small mb-0">' + getCameraErrorMsg(err) + '</div>');
        var btnRetry = getEl('btn-retry-kamera');
        if (btnRetry) btnRetry.classList.remove('d-none');
        var btnGanti = getEl('btn-ganti-kamera');
        if (btnGanti) btnGanti.classList.add('d-none');
    }

    // ── Start kamera ──────────────────────────────────────────────────────────
    function startCamera(facingMode) {
        currentFacingMode = facingMode || 'environment';
        scanSubmitted = false;

        var readerEl = getEl('qr-reader');
        if (!readerEl) return;

        // Pastikan instance baru jika perlu
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader');
        }

        setStatusMsg('<div class="spinner-border spinner-border-sm me-2" role="status"></div>Mengaktifkan kamera...');

        var config = { fps: 10, qrbox: { width: 240, height: 240 } };

        html5QrCode.start(
            { facingMode: currentFacingMode },
            config,
            onScanSuccess,
            onScanFailure
        ).then(function () {
            // Kamera aktif
            setStatusMsg('', true); // sembunyikan pesan status
            var btnGanti = getEl('btn-ganti-kamera');
            var labelKamera = getEl('label-kamera');
            if (btnGanti) btnGanti.classList.remove('d-none');
            if (labelKamera) labelKamera.textContent = currentFacingMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        }).catch(function (err) {
            console.warn('[QR Scanner] Camera error:', err);
            showCameraError(err);
        });
    }

    // ── Ganti kamera (depan ↔ belakang) ──────────────────────────────────────
    function switchCamera() {
        var next = currentFacingMode === 'environment' ? 'user' : 'environment';
        stopScanner(function () {
            html5QrCode = new Html5Qrcode('qr-reader');
            startCamera(next);
        });
    }

    // ── Inisialisasi halaman ──────────────────────────────────────────────────
    function init() {
        // Pastikan kita di halaman validasi
        if (!getEl('qr-reader')) return;

        // Reset state
        html5QrCode = null;
        scanSubmitted = false;

        // Auto-start kamera
        startCamera('environment');

        // Event: tombol ganti kamera
        var btnGanti = getEl('btn-ganti-kamera');
        if (btnGanti) {
            // Hapus listener lama agar tidak dobel saat navigasi Turbo
            var newBtn = btnGanti.cloneNode(true);
            btnGanti.parentNode.replaceChild(newBtn, btnGanti);
            newBtn.addEventListener('click', function () {
                newBtn.disabled = true;
                switchCamera();
                setTimeout(function () { newBtn.disabled = false; }, 2000);
            });
        }

        // Event: tombol coba lagi kamera
        var btnRetry = getEl('btn-retry-kamera');
        if (btnRetry) {
            var newRetry = btnRetry.cloneNode(true);
            btnRetry.parentNode.replaceChild(newRetry, btnRetry);
            newRetry.addEventListener('click', function () {
                newRetry.classList.add('d-none');
                html5QrCode = new Html5Qrcode('qr-reader');
                startCamera(currentFacingMode);
            });
        }
    }

    // ── Lifecycle events ──────────────────────────────────────────────────────
    // turbo:load: dipanggil saat navigasi Turbo maupun load awal
    document.addEventListener('turbo:load', function () {
        // Stop scanner lama jika ada (dari navigasi sebelumnya)
        if (html5QrCode) {
            html5QrCode.stop().catch(function () {}).finally(function () {
                html5QrCode = null;
                init();
            });
        } else {
            init();
        }
    });

    // turbo:before-cache: hentikan kamera sebelum halaman di-cache Turbo
    document.addEventListener('turbo:before-cache', function () {
        if (html5QrCode) {
            html5QrCode.stop().catch(function () {});
            html5QrCode = null;
        }
        // Sembunyikan reader agar cache tidak menyimpan video frame
        var readerEl = getEl('qr-reader');
        if (readerEl) readerEl.innerHTML = '';
    });

    // Fallback: jika Turbo tidak aktif (DOMContentLoaded biasa)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // Jika sudah ready dan turbo:load tidak terpicu
        if (!window._qrInitDone) {
            window._qrInitDone = true;
            // Delay sedikit agar library html5-qrcode selesai parse
            setTimeout(init, 100);
        }
    }
})();
</script>
@endpush
