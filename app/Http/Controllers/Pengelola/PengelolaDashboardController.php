<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Wisata;
use App\Models\PenjualanOffline;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengelolaDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengelola_bumdes']);
    }

    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulan');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $date = Carbon::parse($tanggal);

        $dateCallback = function ($q) use ($periode, $date) {
            $q->paid();
            if ($periode === 'hari') {
                $q->whereDate('created_at', $date);
            } elseif ($periode === 'minggu') {
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                $q->whereBetween('created_at', [$start, $end]);
            } else {
                $q->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            }
        };

        $dateCallbackOffline = function ($q) use ($periode, $date) {
            if ($periode === 'hari') {
                $q->whereDate('tanggal', $date);
            } elseif ($periode === 'minggu') {
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                $q->whereBetween('tanggal', [$start, $end]);
            } else {
                $q->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
            }
        };

        $wisata = Wisata::withSum(['tiket as tiket_online' => $dateCallback], 'jumlah')
            ->withSum(['tiket as pendapatan_online' => $dateCallback], 'total_harga')
            ->withSum(['penjualanOfflines as tiket_offline' => $dateCallbackOffline], 'jumlah_tiket')
            ->withSum(['penjualanOfflines as pendapatan_offline' => $dateCallbackOffline], 'total_pendapatan')
            ->get();

        $queryTotal = Tiket::paid();
        $queryTotalOffline = PenjualanOffline::query();
        if ($periode === 'hari') {
            $queryTotal->whereDate('created_at', $date);
            $queryTotalOffline->whereDate('tanggal', $date);
        } elseif ($periode === 'minggu') {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
            $queryTotal->whereBetween('created_at', [$start, $end]);
            $queryTotalOffline->whereBetween('tanggal', [$start, $end]);
        } else {
            $queryTotal->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryTotalOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
        }

        $totalTiket = $queryTotal->sum('jumlah') + $queryTotalOffline->sum('jumlah_tiket');
        $totalPendapatan = $queryTotal->sum('total_harga') + $queryTotalOffline->sum('total_pendapatan');

        $labelWaktu = 'Bulan Ini';
        if ($periode === 'hari') {
            $labelWaktu = 'Hari Ini';
        } elseif ($periode === 'minggu') {
            $labelWaktu = 'Minggu Ini';
        }

        if ($request->expectsJson() || $request->ajax()) {
            $wisataData = $wisata->map(fn ($w) => [
                'nama' => $w->nama,
                'tiket_bulan_ini' => (int) ($w->tiket_online ?? 0) + (int) ($w->tiket_offline ?? 0),
                'pendapatan_bulan_ini' => (float) ($w->pendapatan_online ?? 0) + (float) ($w->pendapatan_offline ?? 0),
            ]);
            return response()->json([
                'labelWaktu' => $labelWaktu,
                'totalTiketBulan' => $totalTiket,
                'totalPendapatanBulan' => $totalPendapatan,
                'wisata' => $wisataData,
            ]);
        }

        // Data untuk grafik
        $chartData = [
            'labels' => $wisata->pluck('nama')->toArray(),
            'tiket_terjual' => $wisata->map(fn($w) => (int) ($w->tiket_online ?? 0) + (int) ($w->tiket_offline ?? 0))->toArray(),
            'pendapatan' => $wisata->map(fn($w) => (float) ($w->pendapatan_online ?? 0) + (float) ($w->pendapatan_offline ?? 0))->toArray(),
        ];

        return view('pengelola.dashboard', [
            'wisata' => $wisata,
            'totalTiketBulan' => $totalTiket,
            'totalPendapatanBulan' => $totalPendapatan,
            'chartData' => $chartData,
            'periode' => $periode,
            'tanggal' => $tanggal,
            'labelWaktu' => $labelWaktu,
        ]);
    }

    public function laporan(Request $request)
    {
        $periode = $request->get('periode', 'hari');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $date = Carbon::parse($tanggal);

        // Query Online
        $queryOnline = Tiket::with('wisata')->paid();
        // Query Offline
        $queryOffline = PenjualanOffline::query();

        if ($periode === 'hari') {
            $queryOnline->whereDate('created_at', $date);
            $queryOffline->whereDate('tanggal', $date);
            $label = $date->translatedFormat('l, d F Y');
        } elseif ($periode === 'minggu') {
            $start = $date->copy()->startOfWeek();
            $end   = $date->copy()->endOfWeek();
            $queryOnline->whereBetween('created_at', [$start, $end]);
            $queryOffline->whereBetween('tanggal', [$start, $end]);
            $label = 'Minggu ' . $start->format('d/m') . ' - ' . $end->format('d/m/Y');
        } else {
            $queryOnline->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
            $label = $date->translatedFormat('F Y');
        }

        $dataOnline  = $queryOnline->orderBy('created_at')->get();
        $dataOffline = $queryOffline->get();

        $groupedOnline  = $dataOnline->groupBy('id_wisata');
        $groupedOffline = $dataOffline->groupBy('id_wisata');

        $wisata = Wisata::orderBy('nama')->get();
        $rekap  = [];
        foreach ($wisata as $w) {
            $online  = $groupedOnline->get($w->id, collect());
            $offline = $groupedOffline->get($w->id, collect());

            $jumlahOnline      = $online->sum('jumlah');
            $pendapatanOnline  = $online->sum('total_harga');
            $jumlahOffline     = $offline->sum('jumlah_tiket');
            $pendapatanOffline = $offline->sum('total_pendapatan');

            $rekap[] = [
                'wisata'            => $w,
                'jumlah_tiket'      => $jumlahOnline + $jumlahOffline,
                'pendapatan'        => $pendapatanOnline + $pendapatanOffline,
                'transaksi'         => $online->count() + $offline->count(),
                // rincian per channel (opsional, untuk laporan detail)
                'tiket_online'      => $jumlahOnline,
                'pendapatan_online' => $pendapatanOnline,
                'tiket_offline'     => $jumlahOffline,
                'pendapatan_offline'=> $pendapatanOffline,
            ];
        }

        $totalTiket      = $dataOnline->sum('jumlah')       + $dataOffline->sum('jumlah_tiket');
        $totalPendapatan = $dataOnline->sum('total_harga')  + $dataOffline->sum('total_pendapatan');

        if ($request->expectsJson() || $request->ajax()) {
            $rekapJson = [];
            foreach ($rekap as $r) {
                $rekapJson[] = [
                    'wisata_nama'  => $r['wisata']->nama,
                    'transaksi'    => $r['transaksi'],
                    'jumlah_tiket' => $r['jumlah_tiket'],
                    'pendapatan'   => $r['pendapatan'],
                ];
            }
            return response()->json([
                'label'          => $label,
                'rekap'          => $rekapJson,
                'totalTiket'     => $totalTiket,
                'totalPendapatan'=> $totalPendapatan,
            ]);
        }

        return view('pengelola.laporan', compact('periode', 'tanggal', 'label', 'rekap', 'totalTiket', 'totalPendapatan'));
    }
}
