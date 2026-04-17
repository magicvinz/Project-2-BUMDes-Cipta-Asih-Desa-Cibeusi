<?php

namespace App\Http\Controllers;

use App\Models\ProdukKhas;

class ProdukKhasController extends Controller
{
    /**
     * Menampilkan daftar produk khas desa Cibeusi.
     */
    public function index()
    {
        $produk = ProdukKhas::orderBy('urutan')->orderBy('nama')->get();

        return view('produk-khas.index', compact('produk'));
    }
}
