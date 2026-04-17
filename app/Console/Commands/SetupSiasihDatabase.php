<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupSiasihDatabase extends Command
{
    protected $signature = 'siasih:setup-database {--no-seed : Lewati pengisian data awal}';
    protected $description = 'Buat database jika belum ada, lalu migrate:fresh + seed';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');

        if (empty($database)) {
            $this->error('DB_DATABASE di .env belum diisi.');
            return 1;
        }

        $this->info("Menggunakan database: {$database}");

        try {
            // Coba koneksi tanpa memilih database (pakai mysql default)
            $pdo = new \PDO(
                'mysql:host=' . config('database.connections.mysql.host') . ';port=' . config('database.connections.mysql.port'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password')
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("Database '{$database}' siap.");
        } catch (\Throwable $e) {
            $this->warn("Tidak bisa membuat database otomatis: " . $e->getMessage());
            $this->line("Pastikan database '{$database}' sudah dibuat di MySQL, lalu jalankan lagi.");
        }

        $this->info('Menjalankan migrate:fresh...');
        $exit = $this->call('migrate:fresh', [
            '--force' => true,
        ]);

        if ($exit !== 0) {
            return $exit;
        }

        if (! $this->option('no-seed')) {
            $this->info('Menjalankan db:seed...');
            $this->call('db:seed', ['--force' => true]);
        }

        $this->newLine();
        $this->info('Selesai. Login: pengunjung@siasih.com / password');
        return 0;
    }
}
