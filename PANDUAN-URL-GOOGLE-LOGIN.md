# Penjelasan URL untuk Login Google (Laragon 6)

## Penting: Google tidak menerima domain .test

Google OAuth **hanya** menerima:
- **localhost** (untuk development), atau
- Domain dengan **TLD publik** seperti .com, .org, .net (untuk production).

Domain seperti **siasih.test**, **.local**, **.localhost** **tidak boleh** dipakai sebagai Redirect URI. Kalau dipakai, muncul error: *"must end with a public top-level domain"* atau *"must use a valid top private domain"*.

Oleh karena itu untuk development pakai **http://localhost/siasih/public** (bukan siasih.test) saat memakai Login Google.

---

## 1. Apa itu URL dalam konteks ini?

**URL** = alamat yang Anda ketik di browser untuk membuka situs.

- Untuk **Login Google** di development: pakai **http://localhost/siasih/public** (sudah diatur di .env).
- Untuk browsing biasa (tanpa tes Google Login): tetap boleh pakai **http://siasih.test**.

---

## 2. Di mana URL diatur?

Di file **`.env`** (di root project):

```env
APP_URL=http://localhost/siasih/public
```

- **APP_URL** = alamat dasar situs Anda, **tanpa** garis miring di akhir.
- Untuk development dengan Login Google sudah dipakai **localhost** supaya diterima Google.

---

## 3. Apa hubungannya dengan Google Login?

Saat user klik "Login dengan Google":

1. User diarahkan ke Google.
2. Setelah login, Google mengembalikan user ke **alamat callback** di situs Anda.
3. Alamat callback itu **harus persis** sama dengan yang didaftarkan di Google Cloud Console.

Rumusnya:

**Alamat callback = APP_URL + `/auth/google/callback`**

Dengan `APP_URL=http://localhost/siasih/public`:

- Alamat callback = **http://localhost/siasih/public/auth/google/callback**

---

## 4. Apa yang harus Anda isi di Google Cloud Console?

1. Buka [Google Cloud Console](https://console.cloud.google.com/) → **APIs & Services** → **Credentials**.
2. Buat atau edit **OAuth 2.0 Client ID** (tipe **Web application**).
3. Di bagian **Authorized redirect URIs** klik **ADD URI**.
4. Isi **persis** alamat ini (untuk development Laragon):

   **`http://localhost/siasih/public/auth/google/callback`**

5. Simpan (Create / Save).

Jangan pakai **http://siasih.test/...** — Google akan menolak domain .test.

---

## 5. Ringkasan untuk Laragon 6

- **APP_URL** di `.env`: **http://localhost/siasih/public** (agar Google menerima).
- Di Google Console → **Authorized redirect URIs** isi:  
  **http://localhost/siasih/public/auth/google/callback**
- Saat **mengetes Login Google**, buka situs lewat: **http://localhost/siasih/public** (bukan siasih.test).
- Isi **GOOGLE_CLIENT_ID** dan **GOOGLE_CLIENT_SECRET** di `.env` dari Google Console.

Nanti di production (domain .com/.org), Anda tambahkan redirect URI production di Google dan sesuaikan APP_URL.
