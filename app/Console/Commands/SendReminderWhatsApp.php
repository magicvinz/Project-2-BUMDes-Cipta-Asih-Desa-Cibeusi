<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReminderWhatsApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WhatsApp H-1 sebelum kunjungan untuk tiket lunas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $besok = \Carbon\Carbon::tomorrow()->toDateString();
        $tikets = \App\Models\Tiket::with(['user', 'wisata'])
            ->where('status', 'paid')
            ->whereDate('tanggal_berkunjung', $besok)
            ->get();

        if ($tikets->isEmpty()) {
            $this->info('Tidak ada tiket yang perlu direminder untuk besok.');
            return;
        }

        $wa = new \App\Services\WhatsAppService();
        $berhasil = 0;

        foreach ($tikets as $tiket) {
            $userHp = $tiket->user->no_hp ?? null;
            if ($userHp) {
                $pesan = "Halo {$tiket->user->name},\n\nJangan lupa, besok ({$tiket->tanggal_berkunjung->format('d/m/Y')}) adalah jadwal petualangan Anda di *{$tiket->wisata->nama}*!\n\nSiapkan fisik dan perlengkapan Anda. Pastikan membawa e-tiket (Kode: *{$tiket->kode_tiket}*) untuk ditunjukkan pada petugas kami.\n\nSelamat berwisata!\nBUMDes Cipta Asih";
                
                $wa->sendMessage($userHp, $pesan);
                $berhasil++;
            }
        }

        $this->info("Berhasil mengirim reminder ke {$berhasil} pengunjung.");
    }
}
