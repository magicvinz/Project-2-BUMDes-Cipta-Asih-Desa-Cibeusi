# Cek redirect_uri_mismatch — Login Google

## 1. Redirect URI yang dipakai aplikasi (sudah di .env)

```
http://localhost/siasih/public/auth/google/callback
```

- Tidak ada spasi, tidak ada garis miring di akhir.
- Pakai **http** (bukan https).

---

## 2. Di Google Cloud Console

1. Buka: https://console.cloud.google.com/apis/credentials
2. Klik OAuth 2.0 Client ID Anda (tipe Web application).
3. Di **Authorized redirect URIs**:
   - Hapus semua URI yang salah (mis. yang pakai siasih.test).
   - Klik **+ ADD URI**.
   - **Copy–paste persis** (jangan ketik manual):
     ```
     http://localhost/siasih/public/auth/google/callback
     ```
   - Jangan tambah spasi atau `/` di akhir.
4. Klik **SAVE**.

---

## 3. Di komputer Anda

Jalankan sekali (di folder project, lewat terminal Laragon atau CMD):

```
php artisan config:clear
```

Lalu buka situs **hanya** lewat:

```
http://localhost/siasih/public
```

Jangan buka lewat siasih.test saat tes Login Google.

---

## 4. Coba lagi

1. Buka: http://localhost/siasih/public  
2. Klik Login → Login dengan Google  
3. Pilih akun Google  

Kalau masih error, pastikan di Google Console isi **Authorized redirect URIs** benar-benar sama persis dengan baris di atas (copy dari file ini).
