<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;

    public function __construct()
    {
        // Secara default menggunakan localhost:3000
        $this->apiUrl = config('services.whatsapp.url', 'http://localhost:3000');
    }

    /**
     * Mengirim pesan text via WA Gateway Node.js
     *
     * @param string $number Nomor tujuan (format bebas, akan diformat ulang oleh Node.js atau di sini)
     * @param string $message Isi pesan
     * @return array|bool Response dari gateway, false jika gagal.
     */
    public function sendMessage($number, $message)
    {
        try {
            // Bisa cek status terlebih dahulu 
            // $status = Http::timeout(3)->get($this->apiUrl . '/status');
            // if (!$status->successful() || $status->json('status') !== 'ready') return false;

            $response = Http::timeout(10)->post($this->apiUrl . '/send', [
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WA Gateway Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WA Gateway Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim pesan beserta media via WA Gateway Node.js
     *
     * @param string $number Nomor tujuan
     * @param string $message Isi pesan / caption
     * @param string $mediaUrl URL gambar terbuka (berkas media publik)
     * @return array|bool Response dari gateway, false jika gagal.
     */
    public function sendMedia($number, $message, $mediaUrl)
    {
        try {
            $response = Http::timeout(15)->post($this->apiUrl . '/send', [
                'number' => $number,
                'message' => $message,
                'mediaUrl' => $mediaUrl,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WA Gateway Media Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WA Gateway Media Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah Bot WA sedang aktif dan siap.
     */
    public function isReady()
    {
        try {
            $response = Http::timeout(3)->get($this->apiUrl . '/status');
            return $response->successful() && $response->json('status') === 'ready';
        } catch (\Exception $e) {
            return false;
        }
    }
}
