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
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama wisata wajib diisi.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.min' => 'Harga tiket tidak boleh negatif.',
        ]);

        $validated['harga_tiket'] = (int) round($validated['harga_tiket']);

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
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nama.required' => 'Nama wisata wajib diisi.',
            'harga_tiket.required' => 'Harga tiket wajib diisi.',
            'harga_tiket.min' => 'Harga tiket tidak boleh negatif.',
        ]);

        $validated['harga_tiket'] = (int) round($validated['harga_tiket']);

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
            'image' => $path,
            'caption' => $request->input('caption'),
        ];
        $wisata->update(['galleries' => $galleries]);

        return back()->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function destroyGallery(Wisata $wisata, $index)
    {
        $galleries = $wisata->galleries ?? [];
        
        if (isset($galleries[$index])) {
            $imagePath = $galleries[$index]['image'];
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            unset($galleries[$index]);
            // Re-index array
            $galleries = array_values($galleries);
            $wisata->update(['galleries' => $galleries]);
            return back()->with('success', 'Foto galeri berhasil dihapus.');
        }

        return back()->with('error', 'Foto galeri tidak ditemukan.');
    }
}
