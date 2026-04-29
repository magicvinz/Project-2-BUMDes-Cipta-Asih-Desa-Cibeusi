<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Penjualan_Wisata_{{ str_replace(' ', '_', $label) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-cibeusi.png') }}">
    <style>
        /* CSS Khusus Print (Minimalist & Professional) */
        @page { size: A4 portrait; margin: 2 cm; }
        body {
            font-family: 'Times New Roman', Times, serif; /* Font resmi Laporan */
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        /* Header / Kop Laporan */
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
        }
        .kop-surat h1 {
            font-size: 20pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .kop-surat h2 {
            font-size: 16pt;
            margin: 0 0 5px 0;
            font-weight: normal;
        }
        .kop-surat p {
            font-size: 11pt;
            margin: 0;
        }
        
        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 30px;
        }
        .judul-laporan h3 {
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 5px 0;
        }
        .judul-laporan p {
            margin: 0;
            font-size: 11pt;
        }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Summary Section (Text Base, not Cards for Print) */
        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .summary-box h4 {
            margin: 0 0 10px 0;
            font-size: 12pt;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
        }
        .summary-col {
            flex: 1;
        }

        /* Tanda Tangan */
        .signature-area {
            width: 100%;
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .tandatangan-space {
            height: 80px;
        }

        /* Hide elements when printing */
        @media print {
            .no-print { display: none !important; }
        }

        /* Tombol Bantuan (Tengah Atas) */
        .print-btn-container {
            text-align: center;
            margin: 20px 0;
        }
        .btn-print {
            padding: 10px 25px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12pt;
            cursor: pointer;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="btn-print">Cetak Laporan Sekarang</button>
        <p style="color: #666; font-size: 10pt; font-family: Arial; margin-top: 10px;">Tekan Ctrl+P (Windows) atau Cmd+P (Mac) sebagai alternatif.</p>
    </div>

    <div class="kop-surat">
        <h1>BUMDes Siasih</h1>
        <h2>Sistem Informasi Pariwisata & Produk Khas</h2>
        <p>Jl. Pariwisata Cibeusi, Ciater, Kabupaten Subang, Jawa Barat</p>
    </div>

    <div class="judul-laporan">
        <h3>Laporan Rekapitulasi Pendapatan Wisata</h3>
        <p>Periode: {{ $label }}</p>
    </div>

    <!-- Ringkasan Teks (Lebih Printer-Friendly dari Card HTML) -->
    <div class="summary-box">
        <h4>Ringkasan Pendapatan</h4>
        <div class="summary-row">
            @if($jenis == 'semua' || $jenis == 'online')
            <div class="summary-col">
                <strong>Penjualan Online:</strong><br>
                Tiket: {{ number_format($totalTiketOnlineAll, 0, ',', '.') }} Tiket<br>
                Rupiah: Rp {{ number_format($totalPendapatanOnlineAll, 0, ',', '.') }}
            </div>
            @endif
            @if($jenis == 'semua' || $jenis == 'offline')
            <div class="summary-col text-center">
                <strong>Penjualan Offline:</strong><br>
                Tiket: {{ number_format($totalTiketOfflineAll, 0, ',', '.') }} Tiket<br>
                Rupiah: Rp {{ number_format($totalPendapatanOfflineAll, 0, ',', '.') }}
            </div>
            @endif
            <div class="summary-col text-right">
                <strong>Total Keseluruhan:</strong><br>
                <div style="font-size: 14pt; margin-top: 5px;">
                    <strong>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Rinci -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 25%;">Nama Wisata</th>
                @if($jenis == 'semua' || $jenis == 'online')
                <th colspan="2">Jalur Online</th>
                @endif
                @if($jenis == 'semua' || $jenis == 'offline')
                <th colspan="2">Jalur Offline (Fisik)</th>
                @endif
                <th rowspan="2" style="width: 15%;">Total Pendapatan</th>
            </tr>
            <tr>
                @if($jenis == 'semua' || $jenis == 'online')
                <th style="font-weight: normal; font-size: 10pt; width: 10%;">Tiket</th>
                <th style="font-weight: normal; font-size: 10pt; width: 15%;">Rupiah</th>
                @endif
                @if($jenis == 'semua' || $jenis == 'offline')
                <th style="font-weight: normal; font-size: 10pt; width: 10%;">Tiket</th>
                <th style="font-weight: normal; font-size: 10pt; width: 15%;">Rupiah</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->wisata }}</td>
                
                @if($jenis == 'semua' || $jenis == 'online')
                <td class="text-center">{{ $item->tiket_online > 0 ? $item->tiket_online : '-' }}</td>
                <td class="text-right">{{ $item->pendapatan_online > 0 ? number_format($item->pendapatan_online, 0, ',', '.') : '-' }}</td>
                @endif
                
                @if($jenis == 'semua' || $jenis == 'offline')
                <td class="text-center">{{ $item->tiket_offline > 0 ? $item->tiket_offline : '-' }}</td>
                <td class="text-right">{{ $item->pendapatan_offline > 0 ? number_format($item->pendapatan_offline, 0, ',', '.') : '-' }}</td>
                @endif
                
                <td class="text-right" style="font-weight: bold;">{{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $jenis == 'semua' ? '7' : '5' }}" class="text-center"><em>Tidak ada rekap penjualan wisata pada periode {{ $label }}.</em></td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                @if($jenis == 'semua' || $jenis == 'online')
                <td class="text-center">{{ number_format($totalTiketOnlineAll, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPendapatanOnlineAll, 0, ',', '.') }}</td>
                @endif
                @if($jenis == 'semua' || $jenis == 'offline')
                <td class="text-center">{{ number_format($totalTiketOfflineAll, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPendapatanOfflineAll, 0, ',', '.') }}</td>
                @endif
                <td class="text-right">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p style="margin-bottom: 5px;">Subang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin-top: 0;">Pengelola BUMDes Siasih,</p>
            
            <div class="tandatangan-space"></div>
            
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">{{ Auth::user()->name }}</p>
        </div>
    </div>

    <script>
        // Opsional: Otomatis memunculkan dialog print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
