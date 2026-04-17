<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\PenjualanOffline;
use App\Models\Tiket;
use App\Models\Wisata;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengelola_bumdes']);
    }

    public function index(Request $request)
    {
        $periode = $request->get('periode', 'hari'); // hari, minggu, bulan
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $jenis   = $request->get('jenis', 'semua');
        
        $date = Carbon::parse($tanggal);

        // Menyiapkan Query Filter
        $queryTiket = Tiket::paid();
        $queryOffline = PenjualanOffline::query();

        if ($periode === 'hari') {
            $queryTiket->whereDate('created_at', $date);
            $queryOffline->whereDate('tanggal', $date);
            $label = $date->translatedFormat('l, d F Y');
        } elseif ($periode === 'minggu') {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
            $queryTiket->whereBetween('created_at', [$start, $end]);
            $queryOffline->whereBetween('tanggal', [$start, $end]);
            $label = 'Minggu ' . $start->format('d/m') . ' - ' . $end->format('d/m/Y');
        } else {
            $queryTiket->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
            $label = $date->translatedFormat('F Y');
        }

        $wisatas = Wisata::all();
        $laporan = collect();

        $totalTiketOnlineAll = 0;
        $totalPendapatanOnlineAll = 0;
        $totalTiketOfflineAll = 0;
        $totalPendapatanOfflineAll = 0;

        foreach ($wisatas as $wisata) {
            // Ambil data Online
            $tiketOnlineJumlah = 0;
            $pendapatanOnline = 0;
            if ($jenis === 'semua' || $jenis === 'online') {
                $tiketOnline = (clone $queryTiket)->where('id_wisata', $wisata->id)->get();
                $tiketOnlineJumlah = $tiketOnline->sum('jumlah');
                $pendapatanOnline = $tiketOnline->sum('total_harga');
            }

            // Ambil data Offline
            $tiketOfflineJumlah = 0;
            $pendapatanOffline = 0;
            if ($jenis === 'semua' || $jenis === 'offline') {
                $tiketOffline = (clone $queryOffline)->where('id_wisata', $wisata->id)->get();
                $tiketOfflineJumlah = $tiketOffline->sum('jumlah_tiket');
                $pendapatanOffline = $tiketOffline->sum('total_pendapatan');
            }

            $totalPendapatanWisata = $pendapatanOnline + $pendapatanOffline;
            
            // Masukkan ke dalam collection rekap
            $laporan->push((object)[
                'wisata' => $wisata->nama,
                'tiket_online' => $tiketOnlineJumlah,
                'pendapatan_online' => $pendapatanOnline,
                'tiket_offline' => $tiketOfflineJumlah,
                'pendapatan_offline' => $pendapatanOffline,
                'total_pendapatan' => $totalPendapatanWisata,
            ]);

            // Tambahkan ke Total Keseluruhan
            $totalTiketOnlineAll += $tiketOnlineJumlah;
            $totalPendapatanOnlineAll += $pendapatanOnline;
            $totalTiketOfflineAll += $tiketOfflineJumlah;
            $totalPendapatanOfflineAll += $pendapatanOffline;
        }

        $totalKeseluruhan = $totalPendapatanOnlineAll + $totalPendapatanOfflineAll;

        return view('pengelola.laporan.index', compact(
            'periode', 'tanggal', 'label', 'laporan', 
            'totalTiketOnlineAll', 'totalPendapatanOnlineAll',
            'totalTiketOfflineAll', 'totalPendapatanOfflineAll', 'totalKeseluruhan', 'jenis'
        ));
    }

    public function print(Request $request)
    {
        $periode = $request->get('periode', 'hari'); 
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $jenis   = $request->get('jenis', 'semua');
        
        $date = Carbon::parse($tanggal);

        $queryTiket = Tiket::paid();
        $queryOffline = PenjualanOffline::query();

        if ($periode === 'hari') {
            $queryTiket->whereDate('created_at', $date);
            $queryOffline->whereDate('tanggal', $date);
            $label = $date->translatedFormat('l, d F Y');
        } elseif ($periode === 'minggu') {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
            $queryTiket->whereBetween('created_at', [$start, $end]);
            $queryOffline->whereBetween('tanggal', [$start, $end]);
            $label = 'Minggu ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        } else {
            $queryTiket->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
            $label = $date->translatedFormat('F Y');
        }

        $wisatas = Wisata::all();
        $laporan = collect();

        $totalTiketOnlineAll = 0;
        $totalPendapatanOnlineAll = 0;
        $totalTiketOfflineAll = 0;
        $totalPendapatanOfflineAll = 0;

        foreach ($wisatas as $wisata) {
            $tiketOnlineJumlah = 0;
            $pendapatanOnline = 0;
            if ($jenis === 'semua' || $jenis === 'online') {
                $tiketOnline = (clone $queryTiket)->where('id_wisata', $wisata->id)->get();
                $tiketOnlineJumlah = $tiketOnline->sum('jumlah');
                $pendapatanOnline = $tiketOnline->sum('total_harga');
            }

            $tiketOfflineJumlah = 0;
            $pendapatanOffline = 0;
            if ($jenis === 'semua' || $jenis === 'offline') {
                $tiketOffline = (clone $queryOffline)->where('id_wisata', $wisata->id)->get();
                $tiketOfflineJumlah = $tiketOffline->sum('jumlah_tiket');
                $pendapatanOffline = $tiketOffline->sum('total_pendapatan');
            }

            $laporan->push((object)[
                'wisata' => $wisata->nama,
                'tiket_online' => $tiketOnlineJumlah,
                'pendapatan_online' => $pendapatanOnline,
                'tiket_offline' => $tiketOfflineJumlah,
                'pendapatan_offline' => $pendapatanOffline,
                'total_pendapatan' => $pendapatanOnline + $pendapatanOffline,
            ]);

            $totalTiketOnlineAll += $tiketOnlineJumlah;
            $totalPendapatanOnlineAll += $pendapatanOnline;
            $totalTiketOfflineAll += $tiketOfflineJumlah;
            $totalPendapatanOfflineAll += $pendapatanOffline;
        }

        $totalKeseluruhan = $totalPendapatanOnlineAll + $totalPendapatanOfflineAll;

        return view('pengelola.laporan.print', compact(
            'periode', 'tanggal', 'label', 'laporan', 
            'totalTiketOnlineAll', 'totalPendapatanOnlineAll',
            'totalTiketOfflineAll', 'totalPendapatanOfflineAll', 'totalKeseluruhan', 'jenis'
        ));
    }
}
