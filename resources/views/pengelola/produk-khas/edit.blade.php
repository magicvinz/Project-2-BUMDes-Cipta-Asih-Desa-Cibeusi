@extends('layouts.app')

@section('title', 'Ubah Produk Khas')

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.produk-khas.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk Khas
    </a>
</div>

<h4 class="fs-4 fw-semibold mb-4">Ubah Produk Khas</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('pengelola.produk-khas.update', $produkKhas) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-medium">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $produkKhas->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_wisata" class="form-label fw-medium">Asal Tempat Wisata</label>
                        <select name="id_wisata" id="id_wisata" class="form-select @error('id_wisata') is-invalid @enderror">
                            <option value="">-- Tidak Terikat Wisata Tertentu --</option>
                            @foreach($wisataList as $w)
                                <option value="{{ $w->id }}" {{ old('id_wisata', $produkKhas->id_wisata) == $w->id ? 'selected' : '' }}>{{ $w->nama }}</option>
                            @endforeach
                        </select>
                        @error('id_wisata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-medium">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $produkKhas->keterangan) }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="urutan" class="form-label fw-medium">Urutan tampil</label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $produkKhas->urutan) }}" min="0" class="form-control @error('urutan') is-invalid @enderror">
                        @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="gambar" class="form-label fw-medium">Gambar</label>
                        @if($produkKhas->gambar_url)
                        <div class="mb-2">
                            <img src="{{ $produkKhas->gambar_url }}" alt="{{ $produkKhas->nama }}" class="rounded" style="height: 100px; width: auto; object-fit: cover;">
                            <div class="form-text mt-1">Gambar saat ini. Unggah baru untuk mengganti.</div>
                        </div>
                        @endif
                        <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="form-control @error('gambar') is-invalid @enderror">
                        <div class="form-text">Maks. 2MB. Format: jpeg, png, jpg, gif, webp.</div>
                        @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                        <a href="{{ route('pengelola.produk-khas.index') }}" class="btn btn-light px-4 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
