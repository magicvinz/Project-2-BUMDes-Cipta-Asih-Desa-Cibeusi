# Login Google & Fitur QR

## 1. Login dengan Google (Pengunjung)

Pengunjung bisa login atau daftar menggunakan akun Google.

### Setup Google OAuth

1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat project baru atau pilih project → **APIs & Services** → **Credentials**.
3. **Create Credentials** → **OAuth client ID**.
4. Application type: **Web application**.
5. Authorized redirect URIs: tambahkan  
   `https://domain-anda.com/auth/google/callback`  
   (development: `http://localhost:8000/auth/google/callback` atau `http://127.0.0.1:8000/auth/google/callback`).
6. Salin **Client ID** dan **Client Secret**.

### Konfigurasi di Laravel

Tambahkan di **`.env`**:

```env
GOOGLE_CLIENT_ID=xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxx
# Opsional; default pakai APP_URL + /auth/google/callback
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Lalu jalankan:

```bash
php artisan config:clear
```

### Perilaku

- **Login:** Tombol "Login dengan Google" di halaman login.
- **Daftar:** Tombol "Daftar dengan Google" di halaman daftar.
- Akun yang login via Google otomatis role **pengunjung**.
- Jika email Google sudah terdaftar (login biasa), akun tersebut akan di-link ke `google_id` dan bisa login dengan Google next time.

---

## 2. Scan QR dengan Kamera (Admin)

Di **Admin** → **Scan QR**, halaman validasi tiket punya dua cara:

1. **Scan dengan kamera:** Area preview kamera (kamera belakang di HP). Arahkan ke QR tiket pengunjung; begitu terdeteksi, kode tiket otomatis dikirim dan halaman hasil validasi muncul.
2. **Input manual:** Isi kode tiket (mis. SI-XXXXXXXX) lalu klik **Cari**.

### Teknologi

- Library: **html5-qrcode** (JavaScript, CDN).
- Browser meminta izin kamera (HTTPS atau localhost).
- Isi QR yang di-scan = **kode tiket** (mis. `SI-ABCD1234`). Server hanya menerima kode itu dan mengecek tiket seperti input manual.

---

## 3. QR Kode Tiket (Tanpa API Eksternal)

QR kode tiket **tidak lagi memakai API pihak ketiga**. Semua digenerate di server Laravel.

### Cara kerja

- **Package:** `simplesoftwareio/simple-qrcode` (BaconQrCode di belakangnya).
- **Route:** `GET /pengunjung/tiket/{tiket}/qrcode` (harus login pengunjung dan hanya untuk tiket milik sendiri).
- **Response:** Gambar PNG (QR berisi **kode tiket** saja, mis. `SI-ABCD1234`).
- Di halaman **Tiket Saya** / detail tiket, gambar QR memakai URL tersebut, jadi tidak ada request ke api.qrserver.com atau layanan luar.

### Install (jika belum)

```bash
composer require simplesoftwareio/simple-qrcode
```

Tidak perlu API key atau konfigurasi tambahan; cukup pakai route di atas.

---

## Ringkasan

| Fitur | Teknologi | Konfigurasi |
|-------|-----------|-------------|
| Login Google | Laravel Socialite + Google OAuth 2.0 | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` di `.env` |
| Scan QR Admin | html5-qrcode (JS, kamera) | Tidak ada; butuh HTTPS atau localhost + izin kamera |
| QR kode tiket | simple-qrcode (PHP, server) | Tidak ada API eksternal; route `/pengunjung/tiket/{id}/qrcode` |
