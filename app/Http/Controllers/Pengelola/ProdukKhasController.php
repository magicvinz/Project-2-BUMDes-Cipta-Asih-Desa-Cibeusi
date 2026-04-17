<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\ProdukKhas;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukKhasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengelola_bumdes']);
    }

    public function index()
    {
        $produk = ProdukKhas::orderBy('urutan')->orderBy('nama')->get();
        return view('pengelola.produk-khas.index', compact('produk'));
    }

    public function create()
    {
        $wisataList = Wisata::orderBy('nama')->get();
        return view('pengelola.produk-khas.create', compact('wisataList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_wisata' => 'nullable|exists:Wisata,id_wisata',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama produk wajib diisi.',
            'id_wisata.exists' => 'Tempat wisata tidak valid.',
        ]);

        $validated['urutan'] = (int) ($validated['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk-khas', 'public');
        } else {
            unset($validated['gambar']);
        }

        ProdukKhas::create($validated);
        return redirect()->route('pengelola.produk-khas.index')->with('success', 'Produk khas berhasil ditambahkan.');
    }

    public function show(ProdukKhas $produkKhas)
    {
        return view('pengelola.produk-khas.show', compact('produkKhas'));
    }

    public function edit(ProdukKhas $produkKhas)
    {
        $wisataList = Wisata::orderBy('nama')->get();
        return view('pengelola.produk-khas.edit', compact('produkKhas', 'wisataList'));
    }

    public function update(Request $request, ProdukKhas $produkKhas)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_wisata' => 'nullable|exists:Wisata,id_wisata',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama produk wajib diisi.',
            'id_wisata.exists' => 'Tempat wisata tidak valid.',
        ]);

        $validated['urutan'] = (int) ($validated['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            if ($produkKhas->gambar && !str_starts_with($produkKhas->gambar, 'http') && Storage::disk('public')->exists($produkKhas->gambar)) {
                Storage::disk('public')->delete($produkKhas->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('produk-khas', 'public');
        } else {
            unset($validated['gambar']);
        }

        $produkKhas->update($validated);
        return redirect()->route('pengelola.produk-khas.index')->with('success', 'Produk khas berhasil diperbarui.');
    }

    public function destroy(ProdukKhas $produkKhas)
    {
        if ($produkKhas->gambar && !str_starts_with($produkKhas->gambar, 'http') && Storage::disk('public')->exists($produkKhas->gambar)) {
            Storage::disk('public')->delete($produkKhas->gambar);
        }
        $produkKhas->delete();
        return redirect()->route('pengelola.produk-khas.index')->with('success', 'Produk khas berhasil dihapus.');
    }

    public function storeGallery(Request $request, ProdukKhas $produkKhas)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('produk-khas-gallery', 'public');

        $galleries = $produkKhas->galleries ?? [];
        $galleries[] = [
            'image' => $path,
            'caption' => $request->input('caption'),
        ];
        $produkKhas->update(['galleries' => $galleries]);

        return back()->with('success', 'Foto berhasil ditambahkan ke galeri produk.');
    }

    public function destroyGallery(ProdukKhas $produkKhas, $index)
    {
        $galleries = $produkKhas->galleries ?? [];
        
        if (isset($galleries[$index])) {
            $imagePath = $galleries[$index]['image'];
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            unset($galleries[$index]);
            // Re-index array
            $galleries = array_values($galleries);
            $produkKhas->update(['galleries' => $galleries]);
            return back()->with('success', 'Foto galeri berhasil dihapus.');
        }

        return back()->with('error', 'Foto galeri tidak ditemukan.');
    }
}
