/*
 Navicat Premium Dump SQL

 Source Server         : jo
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : kampus

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 04/03/2026 02:40:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for academic_settings
-- ----------------------------
DROP TABLE IF EXISTS `academic_settings`;
CREATE TABLE `academic_settings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `max_sks_default` tinyint UNSIGNED NOT NULL DEFAULT 24,
  `max_sks_ips_3` tinyint UNSIGNED NOT NULL DEFAULT 24,
  `grade_a_min` decimal(5, 2) NOT NULL DEFAULT 85.00,
  `grade_a_minus_min` decimal(5, 2) NOT NULL DEFAULT 80.00,
  `grade_b_plus_min` decimal(5, 2) NOT NULL DEFAULT 75.00,
  `grade_b_min` decimal(5, 2) NOT NULL DEFAULT 70.00,
  `grade_b_minus_min` decimal(5, 2) NOT NULL DEFAULT 65.00,
  `grade_c_plus_min` decimal(5, 2) NOT NULL DEFAULT 60.00,
  `grade_c_min` decimal(5, 2) NOT NULL DEFAULT 55.00,
  `grade_d_min` decimal(5, 2) NOT NULL DEFAULT 45.00,
  `krs_open_at` datetime NULL DEFAULT NULL,
  `krs_close_at` datetime NULL DEFAULT NULL,
  `nilai_input_close_at` datetime NULL DEFAULT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `evaluasi_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `auto_nonaktif_if_ukt_unpaid` tinyint(1) NOT NULL DEFAULT 1,
  `bobot_tugas` decimal(5, 2) NOT NULL DEFAULT 30.00,
  `bobot_uts` decimal(5, 2) NOT NULL DEFAULT 25.00,
  `bobot_uas` decimal(5, 2) NOT NULL DEFAULT 35.00,
  `bobot_kehadiran` decimal(5, 2) NOT NULL DEFAULT 10.00,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `academic_settings_updated_by_foreign`(`updated_by` ASC) USING BTREE,
  CONSTRAINT `academic_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of academic_settings
-- ----------------------------
INSERT INTO `academic_settings` VALUES (1, 24, 24, 85.00, 80.00, 75.00, 70.00, 65.00, 60.00, 55.00, 45.00, '2026-02-24 19:30:48', '2026-04-02 19:30:48', '2026-04-17 19:30:48', 0, 1, 1, 30.00, 25.00, 35.00, 10.00, NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for audit_logs
-- ----------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `aksi` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modul` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `entity_id` bigint UNSIGNED NULL DEFAULT NULL,
  `konteks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `before_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `after_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `audit_logs_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `audit_logs_modul_created_at_index`(`modul` ASC, `created_at` ASC) USING BTREE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for dosen
-- ----------------------------
DROP TABLE IF EXISTS `dosen`;
CREATE TABLE `dosen`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nidn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prodi_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `dosen_nidn_unique`(`nidn` ASC) USING BTREE,
  UNIQUE INDEX `dosen_user_id_unique`(`user_id` ASC) USING BTREE,
  INDEX `dosen_prodi_id_foreign`(`prodi_id` ASC) USING BTREE,
  CONSTRAINT `dosen_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `program_studi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dosen
-- ----------------------------
INSERT INTO `dosen` VALUES (1, '0123456789', 'Dosen Satu', 1, 2, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for dosen_pa_mahasiswa
-- ----------------------------
DROP TABLE IF EXISTS `dosen_pa_mahasiswa`;
CREATE TABLE `dosen_pa_mahasiswa`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `dosen_id` bigint UNSIGNED NOT NULL,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NULL DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dosen_pa_mahasiswa_dosen_id_foreign`(`dosen_id` ASC) USING BTREE,
  INDEX `dosen_pa_mahasiswa_mahasiswa_id_foreign`(`mahasiswa_id` ASC) USING BTREE,
  CONSTRAINT `dosen_pa_mahasiswa_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `dosen_pa_mahasiswa_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dosen_pa_mahasiswa
-- ----------------------------

-- ----------------------------
-- Table structure for evaluasi_dosen
-- ----------------------------
DROP TABLE IF EXISTS `evaluasi_dosen`;
CREATE TABLE `evaluasi_dosen`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `krs_detail_id` bigint UNSIGNED NOT NULL,
  `status_selesai` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `evaluasi_dosen_krs_detail_id_unique`(`krs_detail_id` ASC) USING BTREE,
  CONSTRAINT `evaluasi_dosen_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of evaluasi_dosen
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for fakultas
-- ----------------------------
DROP TABLE IF EXISTS `fakultas`;
CREATE TABLE `fakultas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_fakultas` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `fakultas_nama_fakultas_unique`(`nama_fakultas` ASC) USING BTREE,
  INDEX `fakultas_deleted_by_foreign`(`deleted_by` ASC) USING BTREE,
  INDEX `fakultas_deleted_at_index`(`deleted_at` ASC) USING BTREE,
  CONSTRAINT `fakultas_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fakultas
-- ----------------------------
INSERT INTO `fakultas` VALUES (1, 'Fakultas Ilmu Komputer', '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);

-- ----------------------------
-- Table structure for jabatan_dosen
-- ----------------------------
DROP TABLE IF EXISTS `jabatan_dosen`;
CREATE TABLE `jabatan_dosen`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `dosen_id` bigint UNSIGNED NOT NULL,
  `jabatan` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NULL DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jabatan_dosen_dosen_id_foreign`(`dosen_id` ASC) USING BTREE,
  INDEX `jabatan_dosen_deleted_by_foreign`(`deleted_by` ASC) USING BTREE,
  INDEX `jabatan_dosen_deleted_at_index`(`deleted_at` ASC) USING BTREE,
  CONSTRAINT `jabatan_dosen_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `jabatan_dosen_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jabatan_dosen
-- ----------------------------

-- ----------------------------
-- Table structure for jadwal
-- ----------------------------
DROP TABLE IF EXISTS `jadwal`;
CREATE TABLE `jadwal`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` bigint UNSIGNED NOT NULL,
  `dosen_id` bigint UNSIGNED NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jadwal_mata_kuliah_id_foreign`(`mata_kuliah_id` ASC) USING BTREE,
  INDEX `jadwal_tahun_akademik_id_foreign`(`tahun_akademik_id` ASC) USING BTREE,
  INDEX `jadwal_deleted_by_foreign`(`deleted_by` ASC) USING BTREE,
  INDEX `jadwal_deleted_at_index`(`deleted_at` ASC) USING BTREE,
  INDEX `jadwal_ruangan_hari_jam_idx`(`ruangan` ASC, `hari` ASC, `jam_mulai` ASC, `jam_selesai` ASC) USING BTREE,
  INDEX `jadwal_dosen_hari_jam_idx`(`dosen_id` ASC, `hari` ASC, `jam_mulai` ASC, `jam_selesai` ASC) USING BTREE,
  CONSTRAINT `jadwal_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal
-- ----------------------------
INSERT INTO `jadwal` VALUES (1, 1, 1, 'Senin', '08:00:00', '10:30:00', 'Lab-3', 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `jadwal` VALUES (2, 2, 1, 'Selasa', '10:30:00', '13:00:00', 'R-204', 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `jadwal` VALUES (3, 3, 1, 'Rabu', '08:00:00', '10:30:00', 'R-202', 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `jadwal` VALUES (4, 4, 1, 'Kamis', '13:00:00', '14:40:00', 'R-203', 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for krs
-- ----------------------------
DROP TABLE IF EXISTS `krs`;
CREATE TABLE `krs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `total_sks` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `status_krs` enum('draft','final') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `nilai_terkunci` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `krs_mahasiswa_tahun_unique`(`mahasiswa_id` ASC, `tahun_akademik_id` ASC) USING BTREE,
  INDEX `krs_tahun_akademik_id_foreign`(`tahun_akademik_id` ASC) USING BTREE,
  CONSTRAINT `krs_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `krs_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of krs
-- ----------------------------
INSERT INTO `krs` VALUES (1, 1, 1, 11, 'final', 0, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for krs_detail
-- ----------------------------
DROP TABLE IF EXISTS `krs_detail`;
CREATE TABLE `krs_detail`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `krs_id` bigint UNSIGNED NOT NULL,
  `jadwal_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `krs_detail_krs_id_jadwal_id_unique`(`krs_id` ASC, `jadwal_id` ASC) USING BTREE,
  INDEX `krs_detail_jadwal_id_foreign`(`jadwal_id` ASC) USING BTREE,
  CONSTRAINT `krs_detail_jadwal_id_foreign` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `krs_detail_krs_id_foreign` FOREIGN KEY (`krs_id`) REFERENCES `krs` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of krs_detail
-- ----------------------------
INSERT INTO `krs_detail` VALUES (1, 1, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `krs_detail` VALUES (2, 1, 2, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `krs_detail` VALUES (3, 1, 3, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `krs_detail` VALUES (4, 1, 4, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `login_attempts_email_ip_address_attempted_at_index`(`email` ASC, `ip_address` ASC, `attempted_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of login_attempts
-- ----------------------------

-- ----------------------------
-- Table structure for mahasiswa
-- ----------------------------
DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nim` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prodi_id` bigint UNSIGNED NOT NULL,
  `angkatan` smallint UNSIGNED NOT NULL,
  `status_mahasiswa` enum('aktif','cuti','lulus','dropout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `status_akademik` enum('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `catatan_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mahasiswa_nim_unique`(`nim` ASC) USING BTREE,
  UNIQUE INDEX `mahasiswa_user_id_unique`(`user_id` ASC) USING BTREE,
  INDEX `mahasiswa_prodi_id_foreign`(`prodi_id` ASC) USING BTREE,
  CONSTRAINT `mahasiswa_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `program_studi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mahasiswa
-- ----------------------------
INSERT INTO `mahasiswa` VALUES (1, '23010001', 'Mahasiswa Satu', 1, 2023, 'aktif', 'aktif', NULL, 3, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for mahasiswa_status_logs
-- ----------------------------
DROP TABLE IF EXISTS `mahasiswa_status_logs`;
CREATE TABLE `mahasiswa_status_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `changed_by` bigint UNSIGNED NULL DEFAULT NULL,
  `status_lama` enum('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status_baru` enum('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `catatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mahasiswa_status_logs_changed_by_foreign`(`changed_by` ASC) USING BTREE,
  INDEX `mahasiswa_status_logs_mahasiswa_id_created_at_index`(`mahasiswa_id` ASC, `created_at` ASC) USING BTREE,
  CONSTRAINT `mahasiswa_status_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `mahasiswa_status_logs_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mahasiswa_status_logs
-- ----------------------------

-- ----------------------------
-- Table structure for mata_kuliah
-- ----------------------------
DROP TABLE IF EXISTS `mata_kuliah`;
CREATE TABLE `mata_kuliah`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mk` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks` tinyint UNSIGNED NOT NULL,
  `semester` tinyint UNSIGNED NOT NULL,
  `prodi_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mata_kuliah_kode_mk_prodi_id_unique`(`kode_mk` ASC, `prodi_id` ASC) USING BTREE,
  INDEX `mata_kuliah_prodi_id_foreign`(`prodi_id` ASC) USING BTREE,
  INDEX `mata_kuliah_deleted_by_foreign`(`deleted_by` ASC) USING BTREE,
  INDEX `mata_kuliah_deleted_at_index`(`deleted_at` ASC) USING BTREE,
  CONSTRAINT `mata_kuliah_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `mata_kuliah_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `program_studi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mata_kuliah
-- ----------------------------
INSERT INTO `mata_kuliah` VALUES (1, 'IF301', 'Pemrograman Web Lanjut', 3, 6, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (2, 'IF305', 'Basis Data Lanjut', 3, 6, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (3, 'IF307', 'Rekayasa Perangkat Lunak', 3, 6, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (4, 'IF311', 'Kecerdasan Buatan', 2, 6, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_03_03_120000_create_academic_information_schema', 1);
INSERT INTO `migrations` VALUES (5, '2026_03_03_130000_create_audit_logs_table', 1);
INSERT INTO `migrations` VALUES (6, '2026_03_03_140000_add_status_akademik_to_mahasiswa', 1);
INSERT INTO `migrations` VALUES (7, '2026_03_03_150000_create_mahasiswa_status_logs_table', 1);
INSERT INTO `migrations` VALUES (8, '2026_03_04_090000_create_permissions_tables', 1);
INSERT INTO `migrations` VALUES (9, '2026_03_04_110000_add_nilai_terkunci_to_krs', 1);
INSERT INTO `migrations` VALUES (10, '2026_03_04_120000_add_soft_delete_columns_to_master_tables', 1);
INSERT INTO `migrations` VALUES (11, '2026_03_04_130000_create_system_settings_table', 1);
INSERT INTO `migrations` VALUES (12, '2026_03_04_131000_add_krs_period_fields_to_tahun_akademik', 1);
INSERT INTO `migrations` VALUES (13, '2026_03_04_132000_add_reconciliation_fields_to_pembayaran', 1);
INSERT INTO `migrations` VALUES (14, '2026_03_04_133000_add_breakdown_fields_to_nilai', 1);
INSERT INTO `migrations` VALUES (15, '2026_03_04_134000_create_academic_settings_table', 1);
INSERT INTO `migrations` VALUES (16, '2026_03_04_134100_create_login_attempts_table', 1);
INSERT INTO `migrations` VALUES (17, '2026_03_04_134200_add_before_after_data_to_audit_logs', 1);
INSERT INTO `migrations` VALUES (18, '2026_03_04_134300_add_jadwal_overlap_indexes', 1);
INSERT INTO `migrations` VALUES (19, '2026_03_04_134400_extend_status_akademik_enum_and_payment_unique', 1);

-- ----------------------------
-- Table structure for nilai
-- ----------------------------
DROP TABLE IF EXISTS `nilai`;
CREATE TABLE `nilai`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `krs_detail_id` bigint UNSIGNED NOT NULL,
  `nilai_tugas` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_uts` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_uas` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_kehadiran` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_angka` decimal(5, 2) NOT NULL,
  `nilai_huruf` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nilai_krs_detail_id_unique`(`krs_detail_id` ASC) USING BTREE,
  CONSTRAINT `nilai_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nilai
-- ----------------------------
INSERT INTO `nilai` VALUES (1, 1, 85.00, 84.00, 90.00, 95.00, 88.05, 'A', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `nilai` VALUES (2, 2, 76.00, 78.00, 79.00, 84.00, 78.65, 'B+', '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for pembayaran
-- ----------------------------
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tagihan_id` bigint UNSIGNED NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar` decimal(15, 2) NOT NULL,
  `metode_bayar` enum('transfer','cash','va','qris') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transfer',
  `bukti_bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_reconciliation_error` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pembayaran_dup_guard_unique`(`tagihan_id` ASC, `tanggal_bayar` ASC, `jumlah_bayar` ASC, `metode_bayar` ASC) USING BTREE,
  CONSTRAINT `pembayaran_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_ukt` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pembayaran
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `permissions_kode_unique`(`kode` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'dashboard.view', 'Lihat dashboard', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (2, 'krs.view', 'Lihat menu KRS', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (3, 'krs.manage', 'Isi dan simpan KRS', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (4, 'master.view', 'Lihat data master akademik', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (5, 'master.manage', 'Kelola data master akademik', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (6, 'khs.generate', 'Generate/finalisasi KHS', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (7, 'keuangan.view', 'Lihat data tagihan dan pembayaran', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (8, 'keuangan.manage', 'Kelola tagihan dan validasi pembayaran', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (9, 'users.manage', 'Kelola user', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (10, 'roles.manage', 'Kelola role', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (11, 'nilai.manage', 'Input nilai mahasiswa', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (12, 'jadwal.view', 'Lihat jadwal mengajar', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (13, 'nilai.view', 'Lihat nilai / KHS', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (14, 'ukt.view', 'Lihat status pembayaran UKT', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (15, 'mahasiswa.monitor', 'Monitoring mahasiswa bimbingan/perkuliahan', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `permissions` VALUES (16, 'audit.view', 'Lihat audit log sistem', '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for prasyarat_mk
-- ----------------------------
DROP TABLE IF EXISTS `prasyarat_mk`;
CREATE TABLE `prasyarat_mk`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` bigint UNSIGNED NOT NULL,
  `mata_kuliah_prasyarat_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mk_prasyarat_unique`(`mata_kuliah_id` ASC, `mata_kuliah_prasyarat_id` ASC) USING BTREE,
  INDEX `prasyarat_mk_mata_kuliah_prasyarat_id_foreign`(`mata_kuliah_prasyarat_id` ASC) USING BTREE,
  CONSTRAINT `prasyarat_mk_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `prasyarat_mk_mata_kuliah_prasyarat_id_foreign` FOREIGN KEY (`mata_kuliah_prasyarat_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of prasyarat_mk
-- ----------------------------

-- ----------------------------
-- Table structure for program_studi
-- ----------------------------
DROP TABLE IF EXISTS `program_studi`;
CREATE TABLE `program_studi`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_prodi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fakultas_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `program_studi_nama_prodi_fakultas_id_unique`(`nama_prodi` ASC, `fakultas_id` ASC) USING BTREE,
  INDEX `program_studi_fakultas_id_foreign`(`fakultas_id` ASC) USING BTREE,
  INDEX `program_studi_deleted_by_foreign`(`deleted_by` ASC) USING BTREE,
  INDEX `program_studi_deleted_at_index`(`deleted_at` ASC) USING BTREE,
  CONSTRAINT `program_studi_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `program_studi_fakultas_id_foreign` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of program_studi
-- ----------------------------
INSERT INTO `program_studi` VALUES (1, 'Informatika', 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48', NULL, NULL);

-- ----------------------------
-- Table structure for role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `role_permissions_role_id_permission_id_unique`(`role_id` ASC, `permission_id` ASC) USING BTREE,
  INDEX `role_permissions_permission_id_foreign`(`permission_id` ASC) USING BTREE,
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_permissions
-- ----------------------------
INSERT INTO `role_permissions` VALUES (1, 1, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (2, 1, 2, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (3, 1, 3, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (4, 1, 4, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (5, 1, 5, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (6, 1, 7, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (7, 1, 8, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (8, 1, 11, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (9, 1, 12, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (10, 1, 15, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (11, 1, 9, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (12, 1, 10, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (13, 1, 16, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (14, 2, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (15, 2, 4, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (16, 2, 5, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (17, 2, 6, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (18, 3, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (19, 3, 7, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (20, 3, 8, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (21, 4, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (22, 4, 11, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (23, 4, 12, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (24, 4, 15, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (25, 5, 1, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (26, 5, 2, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (27, 5, 3, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (28, 5, 13, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `role_permissions` VALUES (29, 5, 14, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `roles_role_name_unique`(`role_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'super_admin', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `roles` VALUES (2, 'admin_akademik', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `roles` VALUES (3, 'admin_keuangan', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `roles` VALUES (4, 'dosen', '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `roles` VALUES (5, 'mahasiswa', '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------

-- ----------------------------
-- Table structure for system_settings
-- ----------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `system_settings_key_unique`(`key` ASC) USING BTREE,
  INDEX `system_settings_updated_by_foreign`(`updated_by` ASC) USING BTREE,
  CONSTRAINT `system_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_settings
-- ----------------------------

-- ----------------------------
-- Table structure for tagihan_ukt
-- ----------------------------
DROP TABLE IF EXISTS `tagihan_ukt`;
CREATE TABLE `tagihan_ukt`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(15, 2) NOT NULL,
  `status` enum('menunggu','lunas','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ukt_mahasiswa_tahun_unique`(`mahasiswa_id` ASC, `tahun_akademik_id` ASC) USING BTREE,
  INDEX `tagihan_ukt_tahun_akademik_id_foreign`(`tahun_akademik_id` ASC) USING BTREE,
  CONSTRAINT `tagihan_ukt_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ukt_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tagihan_ukt
-- ----------------------------
INSERT INTO `tagihan_ukt` VALUES (1, 1, 1, 4500000.00, 'lunas', '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for tahun_akademik
-- ----------------------------
DROP TABLE IF EXISTS `tahun_akademik`;
CREATE TABLE `tahun_akademik`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tahun` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('ganjil','genap','pendek') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 0,
  `krs_dibuka` tinyint(1) NOT NULL DEFAULT 0,
  `krs_mulai` datetime NULL DEFAULT NULL,
  `krs_selesai` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `tahun_akademik_tahun_semester_unique`(`tahun` ASC, `semester` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tahun_akademik
-- ----------------------------
INSERT INTO `tahun_akademik` VALUES (1, '2025/2026', 'genap', 1, 1, '2026-02-26 19:30:48', '2026-03-23 19:30:48', '2026-03-03 19:30:48', '2026-03-03 19:30:48');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  INDEX `users_role_id_foreign`(`role_id` ASC) USING BTREE,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, 'Super Admin', 'superadmin@kampus.ac.id', NULL, '$2y$12$0zPj4JDEJYe2mp9ROjtQ3eqraRH9EVWmLRJZ2iSB28rImJ7jPloDi', 'aktif', NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `users` VALUES (2, 4, 'Dosen Satu', 'dosen1@kampus.ac.id', NULL, '$2y$12$Sgr4.FsV64XchHC0w1kv6uZEK1w6TiGMEcBMVbu2vfgKNQCcawNt2', 'aktif', NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `users` VALUES (3, 5, 'Mahasiswa Satu', 'mhs1@kampus.ac.id', NULL, '$2y$12$oaerzWMJ7sIZO.ukq6z1.erhV/Tg5c4A7O0Q3sUPzYCrrtGCJHK6W', 'aktif', NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `users` VALUES (4, 2, 'Admin Akademik', 'adminakademik@kampus.ac.id', NULL, '$2y$12$NaTxu7.HhJVVLOUlDJbeSeKPFC1kwosTpyZGyruKzw4WL4kpuT5DS', 'aktif', NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');
INSERT INTO `users` VALUES (5, 3, 'Admin Keuangan', 'adminkeuangan@kampus.ac.id', NULL, '$2y$12$NfyaRWoRDie2DT/6AIVrwudY4sk1SJjTMS6uoTTxwGSX46U25Kwfq', 'aktif', NULL, '2026-03-03 19:30:48', '2026-03-03 19:30:48');

SET FOREIGN_KEY_CHECKS = 1;
