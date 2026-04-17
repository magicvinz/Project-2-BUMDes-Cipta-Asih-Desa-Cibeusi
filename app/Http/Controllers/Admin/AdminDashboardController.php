<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if (! $wisata) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Admin tidak terhubung ke wisata.'], 403);
            }
            return redirect()->route('home')->with('error', 'Admin tidak terhubung ke wisata. Hubungi pengelola untuk menghubungkan akun ke wisata.');
        }

        $hariIni = Tiket::where('id_wisata', $wisata->id)->paid()->whereDate('created_at', today())->count();
        $bulanIni = Tiket::where('id_wisata', $wisata->id)->paid()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $totalPendapatanBulan = Tiket::where('id_wisata', $wisata->id)->paid()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        $riwayatValidasi = Tiket::with('user')
            ->where('id_wisata', $wisata->id)
            ->where('status', 'used')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'wisata_nama' => $wisata->nama,
                'hariIni' => $hariIni,
                'bulanIni' => $bulanIni,
                'totalPendapatanBulan' => $totalPendapatanBulan,
                'riwayatValidasi' => $riwayatValidasi
            ]);
        }

        return view('admin.dashboard', compact('wisata', 'hariIni', 'bulanIni', 'totalPendapatanBulan', 'riwayatValidasi'));
    }
}
