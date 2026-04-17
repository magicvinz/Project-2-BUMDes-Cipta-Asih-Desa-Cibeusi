# Panduan Integrasi Midtrans - SI-ASIH

Sistem SI-ASIH sudah disiapkan untuk pembayaran via Midtrans. Jika Anda ingin mengaktifkan pembayaran online, ikuti langkah berikut.

---

## 1. Daftar Akun Midtrans

1. Buka [https://dashboard.midtrans.com/](https://dashboard.midtrans.com/)
2. Klik **Daftar** dan buat akun (email, password, data merchant).
3. Verifikasi email dan login ke **Dashboard Midtrans**.

---

## 2. Ambil API Keys (Sandbox untuk Development)

1. **Penting:** Login ke **Sandbox** dashboard: [https://dashboard.sandbox.midtrans.com/](https://dashboard.sandbox.midtrans.com/)
2. Pilih **Settings** → **Access Keys**.
3. Pastikan mode **Sandbox** aktif (untuk testing).
4. Salin key yang **berawalan SB-**:
   - **Server Key**: format `SB-Mid-server-xxxxxxxx` (untuk backend)
   - **Client Key**: format `SB-Mid-client-xxxxxxxx` (untuk frontend / Snap.js)

> ⚠️ Jika key Anda **tidak** berawalan `SB-` (misalnya `Mid-server-...`), berarti itu key **Production**. Untuk testing gunakan key Sandbox dari dashboard.sandbox.midtrans.com

---

## 3. Konfigurasi di Laravel

1. Buka file **`.env`** di root proyek.
2. Tambahkan (ganti dengan key Anda):

```env
# Midtrans (Sandbox) - PASTIKAN key berawalan SB-
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**Format key yang benar:**
- Sandbox: `SB-Mid-server-xxx` dan `SB-Mid-client-xxx`
- Production: `Mid-server-xxx` dan `Mid-client-xxx` (tanpa SB-)

Jika `MIDTRANS_IS_PRODUCTION=false`, Anda **wajib** pakai key Sandbox (SB-).

3. Untuk **production**, ganti dengan key production dan set:
   ```env
   MIDTRANS_IS_PRODUCTION=true
   ```

4. Simpan `.env` dan jalankan:
   ```bash
   php artisan config:clear
   ```

---

## 4. Install Dependency (jika belum)

```bash
composer install
```

Package `midtrans/midtrans-php` sudah tercantum di `composer.json`. Setelah di-install, pembayaran Snap akan otomatis muncul saat pengunjung memesan tiket.

---

## 5. URL Notification (Webhook) untuk Status Pembayaran

Midtrans akan mengirim status pembayaran (pending/success/failed) ke server Anda.

1. Di dashboard Midtrans: **Settings** → **Configuration** → **Notification URL**.
2. Isi dengan URL aplikasi Anda, contoh:
   - Lokal (ngrok): `https://xxxx.ngrok.io/payment/notification`
   - Server: `https://domainanda.com/payment/notification`

3. Pastikan URL ini **dapat diakses dari internet** (untuk production pakai domain asli; untuk development bisa pakai [ngrok](https://ngrok.com/)).

---

## 6. Alur Kerja di Aplikasi

- **Tanpa Midtrans** (Server Key & Client Key kosong):  
  Setelah pesan tiket, pengunjung bisa klik **Simulasi Bayar** untuk mengubah status jadi "Sudah Dibayar" (untuk uji coba).

- **Dengan Midtrans**:  
  Setelah pesan tiket, muncul popup **Snap** Midtrans. Setelah pembayaran sukses di Midtrans, Midtrans memanggil `/payment/notification`. Aplikasi akan mengubah status tiket menjadi **paid** sehingga QR code bisa digunakan.

---

## 7. Testing di Sandbox

- Gunakan [kartu test Midtrans](https://docs.midtrans.com/docs/credit-card-testing) untuk simulasi pembayaran.
- Setelah bayar di Snap sandbox, cek di dashboard Midtrans → **Transactions** untuk melihat status.

---

## Ringkasan Checklist

| Langkah | Keterangan |
|--------|------------|
| 1 | Daftar akun Midtrans |
| 2 | Ambil Server Key & Client Key (Sandbox/Production) |
| 3 | Isi `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION` di `.env` |
| 4 | Jalankan `composer install` |
| 5 | Set Notification URL di dashboard Midtrans ke `https://domain-anda/payment/notification` |

Setelah itu, pembayaran tiket SI-ASIH akan berjalan melalui Midtrans.
