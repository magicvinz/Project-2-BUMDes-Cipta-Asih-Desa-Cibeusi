<?php

namespace App\Services;

use App\Models\Tiket;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production');
    }

    /**
     * Buat transaksi Snap. Mengembalikan snap_token atau null jika Midtrans tidak dikonfigurasi.
     */
    public function createTransaction(Tiket $tiket): ?string
    {
        if (! class_exists(\Midtrans\Snap::class)) {
            Log::warning('Midtrans: Class Snap not found. Jalankan: composer require midtrans/midtrans-php');
            session()->flash('midtrans_error', 'Package Midtrans tidak terinstall. Jalankan: composer require midtrans/midtrans-php');
            return null;
        }
        if (empty($this->serverKey)) {
            Log::warning('Midtrans: Server Key kosong. Cek .env MIDTRANS_SERVER_KEY');
            session()->flash('midtrans_error', 'Server Key kosong. Cek .env dan jalankan: php artisan config:clear');
            return null;
        }
        if (empty($this->clientKey)) {
            Log::warning('Midtrans: Client Key kosong. Cek .env MIDTRANS_CLIENT_KEY');
            session()->flash('midtrans_error', 'Client Key kosong. Cek .env dan jalankan: php artisan config:clear');
            return null;
        }

        \Midtrans\Config::$serverKey = $this->serverKey;
        \Midtrans\Config::$isProduction = $this->isProduction;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'SIASIH-' . $tiket->id . '-' . time();
        $tiket->update(['midtrans_order_id' => $orderId]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $tiket->total_harga,
            ],
            'customer_details' => [
                'first_name' => $tiket->user->name,
                'email' => $tiket->user->email,
            ],
            'item_details' => [
                [
                    'id' => (string) $tiket->id_wisata,
                    'price' => (int) $tiket->total_harga,
                    'quantity' => 1,
                    'name' => 'Tiket ' . $tiket->wisata->nama . ' x' . $tiket->jumlah,
                ],
            ],
            // Batas waktu pembayaran: 24 jam. Setelah itu halaman pembayaran & metode bayar tidak bisa dipakai.
            'expiry' => [
                'unit' => 'minutes',
                'duration' => (int) config('services.midtrans.expiry_minutes', 1440), // 1440 = 24 jam
            ],
            // Tombol "Back" / redirect setelah payment di halaman Midtrans mengarah ke Tiket Saya
            'callbacks' => [
                'finish' => \Illuminate\Support\Facades\URL::route('pengunjung.tiket.my', [], true),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            report($e);
            Log::error('Midtrans Snap Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'server_key_prefix' => substr($this->serverKey, 0, 15) . '...',
            ]);
            session()->flash('midtrans_error', $e->getMessage());
            return null;
        }
    }

    /**
     * Verifikasi notifikasi dari Midtrans (webhook)
     */
    public function verifyNotification(array $payload): bool
    {
        if (empty($this->serverKey)) {
            return false;
        }
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        if ($signatureKey !== $expectedSignature) {
            return false;
        }
        return true;
    }
}
