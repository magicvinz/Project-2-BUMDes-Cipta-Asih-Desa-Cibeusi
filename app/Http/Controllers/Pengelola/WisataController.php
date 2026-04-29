<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WisataController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengelola_bumdes']);
    }

    public function index()
    {
        $wisata = Wisata::orderBy('nama')->get();
        return view('pengelola.wisata.index', compact('wisata'));
    }

    public function create()
    {
        return view('pengelola.wisata.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:0',
            'harga_camping' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama wisata wajib diisi.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.min' => 'Harga tiket tidak boleh negatif.',
        ]);

        $validated['harga_tiket'] = (int) round($validated['harga_tiket']);
        $validated['harga_camping'] = $request->filled('harga_camping') ? (int) round((float)$request->input('harga_camping')) : 0;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('wisata', 'public');
        } else {
            unset($validated['gambar']);
        }

        Wisata::create($validated);
        return redirect()->route('pengelola.wisata.index')->with('success', 'Tempat wisata berhasil ditambahkan.');
    }

    public function show(Wisata $wisata)
    {
        $wisata->load(['reviews.user']);
        return view('pengelola.wisata.show', compact('wisata'));
    }

    public function edit(Wisata $wisata)
    {
        return view('pengelola.wisata.edit', compact('wisata'));
    }

    public function update(Request $request, Wisata $wisata)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_tiket' => 'required|numeric|min:0',
            'harga_camping' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama wisata wajib diisi.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.min' => 'Harga tiket tidak boleh negatif.',
        ]);

        $validated['harga_tiket'] = (int) round($validated['harga_tiket']);
        $validated['harga_camping'] = $request->filled('harga_camping') ? (int) round((float)$request->input('harga_camping')) : 0;


        if ($request->hasFile('gambar')) {
            if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
                Storage::disk('public')->delete($wisata->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('wisata', 'public');
        } else {
            unset($validated['gambar']);
        }

        $wisata->update($validated);
        return redirect()->route('pengelola.wisata.index')->with('success', 'Tempat wisata berhasil diperbarui.');
    }

    public function destroy(Wisata $wisata)
    {
        if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
            Storage::disk('public')->delete($wisata->gambar);
        }
        $wisata->delete();
        return redirect()->route('pengelola.wisata.index')->with('success', 'Tempat wisata berhasil dihapus.');
    }

    public function storeGallery(Request $request, Wisata $wisata)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('wisata-gallery', 'public');

        $galleries = $wisata->galleries ?? [];
        $galleries[] = [
            'image'   => $path,
            'caption' => $request->input('caption'),
            'source'  => 'pengelola', // Menandai foto ditambahkan oleh pengelola
        ];
        $wisata->update(['galleries' => $galleries]);

        return back()->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function destroyGallery(Wisata $wisata, $index)
    {
        $galleries = $wisata->galleries ?? [];

        if (!isset($galleries[$index])) {
            return back()->with('error', 'Foto galeri tidak ditemukan.');
        }

        // Hanya foto yang ditambahkan oleh pengelola yang boleh dihapus
        $source = $galleries[$index]['source'] ?? 'pengelola'; // default 'pengelola' untuk data lama
        if ($source !== 'pengelola') {
            return back()->with('error', 'Foto ini tidak dapat dihapus karena berasal dari ulasan pengunjung.');
        }

        $imagePath = $galleries[$index]['image'];
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        unset($galleries[$index]);
        $galleries = array_values($galleries); // Re-index array
        $wisata->update(['galleries' => $galleries]);

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
