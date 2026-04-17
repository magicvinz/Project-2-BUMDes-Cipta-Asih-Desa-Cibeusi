<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenjualanOffline;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $user   = Auth::user();
        $wisata = $user->wisata;

        if (! $wisata) {
            return redirect()->route('home')
                ->with('error', 'Admin tidak terhubung ke wisata. Hubungi pengelola untuk menghubungkan akun ke wisata.');
        }

        $periode = $request->get('periode', 'hari'); // hari | minggu | bulan
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));

        // ── Query Tiket Online (status paid atau used) ────────────────────────
        $queryOnline = Tiket::with('user')
            ->where('id_wisata', $wisata->id)
            ->whereIn('status', ['paid', 'used']);

        // ── Query Penjualan Offline ───────────────────────────────────────────
        $queryOffline = PenjualanOffline::with('creator')
            ->where('id_wisata', $wisata->id);

        // ── Terapkan filter periode ───────────────────────────────────────────
        if ($periode === 'hari') {
            $date  = Carbon::parse($tanggal);
            $label = $date->translatedFormat('l, d F Y');
            $queryOnline->whereDate('created_at', $date);
            $queryOffline->whereDate('tanggal', $date);
        } elseif ($periode === 'minggu') {
            $date  = Carbon::parse($tanggal);
            $start = $date->copy()->startOfWeek();
            $end   = $date->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d/m') . ' – ' . $end->format('d/m/Y');
            $queryOnline->whereBetween('created_at', [$start, $end]);
            $queryOffline->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        } else {
            $date  = Carbon::parse($tanggal);
            $label = $date->translatedFormat('F Y');
            $queryOnline->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
        }

        $dataOnline  = $queryOnline->orderBy('created_at')->get();
        $dataOffline = $queryOffline->orderBy('tanggal')->orderBy('id_penjualan_offline')->get();

        // ── Ringkasan ─────────────────────────────────────────────────────────
        $totalTiketOnline      = $dataOnline->sum('jumlah');
        $totalPendapatanOnline = $dataOnline->sum('total_harga');
        $totalTiketOffline     = $dataOffline->sum('jumlah_tiket');
        $totalPendapatanOffline = $dataOffline->sum('total_pendapatan');
        $grandTotal            = $totalPendapatanOnline + $totalPendapatanOffline;

        // ── JSON untuk AJAX ──────────────────────────────────────────────────
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'label'                  => $label,
                'totalTiketOnline'       => $totalTiketOnline,
                'totalPendapatanOnline'  => $totalPendapatanOnline,
                'totalTiketOffline'      => $totalTiketOffline,
                'totalPendapatanOffline' => $totalPendapatanOffline,
                'grandTotal'             => $grandTotal,
                'dataOnline'  => $dataOnline->map(fn ($d) => [
                    'waktu'      => $d->created_at->format('d/m/Y H:i'),
                    'kode_tiket' => $d->kode_tiket,
                    'pemesan'    => $d->user->name ?? '-',
                    'jumlah'     => $d->jumlah,
                    'total_harga'=> $d->total_harga,
                    'status'     => $d->status,
                ]),
                'dataOffline' => $dataOffline->map(fn ($d) => [
                    'id'               => $d->id_penjualan_offline,
                    'tanggal'          => $d->tanggal->translatedFormat('d F Y'),
                    'jumlah_tiket'     => $d->jumlah_tiket,
                    'total_pendapatan' => $d->total_pendapatan,
                    'diinput_oleh'     => $d->creator->name ?? '-',
                    'url_edit'         => route('admin.penjualan-offline.edit', $d->id_penjualan_offline),
                    'url_destroy'      => route('admin.penjualan-offline.destroy', $d->id_penjualan_offline),
                ]),
            ]);
        }

        return view('admin.laporan', compact(
            'wisata', 'periode', 'tanggal', 'label',
            'dataOnline', 'dataOffline',
            'totalTiketOnline', 'totalPendapatanOnline',
            'totalTiketOffline', 'totalPendapatanOffline',
            'grandTotal'
        ));
    }

    public function print(Request $request)
    {
        $user   = Auth::user();
        $wisata = $user->wisata;

        if (! $wisata) {
            return redirect()->route('home')
                ->with('error', 'Admin tidak terhubung ke wisata.');
        }

        $periode = $request->get('periode', 'hari'); // hari | minggu | bulan
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));

        // ── Query Tiket Online (status paid atau used) ────────────────────────
        $queryOnline = Tiket::with('user')
            ->where('id_wisata', $wisata->id)
            ->whereIn('status', ['paid', 'used']);

        // ── Query Penjualan Offline ───────────────────────────────────────────
        $queryOffline = PenjualanOffline::with('creator')
            ->where('id_wisata', $wisata->id);

        // ── Terapkan filter periode ───────────────────────────────────────────
        if ($periode === 'hari') {
            $date  = Carbon::parse($tanggal);
            $label = $date->translatedFormat('l, d F Y');
            $queryOnline->whereDate('created_at', $date);
            $queryOffline->whereDate('tanggal', $date);
        } elseif ($periode === 'minggu') {
            $date  = Carbon::parse($tanggal);
            $start = $date->copy()->startOfWeek();
            $end   = $date->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d/m') . ' – ' . $end->format('d/m/Y');
            $queryOnline->whereBetween('created_at', [$start, $end]);
            $queryOffline->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        } else {
            $date  = Carbon::parse($tanggal);
            $label = $date->translatedFormat('F Y');
            $queryOnline->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year);
            $queryOffline->whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year);
        }

        $dataOnline  = $queryOnline->orderBy('created_at')->get();
        $dataOffline = $queryOffline->orderBy('tanggal')->orderBy('id_penjualan_offline')->get();

        // ── Ringkasan ─────────────────────────────────────────────────────────
        $totalTiketOnline      = $dataOnline->sum('jumlah');
        $totalPendapatanOnline = $dataOnline->sum('total_harga');
        $totalTiketOffline     = $dataOffline->sum('jumlah_tiket');
        $totalPendapatanOffline = $dataOffline->sum('total_pendapatan');
        $grandTotal            = $totalPendapatanOnline + $totalPendapatanOffline;

        return view('admin.laporan-print', compact(
            'wisata', 'periode', 'tanggal', 'label',
            'dataOnline', 'dataOffline',
            'totalTiketOnline', 'totalPendapatanOnline',
            'totalTiketOffline', 'totalPendapatanOffline',
            'grandTotal'
        ));
    }
}
