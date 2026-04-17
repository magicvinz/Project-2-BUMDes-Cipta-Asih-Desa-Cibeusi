# Referensi: Route Redirect & Teks Tampilan

Dokumen ini menjelaskan **ke mana tombol/redirect mengarah** dan **di mana mengubah teks tampilan** (misalnya "Ke Dashboard") di aplikasi SI-ASIH.

---

## 1. Tombol Back / Redirect setelah Payment Successful

| Lokasi | Tombol/Link | Route / Tujuan |
|--------|-------------|----------------|
| **Detail Tiket** (setelah bayar sukses, `?payment=success`) | "Kembali ke Tiket Saya" | `route('pengunjung.tiket.my')` → halaman **Tiket Saya** |
| **Detail Tiket** (biasa) | "Ke Daftar Tiket Saya" | `route('pengunjung.tiket.my')` → halaman **Tiket Saya** |
| **Halaman Pembayaran (Midtrans)** | "← Kembali ke Tiket Saya" (header) | `route('pengunjung.tiket.my')` → **Tiket Saya** |
| **Midtrans (tombol Back di halaman mereka)** | Back / Kembali ke merchant | Diatur di backend: **callbacks.finish** → URL **Tiket Saya** (sama seperti di atas) |

Setelah pembayaran berhasil, baik dari halaman kita maupun dari tombol Back di Midtrans, pengguna diarahkan ke **Tiket Saya**.

---

## 2. Ringkasan Route per Halaman (Redirect sudah sesuai)

### Pengunjung

| Halaman | Tombol/Link | Route |
|---------|-------------|--------|
| Beranda (home) | Ke Dashboard | `route('dashboard')` |
| Beranda | Daftar & Pesan Tiket | `route('register')` |
| Beranda | Login | `route('login')` |
| Pilih Wisata | Pesan Tiket | `route('pengunjung.tiket.create', $w)` |
| Form Pesan Tiket | Batal | `route('pengunjung.dashboard')` → list wisata |
| Tiket Saya | Detail | `route('pengunjung.tiket.show', $t)` |
| Tiket Saya (kosong) | Pesan tiket | `route('pengunjung.dashboard')` |
| Detail Tiket | Ke Daftar Tiket Saya / Kembali ke Tiket Saya | `route('pengunjung.tiket.my')` |
| Detail Tiket (pending) | Bayar Sekarang | `route('pengunjung.tiket.bayar', $tiket)` |
| Halaman Pembayaran | Kembali ke Tiket Saya | `route('pengunjung.tiket.my')` |
| Halaman Pembayaran (Midtrans error) | Lihat Tiket, Kembali ke Tiket Saya | `route('pengunjung.tiket.show', $tiket)`, `route('pengunjung.tiket.my')` |

### Admin

| Halaman | Tombol/Link | Route |
|---------|-------------|--------|
| Dashboard Admin | Scan QR Tiket | `route('admin.validasi.index')` |
| Dashboard Admin | Lihat Laporan | `route('admin.laporan')` |
| Validasi Detail | Kembali | `route('admin.validasi.index')` |

### Pengelola BUMDes

| Halaman | Tombol/Link | Route |
|---------|-------------|--------|
| Dashboard Pengelola | Lihat Laporan Lengkap | `route('pengelola.laporan')` |

### Layout (navbar & dropdown)

| Teks | Route |
|------|--------|
| Beranda | `route('home')` |
| Pesan Tiket | `route('pengunjung.dashboard')` |
| Tiket Saya | `route('pengunjung.tiket.my')` |
| Dashboard (admin/pengelola) | `route('admin.dashboard')` / `route('pengelola.dashboard')` |
| Scan QR | `route('admin.validasi.index')` |
| Laporan | `route('admin.laporan')` / `route('pengelola.laporan')` |
| Dashboard (dropdown user) | `route('dashboard')` |

---

## 3. Cara Mengubah Teks Tampilan (contoh: "Ke Dashboard")

Teks tampilan (label tombol, link, judul) ada di **file Blade** di `resources/views/`. Ubah string di dalam tag HTML (mis. di dalam `<a>...</a>` atau `<button>...</button>`).

### Contoh: Mengubah "Ke Dashboard"

| Teks saat ini | File | Lokasi (kurang lebih) |
|---------------|------|------------------------|
| **Ke Dashboard** | `resources/views/home.blade.php` | Di dalam `<a href="{{ route('dashboard') }}" ...>Ke Dashboard</a>`. Ganti `Ke Dashboard` dengan teks yang diinginkan, mis. "Ke Beranda Saya" atau "Dashboard Saya". |

**Langkah:**

1. Buka `resources/views/home.blade.php`.
2. Cari teks `Ke Dashboard`.
3. Ganti menjadi teks baru, contoh:
   ```blade
   <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Dashboard Saya</a>
   ```
4. Simpan. Perubahan langsung terlihat setelah refresh (tanpa perlu perintah lain).

### Daftar singkat file untuk teks umum

| Teks yang ingin diubah | File |
|------------------------|------|
| Ke Dashboard (beranda, user sudah login) | `resources/views/home.blade.php` |
| Login, Daftar, Daftar & Pesan Tiket | `resources/views/home.blade.php` |
| Beranda, Pesan Tiket, Tiket Saya, Dashboard, Scan QR, Laporan | `resources/views/layouts/app.blade.php` (navbar) |
| Dashboard (dropdown menu user) | `resources/views/layouts/app.blade.php` |
| Ke Daftar Tiket Saya / Kembali ke Tiket Saya | `resources/views/pengunjung/tiket-show.blade.php` |
| Kembali ke Tiket Saya (halaman bayar) | `resources/views/pengunjung/tiket-bayar.blade.php` |
| Batal (form pesan tiket) | `resources/views/pengunjung/tiket-create.blade.php` |
| Kembali (validasi admin) | `resources/views/admin/validasi-detail.blade.php` |

**Penting:** Hanya ubah **teks yang terlihat** (di antara tag HTML). Jangan ubah `route(...)` atau `href` jika Anda tidak ingin mengubah halaman tujuannya.

---

## 4. Mengubah Tujuan Redirect (Route)

Jika Anda ingin **tombol/link mengarah ke halaman lain**:

1. Cari file Blade yang memuat tombol/link tersebut (lihat tabel di atas).
2. Ganti **nama route** di `route('nama.route')` dengan route yang benar. Daftar route: jalankan `php artisan route:list` di terminal, atau lihat `routes/web.php`.
3. Simpan.

Contoh: agar "Ke Dashboard" di home mengarah ke **Tiket Saya** untuk role pengunjung, Anda perlu logika (mis. cek auth dan role) atau pisah tampilan; secara default satu tombol satu route. Untuk satu tombol ke satu halaman, cukup ganti menjadi mis. `route('pengunjung.tiket.my')` dan ganti teksnya menjadi "Ke Tiket Saya".

---

*Dokumen referensi SI-ASIH — route redirect dan teks tampilan.*
