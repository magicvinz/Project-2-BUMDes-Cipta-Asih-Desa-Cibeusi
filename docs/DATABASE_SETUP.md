# Setup Database SI-ASIH

Panduan ini memastikan database lengkap dan sistem berjalan tanpa error (termasuk perbaikan error query `email` dan foreign key `wisata`).

---

## Cara Otomatis (Satu Perintah)

Jalankan salah satu:

```bash
php artisan siasih:setup-database
```

Atau di Windows: **double-click file `fix-database.bat`** di root proyek.

Perintah ini akan:
1. Membuat database (dari `DB_DATABASE` di `.env`) jika belum ada
2. Menjalankan `migrate:fresh` (semua tabel dibuat ulang)
3. Menjalankan `db:seed` (data wisata + akun contoh)

Tanpa seed: `php artisan siasih:setup-database --no-seed`

Pastikan `.env` berisi `DB_DATABASE=siasih` (atau nama lain) dan kredensial MySQL benar.

---

## Cara 1: Laravel Migrate + Seed (Manual)

Jalankan di root proyek:

```bash
# Hapus semua tabel, buat ulang, lalu isi data awal
php artisan migrate:fresh --seed
```

Ini akan:
1. Menghapus semua tabel
2. Menjalankan migrasi (users, password_reset_tokens, failed_jobs, personal_access_tokens, **wisata**, **tiket**, kolom **role** dan **wisata_id** di users)
3. Mengisi data wisata (Curug, Puncak, Bukit) dan akun contoh (Pengelola, 3 Admin, 1 Pengunjung)

Setelah itu login dengan:
- **Email:** `pengunjung@siasih.com`  
- **Password:** `password`

---

## Cara 2: Import SQL Manual (Jika Migrate Bermasalah)

1. Buat database kosong (misalnya `siasih`).
2. Jalankan migrasi Laravel dulu agar tabel `users` terbentuk:
   ```bash
   php artisan migrate
   ```
3. Import skema SI-ASIH:
   ```bash
   mysql -u root -p siasih < database/siasih_schema.sql
   ```
   Atau buka phpMyAdmin → pilih database → Import → pilih file `database/siasih_schema.sql`.

**Catatan:** Jika muncul error "Duplicate column name 'role'" atau "Duplicate column name 'wisata_id'", artinya kolom itu sudah ada. Bisa abaikan error ALTER tersebut, atau jalankan hanya bagian CREATE TABLE dan INSERT dari file SQL.

---

## Perbaikan Error Query Email

Error seperti:
```text
select * from `users` where `email` = pengunjung@siasih.com limit 1
```
(nilai email tanpa tanda kutip)

Sudah diperbaiki di kode dengan:
- Memastikan nilai `email` selalu dikirim sebagai **string** ke `Auth::attempt()` di `AuthController`.
- Pastikan tabel `users` punya kolom **role** dan **wisata_id** (dari migrasi). Jika belum, jalankan:
  ```bash
  php artisan migrate
  ```
  atau gunakan migrasi lengkap `2025_02_21_100000_create_siasih_database_complete.php`.

---

## Cek Isi Database

Setelah `migrate:fresh --seed`:

| Tabel   | Isi |
|--------|-----|
| `wisata` | 3 baris (Curug, Puncak, Bukit) |
| `users`  | 5 baris (1 Pengelola, 3 Admin, 1 Pengunjung), dengan kolom `role` dan `wisata_id` |
| `tiket`  | Kosong (sampai ada pemesanan) |

Pastikan di `.env`:
- `DB_DATABASE` = nama database Anda
- `DB_USERNAME` dan `DB_PASSWORD` sesuai MySQL Anda

---

## Akun Contoh (Setelah Seed)

| Role             | Email                   | Password |
|------------------|-------------------------|----------|
| Pengelola BUMDes | pengelola@siasih.com    | password |
| Admin Curug      | admin.curug@siasih.com  | password |
| Admin Puncak     | admin.puncak@siasih.com | password |
| Admin Bukit      | admin.bukit@siasih.com  | password |
| Pengunjung       | pengunjung@siasih.com   | password |
