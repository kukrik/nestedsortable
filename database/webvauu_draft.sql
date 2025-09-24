/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 90300
 Source Host           : localhost:3306
 Source Schema         : qcubed-4

 Target Server Type    : MySQL
 Target Server Version : 90300
 File Encoding         : 65001

 Date: 24/09/2025 12:32:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of activity
-- ----------------------------
BEGIN;
INSERT INTO `activity` VALUES (1, 1, 'Active', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i>  Active', 1);
INSERT INTO `activity` VALUES (2, 2, 'Inactive', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i> Inactive', 1);
COMMIT;

-- ----------------------------
-- Table structure for age_categories
-- ----------------------------
DROP TABLE IF EXISTS `age_categories`;
CREATE TABLE `age_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_age` int unsigned DEFAULT NULL,
  `max_age` int unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `age_categories_ibfk_1` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_categories_ibfk_2` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_categories_ibfk_3` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of age_categories
-- ----------------------------
BEGIN;
INSERT INTO `age_categories` VALUES (2, 'U23', 20, 22, 'Alla 23-aastased mehed/ naised', 'Eesti U23 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', '2025-09-03 08:13:46', 1, 2);
INSERT INTO `age_categories` VALUES (3, 'U20', 18, 19, 'Alla 20-aastased noormehed/neiud', 'Eesti U20 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
INSERT INTO `age_categories` VALUES (4, 'U18', 16, 17, 'Alla 18-aastased noormehed/ neiud', 'Eesti U18 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', '2025-02-02 22:45:42', 1, 2);
INSERT INTO `age_categories` VALUES (5, 'U16', 14, 15, 'Alla 16-aastased poisid/ tüdrukud', 'Eesti U16 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
INSERT INTO `age_categories` VALUES (6, 'U14', 12, 13, 'Alla 14-aastased poisid/ tüdrukud', 'Eesti U14 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
INSERT INTO `age_categories` VALUES (7, 'Adults', 23, NULL, 'Üle 23aastased mehed/naised', 'Eesti rekordid', 3, 'Samantha Jones', '2025-09-03 18:43:59', '2025-09-03 18:47:02', 1, 2);
COMMIT;

-- ----------------------------
-- Table structure for age_categories_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `age_categories_editors_assn`;
CREATE TABLE `age_categories_editors_assn` (
  `age_categories_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`age_categories_id`,`user_id`),
  KEY `age_categories_id_idx` (`age_categories_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `age_categories_users_assn_1` FOREIGN KEY (`age_categories_id`) REFERENCES `age_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_categories_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of age_categories_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `age_categories_editors_assn` VALUES (2, 3);
INSERT INTO `age_categories_editors_assn` VALUES (4, 3);
COMMIT;

-- ----------------------------
-- Table structure for age_category_gender
-- ----------------------------
DROP TABLE IF EXISTS `age_category_gender`;
CREATE TABLE `age_category_gender` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `age_category_id` int unsigned DEFAULT NULL,
  `athlete_gender_id` int unsigned DEFAULT NULL,
  `gender_id` int unsigned DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `age_category_id_idx` (`age_category_id`) USING BTREE,
  KEY `athlete_gender_id_idx` (`athlete_gender_id`) USING BTREE,
  KEY `gender_id_idx` (`gender_id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `age_category_gender_ibfk_1` FOREIGN KEY (`age_category_id`) REFERENCES `age_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_ibfk_2` FOREIGN KEY (`athlete_gender_id`) REFERENCES `athlete_gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_ibfk_3` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_ibfk_4` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_ibfk_5` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_ibfk_6` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of age_category_gender
-- ----------------------------
BEGIN;
INSERT INTO `age_category_gender` VALUES (37, 2, 1, 2, 2, 'Alex Smith', '2025-02-10 21:57:38', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (38, 2, 2, 1, 2, 'Alex Smith', '2025-02-10 21:58:20', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (39, 3, 1, 4, 2, 'Alex Smith', '2025-02-10 21:59:05', '2025-09-03 08:02:33', 1, 1);
INSERT INTO `age_category_gender` VALUES (40, 3, 2, 3, 2, 'Alex Smith', '2025-02-10 21:59:33', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (41, 4, 1, 4, 2, 'Alex Smith', '2025-02-10 22:00:22', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (42, 4, 2, 3, 2, 'Alex Smith', '2025-02-10 22:00:36', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (43, 5, 1, 6, 2, 'Alex Smith', '2025-02-10 22:01:02', NULL, 1, 1);
INSERT INTO `age_category_gender` VALUES (44, 5, 2, 5, 2, 'Alex Smith', '2025-02-10 22:01:15', '2025-09-03 08:01:17', 1, 2);
INSERT INTO `age_category_gender` VALUES (45, 6, 1, 6, 2, 'Alex Smith', '2025-02-10 22:08:37', '2025-02-11 17:23:12', 1, 1);
INSERT INTO `age_category_gender` VALUES (46, 6, 2, 5, 2, 'Alex Smith', '2025-02-10 22:08:54', '2025-02-10 22:09:37', 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for age_category_gender_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `age_category_gender_editors_assn`;
CREATE TABLE `age_category_gender_editors_assn` (
  `age_category_gender_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`age_category_gender_id`,`user_id`),
  KEY `age_category_gender_id_idx` (`age_category_gender_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `age_category_gender_users_assn_1` FOREIGN KEY (`age_category_gender_id`) REFERENCES `age_category_gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `age_category_gender_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of age_category_gender_editors_assn
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for album
-- ----------------------------
DROP TABLE IF EXISTS `album`;
CREATE TABLE `album` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gallery_list_id` int unsigned DEFAULT NULL,
  `gallery_group_title_id` int unsigned DEFAULT NULL,
  `group_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `file_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '1',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `folder_id_idx` (`folder_id`) USING BTREE,
  KEY `gallery_group_title_id_idx` (`gallery_group_title_id`) USING BTREE,
  KEY `gallery_list_id_idx` (`gallery_list_id`) USING BTREE,
  KEY `file_id_idx` (`file_id`) USING BTREE,
  CONSTRAINT `album_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `album_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `album_ibfk_3` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `album_ibfk_4` FOREIGN KEY (`gallery_list_id`) REFERENCES `gallery_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1687 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of album
-- ----------------------------
BEGIN;
INSERT INTO `album` VALUES (1209, 67, 29, 'Pildigalerii', 1164, 2937, '4675153776_02d8f8e483_b.jpg', '/pildigalerii/uus-album-08-08-2026/4675153776_02d8f8e483_b.jpg', NULL, NULL, 1, '2025-08-08 22:34:43', NULL);
INSERT INTO `album` VALUES (1210, 67, 29, 'Pildigalerii', 1164, 2938, '4686233863_aeb72a24df_b.jpg', '/pildigalerii/uus-album-08-08-2026/4686233863_aeb72a24df_b.jpg', NULL, NULL, 1, '2025-08-08 22:34:43', NULL);
INSERT INTO `album` VALUES (1211, 67, 29, 'Pildigalerii', 1164, 2939, '4680076964_298f35a321_b.jpg', '/pildigalerii/uus-album-08-08-2026/4680076964_298f35a321_b.jpg', NULL, NULL, 1, '2025-08-08 22:34:43', NULL);
INSERT INTO `album` VALUES (1212, 67, 29, 'Pildigalerii', 1164, 2940, 'DSC_5177_1.jpg', '/pildigalerii/uus-album-08-08-2026/DSC_5177_1.jpg', NULL, NULL, 1, '2025-08-08 22:34:44', NULL);
INSERT INTO `album` VALUES (1213, 67, 29, 'Pildigalerii', 1164, 2941, 'DSC_5197_1.jpg', '/pildigalerii/uus-album-08-08-2026/DSC_5197_1.jpg', NULL, NULL, 1, '2025-08-08 22:34:45', NULL);
INSERT INTO `album` VALUES (1214, 67, 29, 'Pildigalerii', 1164, 2942, 'file60471593_d5a21f14.jpg', '/pildigalerii/uus-album-08-08-2026/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2025-08-08 22:34:45', NULL);
INSERT INTO `album` VALUES (1215, 67, 29, 'Pildigalerii', 1164, 2943, 'DSC_7550.jpg', '/pildigalerii/uus-album-08-08-2026/DSC_7550.jpg', NULL, NULL, 1, '2025-08-08 22:34:46', NULL);
INSERT INTO `album` VALUES (1216, 69, 30, 'Spordigalerii', 1167, 2944, 'EM-Tallin-Tag3-0023.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0023.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1217, 69, 30, 'Spordigalerii', 1167, 2945, 'EM-Tallin-Tag3-0020.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0020.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1218, 69, 30, 'Spordigalerii', 1167, 2946, 'EM-Tallin-Tag3-0034.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0034.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1219, 69, 30, 'Spordigalerii', 1167, 2947, 'EM-Tallin-Tag3-0035.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0035.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1220, 69, 30, 'Spordigalerii', 1167, 2948, 'EM-Tallin-Tag3-0036.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0036.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1221, 69, 30, 'Spordigalerii', 1167, 2949, 'EM-Tallin-Tag3-0037.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0037.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1222, 69, 30, 'Spordigalerii', 1167, 2950, 'EM-Tallin-Tag3-0038.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0038.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1223, 69, 30, 'Spordigalerii', 1167, 2951, 'EM-Tallin-Tag3-0039.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0039.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1224, 69, 30, 'Spordigalerii', 1167, 2952, 'EM-Tallin-Tag3-0044.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0044.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1225, 69, 30, 'Spordigalerii', 1167, 2953, 'EM-Tallin-Tag3-0046.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0046.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1226, 69, 30, 'Spordigalerii', 1167, 2954, 'EM-Tallin-Tag3-0048.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0048.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1227, 69, 30, 'Spordigalerii', 1167, 2955, 'EM-Tallin-Tag3-0050.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0050.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1228, 69, 30, 'Spordigalerii', 1167, 2956, 'EM-Tallin-Tag3-0057.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0057.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1229, 69, 30, 'Spordigalerii', 1167, 2957, 'EM-Tallin-Tag3-0063.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0063.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1230, 69, 30, 'Spordigalerii', 1167, 2958, 'EM-Tallin-Tag3-0065.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0065.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1231, 69, 30, 'Spordigalerii', 1167, 2959, 'EM-Tallin-Tag3-0071.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0071.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1232, 69, 30, 'Spordigalerii', 1167, 2960, 'EM-Tallin-Tag3-0077.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0077.jpg', NULL, NULL, 1, '2025-08-10 00:32:50', NULL);
INSERT INTO `album` VALUES (1233, 69, 30, 'Spordigalerii', 1167, 2961, 'EM-Tallin-Tag3-0108.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0108.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1234, 69, 30, 'Spordigalerii', 1167, 2962, 'EM-Tallin-Tag3-0109.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0109.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1235, 69, 30, 'Spordigalerii', 1167, 2963, 'EM-Tallin-Tag3-0112.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0112.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1236, 69, 30, 'Spordigalerii', 1167, 2964, 'EM-Tallin-Tag3-0127.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0127.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1237, 69, 30, 'Spordigalerii', 1167, 2965, 'EM-Tallin-Tag3-0140.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0140.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1238, 69, 30, 'Spordigalerii', 1167, 2966, 'EM-Tallin-Tag3-0150.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0150.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1239, 69, 30, 'Spordigalerii', 1167, 2967, 'EM-Tallin-Tag3-0160.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0160.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1240, 69, 30, 'Spordigalerii', 1167, 2968, 'EM-Tallin-Tag3-0174.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0174.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1241, 69, 30, 'Spordigalerii', 1167, 2969, 'EM-Tallin-Tag3-0188.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0188.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1242, 69, 30, 'Spordigalerii', 1167, 2970, 'EM-Tallin-Tag3-0202.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0202.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1243, 69, 30, 'Spordigalerii', 1167, 2971, 'EM-Tallin-Tag3-0211.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0211.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1244, 69, 30, 'Spordigalerii', 1167, 2972, 'EM-Tallin-Tag3-0216.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0216.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1245, 69, 30, 'Spordigalerii', 1167, 2973, 'EM-Tallin-Tag3-0228.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0228.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1246, 69, 30, 'Spordigalerii', 1167, 2974, 'EM-Tallin-Tag3-0235.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0235.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1247, 69, 30, 'Spordigalerii', 1167, 2975, 'EM-Tallin-Tag3-0237.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0237.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1248, 69, 30, 'Spordigalerii', 1167, 2976, 'EM-Tallin-Tag3-0247.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0247.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1249, 69, 30, 'Spordigalerii', 1167, 2977, 'EM-Tallin-Tag3-0250.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0250.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1250, 69, 30, 'Spordigalerii', 1167, 2978, 'EM-Tallin-Tag3-0252.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0252.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1251, 69, 30, 'Spordigalerii', 1167, 2979, 'EM-Tallin-Tag3-0264.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0264.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1252, 69, 30, 'Spordigalerii', 1167, 2980, 'EM-Tallin-Tag3-0268.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0268.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1253, 69, 30, 'Spordigalerii', 1167, 2981, 'EM-Tallin-Tag3-0318.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0318.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1254, 69, 30, 'Spordigalerii', 1167, 2982, 'EM-Tallin-Tag3-0326.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0326.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1255, 69, 30, 'Spordigalerii', 1167, 2983, 'EM-Tallin-Tag3-0332.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0332.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1256, 69, 30, 'Spordigalerii', 1167, 2984, 'EM-Tallin-Tag3-0352.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0352.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1257, 69, 30, 'Spordigalerii', 1167, 2985, 'EM-Tallin-Tag3-0384.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0384.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1258, 69, 30, 'Spordigalerii', 1167, 2986, 'EM-Tallin-Tag3-0386.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0386.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1259, 69, 30, 'Spordigalerii', 1167, 2987, 'EM-Tallin-Tag3-0459.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0459.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1260, 69, 30, 'Spordigalerii', 1167, 2988, 'EM-Tallin-Tag3-0483.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0483.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1261, 69, 30, 'Spordigalerii', 1167, 2989, 'EM-Tallin-Tag3-0486.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0486.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1262, 69, 30, 'Spordigalerii', 1167, 2990, 'EM-Tallin-Tag3-0490.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0490.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1263, 69, 30, 'Spordigalerii', 1167, 2991, 'EM-Tallin-Tag3-0492.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0492.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1264, 69, 30, 'Spordigalerii', 1167, 2992, 'EM-Tallin-Tag3-0493.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0493.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1265, 69, 30, 'Spordigalerii', 1167, 2993, 'EM-Tallin-Tag3-0496.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0496.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1266, 69, 30, 'Spordigalerii', 1167, 2994, 'EM-Tallin-Tag3-0498.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0498.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1267, 69, 30, 'Spordigalerii', 1167, 2995, 'EM-Tallin-Tag3-0502.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0502.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1268, 69, 30, 'Spordigalerii', 1167, 2996, 'EM-Tallin-Tag3-0683.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0683.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1269, 69, 30, 'Spordigalerii', 1167, 2997, 'EM-Tallin-Tag3-0691.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0691.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1270, 69, 30, 'Spordigalerii', 1167, 2998, 'EM-Tallin-Tag3-0696.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0696.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1271, 69, 30, 'Spordigalerii', 1167, 2999, 'EM-Tallin-Tag3-0702.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0702.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1272, 69, 30, 'Spordigalerii', 1167, 3000, 'EM-Tallin-Tag3-0707.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0707.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1273, 69, 30, 'Spordigalerii', 1167, 3001, 'EM-Tallin-Tag3-0709.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0709.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1274, 69, 30, 'Spordigalerii', 1167, 3002, 'EM-Tallin-Tag3-0711.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0711.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1275, 69, 30, 'Spordigalerii', 1167, 3003, 'EM-Tallin-Tag3-0714.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0714.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1276, 69, 30, 'Spordigalerii', 1167, 3004, 'EM-Tallin-Tag3-0721.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0721.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1277, 69, 30, 'Spordigalerii', 1167, 3005, 'EM-Tallin-Tag3-0728.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0728.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1278, 69, 30, 'Spordigalerii', 1167, 3006, 'EM-Tallin-Tag3-0733.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0733.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1279, 69, 30, 'Spordigalerii', 1167, 3007, 'EM-Tallin-Tag3-0735.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0735.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1280, 69, 30, 'Spordigalerii', 1167, 3008, 'EM-Tallin-Tag3-0736.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0736.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1281, 69, 30, 'Spordigalerii', 1167, 3009, 'EM-Tallin-Tag3-0742.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0742.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1282, 69, 30, 'Spordigalerii', 1167, 3010, 'EM-Tallin-Tag3-0743.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0743.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1283, 69, 30, 'Spordigalerii', 1167, 3011, 'EM-Tallin-Tag3-0744.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0744.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1284, 69, 30, 'Spordigalerii', 1167, 3012, 'EM-Tallin-Tag3-0745.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0745.jpg', NULL, NULL, 1, '2025-08-10 00:32:51', NULL);
INSERT INTO `album` VALUES (1285, 69, 30, 'Spordigalerii', 1167, 3013, 'EM-Tallin-Tag3-0747.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0747.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1286, 69, 30, 'Spordigalerii', 1167, 3014, 'EM-Tallin-Tag3-0748.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0748.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1287, 69, 30, 'Spordigalerii', 1167, 3015, 'EM-Tallin-Tag3-0751.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0751.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1288, 69, 30, 'Spordigalerii', 1167, 3016, 'EM-Tallin-Tag3-0759.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0759.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1289, 69, 30, 'Spordigalerii', 1167, 3017, 'EM-Tallin-Tag3-0766.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0766.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1290, 69, 30, 'Spordigalerii', 1167, 3018, 'EM-Tallin-Tag3-0773.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0773.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1291, 69, 30, 'Spordigalerii', 1167, 3019, 'EM-Tallin-Tag3-0777.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0777.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1292, 69, 30, 'Spordigalerii', 1167, 3020, 'EM-Tallin-Tag3-0785.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0785.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1293, 69, 30, 'Spordigalerii', 1167, 3021, 'EM-Tallin-Tag3-0789.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0789.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1294, 69, 30, 'Spordigalerii', 1167, 3022, 'EM-Tallin-Tag3-0790.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0790.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1295, 69, 30, 'Spordigalerii', 1167, 3023, 'EM-Tallin-Tag3-0791.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0791.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1296, 69, 30, 'Spordigalerii', 1167, 3024, 'EM-Tallin-Tag3-0793.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0793.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1297, 69, 30, 'Spordigalerii', 1167, 3025, 'EM-Tallin-Tag3-0795.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0795.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1298, 69, 30, 'Spordigalerii', 1167, 3026, 'EM-Tallin-Tag3-0855.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0855.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1299, 69, 30, 'Spordigalerii', 1167, 3027, 'EM-Tallin-Tag3-0869.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0869.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1300, 69, 30, 'Spordigalerii', 1167, 3028, 'EM-Tallin-Tag3-0894.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0894.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1301, 69, 30, 'Spordigalerii', 1167, 3029, 'EM-Tallin-Tag3-0921.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0921.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1302, 69, 30, 'Spordigalerii', 1167, 3030, 'EM-Tallin-Tag3-0929.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0929.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1303, 69, 30, 'Spordigalerii', 1167, 3031, 'EM-Tallin-Tag3-0933.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0933.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1304, 69, 30, 'Spordigalerii', 1167, 3032, 'EM-Tallin-Tag3-0939.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0939.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1305, 69, 30, 'Spordigalerii', 1167, 3033, 'EM-Tallin-Tag3-0947.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0947.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1306, 69, 30, 'Spordigalerii', 1167, 3034, 'EM-Tallin-Tag3-0970.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0970.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1307, 69, 30, 'Spordigalerii', 1167, 3035, 'EM-Tallin-Tag3-0976.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0976.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1308, 69, 30, 'Spordigalerii', 1167, 3036, 'EM-Tallin-Tag3-1005.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1005.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1309, 69, 30, 'Spordigalerii', 1167, 3037, 'EM-Tallin-Tag3-1022.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1022.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1310, 69, 30, 'Spordigalerii', 1167, 3038, 'EM-Tallin-Tag3-1045.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1045.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1311, 69, 30, 'Spordigalerii', 1167, 3039, 'EM-Tallin-Tag3-1075.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1075.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1312, 69, 30, 'Spordigalerii', 1167, 3040, 'EM-Tallin-Tag3-1130.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1130.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1313, 69, 30, 'Spordigalerii', 1167, 3041, 'EM-Tallin-Tag3-1131.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1131.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1314, 69, 30, 'Spordigalerii', 1167, 3042, 'EM-Tallin-Tag3-1137.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1137.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1315, 69, 30, 'Spordigalerii', 1167, 3043, 'EM-Tallin-Tag3-1177.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1177.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1316, 69, 30, 'Spordigalerii', 1167, 3044, 'EM-Tallin-Tag3-1185.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1185.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1317, 69, 30, 'Spordigalerii', 1167, 3045, 'EM-Tallin-Tag3-1254.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1254.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1318, 69, 30, 'Spordigalerii', 1167, 3046, 'EM-Tallin-Tag3-1257.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1257.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1319, 69, 30, 'Spordigalerii', 1167, 3047, 'EM-Tallin-Tag3-1262.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1262.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1320, 69, 30, 'Spordigalerii', 1167, 3048, 'EM-Tallin-Tag3-1278.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1278.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1321, 69, 30, 'Spordigalerii', 1167, 3049, 'EM-Tallin-Tag3-1284.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1284.jpg', NULL, NULL, 1, '2025-08-10 00:32:52', NULL);
INSERT INTO `album` VALUES (1322, 69, 30, 'Spordigalerii', 1167, 3050, 'EM-Tallin-Tag3-1285.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1285.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1323, 69, 30, 'Spordigalerii', 1167, 3051, 'EM-Tallin-Tag3-1292.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1292.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1324, 69, 30, 'Spordigalerii', 1167, 3052, 'EM-Tallin-Tag3-1295.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1295.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1325, 69, 30, 'Spordigalerii', 1167, 3053, 'EM-Tallin-Tag3-1297.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1297.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1326, 69, 30, 'Spordigalerii', 1167, 3054, 'EM-Tallin-Tag3-1298.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1298.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1327, 69, 30, 'Spordigalerii', 1167, 3055, 'EM-Tallin-Tag3-1299.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1299.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1328, 69, 30, 'Spordigalerii', 1167, 3056, 'EM-Tallin-Tag3-1301.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1301.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1329, 69, 30, 'Spordigalerii', 1167, 3057, 'EM-Tallin-Tag3-1303.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1303.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1330, 69, 30, 'Spordigalerii', 1167, 3058, 'EM-Tallin-Tag3-1305.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1305.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1331, 69, 30, 'Spordigalerii', 1167, 3059, 'EM-Tallin-Tag3-1306.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1306.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1332, 69, 30, 'Spordigalerii', 1167, 3060, 'EM-Tallin-Tag3-1310.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1310.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1333, 69, 30, 'Spordigalerii', 1167, 3061, 'EM-Tallin-Tag3-1312.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1312.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1334, 69, 30, 'Spordigalerii', 1167, 3062, 'EM-Tallin-Tag3-1313.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1313.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1335, 69, 30, 'Spordigalerii', 1167, 3063, 'EM-Tallin-Tag3-1314.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1314.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1336, 69, 30, 'Spordigalerii', 1167, 3064, 'EM-Tallin-Tag3-1315.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1315.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1337, 69, 30, 'Spordigalerii', 1167, 3065, 'EM-Tallin-Tag3-1317.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1317.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1338, 69, 30, 'Spordigalerii', 1167, 3066, 'EM-Tallin-Tag3-1318.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1318.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1339, 69, 30, 'Spordigalerii', 1167, 3067, 'EM-Tallin-Tag3-1319.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1319.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1340, 69, 30, 'Spordigalerii', 1167, 3068, 'EM-Tallin-Tag3-1320.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1320.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1341, 69, 30, 'Spordigalerii', 1167, 3069, 'EM-Tallin-Tag3-1321.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1321.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1342, 69, 30, 'Spordigalerii', 1167, 3070, 'EM-Tallin-Tag3-1322.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1322.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1343, 69, 30, 'Spordigalerii', 1167, 3071, 'EM-Tallin-Tag3-1323.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1323.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1344, 69, 30, 'Spordigalerii', 1167, 3072, 'EM-Tallin-Tag3-1325.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1325.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1345, 69, 30, 'Spordigalerii', 1167, 3073, 'EM-Tallin-Tag3-1326.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1326.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1346, 69, 30, 'Spordigalerii', 1167, 3074, 'EM-Tallin-Tag3-1327.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1327.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1347, 69, 30, 'Spordigalerii', 1167, 3075, 'EM-Tallin-Tag3-1329.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1329.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1348, 69, 30, 'Spordigalerii', 1167, 3076, 'EM-Tallin-Tag3-1330.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1330.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1349, 69, 30, 'Spordigalerii', 1167, 3077, 'EM-Tallin-Tag3-1331.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1331.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1350, 69, 30, 'Spordigalerii', 1167, 3078, 'EM-Tallin-Tag3-1333.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1333.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1351, 69, 30, 'Spordigalerii', 1167, 3079, 'EM-Tallin-Tag3-1337.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1337.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1352, 69, 30, 'Spordigalerii', 1167, 3080, 'EM-Tallin-Tag3-1340.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1340.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1353, 69, 30, 'Spordigalerii', 1167, 3081, 'EM-Tallin-Tag3-1341.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1341.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1354, 69, 30, 'Spordigalerii', 1167, 3082, 'EM-Tallin-Tag3-1345.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1345.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1355, 69, 30, 'Spordigalerii', 1167, 3083, 'EM-Tallin-Tag3-1346.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1346.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1356, 69, 30, 'Spordigalerii', 1167, 3084, 'EM-Tallin-Tag3-1348.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1348.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1357, 69, 30, 'Spordigalerii', 1167, 3085, 'EM-Tallin-Tag3-1351.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1351.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1358, 69, 30, 'Spordigalerii', 1167, 3086, 'EM-Tallin-Tag3-1352.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1352.jpg', NULL, NULL, 1, '2025-08-10 00:32:53', NULL);
INSERT INTO `album` VALUES (1359, 69, 30, 'Spordigalerii', 1167, 3087, 'EM-Tallin-Tag3-1356.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1356.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1360, 69, 30, 'Spordigalerii', 1167, 3088, 'EM-Tallin-Tag3-1365.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1365.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1361, 69, 30, 'Spordigalerii', 1167, 3089, 'EM-Tallin-Tag3-1368.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1368.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1362, 69, 30, 'Spordigalerii', 1167, 3090, 'EM-Tallin-Tag3-1370.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1370.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1363, 69, 30, 'Spordigalerii', 1167, 3091, 'EM-Tallin-Tag3-1374.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1374.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1364, 69, 30, 'Spordigalerii', 1167, 3092, 'EM-Tallin-Tag3-1376.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1376.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1365, 69, 30, 'Spordigalerii', 1167, 3093, 'EM-Tallin-Tag3-1378.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1378.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1366, 69, 30, 'Spordigalerii', 1167, 3094, 'EM-Tallin-Tag3-1379.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1379.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1367, 69, 30, 'Spordigalerii', 1167, 3095, 'EM-Tallin-Tag3-1382.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1382.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1368, 69, 30, 'Spordigalerii', 1167, 3096, 'EM-Tallin-Tag3-1384.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1384.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1369, 69, 30, 'Spordigalerii', 1167, 3097, 'EM-Tallin-Tag3-1390.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1390.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1370, 69, 30, 'Spordigalerii', 1167, 3098, 'EM-Tallin-Tag3-1394.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1394.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1371, 69, 30, 'Spordigalerii', 1167, 3099, 'EM-Tallin-Tag3-1397.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1397.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1372, 69, 30, 'Spordigalerii', 1167, 3100, 'EM-Tallin-Tag3-1401.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1401.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1373, 69, 30, 'Spordigalerii', 1167, 3101, 'EM-Tallin-Tag3-1402.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1402.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1374, 69, 30, 'Spordigalerii', 1167, 3102, 'EM-Tallin-Tag3-1404.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1404.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1375, 69, 30, 'Spordigalerii', 1167, 3103, 'EM-Tallin-Tag3-1407.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1407.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1376, 69, 30, 'Spordigalerii', 1167, 3104, 'EM-Tallin-Tag3-1411.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1411.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1377, 69, 30, 'Spordigalerii', 1167, 3105, 'EM-Tallin-Tag3-1419.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1419.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1378, 69, 30, 'Spordigalerii', 1167, 3106, 'EM-Tallin-Tag3-1421.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1421.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1379, 69, 30, 'Spordigalerii', 1167, 3107, 'EM-Tallin-Tag3-1425.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1425.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1380, 69, 30, 'Spordigalerii', 1167, 3108, 'EM-Tallin-Tag3-1428.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1428.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1381, 69, 30, 'Spordigalerii', 1167, 3109, 'EM-Tallin-Tag3-1429.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1429.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1382, 69, 30, 'Spordigalerii', 1167, 3110, 'EM-Tallin-Tag3-1431.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1431.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1383, 69, 30, 'Spordigalerii', 1167, 3111, 'EM-Tallin-Tag3-1434.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1434.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1384, 69, 30, 'Spordigalerii', 1167, 3112, 'EM-Tallin-Tag3-1436.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1436.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1385, 69, 30, 'Spordigalerii', 1167, 3113, 'EM-Tallin-Tag3-1444.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1444.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1386, 69, 30, 'Spordigalerii', 1167, 3114, 'EM-Tallin-Tag3-1453.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1453.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1387, 69, 30, 'Spordigalerii', 1167, 3115, 'EM-Tallin-Tag3-1455.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1455.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1388, 69, 30, 'Spordigalerii', 1167, 3116, 'EM-Tallin-Tag3-1457.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1457.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1389, 69, 30, 'Spordigalerii', 1167, 3117, 'EM-Tallin-Tag3-1465.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1465.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1390, 69, 30, 'Spordigalerii', 1167, 3118, 'EM-Tallin-Tag3-1476.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1476.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1391, 69, 30, 'Spordigalerii', 1167, 3119, 'EM-Tallin-Tag3-1479.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1479.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1392, 69, 30, 'Spordigalerii', 1167, 3120, 'EM-Tallin-Tag3-1483.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1483.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1393, 69, 30, 'Spordigalerii', 1167, 3121, 'EM-Tallin-Tag3-1495.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1495.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1394, 69, 30, 'Spordigalerii', 1167, 3122, 'EM-Tallin-Tag3-1498.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1498.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1395, 69, 30, 'Spordigalerii', 1167, 3123, 'EM-Tallin-Tag3-1509.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1509.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1396, 69, 30, 'Spordigalerii', 1167, 3124, 'EM-Tallin-Tag3-1510.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1510.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1397, 69, 30, 'Spordigalerii', 1167, 3125, 'EM-Tallin-Tag3-1513.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1513.jpg', NULL, NULL, 1, '2025-08-10 00:32:54', NULL);
INSERT INTO `album` VALUES (1398, 69, 30, 'Spordigalerii', 1167, 3126, 'EM-Tallin-Tag3-1514.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1514.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1399, 69, 30, 'Spordigalerii', 1167, 3127, 'EM-Tallin-Tag3-1515.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1515.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1400, 69, 30, 'Spordigalerii', 1167, 3128, 'EM-Tallin-Tag3-1516.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1516.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1401, 69, 30, 'Spordigalerii', 1167, 3129, 'EM-Tallin-Tag3-1519.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1519.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1402, 69, 30, 'Spordigalerii', 1167, 3130, 'EM-Tallin-Tag3-1520.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1520.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1403, 69, 30, 'Spordigalerii', 1167, 3131, 'EM-Tallin-Tag3-1524.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1524.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1404, 69, 30, 'Spordigalerii', 1167, 3132, 'EM-Tallin-Tag3-1526.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1526.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1405, 69, 30, 'Spordigalerii', 1167, 3133, 'EM-Tallin-Tag3-1529.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1529.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1406, 69, 30, 'Spordigalerii', 1167, 3134, 'EM-Tallin-Tag3-1532.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1532.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1407, 69, 30, 'Spordigalerii', 1167, 3135, 'EM-Tallin-Tag3-1540.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1540.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1408, 69, 30, 'Spordigalerii', 1167, 3136, 'EM-Tallin-Tag3-1541.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1541.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1409, 69, 30, 'Spordigalerii', 1167, 3137, 'EM-Tallin-Tag3-1547.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1547.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1410, 69, 30, 'Spordigalerii', 1167, 3138, 'EM-Tallin-Tag3-1550.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1550.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1411, 69, 30, 'Spordigalerii', 1167, 3139, 'EM-Tallin-Tag3-1553.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1553.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1412, 69, 30, 'Spordigalerii', 1167, 3140, 'EM-Tallin-Tag3-1556.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1556.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1413, 69, 30, 'Spordigalerii', 1167, 3141, 'EM-Tallin-Tag3-1557.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1557.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1414, 69, 30, 'Spordigalerii', 1167, 3142, 'EM-Tallin-Tag3-1558.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1558.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1415, 69, 30, 'Spordigalerii', 1167, 3143, 'EM-Tallin-Tag3-1559.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1559.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1416, 69, 30, 'Spordigalerii', 1167, 3144, 'EM-Tallin-Tag3-1560.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1560.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1417, 69, 30, 'Spordigalerii', 1167, 3145, 'EM-Tallin-Tag3-1561.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1561.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1418, 69, 30, 'Spordigalerii', 1167, 3146, 'EM-Tallin-Tag3-1562.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1562.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1419, 69, 30, 'Spordigalerii', 1167, 3147, 'EM-Tallin-Tag3-1563.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1563.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1420, 69, 30, 'Spordigalerii', 1167, 3148, 'EM-Tallin-Tag3-1565.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1565.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1421, 69, 30, 'Spordigalerii', 1167, 3149, 'EM-Tallin-Tag3-1566.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1566.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1422, 69, 30, 'Spordigalerii', 1167, 3150, 'EM-Tallin-Tag3-1567.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1567.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1423, 69, 30, 'Spordigalerii', 1167, 3151, 'EM-Tallin-Tag3-1568.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1568.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1424, 69, 30, 'Spordigalerii', 1167, 3152, 'EM-Tallin-Tag3-1574.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1574.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1425, 69, 30, 'Spordigalerii', 1167, 3153, 'EM-Tallin-Tag3-1575.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1575.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1426, 69, 30, 'Spordigalerii', 1167, 3154, 'EM-Tallin-Tag3-1578.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1578.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1427, 69, 30, 'Spordigalerii', 1167, 3155, 'EM-Tallin-Tag3-1582.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1582.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1428, 69, 30, 'Spordigalerii', 1167, 3156, 'EM-Tallin-Tag3-1584.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1584.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1429, 69, 30, 'Spordigalerii', 1167, 3157, 'EM-Tallin-Tag3-1585.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1585.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1430, 69, 30, 'Spordigalerii', 1167, 3158, 'EM-Tallin-Tag3-1591.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1591.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1431, 69, 30, 'Spordigalerii', 1167, 3159, 'EM-Tallin-Tag3-1592.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1592.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1432, 69, 30, 'Spordigalerii', 1167, 3160, 'EM-Tallin-Tag3-1596.jpg', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1596.jpg', NULL, NULL, 1, '2025-08-10 00:32:55', NULL);
INSERT INTO `album` VALUES (1679, 71, 30, 'Spordigalerii', 1170, 3664, 'EM-Tallin-Tag2-001.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-001.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1680, 71, 30, 'Spordigalerii', 1170, 3665, 'EM-Tallin-Tag2-002.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-002.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1681, 71, 30, 'Spordigalerii', 1170, 3666, 'EM-Tallin-Tag2-006.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-006.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1682, 71, 30, 'Spordigalerii', 1170, 3667, 'EM-Tallin-Tag2-011.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-011.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1683, 71, 30, 'Spordigalerii', 1170, 3668, 'EM-Tallin-Tag2-012.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-012.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1684, 71, 30, 'Spordigalerii', 1170, 3669, 'EM-Tallin-Tag2-015.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-015.jpg', NULL, NULL, 1, '2025-08-25 16:24:09', NULL);
INSERT INTO `album` VALUES (1685, 71, 30, 'Spordigalerii', 1170, 3670, 'EM-Tallin-Tag2-016.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-016.jpg', NULL, NULL, 1, '2025-08-25 16:24:10', NULL);
INSERT INTO `album` VALUES (1686, 71, 30, 'Spordigalerii', 1170, 3671, 'EM-Tallin-Tag2-020.jpg', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-020.jpg', NULL, NULL, 1, '2025-08-25 16:24:10', NULL);
COMMIT;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_id` int DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `category_id_article_fk` FOREIGN KEY (`category_id`) REFERENCES `category_of_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_content_id_article_fk` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_article_fk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of article
-- ----------------------------
BEGIN;
INSERT INTO `article` VALUES (70, 299, 'Eesti Kurtide Liidu-põhikiri', 1, '/status/eesti-kurtide-liidu-pohikiri', 2896, '2840', NULL, NULL, '<p><img alt=\"\" id=\"2840\" src=\"/project/tmp/_files/thumbnail/crop_Tiit töömõtetes.png\" style=\"height:320px; width:320px\" /></p>\n', '2024-07-03 20:43:31', '2025-09-17 01:11:38', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (81, 353, 'Uurime aadressi muutumist', NULL, '/blaaa/uurime-aadressi-muutumist', 2794, '', NULL, NULL, '<p>&nbsp;</p>\n\n<table class=\"table table-bordered table-hover table-striped\" style=\"width:100%\">\n	<tbody>\n		<tr>\n			<td style=\"text-align:center; width:25%\">\n			<h4><span style=\"font-family:Verdana,Geneva,sans-serif\">Kalenderplaan</span></h4>\n			</td>\n			<td style=\"width:25%\">&nbsp;</td>\n			<td style=\"width:25%\">&nbsp;</td>\n			<td style=\"width:25%\">&nbsp;</td>\n		</tr>\n		<tr>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n		</tr>\n		<tr>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n		</tr>\n		<tr>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n			<td>&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-10-17 02:35:53', '2025-09-12 23:20:29', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (82, 456, 'Statistika avapauk', NULL, '/statistika/statistika-avapauk', NULL, '', NULL, NULL, NULL, '2024-12-22 17:34:40', '2025-09-11 19:09:13', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (83, 550, 'Organisatsiooni kontaktandmed', 1, '/organisatsioon/organisatsiooni-kontaktandmed', 2878, '', NULL, NULL, '<table align=\"center\" border=\"0\" style=\"width:600px\">\n	<tbody>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Organisatsiooni nimi:</strong></td>\n			<td style=\"vertical-align:top\">MT&Uuml; Eesti Kurtide Liit</td>\n		</tr>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Juriidiline aadress:</strong></td>\n			<td>N&otilde;mme tee 2, 13426 Tallinn</td>\n		</tr>\n		<tr>\n			<td><strong>Telefon:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>Faks:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>SMS:</strong></td>\n			<td>+372 5218851</td>\n		</tr>\n		<tr>\n			<td><strong>E-mail:</strong></td>\n			<td>ead@<img alt=\"\" src=\"http://www.ead.ee/automatweb/images/at.png\" />ead.ee</td>\n		</tr>\n		<tr>\n			<td><strong>Registrikood:</strong></td>\n			<td>80007861</td>\n		</tr>\n		<tr>\n			<td><strong>Arveldusarve:</strong></td>\n			<td>EE891010022002532007&nbsp;SEB</td>\n		</tr>\n		<tr>\n			<td><strong>SWIFT kood (BIC):</strong></td>\n			<td>EEUHEE2X</td>\n		</tr>\n		<tr>\n			<td><strong>Asutatud:</strong></td>\n			<td>1922</td>\n		</tr>\n		<tr>\n			<td><strong>Liikmete arv:</strong></td>\n			<td>9 &uuml;hingut ja 2 organisatsiooni, 3 ettev&otilde;tet,&nbsp; 857 &uuml;ksikisikut <em>(01.09.2020. a. seisuga)</em></td>\n		</tr>\n		<tr>\n			<td><strong>Juhatuse esimees:</strong></td>\n			<td>\n			<p>Tiit Papp</p>\n			</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-12-23 23:32:25', '2025-09-12 23:20:20', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (87, 608, 'Tänitame kindlati edasi', NULL, '/parent/tanitame-kindlati-edasi', NULL, '', NULL, NULL, '<table align=\"center\" class=\"table table-bordered table-hover table-responsive\" style=\"width:50%\">\n	<caption>\n	<h3 style=\"text-align:center\">Nimekiri</h3>\n	</caption>\n	<thead>\n		<tr>\n			<th scope=\"col\">Eesnimi</th>\n			<th scope=\"col\">Perenimi</th>\n			<th scope=\"col\">Sugu</th>\n		</tr>\n	</thead>\n	<tbody>\n		<tr>\n			<td>Tiit</td>\n			<td>Papp</td>\n			<td>Mees</td>\n		</tr>\n		<tr>\n			<td>Ene</td>\n			<td>Papp</td>\n			<td>Naine</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-12-29 16:18:15', '2025-09-12 23:20:54', 1, 'John Doe', 0);
COMMIT;

-- ----------------------------
-- Table structure for articles_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `articles_editors_assn`;
CREATE TABLE `articles_editors_assn` (
  `articles_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`articles_id`,`user_id`),
  KEY `articles_id_idx` (`articles_id`) USING BTREE,
  KEY `articles_users_idx` (`user_id`),
  CONSTRAINT `articles_users_assn_1` FOREIGN KEY (`articles_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `articles_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of articles_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `articles_editors_assn` VALUES (70, 2);
INSERT INTO `articles_editors_assn` VALUES (70, 3);
INSERT INTO `articles_editors_assn` VALUES (70, 4);
INSERT INTO `articles_editors_assn` VALUES (81, 3);
INSERT INTO `articles_editors_assn` VALUES (81, 4);
INSERT INTO `articles_editors_assn` VALUES (82, 3);
INSERT INTO `articles_editors_assn` VALUES (82, 4);
INSERT INTO `articles_editors_assn` VALUES (83, 3);
INSERT INTO `articles_editors_assn` VALUES (83, 4);
INSERT INTO `articles_editors_assn` VALUES (87, 3);
INSERT INTO `articles_editors_assn` VALUES (87, 4);
COMMIT;

-- ----------------------------
-- Table structure for athlete_gender
-- ----------------------------
DROP TABLE IF EXISTS `athlete_gender`;
CREATE TABLE `athlete_gender` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of athlete_gender
-- ----------------------------
BEGIN;
INSERT INTO `athlete_gender` VALUES (1, 'Naine');
INSERT INTO `athlete_gender` VALUES (2, 'Mees');
COMMIT;

-- ----------------------------
-- Table structure for athletes
-- ----------------------------
DROP TABLE IF EXISTS `athletes`;
CREATE TABLE `athletes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `athlete_gender_id` int unsigned DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `athlete_gender_id_idx` (`athlete_gender_id`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  KEY `id` (`id`,`athlete_gender_id`),
  CONSTRAINT `athletes_ibfk_1` FOREIGN KEY (`athlete_gender_id`) REFERENCES `athlete_gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `athletes_ibfk_2` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `athletes_ibfk_3` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `athletes_ibfk_4` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of athletes
-- ----------------------------
BEGIN;
INSERT INTO `athletes` VALUES (1, 'Kairit', 'Olenko', '1985-12-21', 1, 1, 'John Doe', '2025-01-21 17:23:32', '2025-02-10 01:12:48', 1, 2);
INSERT INTO `athletes` VALUES (12, 'Rinat', 'Raisp', '2001-05-25', 2, 1, 'John Doe', '2025-01-24 22:38:52', '2025-01-28 22:06:30', 1, 2);
INSERT INTO `athletes` VALUES (13, 'Ilvi', 'Vare', '1970-07-03', 1, 1, 'John Doe', '2025-01-25 00:32:48', '2025-09-03 08:00:58', 2, 1);
INSERT INTO `athletes` VALUES (17, 'Tanel', 'Visnap', '1998-09-23', 2, 1, 'John Doe', '2025-01-25 00:47:33', '2025-02-10 15:41:01', 1, 2);
INSERT INTO `athletes` VALUES (22, 'Jörgen', 'Liiv  (Uudelepp)', '1991-04-12', 1, 2, 'Alex Smith', '2025-01-25 04:05:31', '2025-09-06 18:50:46', 1, 2);
INSERT INTO `athletes` VALUES (23, 'Annely', 'Ojastu', '1960-08-10', 1, 2, 'Alex Smith', '2025-01-25 04:54:02', '2025-02-06 23:43:58', 1, 2);
INSERT INTO `athletes` VALUES (25, 'Emilija', 'Manninen', '1981-01-22', 1, 2, 'Alex Smith', '2025-01-25 05:00:49', '2025-09-05 22:42:57', 2, 1);
INSERT INTO `athletes` VALUES (43, 'Ene', 'Papp', '1958-05-14', 1, 3, 'Samantha Jones', '2025-02-06 15:13:13', '2025-02-21 10:51:24', 1, 1);
INSERT INTO `athletes` VALUES (47, 'Sirle', 'Papp', '1988-04-28', 1, 3, 'Samantha Jones', '2025-02-10 15:40:31', '2025-02-21 10:50:28', 1, 2);
INSERT INTO `athletes` VALUES (58, 'Tiit', 'Papp', '1958-03-14', 2, 3, 'Samantha Jones', '2025-09-06 00:38:11', '2025-09-06 18:50:17', 2, 1);
COMMIT;

-- ----------------------------
-- Table structure for athletes_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `athletes_editors_assn`;
CREATE TABLE `athletes_editors_assn` (
  `athletes_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`athletes_id`,`user_id`),
  KEY `athletes_id_idx` (`athletes_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `athletes_users_assn_1` FOREIGN KEY (`athletes_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `athletes_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of athletes_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `athletes_editors_assn` VALUES (1, 3);
INSERT INTO `athletes_editors_assn` VALUES (12, 3);
INSERT INTO `athletes_editors_assn` VALUES (13, 3);
INSERT INTO `athletes_editors_assn` VALUES (17, 3);
INSERT INTO `athletes_editors_assn` VALUES (22, 3);
INSERT INTO `athletes_editors_assn` VALUES (23, 3);
INSERT INTO `athletes_editors_assn` VALUES (25, 3);
COMMIT;

-- ----------------------------
-- Table structure for board
-- ----------------------------
DROP TABLE IF EXISTS `board`;
CREATE TABLE `board` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int unsigned DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `board_id` int unsigned DEFAULT NULL,
  `board_id_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `areas_responsibility` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `board_id_idx` (`board_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `board_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `board_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `board_ibfk_3` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of board
-- ----------------------------
BEGIN;
INSERT INTO `board` VALUES (1, 2806, 2806, 377, 7, 'Juhatus', 0, 'Tiit Papp', 'Juhatuse esimees', 'Liidu esindamine, juhatuse töö korraldamine, üldjuhtimine', NULL, NULL, '', '+372 521 8851', NULL, NULL, 'ead@ead.ee', NULL, 1, '2024-11-01 21:50:11', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (2, 2805, 2805, 377, 7, 'Juhatus', 1, 'Sirle Papp', 'Juhatuse liige', 'Meedia, haridus, töö noortega\n', NULL, NULL, '', '+372 5331 7152', NULL, NULL, 'sirlepapp@gmail.com', NULL, 1, '2024-11-02 00:41:19', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (6, 2804, 2804, 377, 7, 'Juhatus', 2, 'Riina Kuusk', 'Juhatuse aseesimees', 'Tööhõive, töö pensionäridega, esimehe äraolekul liidu esindamine, juhatuse töö korraldamine\n\n', NULL, NULL, '', '+372 5650 3051', NULL, NULL, 'riinak61@gmail.com', NULL, 1, '2024-11-02 19:43:46', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (7, 2801, 2801, 377, 7, 'Juhatus', 3, 'Helle Sass', 'Juhatuse liige', 'Kultuuritöö, liidu esindamine Eesti Puuetega Inimeste Kojas', NULL, NULL, '+372 5399 7837', '+372 5399 7837', NULL, NULL, 'helle.sass@gmail.com', NULL, 1, '2024-11-02 19:50:30', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (8, 2802, 2802, 377, 7, 'Juhatus', 4, 'Janis Golubenkov', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-11-02 19:58:05', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (10, 2803, 2803, 377, 7, 'Juhatus', 5, 'Mati Kartus', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-11-02 20:52:28', '2025-08-25 17:26:12');
INSERT INTO `board` VALUES (13, 2800, 2800, 378, 8, 'Kultuuri juhatus', 1, 'Jakob Hurd', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-11-02 22:34:12', '2025-09-16 17:32:56');
INSERT INTO `board` VALUES (15, 2798, 2798, NULL, 8, 'Kultuuri juhatus', 0, 'Lisse', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-08 16:51:41', '2025-09-16 17:33:08');
COMMIT;

-- ----------------------------
-- Table structure for board_options
-- ----------------------------
DROP TABLE IF EXISTS `board_options`;
CREATE TABLE `board_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `settings_id` int unsigned DEFAULT NULL,
  `input_key` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `activity_status` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`activity_status`) USING BTREE,
  KEY `boards_settings_id_idx` (`settings_id`) USING BTREE,
  CONSTRAINT `boards_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `status_ibfk` FOREIGN KEY (`activity_status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of board_options
-- ----------------------------
BEGIN;
INSERT INTO `board_options` VALUES (1, 7, 1, 'Fullname', 0, 1);
INSERT INTO `board_options` VALUES (2, 7, 2, 'Position', 1, 1);
INSERT INTO `board_options` VALUES (3, 7, 3, 'Areas responsibility', 2, 1);
INSERT INTO `board_options` VALUES (4, 7, 4, 'Interests and hobbies', 6, 2);
INSERT INTO `board_options` VALUES (5, 7, 5, 'Description', 7, 2);
INSERT INTO `board_options` VALUES (6, 7, 6, 'Telephone', 4, 1);
INSERT INTO `board_options` VALUES (7, 7, 7, 'SMS', 3, 1);
INSERT INTO `board_options` VALUES (8, 7, 8, 'Fax', 9, 2);
INSERT INTO `board_options` VALUES (9, 7, 9, 'Address', 8, 2);
INSERT INTO `board_options` VALUES (10, 7, 10, 'Email', 5, 1);
INSERT INTO `board_options` VALUES (11, 7, 11, 'Website', 10, 2);
INSERT INTO `board_options` VALUES (12, 8, 1, 'Fullname', 0, 1);
INSERT INTO `board_options` VALUES (13, 8, 2, 'Position', 1, 1);
INSERT INTO `board_options` VALUES (14, 8, 3, 'Areas responsibility', 2, 1);
INSERT INTO `board_options` VALUES (15, 8, 4, 'Interests and hobbies', 3, 2);
INSERT INTO `board_options` VALUES (16, 8, 5, 'Description', 4, 2);
INSERT INTO `board_options` VALUES (17, 8, 6, 'Telephone', 5, 2);
INSERT INTO `board_options` VALUES (18, 8, 7, 'SMS', 6, 2);
INSERT INTO `board_options` VALUES (19, 8, 8, 'Fax', 7, 2);
INSERT INTO `board_options` VALUES (20, 8, 9, 'Address', 8, 2);
INSERT INTO `board_options` VALUES (21, 8, 10, 'Email', 9, 2);
INSERT INTO `board_options` VALUES (22, 8, 11, 'Website', 10, 2);
INSERT INTO `board_options` VALUES (23, 9, 1, 'Fullname', 0, 1);
INSERT INTO `board_options` VALUES (24, 9, 2, 'Position', 1, 2);
INSERT INTO `board_options` VALUES (25, 9, 3, 'Areas responsibility', 2, 2);
INSERT INTO `board_options` VALUES (26, 9, 4, 'Interests and hobbies', 3, 2);
INSERT INTO `board_options` VALUES (27, 9, 5, 'Description', 4, 2);
INSERT INTO `board_options` VALUES (28, 9, 6, 'Telephone', 5, 2);
INSERT INTO `board_options` VALUES (29, 9, 7, 'SMS', 6, 2);
INSERT INTO `board_options` VALUES (30, 9, 8, 'Fax', 7, 2);
INSERT INTO `board_options` VALUES (31, 9, 9, 'Address', 8, 2);
INSERT INTO `board_options` VALUES (32, 9, 10, 'Email', 9, 2);
INSERT INTO `board_options` VALUES (33, 9, 11, 'Website', 10, 2);
COMMIT;

-- ----------------------------
-- Table structure for boards_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `boards_editors_assn`;
CREATE TABLE `boards_editors_assn` (
  `board_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`board_id`,`user_id`),
  KEY `board_id_idx` (`board_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `board_settings_users_assn_1` FOREIGN KEY (`board_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `board_settings_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of boards_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `boards_editors_assn` VALUES (7, 2);
INSERT INTO `boards_editors_assn` VALUES (7, 3);
INSERT INTO `boards_editors_assn` VALUES (7, 4);
INSERT INTO `boards_editors_assn` VALUES (8, 3);
INSERT INTO `boards_editors_assn` VALUES (8, 4);
INSERT INTO `boards_editors_assn` VALUES (9, 4);
COMMIT;

-- ----------------------------
-- Table structure for boards_settings
-- ----------------------------
DROP TABLE IF EXISTS `boards_settings`;
CREATE TABLE `boards_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `board_locked` int unsigned DEFAULT '0',
  `allowed_uploading` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `allowed_uploading_idx` (`allowed_uploading`) USING BTREE,
  CONSTRAINT `boards_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `boards_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `boards_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `boards_settings_ibfk_4` FOREIGN KEY (`allowed_uploading`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of boards_settings
-- ----------------------------
BEGIN;
INSERT INTO `boards_settings` VALUES (7, 'Juhatus', 'Eesti Kurtide Liidu juhatus 2018 - 2023', 1, 1, 377, '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023', '2024-10-30 22:19:29', '2025-09-17 01:02:15', 1, 'John Doe', 1, 1);
INSERT INTO `boards_settings` VALUES (8, 'Kultuuri juhatus', 'Kultuuri juhatus 2023 - 2028', 1, 1, 378, '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028', '2024-11-01 01:38:19', '2025-09-17 13:15:39', 1, 'John Doe', 1, 2);
INSERT INTO `boards_settings` VALUES (9, 'Spordi juhatus', 'Spordi juhatus 2023 - 2028', 1, 1, 379, '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028', '2024-11-01 13:21:14', '2025-08-20 15:29:20', 1, 'John Doe', 0, 1);
COMMIT;

-- ----------------------------
-- Table structure for category_of_article
-- ----------------------------
DROP TABLE IF EXISTS `category_of_article`;
CREATE TABLE `category_of_article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_1` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of category_of_article
-- ----------------------------
BEGIN;
INSERT INTO `category_of_article` VALUES (1, 'Education', 1, '2020-05-30 10:00:00', '2025-08-31 16:57:37');
INSERT INTO `category_of_article` VALUES (2, 'Culture', 2, '2020-05-30 10:00:00', '2024-11-15 19:17:40');
INSERT INTO `category_of_article` VALUES (3, 'Sport', 2, '2020-05-30 10:00:44', '2024-07-31 12:07:05');
INSERT INTO `category_of_article` VALUES (4, 'History', 2, '2020-05-30 10:00:44', '2024-07-31 12:07:09');
INSERT INTO `category_of_article` VALUES (5, 'Varia', 2, '2020-05-30 10:00:44', '2024-07-31 12:08:32');
INSERT INTO `category_of_article` VALUES (6, 'Info', 2, '2021-06-29 22:10:57', '2025-08-11 19:24:46');
INSERT INTO `category_of_article` VALUES (8, 'Politics', 2, '2021-06-29 22:23:59', '2025-08-31 17:37:19');
COMMIT;

-- ----------------------------
-- Table structure for category_of_news
-- ----------------------------
DROP TABLE IF EXISTS `category_of_news`;
CREATE TABLE `category_of_news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `news_category_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  KEY `news_category_locked_idx` (`news_category_locked`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_2` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of category_of_news
-- ----------------------------
BEGIN;
INSERT INTO `category_of_news` VALUES (2, 'Life', 1, '2020-09-12 11:00:00', '2025-09-19 13:00:16', 0);
INSERT INTO `category_of_news` VALUES (3, 'Education', 1, '2020-09-12 11:00:00', '2025-09-15 18:50:51', 0);
INSERT INTO `category_of_news` VALUES (4, 'Business', 2, '2020-09-13 00:00:00', '2024-07-31 11:43:08', 0);
INSERT INTO `category_of_news` VALUES (5, 'Health', 2, '2020-08-01 21:29:00', '2024-07-31 11:43:12', 0);
INSERT INTO `category_of_news` VALUES (12, 'Sport', 2, '2024-05-16 00:06:15', '2024-07-31 11:43:16', 0);
INSERT INTO `category_of_news` VALUES (17, 'Politics', 1, '2024-08-23 21:44:35', '2025-09-19 13:00:38', 0);
COMMIT;

-- ----------------------------
-- Table structure for content_entities
-- ----------------------------
DROP TABLE IF EXISTS `content_entities`;
CREATE TABLE `content_entities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of content_entities
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for content_type
-- ----------------------------
DROP TABLE IF EXISTS `content_type`;
CREATE TABLE `content_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tabs_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_names` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home page', NULL, 'home', 'Edit homepage', 'HomeEditPanel', 1);
INSERT INTO `content_type` VALUES (2, 'Article', 'Article', 'article', 'Edit article', 'ArticleEditPanel', 1);
INSERT INTO `content_type` VALUES (3, 'News', 'News', 'news', 'Edit news', 'NewsEditPanel', 1);
INSERT INTO `content_type` VALUES (4, 'Gallery', 'Gallery', 'gallery', 'Edit gallery', 'GalleryEditPanel', 1);
INSERT INTO `content_type` VALUES (5, 'Events calendar', 'EventsCalendar', 'events_calendar', 'Edit events calendar', 'EventsCalendarEditPanel', 1);
INSERT INTO `content_type` VALUES (6, 'Sports calendar', 'SportsCalendar', 'sports_calendar', 'Edit sports calendar', 'SportsCalendarEditPanel', 1);
INSERT INTO `content_type` VALUES (7, 'Internal page link', NULL, NULL, 'Edit internal page link', 'InternalPageEditPanel', 1);
INSERT INTO `content_type` VALUES (8, 'Redirecting link', NULL, NULL, 'Edit redirecting link', 'RedirectingEditPanel', 1);
INSERT INTO `content_type` VALUES (9, 'Placeholder', NULL, NULL, 'Edit placeholder', 'PlaceholderEditPanel', 0);
INSERT INTO `content_type` VALUES (10, 'Sports areas', 'SportsAreas', 'sports_areas', 'Edit sports areas', 'SportsAreasEditPanel', 1);
INSERT INTO `content_type` VALUES (11, 'Board', 'Board', 'board', 'Edit board', 'BoardEditPanel', 1);
INSERT INTO `content_type` VALUES (12, 'Members', 'Members', 'members', 'Edit members', 'MembersEditPanel', 1);
INSERT INTO `content_type` VALUES (13, 'Videos', 'Videos', 'videos', 'Edit videos', 'VideosEditPanel', 1);
INSERT INTO `content_type` VALUES (14, 'Statistics (Records)', 'Records', 'statistics', 'Edit record statistics', 'RecordStatisticsEditPanel', 1);
INSERT INTO `content_type` VALUES (15, 'Statistics (Rankings)', 'Rankings', 'statistics', 'Edit ranking statistics', 'RankingsStatisticsPanel', 1);
INSERT INTO `content_type` VALUES (16, 'Statistics (Achievements)', 'Achievements', 'statistics', 'Edit achievement statistics', 'AchievementStatisticsPanel', 1);
INSERT INTO `content_type` VALUES (17, 'Links', 'Links', 'links', 'Edit links', 'LinksEditPanel', 1);
COMMIT;

-- ----------------------------
-- Table structure for content_types_management
-- ----------------------------
DROP TABLE IF EXISTS `content_types_management`;
CREATE TABLE `content_types_management` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type` int unsigned NOT NULL,
  `view_type` int unsigned NOT NULL,
  `table_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_type_id_idx` (`content_type`) USING BTREE,
  KEY `view_type_id_idx` (`view_type`) USING BTREE,
  KEY `id` (`id`,`content_name`),
  KEY `content_name` (`content_name`),
  CONSTRAINT `content_type_id_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `view_type_id_fk` FOREIGN KEY (`view_type`) REFERENCES `view_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of content_types_management
-- ----------------------------
BEGIN;
INSERT INTO `content_types_management` VALUES (1, 'Home view', 1, 1, NULL, 'home');
INSERT INTO `content_types_management` VALUES (2, 'Article detail view', 2, 3, 'Article', 'article');
INSERT INTO `content_types_management` VALUES (3, 'News list view', 3, 2, 'News', 'news');
INSERT INTO `content_types_management` VALUES (4, 'News detail view', 3, 3, 'News', 'news');
INSERT INTO `content_types_management` VALUES (5, 'Gallery list view', 4, 2, 'Gallery', 'gallery');
INSERT INTO `content_types_management` VALUES (6, 'Gallery detail view', 4, 3, 'Gallery', 'gallery');
INSERT INTO `content_types_management` VALUES (7, 'Events calerdar list view', 5, 2, 'EventsCalendar', 'events_calendar');
INSERT INTO `content_types_management` VALUES (8, 'Events calendar detail view', 5, 3, 'EventsCalendar', 'events_calendar');
INSERT INTO `content_types_management` VALUES (9, 'Sports calendar list view', 6, 2, 'SportsCalendar', 'sports_calendar');
INSERT INTO `content_types_management` VALUES (10, 'Sports calendar detail view', 6, 3, 'SportsCalendar', 'sports_calendar');
INSERT INTO `content_types_management` VALUES (11, 'Sports areas detail view', 10, 3, 'SportsAreas', 'sports_areas');
INSERT INTO `content_types_management` VALUES (12, 'Board detail view', 11, 3, 'Board', 'board');
INSERT INTO `content_types_management` VALUES (13, 'Members detail view', 12, 3, 'Members', 'members');
INSERT INTO `content_types_management` VALUES (14, 'Videos detail view', 13, 3, 'Videos', 'videos');
INSERT INTO `content_types_management` VALUES (15, 'Statistics (Records) detail view', 14, 3, 'Records', 'statistics');
INSERT INTO `content_types_management` VALUES (16, 'Statistics (Rankings) detail view', 15, 3, 'Rankings', 'statistics');
INSERT INTO `content_types_management` VALUES (17, 'Statistics (Achievements) detail view', 16, 3, 'Achievements', 'statistics');
INSERT INTO `content_types_management` VALUES (18, 'Links detail view', 17, 3, 'Links', 'links');
COMMIT;

-- ----------------------------
-- Table structure for date_and_time_formats
-- ----------------------------
DROP TABLE IF EXISTS `date_and_time_formats`;
CREATE TABLE `date_and_time_formats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `display_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calendar_date_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calendar_time_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calendar_show_meridian` tinyint(1) DEFAULT '0',
  `is_enabled` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_3` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of date_and_time_formats
-- ----------------------------
BEGIN;
INSERT INTO `date_and_time_formats` VALUES (1, '31.12.2001 23:59:00', 'DD.MM.YYYY', 'hhhh:mm:ss', 'dd.mm.yyyy', ' hh:ii', 0, 1);
INSERT INTO `date_and_time_formats` VALUES (2, '31.12.2001 23:59:00', 'DD.MM.YYYY', 'hhhh:mm:ss', 'dd.mm.yyyy', 'hh:ii', 0, 1);
INSERT INTO `date_and_time_formats` VALUES (3, '31/12/2001 23:59:00', 'DD/MM/YYYY', 'hhhh:mm:ss', 'dd/mm/yyyy', 'hh:ii', 0, 1);
INSERT INTO `date_and_time_formats` VALUES (4, '12/31/2001 23:59:00', 'MM/DD/YYYY', 'hhhh:mm:ss', 'mm/dd/yyyy', 'hh:ii', 0, 1);
INSERT INTO `date_and_time_formats` VALUES (5, '31/12/2001 11:59 pm', 'DD/MM/YYYY', 'hh:mm z', 'dd/mm/yyyy', 'HH:ii p', 1, 1);
INSERT INTO `date_and_time_formats` VALUES (6, '12/31/2001 11:59 pm', 'DD/MM/YYYY', 'hh:mm z', 'dd/mm/yyyy', 'HH:ii p', 1, 1);
INSERT INTO `date_and_time_formats` VALUES (7, '31-12-2001 11:59 pm', 'DD-MM-YYYY', 'hh:mm z', 'dd-mm-yyyy', 'HH:ii p', 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for event_files
-- ----------------------------
DROP TABLE IF EXISTS `event_files`;
CREATE TABLE `event_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `events_calendar_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `files_id_idx` (`files_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `events_calendar_group_id_idx` (`events_calendar_group_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `event_files_ibfk_1` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_files_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of event_files
-- ----------------------------
BEGIN;
INSERT INTO `event_files` VALUES (26, 69, 400, 2025, 'Eesti viipekeele staatus ja kasutamine', 2833, '2025-09-14 22:28:11', '2025-09-14 22:28:27', 1);
INSERT INTO `event_files` VALUES (27, 69, 400, 2025, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1', 3404, '2025-09-14 22:28:22', '2025-09-22 12:45:55', 2);
INSERT INTO `event_files` VALUES (28, 90, 400, 2025, '2013 EKSL sisekj_juhend1', 2756, '2025-09-22 18:19:52', '2025-09-22 18:20:00', 1);
COMMIT;

-- ----------------------------
-- Table structure for events_calendar
-- ----------------------------
DROP TABLE IF EXISTS `events_calendar`;
CREATE TABLE `events_calendar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `events_changes_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_title_id` int unsigned DEFAULT NULL,
  `events_group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_group_id` int unsigned DEFAULT NULL,
  `target_group_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_place` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `beginning_event` date DEFAULT NULL,
  `end_event` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `information` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `schedule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `instruction_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_target_type_id` int unsigned DEFAULT NULL,
  `facebook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `facebook_target_type_id` int unsigned DEFAULT NULL,
  `instagram_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_target_type_id` int unsigned DEFAULT NULL,
  `organizers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `target_group_id_idx` (`target_group_id`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `website_target_type_id_idx` (`website_target_type_id`) USING BTREE,
  KEY `facebook_target_type_id_idx` (`facebook_target_type_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  KEY `menu_content_group_title_id_idx` (`menu_content_group_title_id`) USING BTREE,
  KEY `events_changes_id_idx` (`events_changes_id`) USING BTREE,
  KEY `instagram_target_type_id_idx` (`instagram_target_type_id`) USING BTREE,
  CONSTRAINT `events_calendar_fk_1` FOREIGN KEY (`website_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_10` FOREIGN KEY (`instagram_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_3` FOREIGN KEY (`target_group_id`) REFERENCES `target_group_of_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_4` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_5` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_6` FOREIGN KEY (`facebook_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_7` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_8` FOREIGN KEY (`menu_content_group_title_id`) REFERENCES `events_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_fk_9` FOREIGN KEY (`events_changes_id`) REFERENCES `events_changes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_calendar
-- ----------------------------
BEGIN;
INSERT INTO `events_calendar` VALUES (69, 4, 400, 6, 'Esimene kalender', NULL, NULL, 2794, NULL, '', '', 2025, 'Kurtide päev Tallinnas', '/pensionaride-sundmuste-kalender/esimene-kalender/2025/kurtide-paev-tallinnas', 'Nõmme tee 2, Tallinn', '2025-09-27', NULL, NULL, NULL, '<p>Head kurtide p&auml;eva! Kas v&auml;ga tore?</p>\n', '', NULL, '', NULL, '', NULL, '', NULL, 'Sirle Papp', '456789', 'sirlepapp@ead.ee', 1, 'John Doe', '2025-09-14 22:26:21', '2025-09-22 17:46:18', 1);
INSERT INTO `events_calendar` VALUES (81, NULL, 400, 6, 'Esimene kalender', NULL, NULL, NULL, NULL, '', '', 2024, 'Talkuri MV Discgolfis – tulemused ja kokkuvõte', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/talkuri-mv-discgolfis-–-tulemused-ja-kokkuvote', '', NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', 1, 'John Doe', '2025-09-22 13:13:01', '2025-09-22 17:46:01', 2);
INSERT INTO `events_calendar` VALUES (89, NULL, 400, 6, 'Esimene kalender', NULL, NULL, 1121, NULL, '', '', 2024, 'Eesti kurtide meistrivõistlused koroonas', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/eesti-kurtide-meistrivoistlused-koroonas', 'Tallinn', '2025-09-17', NULL, NULL, NULL, '<p>Eesti kurtide meistriv&otilde;istlused koroonas toimusid P&auml;rnus. Tegemist oli t&auml;htsa s&uuml;ndmusega &ndash; meeste arvestuses m&auml;ngiti meistritiitlile juba 30. ja naiste seas 23. korda. See on pikk ja uhke traditsioon, mis kestab t&auml;naseni.</p>\n\n<p>Kokku osales 17 m&auml;ngijat eri Eesti paigust &ndash; 14 meest ja 3 naist.</p>\n\n<p>Meeste arvestuses osales 14 m&auml;ngijat. Tihedas konkurentsis tuli meistriks kaotuseta Andrus Hakk (P&auml;rnu KSS Eero). Teise koha saavutas Janek Luha&auml;&auml;r (V&otilde;rumaa K&Uuml;) ning kolmanda koha p&auml;lvis Leoni Saar (P&auml;rnu KSS Eero)</p>\n\n<p>Naiste arvestuses oli sel aastal kahjuks osalejaid v&auml;he &ndash; ainult kolm m&auml;ngijat. V&otilde;itjaks tuli Monika Matsina (Tallinna KS Talkur), teise koha saavutas Triin Vilgats (P&auml;rnu KSS Eero) ning kolmandaks j&auml;i Ljudmila Mikson (Tallinna KS Talkur).</p>\n\n<p>T&auml;name k&otilde;iki osalejaid, korraldajaid ja toetajaid. Kohtume j&auml;lle j&auml;rgmistel v&otilde;istlustel!</p>\n\n<p>Tulemused</p>\n', '', NULL, '', NULL, '', NULL, '', NULL, 'fghjkl', '3456789', 'blaa@blaa.ee', 1, 'John Doe', '2025-09-22 17:14:29', '2025-09-22 18:23:26', 1);
INSERT INTO `events_calendar` VALUES (90, 5, 400, 6, 'Esimene kalender', NULL, NULL, NULL, NULL, '', '', 2025, 'Eesti kurtide võrkpalli MV toimusid Tallinnas', 'Esimene kalender/2025/eesti-kurtide-vorkpalli-mv-toimusid-tallinnas', 'Tallinn', '2025-09-17', NULL, NULL, NULL, '<p>Laup&auml;eval 15.m&auml;rtsil toimusid Lillek&uuml;la G&uuml;mnaasiumi v&otilde;imlas 2025.a. Eesti kurtide v&otilde;rkpalli MV.</p>\n\n<p>Seekord osales kolm v&otilde;istkonda. V&otilde;itis KSS Talkuri v&otilde;sitkond, teiseks j&auml;i KSS Eero v&otilde;istkond ning kolmanda koha saavutas KSS Talkur/Kaar &uuml;hisv&otilde;istkond.</p>\n\n<p>Tulemused</p>\n', '', NULL, '', NULL, '', NULL, '', NULL, 'dfghjkl ghjkl', '3456789o', 'blaa@blaa.com', 1, 'John Doe', '2025-09-22 18:18:17', '2025-09-22 18:22:53', 1);
COMMIT;

-- ----------------------------
-- Table structure for events_calendar_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `events_calendar_editors_assn`;
CREATE TABLE `events_calendar_editors_assn` (
  `events_calendar_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`events_calendar_id`,`user_id`),
  KEY `events_calendar_id_idx` (`events_calendar_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `events_calendar_users_assn_1` FOREIGN KEY (`events_calendar_id`) REFERENCES `events_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_calendar_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_calendar_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `events_calendar_editors_assn` VALUES (89, 3);
INSERT INTO `events_calendar_editors_assn` VALUES (90, 3);
COMMIT;

-- ----------------------------
-- Table structure for events_changes
-- ----------------------------
DROP TABLE IF EXISTS `events_changes`;
CREATE TABLE `events_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `events_change_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `events_change_locked_idx` (`events_change_locked`) USING BTREE,
  CONSTRAINT `events_chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_changes
-- ----------------------------
BEGIN;
INSERT INTO `events_changes` VALUES (4, 'Uuendatud', '2024-09-22 16:40:09', '2025-08-26 20:38:59', 1, 1);
INSERT INTO `events_changes` VALUES (5, 'Täiendatud', '2024-09-22 16:40:30', '2025-08-31 19:05:32', 1, 1);
INSERT INTO `events_changes` VALUES (6, 'Edasi lükatud', '2024-09-22 16:40:53', '2025-09-01 21:30:33', 2, 0);
COMMIT;

-- ----------------------------
-- Table structure for events_settings
-- ----------------------------
DROP TABLE IF EXISTS `events_settings`;
CREATE TABLE `events_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `events_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `events_locked_idx` (`events_locked`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `events_status_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `is_reserved_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_settings
-- ----------------------------
BEGIN;
INSERT INTO `events_settings` VALUES (1, 'Sündmuste kalender', 'Ajakava', '/pensionaride-sundmuste-kalender/sundmuste-kalender', 1, 1, 336, '2024-09-18 16:00:00', '2025-09-22 18:24:03', 0);
INSERT INTO `events_settings` VALUES (6, 'Esimene kalender', '', '/pensionaride-sundmuste-kalender/esimene-kalender', 1, 1, 400, '2024-12-03 14:43:49', '2025-09-22 18:24:03', 1);
INSERT INTO `events_settings` VALUES (7, 'Pensionäride sündmuste kalender', NULL, '/pensionaride-sundmuste-kalender', 1, 1, 640, '2025-08-27 16:13:51', '2025-09-22 18:24:03', 0);
COMMIT;

-- ----------------------------
-- Table structure for example
-- ----------------------------
DROP TABLE IF EXISTS `example`;
CREATE TABLE `example` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of example
-- ----------------------------
BEGIN;
INSERT INTO `example` VALUES (1, '<h2>Midagi alustuseks</h2>\n', NULL, '');
INSERT INTO `example` VALUES (2, '<p><img alt=\"\" id=\"1121\" src=\"/project/tmp/_files/thumbnail/Kolletanud_lehed_maas.jpg\" style=\"float:left; height:208px; margin-left:10px; margin-right:10px; width:320px\" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie. Duis velit augue,</p>\n', NULL, '1121');
COMMIT;

-- ----------------------------
-- Table structure for files
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int DEFAULT NULL,
  `mtime` int DEFAULT NULL,
  `dimensions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `locked_file` int unsigned DEFAULT '0',
  `activities_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE,
  KEY `folder_id_idx` (`folder_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3694 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of files
-- ----------------------------
BEGIN;
INSERT INTO `files` VALUES (754, 929, 'seinakell.jpg', 'file', '/Varia/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1700646067, '611 x 404', 611, 404, 0, 0);
INSERT INTO `files` VALUES (755, 929, 'sirlu.jpg', 'file', '/Varia/sirlu.jpg', NULL, 'jpg', 'image/jpeg', 49122, 1700646067, '450 x 600', NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (756, 929, 'sp2_fotologs_net.jpg', 'file', '/Varia/sp2_fotologs_net.jpg', NULL, 'jpg', 'image/jpeg', 17070, 1700646067, '500 x 375', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1121, 1, 'Kolletanud_lehed_maas.jpg', 'file', '/Kolletanud_lehed_maas.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1741298864, '900 x 585', NULL, NULL, 15, 0);
INSERT INTO `files` VALUES (1134, 1, 'EKL aruanne.xlsx', 'file', '/EKL aruanne.xlsx', NULL, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 244192, 1704465357, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (1267, 996, 'jõulus talu.jpg', 'file', '/galerii/epk-kergejoustik-turil-14-05-2003/jõulus talu.jpg', NULL, 'jpg', 'image/jpeg', 72044, 1709141019, '960 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1268, 996, 'Bnowchristmas.jpg', 'file', '/galerii/epk-kergejoustik-turil-14-05-2003/Bnowchristmas.jpg', NULL, 'jpg', 'image/jpeg', 417297, 1709141019, '1600 x 1200', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1269, 996, 'christmas-wallpapers.jpg', 'file', '/galerii/epk-kergejoustik-turil-14-05-2003/christmas-wallpapers.jpg', NULL, 'jpg', 'image/jpeg', 79595, 1709141019, '1024 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1270, 996, 'ekl_joulukaart_2013.jpg', 'file', '/galerii/epk-kergejoustik-turil-14-05-2003/ekl_joulukaart_2013.jpg', NULL, 'jpg', 'image/jpeg', 625113, 1709141019, '1024 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1316, 994, 'allkiri.png', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/allkiri.png', NULL, 'png', 'image/png', 156790, 1725102998, '300 x 300', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1317, 994, '4686233863_aeb72a24df_b.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/4686233863_aeb72a24df_b.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1725103029, '1024 x 683', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1318, 994, 'DSC_0084.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_0084.jpg', NULL, 'jpg', 'image/jpeg', 2817798, 1725102998, '3008 x 2000', 3008, 2000, 0, 1);
INSERT INTO `files` VALUES (1319, 994, 'DSC_5177_1.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1725102998, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1320, 994, 'DSC_5197_1.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1725102998, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1321, 994, 'file60471593_d5a21f14.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1725102998, '900 x 585', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1322, 994, 'f_DSC01660.jpg', 'file', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1725102998, '2500 x 1667', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1345, 1008, '310625658_5771591356213069_6130322049604942068_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, 'jpeg', 'image/jpeg', 231678, 1711368633, '1120 x 2000', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1346, 1008, '310596090_1482278652270764_6161734453730055725_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, 'jpeg', 'image/jpeg', 271363, 1711368633, '1120 x 2000', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1347, 1008, '310651429_413903317415234_1877068238628190472_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, 'jpeg', 'image/jpeg', 168905, 1711368633, '1200 x 1600', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1348, 1008, '310986468_785287795913568_6096172368795184477_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, 'jpeg', 'image/jpeg', 256565, 1711368633, '2000 x 1126', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1349, 1008, '311163895_800320941296129_7328794715150918241_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, 'jpeg', 'image/jpeg', 176036, 1711368633, '1200 x 1600', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1350, 1008, '311271898_5500936233356667_4481537757649627936_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, 'jpeg', 'image/jpeg', 232041, 1711368633, '1120 x 2000', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1351, 1008, '311451979_627793208998847_3710757790573382164_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, 'jpeg', 'image/jpeg', 147897, 1711368633, '1200 x 1600', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1352, 1008, '311464218_606307097952705_2986433564733245675_n.jpeg', 'file', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, 'jpeg', 'image/jpeg', 264296, 1711368633, '2000 x 1126', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1366, 1009, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/galerii/arlese-album-28-02-2024/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1711372676, '1024 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1367, 1009, 'rukkilill.jpg', 'file', '/galerii/arlese-album-28-02-2024/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1711372676, '2288 x 1712', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1368, 1009, 'r 175.jpg', 'file', '/galerii/arlese-album-28-02-2024/r 175.jpg', NULL, 'jpg', 'image/jpeg', 878514, 1711372676, '3072 x 2304', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1369, 1009, 'rahvusvaheline_kurtide.jpg', 'file', '/galerii/arlese-album-28-02-2024/rahvusvaheline_kurtide.jpg', NULL, 'jpg', 'image/jpeg', 83968, 1711372676, '931 x 559', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1370, 1009, 'sirlu.jpg', 'file', '/galerii/arlese-album-28-02-2024/sirlu.jpg', NULL, 'jpg', 'image/jpeg', 49122, 1711372676, '450 x 600', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1371, 1009, 'seinakell.jpg', 'file', '/galerii/arlese-album-28-02-2024/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1711372676, '611 x 404', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1452, 1011, 'almic.png', 'file', '/Logod/almic.png', NULL, 'png', 'image/png', 28333, 1709562798, '125 x 31', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1453, 1011, 'eksl.png', 'file', '/Logod/eksl.png', NULL, 'png', 'image/png', 194813, 1709562798, '987 x 830', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1455, 1011, 'HAK-200.png', 'file', '/Logod/HAK-200.png', NULL, 'png', 'image/png', 20456, 1709562799, '200 x 200', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1456, 1011, 'lhv_logo.jpg', 'file', '/Logod/lhv_logo.jpg', NULL, 'jpg', 'image/jpeg', 13197, 1709562799, '188 x 64', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1457, 1011, 'Merit.jpg', 'file', '/Logod/Merit.jpg', NULL, 'jpg', 'image/jpeg', 25073, 1709562799, '242 x 60', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1458, 1011, 'tartu.png', 'file', '/Logod/tartu.png', NULL, 'png', 'image/png', 32953, 1709562799, '594 x 401', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1459, 1009, 'ekl_joulukaart_2021.jpg', 'file', '/galerii/arlese-album-28-02-2024/ekl_joulukaart_2021.jpg', NULL, 'jpg', 'image/jpeg', 423608, 1711372676, '960 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1460, 1009, 'ekl_joulukaart_2016.jpg', 'file', '/galerii/arlese-album-28-02-2024/ekl_joulukaart_2016.jpg', NULL, 'jpg', 'image/jpeg', 401329, 1711372676, '800 x 372', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1461, 1009, 'joulukaart_2010.jpg', 'file', '/galerii/arlese-album-28-02-2024/joulukaart_2010.jpg', NULL, 'jpg', 'image/jpeg', 140750, 1711372676, '1024 x 768', NULL, NULL, 0, 1);
INSERT INTO `files` VALUES (1462, 1011, 'Eesti_100_M.jpg', 'file', '/Logod/Eesti_100_M.jpg', NULL, 'jpg', 'image/jpeg', 577330, 1709814267, '200 x 158', NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (1464, 923, 'WhatsApp Image 2024-01-18 at 14.48.24.jpeg', 'file', '/Organisatsioon/WhatsApp Image 2024-01-18 at 14.48.24.jpeg', NULL, 'jpeg', 'image/jpeg', 75223, 1710163414, '1080 x 1440', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1465, 923, 'valged_orhideed.jpg', 'file', '/Organisatsioon/valged_orhideed.jpg', NULL, 'jpg', 'image/jpeg', 183298, 1710163414, '960 x 642', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (1466, 923, 'valentinikyynlas.JPG', 'file', '/Organisatsioon/valentinikyynlas.JPG', NULL, 'jpg', 'image/jpeg', 284250, 1710163414, '3008 x 2000', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1467, 923, 'vilinus reis 2262.jpg', 'file', '/Organisatsioon/vilinus reis 2262.jpg', NULL, 'jpg', 'image/jpeg', 154475, 1710163414, '1936 x 1288', NULL, NULL, 8, 0);
INSERT INTO `files` VALUES (1514, 1011, 'broken-image.png', 'file', '/Logod/broken-image.png', NULL, 'png', 'image/png', 15830, 1711025535, '400 x 272', 400, 272, 0, 0);
INSERT INTO `files` VALUES (1515, 1011, 'logo-sliderUi.svg', 'file', '/Logod/logo-sliderUi.svg', NULL, 'svg', 'image/svg+xml', 690, 1711034249, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1516, 1011, 'epikoda-logo.svg', 'file', '/Logod/epikoda-logo.svg', NULL, 'svg', 'image/svg+xml', 24292, 1711034341, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1520, 1011, 'epikoda-logo.jpg', 'file', '/Logod/epikoda-logo.jpg', NULL, 'jpg', 'image/jpeg', 393622, 1711050436, '3000 x 625', 3000, 625, 0, 0);
INSERT INTO `files` VALUES (1521, 1011, 'epikoda-logo-short.jpg', 'file', '/Logod/epikoda-logo-short.jpg', NULL, 'jpg', 'image/jpeg', 56860, 1711051113, '5000 x 5034', 5000, 5034, 1, 0);
INSERT INTO `files` VALUES (1522, 929, 'joulukaart_2010.jpg', 'file', '/Varia/joulukaart_2010.jpg', NULL, 'jpg', 'image/jpeg', 140750, 1711214006, '1024 x 768', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1523, 929, 'rahvusvaheline_kurtide.jpg', 'file', '/Varia/rahvusvaheline_kurtide.jpg', NULL, 'jpg', 'image/jpeg', 83968, 1711214006, '931 x 559', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1524, 929, 'rukkilill.jpg', 'file', '/Varia/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1711214006, '2288 x 1712', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1526, 929, 'DSC_0084.jpg', 'file', '/Varia/DSC_0084.jpg', NULL, 'jpg', 'image/jpeg', 2817798, 1711217700, '3008 x 2000', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1527, 929, 'DSC_5197_1.jpg', 'file', '/Varia/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1711217700, '1600 x 1064', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1528, 929, 'ekl_joulukaart_2016.jpg', 'file', '/Varia/ekl_joulukaart_2016.jpg', NULL, 'jpg', 'image/jpeg', 401329, 1711218176, '800 x 372', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1577, 1017, 'DSC_5197_1.jpg', 'file', '/galerii/uus-popurii/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1719518503, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1578, 1017, 'DSC_5177_1.jpg', 'file', '/galerii/uus-popurii/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1719518503, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1579, 1017, 'f_DSC01660.jpg', 'file', '/galerii/uus-popurii/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1719518503, '2500 x 1667', 2500, 1667, 0, 1);
INSERT INTO `files` VALUES (1580, 1017, 'file60471593_d5a21f14.jpg', 'file', '/galerii/uus-popurii/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1719518503, '900 x 585', 900, 585, 0, 1);
INSERT INTO `files` VALUES (1582, 1018, 'P7140053.jpg', 'file', '/galerii/uus-test/P7140053.jpg', NULL, 'jpg', 'image/jpeg', 846045, 1723032621, '2560 x 1920', 2560, 1920, 0, 1);
INSERT INTO `files` VALUES (1583, 1018, 'P7140059.jpg', 'file', '/galerii/uus-test/P7140059.jpg', NULL, 'jpg', 'image/jpeg', 925661, 1723032621, '2560 x 1920', 2560, 1920, 0, 1);
INSERT INTO `files` VALUES (1584, 1018, 'P7140060.JPG', 'file', '/galerii/uus-test/P7140060.JPG', NULL, 'jpg', 'image/jpeg', 843545, 1723032621, '2560 x 1920', 2560, 1920, 0, 1);
INSERT INTO `files` VALUES (1585, 1018, 'paike_vastu_metsa.jpg', 'file', '/galerii/uus-test/paike_vastu_metsa.jpg', NULL, 'jpg', 'image/jpeg', 254224, 1723032621, '800 x 530', 800, 530, 0, 1);
INSERT INTO `files` VALUES (1586, 937, 'DSC_5177_1.jpg', 'file', '/Konventeerimine/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1711541541, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (1587, 937, '4686233863_aeb72a24df_b.jpg', 'file', '/Konventeerimine/4686233863_aeb72a24df_b.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1711541541, '1024 x 683', 1024, 683, 1, 0);
INSERT INTO `files` VALUES (1588, 937, 'DSC_5197_1.jpg', 'file', '/Konventeerimine/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1711541541, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (1589, 937, 'f_DSC01660.jpg', 'file', '/Konventeerimine/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1711541541, '2500 x 1667', 2500, 1667, 0, 0);
INSERT INTO `files` VALUES (1590, 937, 'file60471593_d5a21f14.jpg', 'file', '/Konventeerimine/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1711541542, '900 x 585', 900, 585, 0, 0);
INSERT INTO `files` VALUES (1591, 937, 'galerii67681.jpg', 'file', '/Konventeerimine/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1711541594, '800 x 533', 800, 533, 0, 0);
INSERT INTO `files` VALUES (1593, 937, 'karikakrad_vihmas.jpg', 'file', '/Konventeerimine/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1711542383, '1280 x 868', 1280, 868, 3, 0);
INSERT INTO `files` VALUES (1594, 937, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/Konventeerimine/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1711542717, '1024 x 768', 1024, 768, 0, 0);
INSERT INTO `files` VALUES (1596, 1018, 'IMG_1172.JPG', 'file', '/galerii/uus-test/IMG_1172.JPG', NULL, 'jpg', 'image/jpeg', 1983398, 1723032621, '2592 x 1936', 2592, 1936, 0, 0);
INSERT INTO `files` VALUES (1692, 1026, 'crop_DSC_0084.png', 'file', '/crop-test/crop_DSC_0084.png', NULL, 'png', 'image/png', 3326457, 1719507748, '1519 x 1518', 1519, 1518, 2, 0);
INSERT INTO `files` VALUES (1693, 1026, 'crop_sirlu.png', 'file', '/crop-test/crop_sirlu.png', NULL, 'png', 'image/png', 96899, 1719519637, '314 x 160', 314, 160, 0, 0);
INSERT INTO `files` VALUES (1694, 1026, 'crop_seinakell.png', 'file', '/crop-test/crop_seinakell.png', NULL, 'png', 'image/png', 120716, 1719520181, '611 x 228', 611, 228, 0, 0);
INSERT INTO `files` VALUES (1695, 1026, 'crop_Tiit_Papp_2021.png', 'file', '/crop-test/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 1052796, 1719520648, '936 x 936', 936, 936, 0, 0);
INSERT INTO `files` VALUES (1712, 1059, 'Kuldnokk puus.jpg', 'file', '/pildigalerii/epk-kergejoustik-turil-14-05-2003/Kuldnokk puus.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1723025040, '1024 x 683', 1024, 683, 0, 1);
INSERT INTO `files` VALUES (1713, 1059, 'Mullid.jpg', 'file', '/pildigalerii/epk-kergejoustik-turil-14-05-2003/Mullid.jpg', NULL, 'jpg', 'image/jpeg', 493210, 1722964635, '1024 x 873', 1024, 873, 0, 1);
INSERT INTO `files` VALUES (1716, 1059, 'seinakell.jpg', 'file', '/pildigalerii/epk-kergejoustik-turil-14-05-2003/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1722858934, '611 x 404', 611, 404, 0, 1);
INSERT INTO `files` VALUES (1717, 1059, 'sirlu.jpg', 'file', '/pildigalerii/epk-kergejoustik-turil-14-05-2003/sirlu.jpg', NULL, 'jpg', 'image/jpeg', 49122, 1722858934, '450 x 600', 450, 600, 0, 1);
INSERT INTO `files` VALUES (1718, 1059, 'sp2_fotologs_net.jpg', 'file', '/pildigalerii/epk-kergejoustik-turil-14-05-2003/sp2_fotologs_net.jpg', NULL, 'jpg', 'image/jpeg', 17070, 1722858934, '500 x 375', 500, 375, 0, 1);
INSERT INTO `files` VALUES (1724, 1049, 'galerii62343.jpg', 'file', '/pildigalerii/ene-album/galerii62343.jpg', NULL, 'jpg', 'image/jpeg', 242336, 1723046450, '800 x 533', 800, 533, 0, 1);
INSERT INTO `files` VALUES (1725, 1049, 'galerii62341.jpg', 'file', '/pildigalerii/ene-album/galerii62341.jpg', NULL, 'jpg', 'image/jpeg', 221007, 1723046478, '800 x 536', 800, 536, 0, 1);
INSERT INTO `files` VALUES (1726, 1049, 'galerii62766.jpg', 'file', '/pildigalerii/ene-album/galerii62766.jpg', NULL, 'jpg', 'image/jpeg', 193688, 1722892811, '800 x 533', 800, 533, 0, 1);
INSERT INTO `files` VALUES (1727, 1049, 'Lill kollases valguses.jpg', 'file', '/pildigalerii/ene-album/Lill kollases valguses.jpg', NULL, 'jpg', 'image/jpeg', 232297, 1722981731, '800 x 533', 800, 533, 0, 1);
INSERT INTO `files` VALUES (1728, 1049, 'Udune lill.jpg', 'file', '/pildigalerii/ene-album/Udune lill.jpg', NULL, 'jpg', 'image/jpeg', 245709, 1722980797, '800 x 533', 800, 533, 0, 1);
INSERT INTO `files` VALUES (1729, 1049, 'sinililled.jpg', 'file', '/pildigalerii/ene-album/sinililled.jpg', NULL, 'jpg', 'image/jpeg', 15123, 1722980082, '528 x 351', 528, 351, 0, 1);
INSERT INTO `files` VALUES (1733, 1050, 'Kuldnokk puuladvas.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/Kuldnokk puuladvas.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1722981391, '1024 x 683', 1024, 683, 0, 1);
INSERT INTO `files` VALUES (1734, 1050, '2.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/2.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1722893892, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1735, 1050, '3.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/3.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1722893892, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (1736, 1050, '4.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/4.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1722893892, '900 x 585', 900, 585, 0, 1);
INSERT INTO `files` VALUES (1737, 1050, '5.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/5.jpg', NULL, 'jpg', 'image/jpeg', 488115, 1722893892, '1024 x 683', 1024, 683, 0, 1);
INSERT INTO `files` VALUES (1738, 1050, '6.jpg', 'file', '/pildigalerii/sugisene-treeningulaager-joulumae-tervisekeskuses/6.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1722893892, '1280 x 868', 1280, 868, 0, 1);
INSERT INTO `files` VALUES (1739, 1049, 'karikakrad.jpg', 'file', '/pildigalerii/ene-album/karikakrad.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1722980031, '1280 x 868', 1280, 868, 0, 1);
INSERT INTO `files` VALUES (1740, 1049, 'Aiaväravad külas.jpg', 'file', '/pildigalerii/ene-album/Aiaväravad külas.jpg', NULL, 'jpg', 'image/jpeg', 488115, 1722965740, '1024 x 683', 1024, 683, 0, 1);
INSERT INTO `files` VALUES (1775, 1049, 'seinakell.jpg', 'file', '/pildigalerii/ene-album/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1722993771, '611 x 404', 611, 404, 0, 1);
INSERT INTO `files` VALUES (1776, 1049, 'rahvusvaheline_kurtide.jpg', 'file', '/pildigalerii/ene-album/rahvusvaheline_kurtide.jpg', NULL, 'jpg', 'image/jpeg', 83968, 1722993771, '931 x 559', 931, 559, 0, 1);
INSERT INTO `files` VALUES (1806, 1065, 'galerii62343.jpg', 'file', '/pildiarhiiv/teeme-uue-galerii/galerii62343.jpg', NULL, 'jpg', 'image/jpeg', 242336, 1723328356, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (1807, 1065, 'jäätunud veetükid.jpg', 'file', '/pildiarhiiv/teeme-uue-galerii/jäätunud veetükid.jpg', NULL, 'jpg', 'image/jpeg', 193688, 1724522654, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (1808, 1065, 'galerii64408.jpg', 'file', '/pildiarhiiv/teeme-uue-galerii/galerii64408.jpg', NULL, 'jpg', 'image/jpeg', 239318, 1723328376, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (1809, 1065, 'loojuv päike järve pinnal.jpg', 'file', '/pildiarhiiv/teeme-uue-galerii/loojuv päike järve pinnal.jpg', NULL, 'jpg', 'image/jpeg', 248933, 1724522612, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (1866, 1021, 'DSC_5197_1.zip', 'file', '/Avaleht/test/DSC_5197_1.zip', NULL, 'zip', 'application/zip', 111798, 1723659352, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2273, 1026, 'crop_DSC_7550-1.png', 'file', '/crop-test/crop_DSC_7550-1.png', NULL, 'png', 'image/png', 2326107, 1724398610, '1230 x 1230', 1230, 1230, 0, 0);
INSERT INTO `files` VALUES (2274, 1026, 'crop_DSC_7550-1-1.png', 'file', '/crop-test/crop_DSC_7550-1-1.png', NULL, 'png', 'image/png', 1405526, 1724398762, '861 x 861', 861, 861, 0, 0);
INSERT INTO `files` VALUES (2376, 1019, 'crop_r 175.png', 'file', '/Varia/Varia esimene kaust/crop_r 175.png', NULL, 'png', 'image/png', 2016017, 1724702619, '1059 x 1059', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2641, 929, 'margus_raud.jpg', 'file', '/Varia/margus_raud.jpg', NULL, 'jpg', 'image/jpeg', 254427, 1725900510, '1365 x 2048', 1365, 2048, 2, 0);
INSERT INTO `files` VALUES (2642, 929, 'crop_margus_raud.png', 'file', '/Varia/crop_margus_raud.png', NULL, 'png', 'image/png', 581210, 1725900551, '624 x 623', 624, 623, 0, 0);
INSERT INTO `files` VALUES (2667, 933, 'Tiit töömõtetes.jpg', 'file', '/Avaleht/Tiit töömõtetes.jpg', NULL, 'jpg', 'image/jpeg', 2817798, 1741297622, '3008 x 2000', 3008, 2000, 0, 0);
INSERT INTO `files` VALUES (2668, 933, 'DSC_7550.jpg', 'file', '/Avaleht/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1726072956, '2953 x 1918', 2953, 1918, 0, 0);
INSERT INTO `files` VALUES (2669, 933, 'file60471593_d5a21f14.jpg', 'file', '/Avaleht/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726072957, '900 x 585', 900, 585, 0, 0);
INSERT INTO `files` VALUES (2670, 933, 'f_DSC01660.jpg', 'file', '/Avaleht/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726072957, '2500 x 1667', 2500, 1667, 0, 0);
INSERT INTO `files` VALUES (2671, 1078, 'IMG_0875.jpeg', 'file', '/Uudised/Uudised 2024/IMG_0875.jpeg', NULL, 'jpeg', 'image/jpeg', 1547525, 1726073722, '4032 x 3024', 4032, 3024, 0, 0);
INSERT INTO `files` VALUES (2672, 1078, 'ilus_vanavarav_looduses.jpg', 'file', '/Uudised/Uudised 2024/ilus_vanavarav_looduses.jpg', NULL, 'jpg', 'image/jpeg', 488115, 1726073766, '1024 x 683', 1024, 683, 0, 0);
INSERT INTO `files` VALUES (2673, 1078, 'karikakrad_vihmas.jpg', 'file', '/Uudised/Uudised 2024/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1726073766, '1280 x 868', 1280, 868, 0, 0);
INSERT INTO `files` VALUES (2674, 1078, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/Uudised/Uudised 2024/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1726073766, '1024 x 768', 1024, 768, 0, 0);
INSERT INTO `files` VALUES (2675, 1078, 'r 175.jpg', 'file', '/Uudised/Uudised 2024/r 175.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1726073766, '3072 x 2304', 3072, 2304, 0, 0);
INSERT INTO `files` VALUES (2676, 1078, 'crop_karikakrad_vihmas.png', 'file', '/Uudised/Uudised 2024/crop_karikakrad_vihmas.png', NULL, 'png', 'image/png', 1012954, 1726074085, '868 x 868', 868, 868, 0, 0);
INSERT INTO `files` VALUES (2677, 1078, 'crop_f_DSC01660.png', 'file', '/Uudised/Uudised 2024/crop_f_DSC01660.png', NULL, 'png', 'image/png', 138405, 1726074904, '328 x 327', 328, 327, 0, 0);
INSERT INTO `files` VALUES (2678, 1078, 'crop_r 175.png', 'file', '/Uudised/Uudised 2024/crop_r 175.png', NULL, 'png', 'image/png', 2940897, 1726075068, '1276 x 1275', 1276, 1275, 0, 0);
INSERT INTO `files` VALUES (2679, 1078, 'vanavarav_uudiseks.png', 'file', '/Uudised/Uudised 2024/vanavarav_uudiseks.png', NULL, 'png', 'image/png', 164293, 1726075181, '495 x 151', 495, 151, 0, 0);
INSERT INTO `files` VALUES (2680, 1078, 'vilinus reis 2263.jpg', 'file', '/Uudised/Uudised 2024/vilinus reis 2263.jpg', NULL, 'jpg', 'image/jpeg', 1138428, 1726075313, '1936 x 1288', 1936, 1288, 1, 0);
INSERT INTO `files` VALUES (2712, 1, 'galerii67681.jpg', 'file', '/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1728053575, '800 x 533', NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (2721, 1111, 'Tiit_töötoas_kurtide_majas.jpg', 'file', '/tester/Tiit_töötoas_kurtide_majas.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1741383520, '2500 x 1667', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2722, 1111, 'file60471593_d5a21f14.jpg', 'file', '/tester/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1741383520, '900 x 585', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2726, 1111, 'crop_DSC_5177_1.png', 'file', '/tester/crop_DSC_5177_1.png', NULL, 'png', 'image/png', 670209, 1741383520, '670 x 670', 670, 670, 0, 0);
INSERT INTO `files` VALUES (2727, 1111, 'crop_Tiit_Papp_töölaua_taga.png', 'file', '/tester/crop_Tiit_Papp_töölaua_taga.png', NULL, 'png', 'image/png', 111514, 1741383520, '288 x 289', 288, 289, 0, 0);
INSERT INTO `files` VALUES (2728, 1111, 'Tiit_Papp_2021.jpg', 'file', '/tester/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1741383520, '1200 x 1600', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2740, 1111, 'logo-sliderUi.svg', 'file', '/tester/logo-sliderUi.svg', NULL, 'svg', 'image/svg+xml', 690, 1741383520, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2741, 1111, '02262060750a05.jpg', 'file', '/tester/02262060750a05.jpg', NULL, 'jpg', 'image/jpeg', 47856, 1741383520, '720 x 359', 720, 359, 0, 0);
INSERT INTO `files` VALUES (2743, 1038, 'langenud lehed pargis.jpg', 'file', '/galerii/blaaa/langenud lehed pargis.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1733048755, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2744, 1038, 'kolletanud_vahtralehed.jpg', 'file', '/galerii/blaaa/kolletanud_vahtralehed.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1733048755, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2745, 1038, 'f_DSC01660.jpg', 'file', '/galerii/blaaa/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1733048755, '2500 x 1667', 2500, 1667, 0, 1);
INSERT INTO `files` VALUES (2746, 1038, 'file60471593_d5a21f14.jpg', 'file', '/galerii/blaaa/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1733048755, '900 x 585', 900, 585, 0, 1);
INSERT INTO `files` VALUES (2747, 1038, 'rukkilill.jpg', 'file', '/galerii/blaaa/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1733048755, '2288 x 1712', 2288, 1712, 0, 1);
INSERT INTO `files` VALUES (2749, 1038, 'Tiit pildistab.jpg', 'file', '/galerii/blaaa/Tiit pildistab.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1733048755, '3072 x 2304', 3072, 2304, 0, 1);
INSERT INTO `files` VALUES (2751, 1078, 'sinine_teletorn.jpg', 'file', '/Uudised/Uudised 2024/sinine_teletorn.jpg', NULL, 'jpg', 'image/jpeg', 47345, 1727266275, '720 x 960', 720, 960, 0, 0);
INSERT INTO `files` VALUES (2753, 1078, 'crop_sinine_teletorn.png', 'file', '/Uudised/Uudised 2024/crop_sinine_teletorn.png', NULL, 'png', 'image/png', 104367, 1727267563, '637 x 295', 637, 295, 3, 0);
INSERT INTO `files` VALUES (2754, 1120, '18.A.Ojastuauhinjuhend2017.pdf', 'file', '/spordialad/kergejoustik/juhendid/18.A.Ojastuauhinjuhend2017.pdf', NULL, 'pdf', 'application/pdf', 201732, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2755, 1120, '2013 EKSL sisekj_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2756, 1120, '2013 EKSL sisekj_juhend1.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend1.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2757, 1120, 'Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', 'file', '/spordialad/kergejoustik/juhendid/Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', NULL, 'pdf', 'application/pdf', 471776, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2758, 1120, 'EKSL MV juhend2013 kergej.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2013 kergej.pdf', NULL, 'pdf', 'application/pdf', 173725, 1727867443, NULL, NULL, NULL, 3, 0);
INSERT INTO `files` VALUES (2759, 1120, 'EKSL MV juhend2014 kergej 310514.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2014 kergej 310514.pdf', NULL, 'pdf', 'application/pdf', 276908, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2760, 1120, 'EKSL MV KJ  juhend 2018.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV KJ  juhend 2018.pdf', NULL, 'pdf', 'application/pdf', 109552, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2761, 1120, 'EKSL_kergejõustiku_MV_juhend_2012.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL_kergejõustiku_MV_juhend_2012.pdf', NULL, 'pdf', 'application/pdf', 72509, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2762, 1120, 'EPKMVkergej_15062019_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/EPKMVkergej_15062019_juhend.pdf', NULL, 'pdf', 'application/pdf', 131368, 1727867443, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2763, 1120, 'epok-kergej-mv-juhend-2016_OapQlwhK.pdf', 'file', '/spordialad/kergejoustik/juhendid/epok-kergej-mv-juhend-2016_OapQlwhK.pdf', NULL, 'pdf', 'application/pdf', 519683, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2764, 1121, '2012_EKSL_MV_protkergej 260512.pdf', 'file', '/spordialad/kergejoustik/tulemused/2012_EKSL_MV_protkergej 260512.pdf', NULL, 'pdf', 'application/pdf', 276151, 1727867694, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2794, 1, 'kurtide_liidu_maja_2013.jpg', 'file', '/kurtide_liidu_maja_2013.jpg', NULL, 'jpg', 'image/jpeg', 770166, 1729795447, '2048 x 1362', 2048, 1362, 4, 0);
INSERT INTO `files` VALUES (2795, 1126, 'Helle_Sass.png', 'file', '/Juhatus/2018-2023/Helle_Sass.png', NULL, 'png', 'image/png', 655482, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2796, 1126, 'Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/Janis_Golubenkov.png', NULL, 'png', 'image/png', 755152, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2797, 1126, 'Mati_Kartus.png', 'file', '/Juhatus/2018-2023/Mati_Kartus.png', NULL, 'png', 'image/png', 696185, 1730573593, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2798, 1126, 'Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/Riina_Kuusk.png', NULL, 'png', 'image/png', 711437, 1730567901, '568 x 850', 568, 850, 2, 0);
INSERT INTO `files` VALUES (2799, 1126, 'Sirle_Papp.png', 'file', '/Juhatus/2018-2023/Sirle_Papp.png', NULL, 'png', 'image/png', 642962, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2800, 1126, 'Tiit_Papp.png', 'file', '/Juhatus/2018-2023/Tiit_Papp.png', NULL, 'png', 'image/png', 619869, 1730567901, '568 x 850', 568, 850, 2, 0);
INSERT INTO `files` VALUES (2801, 1126, 'crop_Helle_Sass.png', 'file', '/Juhatus/2018-2023/crop_Helle_Sass.png', NULL, 'png', 'image/png', 387605, 1730567977, '558 x 559', 558, 559, 1, 0);
INSERT INTO `files` VALUES (2802, 1126, 'crop_Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/crop_Janis_Golubenkov.png', NULL, 'png', 'image/png', 340410, 1730568004, '550 x 549', 550, 549, 1, 0);
INSERT INTO `files` VALUES (2803, 1126, 'crop_Mati_Kartus.png', 'file', '/Juhatus/2018-2023/crop_Mati_Kartus.png', NULL, 'png', 'image/png', 360208, 1730573579, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2804, 1126, 'crop_Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/crop_Riina_Kuusk.png', NULL, 'png', 'image/png', 411006, 1730568055, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2805, 1126, 'crop_Sirle_Papp.png', 'file', '/Juhatus/2018-2023/crop_Sirle_Papp.png', NULL, 'png', 'image/png', 359834, 1730568080, '550 x 550', 550, 550, 3, 0);
INSERT INTO `files` VALUES (2806, 1126, 'crop_Tiit_Papp.png', 'file', '/Juhatus/2018-2023/crop_Tiit_Papp.png', NULL, 'png', 'image/png', 357732, 1730568106, '540 x 540', 540, 540, 2, 0);
INSERT INTO `files` VALUES (2807, 1111, 'crop_Tiit_Papp_2021.png', 'file', '/tester/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 1513373, 1741383520, '1102 x 1102', 1102, 1102, 1, 0);
INSERT INTO `files` VALUES (2823, 933, 'crop_Bnowchristmas_1600x1200.png', 'file', '/Avaleht/crop_Bnowchristmas_1600x1200.png', NULL, 'png', 'image/png', 1025633, 1735826540, '1104 x 1104', 1104, 1104, 0, 0);
INSERT INTO `files` VALUES (2825, 1147, 'smartcrop.jpg', 'file', '/Uudised/Uudised 2025/smartcrop.jpg', NULL, 'jpg', 'image/jpeg', 202389, 1735927633, '1370 x 850', 1370, 850, 1, 0);
INSERT INTO `files` VALUES (2826, 1147, 'smartcrop_1.jpg', 'file', '/Uudised/Uudised 2025/smartcrop_1.jpg', NULL, 'jpg', 'image/jpeg', 27486, 1735927633, '1370 x 850', 1370, 850, 1, 0);
INSERT INTO `files` VALUES (2827, 1147, 'resize.jpg', 'file', '/Uudised/Uudised 2025/resize.jpg', NULL, 'jpg', 'image/jpeg', 1523453, 1735927762, '2849 x 1780', 2849, 1780, 1, 0);
INSERT INTO `files` VALUES (2830, 1111, 'EKSL Tänuõhtu 2021 esitlus.pdf', 'file', '/tester/EKSL Tänuõhtu 2021 esitlus.pdf', NULL, 'pdf', 'application/pdf', 2321958, 1741383520, NULL, NULL, NULL, 3, 0);
INSERT INTO `files` VALUES (2833, 1, 'Eesti viipekeele staatus ja kasutamine.pdf', 'file', '/Eesti viipekeele staatus ja kasutamine.pdf', NULL, 'pdf', 'application/pdf', 208005, 1736698074, NULL, NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (2834, 933, 'crop_kurtide_liidu_maja_2013.png', 'file', '/Avaleht/crop_kurtide_liidu_maja_2013.png', NULL, 'png', 'image/png', 2949766, 1741267826, '1362 x 1362', 1362, 1362, 0, 0);
INSERT INTO `files` VALUES (2835, 1, 'crop_kurtide_liidu_maja_2013.png', 'file', '/crop_kurtide_liidu_maja_2013.png', NULL, 'png', 'image/png', 2949766, 1741301285, '1362 x 1362', 1362, 1362, 0, 0);
INSERT INTO `files` VALUES (2840, 1, 'crop_Tiit töömõtetes.png', 'file', '/crop_Tiit töömõtetes.png', NULL, 'png', 'image/png', 1823341, 1741300838, '1140 x 1140', 1140, 1140, 2, 0);
INSERT INTO `files` VALUES (2852, 1158, 'Tiit_Papp_2021.jpg', 'file', '/tester/vana-kaust/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1741383520, '1200 x 1600', 1200, 1600, 0, 0);
INSERT INTO `files` VALUES (2853, 1158, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.pdf', 'file', '/tester/vana-kaust/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.pdf', NULL, 'pdf', 'application/pdf', 1335377, 1741383520, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2854, 1158, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', 'file', '/tester/vana-kaust/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 1138176, 1741383520, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2855, 1158, '9.Kurtide_kultuur_Lilli_ja_Triin.ppt', 'file', '/tester/vana-kaust/9.Kurtide_kultuur_Lilli_ja_Triin.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 812544, 1741383520, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2856, 1158, 'Skaneeritud_lastepilt_korrigeeritud.jpg', 'file', '/tester/vana-kaust/Skaneeritud_lastepilt_korrigeeritud.jpg', NULL, 'jpg', 'image/jpeg', 2970616, 1741383520, '2338 x 1654', 2338, 1654, 0, 0);
INSERT INTO `files` VALUES (2857, 1160, 'crop_Tiit_Papp_2021.png', 'file', '/tester/tiidu-kaust/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 919291, 1741383520, '883 x 882', 883, 882, 0, 0);
INSERT INTO `files` VALUES (2862, 935, 'crop_jõulune maja.jpg.png', 'file', '/Avaleht/test 2/crop_jõulune maja.jpg.png', NULL, 'png', 'image/png', 548928, 1741386545, '654 x 654', 654, 654, 0, 0);
INSERT INTO `files` VALUES (2863, 935, 'IMG_3637.jpg', 'file', '/Avaleht/test 2/IMG_3637.jpg', NULL, 'jpg', 'image/jpeg', 7294898, 1741386595, '4032 x 3024', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2864, 935, 'ekl_joulukaart_2013_thumbitud.jpg', 'file', '/Avaleht/test 2/ekl_joulukaart_2013_thumbitud.jpg', NULL, 'jpg', 'image/jpeg', 351946, 1741386595, '630 x 473', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2865, 935, 'ekl_joulukaart_2021.jpg', 'file', '/Avaleht/test 2/ekl_joulukaart_2021.jpg', NULL, 'jpg', 'image/jpeg', 423608, 1741386596, '960 x 768', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2866, 935, 'jõulune maja.jpg.jpg', 'file', '/Avaleht/test 2/jõulune maja.jpg.jpg', NULL, 'jpg', 'image/jpeg', 72044, 1741386596, '960 x 768', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2867, 935, 'DSC_7550.jpg', 'file', '/Avaleht/test 2/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1741386622, '2953 x 1918', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2868, 935, 'Tiit töömõtetes.jpg', 'file', '/Avaleht/test 2/Tiit töömõtetes.jpg', NULL, 'jpg', 'image/jpeg', 2817798, 1741386622, '3008 x 2000', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2869, 933, 'crop_DSC_7550.png', 'file', '/Avaleht/crop_DSC_7550.png', NULL, 'png', 'image/png', 3058439, 1741386706, '1424 x 1424', 1424, 1424, 0, 0);
INSERT INTO `files` VALUES (2871, 933, 'Tiit_Papp_2021.jpg', 'file', '/Avaleht/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1741449805, '1200 x 1600', 1200, 1600, 0, 0);
INSERT INTO `files` VALUES (2872, 933, 'crop_Tiit_Papp_2021.png', 'file', '/Avaleht/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 898813, 1741449834, '894 x 894', 894, 894, 0, 0);
INSERT INTO `files` VALUES (2873, 933, 'Skaneeritud_lastepilt.jpg', 'file', '/Avaleht/Skaneeritud_lastepilt.jpg', NULL, 'jpg', 'image/jpeg', 2364824, 1741450209, '2338 x 1654', 2338, 1654, 0, 0);
INSERT INTO `files` VALUES (2878, 1, 'crop_kurtide_liidu_maja_2013-1.png', 'file', '/crop_kurtide_liidu_maja_2013-1.png', NULL, 'png', 'image/png', 2141824, 1741569040, '2030 x 490', 2030, 490, 2, 0);
INSERT INTO `files` VALUES (2879, 1021, 'Tiit_Papp_2021.jpg', 'file', '/Avaleht/test/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1742128666, '1200 x 1600', 1200, 1600, 0, 0);
INSERT INTO `files` VALUES (2892, 1021, 'DSC_5197_1.jpg', 'file', '/Avaleht/test/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1742129407, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (2893, 1021, 'DSC_5177_1.jpg', 'file', '/Avaleht/test/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1742129407, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (2894, 1021, 'f_DSC01660.jpg', 'file', '/Avaleht/test/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1742129407, '2500 x 1667', 2500, 1667, 0, 0);
INSERT INTO `files` VALUES (2895, 1021, 'file60471593_d5a21f14.jpg', 'file', '/Avaleht/test/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1742129408, '900 x 585', 900, 585, 0, 0);
INSERT INTO `files` VALUES (2896, 1, 'efektne_valgusmang.jpg', 'file', '/efektne_valgusmang.jpg', NULL, 'jpg', 'image/jpeg', 407603, 1754054038, '1000 x 666', 1000, 666, 1, 0);
INSERT INTO `files` VALUES (2897, 1147, 'smartcrop-1.jpg', 'file', '/Uudised/Uudised 2025/smartcrop-1.jpg', NULL, 'jpg', 'image/jpeg', 208673, 1754386704, '1370 x 850', 1370, 850, 2, 0);
INSERT INTO `files` VALUES (2898, 1147, 'smartcrop_trans.jpeg', 'file', '/Uudised/Uudised 2025/smartcrop_trans.jpeg', NULL, 'jpeg', 'image/jpeg', 113899, 1754392350, '1370 x 850', 1370, 850, 2, 0);
INSERT INTO `files` VALUES (2937, 1164, '4675153776_02d8f8e483_b.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/4675153776_02d8f8e483_b.jpg', NULL, 'jpg', 'image/jpeg', 95732, 1754681683, '799 x 533', 799, 533, 1, 1);
INSERT INTO `files` VALUES (2938, 1164, '4686233863_aeb72a24df_b.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/4686233863_aeb72a24df_b.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1754681683, '1024 x 683', 1024, 683, 1, 1);
INSERT INTO `files` VALUES (2939, 1164, '4680076964_298f35a321_b.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/4680076964_298f35a321_b.jpg', NULL, 'jpg', 'image/jpeg', 493210, 1754681683, '1024 x 873', 1024, 873, 1, 1);
INSERT INTO `files` VALUES (2940, 1164, 'DSC_5177_1.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1754681683, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2941, 1164, 'DSC_5197_1.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1754681684, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2942, 1164, 'file60471593_d5a21f14.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1754681685, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2943, 1164, 'DSC_7550.jpg', 'file', '/pildigalerii/uus-album-08-08-2026/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1754681685, '2953 x 1918', 2953, 1918, 1, 1);
INSERT INTO `files` VALUES (2944, 1167, 'EM-Tallin-Tag3-0023.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0023.jpg', NULL, 'jpg', 'image/jpeg', 91238, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2945, 1167, 'EM-Tallin-Tag3-0020.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0020.jpg', NULL, 'jpg', 'image/jpeg', 130447, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2946, 1167, 'EM-Tallin-Tag3-0034.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0034.jpg', NULL, 'jpg', 'image/jpeg', 86548, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2947, 1167, 'EM-Tallin-Tag3-0035.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0035.jpg', NULL, 'jpg', 'image/jpeg', 69016, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2948, 1167, 'EM-Tallin-Tag3-0036.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0036.jpg', NULL, 'jpg', 'image/jpeg', 73444, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2949, 1167, 'EM-Tallin-Tag3-0037.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0037.jpg', NULL, 'jpg', 'image/jpeg', 79617, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2950, 1167, 'EM-Tallin-Tag3-0038.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0038.jpg', NULL, 'jpg', 'image/jpeg', 100006, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2951, 1167, 'EM-Tallin-Tag3-0039.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0039.jpg', NULL, 'jpg', 'image/jpeg', 99293, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2952, 1167, 'EM-Tallin-Tag3-0044.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0044.jpg', NULL, 'jpg', 'image/jpeg', 125679, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2953, 1167, 'EM-Tallin-Tag3-0046.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0046.jpg', NULL, 'jpg', 'image/jpeg', 100317, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2954, 1167, 'EM-Tallin-Tag3-0048.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0048.jpg', NULL, 'jpg', 'image/jpeg', 93968, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2955, 1167, 'EM-Tallin-Tag3-0050.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0050.jpg', NULL, 'jpg', 'image/jpeg', 106682, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2956, 1167, 'EM-Tallin-Tag3-0057.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0057.jpg', NULL, 'jpg', 'image/jpeg', 76729, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2957, 1167, 'EM-Tallin-Tag3-0063.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0063.jpg', NULL, 'jpg', 'image/jpeg', 85755, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2958, 1167, 'EM-Tallin-Tag3-0065.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0065.jpg', NULL, 'jpg', 'image/jpeg', 92363, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2959, 1167, 'EM-Tallin-Tag3-0071.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0071.jpg', NULL, 'jpg', 'image/jpeg', 79840, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2960, 1167, 'EM-Tallin-Tag3-0077.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0077.jpg', NULL, 'jpg', 'image/jpeg', 76307, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2961, 1167, 'EM-Tallin-Tag3-0108.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0108.jpg', NULL, 'jpg', 'image/jpeg', 65534, 1754775170, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2962, 1167, 'EM-Tallin-Tag3-0109.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0109.jpg', NULL, 'jpg', 'image/jpeg', 81762, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2963, 1167, 'EM-Tallin-Tag3-0112.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0112.jpg', NULL, 'jpg', 'image/jpeg', 73141, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2964, 1167, 'EM-Tallin-Tag3-0127.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0127.jpg', NULL, 'jpg', 'image/jpeg', 39232, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2965, 1167, 'EM-Tallin-Tag3-0140.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0140.jpg', NULL, 'jpg', 'image/jpeg', 36496, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2966, 1167, 'EM-Tallin-Tag3-0150.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0150.jpg', NULL, 'jpg', 'image/jpeg', 33273, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2967, 1167, 'EM-Tallin-Tag3-0160.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0160.jpg', NULL, 'jpg', 'image/jpeg', 39384, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2968, 1167, 'EM-Tallin-Tag3-0174.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0174.jpg', NULL, 'jpg', 'image/jpeg', 42570, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2969, 1167, 'EM-Tallin-Tag3-0188.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0188.jpg', NULL, 'jpg', 'image/jpeg', 39742, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2970, 1167, 'EM-Tallin-Tag3-0202.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0202.jpg', NULL, 'jpg', 'image/jpeg', 37588, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2971, 1167, 'EM-Tallin-Tag3-0211.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0211.jpg', NULL, 'jpg', 'image/jpeg', 36647, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2972, 1167, 'EM-Tallin-Tag3-0216.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0216.jpg', NULL, 'jpg', 'image/jpeg', 42460, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2973, 1167, 'EM-Tallin-Tag3-0228.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0228.jpg', NULL, 'jpg', 'image/jpeg', 40567, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2974, 1167, 'EM-Tallin-Tag3-0235.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0235.jpg', NULL, 'jpg', 'image/jpeg', 47196, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2975, 1167, 'EM-Tallin-Tag3-0237.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0237.jpg', NULL, 'jpg', 'image/jpeg', 47209, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2976, 1167, 'EM-Tallin-Tag3-0247.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0247.jpg', NULL, 'jpg', 'image/jpeg', 51807, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2977, 1167, 'EM-Tallin-Tag3-0250.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0250.jpg', NULL, 'jpg', 'image/jpeg', 40914, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2978, 1167, 'EM-Tallin-Tag3-0252.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0252.jpg', NULL, 'jpg', 'image/jpeg', 47236, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2979, 1167, 'EM-Tallin-Tag3-0264.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0264.jpg', NULL, 'jpg', 'image/jpeg', 133485, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2980, 1167, 'EM-Tallin-Tag3-0268.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0268.jpg', NULL, 'jpg', 'image/jpeg', 50311, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2981, 1167, 'EM-Tallin-Tag3-0318.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0318.jpg', NULL, 'jpg', 'image/jpeg', 72801, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2982, 1167, 'EM-Tallin-Tag3-0326.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0326.jpg', NULL, 'jpg', 'image/jpeg', 138340, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2983, 1167, 'EM-Tallin-Tag3-0332.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0332.jpg', NULL, 'jpg', 'image/jpeg', 97684, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2984, 1167, 'EM-Tallin-Tag3-0352.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0352.jpg', NULL, 'jpg', 'image/jpeg', 96243, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2985, 1167, 'EM-Tallin-Tag3-0384.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0384.jpg', NULL, 'jpg', 'image/jpeg', 36077, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2986, 1167, 'EM-Tallin-Tag3-0386.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0386.jpg', NULL, 'jpg', 'image/jpeg', 128249, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2987, 1167, 'EM-Tallin-Tag3-0459.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0459.jpg', NULL, 'jpg', 'image/jpeg', 122695, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2988, 1167, 'EM-Tallin-Tag3-0483.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0483.jpg', NULL, 'jpg', 'image/jpeg', 68310, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2989, 1167, 'EM-Tallin-Tag3-0486.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0486.jpg', NULL, 'jpg', 'image/jpeg', 93005, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2990, 1167, 'EM-Tallin-Tag3-0490.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0490.jpg', NULL, 'jpg', 'image/jpeg', 101358, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2991, 1167, 'EM-Tallin-Tag3-0492.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0492.jpg', NULL, 'jpg', 'image/jpeg', 109205, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2992, 1167, 'EM-Tallin-Tag3-0493.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0493.jpg', NULL, 'jpg', 'image/jpeg', 115027, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2993, 1167, 'EM-Tallin-Tag3-0496.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0496.jpg', NULL, 'jpg', 'image/jpeg', 113771, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2994, 1167, 'EM-Tallin-Tag3-0498.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0498.jpg', NULL, 'jpg', 'image/jpeg', 147548, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2995, 1167, 'EM-Tallin-Tag3-0502.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0502.jpg', NULL, 'jpg', 'image/jpeg', 145041, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (2996, 1167, 'EM-Tallin-Tag3-0683.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0683.jpg', NULL, 'jpg', 'image/jpeg', 36699, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2997, 1167, 'EM-Tallin-Tag3-0691.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0691.jpg', NULL, 'jpg', 'image/jpeg', 54367, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2998, 1167, 'EM-Tallin-Tag3-0696.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0696.jpg', NULL, 'jpg', 'image/jpeg', 42013, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (2999, 1167, 'EM-Tallin-Tag3-0702.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0702.jpg', NULL, 'jpg', 'image/jpeg', 40754, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3000, 1167, 'EM-Tallin-Tag3-0707.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0707.jpg', NULL, 'jpg', 'image/jpeg', 80952, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3001, 1167, 'EM-Tallin-Tag3-0709.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0709.jpg', NULL, 'jpg', 'image/jpeg', 84626, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3002, 1167, 'EM-Tallin-Tag3-0711.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0711.jpg', NULL, 'jpg', 'image/jpeg', 96710, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3003, 1167, 'EM-Tallin-Tag3-0714.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0714.jpg', NULL, 'jpg', 'image/jpeg', 43261, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3004, 1167, 'EM-Tallin-Tag3-0721.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0721.jpg', NULL, 'jpg', 'image/jpeg', 42674, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3005, 1167, 'EM-Tallin-Tag3-0728.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0728.jpg', NULL, 'jpg', 'image/jpeg', 40921, 1754775171, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3006, 1167, 'EM-Tallin-Tag3-0733.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0733.jpg', NULL, 'jpg', 'image/jpeg', 76717, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3007, 1167, 'EM-Tallin-Tag3-0735.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0735.jpg', NULL, 'jpg', 'image/jpeg', 79785, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3008, 1167, 'EM-Tallin-Tag3-0736.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0736.jpg', NULL, 'jpg', 'image/jpeg', 107564, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3009, 1167, 'EM-Tallin-Tag3-0742.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0742.jpg', NULL, 'jpg', 'image/jpeg', 84393, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3010, 1167, 'EM-Tallin-Tag3-0743.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0743.jpg', NULL, 'jpg', 'image/jpeg', 90887, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3011, 1167, 'EM-Tallin-Tag3-0744.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0744.jpg', NULL, 'jpg', 'image/jpeg', 88612, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3012, 1167, 'EM-Tallin-Tag3-0745.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0745.jpg', NULL, 'jpg', 'image/jpeg', 85539, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3013, 1167, 'EM-Tallin-Tag3-0747.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0747.jpg', NULL, 'jpg', 'image/jpeg', 113687, 1754775171, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3014, 1167, 'EM-Tallin-Tag3-0748.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0748.jpg', NULL, 'jpg', 'image/jpeg', 110407, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3015, 1167, 'EM-Tallin-Tag3-0751.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0751.jpg', NULL, 'jpg', 'image/jpeg', 39481, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3016, 1167, 'EM-Tallin-Tag3-0759.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0759.jpg', NULL, 'jpg', 'image/jpeg', 40340, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3017, 1167, 'EM-Tallin-Tag3-0766.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0766.jpg', NULL, 'jpg', 'image/jpeg', 38387, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3018, 1167, 'EM-Tallin-Tag3-0773.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0773.jpg', NULL, 'jpg', 'image/jpeg', 35895, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3019, 1167, 'EM-Tallin-Tag3-0777.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0777.jpg', NULL, 'jpg', 'image/jpeg', 43956, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3020, 1167, 'EM-Tallin-Tag3-0785.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0785.jpg', NULL, 'jpg', 'image/jpeg', 43319, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3021, 1167, 'EM-Tallin-Tag3-0789.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0789.jpg', NULL, 'jpg', 'image/jpeg', 94485, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3022, 1167, 'EM-Tallin-Tag3-0790.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0790.jpg', NULL, 'jpg', 'image/jpeg', 91175, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3023, 1167, 'EM-Tallin-Tag3-0791.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0791.jpg', NULL, 'jpg', 'image/jpeg', 88170, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3024, 1167, 'EM-Tallin-Tag3-0793.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0793.jpg', NULL, 'jpg', 'image/jpeg', 104057, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3025, 1167, 'EM-Tallin-Tag3-0795.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0795.jpg', NULL, 'jpg', 'image/jpeg', 116944, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3026, 1167, 'EM-Tallin-Tag3-0855.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0855.jpg', NULL, 'jpg', 'image/jpeg', 98011, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3027, 1167, 'EM-Tallin-Tag3-0869.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0869.jpg', NULL, 'jpg', 'image/jpeg', 84365, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3028, 1167, 'EM-Tallin-Tag3-0894.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0894.jpg', NULL, 'jpg', 'image/jpeg', 96096, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3029, 1167, 'EM-Tallin-Tag3-0921.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0921.jpg', NULL, 'jpg', 'image/jpeg', 58405, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3030, 1167, 'EM-Tallin-Tag3-0929.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0929.jpg', NULL, 'jpg', 'image/jpeg', 102032, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3031, 1167, 'EM-Tallin-Tag3-0933.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0933.jpg', NULL, 'jpg', 'image/jpeg', 129038, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3032, 1167, 'EM-Tallin-Tag3-0939.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0939.jpg', NULL, 'jpg', 'image/jpeg', 196505, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3033, 1167, 'EM-Tallin-Tag3-0947.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0947.jpg', NULL, 'jpg', 'image/jpeg', 82254, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3034, 1167, 'EM-Tallin-Tag3-0970.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0970.jpg', NULL, 'jpg', 'image/jpeg', 83661, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3035, 1167, 'EM-Tallin-Tag3-0976.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-0976.jpg', NULL, 'jpg', 'image/jpeg', 101405, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3036, 1167, 'EM-Tallin-Tag3-1005.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1005.jpg', NULL, 'jpg', 'image/jpeg', 96267, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3037, 1167, 'EM-Tallin-Tag3-1022.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1022.jpg', NULL, 'jpg', 'image/jpeg', 110639, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3038, 1167, 'EM-Tallin-Tag3-1045.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1045.jpg', NULL, 'jpg', 'image/jpeg', 103694, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3039, 1167, 'EM-Tallin-Tag3-1075.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1075.jpg', NULL, 'jpg', 'image/jpeg', 93895, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3040, 1167, 'EM-Tallin-Tag3-1130.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1130.jpg', NULL, 'jpg', 'image/jpeg', 77583, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3041, 1167, 'EM-Tallin-Tag3-1131.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1131.jpg', NULL, 'jpg', 'image/jpeg', 76098, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3042, 1167, 'EM-Tallin-Tag3-1137.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1137.jpg', NULL, 'jpg', 'image/jpeg', 109119, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3043, 1167, 'EM-Tallin-Tag3-1177.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1177.jpg', NULL, 'jpg', 'image/jpeg', 27114, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3044, 1167, 'EM-Tallin-Tag3-1185.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1185.jpg', NULL, 'jpg', 'image/jpeg', 69914, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3045, 1167, 'EM-Tallin-Tag3-1254.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1254.jpg', NULL, 'jpg', 'image/jpeg', 38859, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3046, 1167, 'EM-Tallin-Tag3-1257.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1257.jpg', NULL, 'jpg', 'image/jpeg', 105486, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3047, 1167, 'EM-Tallin-Tag3-1262.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1262.jpg', NULL, 'jpg', 'image/jpeg', 164973, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3048, 1167, 'EM-Tallin-Tag3-1278.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1278.jpg', NULL, 'jpg', 'image/jpeg', 44998, 1754775172, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3049, 1167, 'EM-Tallin-Tag3-1284.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1284.jpg', NULL, 'jpg', 'image/jpeg', 116463, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3050, 1167, 'EM-Tallin-Tag3-1285.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1285.jpg', NULL, 'jpg', 'image/jpeg', 103535, 1754775172, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3051, 1167, 'EM-Tallin-Tag3-1292.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1292.jpg', NULL, 'jpg', 'image/jpeg', 76910, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3052, 1167, 'EM-Tallin-Tag3-1295.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1295.jpg', NULL, 'jpg', 'image/jpeg', 109199, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3053, 1167, 'EM-Tallin-Tag3-1297.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1297.jpg', NULL, 'jpg', 'image/jpeg', 97192, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3054, 1167, 'EM-Tallin-Tag3-1298.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1298.jpg', NULL, 'jpg', 'image/jpeg', 128098, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3055, 1167, 'EM-Tallin-Tag3-1299.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1299.jpg', NULL, 'jpg', 'image/jpeg', 104352, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3056, 1167, 'EM-Tallin-Tag3-1301.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1301.jpg', NULL, 'jpg', 'image/jpeg', 123739, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3057, 1167, 'EM-Tallin-Tag3-1303.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1303.jpg', NULL, 'jpg', 'image/jpeg', 132381, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3058, 1167, 'EM-Tallin-Tag3-1305.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1305.jpg', NULL, 'jpg', 'image/jpeg', 125063, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3059, 1167, 'EM-Tallin-Tag3-1306.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1306.jpg', NULL, 'jpg', 'image/jpeg', 119490, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3060, 1167, 'EM-Tallin-Tag3-1310.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1310.jpg', NULL, 'jpg', 'image/jpeg', 144235, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3061, 1167, 'EM-Tallin-Tag3-1312.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1312.jpg', NULL, 'jpg', 'image/jpeg', 104213, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3062, 1167, 'EM-Tallin-Tag3-1313.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1313.jpg', NULL, 'jpg', 'image/jpeg', 179927, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3063, 1167, 'EM-Tallin-Tag3-1314.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1314.jpg', NULL, 'jpg', 'image/jpeg', 149575, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3064, 1167, 'EM-Tallin-Tag3-1315.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1315.jpg', NULL, 'jpg', 'image/jpeg', 151919, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3065, 1167, 'EM-Tallin-Tag3-1317.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1317.jpg', NULL, 'jpg', 'image/jpeg', 129004, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3066, 1167, 'EM-Tallin-Tag3-1318.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1318.jpg', NULL, 'jpg', 'image/jpeg', 96620, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3067, 1167, 'EM-Tallin-Tag3-1319.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1319.jpg', NULL, 'jpg', 'image/jpeg', 96579, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3068, 1167, 'EM-Tallin-Tag3-1320.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1320.jpg', NULL, 'jpg', 'image/jpeg', 104652, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3069, 1167, 'EM-Tallin-Tag3-1321.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1321.jpg', NULL, 'jpg', 'image/jpeg', 116613, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3070, 1167, 'EM-Tallin-Tag3-1322.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1322.jpg', NULL, 'jpg', 'image/jpeg', 101272, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3071, 1167, 'EM-Tallin-Tag3-1323.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1323.jpg', NULL, 'jpg', 'image/jpeg', 94669, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3072, 1167, 'EM-Tallin-Tag3-1325.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1325.jpg', NULL, 'jpg', 'image/jpeg', 96197, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3073, 1167, 'EM-Tallin-Tag3-1326.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1326.jpg', NULL, 'jpg', 'image/jpeg', 96696, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3074, 1167, 'EM-Tallin-Tag3-1327.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1327.jpg', NULL, 'jpg', 'image/jpeg', 89078, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3075, 1167, 'EM-Tallin-Tag3-1329.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1329.jpg', NULL, 'jpg', 'image/jpeg', 139614, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3076, 1167, 'EM-Tallin-Tag3-1330.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1330.jpg', NULL, 'jpg', 'image/jpeg', 123388, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3077, 1167, 'EM-Tallin-Tag3-1331.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1331.jpg', NULL, 'jpg', 'image/jpeg', 124931, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3078, 1167, 'EM-Tallin-Tag3-1333.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1333.jpg', NULL, 'jpg', 'image/jpeg', 139975, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3079, 1167, 'EM-Tallin-Tag3-1337.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1337.jpg', NULL, 'jpg', 'image/jpeg', 129264, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3080, 1167, 'EM-Tallin-Tag3-1340.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1340.jpg', NULL, 'jpg', 'image/jpeg', 137210, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3081, 1167, 'EM-Tallin-Tag3-1341.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1341.jpg', NULL, 'jpg', 'image/jpeg', 121826, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3082, 1167, 'EM-Tallin-Tag3-1345.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1345.jpg', NULL, 'jpg', 'image/jpeg', 81120, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3083, 1167, 'EM-Tallin-Tag3-1346.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1346.jpg', NULL, 'jpg', 'image/jpeg', 101422, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3084, 1167, 'EM-Tallin-Tag3-1348.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1348.jpg', NULL, 'jpg', 'image/jpeg', 96194, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3085, 1167, 'EM-Tallin-Tag3-1351.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1351.jpg', NULL, 'jpg', 'image/jpeg', 93431, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3086, 1167, 'EM-Tallin-Tag3-1352.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1352.jpg', NULL, 'jpg', 'image/jpeg', 125782, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3087, 1167, 'EM-Tallin-Tag3-1356.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1356.jpg', NULL, 'jpg', 'image/jpeg', 116149, 1754775173, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3088, 1167, 'EM-Tallin-Tag3-1365.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1365.jpg', NULL, 'jpg', 'image/jpeg', 68514, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3089, 1167, 'EM-Tallin-Tag3-1368.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1368.jpg', NULL, 'jpg', 'image/jpeg', 84070, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3090, 1167, 'EM-Tallin-Tag3-1370.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1370.jpg', NULL, 'jpg', 'image/jpeg', 157299, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3091, 1167, 'EM-Tallin-Tag3-1374.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1374.jpg', NULL, 'jpg', 'image/jpeg', 125351, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3092, 1167, 'EM-Tallin-Tag3-1376.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1376.jpg', NULL, 'jpg', 'image/jpeg', 126719, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3093, 1167, 'EM-Tallin-Tag3-1378.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1378.jpg', NULL, 'jpg', 'image/jpeg', 121439, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3094, 1167, 'EM-Tallin-Tag3-1379.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1379.jpg', NULL, 'jpg', 'image/jpeg', 127174, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3095, 1167, 'EM-Tallin-Tag3-1382.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1382.jpg', NULL, 'jpg', 'image/jpeg', 119840, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3096, 1167, 'EM-Tallin-Tag3-1384.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1384.jpg', NULL, 'jpg', 'image/jpeg', 60445, 1754775174, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3097, 1167, 'EM-Tallin-Tag3-1390.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1390.jpg', NULL, 'jpg', 'image/jpeg', 61779, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3098, 1167, 'EM-Tallin-Tag3-1394.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1394.jpg', NULL, 'jpg', 'image/jpeg', 131314, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3099, 1167, 'EM-Tallin-Tag3-1397.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1397.jpg', NULL, 'jpg', 'image/jpeg', 109621, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3100, 1167, 'EM-Tallin-Tag3-1401.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1401.jpg', NULL, 'jpg', 'image/jpeg', 98859, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3101, 1167, 'EM-Tallin-Tag3-1402.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1402.jpg', NULL, 'jpg', 'image/jpeg', 87211, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3102, 1167, 'EM-Tallin-Tag3-1404.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1404.jpg', NULL, 'jpg', 'image/jpeg', 116966, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3103, 1167, 'EM-Tallin-Tag3-1407.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1407.jpg', NULL, 'jpg', 'image/jpeg', 83892, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3104, 1167, 'EM-Tallin-Tag3-1411.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1411.jpg', NULL, 'jpg', 'image/jpeg', 70679, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3105, 1167, 'EM-Tallin-Tag3-1419.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1419.jpg', NULL, 'jpg', 'image/jpeg', 67448, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3106, 1167, 'EM-Tallin-Tag3-1421.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1421.jpg', NULL, 'jpg', 'image/jpeg', 83518, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3107, 1167, 'EM-Tallin-Tag3-1425.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1425.jpg', NULL, 'jpg', 'image/jpeg', 32023, 1754775174, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3108, 1167, 'EM-Tallin-Tag3-1428.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1428.jpg', NULL, 'jpg', 'image/jpeg', 75457, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3109, 1167, 'EM-Tallin-Tag3-1429.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1429.jpg', NULL, 'jpg', 'image/jpeg', 82557, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3110, 1167, 'EM-Tallin-Tag3-1431.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1431.jpg', NULL, 'jpg', 'image/jpeg', 90220, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3111, 1167, 'EM-Tallin-Tag3-1434.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1434.jpg', NULL, 'jpg', 'image/jpeg', 43316, 1754775174, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3112, 1167, 'EM-Tallin-Tag3-1436.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1436.jpg', NULL, 'jpg', 'image/jpeg', 161145, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3113, 1167, 'EM-Tallin-Tag3-1444.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1444.jpg', NULL, 'jpg', 'image/jpeg', 175054, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3114, 1167, 'EM-Tallin-Tag3-1453.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1453.jpg', NULL, 'jpg', 'image/jpeg', 136763, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3115, 1167, 'EM-Tallin-Tag3-1455.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1455.jpg', NULL, 'jpg', 'image/jpeg', 146818, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3116, 1167, 'EM-Tallin-Tag3-1457.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1457.jpg', NULL, 'jpg', 'image/jpeg', 119396, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3117, 1167, 'EM-Tallin-Tag3-1465.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1465.jpg', NULL, 'jpg', 'image/jpeg', 123019, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3118, 1167, 'EM-Tallin-Tag3-1476.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1476.jpg', NULL, 'jpg', 'image/jpeg', 138895, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3119, 1167, 'EM-Tallin-Tag3-1479.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1479.jpg', NULL, 'jpg', 'image/jpeg', 197396, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3120, 1167, 'EM-Tallin-Tag3-1483.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1483.jpg', NULL, 'jpg', 'image/jpeg', 103533, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3121, 1167, 'EM-Tallin-Tag3-1495.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1495.jpg', NULL, 'jpg', 'image/jpeg', 103124, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3122, 1167, 'EM-Tallin-Tag3-1498.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1498.jpg', NULL, 'jpg', 'image/jpeg', 102169, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3123, 1167, 'EM-Tallin-Tag3-1509.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1509.jpg', NULL, 'jpg', 'image/jpeg', 123265, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3124, 1167, 'EM-Tallin-Tag3-1510.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1510.jpg', NULL, 'jpg', 'image/jpeg', 224003, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3125, 1167, 'EM-Tallin-Tag3-1513.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1513.jpg', NULL, 'jpg', 'image/jpeg', 138160, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3126, 1167, 'EM-Tallin-Tag3-1514.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1514.jpg', NULL, 'jpg', 'image/jpeg', 138303, 1754775174, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3127, 1167, 'EM-Tallin-Tag3-1515.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1515.jpg', NULL, 'jpg', 'image/jpeg', 163217, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3128, 1167, 'EM-Tallin-Tag3-1516.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1516.jpg', NULL, 'jpg', 'image/jpeg', 47500, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3129, 1167, 'EM-Tallin-Tag3-1519.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1519.jpg', NULL, 'jpg', 'image/jpeg', 46117, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3130, 1167, 'EM-Tallin-Tag3-1520.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1520.jpg', NULL, 'jpg', 'image/jpeg', 44318, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3131, 1167, 'EM-Tallin-Tag3-1524.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1524.jpg', NULL, 'jpg', 'image/jpeg', 48253, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3132, 1167, 'EM-Tallin-Tag3-1526.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1526.jpg', NULL, 'jpg', 'image/jpeg', 47008, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3133, 1167, 'EM-Tallin-Tag3-1529.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1529.jpg', NULL, 'jpg', 'image/jpeg', 49705, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3134, 1167, 'EM-Tallin-Tag3-1532.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1532.jpg', NULL, 'jpg', 'image/jpeg', 47414, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3135, 1167, 'EM-Tallin-Tag3-1540.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1540.jpg', NULL, 'jpg', 'image/jpeg', 74398, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3136, 1167, 'EM-Tallin-Tag3-1541.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1541.jpg', NULL, 'jpg', 'image/jpeg', 45336, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3137, 1167, 'EM-Tallin-Tag3-1547.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1547.jpg', NULL, 'jpg', 'image/jpeg', 48278, 1754775175, '399 x 600', 399, 600, 1, 1);
INSERT INTO `files` VALUES (3138, 1167, 'EM-Tallin-Tag3-1550.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1550.jpg', NULL, 'jpg', 'image/jpeg', 79246, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3139, 1167, 'EM-Tallin-Tag3-1553.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1553.jpg', NULL, 'jpg', 'image/jpeg', 107632, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3140, 1167, 'EM-Tallin-Tag3-1556.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1556.jpg', NULL, 'jpg', 'image/jpeg', 101314, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3141, 1167, 'EM-Tallin-Tag3-1557.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1557.jpg', NULL, 'jpg', 'image/jpeg', 70967, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3142, 1167, 'EM-Tallin-Tag3-1558.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1558.jpg', NULL, 'jpg', 'image/jpeg', 104785, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3143, 1167, 'EM-Tallin-Tag3-1559.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1559.jpg', NULL, 'jpg', 'image/jpeg', 127731, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3144, 1167, 'EM-Tallin-Tag3-1560.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1560.jpg', NULL, 'jpg', 'image/jpeg', 122298, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3145, 1167, 'EM-Tallin-Tag3-1561.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1561.jpg', NULL, 'jpg', 'image/jpeg', 100516, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3146, 1167, 'EM-Tallin-Tag3-1562.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1562.jpg', NULL, 'jpg', 'image/jpeg', 100595, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3147, 1167, 'EM-Tallin-Tag3-1563.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1563.jpg', NULL, 'jpg', 'image/jpeg', 90482, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3148, 1167, 'EM-Tallin-Tag3-1565.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1565.jpg', NULL, 'jpg', 'image/jpeg', 110857, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3149, 1167, 'EM-Tallin-Tag3-1566.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1566.jpg', NULL, 'jpg', 'image/jpeg', 107311, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3150, 1167, 'EM-Tallin-Tag3-1567.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1567.jpg', NULL, 'jpg', 'image/jpeg', 100471, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3151, 1167, 'EM-Tallin-Tag3-1568.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1568.jpg', NULL, 'jpg', 'image/jpeg', 90245, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3152, 1167, 'EM-Tallin-Tag3-1574.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1574.jpg', NULL, 'jpg', 'image/jpeg', 159559, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3153, 1167, 'EM-Tallin-Tag3-1575.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1575.jpg', NULL, 'jpg', 'image/jpeg', 159057, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3154, 1167, 'EM-Tallin-Tag3-1578.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1578.jpg', NULL, 'jpg', 'image/jpeg', 90470, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3155, 1167, 'EM-Tallin-Tag3-1582.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1582.jpg', NULL, 'jpg', 'image/jpeg', 86215, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3156, 1167, 'EM-Tallin-Tag3-1584.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1584.jpg', NULL, 'jpg', 'image/jpeg', 126743, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3157, 1167, 'EM-Tallin-Tag3-1585.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1585.jpg', NULL, 'jpg', 'image/jpeg', 159389, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3158, 1167, 'EM-Tallin-Tag3-1591.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1591.jpg', NULL, 'jpg', 'image/jpeg', 146715, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3159, 1167, 'EM-Tallin-Tag3-1592.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1592.jpg', NULL, 'jpg', 'image/jpeg', 144875, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3160, 1167, 'EM-Tallin-Tag3-1596.jpg', 'file', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023/EM-Tallin-Tag3-1596.jpg', NULL, 'jpg', 'image/jpeg', 107478, 1754775175, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3404, 1, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1.ppt', 'file', '/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 1138176, 1755986594, NULL, 0, 0, 0, 0);
INSERT INTO `files` VALUES (3660, 1168, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', 'file', '/uurime/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 1138176, 1756118076, NULL, 0, 0, 1, 0);
INSERT INTO `files` VALUES (3661, 1168, 'Fonoluku salajane kood.pdf', 'file', '/uurime/Fonoluku salajane kood.pdf', NULL, 'pdf', 'application/pdf', 1036688, 1756118230, NULL, 0, 0, 1, 0);
INSERT INTO `files` VALUES (3662, 1168, 'DSC_7550.jpg', 'file', '/uurime/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1756119157, '2953 x 1918', 2953, 1918, 0, 0);
INSERT INTO `files` VALUES (3663, 1, 'crop_efektne_valgusmang.png', 'file', '/crop_efektne_valgusmang.png', NULL, 'png', 'image/png', 366774, 1756119620, '492 x 492', 492, 492, 1, 0);
INSERT INTO `files` VALUES (3664, 1170, 'EM-Tallin-Tag2-001.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-001.jpg', NULL, 'jpg', 'image/jpeg', 63891, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3665, 1170, 'EM-Tallin-Tag2-002.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-002.jpg', NULL, 'jpg', 'image/jpeg', 48329, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3666, 1170, 'EM-Tallin-Tag2-006.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-006.jpg', NULL, 'jpg', 'image/jpeg', 68219, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3667, 1170, 'EM-Tallin-Tag2-011.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-011.jpg', NULL, 'jpg', 'image/jpeg', 61933, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3668, 1170, 'EM-Tallin-Tag2-012.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-012.jpg', NULL, 'jpg', 'image/jpeg', 63633, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3669, 1170, 'EM-Tallin-Tag2-015.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-015.jpg', NULL, 'jpg', 'image/jpeg', 51469, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3670, 1170, 'EM-Tallin-Tag2-016.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-016.jpg', NULL, 'jpg', 'image/jpeg', 57184, 1756128249, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3671, 1170, 'EM-Tallin-Tag2-020.jpg', 'file', '/spordigalerii/meeting-15-03-2012/EM-Tallin-Tag2-020.jpg', NULL, 'jpg', 'image/jpeg', 60625, 1756128250, '902 x 600', 902, 600, 1, 1);
INSERT INTO `files` VALUES (3673, 1147, 'kartulid.jpg', 'file', '/Uudised/Uudised 2025/kartulid.jpg', NULL, 'jpg', 'image/jpeg', 327803, 1758007868, '2048 x 1280', 2048, 1280, 1, 0);
INSERT INTO `files` VALUES (3674, 1171, 'Marja-Liisa Müllermann.jpg', 'file', '/tokyo-2025/Marja-Liisa Müllermann.jpg', NULL, 'jpg', 'image/jpeg', 4244385, 1758187317, '2972 x 4458', 2972, 4458, 0, 0);
INSERT INTO `files` VALUES (3675, 1171, 'Janno Iljas.jpg', 'file', '/tokyo-2025/Janno Iljas.jpg', NULL, 'jpg', 'image/jpeg', 3288530, 1758187317, '2682 x 4023', 2682, 4023, 0, 0);
INSERT INTO `files` VALUES (3676, 1171, 'Martin Betlem.jpg', 'file', '/tokyo-2025/Martin Betlem.jpg', NULL, 'jpg', 'image/jpeg', 3016888, 1758187318, '2530 x 3795', 2530, 3795, 0, 0);
INSERT INTO `files` VALUES (3677, 1171, 'Martin Viljasaar.jpg', 'file', '/tokyo-2025/Martin Viljasaar.jpg', NULL, 'jpg', 'image/jpeg', 4838123, 1758187318, '2839 x 4259', 2839, 4259, 0, 0);
INSERT INTO `files` VALUES (3678, 1171, 'Rinat Raisp.jpg', 'file', '/tokyo-2025/Rinat Raisp.jpg', NULL, 'jpg', 'image/jpeg', 4729967, 1758187319, '2815 x 4223', 2815, 4223, 0, 0);
INSERT INTO `files` VALUES (3679, 1171, 'Simon Teiss.jpg', 'file', '/tokyo-2025/Simon Teiss.jpg', NULL, 'jpg', 'image/jpeg', 4062292, 1758187319, '2725 x 4087', 2725, 4087, 0, 0);
INSERT INTO `files` VALUES (3680, 1171, 'Tanel Visnap.jpg', 'file', '/tokyo-2025/Tanel Visnap.jpg', NULL, 'jpg', 'image/jpeg', 4604938, 1758187320, '2983 x 4474', 2983, 4474, 0, 0);
INSERT INTO `files` VALUES (3682, 1171, 'crop_Marja-Liisa Müllermann.png', 'file', '/tokyo-2025/crop_Marja-Liisa Müllermann.png', NULL, 'png', 'image/png', 11201944, 1758187460, '2972 x 3714', 2972, 3714, 0, 0);
INSERT INTO `files` VALUES (3684, 1171, 'crop_Janno Iljas.png', 'file', '/tokyo-2025/crop_Janno Iljas.png', NULL, 'png', 'image/png', 8403444, 1758187606, '2551 x 3189', 2551, 3189, 0, 0);
INSERT INTO `files` VALUES (3685, 1171, 'crop_Martin Betlem.png', 'file', '/tokyo-2025/crop_Martin Betlem.png', NULL, 'png', 'image/png', 8180516, 1758187650, '2528 x 3161', 2528, 3161, 0, 0);
INSERT INTO `files` VALUES (3686, 1171, 'crop_Martin Viljasaar.png', 'file', '/tokyo-2025/crop_Martin Viljasaar.png', NULL, 'png', 'image/png', 12033583, 1758187692, '2840 x 3552', 2840, 3552, 0, 0);
INSERT INTO `files` VALUES (3687, 1171, 'crop_Simon Teiss.png', 'file', '/tokyo-2025/crop_Simon Teiss.png', NULL, 'png', 'image/png', 10194676, 1758187796, '2725 x 3406', 2725, 3406, 0, 0);
INSERT INTO `files` VALUES (3689, 1171, 'crop_Tanel Visnap.png', 'file', '/tokyo-2025/crop_Tanel Visnap.png', NULL, 'png', 'image/png', 11446333, 1758187946, '2937 x 3671', 2937, 3671, 0, 0);
INSERT INTO `files` VALUES (3693, 1171, 'crop_Rinat Raisp.png', 'file', '/tokyo-2025/crop_Rinat Raisp.png', NULL, 'png', 'image/png', 12913669, 1758190457, '2816 x 3805', 2816, 3805, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for folders
-- ----------------------------
DROP TABLE IF EXISTS `folders`;
CREATE TABLE `folders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mtime` int DEFAULT NULL,
  `locked_file` int unsigned DEFAULT '0',
  `activities_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of folders
-- ----------------------------
BEGIN;
INSERT INTO `folders` VALUES (1, NULL, '/', 'Repository', 'dir', 1756119620, 1, 0);
INSERT INTO `folders` VALUES (923, 1, '/Organisatsioon', 'Organisatsioon', 'dir', 1710165414, 1, 0);
INSERT INTO `folders` VALUES (929, 1, '/Varia', 'Varia', 'dir', 1725900551, 1, 0);
INSERT INTO `folders` VALUES (933, 1, '/Avaleht', 'Avaleht', 'dir', 1755985243, 1, 0);
INSERT INTO `folders` VALUES (935, 933, '/Avaleht/test 2', 'test 2', 'dir', 1741386545, 1, 0);
INSERT INTO `folders` VALUES (937, 1, '/Konventeerimine', 'Konventeerimine', 'dir', 1711542685, 1, 0);
INSERT INTO `folders` VALUES (989, 1, '/galerii', 'galerii', 'dir', 1708159047, 1, 1);
INSERT INTO `folders` VALUES (994, 989, '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses', 'Sügisene treeningulaager Jõulumäe Tervisekeskuses', 'dir', 1725102998, 1, 1);
INSERT INTO `folders` VALUES (996, 989, '/galerii/epk-kergejoustik-turil-14-05-2003', 'EPK kergejõustik Türil 14.05.2003', 'dir', 1709141019, 1, 1);
INSERT INTO `folders` VALUES (1008, 989, '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022', 'Lääne-Virumaa KÜ koosolek Rakveres 15.10.2022', 'dir', 1711368633, 1, 1);
INSERT INTO `folders` VALUES (1009, 989, '/galerii/arlese-album-28-02-2024', 'Arlese album 28.02.2024', 'dir', 1711372675, 1, 1);
INSERT INTO `folders` VALUES (1011, 1, '/Logod', 'Logod', 'dir', 1711051098, 1, 0);
INSERT INTO `folders` VALUES (1017, 989, '/galerii/uus-popurii', 'Uus popurii', 'dir', 1719518503, 1, 1);
INSERT INTO `folders` VALUES (1018, 989, '/galerii/uus-test', 'Uus test', 'dir', 1723032621, 1, 1);
INSERT INTO `folders` VALUES (1019, 929, '/Varia/Varia esimene kaust', 'Varia esimene kaust', 'dir', 1718979767, 1, 0);
INSERT INTO `folders` VALUES (1021, 933, '/Avaleht/test', 'test', 'dir', 1742129375, 1, 0);
INSERT INTO `folders` VALUES (1026, 1, '/crop-test', 'crop-test', 'dir', 1725148715, 1, 0);
INSERT INTO `folders` VALUES (1038, 989, '/galerii/blaaa', 'Blaaa', 'dir', 1733048755, 1, 1);
INSERT INTO `folders` VALUES (1077, 1, '/Uudised', 'Uudised', 'dir', 1724399541, 1, 0);
INSERT INTO `folders` VALUES (1078, 1077, '/Uudised/Uudised 2024', 'Uudised 2024', 'dir', 1727267563, 1, 0);
INSERT INTO `folders` VALUES (1111, 1, '/tester', 'TESTER', 'dir', 1741383520, 1, 0);
INSERT INTO `folders` VALUES (1114, 1, '/spordialad', 'spordialad', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1118, 1114, '/spordialad/kergejoustik', 'kergejoustik', 'dir', 1727867052, 1, 0);
INSERT INTO `folders` VALUES (1119, 1118, '/spordialad/kergejoustik/ajakavad', 'ajakavad', 'dir', 1727867077, 0, 0);
INSERT INTO `folders` VALUES (1120, 1118, '/spordialad/kergejoustik/juhendid', 'juhendid', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1121, 1118, '/spordialad/kergejoustik/tulemused', 'tulemused', 'dir', 1727867651, 1, 0);
INSERT INTO `folders` VALUES (1125, 1, '/Juhatus', 'Juhatus', 'dir', 1730567842, 1, 0);
INSERT INTO `folders` VALUES (1126, 1125, '/Juhatus/2018-2023', '2018-2023', 'dir', 1730568106, 1, 0);
INSERT INTO `folders` VALUES (1147, 1077, '/Uudised/Uudised 2025', 'Uudised 2025', 'dir', 1758007847, 1, 0);
INSERT INTO `folders` VALUES (1158, 1111, '/tester/vana-kaust', 'VANA KAUST', 'dir', 1741383520, 1, 0);
INSERT INTO `folders` VALUES (1159, 1, '/booo', 'BÖÖÖ', 'dir', 1756415991, 0, 0);
INSERT INTO `folders` VALUES (1160, 1111, '/tester/tiidu-kaust', 'TIIDU KAUST', 'dir', 1741383520, 1, 0);
INSERT INTO `folders` VALUES (1163, 1, '/pildigalerii', 'Pildigalerii', 'dir', 1754681277, 1, 1);
INSERT INTO `folders` VALUES (1164, 1163, '/pildigalerii/uus-album-08-08-2026', 'Uus album 08.08.2026', 'dir', 1754681277, 1, 1);
INSERT INTO `folders` VALUES (1166, 1, '/spordigalerii', 'Spordigalerii', 'dir', 1754775036, 1, 1);
INSERT INTO `folders` VALUES (1167, 1166, '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023', 'Kergejõustiku võistlused Tartus 12.09.2023', 'dir', 1754775036, 1, 1);
INSERT INTO `folders` VALUES (1168, 1, '/uurime', 'UURIME', 'dir', 1756119137, 1, 0);
INSERT INTO `folders` VALUES (1170, 1166, '/spordigalerii/meeting-15-03-2012', 'Meeting 15.03.2012', 'dir', 1756127724, 1, 1);
INSERT INTO `folders` VALUES (1171, 1, '/tokyo-2025', 'Tokyo 2025', 'dir', 1758190457, 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for frontend_links
-- ----------------------------
DROP TABLE IF EXISTS `frontend_links`;
CREATE TABLE `frontend_links` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `linked_id` int unsigned DEFAULT NULL,
  `grouped_id` int unsigned DEFAULT NULL,
  `content_types_managament_id` int unsigned DEFAULT NULL,
  `frontend_class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frontend_template_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frontend_title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_types_managament_id_idx` (`content_types_managament_id`) USING BTREE,
  KEY `linked_id_idx` (`linked_id`) USING BTREE,
  KEY `grouped_id_idx` (`grouped_id`) USING BTREE,
  CONSTRAINT `content_types_managament_id_frontend_links_fk` FOREIGN KEY (`content_types_managament_id`) REFERENCES `content_types_management` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of frontend_links
-- ----------------------------
BEGIN;
INSERT INTO `frontend_links` VALUES (135, 247, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Kurtide kultuuri uudised', '/uudised/kultuuri-uudised/kurtide-kultuuri-uudised');
INSERT INTO `frontend_links` VALUES (164, 299, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Eesti Kurtide Liidu-põhikiri', '/status/eesti-kurtide-liidu-pohikiri');
INSERT INTO `frontend_links` VALUES (204, 13, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'EKSL sisekergejõustiku võistlused', '/spordikalender/2026/bobisoit-2025');
INSERT INTO `frontend_links` VALUES (269, 336, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Ajakava', '/pensionaride-sundmuste-kalender/sundmuste-kalender/ajakava');
INSERT INTO `frontend_links` VALUES (272, 38, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide tore päev', '/pensionaride-sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes');
INSERT INTO `frontend_links` VALUES (273, 39, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/pensionaride-sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes');
INSERT INTO `frontend_links` VALUES (274, 40, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev Tallinnas', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/esimese-sundmuse-loeng');
INSERT INTO `frontend_links` VALUES (275, 41, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev Tartus', '/pensionaride-sundmuste-kalender/2030/naistepaev-tartus');
INSERT INTO `frontend_links` VALUES (277, 337, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', '', '/spordikalender');
INSERT INTO `frontend_links` VALUES (278, 1, 337, 1, 'StandardHomeController', 'StandardHomeController.tpl.php', 'Homepage', '');
INSERT INTO `frontend_links` VALUES (279, 338, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 'Spordivõistluste ajakava', '/spordikalender/spordisundmuste-kalender/spordivoistluste-ajakava');
INSERT INTO `frontend_links` VALUES (282, 4, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maavõistlused Tartus', '/spordikalender/2026/bobisoit-2025');
INSERT INTO `frontend_links` VALUES (286, 8, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kergejõustikuvõistlused', '/spordikalender/2026/bobisoit-2025');
INSERT INTO `frontend_links` VALUES (288, 10, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Blaaa', 'Spordikalender/2024/blaaa');
INSERT INTO `frontend_links` VALUES (289, 11, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/spordikalender/2024/eesti-ja-lati-kurtide-jalgpalli-sopruskohtumine');
INSERT INTO `frontend_links` VALUES (290, 12, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maahoki', '/spordikalender/spordisundmuste-kalender/2025/maahoki');
INSERT INTO `frontend_links` VALUES (291, 13, 337, 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 'Jäähoki', '/spordikalender/2026/bobisoit-2025');
INSERT INTO `frontend_links` VALUES (292, 43, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Pensionäride kokkutulek', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/esimese-sundmuse-loeng');
INSERT INTO `frontend_links` VALUES (293, 1, NULL, 1, 'StandardHomeController', 'StandardHomeController.tpl.php', '', '');
INSERT INTO `frontend_links` VALUES (295, 342, NULL, 11, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordialad', '/spordialad');
INSERT INTO `frontend_links` VALUES (305, 348, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Kaasautor', '/kaasautorlus/vaatame-kaasautorlust');
INSERT INTO `frontend_links` VALUES (310, 353, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Uurime aadressi muutumist', '/blaaa/uurime-aadressi-muutumist');
INSERT INTO `frontend_links` VALUES (327, 377, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Eesti Kurtide Liidu juhatus 2018 - 2023', '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023');
INSERT INTO `frontend_links` VALUES (333, 379, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Spordi juhatus 2023 - 2028', '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028');
INSERT INTO `frontend_links` VALUES (334, 378, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Kultuuri juhatus 2023 - 2028', '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028');
INSERT INTO `frontend_links` VALUES (336, 378, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Kultuuri juhatus', '/kultuuri-juhatus');
INSERT INTO `frontend_links` VALUES (337, 379, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Spordi juhatus', '/spordi-juhatus');
INSERT INTO `frontend_links` VALUES (345, 392, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Liikmesühingud', '/liikmesuhingud');
INSERT INTO `frontend_links` VALUES (346, 393, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordiseltsid', '/spordiseltsid');
INSERT INTO `frontend_links` VALUES (347, 394, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Kultuuriseltsid', '/kultuuriseltsid');
INSERT INTO `frontend_links` VALUES (348, 396, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Eesti Kurtide Spordiliidu liikmesseltsid', '/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid');
INSERT INTO `frontend_links` VALUES (357, 400, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', '', '/pensionaride-sundmuste-kalender/esimene-kalender');
INSERT INTO `frontend_links` VALUES (358, 46, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Esimese sündmuse loeng', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/esimese-sundmuse-loeng');
INSERT INTO `frontend_links` VALUES (373, 433, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Uued liikmed', '/uued-liikmed');
INSERT INTO `frontend_links` VALUES (374, 439, NULL, 14, 'StandardVideosController', 'StandardVideosController.tpl.php', 'Videote list', '/videod/videote-list');
INSERT INTO `frontend_links` VALUES (376, 442, NULL, 18, 'Links', 'StandardLinksController.tpl.php', 'Partnerite lingid', '/statistika-lingid/lingid/partnerite-lingid');
INSERT INTO `frontend_links` VALUES (377, 443, NULL, 18, 'Links', 'StandardLinksController.tpl.php', '', '/statistika-lingid/teised-lingid');
INSERT INTO `frontend_links` VALUES (383, 451, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', 'Rekordid', '/rekordid');
INSERT INTO `frontend_links` VALUES (384, 452, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', '', '/statistika/saavutused/rekordid');
INSERT INTO `frontend_links` VALUES (385, 453, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', NULL, '/statistika/saavutused/edetabelid');
INSERT INTO `frontend_links` VALUES (387, 456, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Statistika avapauk', '/statistikastatistika-avapauk');
INSERT INTO `frontend_links` VALUES (388, 550, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Organisatsiooni kontaktandmed', '/organisatsioon/organisatsiooni-kontaktandmed');
INSERT INTO `frontend_links` VALUES (390, 283, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', '', '/uudised/spordiuudised');
INSERT INTO `frontend_links` VALUES (394, 608, NULL, 2, 'Article', 'StandardArticleController.tpl.php', 'Tänitame kindlati edasi', '/parenttanitame-kindlati-edasi');
INSERT INTO `frontend_links` VALUES (409, 97, 641, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Politico: Vene gaasitransiidi lõppemine tekitab regioonis pingeid', '/uudised/poliitika-uudised/politico-vene-gaasitransiidi-loppemine-tekitab-regioonis-pingeid');
INSERT INTO `frontend_links` VALUES (410, 98, 641, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Saksamaa päikeseenergia sektor vaevleb pankrotilaine küüsis', '/uudised/poliitika-uudised/saksamaa-paikeseenergia-sektor-vaevleb-pankrotilaine-kuusis');
INSERT INTO `frontend_links` VALUES (411, 99, 283, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Aigro jäi Innsbruckis esimesena lõppvõistluse ukse taha', '/uudised/spordiuudised/aigro-jai-innsbruckis-esimesena-loppvoistluse-ukse-taha');
INSERT INTO `frontend_links` VALUES (412, 100, 283, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Männama ja Lepik alustasid Dakari rallit: auto toimis suurepäraselt', '/uudised/spordiuudised/mannama-ja-lepik-alustasid-dakari-rallit-auto-toimis-suureparaselt');
INSERT INTO `frontend_links` VALUES (413, 47, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Sportlaste kokkutulek Tartus', '/pensionaride-sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes');
INSERT INTO `frontend_links` VALUES (414, 101, 247, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Marja Unt: raamatuaastal rõõmustame, aga peame rääkima ka murekohtadest', '/uudised/kultuuri-uudised/marja-unt-raamatuaastal-roomustame-aga-peame-raakima-ka-murekohtadest');
INSERT INTO `frontend_links` VALUES (418, 22, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Bobisõit 2025', '/spordikalender/spordisundmuste-kalender/2026/bobisoit-2025');
INSERT INTO `frontend_links` VALUES (425, 621, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Uudised', '/uudised/kultuuri-uudised/kurtide-kultuuri-uudised');
INSERT INTO `frontend_links` VALUES (427, 623, NULL, 18, 'Links', 'StandardLinksController.tpl.php', '', '/statistika-lingid');
INSERT INTO `frontend_links` VALUES (428, 624, NULL, 18, 'Links', 'StandardLinksController.tpl.php', 'Statistika uued lingid', '/statistika/saavutused/statistika-uued-lingid');
INSERT INTO `frontend_links` VALUES (436, 633, NULL, 17, 'StandardAchievementsController', 'StandardAchievementsControllertpl.php', '', '/statistika/saavutused');
INSERT INTO `frontend_links` VALUES (439, 102, 641, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Toidu käibemaksu langetamine 13 protsendile maksaks riigile 245 miljonit', '/uudised/poliitika-uudised/toidu-kaibemaksu-langetamine-13-protsendile-maksaks-riigile-245-miljonit');
INSERT INTO `frontend_links` VALUES (443, 637, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Eesti Kurtide Liidu pildigaleriide arhiiv', '/pildigalerii/eesti-kurtide-liidu-pildigaleriide-arhiiv');
INSERT INTO `frontend_links` VALUES (446, 638, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Pildigalerii', '/spordigalerii/pildigalerii');
INSERT INTO `frontend_links` VALUES (447, 69, 638, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev Tallinnas', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023');
INSERT INTO `frontend_links` VALUES (448, 639, NULL, 14, 'StandardVideosController', 'StandardVideosController.tpl.php', 'Uued videod', '/uus-videote-nimekiri/uued-videod');
INSERT INTO `frontend_links` VALUES (450, 71, 638, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Meeting 15.03.2012', '/spordigalerii/meeting-15-03-2012');
INSERT INTO `frontend_links` VALUES (451, 640, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Pensionäride sündmuste kalender', '/pensionaride-sundmuste-kalender');
INSERT INTO `frontend_links` VALUES (452, 49, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev Tallinnas', '/pensionaride-sundmuste-kalender/2025/kurtide-paev-tallinnas-1');
INSERT INTO `frontend_links` VALUES (455, 51, 640, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Taidlejate päev', '/pensionaride-sundmuste-kalender/2024/taidlejate-paev');
INSERT INTO `frontend_links` VALUES (456, 641, NULL, 3, 'News', 'StandardNewsListController.tpl.php', 'Poliitika uudised', '/uudised/poliitika-uudised');
INSERT INTO `frontend_links` VALUES (457, 69, 400, 8, 'EventsCalendar', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev Tallinnas', '/pensionaride-sundmuste-kalender/esimene-kalender/2025/kurtide-paev-tallinnas');
INSERT INTO `frontend_links` VALUES (458, 106, 283, 4, 'News', 'StandardNewsDetailController.tpl.php', 'Holger Peel: Eesti treenerikoolitus ei ela parimaid päevi', '/uudised/spordiuudised/holger-peel-eesti-treenerikoolitus-ei-ela-parimaid-paevi');
INSERT INTO `frontend_links` VALUES (463, 24, 338, 10, 'SportsCalendar', 'StandardSportsCalendarDetailController.tpl.php', 'Suvaline võistlus', '/spordikalender/spordisundmuste-kalender/2024/suvaline-voistlus');
INSERT INTO `frontend_links` VALUES (464, 110, 641, 4, 'News', 'StandardNewsDetailController.tpl.php', 'Kasvataja: Lõuna-Eestis tuleb tänavu poole kehvem kartulisaak', '/uudised/poliitika-uudised/kasvataja-louna-eestis-tuleb-tanavu-poole-kehvem-kartulisaak');
INSERT INTO `frontend_links` VALUES (466, 26, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Rahvusvaheline kurtide 3x3 korvpalli turniir Tallinn Cup 2025', 'Spordikalender/2025/rahvusvaheline-kurtide-3x3-korvpalli-turniir-tallinn-cup-2025');
INSERT INTO `frontend_links` VALUES (467, 27, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti meistrivõistlused para- ja kurtide kergejõustikus 2025', 'Spordikalender/2025/eesti-meistrivoistlused-para-ja-kurtide-kergejoustikus-2025');
INSERT INTO `frontend_links` VALUES (468, 28, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Talkur Open Bowling 2025', 'Spordikalender/2025/talkur-open-bowling-2025');
INSERT INTO `frontend_links` VALUES (471, 81, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Talkuri MV Discgolfis – tulemused ja kokkuvõte', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/eesti-kurtide-meistrivoistlused-koroonas');
INSERT INTO `frontend_links` VALUES (472, 89, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti kurtide meistrivõistlused koroonas', '/pensionaride-sundmuste-kalender/esimene-kalender/2024/eesti-kurtide-meistrivoistlused-koroonas');
INSERT INTO `frontend_links` VALUES (473, 90, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti kurtide võrkpalli MV toimusid Tallinnas', 'Esimene kalender/2025/eesti-kurtide-vorkpalli-mv-toimusid-tallinnas');
INSERT INTO `frontend_links` VALUES (474, 67, 637, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Uus album 08.08.2026', '/pildigalerii/uus-album-08-08-2026');
COMMIT;

-- ----------------------------
-- Table structure for frontend_options
-- ----------------------------
DROP TABLE IF EXISTS `frontend_options`;
CREATE TABLE `frontend_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `frontend_template_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_types_management_id` int unsigned DEFAULT NULL,
  `folder_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frontend_template_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `content_types_management_id_idx` (`content_types_management_id`) USING BTREE,
  KEY `frontend_template_name` (`frontend_template_name`),
  KEY `class_name` (`class_name`),
  KEY `frontend_template_path` (`frontend_template_path`),
  KEY `frontend_template_path_2` (`frontend_template_path`,`id`),
  CONSTRAINT `frontend_options_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `frontend_options_ibfk_2` FOREIGN KEY (`content_types_management_id`) REFERENCES `content_types_management` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of frontend_options
-- ----------------------------
BEGIN;
INSERT INTO `frontend_options` VALUES (1, 'Home (standard)', 1, 'home', 'StandardHomeController', 'StandardHomeController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (2, 'Article (standard)', 2, 'article', 'StandardArticleController', 'StandardArticleController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (3, 'News list (standard)', 3, 'news', 'StandardNewsListController', 'StandardNewsListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (4, 'News detail (standard)', 4, 'news', 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (5, 'Gallery list (standard)', 5, 'gallery', 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (6, 'Gallery detail (standard)', 6, 'gallery', 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (7, 'Events calendar list (standard)', 7, 'events_calendar', 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (8, 'Events calendar detail (standard)', 8, 'events_calendar', 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (9, 'Sports calendar list (standard)', 9, 'sports_calendar', 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (10, 'Sports calendar detail (standard)', 10, 'sports_calendar', 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (11, 'Sports areas (standard)', 11, 'sports_areas', 'SportsAreasController', 'SportsAreasController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (12, 'Board (standard)', 12, 'board', 'StandardBoardController', 'StandardBoardController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (13, 'Members (standard)', 13, 'members', 'StandardMembersController', 'StandardMembersController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (14, 'Videos (standard)', 14, 'videos', 'StandardVideosController', 'StandardVideosController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (15, 'Records (standard)', 15, 'statistics', 'StandardRecordsController', 'StandardRecordsController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (16, 'Rankings (standard)', 16, 'statistics', 'StandardRankingsController', 'StandardRankingsController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (17, 'Achievements (standard)', 17, 'statistics', 'StandardAchievementsController', 'StandardAchievementsControllertpl.php', 1);
INSERT INTO `frontend_options` VALUES (18, 'Links (standard)', 18, NULL, 'StandardLinksController', 'StandardLinksController.tpl.php', 1);
COMMIT;

-- ----------------------------
-- Table structure for frontend_template_locking
-- ----------------------------
DROP TABLE IF EXISTS `frontend_template_locking`;
CREATE TABLE `frontend_template_locking` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `frontend_template_locked_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `frontend_template_locked_id_idx` (`frontend_template_locked_id`) USING BTREE,
  KEY `id-idx` (`id`) USING BTREE,
  CONSTRAINT `template_locking_ibfk_1` FOREIGN KEY (`frontend_template_locked_id`) REFERENCES `frontend_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of frontend_template_locking
-- ----------------------------
BEGIN;
INSERT INTO `frontend_template_locking` VALUES (1, 1);
INSERT INTO `frontend_template_locking` VALUES (2, 2);
INSERT INTO `frontend_template_locking` VALUES (3, 3);
INSERT INTO `frontend_template_locking` VALUES (4, 4);
INSERT INTO `frontend_template_locking` VALUES (5, 5);
INSERT INTO `frontend_template_locking` VALUES (6, 6);
INSERT INTO `frontend_template_locking` VALUES (7, 7);
INSERT INTO `frontend_template_locking` VALUES (8, 8);
INSERT INTO `frontend_template_locking` VALUES (9, 9);
INSERT INTO `frontend_template_locking` VALUES (10, 10);
INSERT INTO `frontend_template_locking` VALUES (11, 11);
INSERT INTO `frontend_template_locking` VALUES (12, 12);
INSERT INTO `frontend_template_locking` VALUES (13, 13);
INSERT INTO `frontend_template_locking` VALUES (14, 14);
INSERT INTO `frontend_template_locking` VALUES (15, 15);
INSERT INTO `frontend_template_locking` VALUES (16, 16);
INSERT INTO `frontend_template_locking` VALUES (17, 17);
INSERT INTO `frontend_template_locking` VALUES (18, 18);
COMMIT;

-- ----------------------------
-- Table structure for gallery_list
-- ----------------------------
DROP TABLE IF EXISTS `gallery_list`;
CREATE TABLE `gallery_list` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `gallery_group_title_id` int unsigned DEFAULT NULL,
  `group_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_folder_id` int unsigned DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `list_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  KEY `parent_folder_id_idx` (`parent_folder_id`) USING BTREE,
  KEY `gallery_group_title_id_idx` (`gallery_group_title_id`) USING BTREE,
  KEY `folder_id_idx` (`folder_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `gallery_group_title_id_menu_content_ibfk` FOREIGN KEY (`gallery_group_title_id`) REFERENCES `gallery_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_folder_id_ibfk` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_menu_content_group_id_ibfk` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_parent_folder_id_ibfk` FOREIGN KEY (`parent_folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_gallery_list_ibfk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of gallery_list
-- ----------------------------
BEGIN;
INSERT INTO `gallery_list` VALUES (67, 637, 29, 'Pildigalerii', 1163, 1164, 'Uus album 08.08.2026', NULL, NULL, '/pildigalerii/uus-album-08-08-2026', '/pildigalerii/uus-album-08-08-2026', 3, 'Samantha Jones', 1, '2025-08-08 22:27:57', '2025-09-22 20:18:12');
INSERT INTO `gallery_list` VALUES (69, 638, 30, 'Spordigalerii', 1166, 1167, 'Kergejõustiku võistlused Tartus 12.09.2023', 'FOTO: Kairit Olenko', 'Kergejõustiku võistlused Tartus 12.09.2023', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023', '/spordigalerii/kergejoustiku-voistlused-tartus-12-09-2023', 3, 'Samantha Jones', 1, '2025-08-10 00:30:36', '2025-09-22 21:48:25');
INSERT INTO `gallery_list` VALUES (71, 638, 30, 'Spordigalerii', 1166, 1170, 'Meeting 15.03.2012', NULL, NULL, '/spordigalerii/meeting-15-03-2012', '/spordigalerii/meeting-15-03-2012', 3, 'Samantha Jones', 1, '2025-08-25 16:15:24', '2025-09-22 21:47:27');
COMMIT;

-- ----------------------------
-- Table structure for gallery_settings
-- ----------------------------
DROP TABLE IF EXISTS `gallery_settings`;
CREATE TABLE `gallery_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gallery_group_id` int unsigned DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `folder_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `albums_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `folder_id_idx` (`folder_id`) USING BTREE,
  KEY `albums_locked_idx` (`albums_locked`) USING BTREE,
  KEY `gallery_group_id_idx` (`gallery_group_id`) USING BTREE,
  KEY `gallery_idx` (`status`) USING BTREE,
  CONSTRAINT `gallery_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_settings_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_settings_ibfk_3` FOREIGN KEY (`gallery_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_settings_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of gallery_settings
-- ----------------------------
BEGIN;
INSERT INTO `gallery_settings` VALUES (29, 637, 1, 1, 1163, 'Pildigalerii', 'Eesti Kurtide Liidu pildigaleriide arhiiv', '/pildigalerii', '2025-08-08 22:27:25', '2025-08-25 16:24:49', 1);
INSERT INTO `gallery_settings` VALUES (30, 638, 1, 1, 1166, 'Spordigalerii', 'Pildigalerii', '/spordigalerii', '2025-08-10 00:29:24', '2025-09-22 21:46:11', 1);
COMMIT;

-- ----------------------------
-- Table structure for gallerylist_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `gallerylist_editors_assn`;
CREATE TABLE `gallerylist_editors_assn` (
  `gallerylist_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`gallerylist_id`,`user_id`),
  KEY `gallerylist_id_idx` (`gallerylist_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `gallerylist_users_assn_1` FOREIGN KEY (`gallerylist_id`) REFERENCES `gallery_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallerylist_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of gallerylist_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `gallerylist_editors_assn` VALUES (67, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (69, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (71, 1);
COMMIT;

-- ----------------------------
-- Table structure for genders
-- ----------------------------
DROP TABLE IF EXISTS `genders`;
CREATE TABLE `genders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `genders_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genders_ibfk_2` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genders_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of genders
-- ----------------------------
BEGIN;
INSERT INTO `genders` VALUES (1, 'Mehed', 1, 'John Doe', '2025-02-01 00:00:00', '2025-09-04 08:03:31', 1, 2);
INSERT INTO `genders` VALUES (2, 'Naised', 1, 'John Doe', '2025-02-01 00:00:00', NULL, 1, 2);
INSERT INTO `genders` VALUES (3, 'Noormehed', 1, 'John Doe', '2025-02-01 00:00:00', NULL, 1, 2);
INSERT INTO `genders` VALUES (4, 'Neiud', 1, 'John Doe', '2025-02-01 00:00:00', NULL, 1, 2);
INSERT INTO `genders` VALUES (5, 'Poisid', 1, 'John Doe', '2025-02-01 00:00:00', NULL, 1, 2);
INSERT INTO `genders` VALUES (6, 'Tüdrukud', 1, 'John Doe', '2025-02-01 00:00:00', '2025-02-07 12:12:24', 1, 2);
COMMIT;

-- ----------------------------
-- Table structure for genders_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `genders_editors_assn`;
CREATE TABLE `genders_editors_assn` (
  `genders_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`genders_id`,`user_id`),
  KEY `genders_id_idx` (`genders_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `genders_users_assn_1` FOREIGN KEY (`genders_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genders_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of genders_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `genders_editors_assn` VALUES (1, 3);
INSERT INTO `genders_editors_assn` VALUES (6, 3);
COMMIT;

-- ----------------------------
-- Table structure for items_per_page
-- ----------------------------
DROP TABLE IF EXISTS `items_per_page`;
CREATE TABLE `items_per_page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `items_per` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `items_per_num` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of items_per_page
-- ----------------------------
BEGIN;
INSERT INTO `items_per_page` VALUES (1, '10', 10);
INSERT INTO `items_per_page` VALUES (2, '25', 25);
INSERT INTO `items_per_page` VALUES (3, '50', 50);
INSERT INTO `items_per_page` VALUES (4, '100', 100);
COMMIT;

-- ----------------------------
-- Table structure for language
-- ----------------------------
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_active_idx` (`is_active`) USING BTREE,
  CONSTRAINT `is_active_fk` FOREIGN KEY (`is_active`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of language
-- ----------------------------
BEGIN;
INSERT INTO `language` VALUES (1, 'Estonian', 'et', 'et_EE', 1);
INSERT INTO `language` VALUES (2, 'English', 'en', 'en_US', 1);
INSERT INTO `language` VALUES (3, 'Russian', 'ru', 'ru_RU', 1);
COMMIT;

-- ----------------------------
-- Table structure for link_types
-- ----------------------------
DROP TABLE IF EXISTS `link_types`;
CREATE TABLE `link_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of link_types
-- ----------------------------
BEGIN;
INSERT INTO `link_types` VALUES (1, 'Destination');
INSERT INTO `link_types` VALUES (2, 'Attachment');
COMMIT;

-- ----------------------------
-- Table structure for links
-- ----------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `settings_id` int unsigned DEFAULT NULL,
  `settings_id_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `link_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `settings_id_idx` (`settings_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  KEY `files-id_idx` (`files_id`) USING BTREE,
  CONSTRAINT `links_category_id_ibfk` FOREIGN KEY (`category_id`) REFERENCES `links_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_files_id_ibfk` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_menu_content_group_id_ibfk` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `links_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_status_ibfk` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links
-- ----------------------------
BEGIN;
INSERT INTO `links` VALUES (1, 442, 1, 'Lingid', 'Eesti Kurtide Liit', 'https://www.ead.ee', NULL, NULL, NULL, 7, '2024-12-15 21:22:27', '2025-03-20 13:17:11', 1);
INSERT INTO `links` VALUES (2, 442, 1, 'Lingid', 'Tallinna ja Harjumaa Kurtide Ühing', 'https://www.thky.ee', NULL, 1, 'Liikmesühingud', 9, '2024-12-15 14:25:18', '2025-08-21 18:34:20', 1);
INSERT INTO `links` VALUES (3, 442, 1, 'Lingid', 'Eesti Puuetega Inimeste Koda', 'https://www.epikoda.ee', NULL, 2, 'Koostööpartnerid', 8, '2024-12-15 16:55:50', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (4, NULL, 4, 'Statistika uued lingid', 'Parimad sportlased ja seltsid', NULL, 2830, NULL, NULL, 3, '2025-01-11 23:11:19', '2025-08-25 13:57:47', 1);
INSERT INTO `links` VALUES (7, NULL, 4, 'Statistika uued lingid', 'Uus esitlus', NULL, 2755, NULL, NULL, 2, '2025-01-12 16:58:33', '2025-08-25 13:57:47', 1);
INSERT INTO `links` VALUES (9, NULL, 4, 'Statistika uued lingid', 'Põnev ettekanne', NULL, 2833, NULL, NULL, 1, '2025-01-12 20:53:42', '2025-08-25 13:57:47', 1);
INSERT INTO `links` VALUES (10, NULL, 1, 'Lingid', 'Testime uut aadressi', 'https://www.neti.ee', NULL, 2, 'Koostööpartnerid', 1, '2025-03-20 14:04:42', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (12, NULL, 1, 'Lingid', 'Uus aadress', 'https://www.neti.ee', NULL, NULL, NULL, 2, '2025-01-25 20:55:33', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (13, NULL, 1, 'Lingid', 'Uus testi aadress', 'https://www.talkur.ee', NULL, 2, 'Koostööpartnerid', 0, '2025-03-20 14:05:31', '2025-08-21 17:44:58', 2);
INSERT INTO `links` VALUES (28, NULL, 1, 'Lingid', 'Testime linki', NULL, NULL, NULL, NULL, 10, '2025-08-23 11:32:16', NULL, 1);
INSERT INTO `links` VALUES (51, 443, 2, 'Teised lingid', 'Uus link', 'blaaaa', NULL, NULL, NULL, 0, '2025-08-23 14:17:01', '2025-08-23 19:32:25', 1);
INSERT INTO `links` VALUES (70, 443, 2, 'Teised lingid', 'BLAAA', 'sdfghjklö', NULL, NULL, NULL, 2, '2025-08-23 18:57:22', '2025-08-23 19:49:02', 1);
INSERT INTO `links` VALUES (71, 443, 2, 'Teised lingid', 'BÖÖÖÖ', 'fghjkkbbbnn', NULL, NULL, NULL, 3, '2025-08-23 18:58:47', '2025-08-25 14:43:05', 1);
INSERT INTO `links` VALUES (72, 443, 2, 'Teised lingid', 'SAAB', 'dfghjkl', NULL, NULL, NULL, 1, '2025-08-23 19:04:03', '2025-08-23 19:32:25', 1);
INSERT INTO `links` VALUES (73, 623, 3, 'Statistika lingid', 'Uus fail', NULL, 3661, NULL, NULL, 1, '2025-08-23 21:53:17', '2025-08-25 13:52:58', 1);
INSERT INTO `links` VALUES (74, 623, 4, 'Statistika uued lingid', 'Uus esitlus', NULL, 3660, NULL, NULL, 0, '2025-08-25 13:56:00', '2025-08-25 13:57:47', 2);
INSERT INTO `links` VALUES (75, 623, 3, 'Statistika lingid', 'Uurime põhjalikult', '', 2833, 1, 'Liikmesühingud', 2, '2025-08-25 13:59:18', '2025-08-25 14:54:04', 1);
INSERT INTO `links` VALUES (76, 623, 3, 'Statistika lingid', 'TEST', NULL, NULL, NULL, NULL, 3, '2025-08-25 17:19:24', NULL, 2);
COMMIT;

-- ----------------------------
-- Table structure for links_category
-- ----------------------------
DROP TABLE IF EXISTS `links_category`;
CREATE TABLE `links_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `links_category_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links_category
-- ----------------------------
BEGIN;
INSERT INTO `links_category` VALUES (1, 'Liikmesühingud', 1, '2024-12-15 18:24:36', '2025-08-31 19:47:52');
INSERT INTO `links_category` VALUES (2, 'Koostööpartnerid', 1, '2024-12-15 18:24:49', '2025-08-31 20:33:32');
INSERT INTO `links_category` VALUES (3, 'Spordiseltsid', 2, '2025-08-31 19:27:29', '2025-08-31 20:49:19');
COMMIT;

-- ----------------------------
-- Table structure for links_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `links_editors_assn`;
CREATE TABLE `links_editors_assn` (
  `links_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`links_id`,`user_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  KEY `links_id_idx` (`links_id`) USING BTREE,
  CONSTRAINT `links_editors_assn_ibfk_1` FOREIGN KEY (`links_id`) REFERENCES `links_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_editors_assn_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `links_editors_assn` VALUES (1, 3);
INSERT INTO `links_editors_assn` VALUES (2, 3);
INSERT INTO `links_editors_assn` VALUES (1, 4);
INSERT INTO `links_editors_assn` VALUES (2, 4);
INSERT INTO `links_editors_assn` VALUES (3, 4);
INSERT INTO `links_editors_assn` VALUES (4, 4);
COMMIT;

-- ----------------------------
-- Table structure for links_settings
-- ----------------------------
DROP TABLE IF EXISTS `links_settings`;
CREATE TABLE `links_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `link_type` int unsigned DEFAULT NULL,
  `link_type_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `links_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `links_locked_idx` (`links_locked`) USING BTREE,
  KEY `link_type_idx` (`link_type`) USING BTREE,
  CONSTRAINT `links_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links_settings
-- ----------------------------
BEGIN;
INSERT INTO `links_settings` VALUES (1, 'Lingid', 'Partnerite lingid', 1, 1, 1, 'Destination', 442, '/statistika-lingid/lingid', '2024-12-14 11:48:54', '2025-08-23 11:32:16', 1, 'John Doe', 1);
INSERT INTO `links_settings` VALUES (2, 'Teised lingid', '', 1, 1, 1, 'Destination', 443, '/statistika-lingid/teised-lingid', '2024-12-15 19:45:43', '2025-08-25 17:19:00', 1, 'John Doe', 1);
INSERT INTO `links_settings` VALUES (3, 'Statistika lingid', '', 1, 1, 2, 'Attachment', 623, '/statistika-lingid', '2025-01-11 23:08:21', '2025-08-25 17:19:24', 1, 'John Doe', 1);
INSERT INTO `links_settings` VALUES (4, 'Statistika uued lingid', NULL, 1, 1, 2, 'Attachment', 624, '/statistika/saavutused/statistika-uued-lingid', '2025-01-13 15:06:09', '2025-08-25 13:57:47', 1, 'John Doe', 1);
COMMIT;

-- ----------------------------
-- Table structure for list_of_sliders
-- ----------------------------
DROP TABLE IF EXISTS `list_of_sliders`;
CREATE TABLE `list_of_sliders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_status` int unsigned DEFAULT '2',
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  KEY `admin_status_idx` (`admin_status`),
  CONSTRAINT `list_of_sliders_ibfk_2` FOREIGN KEY (`status`) REFERENCES `slider_list_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `list_of_sliders_ibfk_3` FOREIGN KEY (`admin_status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of list_of_sliders
-- ----------------------------
BEGIN;
INSERT INTO `list_of_sliders` VALUES (1, 'Sponsors', 1, 1, '2024-03-06 22:26:00', '2024-09-14 21:43:21');
INSERT INTO `list_of_sliders` VALUES (2, 'Advertising', 1, 1, '2024-03-07 21:24:41', '2024-10-11 21:28:10');
COMMIT;

-- ----------------------------
-- Table structure for locking
-- ----------------------------
DROP TABLE IF EXISTS `locking`;
CREATE TABLE `locking` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_locking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drawn_locking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of locking
-- ----------------------------
BEGIN;
INSERT INTO `locking` VALUES (1, 1, 'Free', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i> Free', 1);
INSERT INTO `locking` VALUES (2, 1, 'Locked', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i> Locked', 1);
COMMIT;

-- ----------------------------
-- Table structure for members
-- ----------------------------
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int unsigned DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `member_id` int unsigned DEFAULT NULL,
  `member_id_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `member_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registry_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representative_fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representative_telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representative_sms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representative_fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representative_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `members_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `member_id_idx` (`member_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_ibfk_3` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of members
-- ----------------------------
BEGIN;
INSERT INTO `members` VALUES (3, 2807, 2807, 396, 10, 'Spordiseltsid', 1, 'Tallinna Kurtide Spordiselts TALKUR', '80044916', NULL, 'Edgar Liim', NULL, '+372 567 12067', NULL, NULL, NULL, NULL, NULL, '+372 601 5361', 'Nõmme tee 2\nTallinn 13426', 'talkur93@gmail.com', 'https://talkur.ee', '75', 1, '2024-11-12 11:33:00', '2025-08-25 17:23:24');
INSERT INTO `members` VALUES (4, 2798, 2798, 396, 10, 'Spordiseltsid', 2, 'Pärnu Kurtide Spordiselts EERO', '80042975', NULL, 'Eero Pevkur', NULL, '+372 565 03052', NULL, NULL, NULL, NULL, NULL, '+372 442 7131', 'Lubja 48a\nPärnu 80010', 'ksseero@gmail.com', 'http://eero.onepagefree.com', '', 1, '2024-11-12 11:48:33', '2025-08-25 17:23:24');
INSERT INTO `members` VALUES (5, 2800, 2800, 396, 10, 'Spordiseltsid', 0, 'Tartu Kurtide Spordiselts KAAR', '80037661', NULL, 'Jaan-Raul Ojastu', NULL, '+372 585 44757', NULL, NULL, NULL, NULL, NULL, '', 'Suur-Kaar 56\nTartu 50404', 'kaaresport@kaaresport.ee', 'https://www.kaaresport.ee', '', 1, '2024-11-14 08:10:55', '2025-08-25 17:23:24');
COMMIT;

-- ----------------------------
-- Table structure for members_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `members_editors_assn`;
CREATE TABLE `members_editors_assn` (
  `members_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`members_id`,`user_id`),
  KEY `members_id_idx` (`members_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `members_settings_users_assn_1` FOREIGN KEY (`members_id`) REFERENCES `members_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of members_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `members_editors_assn` VALUES (10, 2);
INSERT INTO `members_editors_assn` VALUES (10, 4);
COMMIT;

-- ----------------------------
-- Table structure for members_options
-- ----------------------------
DROP TABLE IF EXISTS `members_options`;
CREATE TABLE `members_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `settings_id` int unsigned DEFAULT NULL,
  `input_key` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `activity_status` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`activity_status`) USING BTREE,
  KEY `members_settings_id_idx` (`settings_id`) USING BTREE,
  CONSTRAINT `activity_status_ibfk` FOREIGN KEY (`activity_status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `members_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of members_options
-- ----------------------------
BEGIN;
INSERT INTO `members_options` VALUES (82, 10, 1, 'Member name', 0, 1);
INSERT INTO `members_options` VALUES (83, 10, 2, 'Registry code', 2, 1);
INSERT INTO `members_options` VALUES (84, 10, 3, 'Bank account number', 10, 2);
INSERT INTO `members_options` VALUES (85, 10, 4, 'Representative\'s full name', 3, 1);
INSERT INTO `members_options` VALUES (86, 10, 5, 'Representative\'s telephone', 12, 2);
INSERT INTO `members_options` VALUES (87, 10, 6, 'Representative\'s SMS', 4, 1);
INSERT INTO `members_options` VALUES (88, 10, 7, 'Representative\'s fax', 9, 2);
INSERT INTO `members_options` VALUES (89, 10, 8, 'Representative\'s email', 11, 2);
INSERT INTO `members_options` VALUES (90, 10, 9, 'Description', 13, 2);
INSERT INTO `members_options` VALUES (91, 10, 10, 'Telephone', 14, 2);
INSERT INTO `members_options` VALUES (92, 10, 11, 'SMS', 15, 2);
INSERT INTO `members_options` VALUES (93, 10, 12, 'Fax', 5, 1);
INSERT INTO `members_options` VALUES (94, 10, 13, 'Address', 1, 1);
INSERT INTO `members_options` VALUES (95, 10, 14, 'Email', 7, 1);
INSERT INTO `members_options` VALUES (96, 10, 15, 'Website', 6, 1);
INSERT INTO `members_options` VALUES (97, 10, 16, 'Members number', 8, 1);
COMMIT;

-- ----------------------------
-- Table structure for members_settings
-- ----------------------------
DROP TABLE IF EXISTS `members_settings`;
CREATE TABLE `members_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT '2',
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `members_locked` int unsigned DEFAULT '0',
  `allowed_uploading` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `allowed_uploading_idx` (`allowed_uploading`) USING BTREE,
  CONSTRAINT `members_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_ibfk_3` FOREIGN KEY (`allowed_uploading`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_ibfk_4` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of members_settings
-- ----------------------------
BEGIN;
INSERT INTO `members_settings` VALUES (10, 'Spordiseltsid', 'Eesti Kurtide Spordiliidu liikmesseltsid', 1, 1, 396, '/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid', '2024-11-12 11:19:25', '2025-08-25 17:23:24', 1, 'John Doe', 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `depth` int DEFAULT '0',
  `left` int DEFAULT NULL,
  `right` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id_idx` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=642 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (47, NULL, 0, 16, 17);
INSERT INTO `menu` VALUES (247, 621, 1, 19, 20);
INSERT INTO `menu` VALUES (283, 621, 1, 21, 22);
INSERT INTO `menu` VALUES (299, NULL, 0, 14, 15);
INSERT INTO `menu` VALUES (336, 640, 1, 31, 32);
INSERT INTO `menu` VALUES (337, NULL, 0, 34, 37);
INSERT INTO `menu` VALUES (338, 337, 1, 35, 36);
INSERT INTO `menu` VALUES (342, NULL, 0, 38, 39);
INSERT INTO `menu` VALUES (353, NULL, 0, 26, 27);
INSERT INTO `menu` VALUES (377, 550, 1, 5, 6);
INSERT INTO `menu` VALUES (378, 550, 1, 7, 8);
INSERT INTO `menu` VALUES (379, 550, 1, 9, 10);
INSERT INTO `menu` VALUES (396, NULL, 0, 12, 13);
INSERT INTO `menu` VALUES (400, 640, 1, 29, 30);
INSERT INTO `menu` VALUES (439, NULL, 0, 42, 43);
INSERT INTO `menu` VALUES (442, 623, 1, 61, 62);
INSERT INTO `menu` VALUES (443, 623, 1, 63, 64);
INSERT INTO `menu` VALUES (452, 633, 2, 48, 49);
INSERT INTO `menu` VALUES (453, 633, 2, 50, 51);
INSERT INTO `menu` VALUES (456, NULL, 0, 46, 55);
INSERT INTO `menu` VALUES (550, NULL, 0, 4, 11);
INSERT INTO `menu` VALUES (608, NULL, 0, 40, 41);
INSERT INTO `menu` VALUES (621, NULL, 0, 18, 25);
INSERT INTO `menu` VALUES (623, NULL, 0, 60, 65);
INSERT INTO `menu` VALUES (624, 633, 2, 52, 53);
INSERT INTO `menu` VALUES (633, 456, 1, 47, 54);
INSERT INTO `menu` VALUES (636, NULL, 0, 56, 57);
INSERT INTO `menu` VALUES (637, NULL, 0, 58, 59);
INSERT INTO `menu` VALUES (638, NULL, 0, 66, 67);
INSERT INTO `menu` VALUES (639, NULL, 0, 44, 45);
INSERT INTO `menu` VALUES (640, NULL, 0, 28, 33);
INSERT INTO `menu` VALUES (641, 621, 1, 23, 24);
COMMIT;

-- ----------------------------
-- Table structure for menu_content
-- ----------------------------
DROP TABLE IF EXISTS `menu_content`;
CREATE TABLE `menu_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int unsigned DEFAULT NULL,
  `menu_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` int unsigned DEFAULT NULL,
  `menu_tree_hierarchy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homely_url` int unsigned DEFAULT NULL,
  `is_redirect` int unsigned DEFAULT NULL,
  `internal_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_page_id` int unsigned DEFAULT NULL,
  `selected_page_locked` int DEFAULT '0',
  `target_type` int unsigned DEFAULT NULL,
  `setting_locked` int unsigned DEFAULT NULL,
  `is_enabled` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `content_type_idx` (`content_type`) USING BTREE,
  KEY `target_type_idx` (`target_type`) USING BTREE,
  KEY `selected_page_id_idx` (`selected_page_id`) USING BTREE,
  CONSTRAINT `content_type_menu_content_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_id_menu_content_fk` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `selected_page_id_fk` FOREIGN KEY (`selected_page_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target_type_menu_content_fk` FOREIGN KEY (`target_type`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=642 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Homepage', NULL, 1, '/homepage', '', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (47, 47, 'QCubed arenduse koduleht', NULL, 8, '/qcubed-arenduse-koduleht', NULL, NULL, 1, NULL, 'https://qcubed.eu', NULL, 0, 2, 1, 1);
INSERT INTO `menu_content` VALUES (247, 247, 'Kultuuri uudised', 'Kurtide kultuuri uudised', 3, '/uudised/kultuuri-uudised', '/uudised/kultuuri-uudised/kurtide-kultuuri-uudised', 1, NULL, NULL, NULL, NULL, 1, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (283, 283, 'Spordiuudised', '', 3, '/uudised/spordiuudised', '/uudised/spordiuudised', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (299, 299, 'Status', 'Eesti Kurtide Liidu-põhikiri', 2, '/status', '/status/eesti-kurtide-liidu-pohikiri', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (336, 336, 'Sündmuste kalender', 'Ajakava', 5, '/pensionaride-sundmuste-kalender/sundmuste-kalender', '/pensionaride-sundmuste-kalender/sundmuste-kalender/ajakava', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (337, 337, 'Spordikalender', '', 6, '/spordikalender', '/spordikalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (338, 338, 'Spordisündmuste kalender', 'Spordivõistluste ajakava', 6, '/spordikalender/spordisundmuste-kalender', '/spordikalender/spordisundmuste-kalender/spordivoistluste-ajakava', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (342, 342, 'Spordialad', NULL, 10, '/spordialad', '/spordialad', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (353, 353, 'BLAAA', 'Uurime aadressi muutumist', 2, '/blaaa', '/blaaa/uurime-aadressi-muutumist', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (377, 377, 'Juhatus', 'Eesti Kurtide Liidu juhatus 2018 - 2023', 11, '/organisatsioon/juhatus', '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (378, 378, 'Kultuuri juhatus', 'Kultuuri juhatus 2023 - 2028', 11, '/organisatsioon/kultuuri-juhatus', '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (379, 379, 'Spordi juhatus', 'Spordi juhatus 2023 - 2028', 11, '/organisatsioon/spordi-juhatus', '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (396, 396, 'Spordiseltsid', 'Eesti Kurtide Spordiliidu liikmesseltsid', 12, '/spordiseltsid', '/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (400, 400, 'Esimene kalender', '', 5, '/pensionaride-sundmuste-kalender/esimene-kalender', '/pensionaride-sundmuste-kalender/esimene-kalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (439, 439, 'Videod', 'Videote list', 13, '/videod', '/videod/videote-list', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (442, 442, 'Lingid', 'Partnerite lingid', 17, '/statistika-lingid/lingid', '/statistika-lingid/lingid/partnerite-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (443, 443, 'Teised lingid', '', 17, '/statistika-lingid/teised-lingid', '/statistika-lingid/teised-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (452, 452, 'Rekordid', '', 14, '/statistika/saavutused/rekordid', '/statistika/saavutused/rekordid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (453, 453, 'Edetabelid', NULL, 15, '/statistika/saavutused/edetabelid', '/statistika/saavutused/edetabelid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (456, 456, 'Statistika', 'Statistika avapauk', 2, '/statistika', '/statistikastatistika-avapauk', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (550, 550, 'Organisatsioon', 'Organisatsiooni kontaktandmed', 2, '/organisatsioon', '/organisatsioon/organisatsiooni-kontaktandmed', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (608, 608, 'PARENT', 'Tänitame kindlati edasi', 2, '/parent', '/parent/tanitame-kindlati-edasi', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (621, 621, 'Uudised', '', 7, '/uudised', '/uudised/kultuuri-uudised/kurtide-kultuuri-uudised', 1, 2, '/uudised/kultuuri-uudised/kurtide-kultuuri-uudised', '', 247, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (623, 623, 'Statistika lingid', '', 17, '/statistika-lingid', '/statistika-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (624, 624, 'Statistika uued lingid', NULL, 17, '/statistika/saavutused/statistika-uued-lingid', '/statistika/saavutused/statistika-uued-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (633, 633, 'Saavutused', '', 16, '/statistika/saavutused', '/statistika/saavutused', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (636, 636, 'Suunamine kurtide liidu kodulehele', NULL, 8, '/suunamine-kurtide-liidu-kodulehele', NULL, NULL, 1, NULL, 'https://www.ead.ee/', NULL, 0, 4, 1, 1);
INSERT INTO `menu_content` VALUES (637, 637, 'Pildigalerii', 'Eesti Kurtide Liidu pildigaleriide arhiiv', 4, '/pildigalerii', '/pildigalerii/eesti-kurtide-liidu-pildigaleriide-arhiiv', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (638, 638, 'Spordigalerii', 'Pildigalerii', 4, '/spordigalerii', '/spordigalerii/pildigalerii', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (639, 639, 'Uus videote nimekiri', 'Uued videod', 13, '/uus-videote-nimekiri', '/uus-videote-nimekiri/uued-videod', 1, NULL, NULL, NULL, NULL, 0, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (640, 640, 'Pensionäride sündmuste kalender', NULL, 5, '/pensionaride-sundmuste-kalender', '/pensionaride-sundmuste-kalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (641, 641, 'Poliitika uudised', NULL, 3, '/uudised/poliitika-uudised', '/uudised/poliitika-uudised', 1, NULL, NULL, NULL, NULL, 0, NULL, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for metadata
-- ----------------------------
DROP TABLE IF EXISTS `metadata`;
CREATE TABLE `metadata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  CONSTRAINT `menu_content_id_metadata_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, 'Avalehe võtmesõnad', 'Avalehe kirjeldus', 'Kodulehe autor');
INSERT INTO `metadata` VALUES (74, 247, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (81, 283, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (91, 299, 'Eesti Kurtide Liit', 'Lahti seletatud põhikiri', 'John Doe');
INSERT INTO `metadata` VALUES (120, 336, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (121, 337, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (122, 338, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (124, 342, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (133, 353, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (155, 377, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (156, 378, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (157, 379, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (168, 396, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (170, 400, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (191, 439, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (193, 442, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (194, 443, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (199, 452, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (200, 453, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (202, 456, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (203, 550, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (207, 608, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (212, 623, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (213, 624, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (221, 633, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (222, 637, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (223, 638, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (224, 639, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (225, 640, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (226, 641, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `changes_id` int unsigned DEFAULT NULL,
  `menu_content_id` int unsigned DEFAULT NULL,
  `news_group_title_id` int unsigned DEFAULT NULL,
  `group_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `news_category_id` int unsigned DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint unsigned DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `news_category_id_idx` (`news_category_id`) USING BTREE,
  KEY `post_date_idx` (`post_date`) USING BTREE,
  KEY `available_from_idx` (`available_from`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `news_group_title_id_idx` (`news_group_title_id`) USING BTREE,
  KEY `changes_id_idx` (`changes_id`) USING BTREE,
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_category_id`) REFERENCES `category_of_news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_2` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_4` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_5` FOREIGN KEY (`news_group_title_id`) REFERENCES `news_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_6` FOREIGN KEY (`changes_id`) REFERENCES `news_changes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news
-- ----------------------------
BEGIN;
INSERT INTO `news` VALUES (97, NULL, 641, 52, 'Poliitika uudised', 'Politico: Vene gaasitransiidi lõppemine tekitab regioonis pingeid', NULL, NULL, '/uudised/poliitika-uudised/politico-vene-gaasitransiidi-loppemine-tekitab-regioonis-pingeid', 2898, '', 'Ungari peaminister Viktor Orban (vasakul) ja Slovakkia peaminister Robert Fico on Vene gaasitransiidi lõpetava Ukraina kõige teravamad kriitikud.Ungari peaminister Viktor Orban (vasakul) ja Slovakkia peaminister Robert Fico on Vene gaasitransiidi lõpetava Ukraina kõige teravamad kriitikud. ', 'SCANPIX / AFP', '<p>Vene gaasitarnete l&otilde;ppemine aastavahetusel Ukrainat l&auml;biva gaasitoru kaudu tekitab regioonis pingeid ja t&otilde;stab hindu, kuid ei peaks siiski ohustama energia varustuskindlust, kirjutas v&auml;ljaanne Politico teisip&auml;eval.</p>\n\n<p><strong>Ungari ja Slovakkia ettev&otilde;tted kaotavad konkurentsiv&otilde;imes</strong></p>\n\n<p><a href=\"https://www.politico.eu/newsletter/brussels-playbook/does-putin-turn-into-a-pumpkin-at-midnight/\" rel=\"noopener\" target=\"_blank\">Politico vahendatud eksperdi</a> hinnangul toob Vene odava gaasi l&otilde;ppemine kaasa hinnat&otilde;usu transiidi l&otilde;ppemise &uuml;le k&otilde;ige enam pahandanud Ungaris ja Slovakkias, kuid gaasi puudust pole ette n&auml;ha.</p>\n\n<p>&quot;Vene gaasitarnete j&auml;rsu languse t&otilde;ttu v&otilde;ivad Ungaris ja Slovakkias hinnad t&otilde;usta, millel on potentsiaalne m&otilde;ju kogu piirkonnale, suurendades survet Euroopa Liidule k&auml;rpida energiaarveid. Kuid ettev&otilde;tte ICIS gaasituru eksperdi Aura Sabaduse s&otilde;nul on k&uuml;simus pigem hinnas kui pakkumises,&quot; kirjutas Politico.</p>\n\n<p>&quot;T&scaron;ehhi gaasihoidlate t&auml;ituvus on umbes 67 protsenti, Slovakkias 76 protsenti ja Ungaris umbes 69 protsenti, nii et nendega peaks k&otilde;ik korras olema &ndash; n&otilde;udlus ei tundu olevat v&auml;ga suur ja ilmaprognoos j&auml;&auml;b hooaja keskmise piiresse,&quot; &uuml;tles Sabadus Politicole. &quot;L&otilde;ppkokkuv&otilde;ttes on k&uuml;simus Slovakkia ja Ungari ettev&otilde;tetes, mis teenivad t&auml;nu juurdep&auml;&auml;sule odavale Vene gaasile,&quot; lisas ta.</p>\n', '2025-01-03 19:37:55', '2025-09-21 19:47:33', 1, '2025-12-31 00:00:00', NULL, 3, 'Samantha Jones', 4, 0);
INSERT INTO `news` VALUES (98, 1, 641, 52, 'Poliitika uudised', 'Saksamaa päikeseenergia sektor vaevleb pankrotilaine küüsis', NULL, NULL, '/uudised/poliitika-uudised/saksamaa-paikeseenergia-sektor-vaevleb-pankrotilaine-kuusis', 2827, '', NULL, NULL, '<p>P&auml;ikesepaneelid Saksamaa eluhoonete katustel Autor/allikas: SCANPIX/Caro/Oberhaeuser</p>\n\n<p>Saksamaa p&auml;ikeseenergia turgu tabas n&otilde;udluse kasvu pidurdumise t&otilde;ttu pankroti- ja koondamiste laine, kirjutab <a href=\"https://www.ft.com/content/83b927f7-db90-49de-8f2c-d0fd88631573\" target=\"_self\">Financial Times</a>.</p>\n\n<p>Mitmed Saksa p&auml;ikesepaneelide paigaldamisega tegelevad ettev&otilde;tted on pankrotti l&auml;inud v&otilde;i pidanud strateegiamuudatusi vastu v&otilde;tma.</p>\n\n<p>Kuigi p&auml;ikesepaneelide m&uuml;&uuml;gi langus ja sellest tulenev &uuml;lek&uuml;llus on tarbijate jaoks kaasa toonud j&auml;rsu hinnalanguse, on selle m&otilde;ju investorite hinnangule olnud negatiivne.</p>\n\n<p>&quot;Mingil m&auml;&auml;ral on see konsolideerumine p&auml;rast paari erakordset aastat,&quot; &uuml;tles t&ouml;&ouml;stuse lobir&uuml;hma Solarpower Europe tegevjuhi aset&auml;itja Dries Acke. &quot;Punaste numbritega ei saa olla rohelist &uuml;leminekut. Sektor peab olema kasumlik.&quot;</p>\n\n<p>Saksamaal kasvas n&otilde;udlus p&auml;ikesepaneelide j&auml;rele p&auml;rast Venemaa t&auml;iemahulist sissetungi Ukrainasse 2022. aastal, kui tarbijad, kes seisid silmitsi h&uuml;ppeliselt kasvavate energiaarvetega, hakkasid rohkem kasutama p&auml;ikeseenergiat.</p>\n', '2025-01-03 19:42:23', '2025-09-21 15:39:38', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (99, NULL, 283, 46, 'Spordiuudised', 'Aigro jäi Innsbruckis esimesena lõppvõistluse ukse taha', NULL, NULL, '/uudised/spordiuudised/aigro-jai-innsbruckis-esimesena-loppvoistluse-ukse-taha', 2826, '', NULL, NULL, '<p>Nelja h&uuml;ppem&auml;e turnee esimesel kahel etapil l&otilde;ppv&otilde;istlusele p&auml;&auml;senud Artti Aigro Innsbrucki edasip&auml;&auml;su ei taganud.</p>\n\n<p>Turnee avaeetapil Oberstdorfis&nbsp;21. koha ning siis Garmisch-Partenkirchenis 23. koha teeninud Aigro maandus Innsbrucki m&auml;el 108,5 meetri kaugusele ning teenis h&uuml;ppe eest 94,8 punkti.</p>\n\n<p>25-aastasel eestlasel s&auml;ilis k&uuml;ll v&auml;ike lootus edasi p&auml;&auml;seda, aga teised konkurendid ei v&auml;&auml;ratanud ning Aigro l&otilde;petas eelv&otilde;istluse 51. kohaga ehk j&auml;i kokkuv&otilde;ttes esimesena l&otilde;ppv&otilde;istluselt v&auml;lja. Viimasena p&auml;&auml;ses edasi sloveen Lovro Kos, kes sai 108-meetrise h&uuml;ppe eest 95,1 punkti.</p>\n\n<p>&quot;Ma olen s&otilde;natu, sest vaadates videot, siis ma ei leia sellist viga, mis karistaks mind 10-15 meetriga,&quot; kommenteeris Aigro p&auml;rast sooritust. &quot;Eks ma pean treeneritega pika arutelu tegema, et mingi suur viga &uuml;les leida, mis h&uuml;pet nii palju m&otilde;jutab.&quot;</p>\n\n<p>Aigro oli enne Innsbrucki v&otilde;istlust Nelja h&uuml;ppem&auml;e turnee &uuml;ldkokkuv&otilde;ttes 532,7 punktiga 19. kohal. &quot;Kahju on natuke tuuri &uuml;ldkokkuv&otilde;ttes,&quot; tunnistab eestlane. &quot;See koht langeb p&auml;ris k&otilde;vasti, aga midagi ei ole. L&auml;hme edasi Bischofshofenisse, kus &uuml;ritab natukenegi h&uuml;ppetaset parandada.&quot;</p>\n', '2025-01-03 19:43:00', '2025-09-19 12:11:32', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (100, NULL, 283, 46, 'Spordiuudised', 'Männama ja Lepik alustasid Dakari rallit: auto toimis suurepäraselt', NULL, NULL, '/uudised/spordiuudised/mannama-ja-lepik-alustasid-dakari-rallit-auto-toimis-suureparaselt', 2825, '', NULL, NULL, '<p>Eesti rallis&otilde;itjad Urvo M&auml;nnama ja Risto Lepik tegid reedel algust Dakari ralliga, kui teenisid 29 kilomeetri pikkusel proloogil 22. koha.</p>\n\n<p>M&auml;nnama ja Lepik (Overdrive Racing) l&auml;bisid distantsi ajaga 16.41, millega p&auml;lvisid 22. koha. &quot;Proloog on tehtud, auto toimis &uuml;ldpildis suurep&auml;raselt, tehniline pool toimis ideaalselt. Mul navigeerimispuldil tekkisid probleemid, aga kuna meil on seal mitu pulti ja erinevaid nuppe, siis v&otilde;tsime kohe j&auml;rgmised aparaadid kasutusele,&quot; r&auml;&auml;kis kaardilugeja Lepik p&auml;rast katset.</p>\n\n<p>&quot;Katse oli tore ja tulemus ka hea. Eks n&uuml;&uuml;d n&auml;ha, kuidas [laup&auml;eval] katsele peaminek meil on, kus t&auml;pselt asume. Eelmise aastaga on natuke seda olukorda muudetud, me ei pruugi 22. peale minna,&quot; lisas Lepik.</p>\n', '2025-01-03 19:43:33', '2025-09-19 12:11:17', 0, NULL, NULL, 3, 'Samantha Jones', 2, 0);
INSERT INTO `news` VALUES (101, NULL, 247, 20, 'Kultuuri uudised', 'Marja Unt: raamatuaastal rõõmustame, aga peame rääkima ka murekohtadest', NULL, NULL, '/uudised/kultuuri-uudised/marja-unt-raamatuaastal-roomustame-aga-peame-raakima-ka-murekohtadest', 1121, '2840', NULL, NULL, '<p>Kirjandussaade \"Loetud ja kirjutatud\" heitis seekord pilgu ettepoole – mida toob meile eesti raamatu aasta 2025? Raamatuaasta peakorraldaja ning Eesti Kirjanduse Seltsi tegevjuht Marja Unt sõnas saates, et peamine eesmärk on selgitada inimestele omakeelse kirjanduse ja kirjakeele väärtust.</p>\n\n<p>Eesti raamatu aastat ei tähistata sugugi esimest korda. \"Eesti raamatu aasta traditsioon sai alguse 1935. aastal, kui tähistati toona esimeseks teadaolevaks eestikeelseks raamatuks peetud Wanradti ja Koelli katekismuse 400. aastapäeva. Toimusid kõikvõimalikud raamatu ja kirjandusega seotud sündmused üle Eesti ja esimese raamatu aasta üheks eesmärgiks oli meie omakeelset raamatu kultuuri väärtustada, teha toonases sõnakasutuses heas mõttes raamatu propagandat, et meie omakeelset raamatut kui kultuuri alustala laiemalt rahva teadvusesse viia,\" rääkis Unt.<img alt=\"\" id=\"2840\" src=\"/project/tmp/_files/thumbnail/crop_Tiit töömõtetes.png\" style=\"height:320px; width:320px\" /></p>\n', '2025-01-06 22:26:29', '2025-09-19 12:11:05', 0, NULL, NULL, 3, 'Samantha Jones', 3, 0);
INSERT INTO `news` VALUES (102, NULL, 641, 52, 'Poliitika uudised', 'Toidu käibemaksu langetamine 13 protsendile maksaks riigile 245 miljonit', NULL, NULL, '/uudised/poliitika-uudised/toidu-kaibemaksu-langetamine-13-protsendile-maksaks-riigile-245-miljonit', 2897, '', 'Juustud poeletil.', 'Priit Mürk/ERR', '<p>Kui riik langetaks toidu käibemaksumäära 13 protsendile, vähendaks see riigi tuleva aasta maksulaekumist 245 miljoni euro võrra, näitavad rahandusministeeriumi arvutused.</p>\n\n<p>1. juulist tõusis Eestis käibemaks 24 protsendini. Tänavu toob käibemaksutõus riigieelarvesse rahandusministeeriumi prognoosi kohaselt hinnanguliselt 108 miljonit eurot lisatulu ning selle hulgas on kõigilt kaupadelt ja teenustelt laekuv maks. Eraldi arvestust toidult käibemaksutõusu tagajärel saadavast lisalaekumisest rahandusministeerium ei pea.</p>\n\n<p>Juuni lõpus algatatud petitsioon toidukaupade käibemaksumäära vähendamiseks oli teisipäeva keskpäevaks kogunud üle 90 000 toetusallkirja, kuid seni on valitsus eesotsas peaminister Kristen Michaliga ettepanekusse kriitiliselt suhtunud.</p>\n\n<p>Kui aga toidu käibemaksumäära tõepoolest langetataks, siis näiteks 13-protsendiline toiduainete käibemaks tähendaks rahandusministeeriumi arvutuste kohaselt, et toidukaupadelt laekuks tuleval aastal 290 miljonit eurot käibemaksu, mida oleks 245 miljonit eurot vähem, kui 24-protsendilise standardmäära puhul.</p>\n\n<p>\"Sealjuures võib soodusmäära kehtestamine olla ka väiksema kuluga, kui rakendada soodusmäära vaid konkreetsetele kaubagruppidele, nagu on tehtud Lätis. Seal kehtib soodusmäär kindla nimekirja alusel värskele puu- ja köögiviljale, kuid see ei laiene kuivatatud, soolatud või külmutatud toodetele ega ka eksootilistele toodetele,\" selgitas rahandusministeeriumi kommunikatsiooniosakonna nõunik Anna-Liisa Villmann.</p>\n\n<p>Villmann ütles, et hinnavõrdluste põhjal ei ole tegelikkuses Läti hinnatasemel Eestiga vahet.</p>\n\n<p>Coop Eesti keskühistu on välja toonud, et kui langetadagi ainult põhitoiduainete – piima, värske liha ja köögiviljade – käibemaksu, oleks mõju riigi laekumisele märksa väiksem ehk 30 miljonit eurot.</p>\n', '2025-08-05 09:25:40', '2025-09-21 15:39:01', 0, NULL, NULL, 3, 'Samantha Jones', 2, 0);
INSERT INTO `news` VALUES (106, NULL, 283, 46, 'Spordiuudised', 'Holger Peel: Eesti treenerikoolitus ei ela parimaid päevi', NULL, NULL, '/uudised/spordiuudised/holger-peel-eesti-treenerikoolitus-ei-ela-parimaid-paevi', 1121, '', NULL, NULL, '<p>Aasta parima meessportlase Johannes Ermi treener Holger Peel rääkis Vikeraadios suurepärasest hooajast, kuid tõdes, et mitmevõistluse kultuur Eestis võib tulevikus treenerite puudumisel hoobi saada.</p>\n\n<p>Peel rääkis \"Vikerhommikus\" ütles, et kümnevõistlusel on Eestis traditsioonid, millele panid aluse Aleksander Klumberg ja Fred Kudu. Lisaks tõi Peel välja TV 10 Olümpiastardi võistlussarja, mis annab noortele kergejõustiklastele võimaluse erinevate aladega tutvuda.</p>\n\n<p>\"Teave on meil väga kõva, see on kindlasti teiste riikidega võrreldes oluliselt kõvem. Küllalt noored lapsed teavad, et selline ala on olemas ja eestlane ilmselt ei olegi nii andekas, et üksikaladel kõrgelt lennata,\" sõnas Peel. \"Kümnevõistlus on raske, seal on kaks pikka päeva, kümme ala, väga erinevad treeningud. Johanneski ütles aastalõpu peoõhtul, et üksikala oleks teha palju kergem.\"</p>\n\n<p>Kui talispordis peavad Eesti parimad sportlased kõrgematesse tippudesse jõudmiseks minema teiste koondiste juurde harjutama, siis mitmevõistluses sellist vajadust pole. \"Kümnevõistlejad küll ei pea Eestist ära minema, see on selge. Mõnedel aladel on see vajadus tingimuste pärast, sest meie riik ei suuda kindlustada kõike,\" ütles Peel.</p>\n', '2025-09-15 12:37:22', '2025-09-19 12:10:54', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (110, NULL, 641, 52, 'Poliitika uudised', 'Kasvataja: Lõuna-Eestis tuleb tänavu poole kehvem kartulisaak', NULL, NULL, '/uudised/poliitika-uudised/kasvataja-louna-eestis-tuleb-tanavu-poole-kehvem-kartulisaak', 3673, '', ' Varane kartul Paide turul', 'Olev Kenk/ERR', '<p>Lõuna-Eestis on kevadised vihmad ja kartuli hilisem mahapanek sügisesele saagile palju kahju teinud. Kesk-Eestis on saak parem, kuid praeguse vihmaga kartulit maast võtta ei saa. Eesti kartuli hinda napp saak aga üles ei vii, sest seda ei luba kauplusekettide hinnapoliitika.</p>\n\n<p>Lõuna-Eestis on kartuli saagikus tänavu kasvatajati erinev, kuigi kehvem, kui loodeti, ütles tulundusühistu Eestimaa Kartul juhatuse liige Rasmus Kolberg. </p>\n\n<p>\"Meie inimesed pole sisuliselt veel koristama hakanud – loodetakse pisut ka saagikuse tõusu, aga ilmatingimused on ka väga keerulised. Täpset pilti veel ees ei ole, aga saak on kindlasti poole võrra väiksem kui planeeritud,\" rääkis Kolberg.</p>\n\n<p>Põhjuseks on paljuski liigniiskus kevadperioodil – Lõuna- ja Kesk-Eestis sadas palju vihma.</p>\n\n<p>\"Paljudel lihtsalt uppus kartul ära ja tärkamisprotsent oli väga madal. Veel üks põhjus oli väga agressiivne lehemädanik – õigel ajal ei saanud taimekaitsetööd teha, sest põld ei kandnud,\" lausus Kolberg.  </p>\n\n<p>Tulundusühistul Eestimaa Kartul on kaheksa ühistu liiget: kõige põhjapoolsem on Kolbergi kasvatus Jõgeva vallas, kõige lõunapoolsem Verioral. Neil kõigil on niiskuseprobleem.</p>\n\n<p><strong>Kolberg: Eesti kartuliga isevarustatuse tase langeb</strong></p>\n\n<p>Rasked olud on pannud mõned kasvatajad kartulist loobuma.</p>\n\n<p>\"Iga talunik teeb otsused kartulikasvatamise jätkamise kohta ise, aga järjest on olnud raskeid aastaid. Siitkandist on ka loobujaid, ka meie ühistu liikmed vähendavad kartuli alla minevaid pindasid. Eks seal on ka teisi põhjuseid: volatiivne turg ja see, et inimesed vananevad ja otsustavad lõpetada kartulikasvatamise,\" sõnas Kolberg.</p>\n\n<p>Eesti turg on üsna pisikene ja välismaise kartuli surve on suur. Tarbijale Kolberg suurt muutust ei näe, aga tootjale on olukord keeruline. </p>\n', '2025-09-16 10:28:45', '2025-09-19 13:00:38', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for news_changes
-- ----------------------------
DROP TABLE IF EXISTS `news_changes`;
CREATE TABLE `news_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `news_change_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `news_change_locked_idx` (`news_change_locked`) USING BTREE,
  CONSTRAINT `chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_changes
-- ----------------------------
BEGIN;
INSERT INTO `news_changes` VALUES (1, 'Uuendatud', '2024-08-23 20:00:00', NULL, 1, 1);
INSERT INTO `news_changes` VALUES (2, 'Täiendatud', '2024-08-23 18:00:00', '2024-08-23 21:27:11', 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for news_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `news_editors_assn`;
CREATE TABLE `news_editors_assn` (
  `news_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`news_id`,`user_id`),
  KEY `news_id_idx` (`news_id`) USING BTREE,
  KEY `news_users_assn_2` (`user_id`),
  CONSTRAINT `news_users_assn_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  CONSTRAINT `news_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `news_editors_assn` VALUES (97, 1);
INSERT INTO `news_editors_assn` VALUES (97, 2);
INSERT INTO `news_editors_assn` VALUES (97, 4);
INSERT INTO `news_editors_assn` VALUES (98, 1);
INSERT INTO `news_editors_assn` VALUES (98, 2);
INSERT INTO `news_editors_assn` VALUES (98, 4);
INSERT INTO `news_editors_assn` VALUES (99, 1);
INSERT INTO `news_editors_assn` VALUES (99, 2);
INSERT INTO `news_editors_assn` VALUES (99, 4);
INSERT INTO `news_editors_assn` VALUES (100, 1);
INSERT INTO `news_editors_assn` VALUES (100, 2);
INSERT INTO `news_editors_assn` VALUES (100, 4);
INSERT INTO `news_editors_assn` VALUES (101, 1);
INSERT INTO `news_editors_assn` VALUES (101, 2);
INSERT INTO `news_editors_assn` VALUES (101, 4);
INSERT INTO `news_editors_assn` VALUES (102, 4);
INSERT INTO `news_editors_assn` VALUES (106, 4);
INSERT INTO `news_editors_assn` VALUES (110, 4);
COMMIT;

-- ----------------------------
-- Table structure for news_files
-- ----------------------------
DROP TABLE IF EXISTS `news_files`;
CREATE TABLE `news_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `news_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `files_id_idx` (`files_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `news_group_id_idx` (`news_group_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `news_files_ibfk_1` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_files_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_files
-- ----------------------------
BEGIN;
INSERT INTO `news_files` VALUES (23, 102, 641, 'Eesti viipekeele staatus ja kasutamine', 2833, '2025-09-14 00:20:33', NULL, 2);
INSERT INTO `news_files` VALUES (24, 102, 641, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1', 3404, '2025-09-14 07:39:46', '2025-09-14 07:51:54', 1);
INSERT INTO `news_files` VALUES (26, 106, 283, 'EKL aruanne', 1134, '2025-09-15 12:41:46', '2025-09-15 12:42:05', 1);
COMMIT;

-- ----------------------------
-- Table structure for news_settings
-- ----------------------------
DROP TABLE IF EXISTS `news_settings`;
CREATE TABLE `news_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `news_locked` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `news_locked_idx` (`news_locked`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `is_reserved_ibfk` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_settings
-- ----------------------------
BEGIN;
INSERT INTO `news_settings` VALUES (20, 'Kultuuri uudised', 'Kurtide kultuuri uudised', 1, 1, 247, '/uudised/kultuuri-uudised', '2021-11-29 00:25:33', '2025-09-19 12:11:05', 1);
INSERT INTO `news_settings` VALUES (46, 'Spordiuudised', '', 1, 1, 283, '/uudised/spordiuudised', '2024-05-13 10:19:52', '2025-09-19 12:10:54', 1);
INSERT INTO `news_settings` VALUES (52, 'Poliitika uudised', NULL, 1, 1, 641, '/uudised/poliitika-uudised', '2025-09-13 00:28:38', '2025-09-20 14:37:20', 1);
COMMIT;

-- ----------------------------
-- Table structure for organizing_institution
-- ----------------------------
DROP TABLE IF EXISTS `organizing_institution`;
CREATE TABLE `organizing_institution` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `is_locked_ibfk` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `organizing_institution_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of organizing_institution
-- ----------------------------
BEGIN;
INSERT INTO `organizing_institution` VALUES (1, 'Eesti Paraolümpiakomitee võistlused', '2024-09-29 02:10:39', NULL, 1, 1);
INSERT INTO `organizing_institution` VALUES (2, 'Rahvusvahelised võistlused', '2024-09-29 02:16:07', '2025-09-19 20:01:52', 1, 2);
INSERT INTO `organizing_institution` VALUES (3, 'Treeninglaagrid', '2024-09-29 02:16:33', NULL, 1, 1);
INSERT INTO `organizing_institution` VALUES (5, 'Klubide traditsioonilised võistlused ja üritused', '2024-09-29 02:17:47', '2024-09-29 19:30:39', 1, 1);
INSERT INTO `organizing_institution` VALUES (6, 'Koolitused', '2024-11-24 23:46:24', '2025-09-01 22:02:09', 2, 1);
COMMIT;

-- ----------------------------
-- Table structure for qc_watchers
-- ----------------------------
DROP TABLE IF EXISTS `qc_watchers`;
CREATE TABLE `qc_watchers` (
  `table_key` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ts` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`table_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of qc_watchers
-- ----------------------------
BEGIN;
INSERT INTO `qc_watchers` VALUES ('qcubed-5.address', '0.96962000 1729575936');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.age_categories', '0.78731100 1739490314');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.age_categories_editors_assn', '0.78975600 1739490314');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.age_category_gender', '0.99020000 1740751913');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.age_category_gender_editors_assn', '0.08153600 1738221771');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.album', '0.08704800 1741452453');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.areas_of_sports', '0.74142200 1727466236');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.article', '0.29567500 1742378886');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.articles_editors_assn', '0.46112600 1736865645');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.athlete_gender', '0.43990500 1737401158');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.athletes', '0.17823200 1740677112');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.athletes_editors_assn', '0.24352300 1739142768');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.board', '0.44345500 1741452777');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.board_options', '0.32137100 1737057747');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.boards_editors_assn', '0.13256500 1730579004');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.boards_settings', '0.44601900 1741452777');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.category_of_article', '0.71081600 1734195042');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.category_of_news', '0.92552700 1731707238');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.changes', '0.40599200 1724427629');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.content_types_management', '0.07780100 1741956445');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.date_and_time_formats', '0.59612300 1742498979');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.error_pages', '0.85367600 1725100463');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.error_pages_editors_assn', '0.84335300 1722446899');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar', '0.49932500 1742475166');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar_area_sports_assn', '0.02561500 1726813560');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar_editors_assn', '0.79899000 1733229799');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_changes', '0.74973000 1736266917');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_settings', '0.56813300 1736547843');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_target_calendar_assn', '0.02737000 1726813560');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.example', '0.26335700 1742125765');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.files', '0.45243700 1742475166');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.folders', '0.82298800 1742129407');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_links', '0.78030500 1742770140');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_options', '0.19058300 1741962557');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_template_locking', '0.75551300 1742068599');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.galleries', '0.93316300 1733048755');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallery_list', '0.84476900 1741452455');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallery_settings', '0.83941400 1741452455');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallerylist_editors_assn', '0.26874800 1736259634');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gender', '0.86777400 1737396800');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.genders', '0.48503200 1739217675');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.genders_editors_assn', '0.48141300 1738455104');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.links', '0.28962400 1742497148');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.links_category', '0.45493600 1734280316');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.links_editors_assn', '0.76064000 1742476704');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.links_settings', '0.29154600 1742497148');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.list_of_galleries', '0.91938900 1733048755');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.list_of_sliders', '0.05261900 1728671290');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.locking', '0.67601500 1738436881');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members', '0.25873700 1736840534');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_editors_assn', '0.04096100 1731403980');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_options', '0.23386000 1736457978');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_settings', '0.63408100 1736807052');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.menu', '0.62468100 1742477228');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.menu_content', '0.63553600 1742477228');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.metadata', '0.38438200 1742388930');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.milestone', '0.09908100 1729575937');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news', '0.68700000 1742770177');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_changes', '0.97413400 1726224669');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_editors_assn', '0.68302800 1742770177');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_settings', '0.68338700 1742333747');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.organizing_institution', '0.76334300 1732484784');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.person', '0.59626700 1739736819');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.person_with_lock', '0.53716000 1737460963');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.project', '0.00256700 1729575937');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.records', '0.85699600 1740752011');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.records_editors_assn', '0.52842000 1739651646');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.reserve', '0.61388900 1738088238');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders', '0.21544900 1741797553');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders_editors_assn', '0.00201600 1730394249');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders_settings', '0.22127900 1741797553');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_areas', '0.85451600 1740752011');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_areas_competition_areas', '0.69560200 1740745028');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_calendar', '0.40580000 1738569098');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_calendar_editors_assn', '0.81425900 1738568983');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_changes', '0.31120400 1736923986');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_competition_areas', '0.66115900 1740676836');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_content_types', '0.75886500 1728165065');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_settings', '0.95070300 1736547847');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_tables', '0.29224400 1736926343');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.statistics_editors_assn', '0.60405800 1742388365');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.statistics_settings', '0.40046900 1742389020');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.statistics_slug_identifier', '0.50845100 1734716955');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.statistics_types', '0.46479300 1734554274');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.target_group_of_calendar', '0.62968800 1736842703');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.team_member_project_assn', '0.99436100 1729575936');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.temp_data', '0.32284400 1728132291');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.title_of_newsgroup', '0.83788400 1728515402');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.type_test', '0.45395200 1737460880');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.videos', '0.04388900 1742476574');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.videos_editors_assn', '0.75796900 1734171982');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.videos_settings', '0.04627200 1742476574');
COMMIT;

-- ----------------------------
-- Table structure for record_flags
-- ----------------------------
DROP TABLE IF EXISTS `record_flags`;
CREATE TABLE `record_flags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of record_flags
-- ----------------------------
BEGIN;
INSERT INTO `record_flags` VALUES (1, '<i class=\"fa fa-check fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i>');
INSERT INTO `record_flags` VALUES (2, '<i class=\"fa fa-times fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i>');
COMMIT;

-- ----------------------------
-- Table structure for records
-- ----------------------------
DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned DEFAULT NULL,
  `athlete_gender_id` int unsigned DEFAULT NULL,
  `age_category_id` int unsigned DEFAULT NULL,
  `sport_area_id` int unsigned DEFAULT NULL,
  `competition_area_id` int unsigned DEFAULT NULL,
  `result` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` enum('seconds','meters','points') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detailed_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `competition_venue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `competition_date` date DEFAULT NULL,
  `is_youth_records` tinyint unsigned DEFAULT '0',
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `athlete_id_idx` (`athlete_id`) USING BTREE,
  KEY `age_category_id_idx` (`age_category_id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `sport_area_id_idx` (`sport_area_id`) USING BTREE,
  KEY `competition_area_id_idx` (`competition_area_id`) USING BTREE,
  KEY `athlete_gender_id_idx` (`athlete_gender_id`) USING BTREE,
  CONSTRAINT `records_ibfk_1` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_2` FOREIGN KEY (`age_category_id`) REFERENCES `age_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_3` FOREIGN KEY (`sport_area_id`) REFERENCES `sports_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_4` FOREIGN KEY (`competition_area_id`) REFERENCES `sports_competition_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_5` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_6` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_ibfk_7` FOREIGN KEY (`athlete_gender_id`) REFERENCES `athlete_gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of records
-- ----------------------------
BEGIN;
INSERT INTO `records` VALUES (13, 22, 2, 5, 8, 3, '22.38', 'seconds', '', '', 'Darmstadt/GER', '2005-07-09', 1, 3, 'Samantha Jones', '2025-02-22 15:17:42', NULL, 1);
INSERT INTO `records` VALUES (18, 17, 2, 4, 8, 2, '10.45*', 'seconds', '-0.2', '', 'Valga', '2016-07-28', 1, 3, 'Samantha Jones', '2025-02-22 17:36:42', NULL, 1);
INSERT INTO `records` VALUES (19, 22, 2, 3, 8, 2, '11.05', 'seconds', '', '', 'Paide', '2008-07-28', 0, 3, 'Samantha Jones', '2025-02-22 17:39:22', NULL, 1);
INSERT INTO `records` VALUES (20, 12, 2, 5, 8, 2, '11.07', 'seconds', '', '', 'Paide', '2016-07-28', 0, 3, 'Samantha Jones', '2025-02-22 17:40:27', NULL, 1);
INSERT INTO `records` VALUES (25, 1, 1, 2, 8, 3, '23.11', 'seconds', '', '', 'rtgyhujikolö', '2008-07-28', 0, 3, 'Samantha Jones', '2025-02-28 13:45:42', NULL, 1);
INSERT INTO `records` VALUES (27, 47, 1, 4, 8, 3, '23.22', 'seconds', '', '', 'dfghjklö', '2005-07-23', 0, 3, 'Samantha Jones', '2025-02-28 13:56:39', NULL, 1);
INSERT INTO `records` VALUES (28, 22, 2, 3, 8, 5, '56.11', 'seconds', '', '', 'rtyhujiklöä', '2008-07-28', 0, 3, 'Samantha Jones', '2025-02-28 14:17:08', NULL, 1);
INSERT INTO `records` VALUES (29, 12, 2, 2, 8, 5, '56.10', 'seconds', '-0.01', '', 'rtyuiol', '2022-07-27', 1, 3, 'Samantha Jones', '2025-02-28 16:11:53', NULL, 1);
INSERT INTO `records` VALUES (30, 12, 2, 5, 8, 5, '56.22', 'seconds', '', '', 'dfghjklö', '2015-07-28', 0, 3, 'Samantha Jones', '2025-02-28 16:13:31', NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for records_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `records_editors_assn`;
CREATE TABLE `records_editors_assn` (
  `records_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`records_id`,`user_id`),
  KEY `records_id_idx` (`records_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `records_editors_assn_1` FOREIGN KEY (`records_id`) REFERENCES `records` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_editors_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of records_editors_assn
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for related_project_assn
-- ----------------------------
DROP TABLE IF EXISTS `related_project_assn`;
CREATE TABLE `related_project_assn` (
  `project_id` int unsigned NOT NULL,
  `child_project_id` int unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`child_project_id`),
  KEY `IDX_relatedprojectassn_2` (`child_project_id`),
  CONSTRAINT `related_project_assn_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  CONSTRAINT `related_project_assn_2` FOREIGN KEY (`child_project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of related_project_assn
-- ----------------------------
BEGIN;
INSERT INTO `related_project_assn` VALUES (4, 1);
INSERT INTO `related_project_assn` VALUES (1, 3);
INSERT INTO `related_project_assn` VALUES (1, 4);
COMMIT;

-- ----------------------------
-- Table structure for reserve
-- ----------------------------
DROP TABLE IF EXISTS `reserve`;
CREATE TABLE `reserve` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of reserve
-- ----------------------------
BEGIN;
INSERT INTO `reserve` VALUES (1, 1, 'Is reserved', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i>  Is reserved', 1);
INSERT INTO `reserve` VALUES (2, 1, 'Free', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i> Free', 1);
INSERT INTO `reserve` VALUES (3, 1, 'Locked', '<i class=\"fa fa-circle fa-lg\" style=\"color:#e6a819;line-height:0.1;\"></i> Locked', 1);
COMMIT;

-- ----------------------------
-- Table structure for slider_list_status
-- ----------------------------
DROP TABLE IF EXISTS `slider_list_status`;
CREATE TABLE `slider_list_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of slider_list_status
-- ----------------------------
BEGIN;
INSERT INTO `slider_list_status` VALUES (1, 1, 'Public carousel', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #449d44; line-height: .1;\"></i>  Public carousel', 1);
INSERT INTO `slider_list_status` VALUES (2, 1, 'Hidden carousel', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ff0000; line-height: .1;\"></i> Hidden carousel', 1);
INSERT INTO `slider_list_status` VALUES (3, 1, 'Carousel draft', '<i class=\"fa fa-circle-o fa-lg\" aria-hidden=\"true\" style=\"color: #000000; line-height: .1;\"></i> Carousel draft', 1);
INSERT INTO `slider_list_status` VALUES (4, 1, 'Carousel waiting...', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ffb00c; line-height: .1;\"></i> Carousel waiting...', 1);
COMMIT;

-- ----------------------------
-- Table structure for sliders
-- ----------------------------
DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int unsigned DEFAULT NULL,
  `file_id` int unsigned DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dimensions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `top` int DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `group_id_idx` (`group_id`) USING BTREE,
  KEY `order_id_idx` (`order`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  CONSTRAINT `sliders_ibfk_1` FOREIGN KEY (`status`) REFERENCES `slider_list_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sliders
-- ----------------------------
BEGIN;
INSERT INTO `sliders` VALUES (61, 1, 1462, 3, 'Eesti 100', 'https://www.ev100.ee/', '/Logod/Eesti_100_M.jpg', 'jpg', '200 x 158', 110, 86, 10, '2024-03-20 02:45:52', '2024-10-19 21:57:37', 1);
INSERT INTO `sliders` VALUES (62, 1, 1455, 6, 'Hea annetuste koguja', 'https://heakodanik.ee/wp-content/uploads/2015/10/annetuste-kogumise-hea-tava.pdf', '/Logod/HAK-200.png', 'png', '200 x 200', 100, 100, -3, '2024-03-20 02:45:52', '2024-09-15 16:13:37', 1);
INSERT INTO `sliders` VALUES (63, 1, 1457, 1, 'Merit tarkvara', 'https://www.merit.ee/', '/Logod/Merit.jpg', 'jpg', '242 x 60', 242, 60, 18, '2024-03-20 02:45:52', '2024-09-15 16:13:23', 1);
INSERT INTO `sliders` VALUES (64, 1, 1452, 5, 'Almic', 'https://almic.ee/', '/Logod/almic.png', 'png', '125 x 31', 125, 31, 37, '2024-03-20 02:45:52', '2024-09-15 16:12:54', 1);
INSERT INTO `sliders` VALUES (65, 1, 1453, 0, 'Eesti Kurtide Spordiliit', 'http://www.kurtidespordiliit.ee/', '/Logod/eksl.png', 'png', '987 x 830', 105, 88, 5, '2024-03-20 02:45:52', '2024-09-15 16:14:01', 1);
INSERT INTO `sliders` VALUES (67, 1, 1458, 4, 'Tartu linn', 'https://tartu.ee/et', '/Logod/tartu.png', 'png', '594 x 401', 149, 100, NULL, '2024-03-20 02:45:52', '2024-09-15 16:13:44', 1);
INSERT INTO `sliders` VALUES (71, 1, 1521, 2, 'Eesti Puuetega Inimeste Koda', 'https://epikoda.ee/', '/Logod/epikoda-logo-short.jpg', 'jpg', '1490 x 1500', 80, 80, 12, '2024-03-21 21:59:18', '2024-09-15 16:13:01', 1);
INSERT INTO `sliders` VALUES (103, 2, 1467, 1, 'Luik', NULL, '/Organisatsioon/vilinus reis 2262.jpg', 'jpg', '1936 x 1288', NULL, NULL, NULL, '2024-09-15 00:21:14', '2024-10-28 01:28:39', 1);
INSERT INTO `sliders` VALUES (104, 2, 1465, 2, NULL, NULL, '/Organisatsioon/valged_orhideed.jpg', 'jpg', '960 x 642', NULL, NULL, NULL, '2024-09-15 15:53:30', '2024-10-30 19:35:39', 1);
INSERT INTO `sliders` VALUES (109, 2, 2712, 3, 'Kõrred härmatises', NULL, '/galerii67681.jpg', 'jpg', '800 x 533', NULL, NULL, NULL, '2024-10-16 01:04:00', '2024-10-18 23:12:23', 1);
INSERT INTO `sliders` VALUES (113, 2, 1593, 0, 'Karikakrad vastu päikest', NULL, '/Konventeerimine/karikakrad_vihmas.jpg', 'jpg', '1280 x 868', NULL, NULL, NULL, '2024-10-19 21:03:12', '2024-10-19 21:06:27', 1);
COMMIT;

-- ----------------------------
-- Table structure for sliders_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `sliders_editors_assn`;
CREATE TABLE `sliders_editors_assn` (
  `sliders_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`sliders_id`,`user_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  KEY `sliders_id_idx` (`sliders_id`) USING BTREE,
  CONSTRAINT `sliders_settings_users_assn_1` FOREIGN KEY (`sliders_id`) REFERENCES `sliders_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sliders_settings_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sliders_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `sliders_editors_assn` VALUES (1, 2);
INSERT INTO `sliders_editors_assn` VALUES (2, 2);
INSERT INTO `sliders_editors_assn` VALUES (1, 4);
INSERT INTO `sliders_editors_assn` VALUES (2, 4);
COMMIT;

-- ----------------------------
-- Table structure for sliders_list
-- ----------------------------
DROP TABLE IF EXISTS `sliders_list`;
CREATE TABLE `sliders_list` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vi_0900_ai_ci NOT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `post_update_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  KEY `post_update_user` (`post_update_user`) USING BTREE,
  CONSTRAINT `sliders_list_ibfk_2` FOREIGN KEY (`status`) REFERENCES `slider_list_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vi_0900_ai_ci;

-- ----------------------------
-- Records of sliders_list
-- ----------------------------
BEGIN;
INSERT INTO `sliders_list` VALUES (1, 'Sponsors', 1, '2024-03-06 22:26:00', '2025-07-20 13:17:28', NULL);
INSERT INTO `sliders_list` VALUES (2, 'Advertisements', 1, '2024-03-07 21:24:41', '2025-07-20 11:01:56', NULL);
COMMIT;

-- ----------------------------
-- Table structure for sliders_settings
-- ----------------------------
DROP TABLE IF EXISTS `sliders_settings`;
CREATE TABLE `sliders_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint unsigned DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  KEY `admin_status_idx` (`admin_status`),
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `sliders_settings_ibfk_1` FOREIGN KEY (`status`) REFERENCES `slider_list_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sliders_settings_ibfk_2` FOREIGN KEY (`admin_status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sliders_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sliders_settings
-- ----------------------------
BEGIN;
INSERT INTO `sliders_settings` VALUES (1, 'Sponsors', 1, '2024-03-06 22:26:00', '2024-12-05 13:07:08', 0, NULL, NULL, 1, 'John Doe', 1);
INSERT INTO `sliders_settings` VALUES (2, 'Advertising', 1, '2024-03-07 21:24:41', '2025-03-12 16:39:13', 0, NULL, NULL, 1, 'John Doe', 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_areas
-- ----------------------------
DROP TABLE IF EXISTS `sports_areas`;
CREATE TABLE `sports_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_table_update_date` date DEFAULT NULL,
  `ranking_update_date` date DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `areas_of_sports_ibfk_1` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `areas_of_sports_ibfk_2` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_areas` VALUES (1, 'Bowling', NULL, NULL, '2021-07-08 23:41:56', '2024-09-27 22:38:13', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (2, 'Discgolf', NULL, NULL, '2021-07-08 23:42:34', '2021-07-09 23:51:43', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (3, 'Jalgpall', NULL, NULL, '2021-07-19 01:13:39', '2024-09-26 15:46:04', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (4, 'Kabe', NULL, NULL, '2021-07-19 01:14:11', '2025-01-16 12:48:02', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (5, 'Karate', NULL, NULL, '2021-07-19 01:14:35', '2021-08-04 14:54:50', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (6, 'Kelgutamine', NULL, NULL, '2021-07-19 01:33:57', '2021-08-04 14:48:28', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (7, 'Kepikõnd', NULL, NULL, '2021-07-19 01:34:18', '2021-11-26 03:31:03', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (8, 'Kergejõustik', '2025-02-28', NULL, '2021-07-19 01:34:50', '2024-09-27 13:14:07', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (9, 'Koroona', NULL, NULL, '2021-07-19 01:35:09', '2021-08-04 14:48:33', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (10, 'Korvpall', NULL, NULL, '2021-07-19 01:35:34', '2024-09-26 15:45:38', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (11, 'Lauatennis', NULL, NULL, '2021-07-19 01:35:52', '2021-08-04 14:51:14', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (12, 'Male', NULL, NULL, '2021-07-19 01:36:13', '2021-08-04 14:50:20', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (13, 'Minigolf', NULL, NULL, '2021-07-19 01:36:30', '2025-09-02 15:17:23', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (14, 'Murdmaajooks', NULL, NULL, '2021-07-19 03:44:02', '2024-09-26 15:45:33', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (15, 'Noolevise', NULL, NULL, '2021-07-19 03:47:13', '2021-08-04 14:49:59', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (16, 'Orienteerumine', NULL, NULL, '2021-07-19 03:47:40', '2024-09-26 15:45:54', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (17, 'Pesapall', NULL, NULL, '2021-07-19 03:47:58', '2021-08-04 14:49:52', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (18, 'Petank', NULL, NULL, '2021-07-19 03:48:17', '2021-08-04 14:49:43', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (19, 'Rannavolle', NULL, NULL, '2021-07-19 03:48:35', '2024-09-26 15:45:26', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (20, 'Rulluisutamine', NULL, NULL, '2021-07-19 03:48:54', '2024-09-26 15:45:09', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (21, 'Saalihoki', NULL, NULL, '2021-07-19 03:49:10', '2024-09-26 15:45:20', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (22, 'Saalijalgpall', NULL, NULL, '2021-07-19 03:49:29', '2021-08-04 14:49:36', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (23, 'Sisekergejõustik', NULL, NULL, '2021-07-19 03:49:46', '2025-01-16 15:46:22', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (24, 'Sulgpall', NULL, NULL, '2021-07-19 03:50:02', '2021-08-04 14:49:26', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (25, 'Suusatamine', NULL, NULL, '2021-07-19 03:50:20', '2024-09-26 15:45:02', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (26, 'Tennis', NULL, NULL, '2021-07-19 03:50:35', '2021-08-04 14:49:19', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (27, 'Triatlon', NULL, NULL, '2021-07-19 03:50:50', '2025-01-16 15:43:03', 1, 1, NULL);
INSERT INTO `sports_areas` VALUES (28, 'Uisutamine', NULL, NULL, '2021-07-19 03:51:05', '2021-08-04 14:48:55', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (29, 'Ujumine', NULL, NULL, '2021-07-19 03:51:37', '2025-01-18 12:15:01', 1, 2, NULL);
INSERT INTO `sports_areas` VALUES (30, 'Viievõistlus', NULL, NULL, '2021-07-19 03:51:55', '2024-09-27 22:42:20', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (31, 'Võrkpall', NULL, NULL, '2021-07-19 03:52:10', '2025-09-04 21:12:47', 2, 1, NULL);
INSERT INTO `sports_areas` VALUES (33, 'Muu', NULL, NULL, '2024-09-26 16:01:23', '2025-01-16 13:03:23', 2, 1, NULL);
COMMIT;

-- ----------------------------
-- Table structure for sports_areas_competition_areas
-- ----------------------------
DROP TABLE IF EXISTS `sports_areas_competition_areas`;
CREATE TABLE `sports_areas_competition_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sports_competition_areas_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `is_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sports_areas_id_idx` (`sports_areas_id`) USING BTREE,
  KEY `sports_competition_areas_id_idx` (`sports_competition_areas_id`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `sports_areas_competition_areas_ibfk_1` FOREIGN KEY (`sports_areas_id`) REFERENCES `sports_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_areas_competition_areas_ibfk_2` FOREIGN KEY (`sports_competition_areas_id`) REFERENCES `sports_competition_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_areas_competition_areas_ibfk_3` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of sports_areas_competition_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_areas_competition_areas` VALUES (1, 8, 2, '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (2, 8, 3, '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (3, 8, 5, '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (4, 8, 4, '2025-01-16 00:00:00', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (18, 29, 12, '2025-01-19 18:45:39', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (26, 8, 17, '2025-02-27 19:14:50', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (27, 23, 23, '2025-02-27 19:20:36', NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_calendar
-- ----------------------------
DROP TABLE IF EXISTS `sports_calendar`;
CREATE TABLE `sports_calendar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `year` year DEFAULT NULL,
  `events_changes_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_title_id` int unsigned DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizing_institution_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_place` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `beginning_event` date DEFAULT NULL,
  `end_event` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `information` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `schedule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sports_content_types_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_target_type_id` int unsigned DEFAULT NULL,
  `facebook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_target_type_id` int unsigned DEFAULT NULL,
  `instagram_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_target_type_id` int unsigned DEFAULT NULL,
  `organizers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `website_target_type_id_idx` (`website_target_type_id`) USING BTREE,
  KEY `facebook_target_type_id_idx` (`facebook_target_type_id`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  KEY `menu_content_group_title_id_idx` (`menu_content_group_title_id`) USING BTREE,
  KEY `events_changes_id_idx` (`events_changes_id`) USING BTREE,
  KEY `sports_areas_id_idx` (`sports_areas_id`) USING BTREE,
  KEY `instagram_target_type_id_idx` (`instagram_target_type_id`) USING BTREE,
  KEY `organizing_institution_id_idx` (`organizing_institution_id`) USING BTREE,
  KEY `year` (`year`),
  CONSTRAINT `sports_calendar_fk_1` FOREIGN KEY (`website_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_10` FOREIGN KEY (`organizing_institution_id`) REFERENCES `organizing_institution` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_2` FOREIGN KEY (`instagram_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_3` FOREIGN KEY (`sports_areas_id`) REFERENCES `sports_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_4` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_5` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_6` FOREIGN KEY (`facebook_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_7` FOREIGN KEY (`menu_content_group_title_id`) REFERENCES `sports_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_8` FOREIGN KEY (`events_changes_id`) REFERENCES `sports_changes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_9` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_calendar
-- ----------------------------
BEGIN;
INSERT INTO `sports_calendar` VALUES (10, 2024, NULL, 337, 1, 13, 2712, NULL, '', '', 2, 'Blaaa', '/spordikalender/2024/blaaa', 'Tallinnas', '2024-09-29', NULL, '10:30:00', NULL, '', '', '', 'https://www.facebook.com/eestikurtidespordiliit', 1, 'https://www.facebook.com/eestikurtidespordiliit', 1, NULL, NULL, 'Anneli Ojastu', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 22:25:29', '2025-09-22 01:49:49', 1);
INSERT INTO `sports_calendar` VALUES (11, 2024, NULL, 337, 1, 3, 2898, NULL, NULL, NULL, NULL, 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/spordikalender/2024/eesti-ja-lati-kurtide-jalgpalli-sopruskohtumine', 'Jõgeva', '2024-10-10', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 23:57:13', '2025-09-21 22:09:41', 1);
INSERT INTO `sports_calendar` VALUES (12, 2024, NULL, 338, 2, 8, 2641, NULL, NULL, NULL, NULL, 'Maahoki', '/spordikalender/spordisundmuste-kalender/2024/maahoki', 'Tallinna staadion, Kalevi tn...', '2024-09-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Annely Ojastu', '+372 1234 5678', 'edso@edso.ee', 4, 'Brett Carlisle', '2024-09-28 00:04:50', '2025-09-21 17:14:09', 1);
INSERT INTO `sports_calendar` VALUES (13, 2024, 2, 337, 1, 8, 2680, NULL, '', '', NULL, 'EKSL sisekergejõustiku võistlused', '/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 'Lasname Spordihallis, Punane 8, Tallinn', '2024-10-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, 'Ilvi Vare', '+372 1234 5678', 'eksl@eksl.ee', 4, 'Brett Carlisle', '2024-09-28 00:12:20', '2025-09-21 15:32:33', 1);
INSERT INTO `sports_calendar` VALUES (22, 2025, NULL, 338, 2, 1, NULL, NULL, '', '', NULL, 'Bobisõit 2025', '/spordikalender/spordisundmuste-kalender/2025/bobisoit-2025', 'Liepaja, Riga', '2025-09-04', NULL, NULL, NULL, '', '', NULL, '', NULL, '', NULL, NULL, NULL, 'Madis Müller', '4567890p', 'madis@eksl.ee', 4, 'Brett Carlisle', '2025-01-07 21:24:08', '2025-09-22 01:49:16', 1);
INSERT INTO `sports_calendar` VALUES (24, 2024, NULL, 338, 2, 2, 3663, NULL, NULL, NULL, NULL, 'Suvaline võistlus', '/spordikalender/spordisundmuste-kalender/2024/suvaline-voistlus', NULL, '2025-09-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'Brett Carlisle', '2025-09-15 19:23:21', '2025-09-21 17:14:40', 2);
INSERT INTO `sports_calendar` VALUES (26, 2025, 1, 337, 1, 13, NULL, NULL, '', '', NULL, 'Rahvusvaheline kurtide 3x3 korvpalli turniir Tallinn Cup 2025', 'Spordikalender/2025/rahvusvaheline-kurtide-3x3-korvpalli-turniir-tallinn-cup-2025', 'Tallinn', '2025-09-28', NULL, NULL, NULL, '<p><strong>Tallinn v&otilde;&otilde;rustas rahvusvahelist 3x3 korvpalliv&otilde;istlust</strong>&nbsp;</p>\n\n<p>9.&ndash;10. augustil toimus Tallinnas Vabaduse v&auml;ljakul rahvusvaheline 3x3 korvpalliv&otilde;istlus, mis oli EKSL korvpallisektsiooni esimene samm rahvusvahelise klubide 3x3 formaadis v&otilde;istluse korraldamisel.</p>\n\n<p>V&otilde;istlusel osales kokku 9 meeskonda ja 3 veteranide meeskonda.<br />\nP&otilde;hiturniiri ja finaalide tulemusel saavutas:</p>\n\n<ul>\n	<li>\n	<p><strong>I koha</strong>&nbsp;&ndash; Poola klubi&nbsp;<strong>WKSN SWIT Wrocław</strong>&nbsp;(k&otilde;ik v&otilde;idud)</p>\n	</li>\n	<li>\n	<p><strong>II koha</strong>&nbsp;&ndash; Rootsi klubi&nbsp;<strong>IK Hephata</strong></p>\n	</li>\n	<li>\n	<p><strong>III koha</strong>&nbsp;&ndash; L&auml;ti klubi&nbsp;<strong>SK &quot;NB&quot; 2</strong></p>\n	</li>\n</ul>\n\n<p>Eestit esindasid&nbsp;<strong>Tartu KSS KAAR</strong>,&nbsp;<strong>V&otilde;rumaa K&Uuml;</strong>&nbsp;ja&nbsp;<strong>P&auml;rnu KSS Eero</strong>.</p>\n\n<p><strong>Veteranide turniiril</strong>&nbsp;esindas Eestit&nbsp;<strong>Tallinna KS Talkur</strong>.<br />\nTulemused:</p>\n\n<ul>\n	<li>\n	<p><strong>I koht</strong>&nbsp;&ndash; Rootsi klubi&nbsp;<strong>IK Hephata</strong></p>\n	</li>\n	<li>\n	<p><strong>II koht</strong>&nbsp;&ndash;&nbsp;<strong>Tallinna KS Talkur</strong>&nbsp;(Eesti)</p>\n	</li>\n	<li>\n	<p><strong>III koht</strong>&nbsp;&ndash; Soome klubi&nbsp;<strong>VALPAS DEAFWOLVES</strong></p>\n	</li>\n</ul>\n\n<p>Parima m&auml;ngija tiitel l&auml;ks v&auml;ga kindlalt Poola klubi m&auml;ngijale Pawel Wiśniewskile, kelle valisid v&auml;ljakukohtunikud ja korvpallisektsioon.</p>\n\n<p>Ilus ja p&auml;ikesepaisteline kahep&auml;evane &uuml;ritus kulges ladusalt algusest l&otilde;puni.<br />\nSuur t&auml;nu EKSL-ile ja vabatahtlikele, kes abistasid EKSL korvpallisektsiooni, samuti Tallinna Linnavalitsusele ning eriti Roberts Peetsile, t&auml;nu kellele sai &uuml;ritus toimuda Vabaduse v&auml;ljaku 3x3 korvpalliplatsil.</p>\n\n<p>Pealtvaatajaid oli rohkelt ning atmosf&auml;&auml;r oli elav ja sportlik.</p>\n\n<p>Eesti Kurtide Spordiliidu president Ilvi Vare t&auml;nus&otilde;nad:&nbsp; &acute;&acute;Suur t&auml;nu Jaan P&auml;rgmale ja kogu korraldusmeeskonnale suurep&auml;rase Tallinn Cup 2025 turniiri eest. Teie p&uuml;hendumus ja t&ouml;&ouml; tegid selle s&uuml;ndmuse v&otilde;imalikuks ning aitasid tugevdada meie kogukonda ja muuta kurtide sport n&auml;htavaks. Olen v&auml;ga uhke meie kogukonna &uuml;le ja t&auml;nulik k&otilde;igile, kes panustasid sellesse kaunisse &uuml;ritusse&acute;&acute;.</p>\n\n<p>Tulemused</p>\n', '', NULL, '', NULL, '', NULL, NULL, NULL, 'Sergei', '3456789', 'dfghjk@fghjk.ee', 4, 'Brett Carlisle', '2025-09-21 16:46:02', '2025-09-21 17:11:48', 1);
INSERT INTO `sports_calendar` VALUES (27, 2025, NULL, 337, 1, 8, NULL, NULL, '', '', NULL, 'Eesti meistrivõistlused para- ja kurtide kergejõustikus 2025 ', 'Spordikalender/2025/eesti-meistrivoistlused-para-ja-kurtide-kergejoustikus-2025', 'Tallinn', '2025-09-26', NULL, NULL, NULL, '<p>Eesti meistriv&otilde;istlused para- ja kurtide kergej&otilde;ustikus&nbsp; toimunud Rakveres 19.juulil, kus osalesid meie 4 sportlast: Rinat Raisp, Simon Teiss, Marko Vingisaar ja Cathy Saem.</p>\n\n<p>Meeste 100 meetri jooksus p&uuml;stitasid v&auml;geva isikliku rekordi Simon Teiss 11,10 (rekordi parandust 10 sajandikku!) ja Rinat Raisp 11,69 (rekordi parandust 13 sajandikku!).<br />\n400 meetri jooksus sai Rinat ajaks 52,54, mis j&auml;&auml;b tema eelmise suve isiklikule rekordile alla 13 sajandikku.<br />\nCathy Saem t&otilde;ukas kuuli 9.27, &nbsp;viskas esimesel katsel oda 27.79 ja jooksis 100 meetrit ajaga 14,3 sek.</p>\n\n<p>&nbsp;</p>\n\n<p>Treeningplaanide j&auml;rgi ja treeneriga j&auml;tkavad praegu treenimist need sportlased, kes valmistuvad Eesti meistriv&otilde;istlusteks 2-3.augustil, erinevateks seeriajooksu&nbsp;etappideks&nbsp;ja Kurtide Ol&uuml;mpiam&auml;ngudeks 15-26.novembrini Tokyos.</p>\n\n<p>&nbsp;</p>\n\n<p>Tulemused</p>\n\n<p><em>Viimati muudetud: 22.07.2025 </em></p>\n', '', NULL, '', NULL, '', NULL, NULL, NULL, 'dfghjk,.', '3456789', 'dfghnm@fghj.com', 4, 'Brett Carlisle', '2025-09-21 16:48:41', '2025-09-21 16:50:06', 1);
INSERT INTO `sports_calendar` VALUES (28, 2025, NULL, 337, 1, 1, NULL, NULL, '', '', NULL, 'Talkur Open Bowling 2025', 'Spordikalender/2025/talkur-open-bowling-2025', 'Tallinn', '2025-09-10', NULL, NULL, NULL, '<p>10.-11.mail toimus 23-ndat korda rahvusvaheline bowlinguturniir Mustam&auml;e Elamuste keskuses. Parima naism&auml;ngijana v&otilde;itis Heli P&uuml;ss (Talkur) 1081p, teise koha saavutas Sirie Luik (Kaar) 1040p ja kolmanda koha saavutas Rasma Maurina (Riga) 984p.</p>\n\n<p>Meesm&auml;ngijatest saavutas esikoha Mihkel P&uuml;ss (Talkur) 1294p, teist korda meister.</p>\n\n<p>Teise koha saavutas Maris Dukurs (Riga) 1292p. Kes, kaotas napilt 2 punktiga. Kolmanda koha saavutas Girts Gabrans (Riga) 1261p.</p>\n\n<p>Naispaarism&auml;ng</p>\n\n<p>1.koht Sirie Luik (Kaar) 859p ja Ljuda Mikson 877p (Kaar), kokku 1736p.</p>\n\n<p>2. koht Riina Kuusk 742p (Talkur) ja Heli P&uuml;ss 983p (Talkur), kokku 1725p.</p>\n\n<p>3.koht Terje Liim (Talkur) 760p ja Kerly Ohlo (Talkur) 881p, kokku 1641p.</p>\n\n<p>Meespaarism&auml;ng</p>\n\n<p>1.koht leedulased Kestutis Gumbrys 1137 p (Vilnius) ja Ruslanas Minkevicius 1290p (Vilnius), kokku 2427 p.</p>\n\n<p>2.koht Girts Gabrans (Riga) 1215p ja Maris Dukurs (Riga) 1179p, kokku 2394p.</p>\n\n<p>3.koht Priit P&otilde;ldsamm (Kaar) 1229p ja Teet Ojamets 1136p (Kaar), kokku 2365p.</p>\n\n<p>Valiti parimad isiklikud rekordid - Liga &Otilde;un 207p (indiv.) ja Girts Gabrans 257p (Riga).</p>\n\n<p>Kohtumiseni j&auml;rgmisel aastal 2026.a Tallinnas!</p>\n', '', NULL, '', NULL, '', NULL, NULL, NULL, 'fghjkl', '345678io', 'dfghj@dfghj.ee', 4, 'Brett Carlisle', '2025-09-22 01:32:15', '2025-09-22 01:33:32', 2);
COMMIT;

-- ----------------------------
-- Table structure for sports_calendar_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `sports_calendar_editors_assn`;
CREATE TABLE `sports_calendar_editors_assn` (
  `sports_calendar_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`sports_calendar_id`,`user_id`),
  KEY `sports_calendar_id_idx` (`sports_calendar_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `sports_calendar_users_assn_1` FOREIGN KEY (`sports_calendar_id`) REFERENCES `sports_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_calendar_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `sports_calendar_editors_assn` VALUES (10, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (11, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (12, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (13, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (22, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (24, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (26, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (27, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (28, 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_changes
-- ----------------------------
DROP TABLE IF EXISTS `sports_changes`;
CREATE TABLE `sports_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `sports_change_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `sports_change_locked_idx` (`sports_change_locked`) USING BTREE,
  CONSTRAINT `sports_changes_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_changes
-- ----------------------------
BEGIN;
INSERT INTO `sports_changes` VALUES (1, 'Uuendatud', '2024-09-22 16:40:09', '2025-01-15 08:52:58', 1, 1);
INSERT INTO `sports_changes` VALUES (2, 'Täiendatud', '2024-09-22 16:40:30', '2025-08-13 00:11:44', 1, 1);
INSERT INTO `sports_changes` VALUES (3, 'Edasi lükatud', '2024-09-22 16:40:53', '2025-01-15 08:53:06', 1, 0);
INSERT INTO `sports_changes` VALUES (4, 'Tühistatud', '2024-09-29 01:19:25', '2025-09-01 21:31:37', 2, 0);
COMMIT;

-- ----------------------------
-- Table structure for sports_competition_areas
-- ----------------------------
DROP TABLE IF EXISTS `sports_competition_areas`;
CREATE TABLE `sports_competition_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `is_locked` int unsigned DEFAULT '1',
  `unit_id` int unsigned DEFAULT NULL,
  `is_detailed_result` int unsigned DEFAULT '2',
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`is_enabled`) USING BTREE,
  KEY `is_locked_id` (`is_locked`) USING BTREE,
  KEY `unit_id_idx` (`unit_id`) USING BTREE,
  KEY `is_detailed_result_idx` (`is_detailed_result`) USING BTREE,
  CONSTRAINT `sports_competition_areas_ibfk_1` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_competition_areas_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `sports_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_competition_areas_ibfk_3` FOREIGN KEY (`is_detailed_result`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_competition_areas_ibfk_4` FOREIGN KEY (`is_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_competition_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_competition_areas` VALUES (2, '100 m', '2025-01-15 00:00:00', '2025-09-04 10:42:04', 1, 2, 1, 2, '100 m võitja: Sirle Papp');
INSERT INTO `sports_competition_areas` VALUES (3, '200 m', '2025-01-15 00:00:00', '2025-02-26 01:41:22', 1, 2, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (4, '400 m', '2025-01-15 17:15:20', '2025-01-16 11:58:04', 1, 2, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (5, '800 m', '2025-01-15 00:00:00', '2025-02-26 14:54:55', 1, 2, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (6, '1500 m', '2025-01-16 15:47:04', '2025-09-04 13:20:23', 2, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (8, '3000 tj', '2025-01-16 15:48:13', '2025-02-25 21:46:49', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (9, 'Kaugushüpe', '2025-01-17 21:27:26', '2025-02-26 15:13:03', 1, 1, 2, 2, 'Kaugushüppe võitja: Kairit Olenko');
INSERT INTO `sports_competition_areas` VALUES (11, '100 m vabalt', '2025-01-18 12:13:33', '2025-02-26 18:20:42', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (12, '50 m rinnuli', '2025-01-18 12:14:01', '2025-02-25 23:31:53', 1, 2, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (13, '100 m rinnuli', '2025-01-18 12:14:28', '2025-09-04 12:57:19', 2, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (17, '10-võistlus', '2025-02-25 21:49:04', '2025-09-04 11:43:49', 1, 2, 3, 1, NULL);
INSERT INTO `sports_competition_areas` VALUES (18, '7-võistlus', '2025-02-25 21:50:18', NULL, 1, 1, 3, 1, NULL);
INSERT INTO `sports_competition_areas` VALUES (19, '50 m vabalt', '2025-02-26 00:35:12', '2025-09-04 12:38:08', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (20, 'Üksik', '2025-02-26 15:07:08', '2025-02-26 18:20:16', 1, 1, 3, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (21, 'Paar', '2025-02-26 15:09:30', NULL, 1, 1, 3, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (22, 'Kõrgushüpe', '2025-02-26 15:11:54', '2025-02-26 15:13:22', 1, 1, 2, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (23, '100 m', '2025-02-27 19:16:36', NULL, 1, 2, 1, 2, NULL);
COMMIT;

-- ----------------------------
-- Table structure for sports_content_types
-- ----------------------------
DROP TABLE IF EXISTS `sports_content_types`;
CREATE TABLE `sports_content_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `type_locked` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE,
  KEY `status_idx` (`status`),
  KEY `type_locked_idx` (`type_locked`) USING BTREE,
  CONSTRAINT `sports_content_types_status_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_content_types_status_ibfk_2` FOREIGN KEY (`type_locked`) REFERENCES `locking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_content_types
-- ----------------------------
BEGIN;
INSERT INTO `sports_content_types` VALUES (1, 'Juhendid', '2024-10-02 12:00:00', '2025-08-31 16:32:19', 1, 2);
INSERT INTO `sports_content_types` VALUES (2, 'Tulemused', '2024-10-02 12:00:10', '2024-10-03 10:34:09', 1, 2);
INSERT INTO `sports_content_types` VALUES (3, 'Ajakavad', '2024-10-02 12:00:30', '2024-10-06 00:51:05', 1, 2);
COMMIT;

-- ----------------------------
-- Table structure for sports_settings
-- ----------------------------
DROP TABLE IF EXISTS `sports_settings`;
CREATE TABLE `sports_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `events_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `events_locked_idx` (`events_locked`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `sports_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_settings
-- ----------------------------
BEGIN;
INSERT INTO `sports_settings` VALUES (1, 'Spordikalender', '', '/spordikalender', 1, 1, 337, '2024-09-25 21:20:41', '2025-09-22 10:40:32', 1);
INSERT INTO `sports_settings` VALUES (2, 'Spordisündmuste kalender', 'Spordivõistluste ajakava', '/spordikalender/spordisundmuste-kalender', 1, 1, 338, '2024-09-27 11:43:56', '2025-09-22 10:40:32', 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_tables
-- ----------------------------
DROP TABLE IF EXISTS `sports_tables`;
CREATE TABLE `sports_tables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sports_calendar_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_date` datetime DEFAULT NULL,
  `sports_content_types_id` int unsigned DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `frontend_visible` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sports_content_type_id_idx` (`sports_content_types_id`) USING BTREE,
  KEY `files_id_idx` (`files_id`) USING BTREE,
  KEY `sport_areas_id_idx` (`sports_areas_id`) USING BTREE,
  KEY `sports_calendar_group_id_idx` (`sports_calendar_group_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `sports_tables_ibfk_1` FOREIGN KEY (`sports_content_types_id`) REFERENCES `sports_content_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_2` FOREIGN KEY (`sports_areas_id`) REFERENCES `sports_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_3` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_5` FOREIGN KEY (`sports_calendar_group_id`) REFERENCES `sports_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_tables
-- ----------------------------
BEGIN;
INSERT INTO `sports_tables` VALUES (1, 13, 337, 2024, 'EKSL sisekergejõustiku võistluste juhend 2024', '2024-10-10 00:00:00', 1, 8, 2755, '2024-10-05 18:20:29', '2024-12-13 15:56:18', 1, 1);
INSERT INTO `sports_tables` VALUES (2, 13, 337, 2024, 'EKSL sisekergejõustiku tulemused 2024', '2024-10-10 00:00:00', 2, 8, 2764, '2024-10-05 21:18:18', '2024-12-13 15:56:29', 1, 1);
INSERT INTO `sports_tables` VALUES (5, 13, 337, 2024, 'EKSL siekergejõustiku ajakava 2023', '2024-10-10 00:00:00', 3, 8, 2758, '2024-10-06 14:20:58', '2024-12-13 15:56:38', 1, 1);
INSERT INTO `sports_tables` VALUES (6, 11, 337, 2024, 'Jalgpalli juhend 2021', '2025-01-15 00:00:00', 1, 3, 2757, '2024-10-08 01:04:24', '2025-01-15 09:32:05', 1, 0);
INSERT INTO `sports_tables` VALUES (7, 10, 337, 2024, 'EKSL kergejõustiku MV juhend 2012', '2025-09-02 00:00:00', 1, 8, 2761, '2024-10-09 20:33:50', '2025-09-02 15:59:12', 2, 1);
INSERT INTO `sports_tables` VALUES (9, 10, 337, 2024, 'EKSL kergejõustiku MV juhend 2013', '2025-01-15 00:00:00', 1, 1, 2754, '2024-12-03 15:54:05', '2025-01-15 09:31:38', 1, 1);
INSERT INTO `sports_tables` VALUES (17, 12, 338, 2024, 'Suvaline tulemuste tabel', '2024-12-18 00:00:00', 2, 8, 2757, '2024-12-13 14:49:03', '2024-12-13 15:46:14', 1, 1);
INSERT INTO `sports_tables` VALUES (18, 12, 338, 2024, 'Suvaline juhend 18.12.2024', '2024-12-18 00:00:00', 1, 8, 2764, '2024-12-13 15:53:48', NULL, 1, 1);
INSERT INTO `sports_tables` VALUES (20, 12, 338, 2024, 'Eesti viipekeele ajakava', '2025-01-15 00:00:00', 3, 8, 2833, '2025-01-15 09:09:47', '2025-01-15 09:32:23', 2, 1);
INSERT INTO `sports_tables` VALUES (22, 10, 337, 2024, '2012_EKSL_MV_protkergej 260512', '2025-08-21 00:00:00', 2, 1, 2764, '2025-08-31 13:13:05', '2025-08-31 13:14:03', 1, 1);
INSERT INTO `sports_tables` VALUES (26, 22, 338, 2025, 'Eesti viipekeele staatus ja kasutamine', NULL, 3, 1, 2833, '2025-09-20 22:07:54', '2025-09-20 22:08:38', 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for sports_units
-- ----------------------------
DROP TABLE IF EXISTS `sports_units`;
CREATE TABLE `sports_units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of sports_units
-- ----------------------------
BEGIN;
INSERT INTO `sports_units` VALUES (1, 'Seconds', 'seconds');
INSERT INTO `sports_units` VALUES (2, 'Meters', 'meters');
INSERT INTO `sports_units` VALUES (3, 'Points', 'points');
COMMIT;

-- ----------------------------
-- Table structure for statistics
-- ----------------------------
DROP TABLE IF EXISTS `statistics`;
CREATE TABLE `statistics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `settings_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `settings_id_idx` (`settings_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  KEY `activity_status_idx` (`status`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `assigned_by_user_ibfk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_category_id_ibfk` FOREIGN KEY (`category_id`) REFERENCES `statistics_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `statistics_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of statistics
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for statistics_category
-- ----------------------------
DROP TABLE IF EXISTS `statistics_category`;
CREATE TABLE `statistics_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `statistics_category_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of statistics_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for statistics_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `statistics_editors_assn`;
CREATE TABLE `statistics_editors_assn` (
  `statistics_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`statistics_id`,`user_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  KEY `statistics_id_idx` (`statistics_id`) USING BTREE,
  CONSTRAINT `statistics_editors_assn_ibfk_1` FOREIGN KEY (`statistics_id`) REFERENCES `statistics_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_editors_assn_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of statistics_editors_assn
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for statistics_settings
-- ----------------------------
DROP TABLE IF EXISTS `statistics_settings`;
CREATE TABLE `statistics_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url_destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statistics_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `statistics_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `statistics_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of statistics_settings
-- ----------------------------
BEGIN;
INSERT INTO `statistics_settings` VALUES (14, 'records.php', 'Rekordid', '', 1, 1, 452, '/statistika/saavutused/rekordid', '2024-12-20 19:48:56', '2025-08-13 00:10:56', 1, 'John Doe', 1);
INSERT INTO `statistics_settings` VALUES (15, 'rankings_list.php', 'Edetabelid', NULL, 1, 1, 453, '/statistika/saavutused/edetabelid', '2024-12-20 19:51:25', '2025-01-21 23:44:22', 1, 'John Doe', 0);
INSERT INTO `statistics_settings` VALUES (16, 'achievements_list.php', 'Saavutused', '', 1, 1, 633, '/statistika/saavutused', '2025-03-19 12:55:30', '2025-08-11 18:00:00', 1, 'John Doe', 0);
COMMIT;

-- ----------------------------
-- Table structure for statistics_types
-- ----------------------------
DROP TABLE IF EXISTS `statistics_types`;
CREATE TABLE `statistics_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `type_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `statistics_types_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of statistics_types
-- ----------------------------
BEGIN;
INSERT INTO `statistics_types` VALUES (1, 'Rekordid', 'records_list.php', NULL, NULL, 1, 1);
INSERT INTO `statistics_types` VALUES (2, 'Edetabelid', 'rankings_list.php', NULL, NULL, 1, 1);
INSERT INTO `statistics_types` VALUES (3, 'Saavutused', 'achievements_list.php', NULL, NULL, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for status
-- ----------------------------
DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of status
-- ----------------------------
BEGIN;
INSERT INTO `status` VALUES (1, 1, 'Published', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #449d44; line-height: .1;\"></i>  Published', 1);
INSERT INTO `status` VALUES (2, 2, 'Hidden', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ff0000; line-height: .1;\"></i> Hidden', 1);
INSERT INTO `status` VALUES (3, 3, 'Draft', '<i class=\"fa fa-circle-o fa-lg\" aria-hidden=\"true\" style=\"color: #000000; line-height: .1;\"></i> Draft', 1);
INSERT INTO `status` VALUES (4, 4, 'Waiting...', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ffb00c; line-height: .1;\"></i> Waiting...', 1);
INSERT INTO `status` VALUES (5, 5, 'Currently...', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #3498db; line-height: .1;\"></i>  Currently...', 1);
INSERT INTO `status` VALUES (6, 6, 'Closed...', '<i class=\"fa fa-circle-o fa-lg\" aria-hidden=\"true\" style=\"color: #ff0000; line-height: .1;\"></i>  Closed...', 1);
COMMIT;

-- ----------------------------
-- Table structure for target_group_of_calendar
-- ----------------------------
DROP TABLE IF EXISTS `target_group_of_calendar`;
CREATE TABLE `target_group_of_calendar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `target_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  KEY `target_locked_idx` (`target_locked`) USING BTREE,
  CONSTRAINT `is_enabled_id_fk` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of target_group_of_calendar
-- ----------------------------
BEGIN;
INSERT INTO `target_group_of_calendar` VALUES (2, 'Kalenderplaan', 1, '2021-06-09 00:47:50', '2025-09-22 17:42:34', 0);
INSERT INTO `target_group_of_calendar` VALUES (3, 'Pensionäride kalenderplaan', 2, '2021-07-02 20:02:13', '2025-09-22 18:08:44', 0);
INSERT INTO `target_group_of_calendar` VALUES (4, 'Sportlaste kalenderplaan', 1, '2021-07-04 23:09:26', '2025-09-22 18:23:26', 0);
INSERT INTO `target_group_of_calendar` VALUES (9, 'Taidlejate kalenderplaan', 2, '2021-07-20 23:07:10', '2025-09-14 21:40:11', 0);
COMMIT;

-- ----------------------------
-- Table structure for target_type
-- ----------------------------
DROP TABLE IF EXISTS `target_type`;
CREATE TABLE `target_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of target_type
-- ----------------------------
BEGIN;
INSERT INTO `target_type` VALUES (1, 'New Window (_blank)', '_blank');
INSERT INTO `target_type` VALUES (2, 'Topmost Window (_top)', '_top');
INSERT INTO `target_type` VALUES (3, 'Same Window (_self)', '_self');
INSERT INTO `target_type` VALUES (4, 'Parent Window (_parent)', '_parent');
COMMIT;

-- ----------------------------
-- Table structure for team_member_project_assn
-- ----------------------------
DROP TABLE IF EXISTS `team_member_project_assn`;
CREATE TABLE `team_member_project_assn` (
  `person_id` int unsigned NOT NULL,
  `project_id` int unsigned NOT NULL,
  PRIMARY KEY (`person_id`,`project_id`),
  KEY `IDX_teammemberprojectassn_2` (`project_id`),
  CONSTRAINT `person_team_member_project_assn` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `project_team_member_project_assn` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of team_member_project_assn
-- ----------------------------
BEGIN;
INSERT INTO `team_member_project_assn` VALUES (2, 1);
INSERT INTO `team_member_project_assn` VALUES (5, 1);
INSERT INTO `team_member_project_assn` VALUES (6, 1);
INSERT INTO `team_member_project_assn` VALUES (7, 1);
INSERT INTO `team_member_project_assn` VALUES (8, 1);
INSERT INTO `team_member_project_assn` VALUES (2, 2);
INSERT INTO `team_member_project_assn` VALUES (4, 2);
INSERT INTO `team_member_project_assn` VALUES (5, 2);
INSERT INTO `team_member_project_assn` VALUES (7, 2);
INSERT INTO `team_member_project_assn` VALUES (9, 2);
INSERT INTO `team_member_project_assn` VALUES (10, 2);
INSERT INTO `team_member_project_assn` VALUES (1, 3);
INSERT INTO `team_member_project_assn` VALUES (4, 3);
INSERT INTO `team_member_project_assn` VALUES (6, 3);
INSERT INTO `team_member_project_assn` VALUES (8, 3);
INSERT INTO `team_member_project_assn` VALUES (10, 3);
INSERT INTO `team_member_project_assn` VALUES (1, 4);
INSERT INTO `team_member_project_assn` VALUES (2, 4);
INSERT INTO `team_member_project_assn` VALUES (3, 4);
INSERT INTO `team_member_project_assn` VALUES (5, 4);
INSERT INTO `team_member_project_assn` VALUES (8, 4);
INSERT INTO `team_member_project_assn` VALUES (11, 4);
INSERT INTO `team_member_project_assn` VALUES (12, 4);
COMMIT;

-- ----------------------------
-- Table structure for teams
-- ----------------------------
DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int unsigned DEFAULT NULL,
  `group_id` int unsigned DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `areas_responsibility` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interests` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `status_teams_ibfk` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of teams
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` int unsigned DEFAULT NULL,
  `preferred_language` int unsigned DEFAULT NULL,
  `items_per_page_by_assigned_user` int unsigned DEFAULT NULL,
  `preferred_date_time_format` int unsigned DEFAULT NULL,
  `preferred_portlets_sort` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_activity` int unsigned DEFAULT '2',
  `preferences_set` tinyint unsigned DEFAULT '0',
  `last_active` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `crated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`) USING BTREE,
  KEY `first_name_idx` (`first_name`) USING BTREE,
  KEY `last_name_idx` (`last_name`) USING BTREE,
  KEY `items_per_page_by_assigned_user_idx` (`items_per_page_by_assigned_user`) USING BTREE,
  KEY `preferred_date_time_format_idx` (`preferred_date_time_format`) USING BTREE,
  KEY `preferred_language_id` (`preferred_language`) USING BTREE,
  KEY `user_activity_idx` (`user_activity`) USING BTREE,
  KEY `role_idx` (`role`) USING BTREE,
  CONSTRAINT `items_per_page_by_assigned_user_fk` FOREIGN KEY (`items_per_page_by_assigned_user`) REFERENCES `items_per_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preferred_date_time_format_fk` FOREIGN KEY (`preferred_date_time_format`) REFERENCES `date_and_time_formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preferred_language_fk` FOREIGN KEY (`preferred_language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_fk` FOREIGN KEY (`role`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_activity_fk` FOREIGN KEY (`user_activity`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'John', 'Doe', 'doe@gmail.com', 'johndoe', NULL, NULL, 1, 3, 1, NULL, 1, 0, NULL, NULL);
INSERT INTO `user` VALUES (2, 'Alex', 'Smith', 'smith@gmail.com', 'alexsmith', NULL, NULL, 2, 2, 7, NULL, 1, 0, NULL, NULL);
INSERT INTO `user` VALUES (3, 'Samantha', 'Jones', 'samanthajones@gmail.com', 'samantha', NULL, NULL, NULL, 3, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `user` VALUES (4, 'Brett', 'Carlisle', 'carlisle@gmail.com', 'carlisle', NULL, NULL, NULL, 4, NULL, NULL, 1, 0, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for user_access
-- ----------------------------
DROP TABLE IF EXISTS `user_access`;
CREATE TABLE `user_access` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `content_entity_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`) USING BTREE,
  KEY `content_entity_id_idx` (`content_entity_id`) USING BTREE,
  CONSTRAINT `content_entity_id_ibfk` FOREIGN KEY (`content_entity_id`) REFERENCES `content_entities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_ibfk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_access
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_roles
-- ----------------------------
BEGIN;
INSERT INTO `user_roles` VALUES (1, 'Administrator', 'administrator', '2025-03-21 00:00:00');
INSERT INTO `user_roles` VALUES (2, 'Editor', 'editor', '2025-03-21 00:00:00');
INSERT INTO `user_roles` VALUES (3, 'Developer', 'developer', '2025-03-21 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for videos
-- ----------------------------
DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `settings_id` int unsigned DEFAULT NULL,
  `settings_id_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `introduction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `embed_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `settings_id_idx` (`settings_id`) USING BTREE,
  KEY `activity_status_idx` (`status`) USING BTREE,
  KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE,
  CONSTRAINT `menu_content_group_id_ibfk` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `videos_activity_status_ibfk` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `videos_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `videos_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of videos
-- ----------------------------
BEGIN;
INSERT INTO `videos` VALUES (22, 439, 1, 'Videod', 'ICSD presidendi dr. Valery Rukhledev videosõnum', 0, NULL, '<iframe src=\"https://www.youtube.com/embed/Boogq_ipmRQ?si=Qb5CPzXj-QYZgj2B\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:10:16', '2025-08-25 17:27:30', 1);
INSERT INTO `videos` VALUES (23, 439, 1, 'Videod', 'ICSD kongressi kokkuvõte', 1, NULL, '<iframe src=\"https://www.youtube.com/embed/4eGkwY_0TUw?si=by6DzTQ4u0J-4vHD\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:18:02', '2025-08-25 17:27:30', 1);
INSERT INTO `videos` VALUES (24, 439, 3, 'Uus videote nimekiri', 'Rahvusvahelise puuetega inimeste päeva tähistamine 3. detsembril 2023', 3, '', '<iframe src=\'https://videoteek.ead.ee/embed/65e19bc67f702\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Nii kui igal pool t&auml;histatakse rahvusvahelist puuetega inimeste p&auml;eva, siis meie ei j&auml;&auml; maha!</p>\n\n<p>Eesti Kurtide Liit t&auml;histab seda &uuml;heskoos teistega ning t&auml;na oli &uuml;htekuuluvuskontsert, mis oli meile imeliselt ligip&auml;&auml;setav - viipekeelsed laulud, viipekeele t&otilde;lgid k&otilde;rval, subtiitrid - &uuml;hes&otilde;naga t&auml;ielik ligip&auml;&auml;setavus oli tagatud.</p>\n\n<p>Kuigi lava peal r&auml;&auml;giti m&otilde;nedest olulistest teemadest, nagu sellest, et keegi ei tohiks maha j&auml;&auml;da v&otilde;i olla erinevalt koheldud, vaid k&otilde;ik peaksid olema v&otilde;rdsed, loodame meie, et neist s&otilde;nadest peetakse kinni!</p>\n\n<p>Suurimad t&auml;nud nendele, kes selle kontserdi suure s&uuml;damega panustasid ja korraldasid nii arvestades iga inimese tema vajadusega!</p>\n', '2023-12-03 14:54:46', '2025-08-25 17:28:29', 1);
INSERT INTO `videos` VALUES (25, 439, 1, 'Videod', 'Eesti Delegatsioon 23.Kurtide suveolümpiamängude avatseremoonial', 2, NULL, '<iframe src=\"https://www.youtube.com/embed/OT-BvoP6bfU?si=KJ__eEC_ZkbPdoTP\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:56:29', '2025-08-25 17:27:30', 1);
INSERT INTO `videos` VALUES (27, 439, 3, 'Uus videote nimekiri', 'Eesti Kurtide Liidu eesti viipekeele päeva viipekeelne videotervitus 2024', 1, NULL, '<iframe src=\'https://videoteek.ead.ee/embed/65e19154710b5\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Eesti Kurtide Liit on organisatsioon, mis esindab ja &uuml;hendab Eestis elavaid kurte. Eesti viipekeel on &auml;ram&auml;rkimist leidnud juba 2007. aastal 1. m&auml;rtsil kehtima hakanud keeleseaduses eesti riigikeelega v&otilde;rdv&auml;&auml;rses staatuses oleva iseseisva keelena.</p>\n\n<p>T&auml;histamaks v&auml;&auml;rikalt eesti viipekeele 15. aastap&auml;eva, otsustas Eesti Kurtide Liidu juhatus korraldada aktsiooni, kus meie k&auml;isime eesti viipekeelt &otilde;petamas.</p>\n\n<p>Kas tahate teada avaliku tegelaste nimeviipeid? Kas tahate teada, kust keegi on p&auml;rit?</p>\n\n<p>Meie t&auml;name k&otilde;iki osalejaid, kes vastasid positiivselt meie &uuml;leskutsele.</p>\n\n<p>Soovime k&otilde;igile head eesti viipekeele p&auml;eva!</p>\n\n<p>Eesti Kurtide Liidu juhatus</p>\n', '2024-03-01 17:14:25', '2025-08-25 17:28:29', 1);
INSERT INTO `videos` VALUES (28, 439, 3, 'Uus videote nimekiri', 'Eesti Kurtide Liidu uue juhatuse väike kokkuvõte 2023. aastast', 2, '', '<iframe src=\'https://videoteek.ead.ee/embed/658ea467d4675\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Eesti Kurtide Liidu liidukoosolekul 20. mail 2023 valitud uus juhatus sai l&uuml;hikese ajaga midagi korda saata. Uue juhatuse liikmed annavad &uuml;levaate oma l&uuml;hitegevusest.</p>\n\n<p>Eesti Kurtide Liidu juhatus soovib headele liikmetele, s&otilde;pradele ja koost&ouml;&ouml;partneritele rahulikke j&otilde;ulup&uuml;hi ning tegusat ja &otilde;nnelikku uut aastat!</p>\n', '2023-12-29 17:15:25', '2025-08-25 17:28:29', 1);
INSERT INTO `videos` VALUES (29, NULL, 3, 'Uus videote nimekiri', 'Eesti kurtide pensionäride XIV kokkutulek kruiisil', 0, '', '<iframe src=\'https://videoteek.ead.ee/embed/64be58ded4340\' frameborder=\'0\' allowfullscreen></iframe>', '', '2025-08-20 21:54:43', '2025-08-25 17:28:29', 1);
COMMIT;

-- ----------------------------
-- Table structure for videos_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `videos_editors_assn`;
CREATE TABLE `videos_editors_assn` (
  `videos_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`videos_id`,`user_id`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  KEY `videos_id_idx` (`videos_id`) USING BTREE,
  CONSTRAINT `videos_editors_assn_ibfk_1` FOREIGN KEY (`videos_id`) REFERENCES `videos_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `videos_editors_assn_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of videos_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `videos_editors_assn` VALUES (1, 3);
INSERT INTO `videos_editors_assn` VALUES (1, 4);
INSERT INTO `videos_editors_assn` VALUES (3, 4);
COMMIT;

-- ----------------------------
-- Table structure for videos_settings
-- ----------------------------
DROP TABLE IF EXISTS `videos_settings`;
CREATE TABLE `videos_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `videos_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`id`) USING BTREE,
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `videos_locked_idx` (`videos_locked`) USING BTREE,
  CONSTRAINT `videos_settings_ibfk_1` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `videos_settings_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `videos_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of videos_settings
-- ----------------------------
BEGIN;
INSERT INTO `videos_settings` VALUES (1, 'Videod', 'Videote list', 1, 1, 439, '/videod/videote-list', '2024-12-08 21:40:26', '2025-08-25 17:27:30', 1, 'John Doe', 1);
INSERT INTO `videos_settings` VALUES (3, 'Uus videote nimekiri', 'Uued videod', 1, 1, 639, '/uus-videote-nimekiri/uued-videod', '2025-08-21 11:26:54', '2025-08-25 17:28:29', 1, 'John Doe', 1);
COMMIT;

-- ----------------------------
-- Table structure for view_type
-- ----------------------------
DROP TABLE IF EXISTS `view_type`;
CREATE TABLE `view_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of view_type
-- ----------------------------
BEGIN;
INSERT INTO `view_type` VALUES (3, 'Detail type');
INSERT INTO `view_type` VALUES (1, 'Home type');
INSERT INTO `view_type` VALUES (2, 'List type');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
