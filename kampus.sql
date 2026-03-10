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

 Date: 10/03/2026 09:12:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for academic_decisions
-- ----------------------------
DROP TABLE IF EXISTS `academic_decisions`;
CREATE TABLE `academic_decisions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `context` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decision` enum('allow_suspend','allow_restore','override') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `decided_by` bigint UNSIGNED NULL DEFAULT NULL,
  `decided_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `academic_decisions_mahasiswa_id_foreign`(`mahasiswa_id` ASC) USING BTREE,
  INDEX `academic_decisions_decided_by_foreign`(`decided_by` ASC) USING BTREE,
  INDEX `ad_context_idx`(`tahun_akademik_id` ASC, `context` ASC, `decided_at` ASC) USING BTREE,
  CONSTRAINT `academic_decisions_decided_by_foreign` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `academic_decisions_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `academic_decisions_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of academic_decisions
-- ----------------------------

-- ----------------------------
-- Table structure for academic_rule_snapshots
-- ----------------------------
DROP TABLE IF EXISTS `academic_rule_snapshots`;
CREATE TABLE `academic_rule_snapshots`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `rules_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `snapshotted_at` datetime NOT NULL,
  `locked_at` datetime NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ars_tahun_unique`(`tahun_akademik_id` ASC) USING BTREE,
  INDEX `academic_rule_snapshots_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `ars_lock_idx`(`locked_at` ASC, `snapshotted_at` ASC) USING BTREE,
  CONSTRAINT `academic_rule_snapshots_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `academic_rule_snapshots_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of academic_rule_snapshots
-- ----------------------------
INSERT INTO `academic_rule_snapshots` VALUES (1, 2, '{\"max_sks_default\":24,\"max_sks_ips_3\":24,\"grade_ranges\":[{\"min\":85,\"grade\":\"A\"},{\"min\":80,\"grade\":\"A-\"},{\"min\":75,\"grade\":\"B+\"},{\"min\":70,\"grade\":\"B\"},{\"min\":65,\"grade\":\"B-\"},{\"min\":60,\"grade\":\"C+\"},{\"min\":55,\"grade\":\"C\"},{\"min\":45,\"grade\":\"D\"},{\"min\":0,\"grade\":\"E\"}],\"grade_points\":{\"A\":4,\"A-\":3.7,\"B+\":3.3,\"B\":3,\"B-\":2.7,\"C+\":2.3,\"C\":2,\"D\":1,\"E\":0},\"nilai_bobot\":{\"tugas\":30,\"uts\":25,\"uas\":35,\"kehadiran\":10},\"krs_window\":{\"open_at\":\"2026-02-22 18:11:39\",\"close_at\":\"2026-04-08 18:11:39\"},\"nilai_input_close_at\":\"2026-04-23 18:11:39\"}', '2026-03-09 18:22:58', NULL, NULL, '2026-03-09 18:22:58', '2026-03-09 18:22:58');

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
INSERT INTO `academic_settings` VALUES (1, 24, 24, 85.00, 80.00, 75.00, 70.00, 65.00, 60.00, 55.00, 45.00, '2026-02-22 18:11:39', '2026-04-08 18:11:39', '2026-04-23 18:11:39', 0, 1, 1, 30.00, 25.00, 35.00, 10.00, NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------
INSERT INTO `audit_logs` VALUES (1, 1, 'Login sukses', 'auth', 'users', 1, '{\"target\":\"\\/dashboard\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 18:22:28', '2026-03-09 18:22:28');
INSERT INTO `audit_logs` VALUES (2, NULL, 'Logout', 'auth', 'users', 1, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 18:53:06', '2026-03-09 18:53:06');
INSERT INTO `audit_logs` VALUES (3, 1, 'Login sukses', 'auth', 'users', 1, '{\"target\":\"\\/dashboard\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 18:53:25', '2026-03-09 18:53:25');
INSERT INTO `audit_logs` VALUES (4, 1, 'Ubah status tagihan UKT', 'keuangan', 'tagihan_ukt', 22, '{\"before\":\"partial\",\"after\":\"disputed\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 19:08:09', '2026-03-09 19:08:09');
INSERT INTO `audit_logs` VALUES (5, 1, 'Ubah status tagihan UKT', 'keuangan', 'tagihan_ukt', 22, '{\"before\":\"disputed\",\"after\":\"open\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 19:08:14', '2026-03-09 19:08:14');
INSERT INTO `audit_logs` VALUES (6, 1, 'Soft delete fakultas', 'master_fakultas', 'fakultas', 2, '{\"deleted_data\":{\"nama_fakultas\":\"Fakultas Ekonomi dan Bisnis\"}}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 19:40:59', '2026-03-09 19:40:59');
INSERT INTO `audit_logs` VALUES (7, 1, 'Restore master data', 'super_admin', 'fakultas', 2, '{\"table_name\":\"fakultas\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 19:41:28', '2026-03-09 19:41:28');
INSERT INTO `audit_logs` VALUES (8, 1, 'Login sukses', 'auth', 'users', 1, '{\"target\":\"\\/dashboard\"}', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 01:55:54', '2026-03-10 01:55:54');

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
INSERT INTO `cache` VALUES ('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1773107812);
INSERT INTO `cache` VALUES ('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1773107812;', 1773107812);

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
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dosen
-- ----------------------------
INSERT INTO `dosen` VALUES (1, '1987000001', 'Dosen 01', 1, 4, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (2, '1987000002', 'Dosen 02', 2, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (3, '1987000003', 'Dosen 03', 3, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (4, '1987000004', 'Dosen 04', 4, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (5, '1987000005', 'Dosen 05', 1, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (6, '1987000006', 'Dosen 06', 2, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (7, '1987000007', 'Dosen 07', 3, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (8, '1987000008', 'Dosen 08', 4, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (9, '1987000009', 'Dosen 09', 1, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (10, '1987000010', 'Dosen 10', 2, 13, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (11, '1987000011', 'Dosen 11', 3, 14, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (12, '1987000012', 'Dosen 12', 4, 15, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (13, '1987000013', 'Dosen 13', 1, 16, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (14, '1987000014', 'Dosen 14', 2, 17, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen` VALUES (15, '1987000015', 'Dosen 15', 3, 18, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dosen_pa_mahasiswa
-- ----------------------------
INSERT INTO `dosen_pa_mahasiswa` VALUES (1, 8, 1, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (2, 9, 2, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (3, 8, 3, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (4, 9, 4, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (5, 8, 5, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (6, 9, 6, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (7, 8, 7, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (8, 9, 8, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (9, 8, 9, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (10, 9, 10, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (11, 8, 11, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (12, 9, 12, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (13, 8, 13, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (14, 9, 14, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (15, 8, 15, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (16, 9, 16, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (17, 8, 17, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (18, 9, 18, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (19, 8, 19, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (20, 9, 20, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (21, 8, 21, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (22, 9, 22, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (23, 8, 23, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (24, 9, 24, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `dosen_pa_mahasiswa` VALUES (25, 8, 25, '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

-- ----------------------------
-- Table structure for evaluasi_dosen
-- ----------------------------
DROP TABLE IF EXISTS `evaluasi_dosen`;
CREATE TABLE `evaluasi_dosen`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NULL DEFAULT NULL,
  `dosen_id` bigint UNSIGNED NULL DEFAULT NULL,
  `mata_kuliah_id` bigint UNSIGNED NULL DEFAULT NULL,
  `tahun_akademik_id` bigint UNSIGNED NULL DEFAULT NULL,
  `krs_detail_id` bigint UNSIGNED NOT NULL,
  `status_selesai` tinyint(1) NOT NULL DEFAULT 0,
  `nilai_1` tinyint UNSIGNED NULL DEFAULT NULL,
  `nilai_2` tinyint UNSIGNED NULL DEFAULT NULL,
  `nilai_3` tinyint UNSIGNED NULL DEFAULT NULL,
  `komentar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `evaluasi_dosen_krs_detail_id_unique`(`krs_detail_id` ASC) USING BTREE,
  INDEX `evaluasi_dosen_mata_kuliah_id_foreign`(`mata_kuliah_id` ASC) USING BTREE,
  INDEX `evaluasi_dosen_tahun_akademik_id_foreign`(`tahun_akademik_id` ASC) USING BTREE,
  INDEX `evaluasi_mhs_ta_idx`(`mahasiswa_id` ASC, `tahun_akademik_id` ASC) USING BTREE,
  INDEX `evaluasi_dosen_mk_ta_idx`(`dosen_id` ASC, `mata_kuliah_id` ASC, `tahun_akademik_id` ASC) USING BTREE,
  CONSTRAINT `evaluasi_dosen_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `evaluasi_dosen_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `evaluasi_dosen_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `evaluasi_dosen_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `evaluasi_dosen_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of evaluasi_dosen
-- ----------------------------
INSERT INTO `evaluasi_dosen` VALUES (1, 9, 8, 1, 2, 23, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-09 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (2, 13, 8, 1, 2, 34, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-08 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (3, 9, 9, 2, 2, 24, 1, 5, 3, 4, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-07 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (4, 13, 9, 2, 2, 35, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-06 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (5, 9, 10, 3, 2, 25, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-05 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (6, 13, 10, 3, 2, 36, 1, 5, 3, 4, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-04 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (7, 3, 15, 8, 2, 7, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-03 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (8, 19, 15, 8, 2, 51, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-02 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (9, 23, 15, 8, 2, 62, 1, 5, 3, 4, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-01 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (10, 3, 8, 9, 2, 8, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-02-28 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (11, 19, 8, 9, 2, 52, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-09 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (12, 23, 8, 9, 2, 63, 1, 5, 3, 4, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-08 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (13, 3, 9, 10, 2, 9, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-07 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (14, 19, 9, 10, 2, 53, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-06 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (15, 23, 9, 10, 2, 64, 1, 5, 3, 4, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-05 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (16, 4, 10, 11, 2, 10, 1, 3, 4, 5, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-04 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `evaluasi_dosen` VALUES (17, 4, 11, 12, 2, 11, 1, 4, 5, 3, 'Pembelajaran berjalan baik, materi jelas dan terstruktur.', '2026-03-03 18:11:47', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fakultas
-- ----------------------------
INSERT INTO `fakultas` VALUES (1, 'Fakultas Ilmu Komputer', '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `fakultas` VALUES (2, 'Fakultas Ekonomi dan Bisnis', '2026-03-09 18:11:39', '2026-03-09 19:41:28', NULL, NULL);
INSERT INTO `fakultas` VALUES (3, 'Fakultas Teknik', '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jabatan_dosen
-- ----------------------------
INSERT INTO `jabatan_dosen` VALUES (1, 1, 'Rektor', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (2, 2, 'Wakil Rektor', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (3, 3, 'Dekan', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (4, 4, 'Wakil Dekan', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (5, 5, 'Kaprodi', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (6, 6, 'Ketua Prodi', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (7, 7, 'Sekretaris Prodi', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (8, 8, 'Dosen Pembimbing Akademik', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (9, 9, 'Dosen Pembimbing Akademik', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (10, 10, 'Koordinator Mata Kuliah', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (11, 11, 'Dosen Pengampu', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (12, 12, 'Dosen Pengampu', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (13, 13, 'Dosen Pengampu', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (14, 14, 'Dosen Pengampu', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (15, 15, 'Dosen Pengampu', '2025-08-01', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jabatan_dosen` VALUES (16, 5, 'Kaprodi', '2024-08-01', '2025-07-31', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal
-- ----------------------------
INSERT INTO `jadwal` VALUES (1, 1, 8, 'Senin', '08:00:00', '10:30:00', 'R-201', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (2, 2, 9, 'Selasa', '10:30:00', '13:00:00', 'R-202', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (3, 3, 10, 'Rabu', '13:00:00', '15:30:00', 'R-203', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (4, 4, 11, 'Kamis', '15:30:00', '18:00:00', 'R-204', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (5, 5, 12, 'Jumat', '08:00:00', '10:30:00', 'R-205', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (6, 6, 13, 'Sabtu', '10:30:00', '13:00:00', 'R-206', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (7, 7, 14, 'Senin', '13:00:00', '15:30:00', 'R-207', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (8, 8, 15, 'Selasa', '15:30:00', '18:00:00', 'R-208', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (9, 9, 8, 'Rabu', '08:00:00', '10:30:00', 'R-209', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (10, 10, 9, 'Kamis', '10:30:00', '13:00:00', 'R-210', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (11, 11, 10, 'Jumat', '13:00:00', '15:30:00', 'R-211', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `jadwal` VALUES (12, 12, 11, 'Sabtu', '15:30:00', '18:00:00', 'R-212', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of krs
-- ----------------------------
INSERT INTO `krs` VALUES (1, 1, 2, 9, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (2, 2, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (3, 3, 2, 8, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (4, 4, 2, 6, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (5, 5, 2, 9, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (6, 6, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (7, 7, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (8, 8, 2, 6, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (9, 9, 2, 9, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (10, 10, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (11, 11, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (12, 12, 2, 6, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (13, 13, 2, 9, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (14, 14, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (15, 15, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (16, 16, 2, 6, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (17, 17, 2, 9, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (18, 18, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (19, 19, 2, 8, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (20, 20, 2, 6, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (21, 21, 2, 9, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (22, 22, 2, 8, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (23, 23, 2, 8, 'final', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (24, 24, 2, 6, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs` VALUES (25, 25, 2, 9, 'draft', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 70 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of krs_detail
-- ----------------------------
INSERT INTO `krs_detail` VALUES (1, 1, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (2, 1, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (3, 1, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (4, 2, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (5, 2, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (6, 2, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (7, 3, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (8, 3, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (9, 3, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (10, 4, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (11, 4, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (12, 5, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (13, 5, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (14, 5, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (15, 6, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (16, 6, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (17, 6, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (18, 7, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (19, 7, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (20, 7, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (21, 8, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (22, 8, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (23, 9, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (24, 9, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (25, 9, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (26, 10, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (27, 10, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (28, 10, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (29, 11, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (30, 11, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (31, 11, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (32, 12, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (33, 12, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (34, 13, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (35, 13, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (36, 13, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (37, 14, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (38, 14, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (39, 14, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (40, 15, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (41, 15, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (42, 15, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (43, 16, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (44, 16, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (45, 17, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (46, 17, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (47, 17, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (48, 18, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (49, 18, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (50, 18, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (51, 19, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (52, 19, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (53, 19, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (54, 20, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (55, 20, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (56, 21, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (57, 21, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (58, 21, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (59, 22, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (60, 22, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (61, 22, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (62, 23, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (63, 23, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (64, 23, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (65, 24, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (66, 24, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (67, 25, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (68, 25, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `krs_detail` VALUES (69, 25, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of login_attempts
-- ----------------------------
INSERT INTO `login_attempts` VALUES (1, 'superadmin@kampus.ac.id', '127.0.0.1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 18:22:28', '2026-03-09 18:22:28', '2026-03-09 18:22:28');
INSERT INTO `login_attempts` VALUES (2, 'superadmin@kampus.ac.id', '127.0.0.1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 18:53:25', '2026-03-09 18:53:25', '2026-03-09 18:53:25');
INSERT INTO `login_attempts` VALUES (3, 'superadmin@kampus.ac.id', '127.0.0.1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-10 01:55:54', '2026-03-10 01:55:54', '2026-03-10 01:55:54');

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
  `enrollment_status_current` enum('aktif','cuti','lulus','do') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `global_hold` tinyint(1) NOT NULL DEFAULT 0,
  `global_hold_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
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
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mahasiswa
-- ----------------------------
INSERT INTO `mahasiswa` VALUES (1, '23010001', 'Mahasiswa 01', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 19, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (2, '23010002', 'Mahasiswa 02', 2, 2024, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 20, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (3, '23010003', 'Mahasiswa 03', 3, 2025, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 21, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (4, '23010004', 'Mahasiswa 04', 4, 2022, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 22, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (5, '23010005', 'Mahasiswa 05', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 23, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (6, '23010006', 'Mahasiswa 06', 2, 2024, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 24, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (7, '23010007', 'Mahasiswa 07', 3, 2025, 'cuti', 'nonaktif', 'cuti', 0, NULL, NULL, 25, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (8, '23010008', 'Mahasiswa 08', 4, 2022, 'aktif', 'suspended_pending_decision', 'aktif', 0, NULL, NULL, 26, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (9, '23010009', 'Mahasiswa 09', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 27, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (10, '23010010', 'Mahasiswa 10', 2, 2024, 'dropout', 'do', 'do', 0, NULL, NULL, 28, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (11, '23010011', 'Mahasiswa 11', 3, 2025, 'aktif', 'aktif', 'aktif', 1, 'Hold finansial menunggu verifikasi.', NULL, 29, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (12, '23010012', 'Mahasiswa 12', 4, 2022, 'lulus', 'alumni', 'lulus', 0, NULL, NULL, 30, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (13, '23010013', 'Mahasiswa 13', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 31, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (14, '23010014', 'Mahasiswa 14', 2, 2024, 'cuti', 'nonaktif', 'cuti', 0, NULL, NULL, 32, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (15, '23010015', 'Mahasiswa 15', 3, 2025, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 33, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (16, '23010016', 'Mahasiswa 16', 4, 2022, 'aktif', 'suspended_pending_decision', 'aktif', 0, NULL, NULL, 34, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (17, '23010017', 'Mahasiswa 17', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 35, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (18, '23010018', 'Mahasiswa 18', 2, 2024, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 36, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (19, '23010019', 'Mahasiswa 19', 3, 2025, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 37, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (20, '23010020', 'Mahasiswa 20', 4, 2022, 'dropout', 'do', 'do', 0, NULL, NULL, 38, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (21, '23010021', 'Mahasiswa 21', 1, 2023, 'cuti', 'nonaktif', 'cuti', 0, NULL, NULL, 39, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (22, '23010022', 'Mahasiswa 22', 2, 2024, 'aktif', 'aktif', 'aktif', 1, 'Hold finansial menunggu verifikasi.', NULL, 40, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (23, '23010023', 'Mahasiswa 23', 3, 2025, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 41, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (24, '23010024', 'Mahasiswa 24', 4, 2022, 'lulus', 'alumni', 'lulus', 0, NULL, NULL, 42, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa` VALUES (25, '23010025', 'Mahasiswa 25', 1, 2023, 'aktif', 'aktif', 'aktif', 0, NULL, NULL, 43, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

-- ----------------------------
-- Table structure for mahasiswa_enrollment_history
-- ----------------------------
DROP TABLE IF EXISTS `mahasiswa_enrollment_history`;
CREATE TABLE `mahasiswa_enrollment_history`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `status` enum('aktif','cuti','lulus','do') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `effective_from` datetime NOT NULL,
  `effective_until` datetime NULL DEFAULT NULL,
  `set_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mahasiswa_enrollment_history_set_by_foreign`(`set_by` ASC) USING BTREE,
  INDEX `meh_lookup_idx`(`mahasiswa_id` ASC, `effective_from` ASC) USING BTREE,
  CONSTRAINT `mahasiswa_enrollment_history_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `mahasiswa_enrollment_history_set_by_foreign` FOREIGN KEY (`set_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mahasiswa_enrollment_history
-- ----------------------------
INSERT INTO `mahasiswa_enrollment_history` VALUES (1, 1, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (2, 2, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (3, 3, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (4, 4, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (5, 5, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (6, 6, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (7, 7, 'cuti', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (8, 8, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (9, 9, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (10, 10, 'do', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (11, 11, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (12, 12, 'lulus', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (13, 13, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (14, 14, 'cuti', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (15, 15, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (16, 16, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (17, 17, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (18, 18, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (19, 19, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (20, 20, 'do', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (21, 21, 'cuti', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (22, 22, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (23, 23, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (24, 24, 'lulus', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_enrollment_history` VALUES (25, 25, 'aktif', 'Seeder initial enrollment status.', '2025-09-09 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

-- ----------------------------
-- Table structure for mahasiswa_period_status
-- ----------------------------
DROP TABLE IF EXISTS `mahasiswa_period_status`;
CREATE TABLE `mahasiswa_period_status`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `tahun_akademik_id` bigint UNSIGNED NOT NULL,
  `eligibility_status` enum('eligible','suspended','suspended_pending_decision') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'eligible',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `effective_from` datetime NOT NULL,
  `effective_until` datetime NULL DEFAULT NULL,
  `set_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mahasiswa_period_status_set_by_foreign`(`set_by` ASC) USING BTREE,
  INDEX `mps_lookup_idx`(`mahasiswa_id` ASC, `tahun_akademik_id` ASC, `effective_from` ASC) USING BTREE,
  INDEX `mps_period_status_idx`(`tahun_akademik_id` ASC, `eligibility_status` ASC) USING BTREE,
  CONSTRAINT `mahasiswa_period_status_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `mahasiswa_period_status_set_by_foreign` FOREIGN KEY (`set_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `mahasiswa_period_status_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mahasiswa_period_status
-- ----------------------------
INSERT INTO `mahasiswa_period_status` VALUES (1, 1, 2, 'suspended_pending_decision', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (2, 2, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (3, 3, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (4, 4, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (5, 5, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (6, 6, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (7, 7, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (8, 8, 2, 'suspended', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (9, 9, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (10, 10, 2, 'suspended_pending_decision', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (11, 11, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (12, 12, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (13, 13, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (14, 14, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (15, 15, 2, 'suspended', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (16, 16, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (17, 17, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (18, 18, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (19, 19, 2, 'suspended_pending_decision', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (20, 20, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (21, 21, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (22, 22, 2, 'suspended', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (23, 23, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (24, 24, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `mahasiswa_period_status` VALUES (25, 25, 2, 'eligible', 'Seeder default eligibility.', '2026-02-23 18:11:47', NULL, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mata_kuliah
-- ----------------------------
INSERT INTO `mata_kuliah` VALUES (1, 'IF301', 'Pemrograman Web Lanjut', 3, 6, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (2, 'IF305', 'Basis Data Lanjut', 3, 6, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (3, 'IF307', 'Rekayasa Perangkat Lunak', 3, 6, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (4, 'IF311', 'Kecerdasan Buatan', 2, 6, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (5, 'SI301', 'Manajemen Proyek SI', 3, 6, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (6, 'SI305', 'Audit Sistem Informasi', 3, 6, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (7, 'SI307', 'Analitik Bisnis', 2, 6, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (8, 'MN301', 'Manajemen Keuangan', 3, 6, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (9, 'MN305', 'Manajemen SDM', 3, 6, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (10, 'MN307', 'Perilaku Organisasi', 2, 6, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (11, 'TS301', 'Mekanika Tanah', 3, 6, 4, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `mata_kuliah` VALUES (12, 'TS305', 'Manajemen Konstruksi', 3, 6, 4, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

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
INSERT INTO `migrations` VALUES (20, '2026_03_04_135000_add_period_eligibility_and_decision_tables', 1);
INSERT INTO `migrations` VALUES (21, '2026_03_04_135100_upgrade_tagihan_status_to_v2', 1);
INSERT INTO `migrations` VALUES (22, '2026_03_04_140000_create_academic_rule_snapshots_table', 1);
INSERT INTO `migrations` VALUES (23, '2026_03_05_090000_upgrade_evaluasi_dosen_table_for_real_responses', 1);

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
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nilai
-- ----------------------------
INSERT INTO `nilai` VALUES (1, 7, 74.00, 71.00, 76.00, 86.00, 75.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (2, 8, 75.00, 72.00, 77.00, 87.00, 76.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (3, 10, 77.00, 74.00, 79.00, 89.00, 78.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (4, 11, 78.00, 75.00, 80.00, 90.00, 79.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (5, 23, 90.00, 87.00, 92.00, 82.00, 89.15, 'A', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (6, 24, 91.00, 88.00, 93.00, 83.00, 90.15, 'A', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (7, 34, 76.00, 68.00, 78.00, 93.00, 76.40, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (8, 35, 77.00, 69.00, 79.00, 94.00, 77.40, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (9, 36, 78.00, 70.00, 80.00, 95.00, 78.40, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (10, 51, 68.00, 85.00, 70.00, 90.00, 75.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (11, 52, 69.00, 86.00, 71.00, 91.00, 76.15, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (12, 62, 79.00, 66.00, 81.00, 81.00, 76.65, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (13, 63, 80.00, 67.00, 82.00, 82.00, 77.65, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `nilai` VALUES (14, 64, 81.00, 68.00, 83.00, 83.00, 78.65, 'B+', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pembayaran
-- ----------------------------
INSERT INTO `pembayaran` VALUES (1, 2, '2026-02-24', 2000000.00, 'va', 'TRX-PRT-2', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (2, 3, '2026-02-19', 2700000.00, 'qris', 'TRX-PD1-3', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (3, 3, '2026-03-03', 1800000.00, 'cash', 'TRX-PD2-3', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (4, 4, '2026-02-20', 3000000.00, 'cash', 'TRX-PD1-4', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (5, 4, '2026-03-01', 2000000.00, 'transfer', 'TRX-PD2-4', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (6, 6, '2026-02-23', 1750000.00, 'va', 'TRX-PRT-6', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (7, 7, '2026-02-26', 1600000.00, 'transfer', 'TRX-DSP-7', 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (8, 9, '2026-02-18', 3000000.00, 'transfer', 'TRX-PD1-9', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (9, 9, '2026-03-03', 2000000.00, 'va', 'TRX-PD2-9', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (10, 12, '2026-02-24', 2000000.00, 'cash', 'TRX-PRT-12', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (11, 13, '2026-02-22', 2700000.00, 'transfer', 'TRX-PD1-13', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (12, 13, '2026-03-01', 1800000.00, 'va', 'TRX-PD2-13', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (13, 14, '2026-02-23', 3000000.00, 'va', 'TRX-PD1-14', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (14, 14, '2026-03-02', 2000000.00, 'qris', 'TRX-PD2-14', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (15, 16, '2026-02-23', 1750000.00, 'cash', 'TRX-PRT-16', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (16, 17, '2026-02-26', 1600000.00, 'transfer', 'TRX-DSP-17', 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (17, 19, '2026-02-21', 3000000.00, 'qris', 'TRX-PD1-19', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (18, 19, '2026-03-01', 2000000.00, 'cash', 'TRX-PD2-19', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (19, 22, '2026-02-24', 2000000.00, 'va', 'TRX-PRT-22', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (20, 23, '2026-02-18', 2700000.00, 'qris', 'TRX-PD1-23', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (21, 23, '2026-03-02', 1800000.00, 'cash', 'TRX-PD2-23', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (22, 24, '2026-02-19', 3000000.00, 'cash', 'TRX-PD1-24', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `pembayaran` VALUES (23, 24, '2026-03-03', 2000000.00, 'transfer', 'TRX-PD2-24', 0, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
INSERT INTO `permissions` VALUES (1, 'dashboard.view', 'Lihat dashboard', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (2, 'krs.view', 'Lihat menu KRS', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (3, 'krs.manage', 'Isi dan simpan KRS', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (4, 'master.view', 'Lihat data master akademik', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (5, 'master.manage', 'Kelola data master akademik', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (6, 'khs.generate', 'Generate/finalisasi KHS', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (7, 'keuangan.view', 'Lihat data tagihan dan pembayaran', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (8, 'keuangan.manage', 'Kelola tagihan dan validasi pembayaran', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (9, 'users.manage', 'Kelola user', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (10, 'roles.manage', 'Kelola role', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (11, 'nilai.manage', 'Input nilai mahasiswa', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (12, 'jadwal.view', 'Lihat jadwal mengajar', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (13, 'nilai.view', 'Lihat nilai / KHS', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (14, 'ukt.view', 'Lihat status pembayaran UKT', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (15, 'mahasiswa.monitor', 'Monitoring mahasiswa bimbingan/perkuliahan', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `permissions` VALUES (16, 'audit.view', 'Lihat audit log sistem', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of program_studi
-- ----------------------------
INSERT INTO `program_studi` VALUES (1, 'Informatika', 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `program_studi` VALUES (2, 'Sistem Informasi', 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `program_studi` VALUES (3, 'Manajemen', 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);
INSERT INTO `program_studi` VALUES (4, 'Teknik Sipil', 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39', NULL, NULL);

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
INSERT INTO `role_permissions` VALUES (1, 1, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (2, 1, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (3, 1, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (4, 1, 4, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (5, 1, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (6, 1, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (7, 1, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (8, 1, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (9, 1, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (10, 1, 15, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (11, 1, 9, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (12, 1, 10, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (13, 1, 16, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (14, 2, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (15, 2, 4, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (16, 2, 5, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (17, 2, 6, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (18, 3, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (19, 3, 7, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (20, 3, 8, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (21, 4, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (22, 4, 11, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (23, 4, 12, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (24, 4, 15, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (25, 5, 1, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (26, 5, 2, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (27, 5, 3, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (28, 5, 13, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `role_permissions` VALUES (29, 5, 14, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
INSERT INTO `roles` VALUES (1, 'super_admin', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `roles` VALUES (2, 'admin_akademik', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `roles` VALUES (3, 'admin_keuangan', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `roles` VALUES (4, 'dosen', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `roles` VALUES (5, 'mahasiswa', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
INSERT INTO `sessions` VALUES ('4oosFCJkb9SJU8etsjScmZhwJgEU1QsqqrbO20vp', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiUDBnT1JzeXcxQk55UExJOHAxQWh1bklCMzZHMmVGVkFZbWNFd0VDWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjEyOiJhdXRoX3VzZXJfaWQiO2k6MTtzOjEyOiJhdXRoX3JvbGVfaWQiO2k6MTtzOjEzOiJhdXRoX2xvZ2luX2F0IjtzOjE5OiIyMDI2LTAzLTEwIDAxOjU1OjU0IjtzOjE4OiJhdXRoX2xhc3RfYWN0aXZpdHkiO2k6MTc3MzEwNzc1NDt9', 1773107756);

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
  `status` enum('open','partial','paid','disputed','void') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ukt_mahasiswa_tahun_unique`(`mahasiswa_id` ASC, `tahun_akademik_id` ASC) USING BTREE,
  INDEX `tagihan_ukt_tahun_akademik_id_foreign`(`tahun_akademik_id` ASC) USING BTREE,
  CONSTRAINT `tagihan_ukt_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ukt_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tagihan_ukt
-- ----------------------------
INSERT INTO `tagihan_ukt` VALUES (1, 1, 2, 3500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (2, 2, 2, 4000000.00, 'partial', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (3, 3, 2, 4500000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (4, 4, 2, 5000000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (5, 5, 2, 5500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (6, 6, 2, 3500000.00, 'partial', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (7, 7, 2, 4000000.00, 'disputed', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (8, 8, 2, 4500000.00, 'void', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (9, 9, 2, 5000000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (10, 10, 2, 5500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (11, 11, 2, 3500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (12, 12, 2, 4000000.00, 'partial', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (13, 13, 2, 4500000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (14, 14, 2, 5000000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (15, 15, 2, 5500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (16, 16, 2, 3500000.00, 'partial', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (17, 17, 2, 4000000.00, 'disputed', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (18, 18, 2, 4500000.00, 'void', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (19, 19, 2, 5000000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (20, 20, 2, 5500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (21, 21, 2, 3500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (22, 22, 2, 4000000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 19:08:14');
INSERT INTO `tagihan_ukt` VALUES (23, 23, 2, 4500000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (24, 24, 2, 5000000.00, 'paid', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tagihan_ukt` VALUES (25, 25, 2, 5500000.00, 'open', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tahun_akademik
-- ----------------------------
INSERT INTO `tahun_akademik` VALUES (1, '2025/2026', 'ganjil', 0, 0, '2025-10-09 18:11:39', '2025-11-09 18:11:39', '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `tahun_akademik` VALUES (2, '2025/2026', 'genap', 1, 1, '2026-02-27 18:11:39', '2026-03-30 18:11:39', '2026-03-09 18:11:39', '2026-03-09 18:11:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, 'Super Admin', 'superadmin@kampus.ac.id', NULL, '$2y$12$VQk.LRgWsHVa1Rm6WZtahunoQvNuS8A9uAxIPAlU67q7aqDgkPBl6', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (2, 2, 'Admin Akademik', 'adminakademik@kampus.ac.id', NULL, '$2y$12$0NNR0Xhlc2zVNeAUcEIzWuucGPfZoA6FIBdvt95Noca5faSd1dshO', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (3, 3, 'Admin Keuangan', 'adminkeuangan@kampus.ac.id', NULL, '$2y$12$WtL5rw2KW40TYLrqPPpDBO6Yrmy8rb6IDHjmcfx6AMH1eVuu5zEmS', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (4, 4, 'Dosen 01', 'dosen01@kampus.ac.id', NULL, '$2y$12$3EH7R5ljPTlz/hOjC0egxuI5/O0SKu7tWWnB/5DHmXhxkq.IrKdAi', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (5, 4, 'Dosen 02', 'dosen02@kampus.ac.id', NULL, '$2y$12$Igup4S2AolAqpSV7JqLi0OI5X3.ReBiYFLXxGeXEP9X.Itkw9azgu', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (6, 4, 'Dosen 03', 'dosen03@kampus.ac.id', NULL, '$2y$12$BkYdXF7magjuMo4YQpLlLu12cig8St1nS/f9f1FuHnR9e0BJuhCKW', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (7, 4, 'Dosen 04', 'dosen04@kampus.ac.id', NULL, '$2y$12$O9iaA0IqG750.XTkZkEMOe63/yCICFPbZ6ob9xI4xQ7UMyqAKgu0a', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (8, 4, 'Dosen 05', 'dosen05@kampus.ac.id', NULL, '$2y$12$POTRHj7km9UoTUuUTk1sdekmXyk9MXjJCtNPP.oWuxrFT4gOVleQK', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (9, 4, 'Dosen 06', 'dosen06@kampus.ac.id', NULL, '$2y$12$rKkGpAHhY2kLHsz8FTGcdOcjADnnOoPNCtOfcTDTPBU.e6aMS709y', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (10, 4, 'Dosen 07', 'dosen07@kampus.ac.id', NULL, '$2y$12$h.4f/m1Qo7Nu0vpu5LZ6s.xN.wgsS/YKSrQF9vcCMlgHU.Y85V2rC', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (11, 4, 'Dosen 08', 'dosen08@kampus.ac.id', NULL, '$2y$12$TIAF1ks4qeOjlpPCrjLbe.8millnryWVGkUCQap9g.FnrvZYWzSDm', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (12, 4, 'Dosen 09', 'dosen09@kampus.ac.id', NULL, '$2y$12$AG.34JZ3ti102hIC24K54uHayQ50ClBOcZqsrL0MV9mHLxC4VxiyW', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (13, 4, 'Dosen 10', 'dosen10@kampus.ac.id', NULL, '$2y$12$SiOxfAh27b08oURgee7rKOyt90XBr7BtPO2xZlPXKLbnZ6tGCMBDi', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (14, 4, 'Dosen 11', 'dosen11@kampus.ac.id', NULL, '$2y$12$dofk3NjhJ3koheb653ahNeSvxQUUsSB9/.Ex2F5uETz8bmmkAYXfO', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (15, 4, 'Dosen 12', 'dosen12@kampus.ac.id', NULL, '$2y$12$g/AWMexuCZmYR/Oo5hbkDuOP1F5Itgg63oSOLUwwzWLZJLqoYlY5K', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (16, 4, 'Dosen 13', 'dosen13@kampus.ac.id', NULL, '$2y$12$rCCI274aYx0NWCi1JyxUROd26IUKCoAG4bTVZPppOzRvhFj7b8jkG', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (17, 4, 'Dosen 14', 'dosen14@kampus.ac.id', NULL, '$2y$12$ZVuJcUxKaY/WrCkXHgBYEuyr459iekHv04Q.pOrjTu.HV7U8fFoPS', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (18, 4, 'Dosen 15', 'dosen15@kampus.ac.id', NULL, '$2y$12$E3ejhjItivOwGldhq2NdA.lfE83udi0fXlyOVSZbYCMt0Cu/W913a', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (19, 5, 'Mahasiswa 01', 'mhs01@kampus.ac.id', NULL, '$2y$12$2z/KVWJQQ/C1yUc4YH/px.bg8YhL/KH/.yxviAgUuc96I08qQqg8a', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (20, 5, 'Mahasiswa 02', 'mhs02@kampus.ac.id', NULL, '$2y$12$HRwxXkElqAMOQ/iJieXos.rp6J4JvmnWfZILJAIbq5iwPFEUS..AO', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (21, 5, 'Mahasiswa 03', 'mhs03@kampus.ac.id', NULL, '$2y$12$Cy/fM98UA0pZZcSP0/57N./pB4gbF771hpH1fJCGaqxabIAXaJrmm', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (22, 5, 'Mahasiswa 04', 'mhs04@kampus.ac.id', NULL, '$2y$12$JEqL2o0ngnnzqBk1EhfWme/4Rzt6y98.PWj64n/jYWvryU017BbIC', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (23, 5, 'Mahasiswa 05', 'mhs05@kampus.ac.id', NULL, '$2y$12$E9IypC5wGAKFDnyXnSD/Ue5ZUNKfFx.AfoPbqoWBq3ASFrlca9ZVC', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (24, 5, 'Mahasiswa 06', 'mhs06@kampus.ac.id', NULL, '$2y$12$hP87xLA9Y2DjcinGVZtmAeegdOdbciKmHrbsUunekpgQcnE7.yvP6', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (25, 5, 'Mahasiswa 07', 'mhs07@kampus.ac.id', NULL, '$2y$12$sssqMkdUTtEq43jz2OkDU.2rEtY/xNAEuqGI4sd6ejw8j3kCVgJM.', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (26, 5, 'Mahasiswa 08', 'mhs08@kampus.ac.id', NULL, '$2y$12$47odWfZDlWWWdLScbcpOBui.dNmwe7d5vK2LmXGB1MhqSzSHCalcG', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (27, 5, 'Mahasiswa 09', 'mhs09@kampus.ac.id', NULL, '$2y$12$orMiosXFS8d8TA5RdUMwNOI7kCHUwTE.X/BrWxPuJ3rlk3HHYlY3.', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (28, 5, 'Mahasiswa 10', 'mhs10@kampus.ac.id', NULL, '$2y$12$EvMZ42S7isRxZA6jicDafO.pWpnuDNQvsKM2ROtz50na5MM3CiyIK', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (29, 5, 'Mahasiswa 11', 'mhs11@kampus.ac.id', NULL, '$2y$12$VmKdin10fc6n2uCuwUc9QeX3soG.QshAqLj7dZqUuq60.tSawJ1y2', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (30, 5, 'Mahasiswa 12', 'mhs12@kampus.ac.id', NULL, '$2y$12$RIYF1O/ZRYwcdNiN.AEefOYfo5pd.Xw9Wtyz6/5NX38mhmi/TF4q2', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (31, 5, 'Mahasiswa 13', 'mhs13@kampus.ac.id', NULL, '$2y$12$g1kCoPWsQSHSKLzzKDtLM.xrW8LLwGMhXR6RfxVLm20DCEw7uMUYe', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (32, 5, 'Mahasiswa 14', 'mhs14@kampus.ac.id', NULL, '$2y$12$uu3p2OKOwideiMVK5VFHpeyIehJYNks4eM9own2Zcjv0QGFEas17.', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (33, 5, 'Mahasiswa 15', 'mhs15@kampus.ac.id', NULL, '$2y$12$u4LAW1mhlM7IuU58y/ZMT.thpZS56k8.fvXqsjVasDrklMuv7W9Hq', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (34, 5, 'Mahasiswa 16', 'mhs16@kampus.ac.id', NULL, '$2y$12$VG3rMwcV2ad3mRurrQnvH.NaC1uPF2PhfphCwcs3vtGIOmR4JITnu', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (35, 5, 'Mahasiswa 17', 'mhs17@kampus.ac.id', NULL, '$2y$12$8TQdOgJoIomtBbsF9TQ4y.KI8IKpcAaGT11jGptQkDD6Py6uoN/PO', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (36, 5, 'Mahasiswa 18', 'mhs18@kampus.ac.id', NULL, '$2y$12$wfpHBoC96efwScXZeKmUqOlAtnFRFNIhfiPn6k8MpQ3P4wjGM9ygK', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (37, 5, 'Mahasiswa 19', 'mhs19@kampus.ac.id', NULL, '$2y$12$kB0/C/f3cXJv/wtYQleS1ui6BaYLCmABNHrbiFHb3jzYvhXn.w3pe', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (38, 5, 'Mahasiswa 20', 'mhs20@kampus.ac.id', NULL, '$2y$12$bgagF7MwLvCUwFmEtYhoZ.5iY9QlbwWCkcRHgFma73Eh6a4NpnvWy', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (39, 5, 'Mahasiswa 21', 'mhs21@kampus.ac.id', NULL, '$2y$12$BT71ZKFeNJGw6f.4T2Hu4eTKUEGKs82YdggDzNmTYgrSO/zFM9m9e', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (40, 5, 'Mahasiswa 22', 'mhs22@kampus.ac.id', NULL, '$2y$12$Vjj/TeNM8CAdYX34rNFzMubVtvRAou6hLUrIF6NVbwQK2.xt0SKyW', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (41, 5, 'Mahasiswa 23', 'mhs23@kampus.ac.id', NULL, '$2y$12$0qFABBWkUkLxaCQ2AgYwF.iazGPojXGy/OQHP2qZwa/Tz.0NwoICO', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (42, 5, 'Mahasiswa 24', 'mhs24@kampus.ac.id', NULL, '$2y$12$esPkTSyMUJHhQuzpLU8dp.28DkHeFBJ6TB0iVw3rigLvJ54SmBLZW', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');
INSERT INTO `users` VALUES (43, 5, 'Mahasiswa 25', 'mhs25@kampus.ac.id', NULL, '$2y$12$sK8kJOhhmejvxcEXN4vgIuIadfHiw3iKa/W64nXVZWsydjOvlCUx6', 'aktif', NULL, '2026-03-09 18:11:39', '2026-03-09 18:11:39');

SET FOREIGN_KEY_CHECKS = 1;
