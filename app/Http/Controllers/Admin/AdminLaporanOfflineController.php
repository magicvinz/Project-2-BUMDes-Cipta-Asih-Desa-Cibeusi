<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenjualanOffline;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLaporanOfflineController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if (!$wisata) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak terhubung ke wisata.');
        }

        $penjualan = PenjualanOffline::with(['wisata', 'creator'])
            ->where('id_wisata', $wisata->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_penjualan_offline', 'desc')
            ->paginate(15);

        return view('admin.penjualan-offline.index', compact('penjualan', 'wisata'));
    }

    public function create()
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if (!$wisata) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak terhubung ke wisata.');
        }

        return view('admin.penjualan-offline.create', compact('wisata'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if (!$wisata) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak terhubung ke wisata.');
        }

        $request->validate([
            'tanggal'      => 'required|date',
            'jumlah_tiket' => 'required|integer|min:1',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $harga = $wisata->harga_tiket;
        if ($wisata->hasCamping() && $request->input('keterangan') === 'camping') {
            $harga = Wisata::HARGA_CAMPING_TIKET_CURUG;
        }

        $total_pendapatan = $request->jumlah_tiket * $harga;

        PenjualanOffline::create([
            'id_wisata'        => $wisata->id,
            'tanggal'          => $request->tanggal,
            'jumlah_tiket'     => $request->jumlah_tiket,
            'total_pendapatan' => $total_pendapatan,
            'id_user'          => Auth::id(),
        ]);

        return redirect()->route('admin.laporan')
            ->with('success', 'Data penjualan offline berhasil ditambahkan.');
    }

    public function edit(PenjualanOffline $penjualanOffline)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        // Pastikan data milik wisata admin ini
        if ($penjualanOffline->id_wisata !== $wisata?->id) {
            abort(403);
        }

        return view('admin.penjualan-offline.edit', compact('penjualanOffline', 'wisata'));
    }

    public function update(Request $request, PenjualanOffline $penjualanOffline)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if ($penjualanOffline->id_wisata !== $wisata?->id) {
            abort(403);
        }

        $request->validate([
            'tanggal'      => 'required|date',
            'jumlah_tiket' => 'required|integer|min:1',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $harga = $wisata->harga_tiket;
        if ($wisata->hasCamping() && $request->input('keterangan') === 'camping') {
            $harga = Wisata::HARGA_CAMPING_TIKET_CURUG;
        }

        $total_pendapatan = $request->jumlah_tiket * $harga;

        $penjualanOffline->update([
            'tanggal'          => $request->tanggal,
            'jumlah_tiket'     => $request->jumlah_tiket,
            'total_pendapatan' => $total_pendapatan,
        ]);

        return redirect()->route('admin.laporan')
            ->with('success', 'Data penjualan offline berhasil diperbarui.');
    }

    public function destroy(PenjualanOffline $penjualanOffline)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if ($penjualanOffline->id_wisata !== $wisata?->id) {
            abort(403);
        }

        $penjualanOffline->delete();

        return redirect()->route('admin.laporan')
            ->with('success', 'Data penjualan offline berhasil dihapus.');
    }
}
