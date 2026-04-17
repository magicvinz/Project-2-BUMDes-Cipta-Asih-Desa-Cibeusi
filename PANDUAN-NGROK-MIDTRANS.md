# Ngrok + Midtrans — Callback Payment Notification

Agar Midtrans bisa mengirim notifikasi pembayaran ke aplikasi lokal Anda, server Midtrans harus bisa mengakses URL publik. Ngrok membuat tunnel dari internet ke Laragon Anda.

---

## 1. Endpoint callback di aplikasi ini

Route yang menangani notifikasi Midtrans:

- **Method:** `POST`
- **Path:** `/payment/notification`
- **Controller:** `MidtransNotificationController`

Jadi **URL callback** selalu: **`{BASE_URL}/payment/notification`**

Contoh:
- Lokal: `http://localhost/siasih/public/payment/notification`
- Dengan ngrok: `https://xxxx.ngrok-free.app/payment/notification`

---

## 2. Langkah: Koneksikan ngrok dengan Midtrans

### A. Jalankan ngrok

1. Install ngrok: https://ngrok.com/download (atau `choco install ngrok`).
2. Pastikan Laragon (Apache) sudah jalan dan situs bisa diakses di browser.
3. Di terminal, jalankan (sesuaikan port jika perlu):

   **Laragon pakai port 80:**
   ```bash
   ngrok http 80
   ```

   **Atau pakai port 8000 (jika pakai `php artisan serve`):**
   ```bash
   ngrok http 8000
   ```

4. Catat URL **https** yang muncul, misalnya:
   ```
   https://abc123.ngrok-free.app
   ```
   (URL ini berubah setiap kali ngrok dijalankan, kecuali pakai akun berbayar.)

   **Catatan:** Jika lewat ngrok Anda harus buka situs dengan subpath (mis. `https://abc123.ngrok-free.app/siasih/public`), pakai URL itu sebagai dasar: callback = `https://abc123.ngrok-free.app/siasih/public/payment/notification`.

### B. Atur .env saat pakai ngrok

Saat ingin tes pembayaran lewat ngrok, set **APP_URL** ke URL ngrok (tanpa `/` di akhir):

```env
APP_URL=https://abc123.ngrok-free.app
```

**Opsional** — jika ingin eksplisit untuk Midtrans, bisa tambahkan:

```env
MIDTRANS_NOTIFICATION_URL=https://abc123.ngrok-free.app/payment/notification
```

Lalu jalankan:

```bash
php artisan config:clear
```

### C. Daftarkan URL callback di Midtrans

1. Buka **Midtrans Dashboard**:
   - Sandbox: https://dashboard.sandbox.midtrans.com/
   - Production: https://dashboard.midtrans.com/
2. Masuk ke **Settings** → **Configuration** (atau **Configuration** → **Notification**).
3. Di **Payment notification URL** (atau **Notification URL**), isi **persis**:
   ```
   https://abc123.ngrok-free.app/payment/notification
   ```
   Ganti `abc123.ngrok-free.app` dengan URL ngrok Anda. Harus **https**, tidak ada spasi, tidak ada slash di akhir.
4. Simpan.

### D. Tes

1. Buka situs lewat URL ngrok: `https://abc123.ngrok-free.app`
2. Login → pesan tiket → bayar (Snap).
3. Selesaikan/simulasi pembayaran di Sandbox.
4. Midtrans akan POST ke `https://abc123.ngrok-free.app/payment/notification`; status tiket akan berubah otomatis jika signature valid.

---

## 3. Ringkasan

| Yang perlu Anda lakukan | Nilai |
|-------------------------|--------|
| Endpoint callback di kode | `POST /payment/notification` (sudah ada) |
| URL callback untuk Midtrans | `https://{URL-NGROK-ANDA}/payment/notification` |
| Isi di Midtrans Dashboard | Payment notification URL = URL di atas |
| Saat pakai ngrok, APP_URL di .env | `https://xxxx.ngrok-free.app` |

Setelah itu, Midtrans dan ngrok sudah terhubung ke endpoint callback di aplikasi ini.
