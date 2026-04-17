-- ============================================================
-- SI-ASIH - Skema database (MySQL/MariaDB)
-- Pakai ini jika Laravel migrate bermasalah.
-- Syarat: database sudah ada, tabel users sudah ada (dari php artisan migrate).
-- Jalankan: mysql -u root -p nama_database < database/siasih_schema.sql
-- Atau lebih disarankan: php artisan migrate:fresh --seed
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Hapus tiket dulu (bergantung wisata & users)
DROP TABLE IF EXISTS `tiket`;

-- 2. Hapus wisata (akan dibuat lagi)
DROP TABLE IF EXISTS `wisata`;

-- 3. Buat tabel wisata
CREATE TABLE `wisata` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `harga_tiket` decimal(12,0) NOT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wisata_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tambah kolom ke users (lewati jika sudah ada)
-- Jika error "Duplicate column", artinya kolom sudah ada, abaikan.
ALTER TABLE `users` ADD COLUMN `role` varchar(20) NOT NULL DEFAULT 'pengunjung' AFTER `email`;
ALTER TABLE `users` ADD COLUMN `wisata_id` bigint unsigned DEFAULT NULL AFTER `role`;
ALTER TABLE `users` ADD CONSTRAINT `users_wisata_id_foreign` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE SET NULL;

-- 5. Buat tabel tiket
CREATE TABLE `tiket` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `wisata_id` bigint unsigned NOT NULL,
  `kode_tiket` varchar(30) NOT NULL,
  `jumlah` int NOT NULL DEFAULT 1,
  `total_harga` decimal(12,0) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `tanggal_berkunjung` date NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tiket_kode_tiket_unique` (`kode_tiket`),
  KEY `tiket_wisata_id_status_index` (`wisata_id`,`status`),
  KEY `tiket_user_id_status_index` (`user_id`,`status`),
  KEY `tiket_tanggal_berkunjung_index` (`tanggal_berkunjung`),
  CONSTRAINT `tiket_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tiket_wisata_id_foreign` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- 6. Data wisata
INSERT INTO `wisata` (`nama`, `slug`, `harga_tiket`, `deskripsi`, `created_at`, `updated_at`) VALUES
('Curug Cibarebeuy', 'curug-cibarebeuy', 15000, 'Air terjun alami dengan pemandangan hijau yang menyejukkan.', NOW(), NOW()),
('Puncak Pasir Ipis', 'puncak-pasir-ipis', 20000, 'Puncak dengan panorama alam yang memukau.', NOW(), NOW()),
('Bukit Panineungan', 'bukit-panineungan', 25000, 'Bukit dengan pemandangan sunrise dan sunset yang indah.', NOW(), NOW());

-- 7. Data user contoh (password: password) - hanya jika tabel users kosong
-- Jika users sudah berisi data, hapus atau sesuaikan INSERT berikut.
INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `role`, `wisata_id`, `remember_token`, `created_at`, `updated_at`) VALUES
('Pengelola BUMDes', 'pengelola@siasih.com', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengelola_bumdes', NULL, NULL, NOW(), NOW()),
('Admin Curug Cibarebeuy', 'admin.curug@siasih.com', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NOW(), NOW()),
('Admin Puncak Pasir Ipis', 'admin.puncak@siasih.com', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 2, NULL, NOW(), NOW()),
('Admin Bukit Panineungan', 'admin.bukit@siasih.com', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 3, NULL, NOW(), NOW()),
('Pengunjung Demo', 'pengunjung@siasih.com', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengunjung', NULL, NULL, NOW(), NOW());
