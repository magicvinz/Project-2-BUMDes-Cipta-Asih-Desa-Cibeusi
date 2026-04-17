<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTicketReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tiket:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat via WhatsApp H-1 sebelum tanggal berkunjung';

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

        $count = 0;

        foreach ($tikets as $tiket) {
            if (!$tiket->user || !$tiket->user->no_hp) {
                continue;
            }

            $nama = $tiket->user->name;
            $wisata = $tiket->wisata->nama;
            $kode = $tiket->kode_tiket;
            $jml = $tiket->jumlah;

            $pesan = "*PENGINGAT (H-1) KUNJUNGAN WISATA*\n\n"
                   . "Halo Kak *$nama* 👋,\n\n"
                   . "Ini adalah pengingat bahwa besok Anda memiliki jadwal kunjungan ke:\n\n"
                   . "🏖️ *Wisata:* $wisata\n"
                   . "🎟️ *Kode Tiket:* $kode\n"
                   . "👥 *Jumlah:* $jml Orang\n\n"
                   . "Siapkan diri Anda dan jangan lupa membawa tiket QR untuk discan di lokasi. Selamat berlibur!\n\n"
                   . "_Pesan otomatis dari SI-ASIH_";

            $waService = new \App\Services\WhatsAppService();
            $waService->sendMessage($tiket->user->no_hp, $pesan);
            
            $this->info("Pengingat terkirim ke {$tiket->user->name} untuk tiket {$kode}");
            $count++;
            
            // Jeda sedikit agar tidak kena antispam rate limit WA
            sleep(1);
        }

        $this->info("Selesai. Total $count pengingat terkirim.");
    }
}
