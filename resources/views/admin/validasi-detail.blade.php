@extends('layouts.app')

@section('title', 'Detail Tiket')

@section('content')
<h4 class="fs-4 fw-semibold mb-4">Detail Tiket</h4>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-4">
                        <tbody>
                            <tr class="border-bottom"><td class="text-muted py-2 w-50">Kode Tiket</td><td class="py-2 fw-medium text-end">{{ $tiket->kode_tiket }}</td></tr>
                            <tr class="border-bottom"><td class="text-muted py-2">Wisata</td><td class="py-2 text-end">{{ $tiket->wisata->nama }}</td></tr>
                            <tr class="border-bottom"><td class="text-muted py-2">Pemesan</td><td class="py-2 text-end">{{ $tiket->user->name }}</td></tr>
                            <tr class="border-bottom"><td class="text-muted py-2">Jumlah</td><td class="py-2 text-end">{{ $tiket->jumlah }} pengunjung</td></tr>
                            <tr class="border-bottom"><td class="text-muted py-2">Tanggal Berkunjung</td><td class="py-2 text-end">{{ $tiket->tanggal_berkunjung->format('d F Y') }}</td></tr>
                            @if($tiket->wisata->hasCamping() && $tiket->camping)
                            <tr class="border-bottom"><td class="text-muted py-2">Keterangan</td><td class="py-2 text-end">{{ $tiket->camping === 'Ya' ? 'Camping' : 'Kunjungan' }}</td></tr>
                            @endif
                            <tr><td class="text-muted py-2">Status</td><td class="py-2 text-end">
                                @if($tiket->status === 'paid')<span class="badge rounded-pill bg-success fw-normal">Sudah Dibayar</span>
                                @elseif($tiket->status === 'used')<span class="badge rounded-pill bg-secondary fw-normal">Sudah Terpakai</span>
                                @else<span class="badge rounded-pill bg-warning text-dark fw-normal">{{ ucfirst($tiket->status) }}</span>@endif
                            </td></tr>
                        </tbody>
                    </table>
                </div>

                @if($tiket->status === 'paid')
                <form action="{{ route('admin.validasi.validasi', $tiket) }}" method="post" class="mt-4" data-turbo="false">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                        <i class="bi bi-check-circle me-1"></i> Validasi Tiket (Tandai Sudah Terpakai)
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.validasi.index') }}" class="btn btn-outline-secondary w-100 py-2 mt-2 fw-medium">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
