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

        $hariIniOnline = Tiket::where('id_wisata', $wisata->id)->paid()->whereDate('created_at', today())->sum('jumlah');
        $hariIniOffline = \App\Models\PenjualanOffline::where('id_wisata', $wisata->id)->whereDate('tanggal', today())->sum('jumlah_tiket');
        $hariIni = $hariIniOnline + $hariIniOffline;

        $bulanIniOnline = Tiket::where('id_wisata', $wisata->id)->paid()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('jumlah');
        $bulanIniOffline = \App\Models\PenjualanOffline::where('id_wisata', $wisata->id)->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('jumlah_tiket');
        $bulanIni = $bulanIniOnline + $bulanIniOffline;

        $pendapatanBulanIniOnline = Tiket::where('id_wisata', $wisata->id)->paid()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');
        $pendapatanBulanIniOffline = \App\Models\PenjualanOffline::where('id_wisata', $wisata->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_pendapatan');
        $totalPendapatanBulan = $pendapatanBulanIniOnline + $pendapatanBulanIniOffline;

        $riwayatValidasi = Tiket::with('user')
            ->where('id_wisata', $wisata->id)
            ->where('status', 'used')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Data Chart Perbandingan Online vs Offline Bulan Ini
        $chartData = [
            'labels' => ['Online', 'Offline (Di Tempat)'],
            'tiket' => [(int)$bulanIniOnline, (int)$bulanIniOffline],
            'pendapatan' => [(float)$pendapatanBulanIniOnline, (float)$pendapatanBulanIniOffline]
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'wisata_nama' => $wisata->nama,
                'hariIni' => $hariIni,
                'bulanIni' => $bulanIni,
                'totalPendapatanBulan' => $totalPendapatanBulan,
                'riwayatValidasi' => $riwayatValidasi,
                'chartData' => $chartData
            ]);
        }

        return view('admin.dashboard', compact('wisata', 'hariIni', 'bulanIni', 'totalPendapatanBulan', 'riwayatValidasi', 'chartData'));
    }
}
