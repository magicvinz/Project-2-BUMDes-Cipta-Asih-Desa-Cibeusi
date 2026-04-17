<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Kirim reminder setiap jam 1 siang untuk pengunjung H-1
        $schedule->command('tiket:reminder')->dailyAt('13:00');

        // Hapus log aktivitas yang lebih dari 3 bulan, berjalan setiap awal kuartal (Jan, Apr, Jul, Okt)
        $schedule->command('activity-log:clean')->quarterly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
