<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use App\Models\ProdukKhas;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function wisataIndex()
    {
        $wisata = Wisata::orderBy('nama')->get();
        return view('publik.wisata-index', compact('wisata'));
    }

    public function wisataShow(Wisata $wisata)
    {
        $wisata->load(['reviews.user']);

        // Cek apakah pengunjung yang login punya tiket used untuk wisata ini
        // dan belum memberikan ulasan (untuk form ulasan langsung di halaman wisata)
        $tiketBisaUlasan = null;
        if (auth()->check() && auth()->user()->isPengunjung()) {
            $tiketBisaUlasan = \App\Models\Tiket::where('id_wisata', $wisata->id_wisata)
                ->where('id_user', auth()->id())
                ->where('status', 'used')
                ->whereDoesntHave('review')
                ->latest('used_at')
                ->first();
        }

        return view('publik.wisata-show', compact('wisata', 'tiketBisaUlasan'));
    }

    public function produkKhasIndex()
    {
        $produk = ProdukKhas::orderBy('urutan')->orderBy('nama')->get();
        return view('publik.produk-khas-index', compact('produk'));
    }

    public function produkKhasShow(ProdukKhas $produk_khas)
    {
        // Load gallery nanti ditambahkan di tahap selanjutnya
        return view('publik.produk-khas-show', compact('produk_khas'));
    }

    public function getWeather()
    {
        // Cache data cuaca selama 30 menit
        return cache()->remember('openmeteo_weather_cibeusi', 1800, function () {
            try {
                // Koordinat GPS dari Plus Code 7M3G+585 (Parkiran Curug Cibareubeuy, Cibeusi, Ciater, Subang)
                $url = 'https://api.open-meteo.com/v1/forecast?latitude=-6.7461&longitude=107.7288&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=Asia%2FJakarta';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode != 200 || empty($response)) {
                    throw new \Exception('Gagal mengambil data cuaca');
                }

                $data = json_decode($response, true);
                if (!$data || !isset($data['current'])) {
                    throw new \Exception('Format data cuaca tidak valid');
                }

                $current = $data['current'];
                $weatherCode = $current['weather_code'];
                $info = $this->parseWmoWeatherCode($weatherCode);

                $cuaca = [
                    'suhu' => round($current['temperature_2m']) . '°C',
                    'kelembaban' => $current['relative_humidity_2m'] . '%',
                    'angin' => round($current['wind_speed_10m']) . ' km/jam',
                    'kondisi' => $info['kondisi'],
                    'icon' => $info['icon']
                ];

                return response()->json([
                    'success' => true,
                    'data' => $cuaca
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }

    private function parseWmoWeatherCode($kode)
    {
        // WMO Weather interpretation codes (Open-Meteo)
        $kode = (int)$kode;
        $map = [
            0 => ['kondisi' => 'Cerah', 'icon' => 'bi-sun'],
            1 => ['kondisi' => 'Cerah Berawan', 'icon' => 'bi-cloud-sun'],
            2 => ['kondisi' => 'Berawan', 'icon' => 'bi-cloud'],
            3 => ['kondisi' => 'Mendung', 'icon' => 'bi-clouds'],
            45 => ['kondisi' => 'Berkabut', 'icon' => 'bi-cloud-fog'],
            48 => ['kondisi' => 'Kabut Rime', 'icon' => 'bi-cloud-fog2'],
            51 => ['kondisi' => 'Gerimis Ringan', 'icon' => 'bi-cloud-drizzle'],
            53 => ['kondisi' => 'Gerimis Sedang', 'icon' => 'bi-cloud-drizzle'],
            55 => ['kondisi' => 'Gerimis Lebat', 'icon' => 'bi-cloud-drizzle'],
            56 => ['kondisi' => 'Gerimis Beku Ringan', 'icon' => 'bi-cloud-drizzle'],
            57 => ['kondisi' => 'Gerimis Beku Lebat', 'icon' => 'bi-cloud-drizzle'],
            61 => ['kondisi' => 'Hujan Ringan', 'icon' => 'bi-cloud-rain'],
            63 => ['kondisi' => 'Hujan Sedang', 'icon' => 'bi-cloud-rain'],
            65 => ['kondisi' => 'Hujan Lebat', 'icon' => 'bi-cloud-rain-heavy'],
            66 => ['kondisi' => 'Hujan Beku Ringan', 'icon' => 'bi-cloud-rain'],
            67 => ['kondisi' => 'Hujan Beku Lebat', 'icon' => 'bi-cloud-rain-heavy'],
            71 => ['kondisi' => 'Salju Ringan', 'icon' => 'bi-snow'],
            73 => ['kondisi' => 'Salju Sedang', 'icon' => 'bi-snow'],
            75 => ['kondisi' => 'Salju Lebat', 'icon' => 'bi-snow'],
            77 => ['kondisi' => 'Butiran Salju', 'icon' => 'bi-snow'],
            80 => ['kondisi' => 'Hujan Lokal Ringan', 'icon' => 'bi-cloud-rain'],
            81 => ['kondisi' => 'Hujan Lokal Sedang', 'icon' => 'bi-cloud-rain-heavy'],
            82 => ['kondisi' => 'Hujan Lokal Lebat', 'icon' => 'bi-cloud-rain-heavy'],
            85 => ['kondisi' => 'Hujan Salju Ringan', 'icon' => 'bi-snow'],
            86 => ['kondisi' => 'Hujan Salju Lebat', 'icon' => 'bi-snow'],
            95 => ['kondisi' => 'Badai Petir', 'icon' => 'bi-cloud-lightning-rain'],
            96 => ['kondisi' => 'Badai Petir & Hujan Es Ringan', 'icon' => 'bi-cloud-lightning-rain'],
            99 => ['kondisi' => 'Badai Petir & Hujan Es Lebat', 'icon' => 'bi-cloud-lightning-rain']
        ];

        return $map[$kode] ?? ['kondisi' => 'Tidak Diketahui', 'icon' => 'bi-cloud'];
    }
}
