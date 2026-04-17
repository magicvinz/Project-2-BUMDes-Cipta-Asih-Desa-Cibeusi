# Struktur Proyek SI-ASIH — Penjelasan Lengkap untuk Pemula

Dokumen ini menjelaskan **untuk apa setiap file ada**, **mengapa file tersebut dibutuhkan**, dan **apa yang terjadi jika tidak ada**. Ditujukan untuk mahasiswa yang baru belajar Laravel atau web development.

---

## Pendahuluan: Analogi Sederhana

Bayangkan proyek SI-ASIH seperti **restoran**:

| Konsep | Dalam Restoran | Dalam Proyek SI-ASIH |
|--------|----------------|----------------------|
| **Route** | Daftar menu (apa yang bisa dipesan) | URL yang bisa diakses (/login, /pengunjung/tiket/saya, dll.) |
| **Controller** | Koki (yang mengolah pesanan) | Kode yang memproses request dan menyiapkan respons |
| **Model** | Buku resep & gudang (data bahan) | Cara akses database (User, Tiket, Wisata) |
| **View** | Piring & penyajian (tampilan ke pelanggan) | File HTML/Blade yang tampil di browser |
| **Middleware** | Pramusaji (cek apakah pelanggan punya reservasi) | Kode yang mengecek login, role, CSRF sebelum request sampai ke controller |
| **Config** | Resep standar restoran (porsi, bumbu) | Pengaturan aplikasi (nama app, koneksi DB, API key) |

---

## 1. File di Root (Akar Proyek)

### artisan

**Apa ini?** File PHP yang menjadi pintu masuk semua perintah baris perintah Laravel.

**Untuk apa?** Memungkinkan Anda menjalankan `php artisan migrate`, `php artisan serve`, `php artisan config:clear`, dll.

**Mengapa ada?** Tanpa file ini, Anda tidak bisa memakai perintah Artisan. Laravel butuh satu titik masuk untuk menjalankan tugas seperti migrasi database, generate key, atau menjalankan server development.

**Analogi:** Seperti tombol "Panel Kontrol" restoran — dari situ Anda mengatur database, cache, dan menjalankan server.

---

### composer.json

**Apa ini?** File yang mendaftarkan **semua paket PHP** yang dibutuhkan proyek.

**Untuk apa?** Mendefinisikan dependensi (Laravel, Midtrans, Socialite, Simple QR Code, dll.) dan autoload untuk class di folder `App\`.

**Mengapa ada?** Tanpa `composer.json`, proyek tidak tahu paket mana yang harus di-install. Saat Anda jalankan `composer install`, Composer membaca file ini dan mengunduh semua paket ke folder `vendor/`.

**Isi penting:**
- `require`: paket yang wajib (Laravel, Midtrans, dll.)
- `autoload`: mapping namespace `App\` ke folder `app/`
- `scripts`: perintah yang dijalankan otomatis (mis. `key:generate` setelah install)

---

### composer.lock

**Apa ini?** File yang mengunci **versi pasti** setiap paket yang terinstall.

**Untuk apa?** Memastikan setiap orang yang clone proyek mendapat versi paket yang sama saat menjalankan `composer install`.

**Mengapa ada?** Tanpa lock, `composer update` bisa mengunduh versi lebih baru yang mungkin tidak kompatibel. Lock membuat hasil install di mana saja menjadi konsisten.

---

### .env

**Apa ini?** File berisi **variabel lingkungan** — pengaturan yang spesifik per komputer/server (dan bersifat rahasia).

**Untuk apa?** Menyimpan: `APP_URL`, `DB_DATABASE`, `DB_PASSWORD`, `GOOGLE_CLIENT_ID`, `MIDTRANS_SERVER_KEY`, dll. Aplikasi membaca nilai ini lewat `env('NAMA_VARIABEL')`.

**Mengapa ada?** Supaya kredensial (password, API key) tidak ikut masuk ke Git. Setiap developer punya `.env` sendiri; file ini tidak di-commit.

**Penting:** Jangan share isi `.env` (terutama password dan secret key) ke publik.

---

### .env.example

**Apa ini?** Template `.env` tanpa nilai rahasia.

**Untuk apa?** Panduan bagi developer baru: variabel apa saja yang harus diisi. Biasanya di-copy menjadi `.env` lalu diisi nilai asli.

**Mengapa ada?** Di-commit ke Git supaya semua tahu struktur env yang dibutuhkan, tanpa membocorkan rahasia.

---

### .gitignore

**Apa ini?** Daftar file/folder yang **tidak** ikut di-commit ke Git.

**Untuk apa?** Mencegah file seperti `vendor/`, `.env`, `node_modules/`, `storage/logs/*` masuk ke repository.

**Mengapa ada?** File tersebut di-generate atau bersifat rahasia; tidak perlu disimpan di Git.

---

### package.json

**Apa ini?** Daftar **dependensi JavaScript** dan script untuk front-end (mirip composer.json tapi untuk Node.js).

**Untuk apa?** Mendefinisikan Vite, Laravel Vite plugin, Axios; script seperti `npm run dev` dan `npm run build`.

**Mengapa ada?** Jika proyek memakai aset JS/CSS yang perlu di-build (Vite), file ini diperlukan. `npm install` membaca file ini.

---

### vite.config.js

**Apa ini?** Konfigurasi **Vite** — alat untuk membundel dan mem-build file JavaScript/CSS.

**Untuk apa?** Menentukan entry point (mis. resources/js/app.js), output, dan integrasi dengan Laravel.

**Mengapa ada?** Supaya `npm run dev` dan `npm run build` tahu file mana yang harus diproses dan kemana hasilnya ditulis.

---

### phpunit.xml

**Apa ini?** Konfigurasi **PHPUnit** — framework untuk tes otomatis.

**Untuk apa?** Menentukan environment tes, koneksi database tes, dan cara menjalankan `php artisan test` atau `./vendor/bin/phpunit`.

**Mengapa ada?** Supaya tes bisa dijalankan dengan pengaturan yang benar (mis. pakai database tes, bukan production).

---

## 2. Folder app/ — Otak Aplikasi

Folder ini berisi **logika bisnis** Anda. Semua kode yang menjalankan fitur aplikasi ada di sini.

### app/Http/Controllers/

**Apa ini?** Folder berisi **Controller** — class yang menangani request HTTP dan mengembalikan response (view, redirect, JSON).

**Untuk apa?** Setiap controller punya method (function) yang dihubungkan ke URL. Contoh: user akses `/login` → `AuthController::showLoginForm()` dipanggil → mengembalikan view form login.

**Mengapa ada?** Tanpa controller, request tidak punya "otak" yang memproses. Controller adalah jembatan antara URL (route) dan tampilan (view) atau aksi (simpan ke DB, redirect).

#### AuthController.php

**Untuk apa?** Menangani login (email+password), register, logout, dan Login dengan Google (redirect ke Google, terima callback).

**Mengapa ada?** Semua yang berkaitan dengan autentikasi dipusatkan di sini supaya tidak berantakan.

#### DashboardController.php

**Untuk apa?** Halaman dashboard default setelah login. Biasanya redirect ke dashboard sesuai role (pengunjung → /pengunjung, admin → /admin, pengelola → /pengelola).

**Mengapa ada?** Satu titik masuk "Ke Dashboard" yang bisa mengarahkan ke tempat yang berbeda tergantung role.

#### MidtransNotificationController.php

**Untuk apa?** Menerima **webhook** dari Midtrans saat pembayaran selesai. Midtrans mengirim POST ke `/payment/notification`; controller ini verifikasi signature dan update status tiket (paid/cancelled).

**Mengapa ada?** Pembayaran diproses di server Midtrans; aplikasi kita harus punya endpoint untuk menerima kabar "pembayaran lunas" dan mengupdate database.

#### Admin/AdminDashboardController.php

**Untuk apa?** Menampilkan angka: tiket hari ini, tiket bulan ini, pendapatan bulan ini — **hanya untuk wisata yang dikelola admin tersebut**.

**Mengapa ada?** Admin butuh ringkasan cepat tanpa masuk ke laporan lengkap.

#### Admin/LaporanAdminController.php

**Untuk apa?** Laporan penjualan **per wisata** dengan filter periode (harian/mingguan/bulanan) dan tanggal.

**Mengapa ada?** Admin butuh melihat berapa tiket terjual dan pendapatan untuk wisatanya saja.

#### Admin/ValidasiTiketController.php

**Untuk apa?** Halaman scan QR & input kode; mencari tiket; menampilkan detail; dan tombol "Validasi" untuk mengubah status tiket menjadi "used".

**Mengapa ada?** Di lokasi wisata, admin perlu memvalidasi tiket pengunjung — ini satu-satunya tempat yang mengubah status tiket dari paid ke used.

#### Pengelola/PengelolaDashboardController.php

**Untuk apa?** Dashboard gabungan (total tiket & pendapatan semua wisata) dan laporan gabungan dengan filter periode.

**Mengapa ada?** Pengelola BUMDes mengawasi semua wisata; butuh angka agregat, bukan per wisata.

#### Pengunjung/TiketController.php

**Untuk apa?** Semua fitur pengunjung: daftar wisata, form pesan tiket, simpan tiket, daftar tiket saya, detail tiket, halaman bayar (Midtrans), generate QR, simulasi bayar.

**Mengapa ada?** Semua aksi pengunjung yang berkaitan dengan tiket dipusatkan di sini.

---

### app/Models/

**Apa ini?** Class yang merepresentasikan **tabel database** dan relasi antar tabel. Pakai **Eloquent ORM** Laravel.

**Untuk apa?** Memudahkan baca/tulis database tanpa menulis SQL mentah. Contoh: `User::find(1)`, `Tiket::where('user_id', 5)->get()`.

**Mengapa ada?** Tanpa model, Anda harus menulis query SQL di setiap controller. Model mengemas tabel menjadi object yang mudah dipakai.

#### User.php

**Untuk apa?** Mewakili tabel `users`. Punya relasi ke `wisata` (untuk admin) dan `tiket`. Helper: `isPengunjung()`, `isAdmin()`, `isPengelolaBumdes()`.

**Mengapa ada?** Setiap pemakai sistem adalah User; kita butuh satu tempat untuk mendefinisikan struktur dan perilaku user.

#### Wisata.php

**Untuk apa?** Mewakili tabel `wisata` (nama, slug, harga_tiket, deskripsi). Relasi ke tiket dan admins.

**Mengapa ada?** Wisata adalah entitas inti — tempat tujuan tiket.

#### Tiket.php

**Untuk apa?** Mewakili tabel `tiket` (kode, jumlah, total_harga, status, tanggal_berkunjung, camping, dll.). Relasi ke user dan wisata. Scope: `paid()`, `used()`. Helper: `qr_content` untuk isi QR.

**Mengapa ada?** Tiket adalah "produk" yang dibeli pengunjung; semua logic tiket (status, QR, validasi) bersandar pada model ini.

---

### app/Http/Middleware/

**Apa ini?** Kode yang dijalankan **sebelum** request sampai ke controller. Seperti "pemeriksa" di pintu masuk.

**Untuk apa?** Mengecek: apakah user sudah login? Apakah punya role yang benar? Apakah token CSRF valid?

**Mengapa ada?** Tanpa middleware, siapa saja bisa akses halaman admin atau mengirim form palsu. Middleware menjaga keamanan dan alur aplikasi.

#### Authenticate.php

**Untuk apa?** Jika user belum login dan mencoba akses halaman yang butuh login → redirect ke halaman login.

**Mengapa ada?** Halaman seperti "Tiket Saya" hanya untuk yang login.

#### CheckRole.php

**Untuk apa?** Memastikan user punya role yang sesuai. Contoh: route `/admin/*` hanya boleh diakses role admin.

**Mengapa ada?** Pengunjung tidak boleh akses halaman admin; admin tidak boleh akses halaman pengelola (jika dibatasi).

#### VerifyCsrfToken.php

**Untuk apa?** Memverifikasi token CSRF pada form POST. Mencegah serangan "Cross-Site Request Forgery" (form dikirim dari situs lain tanpa sepengetahuan user).

**Mengapa ada?** Keamanan. Route seperti `payment/notification` (webhook Midtrans) dikecualikan karena request dari server Midtrans, bukan dari form kita.

#### EncryptCookies, TrimStrings, dll.

**Untuk apa?** Enkripsi cookie, trim spasi di input, cek host, dll.

**Mengapa ada?** Bagian standar keamanan dan kebersihan data dalam Laravel.

---

### app/Http/Kernel.php

**Apa ini?** File yang **mendaftarkan** semua middleware: mana yang global, mana yang dipakai per-route.

**Untuk apa?** Menghubungkan nama middleware (mis. `auth`, `role`) dengan class-nya, dan menentukan urutan eksekusi.

**Mengapa ada?** Tanpa Kernel, Laravel tidak tahu middleware mana yang dipakai untuk route mana.

---

### app/Services/MidtransService.php

**Apa ini?** Class layanan yang mengurus integrasi **Midtrans** (pembayaran online).

**Untuk apa?** Membuat transaksi Snap (token pembayaran), mengatur expiry, callbacks; memverifikasi notifikasi dari Midtrans dan update status tiket.

**Mengapa ada?** Logic Midtrans dipisah dari controller supaya controller tidak penuh kode Midtrans. Jika Midtrans diganti provider lain, cukup ubah Service ini.

---

### app/Exceptions/Handler.php

**Apa ini?** Menangani **exception** (error) yang tidak tertangkap di tempat lain.

**Untuk apa?** Memutuskan: tampilkan halaman error 404/500, atau log saja, atau kirim ke layanan monitoring.

**Mengapa ada?** Supaya user tidak melihat stack trace mentah saat error; dan developer bisa mencatat error di log.

---

### app/Providers/

**Apa ini?** Service Provider — class yang "menyiapkan" layanan aplikasi saat Laravel startup.

**Untuk apa?** AppServiceProvider: registrasi umum. AuthServiceProvider: policy otorisasi. RouteServiceProvider: konfigurasi route. EventServiceProvider: listener event. BroadcastServiceProvider: channel broadcast.

**Mengapa ada?** Laravel memakai sistem provider untuk memuat konfigurasi dan binding; ini arsitektur standar Laravel.

---

### app/Console/Commands/SetupSiasihDatabase.php

**Apa ini?** Perintah Artisan kustom (mis. `php artisan siasih:setup-db`).

**Untuk apa?** Menjalankan setup database khusus SIASIH (migrasi, seeding) dalam satu perintah.

**Mengapa ada?** Mempermudah developer baru: satu perintah saja untuk siapkan database.

---

## 3. Folder bootstrap/

**Apa ini?** Folder yang dipakai Laravel untuk **memulai** aplikasi.

**Untuk apa?** `app.php` membuat instance aplikasi; folder `cache/` menyimpan file cache (packages, services) agar startup lebih cepat.

**Mengapa ada?** Semua request masuk lewat `public/index.php` yang memuat bootstrap; tanpa ini Laravel tidak bisa jalan.

---

## 4. Folder config/

**Apa ini?** Berisi **file konfigurasi** — pengaturan aplikasi yang dibaca dari `.env` atau nilai default.

**Untuk apa?** `config('app.name')`, `config('database.connections.mysql')`, `config('services.midtrans')` — semua berasal dari sini.

**Mengapa ada?** Memusatkan pengaturan supaya mudah diubah tanpa menyentuh kode. Nilai rahasia di `.env`; config hanya membaca dan memberikan default.

**File penting:**
- `app.php`: nama app, URL, locale, timezone
- `database.php`: koneksi MySQL
- `services.php`: Midtrans, Google OAuth
- `auth.php`: pengaturan login
- `session.php`: session
- `parkir.php`: tarif parkir (custom untuk SIASIH)

---

## 5. Folder database/

**Apa ini?** Berisi **migrasi** (skema database), **seeder** (data awal), dan **factory** (pembuat data dummy untuk tes).

### Migrasi

**Apa ini?** File PHP yang mendefinisikan struktur tabel: buat tabel, tambah kolom, ubah tipe.

**Untuk apa?** Supaya struktur database bisa "versi" dan dijalankan ulang di komputer lain dengan `php artisan migrate`.

**Mengapa ada?** Tanpa migrasi, Anda harus buat tabel manual atau kirim file SQL; migrasi membuat proses ini otomatis dan terdokumentasi.

### Seeder

**Apa ini?** Kode yang mengisi data awal: user contoh, wisata (Curug, Puncak, Bukit), dll.

**Untuk apa?** Setelah migrasi, database kosong. Seeder mengisi data minimal agar aplikasi bisa dicoba.

**Mengapa ada?** Developer baru bisa langsung jalankan `php artisan db:seed` dan punya data untuk login dan uji fitur.

### Factory

**Apa ini?** Pembuat data dummy (faker) untuk model, dipakai saat testing atau seeding.

**Untuk apa?** Membuat banyak User/Tiket palsu dengan cepat tanpa mengetik manual.

**Mengapa ada?** Berguna untuk tes dan pengembangan.

---

## 6. Folder public/

**Apa ini?** **Web root** — satu-satunya folder yang di-expose ke web server. Semua request HTTP masuk lewat sini.

**Untuk apa?** `index.php` adalah "pintu depan": menerima request, memuat Laravel, dan meneruskan ke router. `.htaccess` mengarahkan semua URL ke `index.php`.

**Mengapa ada?** Keamanan. Kode di `app/`, `config/`, `.env` tidak boleh diakses langsung dari browser. Hanya `public/` yang boleh; isi folder lain tetap di server.

---

## 7. Folder resources/

**Apa ini?** Berisi **view** (tampilan) dan **aset mentah** (JS, CSS) sebelum di-build.

### resources/views/

**Apa ini?** File **Blade** — template HTML dengan sintaks khusus (`@if`, `@foreach`, `{{ $variabel }}`).

**Untuk apa?** Mendefinisikan tampilan setiap halaman. Controller memanggil `return view('nama.view', $data)`; Laravel merender Blade dan mengirim HTML ke browser.

**Mengapa ada?** Memisahkan tampilan dari logika. Developer front-end bisa ubah HTML/CSS tanpa menyentuh controller.

**Struktur:**
- `layouts/app.blade.php`: layout utama (navbar, footer, tempat konten)
- `home.blade.php`: beranda
- `auth/`: login, register
- `admin/`: dashboard, validasi, laporan
- `pengelola/`: dashboard, laporan
- `pengunjung/`: wisata, tiket create/show/bayar, my-tickets

### resources/js/

**Apa ini?** File JavaScript mentah (app.js, bootstrap.js).

**Untuk apa?** Di-build oleh Vite menjadi satu file yang di-load di view. Bisa berisi Axios, kode polling real-time, dll.

**Mengapa ada?** Jika pakai JS (bukan hanya Bootstrap), butuh entry point dan build process.

---

## 8. Folder routes/

**Apa ini?** Tempat mendefinisikan **URL** dan **apa yang dijalankan** saat URL itu diakses.

**Untuk apa?** `Route::get('/login', ...)` artinya: bila ada request GET ke `/login`, jalankan controller/method tertentu. Menghubungkan URL dengan kode.

**Mengapa ada?** Tanpa route, Laravel tidak tahu URL mana mengarah ke controller mana. Route adalah "peta" aplikasi.

**web.php** berisi hampir semua route SIASIH: home, login, register, Google OAuth, payment notification, dan route grup untuk pengunjung, admin, pengelola.

---

## 9. Folder storage/

**Apa ini?** Penyimpanan file yang **dihasilkan** aplikasi: log, session, cache, view terkompilasi, file upload.

**Untuk apa?** Log error ditulis ke `storage/logs/`. Session disimpan di `storage/framework/sessions/`. Cache config di `storage/framework/cache/`. View Blade yang sudah dikompilasi di `storage/framework/views/`.

**Mengapa ada?** File-file ini dibuat otomatis; tidak di-commit ke Git. Harus ada folder yang writable supaya Laravel bisa menulis.

---

## 10. Folder vendor/

**Apa ini?** Berisi **semua paket Composer** yang di-install (Laravel framework, Midtrans, Socialite, dll.).

**Untuk apa?** Kode library yang dipakai proyek. Anda memanggil class seperti `\Midtrans\Snap::getSnapToken()` — class itu ada di vendor.

**Mengapa ada?** Tidak perlu menulis ulang semua fitur; pakai library yang sudah ada. `composer install` mengisi folder ini.

**Penting:** Jangan edit file di vendor secara manual. Upgrade lewat `composer update` atau `composer require`.

---

## 11. Folder docs/

**Apa ini?** Dokumentasi proyek (file .md).

**Untuk apa?** Menjelaskan proses bisnis, struktur proyek, setup database, cara pakai Google Login, ngrok+Midtrans, troubleshooting, dll.

**Mengapa ada?** Supaya developer (termasuk Anda di masa depan) bisa paham proyek tanpa membongkar kode satu per satu.

---

## Ringkasan: Apa yang Terjadi Tanpa Masing-Masing?

| Tanpa... | Akibat |
|----------|--------|
| **Controller** | Request tidak punya "otak" — tidak ada yang memproses dan mengembalikan response. |
| **Model** | Harus menulis SQL mentah di mana-mana; kode berantakan dan rentan error. |
| **View** | Tidak ada tampilan; controller hanya bisa return teks atau JSON mentah. |
| **Route** | Laravel tidak tahu URL mana mengarah ke mana. |
| **Middleware** | Siapa saja bisa akses halaman admin; form rentan CSRF. |
| **Config** | Pengaturan (DB, API key) terpaksa hardcode di kode — tidak aman dan sulit diubah. |
| **.env** | Kredensial (password, secret) bisa ikut ke Git dan bocor. |
| **Migration** | Harus buat tabel manual; sulit sinkronkan struktur DB antar developer. |
| **vendor/** | Tidak ada Laravel, Midtrans, dll. — aplikasi tidak bisa jalan. |

---

*Dokumen struktur proyek SI-ASIH — penjelasan lengkap untuk pemula. Jika ada file yang masih belum jelas, tanyakan dan akan ditambahkan penjelasannya.*
