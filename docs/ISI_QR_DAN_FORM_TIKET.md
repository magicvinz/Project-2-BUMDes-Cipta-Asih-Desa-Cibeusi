# Isi QR Kode & Form Pemesanan per Wisata

## Isi QR Kode Tiket

Setiap wisata memiliki isi QR yang berbeda (tetap dalam format baris, baris pertama = kode tiket untuk validasi).

### Curug Cibarebeuy
- Baris 1: **Kode unik tiket** (contoh: SI-ABCD1234)
- Baris 2: **Jumlah pengunjung**
- Baris 3: **Tanggal kunjungan** (YYYY-MM-DD)
- Baris 4: **Keterangan** → `Camping` atau `Tidak Camping`

### Puncak Pasir Ipis
- Baris 1: **Kode unik tiket**
- Baris 2: **Jumlah pengunjung**
- Baris 3: **Tanggal kunjungan**

### Bukit Panineungan
- Baris 1: **Kode unik tiket**
- Baris 2: **Jumlah pengunjung**
- Baris 3: **Tanggal kunjungan**

Saat admin scan QR (kamera atau input manual), sistem mengambil **baris pertama** sebagai kode tiket untuk pencarian.

---

## Form Pemesanan Tiket

Form pemesanan **sama** untuk semua wisata, dengan field tambahan hanya untuk Curug:

| Field | Curug Cibarebeuy | Puncak Pasir Ipis | Bukit Panineungan |
|-------|------------------|-------------------|-------------------|
| Jumlah tiket | ✓ | ✓ | ✓ |
| Tanggal berkunjung | ✓ | ✓ | ✓ |
| Keterangan (Camping / Tidak Camping) | ✓ wajib | - | - |

- **Curug Cibarebeuy:** wajib pilih **Camping** atau **Tidak Camping**.
- **Puncak Pasir Ipis** dan **Bukit Panineungan:** hanya jumlah dan tanggal (tidak ada field keterangan).

---

## Database

Kolom baru di tabel `tiket`: **`camping`** (nullable, isi: `Ya` / `Tidak`). Hanya diisi untuk tiket Curug Cibarebeuy.

Jalankan migrasi:
```bash
php artisan migrate
```
