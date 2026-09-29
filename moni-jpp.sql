-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table moni-jpp.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_package_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_job_package_id_foreign` (`job_package_id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_job_package_id_foreign` FOREIGN KEY (`job_package_id`) REFERENCES `job_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.activity_logs: ~63 rows (approximately)
DELETE FROM `activity_logs`;
INSERT INTO `activity_logs` (`id`, `job_package_id`, `user_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
	(1, 8, 1, 'updated', 'Final Harga: Rp 975.868.000 → Rp 975.868.000; Owner Estimate: Rp 1.441.215.949 → Rp 1.441.215.949', '2026-09-13 21:23:13', '2026-09-13 21:23:13'),
	(2, 8, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-13 21:23:21', '2026-09-13 21:23:21'),
	(3, 8, 1, 'updated', 'ADM Keuangan: 95% → 80%; Final Harga: Rp 975.868.000 → Rp 975.868.000; Owner Estimate: Rp 1.441.215.949 → Rp 1.441.215.949', '2026-09-14 18:43:40', '2026-09-14 18:43:40'),
	(4, 8, 1, 'updated', 'ADM Keuangan: 80% → 85%', '2026-09-14 18:47:07', '2026-09-14 18:47:07'),
	(5, 8, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-14 18:47:39', '2026-09-14 18:47:39'),
	(6, 8, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-14 18:47:42', '2026-09-14 18:47:42'),
	(7, 11, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-14 18:48:05', '2026-09-14 18:48:05'),
	(8, 11, 1, 'updated', 'ADM Keuangan: 50% → 60%', '2026-09-14 18:49:40', '2026-09-14 18:49:40'),
	(9, 8, 2, 'updated', 'Fisik Pekerjaan: 85% → 86%', '2026-09-14 18:50:14', '2026-09-14 18:50:14'),
	(10, 8, 2, 'updated', 'ADM Keuangan: 85% → 86%', '2026-09-14 18:57:32', '2026-09-14 18:57:32'),
	(11, 8, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-14 21:00:34', '2026-09-14 21:00:34'),
	(12, 8, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-14 21:00:40', '2026-09-14 21:00:40'),
	(13, 8, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-14 21:00:53', '2026-09-14 21:00:53'),
	(14, 8, 1, 'updated', 'Fisik Pekerjaan: 86% → 85%; ADM Keuangan: 86% → 70%; Aktivitas Terkini diperbarui', '2026-09-14 21:05:35', '2026-09-14 21:05:35'),
	(15, 6, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-14 21:06:40', '2026-09-14 21:06:40'),
	(16, 6, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-14 21:09:24', '2026-09-14 21:09:24'),
	(17, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-14 21:10:22', '2026-09-14 21:10:22'),
	(18, 11, 1, 'document_uploaded', 'Dokumen diunggah: doc_surat_permintaan.', '2026-09-14 21:10:31', '2026-09-14 21:10:31'),
	(19, 11, 1, 'document_deleted', 'Dokumen "doc_surat_permintaan" dihapus.', '2026-09-14 21:11:16', '2026-09-14 21:11:16'),
	(23, 6, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-15 19:33:41', '2026-09-15 19:33:41'),
	(24, 6, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-15 19:35:53', '2026-09-15 19:35:53'),
	(25, 6, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-15 19:36:18', '2026-09-15 19:36:18'),
	(26, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-15 19:36:40', '2026-09-15 19:36:40'),
	(27, 8, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(28, 6, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-16 18:48:00', '2026-09-16 18:48:00'),
	(29, 6, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-16 18:48:07', '2026-09-16 18:48:07'),
	(30, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-17 18:40:58', '2026-09-17 18:40:58'),
	(31, 13, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-17 18:54:55', '2026-09-17 18:54:55'),
	(32, 13, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-17 18:54:55', '2026-09-17 18:54:55'),
	(33, 13, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-17 19:12:17', '2026-09-17 19:12:17'),
	(34, 13, 1, 'document_uploaded', 'Dokumen diunggah: doc_bak.', '2026-09-17 19:12:27', '2026-09-17 19:12:27'),
	(35, 13, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-17 19:12:58', '2026-09-17 19:12:58'),
	(36, 13, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-17 19:13:04', '2026-09-17 19:13:04'),
	(37, 13, 1, 'updated', 'ADM Keuangan: 76% → 100%', '2026-09-21 21:38:30', '2026-09-21 21:38:30'),
	(38, 6, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-21 22:25:42', '2026-09-21 22:25:42'),
	(39, 6, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-21 22:26:04', '2026-09-21 22:26:04'),
	(40, 14, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-22 01:45:21', '2026-09-22 01:45:21'),
	(41, 15, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-22 01:55:37', '2026-09-22 01:55:37'),
	(43, 15, 1, 'updated', 'Fisik Pekerjaan: 0% → 100%; RAB LP-002: 0% → 100%; PB/J LP-002: 0% → 100%; ADM Keuangan: 0% → 50%; Final Harga: - → Rp 67.500.000; Tanggal Mulai diperbarui; Tanggal Selesai diperbarui; Aktivitas Terkini diperbarui', '2026-09-22 02:11:29', '2026-09-22 02:11:29'),
	(44, 15, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-22 02:11:42', '2026-09-22 02:11:42'),
	(45, 15, 1, 'document_deleted', 'Dokumen "doc_rab" dihapus.', '2026-09-22 02:12:54', '2026-09-22 02:12:54'),
	(46, 15, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 02:14:12', '2026-09-22 02:14:12'),
	(47, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 06:13:46', '2026-09-22 06:13:46'),
	(48, 6, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 06:14:53', '2026-09-22 06:14:53'),
	(49, 14, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 08:52:18', '2026-09-22 08:52:18'),
	(50, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 08:52:48', '2026-09-22 08:52:48'),
	(51, 11, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(52, 16, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-22 09:05:40', '2026-09-22 09:05:40'),
	(53, 16, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-22 09:05:40', '2026-09-22 09:05:40'),
	(54, 16, 1, 'document_deleted', 'Dokumen "doc_rab" dihapus.', '2026-09-22 09:07:27', '2026-09-22 09:07:27'),
	(55, 16, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 09:07:32', '2026-09-22 09:07:32'),
	(56, 17, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-22 10:13:35', '2026-09-22 10:13:35'),
	(57, 17, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 10:14:50', '2026-09-22 10:14:50'),
	(58, 17, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-22 10:14:56', '2026-09-22 10:14:56'),
	(59, 18, 1, 'document_uploaded', 'Dokumen diunggah: doc_rab.', '2026-09-22 18:55:08', '2026-09-22 18:55:08'),
	(60, 18, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-22 18:55:08', '2026-09-22 18:55:08'),
	(61, 18, 1, 'updated', 'Data diperbarui (tidak ada perubahan nilai signifikan).', '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(62, 19, 1, 'created', 'Job Package baru ditambahkan.', '2026-09-23 11:50:01', '2026-09-23 11:50:01'),
	(63, 19, 1, 'updated', 'Final Harga: - → Rp 2.160.000', '2026-09-23 12:16:40', '2026-09-23 12:16:40'),
	(64, 14, 1, 'cancelled', 'Job Package dibatalkan.', '2026-09-23 20:06:23', '2026-09-23 20:06:23'),
	(65, 14, 1, 'reactivated', 'Job Package diaktifkan kembali.', '2026-09-23 20:06:29', '2026-09-23 20:06:29'),
	(66, 17, 1, 'updated', 'RAB LP-002: 0% → 50%; Tanggal Mulai diperbarui; Tanggal Selesai diperbarui', '2026-09-24 20:53:56', '2026-09-24 20:53:56'),
	(71, 19, 1, 'updated', 'Fisik Pekerjaan: 30% → 60%', '2026-09-24 21:45:55', '2026-09-24 21:45:55');

-- Dumping structure for table moni-jpp.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.cache: ~8 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:3;', 1790328027),
	('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1790328027;', 1790328027),
	('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1790328026),
	('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1790328026;', 1790328026),
	('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:3;', 1790132095),
	('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1790132095;', 1790132095),
	('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:2;', 1790128874),
	('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1790128874;', 1790128874);

-- Dumping structure for table moni-jpp.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table moni-jpp.dokumentasis
CREATE TABLE IF NOT EXISTS `dokumentasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibilitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publik',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.dokumentasis: ~1 rows (approximately)
DELETE FROM `dokumentasis`;
INSERT INTO `dokumentasis` (`id`, `judul`, `kategori`, `visibilitas`, `file_path`, `ukuran_file`, `created_at`, `updated_at`) VALUES
	(18, 'Rapat Perancangan Alat', 'Foto Kegiatan', 'publik', 'https://drive.google.com/file/d/17c1s7qvqudhRj7U1QJ9SDq9qk2b-lbrP/view?usp=drivesdk', '647.26 KB', '2026-09-22 19:27:44', '2026-09-22 19:27:44');

-- Dumping structure for table moni-jpp.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table moni-jpp.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table moni-jpp.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table moni-jpp.job_packages
CREATE TABLE IF NOT EXISTS `job_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permintaan_dari` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_surat_bak_doc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_surat_masuk` date DEFAULT NULL,
  `no_service_notifikasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_service_order` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_estimate` decimal(15,2) NOT NULL,
  `final_harga` decimal(15,2) DEFAULT NULL,
  `rab_lp002` decimal(5,2) DEFAULT '0.00',
  `pbj_lp002` decimal(5,2) DEFAULT '0.00',
  `progress_pekerjaan` decimal(5,2) DEFAULT '0.00',
  `proses_adm_keuangan` decimal(5,2) DEFAULT '0.00',
  `tanggal_mulai_pekerjaan` date DEFAULT NULL,
  `tanggal_selesai_pekerjaan` date DEFAULT NULL,
  `hasil_progres` decimal(5,2) NOT NULL DEFAULT '0.00',
  `no_po` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_items` json DEFAULT NULL,
  `latest_activity` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','batal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `visibility` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `google_drive_folder_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_rab` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_bak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_surat_permintaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_surat_izin_prinsip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_tor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_bast` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_packages_created_by_foreign` (`created_by`),
  CONSTRAINT `job_packages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.job_packages: ~10 rows (approximately)
DELETE FROM `job_packages`;
INSERT INTO `job_packages` (`id`, `job_package`, `permintaan_dari`, `no_surat_bak_doc`, `tanggal_surat_masuk`, `no_service_notifikasi`, `no_service_order`, `owner_estimate`, `final_harga`, `rab_lp002`, `pbj_lp002`, `progress_pekerjaan`, `proses_adm_keuangan`, `tanggal_mulai_pekerjaan`, `tanggal_selesai_pekerjaan`, `hasil_progres`, `no_po`, `po_items`, `latest_activity`, `keterangan`, `status`, `visibility`, `google_drive_folder_id`, `doc_rab`, `doc_bak`, `doc_surat_permintaan`, `doc_surat_izin_prinsip`, `doc_tor`, `doc_bast`, `created_by`, `created_at`, `updated_at`) VALUES
	(6, 'Permintaan Pengadaan Jasa Sewa Alat Berat Excavator Mini Periode Mar-Okt(Repeat Order) - Dept. Operasi Pabrik-2', NULL, '04547/E/PL/3210/IT/2026', '2026-03-13', '310000007620', '510000007374', 796512000.00, 784000000.00, 100.00, 100.00, 64.00, 76.00, '2026-03-01', '2026-10-31', 68.20, '5450000223', NULL, 'Tahap Pengerjaan', 'PERIODE MAR S.D OKT2026\r\n1. PO JASA EXCAVATOR: 5450000223 (Selesai GRS Termin 3 tgl 04/08/2026)\r\nTECO tgl : \r\nDokumen Masuk SC TGL 30-03-2026', 'aktif', 'public', '1uiXCv8R3Zx6X3TM-9hdWVqoNur75hT6r', 'https://drive.google.com/file/d/12cslSHjXrfBaEaRRobp1UAjBDjyTWXP7/view?usp=drivesdk', 'https://drive.google.com/file/d/1AgN7CJoH-ehJJuZg7JQ3mCwUld3AGaXC/view?usp=drivesdk', 'https://drive.google.com/file/d/1tT-8IfV0uRKuHB7-RWCgqBIw--a4BxHp/view?usp=drivesdk', 'https://drive.google.com/file/d/1jghc5SdhSFLTK_s_2F16EcijatcXewby/view?usp=drivesdk', 'https://drive.google.com/file/d/1EKRM45evu3E3A6l36N4FwZVMI8pf9lwG/view?usp=drivesdk', 'https://drive.google.com/file/d/1pSPMwH3RA2U0SujLYmod6HQomo8O6DEf/view?usp=drivesdk', 1, '2026-09-06 19:02:22', '2026-09-22 06:14:53'),
	(8, 'Jasa Pekerjaan Modifikasi & Upgrade Dozometer NPK  Dept. Rendal Har', NULL, '14686/E/TK/3310/IT/2025 026.BAK-EMG/INT.JPP/PIM/VII/2025', '2025-08-20', '310000006455', '510000006167', 1441215949.00, 975868000.00, 100.00, 100.00, 85.00, 70.00, '2025-08-18', '2025-09-30', 85.75, '5450000129', NULL, 'eksekusi', 'PERIODE OKTOBER S.D NOVEMBER 2025\r\n1. PO ExtraFooding: 5450000129 (Selesai GRS tgl 16/10/2025)\r\n2. PO Material: 5450000127 (Selesai GRS tgl 08/10/2025)\r\n3. PO JASBOR Tahap 1: 5450000128 (Selesai GRS tgl 09/10/2025)\r\n4. PO JASBOR Modif: 5450000137 (Selesai GRS tgl 14/11/2025)\r\n5. PO JASBOR Tahap 2: 5450000140 (Selesai GRS tgl 17/11/2025)\r\n6. PO ExtraFooding 2: 5450000147 (Selesai GRS tgl 25/11/2025)\r\n7. PO JASBOR Ganti Sensor: 5450000160 (Selesai GRS tgl 18/12/2025)\r\n8. PO JASBOR Pulling Cale & Cable: 5450000183 (Selesai GRS tgl 14/04/2026)\r\nTECO tgl : Menunggu Proses Pembayaran ke Vendor', 'aktif', 'public', '18Qgjm5Hv6g3W3Pt-hRGyvgMFds_9PCNQ', 'https://docs.google.com/spreadsheets/d/1t423EFoI1mBVRtXr27h76fX3RnxkbXXt/edit?usp=drivesdk&ouid=102026352254836079988&rtpof=true&sd=true', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-07 02:24:08', '2026-09-14 21:05:35'),
	(11, 'Jasa Cleaning & Painting Equipment MILS Plant NPK', NULL, '05570/E/TK/3310/IT/2026', '2026-04-01', '310000007681', '510000007407', 910800000.00, 627335090.00, 100.00, 100.00, 77.00, 60.00, '2026-04-01', '2026-06-30', 78.45, '5450000246', NULL, 'Menunggu material', NULL, 'aktif', 'public', '1ZfY1xvrNpbYpcz62K9lnu5TDjFw4C1fT', 'https://docs.google.com/spreadsheets/d/1RTNHkYADTGGQhHVyGS6d8M2CPsMNnNCA/edit?usp=drivesdk&ouid=102026352254836079988&rtpof=true&sd=true', 'https://drive.google.com/file/d/1YPlzHPO9_xCrSQ6UaNpJIDBWdrFtJcAT/view?usp=drivesdk', NULL, NULL, NULL, NULL, 1, '2026-09-13 19:10:56', '2026-09-22 09:01:14'),
	(13, 'Jasa Pembersihan Area Handling Bahan Baku & Produk di Pabrik NPK', NULL, '06964/E/TK/3310/IT/2026\r\n024.BAK/INT.JPP/PIM/V/2026', '2026-05-06', '310000007831', '510000007511', 22369000.00, 18000000.00, 100.00, 100.00, 100.00, 100.00, '2026-04-20', '2026-04-23', 100.00, '5007380167', NULL, 'Selesai', 'PERIODE 2026\r\nDokumen Masuk SC Selasa 19/05/2026\r\nPO Jasa: 5007380167 (Selesai GRS tgl 25/06/2026)\r\nTECO tgl :', 'aktif', 'public', '1GG0ccPRnZZcvk5pG4SNV9tznCoWqEMPU', 'https://drive.google.com/file/d/1znNk5bT7efscGZQnjdxhQWm9fh0D3VO2/view?usp=drivesdk', 'https://drive.google.com/file/d/1iDTkW9dSSsyEfr2NK6C1GBPLARXCbm-f/view?usp=drivesdk', NULL, NULL, NULL, NULL, 1, '2026-09-17 18:54:54', '2026-09-21 21:38:30'),
	(14, 'Jasa Borongan Tenaga Alih Daya 2 org Periode September-Desember 2026', NULL, '014/INT.JPP/IX/2026', '2026-09-02', '310000008263', '510000007952', 39500000.00, NULL, 100.00, 100.00, 10.00, 15.00, '2026-09-01', '2026-12-31', 19.25, NULL, NULL, 'PO Jasa: Proses PO', 'TECO tgl :\r\nDokumen Masuk SC Kamis 04/09/2026', 'aktif', 'internal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-09-22 01:45:21', '2026-09-23 20:06:29'),
	(15, 'Jasa Pemeliharaan Pabrik NPK Bidang Mekanikal Periode Mei - Juli 2026', NULL, '020.BAK/INT.JPP/PIM/III/2026', '2026-04-10', '310000007682', '510000007408', 73500000.00, 67500000.00, 100.00, 100.00, 100.00, 50.00, '2026-05-01', '2026-09-30', 97.50, '5450000227', NULL, 'Proses Pembayaran', 'PERIODE April-Jun 2026\r\n1. PO JASBOR : Periode Mei-Juli 5450000227 (Selesai GRS tgl 10/09/2026)\r\nTECO tgl : \r\nDokumen Masuk SC TGL 17-04-2026', 'aktif', 'public', '1pmZp4j-cgyxDKMZyd55eJn99cMP9GvE6', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-09-22 01:55:37', '2026-09-22 02:12:54'),
	(16, 'Jasa Fabrikasi Roller : Carry Roller BW1000 (100ea),  Return Roller BW1000 (100ea), Carry Roller BW800 (100ea) Return Roller BW800 (100ea)', NULL, '08907/E/TK/3310/IT/2025', '2025-05-20', '-', '-', 625410000.00, 0.00, 100.00, 0.00, 0.00, 0.00, '2026-09-01', '2026-10-31', 5.00, NULL, NULL, NULL, 'PERIODE 2026\r\nPO Jasa: \r\nTECO tgl :\r\nRevisi OE Jasa Job Package Internal Pekerjaan \r\nFabrikasi Roller BW 800 & BW 1000', 'aktif', 'public', '12xOS5n4IIO74xPUss2c15eRt9nai3azB', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-09-22 09:05:34', '2026-09-22 09:07:27'),
	(17, 'Pembuatan Infrastruktur Edukowisata', NULL, '09816/E/TR/1210/IT/2024', '2024-04-20', '310000004677', '510000004314', 1.00, NULL, 50.00, 0.00, 0.00, 0.00, '2027-06-06', '2027-10-20', 2.50, NULL, NULL, NULL, '- Proses Pehitungan \r\n- Menunggu selesai di desai gambar dari tim Dept. TJSL & Humas\r\n- Target koordinasi 26-07-2024\r\n- Rapat tindak lanjut sama Tim TJSL 02.08.2024\r\n- DIBATALKAN (Info pak Jufri by WA) tgl 02.08.2024', 'aktif', 'public', '1Xok-7fHAxa6TClRlcE1uPKIY_nkBBITE', 'https://drive.google.com/file/d/1XVIqu44U90Fb61g321WXv4Vo7V9hGNU_/view?usp=drivesdk', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-22 10:13:35', '2026-09-24 20:53:56'),
	(18, 'Jasa Preventive dan Cleaning Equipment pada Belt Conveyor System Pabrik NPK (FAB)  Periode April - Juni 2026', NULL, '05708/E/TK/3310/IT/2026', '2026-06-04', '310000007720', '510000007413', 687350000.00, 474635200.00, 100.00, 100.00, 100.00, 75.00, '2026-04-01', '2026-06-30', 98.75, '5450000234', NULL, 'Menunggu Proses Admin Keuangan', 'PERIODE April-Jun 2026\r\n1. PO JASBOR : 5450000234 (Selesai GRS tgl 31/07/2026)\r\n2. PO MATERIAL: 5450000230 (Selesai GRS tgl 30/06/2026)\r\n3. PO SEWA MOBIL: 5450000228 (Selesai GRS tgl 30/06/2026)\r\nTECO tgl :\r\nDokumen Masuk SC TGL 27-04-2026', 'aktif', 'public', '1I15Hnnp0WgO6z-J4WuHSRETb1Qlog-UV', 'https://drive.google.com/file/d/1CPwW8krqIfj6CoBv2uX3t7rWAPR4-GFa/view?usp=drivesdk', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-22 18:54:56', '2026-09-22 18:55:08'),
	(19, 'Jasa Fabrikasi Roller : Return Roller BW1200 (100ea), Carry Roller BW750 (100ea), Return Roller (BW750) (50ea)', NULL, '02144/E/TK/3310/IT/2026', '2026-02-09', '310000007993', '510000007728', 495000000.00, 2160000.00, 100.00, 100.00, 60.20, 30.00, '2026-09-01', '2026-10-31', 62.67, '5450000242', NULL, 'Menunggu penyelesaian progress pekerjaan beserta proses administrasi keuangan', 'PERIODE 2026\r\nDokumen Masuk SC Selasa 13/07/2026\r\n1. PO Material Gas : 5450000242 (Selesai GRS tgl 12/08/2026)\r\n2. PO Material : 5450000250 (03/09/2026)\r\n3. PO Material Tools : 5450000245 (Selesai GRS tgl 14/08/2026)\r\n4. PO Material Glove : 5450000243 (Selesai GRS tgl 06/08/2026)\r\n5. PO Material Bearing : 5450000248 (Selesai GRS tgl 10/09/2026)\r\n6. PO Material Rubber : Proses DUR\r\n7. PO Material Pipe & Bar : Proses DUR\r\n8. PO Material Jasa : Proses DUR\r\nTECO tgl :', 'aktif', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-09-23 11:50:01', '2026-09-24 21:45:55');

-- Dumping structure for table moni-jpp.job_package_permintaans
CREATE TABLE IF NOT EXISTS `job_package_permintaans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_package_id` bigint unsigned NOT NULL,
  `permintaan_dari` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_package_permintaans_job_package_id_foreign` (`job_package_id`),
  CONSTRAINT `job_package_permintaans_job_package_id_foreign` FOREIGN KEY (`job_package_id`) REFERENCES `job_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.job_package_permintaans: ~9 rows (approximately)
DELETE FROM `job_package_permintaans`;
INSERT INTO `job_package_permintaans` (`id`, `job_package_id`, `permintaan_dari`, `created_at`, `updated_at`) VALUES
	(9, 13, 'Dept. Rendal Har', '2026-09-21 21:38:30', '2026-09-21 21:38:30'),
	(10, 13, 'Dept. Operasi pabrik-2', '2026-09-21 21:38:30', '2026-09-21 21:38:30'),
	(15, 15, 'Dept. HAR ML', '2026-09-22 02:14:12', '2026-09-22 02:14:12'),
	(18, 14, 'Dept JPP', '2026-09-22 08:52:19', '2026-09-22 08:52:19'),
	(21, 11, 'Dept. Rendal Har', '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(22, 11, 'Dept. Operasi pabrik-2', '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(24, 16, 'Dept. Rendal HAR', '2026-09-22 09:07:32', '2026-09-22 09:07:32'),
	(26, 18, 'Dept. Rendal HAR', '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(31, 19, 'Rendal Har', '2026-09-24 21:45:55', '2026-09-24 21:45:55');

-- Dumping structure for table moni-jpp.job_package_pos
CREATE TABLE IF NOT EXISTS `job_package_pos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_package_id` bigint unsigned NOT NULL,
  `no_po` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_package_pos_job_package_id_foreign` (`job_package_id`),
  CONSTRAINT `job_package_pos_job_package_id_foreign` FOREIGN KEY (`job_package_id`) REFERENCES `job_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.job_package_pos: ~17 rows (approximately)
DELETE FROM `job_package_pos`;
INSERT INTO `job_package_pos` (`id`, `job_package_id`, `no_po`, `description`, `price`, `created_at`, `updated_at`) VALUES
	(69, 8, '5450000129', 'ExtraFooding', 9100000.00, '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(70, 8, '5450000127', 'Material', 117528000.00, '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(71, 8, '5450000137', 'JASBOR Modif', 229240000.00, '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(72, 8, '5450000183', 'JASBOR Pulling Cale & Cable', 620000000.00, '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(78, 13, '5007380167', 'JASA', 18000000.00, '2026-09-21 21:38:30', '2026-09-21 21:38:30'),
	(81, 15, '5450000227', 'PO JASBOR', 67500000.00, '2026-09-22 02:14:12', '2026-09-22 02:14:12'),
	(84, 6, '5450000223', NULL, 0.00, '2026-09-22 06:14:53', '2026-09-22 06:14:53'),
	(87, 11, '5450000246', 'JASBOR', 594935090.00, '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(88, 11, '5450000224', 'MATERIAL', 32400000.00, '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(92, 18, '5450000234', 'PO JASBOR', 352457700.00, '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(93, 18, '5450000230', 'PO MATERIAL', 95117500.00, '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(94, 18, '5450000228', 'PO SEWA MOBIL', 27060000.00, '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(107, 19, '5450000242', 'PO Material Gas', 2160000.00, '2026-09-24 21:45:55', '2026-09-24 21:45:55'),
	(108, 19, '5450000250', 'PO Material', 0.00, '2026-09-24 21:45:55', '2026-09-24 21:45:55'),
	(109, 19, '5450000245', 'PO Material Tools', 0.00, '2026-09-24 21:45:55', '2026-09-24 21:45:55'),
	(110, 19, '5450000243', 'PO Material Glove', 0.00, '2026-09-24 21:45:55', '2026-09-24 21:45:55'),
	(111, 19, '5450000248', 'PO Material Bearing', 0.00, '2026-09-24 21:45:55', '2026-09-24 21:45:55');

-- Dumping structure for table moni-jpp.job_package_surats
CREATE TABLE IF NOT EXISTS `job_package_surats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_package_id` bigint unsigned NOT NULL,
  `no_surat_bak_doc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_package_surats_job_package_id_foreign` (`job_package_id`),
  CONSTRAINT `job_package_surats_job_package_id_foreign` FOREIGN KEY (`job_package_id`) REFERENCES `job_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.job_package_surats: ~16 rows (approximately)
DELETE FROM `job_package_surats`;
INSERT INTO `job_package_surats` (`id`, `job_package_id`, `no_surat_bak_doc`, `created_at`, `updated_at`) VALUES
	(5, 8, '14686/E/TK/3310/IT/2025 026.BAK-EMG/INT.JPP/PIM/VII/2025', '2026-09-16 18:46:26', '2026-09-16 18:46:26'),
	(11, 13, '06964/E/TK/3310/IT/2026\r\n024.BAK/INT.JPP/PIM/V/2026', '2026-09-21 21:38:30', '2026-09-21 21:38:30'),
	(17, 15, '020.BAK/INT.JPP/PIM/III/2026', '2026-09-22 02:14:12', '2026-09-22 02:14:12'),
	(20, 6, '04547/E/PL/3210/IT/2026', '2026-09-22 06:14:53', '2026-09-22 06:14:53'),
	(21, 6, '017.BAK-EMG/INT.JPP/PIM/III/2026', '2026-09-22 06:14:53', '2026-09-22 06:14:53'),
	(22, 14, '014/INT.JPP/IX/2026', '2026-09-22 08:52:19', '2026-09-22 08:52:19'),
	(23, 14, 'Izin Prinsip/ Formulir Pengadaan Jasa By VP JPP', '2026-09-22 08:52:19', '2026-09-22 08:52:19'),
	(26, 11, '05570/E/TK/3310/IT/2026', '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(27, 11, '019.BAK/INT.JPP/PIM/III/2027', '2026-09-22 09:01:14', '2026-09-22 09:01:14'),
	(30, 16, '08907/E/TK/3310/IT/2025', '2026-09-22 09:07:32', '2026-09-22 09:07:32'),
	(31, 16, '031.BAK/INT.JPP/PIM/VII/2026', '2026-09-22 09:07:32', '2026-09-22 09:07:32'),
	(36, 18, '05708/E/TK/3310/IT/2026', '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(37, 18, '021.BAK/INT.JPP/PIM/III/2026', '2026-09-22 18:55:46', '2026-09-22 18:55:46'),
	(42, 17, '09816/E/TR/1210/IT/2024', '2026-09-24 20:53:56', '2026-09-24 20:53:56'),
	(45, 19, '02144/E/TK/3310/IT/2026', '2026-09-24 21:45:55', '2026-09-24 21:45:55'),
	(46, 19, '025.BAK/INT.JPP/PIM/VI/2026', '2026-09-24 21:45:55', '2026-09-24 21:45:55');

-- Dumping structure for table moni-jpp.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.migrations: ~17 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_08_21_040504_create_job_packages_table', 1),
	(5, '2026_08_21_044925_add_documents_to_job_packages_table', 1),
	(6, '2026_08_26_083656_add_latest_activity_to_job_packages_table', 1),
	(7, '2026_09_02_053743_add_google_drive_folder_id_to_job_packages_table', 2),
	(8, '2026_09_07_084452_add_po_items_to_job_packages_table', 3),
	(9, '2026_09_07_091213_create_job_package_pos_table', 4),
	(10, '2026_09_10_074728_add_status_to_job_packages_table', 5),
	(11, '2026_09_14_040441_create_activity_logs_table', 6),
	(12, '2026_09_16_020423_create_job_package_surats_table', 7),
	(13, '2026_09_17_033229_add_permintaan_dari_to_job_packages_table', 8),
	(14, '2026_09_17_040801_create_job_package_permintaans_table', 9),
	(15, '2026_09_18_000000_create_dokumentasis_table', 10),
	(16, '2026_09_22_153240_add_visibility_to_job_packages_table', 11),
	(17, '2026_09_23_191034_add_periode_to_job_packages_table', 12);

-- Dumping structure for table moni-jpp.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.password_reset_tokens: ~1 rows (approximately)
DELETE FROM `password_reset_tokens`;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('bintangrn10@gmail.com', '$2y$12$nQWAew.ULyc5dgQVTguRBeh7QmrLSkUvQP5MfDZS7e1tJ0nuiZyhq', '2026-08-26 19:06:56');

-- Dumping structure for table moni-jpp.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.sessions: ~3 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('3bz4nw785rbhB3kqKJzPKlsntj3xgjDZOJRYfE2G', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJBcGV1VklyVXpKNWNGbFBwbU9lNlhwekUzR1VRa1pRZjlxMGlsdEdpIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL21vbmktanBwLnRlc3RcL2FkbWluXC9qb2ItcGFja2FnZXMiLCJyb3V0ZSI6ImFkbWluLmpvYi1wYWNrYWdlcy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1790327973),
	('oiheVwHPO9ZQy3JAFAeMR313Xch8r2HVF1jbmqSo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5WnF2Y2U2QWgyTzVnWWRWWjRJZTMxTHVkTTd2OThuNThEUEpjeURwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbW9uaS1qcHAudGVzdFwvYWRtaW5cL2pvYi1wYWNrYWdlcyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbW9uaS1qcHAudGVzdFwvYWRtaW5cL2pvYi1wYWNrYWdlcyIsInJvdXRlIjoiYWRtaW4uam9iLXBhY2thZ2VzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1790327953),
	('zGXWD2WeLYN5CVpV3YEJTqOT16gJxQzEcAdqx12e', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ6enJpb1Q4WkZxZHA1OWIyZjdpemlDdlpxeEc1S1dVcU45TEt4VFZ1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbW9uaS1qcHAudGVzdFwvYWRtaW5cL2Rhc2hib2FyZCIsInJvdXRlIjoiYWRtaW4uZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1790312091);

-- Dumping structure for table moni-jpp.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table moni-jpp.users: ~3 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `username`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrator JPP', 'admin', 'admin@jpp.com', 'admin', NULL, '$2y$12$LPT9qouSySfvqPWenmGFPeucf3aTmtKJHIWB4oQXP6EH6XFfikN2i', NULL, '2026-08-26 18:42:20', '2026-08-26 18:45:44'),
	(2, 'Bintang', 'bintangrn', 'bintangrn10@gmail.com', 'staff', NULL, '$2y$12$ltJ4VOY65WSZZtvKKpUEL.SN/UM9SR7w5KCU8pRVT9RB//tCupDWa', NULL, '2026-08-26 19:00:05', '2026-08-26 19:00:05'),
	(3, 'Fakrul Razi', 'razi', 'fakrurrazi257@gmail.com', 'staff', NULL, '$2y$12$qqd5GXVi6tVMgypIdtRmJO6lP9A/xi9dYMOQ9tm9nz6QMxAiWpVhq', NULL, '2026-09-22 19:28:34', '2026-09-22 19:28:34');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
