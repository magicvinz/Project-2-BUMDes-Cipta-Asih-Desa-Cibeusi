@extends('layouts.app')

@section('title', $wisata->nama)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.wisata.index') }}" class="text-decoration-none">Wisata</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $wisata->nama }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-bold text-primary mb-3">{{ $wisata->nama }}</h1>
            <img src="{{ $wisata->gambar_url }}" class="img-fluid rounded shadow-sm mb-4 w-100" alt="{{ $wisata->nama }}" style="max-height: 400px; object-fit: cover;">
            
            <div class="bg-white p-4 rounded shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Tentang Wisata</h5>
                <div class="text-muted lh-lg">
                    {!! nl2br(e($wisata->deskripsi)) !!}
                </div>
            </div>

            {{-- Galeri Foto: Carousel dengan panah & thumbnail --}}
            @if($wisata->galleries && count($wisata->galleries) > 0)
            @php $galleries = $wisata->galleries; @endphp
            <div class="mt-4 mb-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Galeri Foto</h5>

                {{-- Carousel Utama --}}
                <div class="position-relative" style="border-radius: 16px; overflow: hidden;">
                    <div id="galleryMain" data-galleries="{{ json_encode($wisata->galleries) }}" style="width:100%; height: 380px; background:#111; border-radius:16px; overflow:hidden; position:relative;">
                        @foreach($galleries as $i => $gallery)
                        <div class="gallery-slide {{ $i === 0 ? 'active' : '' }}"
                            data-index="{{ $i }}"
                            @if($i === 0)
                            style="display:block; width:100%; height:100%;"
                            @else
                            style="display:none; width:100%; height:100%;"
                            @endif
                            >
                            <img src="{{ Storage::url($gallery['image']) }}"
                                alt="{{ $gallery['caption'] ?? $wisata->nama }}"
                                style="width:100%; height:100%; object-fit:cover; cursor:zoom-in;"
                                onclick="openLightbox('{{ $i }}')">
                            @if($gallery['caption'])
                            <div style="position:absolute; bottom:0; left:0; right:0; background:linear-gradient(transparent,rgba(0,0,0,0.65)); color:#fff; padding:1rem 1.25rem; font-size:0.9rem;">
                                {{ $gallery['caption'] }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Tombol Panah Kiri --}}
                    <button onclick="slideGallery(-1)" class="gallery-arrow-btn" id="arrowLeft"
                        style="position:absolute; top:50%; left:12px; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:rgba(0,0,0,0.5); border:none; color:#fff; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; z-index:5;"
                        onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    {{-- Tombol Panah Kanan --}}
                    <button onclick="slideGallery(1)" class="gallery-arrow-btn" id="arrowRight"
                        style="position:absolute; top:50%; right:12px; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:rgba(0,0,0,0.5); border:none; color:#fff; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; z-index:5;"
                        onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    {{-- Counter --}}
                    <div id="galleryCounter" style="position:absolute; top:12px; right:14px; background:rgba(0,0,0,0.5); color:#fff; padding:3px 10px; border-radius:20px; font-size:0.8rem; z-index:5;">
                        1 / {{ count($galleries) }}
                    </div>
                </div>

                {{-- Thumbnail Strip --}}
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    @foreach($galleries as $i => $gallery)
                    <div onclick="goToSlide('{{ $i }}')" id="thumb-{{ $i }}"
                        class="gallery-thumb {{ $i === 0 ? 'active-thumb' : '' }}"
                        style="width:72px; height:56px; border-radius:10px; overflow:hidden; cursor:pointer; transition:border 0.2s; flex-shrink:0;">
                        <img src="{{ Storage::url($gallery['image']) }}"
                            style="width:100%; height:100%; object-fit:cover;"
                            alt="{{ $gallery['caption'] ?? '' }}">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif


            <!-- Ulasan Pengunjung -->
            <div class="mt-5 mb-4">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="fw-bold mb-0 me-3">Ulasan Pengunjung</h5>
                    @if($wisata->reviews->count() > 0)
                        <div class="badge bg-warning text-dark px-2 py-1 fs-6 rounded">
                            <i class="bi bi-star-fill text-dark me-1"></i> {{ number_format($wisata->average_rating, 1) }}
                        </div>
                        <span class="text-muted ms-2 small">({{ $wisata->reviews->count() }} ulasan)</span>
                    @endif
                </div>

                @forelse($wisata->reviews as $review)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">{{ $review->user->name ?? 'Pengunjung' }}</h6>
                                <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-warning mb-2 small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-2 text-secondary">{{ $review->comment }}</p>
                            @if($review->foto_url)
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <img src="{{ $review->foto_url }}"
                                        alt="Foto ulasan {{ $review->user->name }}"
                                        class="rounded border review-photo"
                                        style="max-height: 120px; max-width: 150px; object-fit: cover; cursor: zoom-in; transition: opacity 0.2s;"
                                        data-foto="{{ $review->foto_url }}"
                                        data-caption="{{ $review->comment }}"
                                        onmouseover="this.style.opacity='0.85'"
                                        onmouseout="this.style.opacity='1'"
                                        onclick="openReviewLightbox(this)">
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light alert-permanent border text-center text-muted">
                        Belum ada ulasan untuk wisata ini.
                    </div>
                @endforelse

                {{-- Form Ulasan: Tampil jika pengunjung punya tiket used yang belum diulas --}}
                @if(isset($tiketBisaUlasan) && $tiketBisaUlasan)
                <div class="card border-0 shadow-sm mt-4" style="border-left: 4px solid #00b4d8 !important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-star-fill text-warning me-2"></i>Tulis Ulasan Anda</h6>
                        <p class="text-muted small mb-3">Anda mengunjungi wisata ini — bagikan pengalaman Anda!</p>
                        <form action="{{ route('pengunjung.review.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id_tiket" value="{{ $tiketBisaUlasan->id }}">
                            <input type="hidden" name="id_wisata" value="{{ $wisata->id_wisata }}">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Rating <span class="text-danger">*</span></label>
                                @include('components.star-rating', ['name' => 'rating', 'value' => old('rating', 5), 'id' => 'rating-wisata-show'])
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Komentar</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Ceritakan pengalaman Anda di sini...">{{ old('comment') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Foto (Opsional)</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-1"></i>Kirim Ulasan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @elseif(auth()->check() && auth()->user()->isPengunjung() && !isset($tiketBisaUlasan))
                    {{-- Sudah punya tiket tapi sudah diulas atau tidak ada tiket used --}}
                @endif
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">Informasi Tiket</h5>
                    <hr>
                    @if($wisata->hasCamping())
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="text-muted">Kunjungan</span>
                            <span class="fw-bold">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                            <span class="text-muted">Camping</span>
                            <span class="fw-bold">Rp {{ number_format($wisata->harga_camping_efektif, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Harga per tiket</span>
                            <span class="fw-bold fs-5">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->isPengunjung())
                        <a href="{{ route('pengunjung.tiket.create', $wisata) }}" class="btn btn-primary w-100 mb-2">Pesan Tiket</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">Masuk untuk Pesan Tiket</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Unified Lightbox Modal (Galeri + Ulasan) --}}
<div id="lightboxModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.93); z-index:9999; align-items:center; justify-content:center; flex-direction:column;">
    <button onclick="closeLightbox()" style="position:absolute; top:16px; right:20px; background:rgba(255,255,255,0.1); border:none; color:#fff; font-size:1.5rem; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">✕</button>
    <button id="lbPrev" onclick="lightboxSlide(-1)" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:1.8rem; width:52px; height:52px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">‹</button>
    <img id="lightboxImg" src="" style="max-width:90vw; max-height:82vh; border-radius:12px; object-fit:contain; transition:opacity 0.2s;">
    <div id="lightboxCaption" style="color:rgba(255,255,255,0.85); margin-top:14px; font-size:0.9rem; text-align:center; max-width:80vw;"></div>
    <div id="lightboxCounter" style="color:rgba(255,255,255,0.5); font-size:0.78rem; margin-top:4px;"></div>
    <button id="lbNext" onclick="lightboxSlide(1)" style="position:absolute; right:16px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:1.8rem; width:52px; height:52px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">›</button>
</div>

<script>
/**
 * Galeri & Lightbox — scope window agar bisa dipanggil dari onclick HTML,
 * namun state (galleries, index) direset setiap turbo:load sehingga tidak bocor
 * saat navigasi antar halaman wisata.
 */

// State global — akan direset setiap turbo:load
window._gallery = {
    items: [],
    currentIndex: 0,
    total: 0,
};
window._lightbox = {
    images: [],
    index: 0,
};

// ── Galeri Carousel ──────────────────────────────────────────────────────────
window.updateGalleryUI = function () {
    var g = window._gallery;
    if (!g.total) return;
    document.querySelectorAll('.gallery-slide').forEach(function (s) { s.style.display = 'none'; });
    var active = document.querySelector('.gallery-slide[data-index="' + g.currentIndex + '"]');
    if (active) active.style.display = 'block';
    var counter = document.getElementById('galleryCounter');
    if (counter) counter.textContent = (g.currentIndex + 1) + ' / ' + g.total;
    document.querySelectorAll('.gallery-thumb').forEach(function (t) { t.style.border = '3px solid transparent'; });
    var thumb = document.getElementById('thumb-' + g.currentIndex);
    if (thumb) {
        thumb.style.border = '3px solid #00b4d8';
        thumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
};

window.slideGallery = function (dir) {
    var g = window._gallery;
    g.currentIndex = (g.currentIndex + dir + g.total) % g.total;
    window.updateGalleryUI();
};

window.goToSlide = function (index) {
    window._gallery.currentIndex = parseInt(index);
    window.updateGalleryUI();
};

// ── Lightbox: Galeri ─────────────────────────────────────────────────────────
window.openLightbox = function (index) {
    var g = window._gallery;
    window._lightbox.index = parseInt(index);
    window._lightbox.images = g.items.map(function (item) {
        return { src: item._url, caption: item.caption || '' };
    });
    renderLightbox();
    showLightbox();
};

// ── Lightbox: Ulasan ─────────────────────────────────────────────────────────
window.openReviewLightbox = function (imgEl) {
    var all = document.querySelectorAll('.review-photo');
    window._lightbox.images = Array.from(all).map(function (el) {
        return { src: el.getAttribute('data-foto'), caption: el.getAttribute('data-caption') || '' };
    });
    window._lightbox.index = Array.from(all).indexOf(imgEl);
    if (window._lightbox.index < 0) window._lightbox.index = 0;
    renderLightbox();
    showLightbox();
};

// ── Lightbox: Render ─────────────────────────────────────────────────────────
function renderLightbox() {
    var lb = window._lightbox;
    var item = lb.images[lb.index];
    var img = document.getElementById('lightboxImg');
    if (!img) return;
    img.style.opacity = '0';
    setTimeout(function () {
        img.src = item.src;
        img.onload = function () { img.style.opacity = '1'; };
    }, 80);
    var captEl = document.getElementById('lightboxCaption');
    var cntEl  = document.getElementById('lightboxCounter');
    if (captEl) captEl.textContent = item.caption;
    if (cntEl)  cntEl.textContent  = lb.images.length > 1 ? (lb.index + 1) + ' / ' + lb.images.length : '';
    var showArrows = lb.images.length > 1;
    var prev = document.getElementById('lbPrev');
    var next = document.getElementById('lbNext');
    if (prev) prev.style.display = showArrows ? 'flex' : 'none';
    if (next) next.style.display = showArrows ? 'flex' : 'none';
}

window.lightboxSlide = function (dir) {
    var lb = window._lightbox;
    lb.index = (lb.index + dir + lb.images.length) % lb.images.length;
    renderLightbox();
};

function showLightbox() {
    var m = document.getElementById('lightboxModal');
    if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}

window.closeLightbox = function () {
    var m = document.getElementById('lightboxModal');
    if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
};

// ── Init per turbo:load ──────────────────────────────────────────────────────
function initGalleryPage() {
    // Reset state galeri dari data-galleries attribute
    var el = document.getElementById('galleryMain');
    if (el) {
        var raw = el.getAttribute('data-galleries');
        var parsed = [];
        try { parsed = JSON.parse(raw) || []; } catch(e) {}

        // Buat _url dari path storage agar bisa digunakan di lightbox
        var storageBase = '{{ rtrim(asset("storage"), "/") }}';
        parsed.forEach(function (item) {
            item._url = storageBase + '/' + item.image;
        });

        window._gallery.items        = parsed;
        window._gallery.total        = parsed.length;
        window._gallery.currentIndex = 0;
        window.updateGalleryUI();
    } else {
        window._gallery = { items: [], currentIndex: 0, total: 0 };
    }

    // Reset lightbox state
    window._lightbox = { images: [], index: 0 };

    // Tutup lightbox jika masih terbuka dari halaman sebelumnya
    window.closeLightbox();

    // Event: keyboard
    // Hapus listener lama, tambah baru (clone trick tidak bisa di document, pakai flag)
    document.removeEventListener('keydown', window._galleryKeyHandler);
    window._galleryKeyHandler = function (e) {
        var m = document.getElementById('lightboxModal');
        if (m && m.style.display === 'flex') {
            if (e.key === 'ArrowRight') window.lightboxSlide(1);
            if (e.key === 'ArrowLeft')  window.lightboxSlide(-1);
            if (e.key === 'Escape')     window.closeLightbox();
        } else if (window._gallery.total > 0) {
            if (e.key === 'ArrowRight') window.slideGallery(1);
            if (e.key === 'ArrowLeft')  window.slideGallery(-1);
        }
    };
    document.addEventListener('keydown', window._galleryKeyHandler);

    // Event: klik luar lightbox
    var modal = document.getElementById('lightboxModal');
    if (modal) {
        modal.onclick = function (e) { if (e.target === modal) window.closeLightbox(); };
    }
}

// Jalankan saat Turbo load (navigasi antar halaman) maupun load awal
document.addEventListener('turbo:load', initGalleryPage);

// Fallback jika Turbo tidak aktif
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGalleryPage);
} else {
    initGalleryPage();
}

// Bersihkan saat halaman akan di-cache Turbo
document.addEventListener('turbo:before-cache', function () {
    window.closeLightbox();
    document.removeEventListener('keydown', window._galleryKeyHandler);
});
</script>
@endpush
