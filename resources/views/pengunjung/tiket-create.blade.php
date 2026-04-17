@extends('layouts.app')

@section('title', 'Pesan Tiket')

@section('content')
@php
    $isCurug = $wisata->isCurugCibarebeuy();
@endphp
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('pengunjung.dashboard') }}" class="text-primary text-decoration-none">Wisata</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $wisata->nama }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h4 class="card-title fs-4 fw-semibold">Pesan Tiket {{ $wisata->nama }}</h4>
                <p class="text-muted mt-1 mb-4">Harga: @if($isCurug) <strong>Kunjungan</strong> Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} · <strong>Camping</strong> Rp {{ number_format(\App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG, 0, ',', '.') }} <span class="text-muted">per tiket</span> @else Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} per tiket @endif</p>
                
                <form action="{{ route('pengunjung.tiket.store') }}" method="post" id="form-tiket">
                    @csrf
                    <input type="hidden" name="id_wisata" value="{{ $wisata->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Jumlah Tiket</label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" min="1" max="20">
                        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tanggal Berkunjung</label>
                        <input type="date" name="tanggal_berkunjung" class="form-control @error('tanggal_berkunjung') is-invalid @enderror" value="{{ old('tanggal_berkunjung') }}" min="{{ date('Y-m-d') }}">
                        @error('tanggal_berkunjung')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    @if($isCurug)
                    <div class="mb-3">
                        <label class="form-label fw-medium">Keterangan</label>
                        <select name="camping" id="camping" class="form-select @error('camping') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="Ya" {{ old('camping') === 'Ya' ? 'selected' : '' }}>Camping</option>
                            <option value="Tidak" {{ old('camping') === 'Tidak' ? 'selected' : '' }}>Kunjungan</option>
                        </select>
                        @error('camping')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    
                    <div class="mt-4 p-4 bg-light rounded-3 border">
                        <h6 class="fw-semibold text-dark mb-2">Keterangan Parkir</h6>
                        <p class="small text-muted mb-2">Biaya parkir dibayar manual saat sampai di lokasi wisata.</p>
                        <ul class="small text-secondary mb-0">
                            <li>Motor Kunjungan: Rp 10.000 (include penitipan helm & barang)</li>
                            <li>Motor Camping: Rp 15.000 (include penitipan helm & barang)</li>
                            <li>Mobil Kunjungan: Rp 15.000</li>
                            <li>Mobil Camping: Rp 25.000</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4" id="price-summary-container">
                        <h6 class="fw-semibold text-dark mb-3">Rincian Harga</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0">Harga Tiket (<span id="summary-qty">1</span>x)</td>
                                    <td class="text-end fw-medium pe-0" id="summary-price">Rp 0</td>
                                </tr>
                                <tr class="border-top border-light">
                                    <td class="pt-2 fw-medium ps-0">Total Pembayaran</td>
                                    <td class="text-end pt-2 fw-bold text-primary fs-5 pe-0" id="summary-total">Rp 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4 pt-2">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Lanjut ke Pembayaran</button>
                        <a href="{{ route('pengunjung.dashboard') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $hargaCampingVal  = \App\Models\Wisata::HARGA_CAMPING_TIKET_CURUG;
    $isCurugJs        = $isCurug ? 'true' : 'false';
@endphp
<script>
    function hitungHargaTiket() {
        const defaultPrice  = parseInt("{{ (int) $wisata->harga_tiket }}", 10);
        const isCurug       = ("{{ $isCurugJs }}" === "true");
        const campingPrice  = parseInt("{{ $hargaCampingVal }}", 10) || 0;
        
        const inputJumlah = document.querySelector('input[name="jumlah"]');
        const selectCamping = document.querySelector('select[name="camping"]');
        
        const summaryQty = document.getElementById('summary-qty');
        const summaryPrice = document.getElementById('summary-price');
        const summaryTotal = document.getElementById('summary-total');
        
        if (!inputJumlah || !summaryQty) return;
        
        function formatRupiah(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }
        
        function calculateTotal() {
            let jumlah = parseInt(inputJumlah.value) || 0;
            if (jumlah < 1) jumlah = 1;
            
            let pricePerTicket = defaultPrice;
            if (isCurug && selectCamping && selectCamping.value === 'Ya') {
                pricePerTicket = campingPrice;
            }
            
            let total = pricePerTicket * jumlah;
            
            summaryQty.textContent = jumlah;
            summaryPrice.textContent = formatRupiah(total);
            summaryTotal.textContent = formatRupiah(total);
        }
        
        // Hindari duplikasi listener jika function dipanggil berkali-kali
        inputJumlah.removeEventListener('input', calculateTotal);
        inputJumlah.addEventListener('input', calculateTotal);
        if (selectCamping) {
            selectCamping.removeEventListener('change', calculateTotal);
            selectCamping.addEventListener('change', calculateTotal);
        }
        
        calculateTotal();
    }
    
    // Tetap menggunakan turbo:load sesuai permintaan
    document.addEventListener("turbo:load", hitungHargaTiket);
    
    // Eksekusi fallback langsung untuk mengatasi masalah ngrok yang sering skip turbo:load pertama
    setTimeout(hitungHargaTiket, 150);
</script>
@endpush
