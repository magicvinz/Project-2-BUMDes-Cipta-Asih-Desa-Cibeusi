<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Penjualan_{{ str_replace(' ', '_', $wisata->nama) }}_{{ str_replace(' ', '_', $label) }}</title>
    <style>
        /* CSS Khusus Print */
        @page { size: A4 portrait; margin: 2 cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-surat h1 { font-size: 20pt; margin: 0 0 5px 0; text-transform: uppercase; }
        .kop-surat h2 { font-size: 16pt; margin: 0 0 5px 0; font-weight: normal; }
        .kop-surat p { font-size: 11pt; margin: 0; }
        
        .judul-laporan { text-align: center; margin-bottom: 30px; }
        .judul-laporan h3 { font-size: 14pt; text-transform: uppercase; text-decoration: underline; margin: 0 0 5px 0; }
        .judul-laporan p { margin: 0; font-size: 11pt; }

        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .summary-box h4 { margin: 0 0 10px 0; font-size: 12pt; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .summary-row { display: flex; justify-content: space-between; }
        .summary-col { flex: 1; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        th, td { border: 1px solid #000; padding: 6px 8px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .signature-area {
            width: 100%;
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-box { width: 250px; text-align: center; }
        .tandatangan-space { height: 80px; }

        @media print { .no-print { display: none !important; } }

        .print-btn-container { text-align: center; margin: 20px 0; }
        .btn-print {
            padding: 10px 25px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12pt;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="btn-print">Cetak Laporan Sekarang</button>
        <p style="color: #666; font-size: 10pt; font-family: Arial; margin-top: 10px;">Tekan Ctrl+P (Windows) atau Cmd+P (Mac) sebagai alternatif.</p>
    </div>

    <div class="kop-surat">
        <h1>BUMDes Siasih - {{ $wisata->nama }}</h1>
        <h2>Laporan Rekapitulasi Rinci Penjualan</h2>
        <p>Sistem Pemesanan Tiket Wisata Alam Desa Cibeusi</p>
    </div>

    <div class="judul-laporan">
        <h3>Laporan Rinci Penjualan Tiket</h3>
        <p>Periode: {{ $label }}</p>
    </div>

    <div class="summary-box">
        <h4>Ringkasan Pendapatan</h4>
        <div class="summary-row">
            <div class="summary-col">
                <strong>Penjualan Online:</strong><br>
                Tiket: {{ number_format($totalTiketOnline, 0, ',', '.') }} Tiket<br>
                Rupiah: Rp {{ number_format($totalPendapatanOnline, 0, ',', '.') }}
            </div>
            <div class="summary-col text-center">
                <strong>Penjualan Offline Loket:</strong><br>
                Tiket: {{ number_format($totalTiketOffline, 0, ',', '.') }} Tiket<br>
                Rupiah: Rp {{ number_format($totalPendapatanOffline, 0, ',', '.') }}
            </div>
            <div class="summary-col text-right">
                <strong>Total Keseluruhan Laporan:</strong><br>
                <div style="font-size: 14pt; margin-top: 5px;">
                    <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title">A. Rincian Penjualan Online</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Waktu Pemesanan</th>
                <th style="width: 15%;">Kode Tiket</th>
                <th style="width: 25%;">Pemesan</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 20%;">Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataOnline as $index => $d)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">{{ $d->kode_tiket }}</td>
                <td>{{ $d->user->name ?? '-' }}</td>
                <td class="text-center">{{ $d->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($d->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center"><em>Tidak ada transaksi online pada periode ini.</em></td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal Online</strong></td>
                <td class="text-center"><strong>{{ $totalTiketOnline }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalPendapatanOnline, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">B. Rincian Penjualan Offline (Loket Fisik)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal Laporan</th>
                <th style="width: 25%;">Staf / Diinput Oleh</th>
                <th style="width: 20%;">Jumlah Tiket</th>
                <th style="width: 25%;">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataOffline as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                <td class="text-center">{{ $item->creator->name ?? '-' }}</td>
                <td class="text-center">{{ $item->jumlah_tiket }}</td>
                <td class="text-right">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center"><em>Tidak ada penjualan offline pada periode ini.</em></td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Subtotal Offline</strong></td>
                <td class="text-center"><strong>{{ $totalTiketOffline }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalPendapatanOffline, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p style="margin-bottom: 5px;">Subang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin-top: 0;">Admin Pengelola Laporan {{ $wisata->nama }},</p>
            <div class="tandatangan-space"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">{{ Auth::user()->name }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        }
    </script>
</body>
</html>
