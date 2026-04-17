# Troubleshooting QR Code Tidak Muncul

Jika QR code tidak muncul setelah membeli tiket, ikuti langkah berikut:

## 1. Clear Cache Laravel

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 2. Pastikan Package Terinstall

```bash
composer install
# atau
composer update
```

Package yang diperlukan:
- `bacon/bacon-qr-code` (otomatis terinstall dengan simplesoftwareio/simple-qrcode)

## 3. Cek Error di Browser

1. Buka halaman tiket yang sudah dibayar
2. Klik kanan pada area QR → **Inspect Element**
3. Lihat tab **Console** dan **Network**
4. Cek apakah ada error saat load gambar QR
5. Di tab **Network**, cari request ke `/pengunjung/tiket/{id}/qrcode` dan lihat status code:
   - **200**: QR berhasil di-generate
   - **403**: Akses ditolak (pastikan login sebagai pemilik tiket)
   - **500**: Error di server (cek log Laravel)

## 4. Cek Log Laravel

```bash
tail -f storage/logs/laravel.log
```

Cari error terkait:
- `BaconQrCode failed`
- `QR API fallback failed`
- `Class not found`

## 5. Test Route QR Langsung

Buka di browser (setelah login sebagai pemilik tiket):
```
http://localhost:8000/pengunjung/tiket/{ID_TIKET}/qrcode
```

Ganti `{ID_TIKET}` dengan ID tiket yang sudah dibayar. Seharusnya muncul gambar QR atau SVG.

## 6. Fallback Otomatis

Sistem sudah memiliki **3 level fallback**:

1. **BaconQrCode (SVG)** - Generate di server (prioritas)
2. **API eksternal (qrserver.com)** - Jika BaconQrCode gagal
3. **Placeholder SVG** - Jika semua gagal (menampilkan kode tiket sebagai text)

Jika QR masih tidak muncul, kemungkinan:
- Browser memblokir gambar dari URL tertentu
- Network/firewall memblokir akses ke api.qrserver.com
- Ada error JavaScript yang memblokir render

## 7. Solusi Manual (Jika Semua Gagal)

Jika semua cara di atas tidak berhasil, Anda bisa menggunakan QR generator eksternal:

1. Salin **kode tiket** dari halaman tiket
2. Buka [QR Code Generator](https://www.qr-code-generator.com/)
3. Paste kode tiket
4. Generate dan download QR
5. Tunjukkan QR tersebut di lokasi wisata

---

**Catatan:** QR code berisi informasi lengkap sesuai wisata:
- **Curug Cibarebeuy**: Kode + Jumlah + Tanggal + Camping/Tidak Camping
- **Puncak Pasir Ipis**: Kode + Jumlah + Tanggal
- **Bukit Panineungan**: Kode + Jumlah + Tanggal
