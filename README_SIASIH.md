# SI-ASIH - Sistem Informasi Pemesanan Tiket Wisata

Sistem informasi pemesanan tiket online berbasis QR code untuk wisata alam BUMDes Cipta Asih Desa Cibeusi.

## Fitur

- **Pengunjung**: Daftar, login, pesan tiket (Curug Cibarebeuy, Puncak Pasir Ipis, Bukit Panineungan), lihat tiket saya, QR code tiket, pembayaran via Midtrans atau simulasi.
- **Admin (per wisata)**: Dashboard, scan/input kode QR tiket, validasi tiket (tandai sudah terpakai), laporan penjualan harian/mingguan/bulanan untuk wisatanya saja.
- **Pengelola BUMDes**: Dashboard rekap semua wisata, laporan gabungan harian/mingguan/bulanan dari ketiga admin.

## Instalasi

```bash
# 1. Copy .env
cp .env.example .env

# 2. Generate key
php artisan key:generate

# 3. Install dependency (termasuk Midtrans)
composer install

# 4. Database: buat otomatis + migrate + seed (disarankan)
php artisan siasih:setup-database
# Atau manual: php artisan migrate:fresh --seed
```

**Windows:** Bisa juga double-click `fix-database.bat` setelah langkah 1–3.

Pastikan di `.env`: `DB_DATABASE=siasih` (atau nama database Anda), `DB_USERNAME`, `DB_PASSWORD` sesuai MySQL.

## Akun Default (setelah seed)

| Role            | Email                  | Password  |
|-----------------|------------------------|-----------|
| Pengelola BUMDes| pengelola@siasih.com   | password  |
| Admin Curug     | admin.curug@siasih.com | password  |
| Admin Puncak    | admin.puncak@siasih.com| password  |
| Admin Bukit     | admin.bukit@siasih.com | password  |
| Pengunjung      | pengunjung@siasih.com  | password  |

## Integrasi Midtrans

Pembayaran online memakai Midtrans. Tanpa konfigurasi Midtrans, pengunjung bisa pakai **Simulasi Bayar** untuk testing.

Langkah lengkap ada di **[MIDTRANS_SETUP.md](MIDTRANS_SETUP.md)**.

## Harga Tiket (default seed)

- Curug Cibarebeuy: Rp 15.000
- Puncak Pasir Ipis: Rp 20.000
- Bukit Panineungan: Rp 25.000

## Route Penting

- `/` — Beranda
- `/login`, `/register` — Auth
- `/dashboard` — Redirect sesuai role
- `/pengunjung` — Dashboard pengunjung & pesan tiket
- `/admin` — Dashboard admin, scan QR, laporan
- `/pengelola` — Dashboard pengelola, laporan gabungan
- `/payment/notification` — Webhook Midtrans (POST, no CSRF)

---

SI-ASIH © BUMDes Cipta Asih Desa Cibeusi
