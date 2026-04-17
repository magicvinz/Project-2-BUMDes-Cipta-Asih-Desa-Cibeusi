<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use App\Models\ProdukKhas;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function wisataIndex()
    {
        $wisata = Wisata::orderBy('nama')->get();
        return view('publik.wisata-index', compact('wisata'));
    }

    public function wisataShow(Wisata $wisata)
    {
        // Load gallery nanti ditambahkan di tahap selanjutnya
        $wisata->load(['reviews.user']);
        return view('publik.wisata-show', compact('wisata'));
    }

    public function produkKhasIndex()
    {
        $produk = ProdukKhas::orderBy('urutan')->orderBy('nama')->get();
        return view('publik.produk-khas-index', compact('produk'));
    }

    public function produkKhasShow(ProdukKhas $produk_khas)
    {
        // Load gallery nanti ditambahkan di tahap selanjutnya
        return view('publik.produk-khas-show', compact('produk_khas'));
    }
}
