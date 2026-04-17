<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;

        if (! $orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $midtrans = new MidtransService;
        if (! $midtrans->verifyNotification($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        $tiket = Tiket::where('midtrans_order_id', $orderId)->first();
        if (! $tiket) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus === 'accept') {
            if ($tiket->status !== 'paid') {
                $tiket->update([
                    'status' => 'paid',
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                ]);

                // Kirim notifikasi WhatsApp
                $wa = new \App\Services\WhatsAppService();
                $tiket->load('user', 'wisata');
                if ($tiket->user && $tiket->user->no_hp) {
                    $pesan = "Halo {$tiket->user->name},\n\nPembayaran via Midtrans untuk wisata *{$tiket->wisata->nama}* berhasil dikonfirmasi.\n\nKode Trx: {$tiket->midtrans_order_id}\nKode Tiket: *{$tiket->kode_tiket}*\nTanggal Kunjungan: {$tiket->tanggal_berkunjung->format('d/m/Y')}\n\nTunjukkan pesan ini beserta QR Code kepada petugas.";
                    
                    $encodedContent = urlencode($tiket->qr_content ?? $tiket->kode_tiket);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={$encodedContent}";
                    
                    $wa->sendMedia($tiket->user->no_hp, $pesan, $qrUrl);
                }
            }
        } elseif (in_array($transactionStatus, ['pending'])) {
            // Tetap pending
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel']) || $fraudStatus === 'deny') {
            $tiket->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'OK']);
    }
}
