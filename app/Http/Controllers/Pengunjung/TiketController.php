<?php

namespace App\Http\Controllers\Pengunjung;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Wisata;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TiketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengunjung']);
    }

    public function index()
    {
        $wisata = Wisata::orderBy('nama')->get();
        return view('pengunjung.wisata-index', compact('wisata'));
    }

    public function create(Wisata $wisata)
    {
        return view('pengunjung.tiket-create', compact('wisata'));
    }

    public function store(Request $request)
    {
        $wisata = Wisata::findOrFail($request->input('id_wisata'));
        $rules = [
            'id_wisata' => ['required', 'exists:Wisata,id_wisata'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:20'],
            'tanggal_berkunjung' => ['required', 'date', 'after_or_equal:today'],
            'camping' => ['nullable', 'in:Ya,Tidak'],
        ];
        $isCurug = $wisata->isCurugCibarebeuy();
        if ($isCurug) {
            $rules['camping'] = ['required', 'in:Ya,Tidak'];
        }
        $validated = $request->validate($rules);
        // Total pembayaran hanya tiket wisata; parkir dibayar manual di lokasi
        // Curug Cibarebeuy: Camping = Rp 25.000/tiket, bukan Camping = harga_tiket (Rp 10.000)
        $hargaPerTiket = (int) $wisata->harga_tiket;
        if ($isCurug && ($validated['camping'] ?? '') === 'Ya') {
            $hargaPerTiket = Wisata::HARGA_CAMPING_TIKET_CURUG;
        }
        $totalHarga = $hargaPerTiket * $validated['jumlah'];
        $kodeTiket = 'SI-' . strtoupper(Str::random(8));

        $data = [
            'id_user' => auth()->id(),
            'id_wisata' => $wisata->id,
            'kode_tiket' => $kodeTiket,
            'jumlah' => $validated['jumlah'],
            'total_harga' => $totalHarga,
            'status' => 'pending',
            'tanggal_berkunjung' => $validated['tanggal_berkunjung'],
        ];
        if ($isCurug) {
            $data['camping'] = $validated['camping'] ?? null;
        }
        $tiket = Tiket::create($data);

        // Coba buat transaksi Midtrans; jika tidak dikonfigurasi, pakai simulasi
        $midtrans = new MidtransService;
        $snapToken = $midtrans->createTransaction($tiket);

        if ($snapToken) {
            return view('pengunjung.tiket-bayar', [
                'tiket' => $tiket->load('wisata', 'user'),
                'snap_token' => $snapToken,
            ]);
        }

        // Mode simulasi: langsung ke halaman tiket (anggap bayar manual / offline)
        return redirect()->route('pengunjung.tiket.show', $tiket)->with('success', 'Pemesanan berhasil. Silakan lakukan pembayaran atau gunakan simulasi.');
    }

    /**
     * Halaman pembayaran (untuk retry atau dari store)
     */
    public function bayar(Tiket $tiket)
    {
        if ($tiket->id_user !== auth()->id()) {
            abort(403);
        }
        if ($tiket->status !== 'pending') {
            return redirect()->route('pengunjung.tiket.show', $tiket)->with('info', 'Tiket ini sudah dibayar.');
        }
        $tiket->load('wisata', 'user');
        $midtrans = new MidtransService;
        $snapToken = $midtrans->createTransaction($tiket);

        if ($snapToken) {
            return view('pengunjung.tiket-bayar', [
                'tiket' => $tiket,
                'snap_token' => $snapToken,
            ]);
        }

        $msg = 'Midtrans tidak tersedia. Gunakan simulasi pembayaran di halaman detail tiket.';
        if (session('midtrans_error') && config('app.debug')) {
            $msg .= ' (Debug: ' . session('midtrans_error') . ')';
        }
        return redirect()->route('pengunjung.tiket.show', $tiket)->with('error', $msg);
    }

    public function show(Request $request, Tiket $tiket)
    {
        if ($tiket->id_user !== auth()->id()) {
            abort(403);
        }
        $tiket->load('wisata');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $tiket->id,
                'kode_tiket' => $tiket->kode_tiket,
                'status' => $tiket->status,
                'wisata_nama' => $tiket->wisata->nama,
            ]);
        }

        return view('pengunjung.tiket-show', compact('tiket'));
    }

    public function myTickets(Request $request)
    {
        $tiket = Tiket::where('id_user', auth()->id())
            ->with('wisata')
            ->latest()
            ->paginate(10);

        if ($request->expectsJson() || $request->ajax()) {
            $items = collect($tiket->items())->map(function ($t) {
                $isCurug = $t->wisata && $t->wisata->isCurugCibarebeuy();
                return [
                    'id' => $t->id,
                    'kode_tiket' => $t->kode_tiket,
                    'wisata_nama' => $t->wisata->nama,
                    'wisata_slug' => $t->wisata->slug ?? '',
                    'jumlah' => $t->jumlah,
                    'tanggal_berkunjung' => \Carbon\Carbon::parse($t->tanggal_berkunjung)->format('d/m/Y'),
                    'camping' => $isCurug && $t->camping ? $t->camping : null,
                    'status' => $t->status,
                ];
            });
            return response()->json([
                'data' => $items,
                'current_page' => $tiket->currentPage(),
                'last_page' => $tiket->lastPage(),
            ]);
        }

        return view('pengunjung.my-tickets', compact('tiket'));
    }

    /**
     * Generate gambar QR kode tiket (menggunakan BaconQrCode, fallback ke API eksternal).
     * Tambahkan ?download=1 untuk mengunduh file.
     */
    public function qrcode(Request $request, Tiket $tiket)
    {
        // Cek ownership
        if (!auth()->check() || $tiket->id_user !== auth()->id()) {
            abort(403);
        }

        // Pastikan relasi wisata ter-load
        $tiket->load('wisata');

        $download = $request->boolean('download');
        
        // Generate content QR
        $content = $tiket->qr_content;
        
        if (empty($content)) {
            $content = $tiket->kode_tiket; // Fallback ke kode saja jika qr_content gagal
        }

        try {
            $svg = QrCode::size(240)->generate($content);
            $res = response($svg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'private, max-age=3600');
            
            if ($download) {
                $filename = 'QR-' . $tiket->kode_tiket . '.svg';
                $res->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
            return $res;
        } catch (\Throwable $e) {
            Log::error('QR local generation failed: ' . $e->getMessage());
            
            // Jika gagal, return SVG sederhana dengan kode tiket sebagai text
            $svg = '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="240" height="240"><rect width="240" height="240" fill="#f0f0f0"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-family="Arial" font-size="12">' . htmlspecialchars($tiket->kode_tiket) . '</text></svg>';
            $res = response($svg)->header('Content-Type', 'image/svg+xml');
            if ($download) {
                $filename = 'QR-' . $tiket->kode_tiket . '.svg';
                $res->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
            return $res;
        }
    }

    /**
     * Simulasi pembayaran (untuk development tanpa Midtrans)
     */
    public function simulasiBayar(Tiket $tiket)
    {
        if ($tiket->id_user !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke tiket ini.');
        }
        if ($tiket->status !== 'pending') {
            return back()->with('error', $tiket->status === 'paid' || $tiket->status === 'used'
                ? 'Tiket ini sudah dibayar.'
                : 'Tiket tidak dapat dibayar. Status: ' . ucfirst($tiket->status));
        }

        $tiket->update(['status' => 'paid']);

        // Kirim Notifikasi WA (Silakan nyalakan Node.js server)
        $wa = new \App\Services\WhatsAppService();
        $userHp = $tiket->user->no_hp;
        if ($userHp) {
            $tglStr = \Carbon\Carbon::parse($tiket->tanggal_berkunjung)->format('d/m/Y');
            $pesan = "Halo {$tiket->user->name},\n\nPembayaran tiket Anda untuk wisata *{$tiket->wisata->nama}* telah berhasil.\n\nKode Tiket: *{$tiket->kode_tiket}*\nJumlah: {$tiket->jumlah} orang\nTanggal: {$tglStr}\n\nTunjukkan e-tiket / QR Code ini di loket masuk.\nTerima kasih!";
            
            $encodedContent = urlencode($tiket->qr_content ?? $tiket->kode_tiket);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={$encodedContent}";
            
            $wa->sendMedia($userHp, $pesan, $qrUrl);
        }

        return redirect()->route('pengunjung.tiket.show', $tiket)->with('success', 'Pembayaran berhasil (simulasi). E-Tiket telah dikirimkan via WA.');
    }
}
