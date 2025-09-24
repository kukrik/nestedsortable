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

 Date: 30/07/2025 18:18:07
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
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int unsigned DEFAULT NULL,
  `street` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_address_1` (`person_id`),
  CONSTRAINT `person_address` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of address
-- ----------------------------
BEGIN;
INSERT INTO `address` VALUES (1, 1, '1 Love Drive', 'Phoenix');
INSERT INTO `address` VALUES (2, 2, '2 Doves and a Pine Cone Dr.', 'Dallas');
INSERT INTO `address` VALUES (3, 3, '3 Gold Fish Pl.', 'New York');
INSERT INTO `address` VALUES (4, 3, '323 W QCubed', 'New York');
INSERT INTO `address` VALUES (5, 5, '22 Elm St', 'Palo Alto');
INSERT INTO `address` VALUES (6, 7, '1 Pine St', 'San Jose');
INSERT INTO `address` VALUES (7, 7, '421 Central Expw', 'Mountain View');
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of age_categories
-- ----------------------------
BEGIN;
INSERT INTO `age_categories` VALUES (1, 'Adults', 23, NULL, 'Üle 23aastased mehed/naised', 'Eesti rekordid', 1, 'John Doe', '2025-01-21 00:00:00', '2025-02-05 10:50:35', 1, 2);
INSERT INTO `age_categories` VALUES (2, 'U23', 20, 22, 'Alla 23-aastased mehed/ naised', 'Eesti U23 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', '2025-02-02 23:43:46', 1, 2);
INSERT INTO `age_categories` VALUES (3, 'U20', 18, 19, 'Alla 20-aastased noormehed/neiud', 'Eesti U20 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
INSERT INTO `age_categories` VALUES (4, 'U18', 16, 17, 'Alla 18-aastased noormehed/ neiud', 'Eesti U18 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', '2025-02-02 22:45:42', 1, 2);
INSERT INTO `age_categories` VALUES (5, 'U16', 14, 15, 'Alla 16-aastased poisid/ tüdrukud', 'Eesti U16 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
INSERT INTO `age_categories` VALUES (6, 'U14', 12, 13, 'Alla 14-aastased poisid/ tüdrukud', 'Eesti U14 vanuseklassi rekordid', 1, 'John Doe', '2025-01-21 00:00:00', NULL, 1, 2);
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
INSERT INTO `age_categories_editors_assn` VALUES (1, 3);
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
INSERT INTO `age_category_gender` VALUES (39, 3, 1, 4, 2, 'Alex Smith', '2025-02-10 21:59:05', NULL, 1, 1);
INSERT INTO `age_category_gender` VALUES (40, 3, 2, 3, 2, 'Alex Smith', '2025-02-10 21:59:33', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (41, 4, 1, 4, 2, 'Alex Smith', '2025-02-10 22:00:22', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (42, 4, 2, 3, 2, 'Alex Smith', '2025-02-10 22:00:36', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (43, 5, 1, 6, 2, 'Alex Smith', '2025-02-10 22:01:02', NULL, 1, 1);
INSERT INTO `age_category_gender` VALUES (44, 5, 2, 5, 2, 'Alex Smith', '2025-02-10 22:01:15', NULL, 1, 2);
INSERT INTO `age_category_gender` VALUES (45, 6, 1, 6, 2, 'Alex Smith', '2025-02-10 22:08:37', '2025-02-11 17:23:12', 1, 1);
INSERT INTO `age_category_gender` VALUES (46, 6, 2, 5, 2, 'Alex Smith', '2025-02-10 22:08:54', '2025-02-10 22:09:37', 1, 1);
INSERT INTO `age_category_gender` VALUES (48, 1, 1, 2, 2, 'Alex Smith', '2025-02-11 17:08:43', '2025-02-11 17:12:59', 1, 2);
INSERT INTO `age_category_gender` VALUES (49, 1, 2, 1, 2, 'Alex Smith', '2025-02-11 17:08:57', NULL, 1, 2);
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
  `photo_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `photo_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  CONSTRAINT `album_ibfk_3` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of album
-- ----------------------------
BEGIN;
INSERT INTO `album` VALUES (1060, 40, 21, 'Kogukonna galerii', 1105, 2559, 'Tiit mõtleb.jpg', '/kogukonna-galerii/tiidu-album/Tiit mõtleb.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', '2024-09-17 17:41:29');
INSERT INTO `album` VALUES (1062, 40, 21, 'Kogukonna galerii', 1105, 2561, 'f_DSC01660.jpg', '/kogukonna-galerii/tiidu-album/f_DSC01660.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', NULL);
INSERT INTO `album` VALUES (1063, 40, 21, 'Kogukonna galerii', 1105, 2562, 'karikakrad_vihmas.jpg', '/kogukonna-galerii/tiidu-album/karikakrad_vihmas.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', NULL);
INSERT INTO `album` VALUES (1066, 40, 21, 'Kogukonna galerii', 1105, 2565, 'Pildistamisel.jpg', '/kogukonna-galerii/tiidu-album/Pildistamisel.jpg', NULL, NULL, 1, '2024-08-30 16:53:47', '2024-09-17 17:41:58');
INSERT INTO `album` VALUES (1068, 40, 21, 'Kogukonna galerii', 1105, 2567, 'Luik.jpg', '/kogukonna-galerii/tiidu-album/Luik.jpg', NULL, NULL, 1, '2024-08-30 16:54:28', '2024-09-17 17:41:42');
INSERT INTO `album` VALUES (1070, 41, 21, 'Kogukonna galerii', 1106, 2569, '310596090_1482278652270764_6161734453730055725_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1071, 41, 21, 'Kogukonna galerii', 1106, 2570, '310625658_5771591356213069_6130322049604942068_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1072, 41, 21, 'Kogukonna galerii', 1106, 2571, '310651429_413903317415234_1877068238628190472_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1073, 41, 21, 'Kogukonna galerii', 1106, 2572, '310986468_785287795913568_6096172368795184477_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1074, 41, 21, 'Kogukonna galerii', 1106, 2573, '311163895_800320941296129_7328794715150918241_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1075, 41, 21, 'Kogukonna galerii', 1106, 2574, '311271898_5500936233356667_4481537757649627936_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1076, 41, 21, 'Kogukonna galerii', 1106, 2575, '311451979_627793208998847_3710757790573382164_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1077, 41, 21, 'Kogukonna galerii', 1106, 2576, '311464218_606307097952705_2986433564733245675_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1083, 45, 21, 'Kogukonna galerii', 1110, 2595, '403617_297643386939380_307791209_n.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/403617_297643386939380_307791209_n.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1084, 45, 21, 'Kogukonna galerii', 1110, 2596, '6954421-christmas-lights.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/6954421-christmas-lights.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1085, 45, 21, 'Kogukonna galerii', 1110, 2597, '2078524051_ed4de415ef_o.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/2078524051_ed4de415ef_o.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1086, 45, 21, 'Kogukonna galerii', 1110, 2598, '2094750459_7e05256e05_o.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/2094750459_7e05256e05_o.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1087, 45, 21, 'Kogukonna galerii', 1110, 2599, 'Bnowchristmas_1600x1200.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Bnowchristmas_1600x1200.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1088, 45, 21, 'Kogukonna galerii', 1110, 2600, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1089, 45, 21, 'Kogukonna galerii', 1110, 2601, 'Christmas_candles_by_SizkaS.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_candles_by_SizkaS.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1090, 45, 21, 'Kogukonna galerii', 1110, 2602, 'Christmas_Greetings_2009.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Greetings_2009.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1091, 45, 21, 'Kogukonna galerii', 1110, 2603, 'Christmas_Wallpaper_Snowman_Snow.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Wallpaper_Snowman_Snow.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1093, 45, 21, 'Kogukonna galerii', 1110, 2605, 'christmas-2618263_1280.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2618263_1280.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1094, 45, 21, 'Kogukonna galerii', 1110, 2606, 'christmas-2877141_1280.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2877141_1280.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1095, 45, 21, 'Kogukonna galerii', 1110, 2607, 'Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1096, 45, 21, 'Kogukonna galerii', 1110, 2608, 'christmas-night-magic-house.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-night-magic-house.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1097, 45, 21, 'Kogukonna galerii', 1110, 2609, 'christmas-wallpapers-backgrounds.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers-backgrounds.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1098, 45, 21, 'Kogukonna galerii', 1110, 2610, 'christmas-wallpapers.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1099, 45, 21, 'Kogukonna galerii', 1110, 2611, 'ChristmasCandlelightss1.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ChristmasCandlelightss1.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1100, 45, 21, 'Kogukonna galerii', 1110, 2612, 'ehted_pky.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ehted_pky.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1101, 45, 21, 'Kogukonna galerii', 1110, 2613, 'ekl_jolukaart_2015.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_jolukaart_2015.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1103, 45, 21, 'Kogukonna galerii', 1110, 2615, 'ekl_joulukaart_2012.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2012.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1106, 45, 21, 'Kogukonna galerii', 1110, 2618, 'ekl_joulukaart_2013.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2013.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1109, 45, 21, 'Kogukonna galerii', 1110, 2621, 'ekl_joulukaart_2016.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2016.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1110, 45, 21, 'Kogukonna galerii', 1110, 2622, 'ekl_joulukaart_2021.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2021.jpg', NULL, NULL, 1, '2024-08-31 13:45:13', NULL);
INSERT INTO `album` VALUES (1112, 45, 21, 'Kogukonna galerii', 1110, 2729, 'f_DSC01660.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/f_DSC01660.jpg', NULL, NULL, 1, '2024-09-12 18:40:38', NULL);
INSERT INTO `album` VALUES (1113, 45, 21, 'Kogukonna galerii', 1110, 2730, 'file60471593_d5a21f14.jpg', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-09-12 18:40:38', NULL);
INSERT INTO `album` VALUES (1116, 46, 21, 'Kogukonna galerii', 1112, 2733, 'raamatud.jpg', '/kogukonna-galerii/uus-album/raamatud.jpg', NULL, NULL, 1, '2024-09-12 19:19:07', NULL);
INSERT INTO `album` VALUES (1117, 46, 21, 'Kogukonna galerii', 1112, 2734, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', '/kogukonna-galerii/uus-album/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, NULL, 1, '2024-09-12 19:19:07', NULL);
INSERT INTO `album` VALUES (1118, 46, 21, 'Kogukonna galerii', 1112, 2735, 'r 175.jpg', '/kogukonna-galerii/uus-album/r 175.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1119, 46, 21, 'Kogukonna galerii', 1112, 2736, 'rukkilill.jpg', '/kogukonna-galerii/uus-album/rukkilill.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1120, 46, 21, 'Kogukonna galerii', 1112, 2737, 'seinakell.jpg', '/kogukonna-galerii/uus-album/seinakell.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1121, 46, 21, 'Kogukonna galerii', 1112, 2738, 'vilinus reis 2263.jpg', '/kogukonna-galerii/uus-album/vilinus reis 2263.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1122, 46, 21, 'Kogukonna galerii', 1112, 2739, 'valentinikyynlas.JPG', '/kogukonna-galerii/uus-album/valentinikyynlas.JPG', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1133, 48, 21, 'Kogukonna galerii', 1123, 2777, 'DSC_0008.JPG', '/kogukonna-galerii/tanugala-2024/DSC_0008.JPG', NULL, NULL, 1, '2024-10-10 16:27:49', NULL);
INSERT INTO `album` VALUES (1134, 48, 21, 'Kogukonna galerii', 1123, 2778, 'allkiri.png', '/kogukonna-galerii/tanugala-2024/allkiri.png', NULL, NULL, 1, '2024-10-10 16:27:49', '2024-10-21 01:53:36');
INSERT INTO `album` VALUES (1135, 48, 21, 'Kogukonna galerii', 1123, 2779, 'DSC_0084.JPG', '/kogukonna-galerii/tanugala-2024/DSC_0084.JPG', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1136, 48, 21, 'Kogukonna galerii', 1123, 2780, 'DSC_5197_1.jpg', '/kogukonna-galerii/tanugala-2024/DSC_5197_1.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1137, 48, 21, 'Kogukonna galerii', 1123, 2781, 'DSC_5177_1.jpg', '/kogukonna-galerii/tanugala-2024/DSC_5177_1.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1138, 48, 21, 'Kogukonna galerii', 1123, 2782, 'DSC_7550.jpg', '/kogukonna-galerii/tanugala-2024/DSC_7550.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', '2024-10-21 23:02:04');
INSERT INTO `album` VALUES (1140, 48, 21, 'Kogukonna galerii', 1123, 2784, 'seebimullid.jpg', '/kogukonna-galerii/tanugala-2024/seebimullid.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', '2024-10-30 15:08:00');
INSERT INTO `album` VALUES (1141, 48, 21, 'Kogukonna galerii', 1123, 2785, 'kuldnokk puuladvas.jpg', '/kogukonna-galerii/tanugala-2024/kuldnokk puuladvas.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', '2024-10-30 15:08:57');
INSERT INTO `album` VALUES (1144, 48, 21, 'Kogukonna galerii', 1123, 2788, 'file60471593_d5a21f14.jpg', '/kogukonna-galerii/tanugala-2024/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', NULL);
INSERT INTO `album` VALUES (1145, 48, 21, 'Kogukonna galerii', 1123, 2789, 'galerii67681.jpg', '/kogukonna-galerii/tanugala-2024/galerii67681.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', NULL);
INSERT INTO `album` VALUES (1146, 40, 21, 'Kogukonna galerii', 1105, 2790, 'DSC_5197_1.jpg', '/kogukonna-galerii/tiidu-album/DSC_5197_1.jpg', NULL, NULL, 1, '2024-10-24 21:02:43', NULL);
INSERT INTO `album` VALUES (1147, 40, 21, 'Kogukonna galerii', 1105, 2791, 'DSC_5177_1.jpg', '/kogukonna-galerii/tiidu-album/DSC_5177_1.jpg', NULL, NULL, 1, '2024-10-24 21:02:43', NULL);
INSERT INTO `album` VALUES (1150, 53, 21, 'Kogukonna galerii', 1131, 2808, 'DSC_5197_1.jpg', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/DSC_5197_1.jpg', NULL, NULL, 1, '2024-12-01 01:39:44', NULL);
INSERT INTO `album` VALUES (1151, 53, 21, 'Kogukonna galerii', 1131, 2809, 'DSC_5177_1.jpg', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/DSC_5177_1.jpg', NULL, NULL, 1, '2024-12-01 01:39:45', NULL);
INSERT INTO `album` VALUES (1152, 53, 21, 'Kogukonna galerii', 1131, 2810, 'f_DSC01660.jpg', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/f_DSC01660.jpg', NULL, NULL, 1, '2024-12-01 01:39:45', NULL);
INSERT INTO `album` VALUES (1153, 53, 21, 'Kogukonna galerii', 1131, 2811, 'file60471593_d5a21f14.jpg', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-12-01 01:39:45', NULL);
INSERT INTO `album` VALUES (1154, 55, 21, 'Kogukonna galerii', 1138, 2813, '4686233863_aeb72a24df_b.jpg', '/kogukonna-galerii/esimene-uus-album/4686233863_aeb72a24df_b.jpg', NULL, NULL, 1, '2025-01-01 13:09:46', NULL);
INSERT INTO `album` VALUES (1155, 55, 21, 'Kogukonna galerii', 1138, 2814, 'DSC_0084.JPG', '/kogukonna-galerii/esimene-uus-album/DSC_0084.JPG', NULL, NULL, 1, '2025-01-01 13:09:46', NULL);
INSERT INTO `album` VALUES (1156, 55, 21, 'Kogukonna galerii', 1138, 2815, 'DSC_5177_1.jpg', '/kogukonna-galerii/esimene-uus-album/DSC_5177_1.jpg', NULL, NULL, 1, '2025-01-01 13:09:46', NULL);
INSERT INTO `album` VALUES (1157, 55, 21, 'Kogukonna galerii', 1138, 2816, 'f_DSC01660.jpg', '/kogukonna-galerii/esimene-uus-album/f_DSC01660.jpg', NULL, NULL, 1, '2025-01-01 13:09:46', NULL);
INSERT INTO `album` VALUES (1158, 58, 21, 'Kogukonna galerii', 1143, 2817, 'Tiit_Papp_2021.jpg', '/kogukonna-galerii/testime-teist-uut-albumit/Tiit_Papp_2021.jpg', NULL, NULL, 1, '2025-01-02 09:29:44', NULL);
INSERT INTO `album` VALUES (1159, 57, 26, 'Kurtide galerii', 1142, 2818, '6954421-christmas-lights.jpg', '/kurtide-galerii/testime-uut-albumit/6954421-christmas-lights.jpg', NULL, NULL, 1, '2025-01-02 15:40:48', NULL);
INSERT INTO `album` VALUES (1160, 57, 26, 'Kurtide galerii', 1142, 2819, '403617_297643386939380_307791209_n.jpg', '/kurtide-galerii/testime-uut-albumit/403617_297643386939380_307791209_n.jpg', NULL, NULL, 1, '2025-01-02 15:40:48', NULL);
INSERT INTO `album` VALUES (1161, 57, 26, 'Kurtide galerii', 1142, 2820, 'Bnowchristmas_1600x1200.jpg', '/kurtide-galerii/testime-uut-albumit/Bnowchristmas_1600x1200.jpg', NULL, NULL, 1, '2025-01-02 15:40:49', NULL);
INSERT INTO `album` VALUES (1162, 57, 26, 'Kurtide galerii', 1142, 2821, 'christmas-2877141_1280.jpg', '/kurtide-galerii/testime-uut-albumit/christmas-2877141_1280.jpg', NULL, NULL, 1, '2025-01-02 15:40:49', NULL);
INSERT INTO `album` VALUES (1163, 57, 26, 'Kurtide galerii', 1142, 2822, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', '/kurtide-galerii/testime-uut-albumit/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, NULL, 1, '2025-01-02 15:40:49', NULL);
INSERT INTO `album` VALUES (1164, 60, 26, 'Kurtide galerii', 1145, 2824, 'Sinine ehe kuuseoksa peal.jpg', '/kurtide-galerii/kurtide-sundmus-2024/Sinine ehe kuuseoksa peal.jpg', NULL, NULL, 1, '2025-01-03 12:48:05', '2025-01-03 15:39:26');
INSERT INTO `album` VALUES (1166, 63, 17, 'Pildigalerii', 1149, 2829, 'Tundmatu.png', '/pildigalerii/teeme-uue-susteemiga-albumi/Tundmatu.png', NULL, NULL, 1, '2025-01-07 16:08:28', '2025-01-07 16:08:56');
INSERT INTO `album` VALUES (1167, 63, 17, 'Pildigalerii', 1149, 2874, '4680076964_298f35a321_b.jpg', '/pildigalerii/teeme-uue-susteemiga-albumi/4680076964_298f35a321_b.jpg', NULL, NULL, 1, '2025-03-08 16:47:32', NULL);
INSERT INTO `album` VALUES (1168, 63, 17, 'Pildigalerii', 1149, 2875, '4686233863_aeb72a24df_b.jpg', '/pildigalerii/teeme-uue-susteemiga-albumi/4686233863_aeb72a24df_b.jpg', NULL, NULL, 1, '2025-03-08 16:47:32', NULL);
INSERT INTO `album` VALUES (1169, 63, 17, 'Pildigalerii', 1149, 2876, 'DSC_5177_1.jpg', '/pildigalerii/teeme-uue-susteemiga-albumi/DSC_5177_1.jpg', NULL, NULL, 1, '2025-03-08 16:47:32', NULL);
INSERT INTO `album` VALUES (1170, 63, 17, 'Pildigalerii', 1149, 2877, 'DSC_5197_1.jpg', '/pildigalerii/teeme-uue-susteemiga-albumi/DSC_5197_1.jpg', NULL, NULL, 1, '2025-03-08 16:47:33', NULL);
COMMIT;

-- ----------------------------
-- Table structure for albums
-- ----------------------------
DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vi_0900_ai_ci,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_activity` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of albums
-- ----------------------------
BEGIN;
INSERT INTO `albums` VALUES (38, 989, 'galerii', '/galerii', 'galerii', '2024-02-04 20:09:11', '2024-02-18 19:21:58', 1);
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
INSERT INTO `article` VALUES (70, 299, 'Eesti Kurtide Liidu-põhikiri', NULL, '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', NULL, '', NULL, NULL, '<p><img alt=\"\" src=\"/qcubed-4/project/tmp/_files/thumbnail/Konventeerimine/karikakrad_vihmas.jpg\" style=\"float:left; height:217px; margin:5px 10px; width:320px\" /></p>\n', '2024-07-03 20:43:31', '2025-01-08 16:59:51', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (81, 353, 'Uurime aadressi muutumist', NULL, '/blaaa/uurime-aadressi-muutumist', 2794, '', NULL, NULL, '<table class=\"table table-bordered table-hover table-striped\" style=\"width:70%\">\n	<tbody>\n		<tr>\n			<td style=\"text-align:center; width:285px\"><span style=\"font-family:Verdana,Geneva,sans-serif\">Kalenderplaan</span></td>\n			<td style=\"width:255px\">&nbsp;</td>\n			<td style=\"width:143px\">&nbsp;</td>\n			<td style=\"width:91px\">&nbsp;</td>\n		</tr>\n		<tr>\n			<td style=\"width:285px\">&nbsp;</td>\n			<td style=\"width:255px\">&nbsp;</td>\n			<td style=\"width:143px\">&nbsp;</td>\n			<td style=\"width:91px\">&nbsp;</td>\n		</tr>\n		<tr>\n			<td style=\"width:285px\">&nbsp;</td>\n			<td style=\"width:255px\">&nbsp;</td>\n			<td style=\"width:143px\">&nbsp;</td>\n			<td style=\"width:91px\">&nbsp;</td>\n		</tr>\n		<tr>\n			<td style=\"width:285px\">&nbsp;</td>\n			<td style=\"width:255px\">&nbsp;</td>\n			<td style=\"width:143px\">&nbsp;</td>\n			<td style=\"width:91px\">&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-10-17 02:35:53', '2025-03-06 13:26:22', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (82, 456, 'Statistika avapauk', NULL, '/statistikastatistika-avapauk', NULL, '', NULL, NULL, NULL, '2024-12-22 17:34:40', '2024-12-22 17:35:15', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (83, 550, 'Organisatsiooni kontaktandmed', NULL, '/organisatsioon/organisatsiooni-kontaktandmed', 2878, '', 'Eesti Kurtide Liidu maja Tallinnas', 'FOTO: EKL', '<table align=\"center\" border=\"0\" style=\"width:600px\">\n	<tbody>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Organisatsiooni nimi:</strong></td>\n			<td style=\"vertical-align:top\">MT&Uuml; Eesti Kurtide Liit</td>\n		</tr>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Juriidiline aadress:</strong></td>\n			<td>N&otilde;mme tee 2, 13426 Tallinn</td>\n		</tr>\n		<tr>\n			<td><strong>Telefon:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>Faks:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>SMS:</strong></td>\n			<td>+372 5218851</td>\n		</tr>\n		<tr>\n			<td><strong>E-mail:</strong></td>\n			<td>ead@<img alt=\"\" src=\"http://www.ead.ee/automatweb/images/at.png\" />ead.ee</td>\n		</tr>\n		<tr>\n			<td><strong>Registrikood:</strong></td>\n			<td>80007861</td>\n		</tr>\n		<tr>\n			<td><strong>Arveldusarve:</strong></td>\n			<td>EE891010022002532007&nbsp;SEB</td>\n		</tr>\n		<tr>\n			<td><strong>SWIFT kood (BIC):</strong></td>\n			<td>EEUHEE2X</td>\n		</tr>\n		<tr>\n			<td><strong>Asutatud:</strong></td>\n			<td>1922</td>\n		</tr>\n		<tr>\n			<td><strong>Liikmete arv:</strong></td>\n			<td>9 &uuml;hingut ja 2 organisatsiooni, 3 ettev&otilde;tet,&nbsp; 857 &uuml;ksikisikut <em>(01.09.2020. a. seisuga)</em></td>\n		</tr>\n		<tr>\n			<td><strong>Juhatuse esimees:</strong></td>\n			<td>\n			<p>Tiit Papp</p>\n			</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-12-23 23:32:25', '2025-03-10 01:10:49', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (87, 608, 'Tänitame kindlati edasi', NULL, '/parent/tanitame-kindlati-edasi', 1121, '2794', NULL, NULL, '<table align=\"center\" class=\"table table-bordered table-hover table-responsive\" style=\"width:50%\">\n	<caption>\n	<h3 style=\"text-align:center\">Nimekiri</h3>\n	</caption>\n	<thead>\n		<tr>\n			<th scope=\"col\">Eesnimi</th>\n			<th scope=\"col\">Perenimi</th>\n			<th scope=\"col\">Sugu</th>\n		</tr>\n	</thead>\n	<tbody>\n		<tr>\n			<td>Tiit</td>\n			<td>Papp</td>\n			<td>Mees</td>\n		</tr>\n		<tr>\n			<td>Ene</td>\n			<td>Papp</td>\n			<td>Naine</td>\n		</tr>\n	</tbody>\n</table>\n\n<p><img alt=\"\" id=\"2794\" src=\"/qcubed-4/project/tmp/_files/thumbnail/kurtide_liidu_maja_2013.jpg\" style=\"height:213px; width:320px\" /></p>\n', '2024-12-29 16:18:15', '2025-03-08 16:26:10', 1, 'John Doe', 0);
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
INSERT INTO `articles_editors_assn` VALUES (81, 3);
INSERT INTO `articles_editors_assn` VALUES (82, 3);
INSERT INTO `articles_editors_assn` VALUES (83, 3);
INSERT INTO `articles_editors_assn` VALUES (87, 3);
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of athletes
-- ----------------------------
BEGIN;
INSERT INTO `athletes` VALUES (1, 'Kairit', 'Olenko', '1985-12-21', 1, 1, 'John Doe', '2025-01-21 17:23:32', '2025-02-10 01:12:48', 1, 2);
INSERT INTO `athletes` VALUES (12, 'Rinat', 'Raisp', '2001-05-25', 2, 1, 'John Doe', '2025-01-24 22:38:52', '2025-01-28 22:06:30', 1, 2);
INSERT INTO `athletes` VALUES (13, 'Ilvi', 'Vare', '1970-07-03', 1, 1, 'John Doe', '2025-01-25 00:32:48', '2025-01-26 14:43:59', 1, 1);
INSERT INTO `athletes` VALUES (17, 'Tanel', 'Visnap', '1998-09-23', 2, 1, 'John Doe', '2025-01-25 00:47:33', '2025-02-10 15:41:01', 1, 2);
INSERT INTO `athletes` VALUES (22, 'Jörgen', 'Liiv', '1990-04-12', 2, 2, 'Alex Smith', '2025-01-25 04:05:31', '2025-02-18 16:33:39', 1, 2);
INSERT INTO `athletes` VALUES (23, 'Annely', 'Ojastu', '1960-08-10', 1, 2, 'Alex Smith', '2025-01-25 04:54:02', '2025-02-06 23:43:58', 1, 2);
INSERT INTO `athletes` VALUES (25, 'Emilija', 'Manninen', '1981-01-22', 2, 2, 'Alex Smith', '2025-01-25 05:00:49', '2025-02-10 14:21:21', 1, 1);
INSERT INTO `athletes` VALUES (43, 'Ene', 'Papp', '1958-05-14', 1, 3, 'Samantha Jones', '2025-02-06 15:13:13', '2025-02-21 10:51:24', 1, 1);
INSERT INTO `athletes` VALUES (47, 'Sirle', 'Papp', '1988-04-28', 1, 3, 'Samantha Jones', '2025-02-10 15:40:31', '2025-02-21 10:50:28', 1, 2);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of board
-- ----------------------------
BEGIN;
INSERT INTO `board` VALUES (1, 2806, 2806, 377, 7, 'Juhatus', 0, 'Tiit Papp', 'Juhatuse esimees', 'Liidu esindamine, juhatuse töö korraldamine, üldjuhtimine', NULL, NULL, '', '+372 521 8851', NULL, NULL, 'ead@ead.ee', NULL, 1, '2024-11-01 21:50:11', '2025-03-08 16:52:57');
INSERT INTO `board` VALUES (2, 2805, 2805, 377, 7, 'Juhatus', 2, 'Sirle Papp', 'Juhatuse liige', 'Meedia, haridus, töö noortega\n', NULL, NULL, NULL, '+372 5331 7152', NULL, NULL, 'sirlepapp@gmail.com', NULL, 1, '2024-11-02 00:41:19', '2025-01-16 22:02:51');
INSERT INTO `board` VALUES (6, 2804, 2804, 377, 7, 'Juhatus', 1, 'Riina Kuusk', 'Juhatuse aseesimees', 'Tööhõive, töö pensionäridega, esimehe äraolekul liidu esindamine, juhatuse töö korraldamine\n\n', NULL, NULL, NULL, '+372 5650 3051', NULL, NULL, 'riinak61@gmail.com', NULL, 1, '2024-11-02 19:43:46', '2025-01-16 22:02:51');
INSERT INTO `board` VALUES (7, 2801, 2801, 377, 7, 'Juhatus', 3, 'Helle Sass', 'Juhatuse liige', 'Kultuuritöö, liidu esindamine Eesti Puuetega Inimeste Kojas', NULL, NULL, '+372 5399 7837', '+372 5399 7837', NULL, NULL, 'helle.sass@gmail.com', NULL, 1, '2024-11-02 19:50:30', '2025-01-16 22:02:51');
INSERT INTO `board` VALUES (8, 2802, 2802, 377, 7, 'Juhatus', 4, 'Janis Golubenkov', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 19:58:05', '2025-01-16 22:02:51');
INSERT INTO `board` VALUES (10, 2803, 2803, 377, 7, 'Juhatus', 5, 'Mati Kartus', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 20:52:28', '2025-01-16 22:02:51');
INSERT INTO `board` VALUES (13, 2800, 2800, 378, 8, 'Kultuuri juhatus', 1, 'Jakob Hurd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 22:34:12', '2025-03-08 16:52:08');
INSERT INTO `board` VALUES (15, 2798, 2798, NULL, 8, 'Kultuuri juhatus', 0, 'Lisse', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-08 16:51:41', '2025-03-08 16:52:08');
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
INSERT INTO `board_options` VALUES (13, 8, 2, 'Position', 2, 2);
INSERT INTO `board_options` VALUES (14, 8, 3, 'Areas responsibility', 1, 2);
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
INSERT INTO `boards_settings` VALUES (7, 'Juhatus', 'Eesti Kurtide Liidu juhatus 2018 - 2023', 1, 1, 377, '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023', '2024-10-30 22:19:29', '2025-03-08 16:52:57', 1, 'John Doe', 1, 1);
INSERT INTO `boards_settings` VALUES (8, 'Kultuuri juhatus', 'Kultuuri juhatus 2023 - 2028', 1, 1, 378, '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028/kultuuri-juhatus-2023-2028', '2024-11-01 01:38:19', '2025-03-08 16:52:08', 1, 'John Doe', 1, 1);
INSERT INTO `boards_settings` VALUES (9, 'Spordi juhatus', 'Spordi juhatus 2023 - 2028', 1, 2, 379, '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028/spordi-juhatus-2023-2028', '2024-11-01 13:21:14', '2025-01-10 14:38:51', 1, 'John Doe', 0, 2);
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
INSERT INTO `category_of_article` VALUES (1, 'Education', 2, '2020-05-30 10:00:00', '2024-12-14 18:50:42');
INSERT INTO `category_of_article` VALUES (2, 'Culture', 2, '2020-05-30 10:00:00', '2024-11-15 19:17:40');
INSERT INTO `category_of_article` VALUES (3, 'Sport', 2, '2020-05-30 10:00:44', '2024-07-31 12:07:05');
INSERT INTO `category_of_article` VALUES (4, 'History', 2, '2020-05-30 10:00:44', '2024-07-31 12:07:09');
INSERT INTO `category_of_article` VALUES (5, 'Varia', 2, '2020-05-30 10:00:44', '2024-07-31 12:08:32');
INSERT INTO `category_of_article` VALUES (6, 'Info', 2, '2021-06-29 22:10:57', '2024-07-31 12:08:28');
INSERT INTO `category_of_article` VALUES (8, 'Politics', 2, '2021-06-29 22:23:59', '2024-07-05 22:01:58');
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
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_2` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of category_of_news
-- ----------------------------
BEGIN;
INSERT INTO `category_of_news` VALUES (2, 'Life', 1, '2020-09-12 11:00:00', '2024-11-15 23:47:10');
INSERT INTO `category_of_news` VALUES (3, 'Education', 2, '2020-09-12 11:00:00', '2024-08-23 11:00:59');
INSERT INTO `category_of_news` VALUES (4, 'Business', 2, '2020-09-13 00:00:00', '2024-07-31 11:43:08');
INSERT INTO `category_of_news` VALUES (5, 'Health', 2, '2020-08-01 21:29:00', '2024-07-31 11:43:12');
INSERT INTO `category_of_news` VALUES (12, 'Sport', 2, '2024-05-16 00:06:15', '2024-07-31 11:43:16');
INSERT INTO `category_of_news` VALUES (17, 'Politics', 1, '2024-08-23 21:44:35', '2024-11-15 23:47:18');
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
  `tabs_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_names` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home page', 'Edit homepage', 'HomeEditPanel', 1);
INSERT INTO `content_type` VALUES (2, 'Article', 'Edit article', 'ArticleEditPanel', 1);
INSERT INTO `content_type` VALUES (3, 'News', 'Edit news', 'NewsEditPanel', 1);
INSERT INTO `content_type` VALUES (4, 'Gallery', 'Edit gallery', 'GalleryEditPanel', 1);
INSERT INTO `content_type` VALUES (5, 'Events calendar', 'Edit events calendar', 'EventsCalendarEditPanel', 1);
INSERT INTO `content_type` VALUES (6, 'Sports calendar', 'Edit sports calendar ', 'SportsCalendarEditPanel', 1);
INSERT INTO `content_type` VALUES (7, 'Internal page link', 'Edit internal page link', 'InternalPageEditPanel', 1);
INSERT INTO `content_type` VALUES (8, 'Redirecting link', 'Edit redirecting link', 'RedirectingEditPanel', 1);
INSERT INTO `content_type` VALUES (9, 'Placeholder', 'Edit placeholder', 'PlaceholderEditPanel', 0);
INSERT INTO `content_type` VALUES (10, 'Sports areas', 'Edit sports areas', 'SportsAreasEditPanel', 1);
INSERT INTO `content_type` VALUES (11, 'Board', 'Edit board', 'BoardEditPanel', 1);
INSERT INTO `content_type` VALUES (12, 'Members', 'Edit members', 'MembersEditPanel', 1);
INSERT INTO `content_type` VALUES (13, 'Videos', 'Edit videos', 'VideosEditPanel', 1);
INSERT INTO `content_type` VALUES (14, 'Statistics (Records)', 'Edit record statistics', 'RecordStatisticsEditPanel', 1);
INSERT INTO `content_type` VALUES (15, 'Statistics (Rankings)', 'Edit rankings statistics', 'RankingsStatisticsPanel', 1);
INSERT INTO `content_type` VALUES (16, 'Statistics (Achievements)', 'Edit achievement statistics', 'AchievementStatisticsPanel', 1);
INSERT INTO `content_type` VALUES (17, 'Links', 'Edit links', 'LinksEditPanel', 1);
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
  PRIMARY KEY (`id`),
  KEY `content_type_id_idx` (`content_type`) USING BTREE,
  KEY `view_type_id_idx` (`view_type`) USING BTREE,
  KEY `id` (`id`,`content_name`),
  KEY `content_name` (`content_name`),
  CONSTRAINT `content_type_id_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `view_type_id_fk` FOREIGN KEY (`view_type`) REFERENCES `view_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of content_types_management
-- ----------------------------
BEGIN;
INSERT INTO `content_types_management` VALUES (1, 'Home view', 1, 1);
INSERT INTO `content_types_management` VALUES (2, 'Article detail view', 2, 3);
INSERT INTO `content_types_management` VALUES (3, 'News list view', 3, 2);
INSERT INTO `content_types_management` VALUES (4, 'News detail view', 3, 3);
INSERT INTO `content_types_management` VALUES (5, 'Gallery list view', 4, 2);
INSERT INTO `content_types_management` VALUES (6, 'Gallery detail view', 4, 3);
INSERT INTO `content_types_management` VALUES (7, 'Events calerdar list view', 5, 2);
INSERT INTO `content_types_management` VALUES (8, 'Events calendar detail view', 5, 3);
INSERT INTO `content_types_management` VALUES (9, 'Sports calendar list view', 6, 2);
INSERT INTO `content_types_management` VALUES (10, 'Sports calendar detail view', 6, 3);
INSERT INTO `content_types_management` VALUES (11, 'Sports areas detail view', 10, 3);
INSERT INTO `content_types_management` VALUES (12, 'Board detail view', 11, 3);
INSERT INTO `content_types_management` VALUES (13, 'Members detail view', 12, 3);
INSERT INTO `content_types_management` VALUES (14, 'Videos detail view', 13, 3);
INSERT INTO `content_types_management` VALUES (15, 'Statistics (Records) detail view', 14, 3);
INSERT INTO `content_types_management` VALUES (16, 'Statistics (Rankings) detail view', 15, 3);
INSERT INTO `content_types_management` VALUES (17, 'Statistics (Achievements) detail view', 16, 3);
INSERT INTO `content_types_management` VALUES (18, 'Links detail view', 17, 3);
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_calendar
-- ----------------------------
BEGIN;
INSERT INTO `events_calendar` VALUES (38, NULL, 400, 6, 'Esimene kalender', NULL, NULL, 2726, NULL, NULL, NULL, 2024, 'Kurtide tore päev', '/sundmuste-kalender/esimene-kalender/2024/kurtide-tore-paev', 'Kuu peal', '2024-10-26', NULL, NULL, NULL, NULL, NULL, NULL, 'www.kuu.ku', 4, NULL, NULL, NULL, NULL, 'Humanoid', '+372 1234 5678', 'humanoid@huanoid.ku', 3, 'Samantha Jones', '2024-09-20 17:27:27', '2025-01-14 10:26:25', 1);
INSERT INTO `events_calendar` VALUES (39, NULL, 400, 6, 'Esimene kalender', NULL, '', NULL, NULL, NULL, NULL, 2024, 'Naistepäev', '/sundmuste-kalender/esimene-kalender/2024/naistepaev', 'Mujal', '2024-10-31', NULL, NULL, NULL, NULL, NULL, NULL, 'www.thky.ee', 1, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 521 8851', 'spordiliit@ead.ee', 3, 'Samantha Jones', '2024-09-20 23:04:07', '2025-01-14 10:19:16', 1);
INSERT INTO `events_calendar` VALUES (40, NULL, 336, 1, 'Sündmuste kalender', NULL, '', 2751, NULL, NULL, NULL, 2024, 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev-1', 'Tallinna teletorni juures', '2024-09-23', NULL, '21:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sirle Papp', '+372 58 99990', 'ead@ead.ee', 3, 'Samantha Jones', '2024-09-20 23:04:42', '2025-01-14 10:19:11', 1);
INSERT INTO `events_calendar` VALUES (41, NULL, 336, 1, 'Sündmuste kalender', NULL, '', 1693, NULL, NULL, NULL, 2024, 'Naistepäev', '/sundmuste-kalender/2024/naistepaev', 'Tartus', '2024-09-29', NULL, NULL, NULL, '<p>Head naistep&auml;eva!</p>\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 521 8851', 'tiit.papp@gmail.com', 3, 'Samantha Jones', '2024-09-20 23:43:59', '2025-01-14 10:19:06', 1);
INSERT INTO `events_calendar` VALUES (42, NULL, 336, 1, 'Sündmuste kalender', NULL, '', 2722, NULL, 'Blaaaa voootttt', 'Foto: Tiit Papp', 2024, 'Loeng', '/sundmuste-kalender/2024/loeng', 'Tallinna kurtide klubis, Nõmme tee 2', '2024-09-30', NULL, '17:30:00', NULL, NULL, '<p>Blaaaaa</p>\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 521 8851', 'tiit.papp@gmail.com', 3, 'Samantha Jones', '2024-09-20 23:46:11', '2025-01-14 10:19:02', 1);
INSERT INTO `events_calendar` VALUES (43, NULL, 336, 1, 'Sündmuste kalender', NULL, '', 2753, NULL, NULL, NULL, 2024, 'Pensionäride kokkutulek', '/sundmuste-kalender/2024/pensionaride-kokkutulek', 'Rakvere linnuses', '2024-06-07', NULL, '11:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 1234 5678', 'ead@ead.ee', 3, 'Samantha Jones', '2024-09-28 10:14:44', '2025-01-14 10:18:58', 1);
INSERT INTO `events_calendar` VALUES (46, NULL, 336, 1, 'Sündmuste kalender', NULL, NULL, NULL, NULL, NULL, NULL, 2024, 'Esimese sündmuse loeng', '/sundmuste-kalender/2024/esimese-sundmuse-loeng', 'Booo', '2024-12-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 123 4567', 'tiit.papp@gmail.com', 1, 'John Doe', '2024-12-03 14:44:26', '2025-01-11 00:23:54', 1);
INSERT INTO `events_calendar` VALUES (47, NULL, 336, 1, 'Sündmuste kalender', NULL, NULL, 2712, NULL, NULL, NULL, 2025, 'Sportlaste kokkutulek', '/sundmuste-kalender/2025/sportlaste-kokkutulek', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'John Doe', '2025-01-04 22:18:38', '2025-03-20 12:52:46', 2);
INSERT INTO `events_calendar` VALUES (48, NULL, 400, 6, 'Esimene kalender', NULL, NULL, NULL, NULL, NULL, NULL, 2025, 'Eesti kurtide meistrivõistlused 2025 KABES', '/sundmuste-kalender/esimene-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'John Doe', '2025-01-07 18:10:04', '2025-01-14 10:26:05', 2);
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
INSERT INTO `events_calendar_editors_assn` VALUES (38, 1);
INSERT INTO `events_calendar_editors_assn` VALUES (39, 1);
INSERT INTO `events_calendar_editors_assn` VALUES (40, 1);
INSERT INTO `events_calendar_editors_assn` VALUES (41, 1);
INSERT INTO `events_calendar_editors_assn` VALUES (42, 1);
INSERT INTO `events_calendar_editors_assn` VALUES (43, 1);
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
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `events_chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_changes
-- ----------------------------
BEGIN;
INSERT INTO `events_changes` VALUES (4, 'Uuendatud', '2024-09-22 16:40:09', '2025-01-07 18:21:53', 2);
INSERT INTO `events_changes` VALUES (5, 'Täiendatud', '2024-09-22 16:40:30', '2025-01-07 18:21:08', 2);
INSERT INTO `events_changes` VALUES (6, 'Edasi lükatud', '2024-09-22 16:40:53', '2025-01-07 18:21:57', 2);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events_settings
-- ----------------------------
BEGIN;
INSERT INTO `events_settings` VALUES (1, 'Sündmuste kalender', 'Sündmuste kalender', '/sundmuste-kalender', 1, 1, 336, '2024-09-18 16:00:00', '2025-01-11 00:23:54', 1);
INSERT INTO `events_settings` VALUES (6, 'Esimene kalender', NULL, '/sundmuste-kalender/esimene-kalender', 1, 1, 400, '2024-12-03 14:43:49', '2025-01-11 00:24:03', 1);
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
INSERT INTO `example` VALUES (2, '<p><img alt=\"\" id=\"1593\" src=\"/qcubed-4/project/tmp/_files/thumbnail/Konventeerimine/karikakrad_vihmas.jpg\" style=\"float:left; height:217px; margin:5px 10px; width:320px\" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie. Duis velit augue,</p>\n', NULL, '1593');
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
) ENGINE=InnoDB AUTO_INCREMENT=2896 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of files
-- ----------------------------
BEGIN;
INSERT INTO `files` VALUES (754, 929, 'seinakell.jpg', 'file', '/Varia/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1700646067, '611 x 404', 611, 404, 0, 0);
INSERT INTO `files` VALUES (755, 929, 'sirlu.jpg', 'file', '/Varia/sirlu.jpg', NULL, 'jpg', 'image/jpeg', 49122, 1700646067, '450 x 600', NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (756, 929, 'sp2_fotologs_net.jpg', 'file', '/Varia/sp2_fotologs_net.jpg', NULL, 'jpg', 'image/jpeg', 17070, 1700646067, '500 x 375', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1121, 1, 'Kolletanud_lehed_maas.jpg', 'file', '/Kolletanud_lehed_maas.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1741298864, '900 x 585', NULL, NULL, 4, 0);
INSERT INTO `files` VALUES (1134, 1, 'EKL aruanne.xlsx', 'file', '/EKL aruanne.xlsx', NULL, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 244192, 1704465357, NULL, NULL, NULL, 0, 0);
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
INSERT INTO `files` VALUES (1593, 937, 'karikakrad_vihmas.jpg', 'file', '/Konventeerimine/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1711542383, '1280 x 868', 1280, 868, 4, 0);
INSERT INTO `files` VALUES (1594, 937, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/Konventeerimine/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1711542717, '1024 x 768', 1024, 768, 0, 0);
INSERT INTO `files` VALUES (1596, 1018, 'IMG_1172.JPG', 'file', '/galerii/uus-test/IMG_1172.JPG', NULL, 'jpg', 'image/jpeg', 1983398, 1723032621, '2592 x 1936', 2592, 1936, 0, 0);
INSERT INTO `files` VALUES (1692, 1026, 'crop_DSC_0084.png', 'file', '/crop-test/crop_DSC_0084.png', NULL, 'png', 'image/png', 3326457, 1719507748, '1519 x 1518', 1519, 1518, 2, 0);
INSERT INTO `files` VALUES (1693, 1026, 'crop_sirlu.png', 'file', '/crop-test/crop_sirlu.png', NULL, 'png', 'image/png', 96899, 1719519637, '314 x 160', 314, 160, 1, 0);
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
INSERT INTO `files` VALUES (2559, 1105, 'Tiit mõtleb.jpg', 'file', '/kogukonna-galerii/tiidu-album/Tiit mõtleb.jpg', NULL, 'jpg', 'image/jpeg', 720646, 1726584089, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2561, 1105, 'f_DSC01660.jpg', 'file', '/kogukonna-galerii/tiidu-album/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1725026026, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2562, 1105, 'karikakrad_vihmas.jpg', 'file', '/kogukonna-galerii/tiidu-album/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1725026026, '1280 x 868', 1280, 868, 1, 1);
INSERT INTO `files` VALUES (2565, 1105, 'Pildistamisel.jpg', 'file', '/kogukonna-galerii/tiidu-album/Pildistamisel.jpg', NULL, 'jpg', 'image/jpeg', 878514, 1726584118, '3072 x 2304', 3072, 2304, 1, 1);
INSERT INTO `files` VALUES (2567, 1105, 'Luik.jpg', 'file', '/kogukonna-galerii/tiidu-album/Luik.jpg', NULL, 'jpg', 'image/jpeg', 820580, 1726584102, '2953 x 1918', 2953, 1918, 1, 1);
INSERT INTO `files` VALUES (2569, 1106, '310596090_1482278652270764_6161734453730055725_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, 'jpeg', 'image/jpeg', 271363, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2570, 1106, '310625658_5771591356213069_6130322049604942068_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, 'jpeg', 'image/jpeg', 231678, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2571, 1106, '310651429_413903317415234_1877068238628190472_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, 'jpeg', 'image/jpeg', 168905, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2572, 1106, '310986468_785287795913568_6096172368795184477_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, 'jpeg', 'image/jpeg', 256565, 1725039577, '2000 x 1126', 2000, 1126, 1, 1);
INSERT INTO `files` VALUES (2573, 1106, '311163895_800320941296129_7328794715150918241_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, 'jpeg', 'image/jpeg', 176036, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2574, 1106, '311271898_5500936233356667_4481537757649627936_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, 'jpeg', 'image/jpeg', 232041, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2575, 1106, '311451979_627793208998847_3710757790573382164_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, 'jpeg', 'image/jpeg', 147897, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2576, 1106, '311464218_606307097952705_2986433564733245675_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, 'jpeg', 'image/jpeg', 264296, 1725039577, '2000 x 1126', 2000, 1126, 1, 1);
INSERT INTO `files` VALUES (2595, 1110, '403617_297643386939380_307791209_n.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/403617_297643386939380_307791209_n.jpg', NULL, 'jpg', 'image/jpeg', 72044, 1725101111, '960 x 768', 960, 768, 1, 1);
INSERT INTO `files` VALUES (2596, 1110, '6954421-christmas-lights.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/6954421-christmas-lights.jpg', NULL, 'jpg', 'image/jpeg', 1583745, 1725101111, '2560 x 1600', 2560, 1600, 1, 1);
INSERT INTO `files` VALUES (2597, 1110, '2078524051_ed4de415ef_o.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/2078524051_ed4de415ef_o.jpg', NULL, 'jpg', 'image/jpeg', 301663, 1725101111, '800 x 536', 800, 536, 1, 1);
INSERT INTO `files` VALUES (2598, 1110, '2094750459_7e05256e05_o.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/2094750459_7e05256e05_o.jpg', NULL, 'jpg', 'image/jpeg', 147720, 1725101111, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2599, 1110, 'Bnowchristmas_1600x1200.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Bnowchristmas_1600x1200.jpg', NULL, 'jpg', 'image/jpeg', 417297, 1725101111, '1600 x 1200', 1600, 1200, 1, 1);
INSERT INTO `files` VALUES (2600, 1110, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, 'jpg', 'image/jpeg', 55655, 1725101111, '600 x 465', 600, 465, 1, 1);
INSERT INTO `files` VALUES (2601, 1110, 'Christmas_candles_by_SizkaS.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_candles_by_SizkaS.jpg', NULL, 'jpg', 'image/jpeg', 304103, 1725101111, '700 x 468', 700, 468, 1, 1);
INSERT INTO `files` VALUES (2602, 1110, 'Christmas_Greetings_2009.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Greetings_2009.jpg', NULL, 'jpg', 'image/jpeg', 353985, 1725101111, '800 x 536', 800, 536, 1, 1);
INSERT INTO `files` VALUES (2603, 1110, 'Christmas_Wallpaper_Snowman_Snow.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Wallpaper_Snowman_Snow.jpg', NULL, 'jpg', 'image/jpeg', 66529, 1725101111, '1600 x 1200', 1600, 1200, 1, 1);
INSERT INTO `files` VALUES (2605, 1110, 'christmas-2618263_1280.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2618263_1280.jpg', NULL, 'jpg', 'image/jpeg', 178822, 1725101112, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2606, 1110, 'christmas-2877141_1280.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2877141_1280.jpg', NULL, 'jpg', 'image/jpeg', 253363, 1725101112, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2607, 1110, 'Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', NULL, 'jpg', 'image/jpeg', 200233, 1725101112, '1600 x 1000', 1600, 1000, 1, 1);
INSERT INTO `files` VALUES (2608, 1110, 'christmas-night-magic-house.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-night-magic-house.jpg', NULL, 'jpg', 'image/jpeg', 141329, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2609, 1110, 'christmas-wallpapers-backgrounds.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers-backgrounds.jpg', NULL, 'jpg', 'image/jpeg', 142281, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2610, 1110, 'christmas-wallpapers.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers.jpg', NULL, 'jpg', 'image/jpeg', 79595, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2611, 1110, 'ChristmasCandlelightss1.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ChristmasCandlelightss1.jpg', NULL, 'jpg', 'image/jpeg', 76705, 1725101112, '800 x 600', 800, 600, 1, 1);
INSERT INTO `files` VALUES (2612, 1110, 'ehted_pky.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ehted_pky.jpg', NULL, 'jpg', 'image/jpeg', 49971, 1725101112, '960 x 772', 960, 772, 1, 1);
INSERT INTO `files` VALUES (2613, 1110, 'ekl_jolukaart_2015.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_jolukaart_2015.jpg', NULL, 'jpg', 'image/jpeg', 299740, 1725101112, '720 x 501', 720, 501, 1, 1);
INSERT INTO `files` VALUES (2615, 1110, 'ekl_joulukaart_2012.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2012.jpg', NULL, 'jpg', 'image/jpeg', 464952, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2618, 1110, 'ekl_joulukaart_2013.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2013.jpg', NULL, 'jpg', 'image/jpeg', 625113, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2621, 1110, 'ekl_joulukaart_2016.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2016.jpg', NULL, 'jpg', 'image/jpeg', 401329, 1725101112, '800 x 372', 800, 372, 1, 1);
INSERT INTO `files` VALUES (2622, 1110, 'ekl_joulukaart_2021.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2021.jpg', NULL, 'jpg', 'image/jpeg', 423608, 1725101112, '960 x 768', 960, 768, 1, 1);
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
INSERT INTO `files` VALUES (2712, 1, 'galerii67681.jpg', 'file', '/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1728053575, '800 x 533', NULL, NULL, 6, 0);
INSERT INTO `files` VALUES (2721, 1111, 'Tiit_töötoas_kurtide_majas.jpg', 'file', '/tester/Tiit_töötoas_kurtide_majas.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1741383520, '2500 x 1667', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2722, 1111, 'file60471593_d5a21f14.jpg', 'file', '/tester/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1741383520, '900 x 585', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2726, 1111, 'crop_DSC_5177_1.png', 'file', '/tester/crop_DSC_5177_1.png', NULL, 'png', 'image/png', 670209, 1741383520, '670 x 670', 670, 670, 1, 0);
INSERT INTO `files` VALUES (2727, 1111, 'crop_Tiit_Papp_töölaua_taga.png', 'file', '/tester/crop_Tiit_Papp_töölaua_taga.png', NULL, 'png', 'image/png', 111514, 1741383520, '288 x 289', 288, 289, 0, 0);
INSERT INTO `files` VALUES (2728, 1111, 'Tiit_Papp_2021.jpg', 'file', '/tester/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1741383520, '1200 x 1600', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2729, 1110, 'f_DSC01660.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726155638, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2730, 1110, 'file60471593_d5a21f14.jpg', 'file', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726155638, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2733, 1112, 'raamatud.jpg', 'file', '/kogukonna-galerii/uus-album/raamatud.jpg', NULL, 'jpg', 'image/jpeg', 17729, 1726157947, '228 x 270', 228, 270, 1, 1);
INSERT INTO `files` VALUES (2734, 1112, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/kogukonna-galerii/uus-album/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1726157947, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2735, 1112, 'r 175.jpg', 'file', '/kogukonna-galerii/uus-album/r 175.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1726157947, '3072 x 2304', 3072, 2304, 1, 1);
INSERT INTO `files` VALUES (2736, 1112, 'rukkilill.jpg', 'file', '/kogukonna-galerii/uus-album/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1726157948, '2288 x 1712', 2288, 1712, 1, 1);
INSERT INTO `files` VALUES (2737, 1112, 'seinakell.jpg', 'file', '/kogukonna-galerii/uus-album/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1726157948, '611 x 404', 611, 404, 1, 1);
INSERT INTO `files` VALUES (2738, 1112, 'vilinus reis 2263.jpg', 'file', '/kogukonna-galerii/uus-album/vilinus reis 2263.jpg', NULL, 'jpg', 'image/jpeg', 1138428, 1726157948, '1936 x 1288', 1936, 1288, 1, 1);
INSERT INTO `files` VALUES (2739, 1112, 'valentinikyynlas.JPG', 'file', '/kogukonna-galerii/uus-album/valentinikyynlas.JPG', NULL, 'jpg', 'image/jpeg', 2381402, 1726157948, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2740, 1111, 'logo-sliderUi.svg', 'file', '/tester/logo-sliderUi.svg', NULL, 'svg', 'image/svg+xml', 690, 1741383520, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2741, 1111, '02262060750a05.jpg', 'file', '/tester/02262060750a05.jpg', NULL, 'jpg', 'image/jpeg', 47856, 1741383520, '720 x 359', 720, 359, 0, 0);
INSERT INTO `files` VALUES (2743, 1038, 'langenud lehed pargis.jpg', 'file', '/galerii/blaaa/langenud lehed pargis.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1733048755, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2744, 1038, 'kolletanud_vahtralehed.jpg', 'file', '/galerii/blaaa/kolletanud_vahtralehed.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1733048755, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2745, 1038, 'f_DSC01660.jpg', 'file', '/galerii/blaaa/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1733048755, '2500 x 1667', 2500, 1667, 0, 1);
INSERT INTO `files` VALUES (2746, 1038, 'file60471593_d5a21f14.jpg', 'file', '/galerii/blaaa/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1733048755, '900 x 585', 900, 585, 0, 1);
INSERT INTO `files` VALUES (2747, 1038, 'rukkilill.jpg', 'file', '/galerii/blaaa/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1733048755, '2288 x 1712', 2288, 1712, 0, 1);
INSERT INTO `files` VALUES (2749, 1038, 'Tiit pildistab.jpg', 'file', '/galerii/blaaa/Tiit pildistab.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1733048755, '3072 x 2304', 3072, 2304, 0, 1);
INSERT INTO `files` VALUES (2751, 1078, 'sinine_teletorn.jpg', 'file', '/Uudised/Uudised 2024/sinine_teletorn.jpg', NULL, 'jpg', 'image/jpeg', 47345, 1727266275, '720 x 960', 720, 960, 0, 0);
INSERT INTO `files` VALUES (2753, 1078, 'crop_sinine_teletorn.png', 'file', '/Uudised/Uudised 2024/crop_sinine_teletorn.png', NULL, 'png', 'image/png', 104367, 1727267563, '637 x 295', 637, 295, 4, 0);
INSERT INTO `files` VALUES (2754, 1120, '18.A.Ojastuauhinjuhend2017.pdf', 'file', '/spordialad/kergejoustik/juhendid/18.A.Ojastuauhinjuhend2017.pdf', NULL, 'pdf', 'application/pdf', 201732, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2755, 1120, '2013 EKSL sisekj_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2756, 1120, '2013 EKSL sisekj_juhend1.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend1.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2757, 1120, 'Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', 'file', '/spordialad/kergejoustik/juhendid/Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', NULL, 'pdf', 'application/pdf', 471776, 1727867442, NULL, NULL, NULL, 3, 0);
INSERT INTO `files` VALUES (2758, 1120, 'EKSL MV juhend2013 kergej.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2013 kergej.pdf', NULL, 'pdf', 'application/pdf', 173725, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2759, 1120, 'EKSL MV juhend2014 kergej 310514.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2014 kergej 310514.pdf', NULL, 'pdf', 'application/pdf', 276908, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2760, 1120, 'EKSL MV KJ  juhend 2018.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV KJ  juhend 2018.pdf', NULL, 'pdf', 'application/pdf', 109552, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2761, 1120, 'EKSL_kergejõustiku_MV_juhend_2012.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL_kergejõustiku_MV_juhend_2012.pdf', NULL, 'pdf', 'application/pdf', 72509, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2762, 1120, 'EPKMVkergej_15062019_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/EPKMVkergej_15062019_juhend.pdf', NULL, 'pdf', 'application/pdf', 131368, 1727867443, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2763, 1120, 'epok-kergej-mv-juhend-2016_OapQlwhK.pdf', 'file', '/spordialad/kergejoustik/juhendid/epok-kergej-mv-juhend-2016_OapQlwhK.pdf', NULL, 'pdf', 'application/pdf', 519683, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2764, 1121, '2012_EKSL_MV_protkergej 260512.pdf', 'file', '/spordialad/kergejoustik/tulemused/2012_EKSL_MV_protkergej 260512.pdf', NULL, 'pdf', 'application/pdf', 276151, 1727867694, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2777, 1123, 'DSC_0008.JPG', 'file', '/kogukonna-galerii/tanugala-2024/DSC_0008.JPG', NULL, 'jpg', 'image/jpeg', 2432573, 1728566869, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2778, 1123, 'allkiri.png', 'file', '/kogukonna-galerii/tanugala-2024/allkiri.png', NULL, 'png', 'image/png', 156790, 1729464816, '300 x 300', 300, 300, 1, 1);
INSERT INTO `files` VALUES (2779, 1123, 'DSC_0084.JPG', 'file', '/kogukonna-galerii/tanugala-2024/DSC_0084.JPG', NULL, 'jpg', 'image/jpeg', 2817798, 1728566869, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2780, 1123, 'DSC_5197_1.jpg', 'file', '/kogukonna-galerii/tanugala-2024/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1728566870, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2781, 1123, 'DSC_5177_1.jpg', 'file', '/kogukonna-galerii/tanugala-2024/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1728566870, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2782, 1123, 'DSC_7550.jpg', 'file', '/kogukonna-galerii/tanugala-2024/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1729540924, '2953 x 1918', 2953, 1918, 1, 1);
INSERT INTO `files` VALUES (2784, 1123, 'seebimullid.jpg', 'file', '/kogukonna-galerii/tanugala-2024/seebimullid.jpg', NULL, 'jpg', 'image/jpeg', 493210, 1730293680, '1024 x 873', 1024, 873, 1, 1);
INSERT INTO `files` VALUES (2785, 1123, 'kuldnokk puuladvas.jpg', 'file', '/kogukonna-galerii/tanugala-2024/kuldnokk puuladvas.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1730293737, '1024 x 683', 1024, 683, 1, 1);
INSERT INTO `files` VALUES (2788, 1123, 'file60471593_d5a21f14.jpg', 'file', '/kogukonna-galerii/tanugala-2024/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1729791911, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2789, 1123, 'galerii67681.jpg', 'file', '/kogukonna-galerii/tanugala-2024/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1729791911, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (2790, 1105, 'DSC_5197_1.jpg', 'file', '/kogukonna-galerii/tiidu-album/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1729792962, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2791, 1105, 'DSC_5177_1.jpg', 'file', '/kogukonna-galerii/tiidu-album/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1729792963, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2794, 1, 'kurtide_liidu_maja_2013.jpg', 'file', '/kurtide_liidu_maja_2013.jpg', NULL, 'jpg', 'image/jpeg', 770166, 1729795447, '2048 x 1362', 2048, 1362, 4, 0);
INSERT INTO `files` VALUES (2795, 1126, 'Helle_Sass.png', 'file', '/Juhatus/2018-2023/Helle_Sass.png', NULL, 'png', 'image/png', 655482, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2796, 1126, 'Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/Janis_Golubenkov.png', NULL, 'png', 'image/png', 755152, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2797, 1126, 'Mati_Kartus.png', 'file', '/Juhatus/2018-2023/Mati_Kartus.png', NULL, 'png', 'image/png', 696185, 1730573593, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2798, 1126, 'Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/Riina_Kuusk.png', NULL, 'png', 'image/png', 711437, 1730567901, '568 x 850', 568, 850, 1, 0);
INSERT INTO `files` VALUES (2799, 1126, 'Sirle_Papp.png', 'file', '/Juhatus/2018-2023/Sirle_Papp.png', NULL, 'png', 'image/png', 642962, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2800, 1126, 'Tiit_Papp.png', 'file', '/Juhatus/2018-2023/Tiit_Papp.png', NULL, 'png', 'image/png', 619869, 1730567901, '568 x 850', 568, 850, 1, 0);
INSERT INTO `files` VALUES (2801, 1126, 'crop_Helle_Sass.png', 'file', '/Juhatus/2018-2023/crop_Helle_Sass.png', NULL, 'png', 'image/png', 387605, 1730567977, '558 x 559', 558, 559, 1, 0);
INSERT INTO `files` VALUES (2802, 1126, 'crop_Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/crop_Janis_Golubenkov.png', NULL, 'png', 'image/png', 340410, 1730568004, '550 x 549', 550, 549, 1, 0);
INSERT INTO `files` VALUES (2803, 1126, 'crop_Mati_Kartus.png', 'file', '/Juhatus/2018-2023/crop_Mati_Kartus.png', NULL, 'png', 'image/png', 360208, 1730573579, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2804, 1126, 'crop_Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/crop_Riina_Kuusk.png', NULL, 'png', 'image/png', 411006, 1730568055, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2805, 1126, 'crop_Sirle_Papp.png', 'file', '/Juhatus/2018-2023/crop_Sirle_Papp.png', NULL, 'png', 'image/png', 359834, 1730568080, '550 x 550', 550, 550, 3, 0);
INSERT INTO `files` VALUES (2806, 1126, 'crop_Tiit_Papp.png', 'file', '/Juhatus/2018-2023/crop_Tiit_Papp.png', NULL, 'png', 'image/png', 357732, 1730568106, '540 x 540', 540, 540, 2, 0);
INSERT INTO `files` VALUES (2807, 1111, 'crop_Tiit_Papp_2021.png', 'file', '/tester/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 1513373, 1741383520, '1102 x 1102', 1102, 1102, 1, 0);
INSERT INTO `files` VALUES (2808, 1131, 'DSC_5197_1.jpg', 'file', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1733009984, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2809, 1131, 'DSC_5177_1.jpg', 'file', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1733009984, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2810, 1131, 'f_DSC01660.jpg', 'file', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1733009985, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2811, 1131, 'file60471593_d5a21f14.jpg', 'file', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1733009985, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2813, 1138, '4686233863_aeb72a24df_b.jpg', 'file', '/kogukonna-galerii/esimene-uus-album/4686233863_aeb72a24df_b.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1735729785, '1024 x 683', 1024, 683, 1, 1);
INSERT INTO `files` VALUES (2814, 1138, 'DSC_0084.JPG', 'file', '/kogukonna-galerii/esimene-uus-album/DSC_0084.JPG', NULL, 'jpg', 'image/jpeg', 2817798, 1735729786, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2815, 1138, 'DSC_5177_1.jpg', 'file', '/kogukonna-galerii/esimene-uus-album/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1735729786, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2816, 1138, 'f_DSC01660.jpg', 'file', '/kogukonna-galerii/esimene-uus-album/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1735729786, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2817, 1143, 'Tiit_Papp_2021.jpg', 'file', '/kogukonna-galerii/testime-teist-uut-albumit/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1735802984, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2818, 1142, '6954421-christmas-lights.jpg', 'file', '/kurtide-galerii/testime-uut-albumit/6954421-christmas-lights.jpg', NULL, 'jpg', 'image/jpeg', 1583745, 1735825248, '2560 x 1600', 2560, 1600, 1, 1);
INSERT INTO `files` VALUES (2819, 1142, '403617_297643386939380_307791209_n.jpg', 'file', '/kurtide-galerii/testime-uut-albumit/403617_297643386939380_307791209_n.jpg', NULL, 'jpg', 'image/jpeg', 72044, 1735825248, '960 x 768', 960, 768, 1, 1);
INSERT INTO `files` VALUES (2820, 1142, 'Bnowchristmas_1600x1200.jpg', 'file', '/kurtide-galerii/testime-uut-albumit/Bnowchristmas_1600x1200.jpg', NULL, 'jpg', 'image/jpeg', 417297, 1735825248, '1600 x 1200', 1600, 1200, 1, 1);
INSERT INTO `files` VALUES (2821, 1142, 'christmas-2877141_1280.jpg', 'file', '/kurtide-galerii/testime-uut-albumit/christmas-2877141_1280.jpg', NULL, 'jpg', 'image/jpeg', 253363, 1735825249, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2822, 1142, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', 'file', '/kurtide-galerii/testime-uut-albumit/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, 'jpg', 'image/jpeg', 55655, 1735825249, '600 x 465', 600, 465, 1, 1);
INSERT INTO `files` VALUES (2823, 933, 'crop_Bnowchristmas_1600x1200.png', 'file', '/Avaleht/crop_Bnowchristmas_1600x1200.png', NULL, 'png', 'image/png', 1025633, 1735826540, '1104 x 1104', 1104, 1104, 0, 0);
INSERT INTO `files` VALUES (2824, 1145, 'Sinine ehe kuuseoksa peal.jpg', 'file', '/kurtide-galerii/kurtide-sundmus-2024/Sinine ehe kuuseoksa peal.jpg', NULL, 'jpg', 'image/jpeg', 188849, 1735911566, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2825, 1147, 'smartcrop.jpg', 'file', '/Uudised/Uudised 2025/smartcrop.jpg', NULL, 'jpg', 'image/jpeg', 202389, 1735927633, '1370 x 850', 1370, 850, 1, 0);
INSERT INTO `files` VALUES (2826, 1147, 'smartcrop_1.jpg', 'file', '/Uudised/Uudised 2025/smartcrop_1.jpg', NULL, 'jpg', 'image/jpeg', 27486, 1735927633, '1370 x 850', 1370, 850, 1, 0);
INSERT INTO `files` VALUES (2827, 1147, 'resize.jpg', 'file', '/Uudised/Uudised 2025/resize.jpg', NULL, 'jpg', 'image/jpeg', 1523453, 1735927762, '2849 x 1780', 2849, 1780, 1, 0);
INSERT INTO `files` VALUES (2829, 1149, 'Tundmatu.png', 'file', '/pildigalerii/teeme-uue-susteemiga-albumi/Tundmatu.png', NULL, 'png', 'image/png', 33299, 1736258936, '1000 x 1000', 1000, 1000, 1, 1);
INSERT INTO `files` VALUES (2830, 1111, 'EKSL Tänuõhtu 2021 esitlus.pdf', 'file', '/tester/EKSL Tänuõhtu 2021 esitlus.pdf', NULL, 'pdf', 'application/pdf', 2321958, 1741383520, NULL, NULL, NULL, 3, 0);
INSERT INTO `files` VALUES (2831, 1, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', 'file', '/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 1138176, 1736698074, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2832, 1, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.pdf', 'file', '/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018.pdf', NULL, 'pdf', 'application/pdf', 1335377, 1736698074, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2833, 1, 'Eesti viipekeele staatus ja kasutamine.pdf', 'file', '/Eesti viipekeele staatus ja kasutamine.pdf', NULL, 'pdf', 'application/pdf', 208005, 1736698074, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2834, 933, 'crop_kurtide_liidu_maja_2013.png', 'file', '/Avaleht/crop_kurtide_liidu_maja_2013.png', NULL, 'png', 'image/png', 2949766, 1741267826, '1362 x 1362', 1362, 1362, 0, 0);
INSERT INTO `files` VALUES (2835, 1, 'crop_kurtide_liidu_maja_2013.png', 'file', '/crop_kurtide_liidu_maja_2013.png', NULL, 'png', 'image/png', 2949766, 1741301285, '1362 x 1362', 1362, 1362, 0, 0);
INSERT INTO `files` VALUES (2840, 1, 'crop_Tiit töömõtetes.png', 'file', '/crop_Tiit töömõtetes.png', NULL, 'png', 'image/png', 1823341, 1741300838, '1140 x 1140', 1140, 1140, 0, 0);
INSERT INTO `files` VALUES (2841, 1, 'crop_Kolletanud_lehed_maas.png', 'file', '/crop_Kolletanud_lehed_maas.png', NULL, 'png', 'image/png', 323875, 1741301464, '460 x 459', 460, 459, 0, 0);
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
INSERT INTO `files` VALUES (2874, 1149, '4680076964_298f35a321_b.jpg', 'file', '/pildigalerii/teeme-uue-susteemiga-albumi/4680076964_298f35a321_b.jpg', NULL, 'jpg', 'image/jpeg', 493210, 1741452452, '1024 x 873', 1024, 873, 1, 1);
INSERT INTO `files` VALUES (2875, 1149, '4686233863_aeb72a24df_b.jpg', 'file', '/pildigalerii/teeme-uue-susteemiga-albumi/4686233863_aeb72a24df_b.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1741452452, '1024 x 683', 1024, 683, 1, 1);
INSERT INTO `files` VALUES (2876, 1149, 'DSC_5177_1.jpg', 'file', '/pildigalerii/teeme-uue-susteemiga-albumi/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1741452452, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2877, 1149, 'DSC_5197_1.jpg', 'file', '/pildigalerii/teeme-uue-susteemiga-albumi/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1741452452, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2878, 1, 'crop_kurtide_liidu_maja_2013-1.png', 'file', '/crop_kurtide_liidu_maja_2013-1.png', NULL, 'png', 'image/png', 2141824, 1741569040, '2030 x 490', 2030, 490, 1, 0);
INSERT INTO `files` VALUES (2879, 1021, 'Tiit_Papp_2021.jpg', 'file', '/Avaleht/test/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1742128666, '1200 x 1600', 1200, 1600, 0, 0);
INSERT INTO `files` VALUES (2892, 1021, 'DSC_5197_1.jpg', 'file', '/Avaleht/test/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1742129407, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (2893, 1021, 'DSC_5177_1.jpg', 'file', '/Avaleht/test/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1742129407, '1600 x 1064', 1600, 1064, 0, 0);
INSERT INTO `files` VALUES (2894, 1021, 'f_DSC01660.jpg', 'file', '/Avaleht/test/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1742129407, '2500 x 1667', 2500, 1667, 0, 0);
INSERT INTO `files` VALUES (2895, 1021, 'file60471593_d5a21f14.jpg', 'file', '/Avaleht/test/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1742129408, '900 x 585', 900, 585, 0, 0);
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
) ENGINE=InnoDB AUTO_INCREMENT=1161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of folders
-- ----------------------------
BEGIN;
INSERT INTO `folders` VALUES (1, NULL, '/', 'Repository', 'dir', 1741569040, 1, 0);
INSERT INTO `folders` VALUES (923, 1, '/Organisatsioon', 'Organisatsioon', 'dir', 1710165414, 1, 0);
INSERT INTO `folders` VALUES (929, 1, '/Varia', 'Varia', 'dir', 1725900551, 1, 0);
INSERT INTO `folders` VALUES (933, 1, '/Avaleht', 'Avaleht', 'dir', 1742128272, 1, 0);
INSERT INTO `folders` VALUES (935, 933, '/Avaleht/test 2', 'test 2', 'dir', 1741386545, 1, 0);
INSERT INTO `folders` VALUES (937, 1, '/Konventeerimine', 'Konventeerimine', 'dir', 1711542685, 1, 0);
INSERT INTO `folders` VALUES (989, 1, '/galerii', 'galerii', 'dir', 1708159047, 1, 1);
INSERT INTO `folders` VALUES (994, 989, '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses', 'Sügisene treeningulaager Jõulumäe Tervisekeskuses', 'dir', 1725102998, 1, 1);
INSERT INTO `folders` VALUES (996, 989, '/galerii/epk-kergejoustik-turil-14-05-2003', 'EPK kergejõustik Türil 14.05.2003', 'dir', 1709141019, 1, 1);
INSERT INTO `folders` VALUES (1008, 989, '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022', 'Lääne-Virumaa KÜ koosolek Rakveres 15.10.2022', 'dir', 1711368633, 1, 1);
INSERT INTO `folders` VALUES (1009, 989, '/galerii/arlese-album-28-02-2024', 'Arlese album 28.02.2024', 'dir', 1711372675, 1, 1);
INSERT INTO `folders` VALUES (1010, 1, '/Videod', 'Videod', 'dir', 1725971446, 0, 0);
INSERT INTO `folders` VALUES (1011, 1, '/Logod', 'Logod', 'dir', 1711051098, 1, 0);
INSERT INTO `folders` VALUES (1017, 989, '/galerii/uus-popurii', 'Uus popurii', 'dir', 1719518503, 1, 1);
INSERT INTO `folders` VALUES (1018, 989, '/galerii/uus-test', 'Uus test', 'dir', 1723032621, 1, 1);
INSERT INTO `folders` VALUES (1019, 929, '/Varia/Varia esimene kaust', 'Varia esimene kaust', 'dir', 1718979767, 1, 0);
INSERT INTO `folders` VALUES (1021, 933, '/Avaleht/test', 'test', 'dir', 1742129375, 1, 0);
INSERT INTO `folders` VALUES (1026, 1, '/crop-test', 'crop-test', 'dir', 1725148715, 1, 0);
INSERT INTO `folders` VALUES (1038, 989, '/galerii/blaaa', 'Blaaa', 'dir', 1733048755, 1, 1);
INSERT INTO `folders` VALUES (1077, 1, '/Uudised', 'Uudised', 'dir', 1724399541, 1, 0);
INSERT INTO `folders` VALUES (1078, 1077, '/Uudised/Uudised 2024', 'Uudised 2024', 'dir', 1727267563, 1, 0);
INSERT INTO `folders` VALUES (1101, 1, '/pildigalerii', 'Pildigalerii', 'dir', 1726157912, 1, 1);
INSERT INTO `folders` VALUES (1105, 1124, '/kogukonna-galerii/tiidu-album', 'Tiidu album', 'dir', 1724664207, 1, 1);
INSERT INTO `folders` VALUES (1106, 1124, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', 'dir', 1725026203, 1, 1);
INSERT INTO `folders` VALUES (1110, 1124, '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024', 'Spordiliidu juubeli tähistamine 31.12.2024', 'dir', 1725101014, 1, 1);
INSERT INTO `folders` VALUES (1111, 1, '/tester', 'TESTER', 'dir', 1741383520, 1, 0);
INSERT INTO `folders` VALUES (1112, 1124, '/kogukonna-galerii/uus-album', 'Uus album', 'dir', 1726157912, 1, 1);
INSERT INTO `folders` VALUES (1113, 1124, '/kogukonna-galerii/tiidu-album-1', 'Tiidu album', 'dir', 1726510522, 1, 1);
INSERT INTO `folders` VALUES (1114, 1, '/spordialad', 'spordialad', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1118, 1114, '/spordialad/kergejoustik', 'kergejoustik', 'dir', 1727867052, 1, 0);
INSERT INTO `folders` VALUES (1119, 1118, '/spordialad/kergejoustik/ajakavad', 'ajakavad', 'dir', 1727867077, 0, 0);
INSERT INTO `folders` VALUES (1120, 1118, '/spordialad/kergejoustik/juhendid', 'juhendid', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1121, 1118, '/spordialad/kergejoustik/tulemused', 'tulemused', 'dir', 1727867651, 1, 0);
INSERT INTO `folders` VALUES (1123, 1124, '/kogukonna-galerii/tanugala-2024', 'Tänugala 2024', 'dir', 1728565538, 1, 1);
INSERT INTO `folders` VALUES (1124, 1, '/kogukonna-galerii', 'Kogukonna galerii', 'dir', 1729540333, 1, 1);
INSERT INTO `folders` VALUES (1125, 1, '/Juhatus', 'Juhatus', 'dir', 1730567842, 1, 0);
INSERT INTO `folders` VALUES (1126, 1125, '/Juhatus/2018-2023', '2018-2023', 'dir', 1730568106, 1, 0);
INSERT INTO `folders` VALUES (1130, 1124, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022-1', 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', 'dir', 1733009668, 0, 1);
INSERT INTO `folders` VALUES (1131, 1124, '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-head-pildid', 'Head ööd ja jüriöö ja läänemaa head pildid', 'dir', 1733009924, 1, 1);
INSERT INTO `folders` VALUES (1137, 1, '/kurtide-galerii', 'Kurtide galerii', 'dir', 1733313881, 1, 1);
INSERT INTO `folders` VALUES (1138, 1124, '/kogukonna-galerii/esimene-uus-album', 'Esimene uus album', 'dir', 1735726315, 1, 1);
INSERT INTO `folders` VALUES (1140, 1, '/esimene-uus-album', 'Esimene uus album', 'dir', 1736259630, 1, 1);
INSERT INTO `folders` VALUES (1142, 1137, '/kurtide-galerii/testime-uut-albumit', 'Testime uut albumit', 'dir', 1735735045, 1, 1);
INSERT INTO `folders` VALUES (1143, 1124, '/kogukonna-galerii/testime-teist-uut-albumit', 'Testime teist uut albumit', 'dir', 1735735347, 1, 1);
INSERT INTO `folders` VALUES (1145, 1137, '/kurtide-galerii/kurtide-sundmus-2024', 'Kurtide sündmus 2024', 'dir', 1735901241, 1, 1);
INSERT INTO `folders` VALUES (1146, 1137, '/kurtide-galerii/testime-uut-albumit-1', 'Testime uut albumit', 'dir', 1735912613, 0, 1);
INSERT INTO `folders` VALUES (1147, 1077, '/Uudised/Uudised 2025', 'Uudised 2025', 'dir', 1735927671, 1, 0);
INSERT INTO `folders` VALUES (1149, 1101, '/pildigalerii/teeme-uue-susteemiga-albumi', 'Teeme uue süsteemiga albumi', 'dir', 1736258837, 1, 1);
INSERT INTO `folders` VALUES (1158, 1111, '/tester/vana-kaust', 'VANA KAUST', 'dir', 1741383520, 1, 0);
INSERT INTO `folders` VALUES (1159, 1, '/booo', 'BÖÖÖ', 'dir', 1741348660, 0, 0);
INSERT INTO `folders` VALUES (1160, 1111, '/tester/tiidu-kaust', 'TIIDU KAUST', 'dir', 1741383520, 1, 0);
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
  `is_activated` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `content_types_managament_id_idx` (`content_types_managament_id`) USING BTREE,
  KEY `is_activated_idx` (`is_activated`) USING BTREE,
  KEY `linked_id_idx` (`linked_id`) USING BTREE,
  KEY `grouped_id_idx` (`grouped_id`) USING BTREE,
  CONSTRAINT `content_types_managament_id_frontend_links_fk` FOREIGN KEY (`content_types_managament_id`) REFERENCES `content_types_management` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `is_activated_idx` FOREIGN KEY (`is_activated`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=437 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of frontend_links
-- ----------------------------
BEGIN;
INSERT INTO `frontend_links` VALUES (135, 247, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Kurtide kultuuri uudised', '/kultuuri-uudisedkurtide-kultuuri-uudised', 1);
INSERT INTO `frontend_links` VALUES (164, 299, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Eesti Kurtide Liidu põhikiri', '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', 1);
INSERT INTO `frontend_links` VALUES (204, 13, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'EKSL sisekergejõustiku võistlused', '/sundmuste-kalender/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (248, 329, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', '', '/pildigalerii', 1);
INSERT INTO `frontend_links` VALUES (252, 40, 336, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Tiidu album', '/sundmuste-kalender/2024/kurtide-paev-1', 1);
INSERT INTO `frontend_links` VALUES (255, 41, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/sundmuste-kalender/2024/naistepaev', 1);
INSERT INTO `frontend_links` VALUES (267, 46, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Esimese sündmuse loeng', '/sundmuste-kalender/2024/esimese-sundmuse-loeng', 1);
INSERT INTO `frontend_links` VALUES (268, 47, 336, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Sportlaste kokkutulek', '/sundmuste-kalender/2025/sportlaste-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (269, 336, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Sündmuste kalender', '/sundmuste-kalender/sundmuste-kalender', 1);
INSERT INTO `frontend_links` VALUES (272, 38, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide tore päev', '/sundmuste-kalender/esimene-kalender/2024/kurtide-tore-paev', 1);
INSERT INTO `frontend_links` VALUES (273, 39, 400, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/sundmuste-kalender/esimene-kalender/2024/naistepaev', 1);
INSERT INTO `frontend_links` VALUES (274, 40, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev-1', 1);
INSERT INTO `frontend_links` VALUES (275, 41, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/sundmuste-kalender/2024/kurtide-paev-1', 1);
INSERT INTO `frontend_links` VALUES (277, 337, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', NULL, '/sundmuste-kalender/spordikalender', 1);
INSERT INTO `frontend_links` VALUES (278, 1, 337, 1, 'StandardHomeController', 'StandardHomeController.tpl.php', 'Homepage', '', 1);
INSERT INTO `frontend_links` VALUES (279, 338, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 'Spordisündmuste kalender', '/sundmuste-kalender/spordisundmuste-kalender/spordisundmuste-kalender', 1);
INSERT INTO `frontend_links` VALUES (282, 4, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maavõistlused Tartus', '/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (286, 8, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kergejõustikuvõistlused', '/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (288, 10, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Blaaa', '/sundmuste-kalender/spordisundmuste-kalender/spordisundmuste-kalender/2024/blaaa', 1);
INSERT INTO `frontend_links` VALUES (289, 11, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/sundmuste-kalender/spordikalender/2024/eesti-ja-lati-kurtide-jalgpalli-sopruskohtumine', 1);
INSERT INTO `frontend_links` VALUES (290, 12, 337, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maahoki', '/sundmuste-kalender/spordikalender/2024/maahoki', 1);
INSERT INTO `frontend_links` VALUES (291, 13, 337, 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 'Jäähoki', '/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (292, 43, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Pensionäride kokkutulek', '/sundmuste-kalender/2024/pensionaride-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (293, 1, NULL, 1, 'CustomHomeController', 'CustomHomeController.tpl.php', '', '', 1);
INSERT INTO `frontend_links` VALUES (295, 342, NULL, 11, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordialad', '/sundmuste-kalender/spordialad', 1);
INSERT INTO `frontend_links` VALUES (299, 48, 400, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Eesti kurtide meistrivõistlused 2025 KABES', '/sundmuste-kalender/esimene-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes', 1);
INSERT INTO `frontend_links` VALUES (305, 348, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Kaasautor', '/kaasautorlus/vaatame-kaasautorlust', 1);
INSERT INTO `frontend_links` VALUES (310, 353, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Uurime aadressi muutumist', '/blaaa/uurime-aadressi-muutumist', 1);
INSERT INTO `frontend_links` VALUES (321, 363, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Eesti Kurtide Liidu pildigalerii', '/kogukonna-galerii/eesti-kurtide-liidu-pildigalerii', 1);
INSERT INTO `frontend_links` VALUES (327, 377, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Eesti Kurtide Liidu juhatus 2018 - 2023', '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023', 2);
INSERT INTO `frontend_links` VALUES (333, 379, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Spordi juhatus 2023 - 2028', '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028/spordi-juhatus-2023-2028', 2);
INSERT INTO `frontend_links` VALUES (334, 378, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Kultuuri juhatus 2023 - 2028', '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028/kultuuri-juhatus-2023-2028', 2);
INSERT INTO `frontend_links` VALUES (336, 378, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Kultuuri juhatus', '/kultuuri-juhatus', 1);
INSERT INTO `frontend_links` VALUES (337, 379, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Spordi juhatus', '/spordi-juhatus', 1);
INSERT INTO `frontend_links` VALUES (345, 392, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Liikmesühingud', '/liikmesuhingud', 1);
INSERT INTO `frontend_links` VALUES (346, 393, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordiseltsid', '/spordiseltsid', 1);
INSERT INTO `frontend_links` VALUES (347, 394, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Kultuuriseltsid', '/kultuuriseltsid', 1);
INSERT INTO `frontend_links` VALUES (348, 396, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Eesti Kurtide Spordiliidu liikmesseltsid', '/organisatsioon/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid', 1);
INSERT INTO `frontend_links` VALUES (352, 52, 363, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 1);
INSERT INTO `frontend_links` VALUES (353, 53, 21, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Head ööd ja jüriöö ja läänemaa head pildid', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid', 1);
INSERT INTO `frontend_links` VALUES (356, 45, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'qwertyhujkl', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024', 1);
INSERT INTO `frontend_links` VALUES (357, 400, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Esimene kalender', '/sundmuste-kalender/esimene-kalender', 1);
INSERT INTO `frontend_links` VALUES (358, 46, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Esimese sündmuse loeng', '/tuhi/esimene-kalender/sundmuste-kalender/sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes', 1);
INSERT INTO `frontend_links` VALUES (363, 420, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Eesti Kurtide kogukonna galerii', '/kurtide-galerii/eesti-kurtide-kogukonna-galerii', 1);
INSERT INTO `frontend_links` VALUES (373, 433, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Uued liikmed', '/uued-liikmed', 1);
INSERT INTO `frontend_links` VALUES (374, 439, NULL, 14, 'StandardVideosController', 'StandardVideosController.tpl.php', 'Videote list', '/parent/videod/videote-list', 1);
INSERT INTO `frontend_links` VALUES (376, 442, NULL, 16, 'StandardLinksController', 'StandardLinksController.tpl.php', 'Partnerite lingid', '/lingidpartnerite-lingid', 1);
INSERT INTO `frontend_links` VALUES (377, 443, NULL, 16, 'StandardLinksController', 'StandardLinksController.tpl.php', 'Teised lingid', '/teised-lingid', 1);
INSERT INTO `frontend_links` VALUES (383, 451, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', 'Rekordid', '/rekordid', 1);
INSERT INTO `frontend_links` VALUES (384, 452, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', 'Rekordid', '/statistika/rekordid', 1);
INSERT INTO `frontend_links` VALUES (385, 453, NULL, 16, 'StandardStatisticsController', 'StandardStatisticsController.tpl.php', NULL, '/statistika/edetabelid', 1);
INSERT INTO `frontend_links` VALUES (387, 456, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Statistika avapauk', '/statistikastatistika-avapauk', 2);
INSERT INTO `frontend_links` VALUES (388, 550, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Organisatsiooni kontaktandmed', '/organisatsioon/organisatsiooni-kontaktandmed', 1);
INSERT INTO `frontend_links` VALUES (389, 50, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', '', '/uudised/poliitika-uudised/blaaaa/', 1);
INSERT INTO `frontend_links` VALUES (390, 283, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Spordiuudised', '/uudised/spordiuudised', 1);
INSERT INTO `frontend_links` VALUES (394, 608, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Tänitame kindlati edasi', '/parent/tanitame-kindlati-edasi', 2);
INSERT INTO `frontend_links` VALUES (399, 96, 283, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Holger Peel: Eesti treenerikoolitus ei ela parimaid päevi', '/uudised/spordiuudised/holger-peel-eesti-treenerikoolitus-ei-ela-parimaid-paevi', 1);
INSERT INTO `frontend_links` VALUES (400, 55, 363, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Esimene uus album', '/kogukonna-galerii/esimene-uus-album', 1);
INSERT INTO `frontend_links` VALUES (402, 613, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Esimene uus album', '/esimene-uus-album', 1);
INSERT INTO `frontend_links` VALUES (404, 57, 420, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Testime uut albumit', '/kurtide-galerii/testime-uut-albumit', 1);
INSERT INTO `frontend_links` VALUES (405, 58, 363, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Testime teist uut albumit', '/kogukonna-galerii/testime-teist-uut-albumit', 1);
INSERT INTO `frontend_links` VALUES (407, 60, 420, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Kurtide sündmus 2024', '/kurtide-galerii/kurtide-sundmus-2024', 1);
INSERT INTO `frontend_links` VALUES (408, 61, 420, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Testime uut albumit', '/kurtide-galerii/testime-uut-albumit', 1);
INSERT INTO `frontend_links` VALUES (409, 97, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Politico: Vene gaasitransiidi lõppemine tekitab regioonis pingeid', '/uudised/poliitika-uudised/blaaaa//politico-vene-gaasitransiidi-loppemine-tekitab-regioonis-pingeid', 1);
INSERT INTO `frontend_links` VALUES (410, 98, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Saksamaa päikeseenergia sektor vaevleb pankrotilaine küüsis', '/uudised/poliitika-uudised/blaaaa//saksamaa-paikeseenergia-sektor-vaevleb-pankrotilaine-kuusis', 1);
INSERT INTO `frontend_links` VALUES (411, 99, 283, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Aigro jäi Innsbruckis esimesena lõppvõistluse ukse taha', '/uudised/spordiuudised/aigro-jai-innsbruckis-esimesena-loppvoistluse-ukse-taha', 1);
INSERT INTO `frontend_links` VALUES (412, 100, 283, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Männama ja Lepik alustasid Dakari rallit: auto toimis suurepäraselt', '/uudised/spordiuudised/mannama-ja-lepik-alustasid-dakari-rallit-auto-toimis-suureparaselt', 1);
INSERT INTO `frontend_links` VALUES (413, 47, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Sportlaste kokkutulek', '/tuhi/esimene-kalender/sundmuste-kalender/sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes', 1);
INSERT INTO `frontend_links` VALUES (414, 101, 247, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Marja Unt: raamatuaastal rõõmustame, aga peame rääkima ka murekohtadest', '/kultuuri-uudised/marja-unt-raamatuaastal-roomustame-aga-peame-raakima-ka-murekohtadest', 1);
INSERT INTO `frontend_links` VALUES (415, 63, 329, 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 'Teeme uue süsteemiga albumi', '/pildigalerii/teeme-uue-susteemiga-albumi', 1);
INSERT INTO `frontend_links` VALUES (417, 48, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti kurtide meistrivõistlused 2025 KABES', '/tuhi/esimene-kalender/sundmuste-kalender/sundmuste-kalender/2025/eesti-kurtide-meistrivoistlused-2025-kabes', 1);
INSERT INTO `frontend_links` VALUES (418, 22, 338, 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 'Bobisõit 2025', '/sundmuste-kalender/spordisundmuste-kalender/2025/bobisoit-2025', 1);
INSERT INTO `frontend_links` VALUES (424, 620, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Uus suunamine põhikirjale', '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', 1);
INSERT INTO `frontend_links` VALUES (425, 621, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Uudised', '/kultuuri-uudisedkurtide-kultuuri-uudised', 1);
INSERT INTO `frontend_links` VALUES (427, 623, NULL, 16, 'StandardLinksController', 'StandardLinksController.tpl.php', 'Statistika lingid', '/statistika-lingid', 1);
INSERT INTO `frontend_links` VALUES (428, 624, NULL, 16, 'StandardLinksController', 'StandardLinksController.tpl.php', 'Statistika uued lingid', '/statistika-uued-lingid', 1);
INSERT INTO `frontend_links` VALUES (436, 633, NULL, 17, 'StandardAchievementsController', 'StandardAchievementsControllertpl.php', 'Saavutused', '/statistika/saavutused', 1);
COMMIT;

-- ----------------------------
-- Table structure for frontend_options
-- ----------------------------
DROP TABLE IF EXISTS `frontend_options`;
CREATE TABLE `frontend_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `frontend_template_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_types_management_id` int unsigned NOT NULL,
  `class_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontend_template_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of frontend_options
-- ----------------------------
BEGIN;
INSERT INTO `frontend_options` VALUES (1, 'Home (standard)', 1, 'StandardHomeController', 'StandardHomeController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (2, 'Article detail (standard)', 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (3, 'News list (standard)', 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (4, 'News detail (standard)', 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (5, 'Gallery list (standard)', 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (6, 'Gallery detail (standard)', 6, 'StandardGalleryDetailController', 'StandardGalleryDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (7, 'Events calendar list (standard)', 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (8, 'Events calendar detail (standard)', 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (9, 'Sports calendar list (standard)', 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (10, 'Sports calendar detail (standard)', 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (11, 'Sports areas detail (standard)', 11, 'SportsAreasController', 'SportsAreasController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (12, 'Board detail (standard)', 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (13, 'Members detail (standard)', 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (14, 'Videos detail (standard)', 14, 'StandardVideosController', 'StandardVideosController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (15, 'Records detail (standard)', 15, 'StandardRecordsController', 'StandardRecordsController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (16, 'Rankings detail (standard)', 16, 'StandardRankingsController', 'StandardRankingsController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (17, 'Achievements detail (standard)', 17, 'StandardAchievementsController', 'StandardAchievementsControllertpl.php', 1);
INSERT INTO `frontend_options` VALUES (18, 'Links detail (standard)', 18, 'StandardLinksController', 'StandardLinksController.tpl.php', 1);
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
-- Table structure for galleries
-- ----------------------------
DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int unsigned DEFAULT NULL,
  `list_id` int unsigned DEFAULT NULL,
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
  KEY `list_id_idx` (`list_id`) USING BTREE,
  KEY `id` (`id`,`status`),
  CONSTRAINT `list_id_galleries_ibfk` FOREIGN KEY (`list_id`) REFERENCES `list_of_galleries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `status_galleries_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of galleries
-- ----------------------------
BEGIN;
INSERT INTO `galleries` VALUES (79, 38, 13, 996, 1267, 'jõulus talu.jpg', '/galerii/epk-kergejoustik-turil-14-05-2003/jõulus talu.jpg', 'Sume jõulutalu', 'John Doe', 1, '2024-02-16 14:02:03', '2024-02-28 19:23:39');
INSERT INTO `galleries` VALUES (80, 38, 13, 996, 1268, 'Bnowchristmas.jpg', '/galerii/epk-kergejoustik-turil-14-05-2003/Bnowchristmas.jpg', 'Jõuluehted', NULL, 1, '2024-02-16 14:02:04', '2024-02-28 19:23:39');
INSERT INTO `galleries` VALUES (81, 38, 13, 996, 1269, 'christmas-wallpapers.jpg', '/galerii/epk-kergejoustik-turil-14-05-2003/christmas-wallpapers.jpg', NULL, NULL, 1, '2024-02-16 14:02:04', '2024-02-28 19:23:39');
INSERT INTO `galleries` VALUES (82, 38, 13, 996, 1270, 'ekl_joulukaart_2013.jpg', '/galerii/epk-kergejoustik-turil-14-05-2003/ekl_joulukaart_2013.jpg', NULL, NULL, 1, '2024-02-16 14:02:04', '2024-02-28 19:23:39');
INSERT INTO `galleries` VALUES (124, 38, 11, 994, 1316, 'allkiri.png', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/allkiri.png', 'Allkirja näidis', 'ERR | Tiit Papp', 1, '2024-02-17 10:24:11', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (125, 38, 11, 994, 1317, '4686233863_aeb72a24df_b.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/4686233863_aeb72a24df_b.jpg', NULL, 'FOTO: Kairit Olenko', 1, '2024-02-17 10:24:11', '2024-08-31 14:17:09');
INSERT INTO `galleries` VALUES (126, 38, 11, 994, 1318, 'DSC_0084.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_0084.jpg', NULL, 'ERR | Tiit Papp', 1, '2024-02-17 10:24:12', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (127, 38, 11, 994, 1319, 'DSC_5177_1.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_5177_1.jpg', NULL, NULL, 1, '2024-02-17 10:24:12', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (128, 38, 11, 994, 1320, 'DSC_5197_1.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/DSC_5197_1.jpg', NULL, 'ERR | Tiit Papp', 1, '2024-02-17 10:24:12', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (129, 38, 11, 994, 1321, 'file60471593_d5a21f14.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-02-17 10:24:12', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (130, 38, 11, 994, 1322, 'f_DSC01660.jpg', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses/f_DSC01660.jpg', NULL, NULL, 1, '2024-02-17 10:24:13', '2024-08-31 14:16:38');
INSERT INTO `galleries` VALUES (153, 38, 25, 1008, 1345, '310625658_5771591356213069_6130322049604942068_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:15', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (154, 38, 25, 1008, 1346, '310596090_1482278652270764_6161734453730055725_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:15', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (155, 38, 25, 1008, 1347, '310651429_413903317415234_1877068238628190472_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:15', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (156, 38, 25, 1008, 1348, '310986468_785287795913568_6096172368795184477_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:16', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (157, 38, 25, 1008, 1349, '311163895_800320941296129_7328794715150918241_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:16', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (158, 38, 25, 1008, 1350, '311271898_5500936233356667_4481537757649627936_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:16', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (159, 38, 25, 1008, 1351, '311451979_627793208998847_3710757790573382164_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:16', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (160, 38, 25, 1008, 1352, '311464218_606307097952705_2986433564733245675_n.jpeg', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, NULL, 1, '2024-02-28 20:55:17', '2024-03-25 14:10:33');
INSERT INTO `galleries` VALUES (174, 38, 26, 1009, 1366, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', '/galerii/arlese-album-28-02-2024/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, NULL, 1, '2024-03-01 00:25:42', '2024-03-25 15:17:55');
INSERT INTO `galleries` VALUES (175, 38, 26, 1009, 1367, 'rukkilill.jpg', '/galerii/arlese-album-28-02-2024/rukkilill.jpg', NULL, NULL, 1, '2024-03-01 00:25:42', '2024-03-25 15:17:55');
INSERT INTO `galleries` VALUES (176, 38, 26, 1009, 1368, 'r 175.jpg', '/galerii/arlese-album-28-02-2024/r 175.jpg', NULL, NULL, 1, '2024-03-01 00:25:43', '2024-03-25 15:17:55');
INSERT INTO `galleries` VALUES (177, 38, 26, 1009, 1369, 'rahvusvaheline_kurtide.jpg', '/galerii/arlese-album-28-02-2024/rahvusvaheline_kurtide.jpg', NULL, NULL, 1, '2024-03-01 00:25:43', '2024-03-25 15:17:55');
INSERT INTO `galleries` VALUES (178, 38, 26, 1009, 1370, 'sirlu.jpg', '/galerii/arlese-album-28-02-2024/sirlu.jpg', NULL, NULL, 1, '2024-03-01 00:25:43', '2024-03-25 15:17:55');
INSERT INTO `galleries` VALUES (179, 38, 26, 1009, 1371, 'seinakell.jpg', '/galerii/arlese-album-28-02-2024/seinakell.jpg', NULL, NULL, 1, '2024-03-01 00:25:44', '2024-03-25 15:17:56');
INSERT INTO `galleries` VALUES (180, 38, 26, 1009, 1459, 'ekl_joulukaart_2021.jpg', '/galerii/arlese-album-28-02-2024/ekl_joulukaart_2021.jpg', NULL, NULL, 1, '2024-03-04 17:56:10', '2024-03-25 15:17:56');
INSERT INTO `galleries` VALUES (181, 38, 26, 1009, 1460, 'ekl_joulukaart_2016.jpg', '/galerii/arlese-album-28-02-2024/ekl_joulukaart_2016.jpg', NULL, NULL, 1, '2024-03-04 17:56:10', '2024-03-25 15:17:56');
INSERT INTO `galleries` VALUES (182, 38, 26, 1009, 1461, 'joulukaart_2010.jpg', '/galerii/arlese-album-28-02-2024/joulukaart_2010.jpg', NULL, NULL, 1, '2024-03-04 17:56:10', '2024-03-25 15:17:56');
INSERT INTO `galleries` VALUES (231, 38, 32, 1017, 1577, 'DSC_5197_1.jpg', '/galerii/uus-popurii/DSC_5197_1.jpg', '', 'FOTO: Tiit Papp', 1, '2024-03-26 00:57:28', '2024-06-27 23:01:43');
INSERT INTO `galleries` VALUES (232, 38, 32, 1017, 1578, 'DSC_5177_1.jpg', '/galerii/uus-popurii/DSC_5177_1.jpg', '', '', 1, '2024-03-26 00:57:29', '2024-06-27 23:01:43');
INSERT INTO `galleries` VALUES (233, 38, 32, 1017, 1579, 'f_DSC01660.jpg', '/galerii/uus-popurii/f_DSC01660.jpg', NULL, 'FOTO: Tiit Papp', 1, '2024-03-26 00:57:29', '2024-06-27 23:01:43');
INSERT INTO `galleries` VALUES (234, 38, 32, 1017, 1580, 'file60471593_d5a21f14.jpg', '/galerii/uus-popurii/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-03-26 00:57:29', '2024-06-27 23:01:43');
INSERT INTO `galleries` VALUES (236, 38, 33, 1018, 1582, 'P7140053.jpg', '/galerii/uus-test/P7140053.jpg', '', '', 1, '2024-03-27 11:06:26', '2024-08-07 15:10:21');
INSERT INTO `galleries` VALUES (237, 38, 33, 1018, 1583, 'P7140059.jpg', '/galerii/uus-test/P7140059.jpg', '', 'FOTO: Sergei Matvijenko', 1, '2024-03-27 11:06:26', '2024-08-07 15:10:21');
INSERT INTO `galleries` VALUES (238, 38, 33, 1018, 1584, 'P7140060.JPG', '/galerii/uus-test/P7140060.JPG', NULL, NULL, 1, '2024-03-27 11:06:27', '2024-08-07 15:10:21');
INSERT INTO `galleries` VALUES (239, 38, 33, 1018, 1585, 'paike_vastu_metsa.jpg', '/galerii/uus-test/paike_vastu_metsa.jpg', NULL, NULL, 1, '2024-03-27 11:06:27', '2024-08-07 15:10:21');
INSERT INTO `galleries` VALUES (240, 38, 33, 1018, 1596, 'IMG_1172.JPG', '/galerii/uus-test/IMG_1172.JPG', NULL, NULL, 1, '2024-04-03 09:00:02', '2024-08-07 15:10:21');
INSERT INTO `galleries` VALUES (245, 38, 34, 1038, 2743, 'langenud lehed pargis.jpg', '/galerii/blaaa/langenud lehed pargis.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-12-01 12:25:55');
INSERT INTO `galleries` VALUES (246, 38, 34, 1038, 2744, 'kolletanud_vahtralehed.jpg', '/galerii/blaaa/kolletanud_vahtralehed.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-12-01 12:25:55');
INSERT INTO `galleries` VALUES (247, 38, 34, 1038, 2745, 'f_DSC01660.jpg', '/galerii/blaaa/f_DSC01660.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-12-01 12:25:55');
INSERT INTO `galleries` VALUES (248, 38, 34, 1038, 2746, 'file60471593_d5a21f14.jpg', '/galerii/blaaa/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-12-01 12:25:55');
INSERT INTO `galleries` VALUES (249, 38, 34, 1038, 2747, 'rukkilill.jpg', '/galerii/blaaa/rukkilill.jpg', NULL, NULL, 1, '2024-09-15 20:26:23', '2024-12-01 12:25:55');
INSERT INTO `galleries` VALUES (251, 38, 34, 1038, 2749, 'Tiit pildistab.jpg', '/galerii/blaaa/Tiit pildistab.jpg', NULL, NULL, 1, '2024-09-15 20:26:23', '2024-12-01 12:25:55');
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
  `photo_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of gallery_list
-- ----------------------------
BEGIN;
INSERT INTO `gallery_list` VALUES (40, 363, 21, 'Kogukonna galerii', 1124, 1105, 'Tiidu album', '', '', '/kogukonna-galerii/tiidu-album', '/kogukonna-galerii/tiidu-album', 2, 'Alex Smith', 1, '2024-08-26 12:23:27', '2025-01-07 15:32:12');
INSERT INTO `gallery_list` VALUES (41, 363, 21, 'Kogukonna galerii', 1124, 1106, 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', NULL, NULL, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 2, 'Alex Smith', 2, '2024-08-30 16:56:43', '2024-11-28 00:08:05');
INSERT INTO `gallery_list` VALUES (45, 363, 21, 'Kogukonna galerii', 1124, 1110, 'Spordiliidu juubeli tähistamine 31.12.2024', NULL, NULL, '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024', '/kogukonna-galerii/spordiliidu-juubeli-tahistamine-31-12-2024', 2, 'Alex Smith', 1, '2024-08-31 13:43:34', '2024-12-27 16:07:58');
INSERT INTO `gallery_list` VALUES (46, 363, 21, 'Kogukonna galerii', 1124, 1112, 'Uus album', NULL, NULL, '/kogukonna-galerii/uus-album', '/kogukonna-galerii/uus-album', 2, 'Alex Smith', 1, '2024-09-12 19:18:32', '2024-12-27 16:07:58');
INSERT INTO `gallery_list` VALUES (47, 363, 21, 'Kogukonna galerii', 1124, 1113, 'Tiidu album', '', '', '/kogukonna-galerii/tiidu-album-1', '/kogukonna-galerii/tiidu-album', 2, 'Alex Smith', 2, '2024-09-16 21:15:22', '2024-12-27 16:07:58');
INSERT INTO `gallery_list` VALUES (48, 363, 21, 'Kogukonna galerii', 1124, 1123, 'Tänugala 2024', '', '', '/kogukonna-galerii/tanugala-2024', '/kogukonna-galerii/tanugala-2024', 3, 'Samantha Jones', 1, '2024-10-10 16:05:38', '2024-12-04 14:06:29');
INSERT INTO `gallery_list` VALUES (52, 363, 21, 'Kogukonna galerii', 1124, 1130, 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', NULL, NULL, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022-1', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 3, 'Samantha Jones', 2, '2024-12-01 01:34:28', '2024-12-27 16:07:58');
INSERT INTO `gallery_list` VALUES (53, 363, 21, 'Kogukonna galerii', 1124, 1131, 'Head ööd ja jüriöö ja läänemaa pildid', NULL, NULL, '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-head-pildid', '/kogukonna-galerii/head-ood-ja-jurioo-ja-laanemaa-pildid', 3, 'Samantha Jones', 2, '2024-12-01 01:38:44', '2025-01-01 11:58:02');
INSERT INTO `gallery_list` VALUES (55, 363, 21, 'Kogukonna galerii', 1124, 1138, 'Esimene uus album', NULL, NULL, '/kogukonna-galerii/esimene-uus-album', '/kogukonna-galerii/esimene-uus-album', 3, 'Samantha Jones', 1, '2025-01-01 12:11:55', '2025-03-04 21:13:19');
INSERT INTO `gallery_list` VALUES (57, 420, 26, 'Kurtide galerii', 1137, 1142, 'Testime uut albumit', NULL, NULL, '/kurtide-galerii/testime-uut-albumit', '/kurtide-galerii/testime-uut-albumit', 3, 'Samantha Jones', 2, '2025-01-01 14:37:25', '2025-01-03 15:44:09');
INSERT INTO `gallery_list` VALUES (58, 363, 21, 'Kogukonna galerii', 1124, 1143, 'Testime teist uut albumit', NULL, NULL, '/kogukonna-galerii/testime-teist-uut-albumit', '/kogukonna-galerii/testime-teist-uut-albumit', 3, 'Samantha Jones', 2, '2025-01-01 14:42:27', '2025-01-07 15:30:38');
INSERT INTO `gallery_list` VALUES (60, 420, 26, 'Kurtide galerii', 1137, 1145, 'Kurtide sündmus 2024', NULL, NULL, '/kurtide-galerii/kurtide-sundmus-2024', '/kurtide-galerii/kurtide-sundmus-2024', 3, 'Samantha Jones', 1, '2025-01-03 12:47:21', '2025-01-03 15:44:09');
INSERT INTO `gallery_list` VALUES (61, 420, 26, 'Kurtide galerii', 1137, 1146, 'Testime uut albumit', NULL, NULL, '/kurtide-galerii/testime-uut-albumit-1', '/kurtide-galerii/testime-uut-albumit', 3, 'Samantha Jones', 2, '2025-01-03 15:56:53', '2025-01-07 15:44:54');
INSERT INTO `gallery_list` VALUES (63, 329, 17, 'Pildigalerii', 1101, 1149, 'Teeme uue süsteemiga albumi', NULL, NULL, '/pildigalerii/teeme-uue-susteemiga-albumi', '/pildigalerii/teeme-uue-susteemiga-albumi', 3, 'Samantha Jones', 1, '2025-01-07 16:07:17', '2025-03-08 16:47:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of gallery_settings
-- ----------------------------
BEGIN;
INSERT INTO `gallery_settings` VALUES (17, 329, 1, 1, 1101, 'Pildigalerii', '', '/pildigalerii', '2024-08-25 14:13:22', '2025-03-08 16:47:35', 1);
INSERT INTO `gallery_settings` VALUES (21, 363, 1, 1, 1124, 'Kogukonna galerii', 'Eesti Kurtide Liidu pildigalerii', '/kogukonna-galerii', '2024-10-21 20:56:28', '2025-01-07 15:06:04', 1);
INSERT INTO `gallery_settings` VALUES (26, 420, 1, 1, 1137, 'Kurtide galerii', 'Eesti Kurtide kogukonna galerii', '/kurtide-galerii', '2024-12-04 14:04:41', '2025-01-07 16:08:29', 1);
INSERT INTO `gallery_settings` VALUES (28, 613, 1, 1, 1140, 'Esimene uus album', NULL, '/esimene-uus-album', '2025-01-01 12:27:37', '2025-01-07 16:17:29', 0);
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
INSERT INTO `gallerylist_editors_assn` VALUES (40, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (40, 3);
INSERT INTO `gallerylist_editors_assn` VALUES (41, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (41, 3);
INSERT INTO `gallerylist_editors_assn` VALUES (45, 3);
INSERT INTO `gallerylist_editors_assn` VALUES (46, 3);
INSERT INTO `gallerylist_editors_assn` VALUES (47, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (47, 3);
INSERT INTO `gallerylist_editors_assn` VALUES (48, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (48, 4);
INSERT INTO `gallerylist_editors_assn` VALUES (53, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (55, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (57, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (58, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (60, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (61, 1);
INSERT INTO `gallerylist_editors_assn` VALUES (63, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of genders
-- ----------------------------
BEGIN;
INSERT INTO `genders` VALUES (1, 'Mehed', 1, 'John Doe', '2025-02-01 00:00:00', '2025-02-03 00:27:52', 1, 2);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links
-- ----------------------------
BEGIN;
INSERT INTO `links` VALUES (1, 442, 1, 'Lingid', 'Eesti Kurtide Liit', 'https://www.ead.ee', NULL, NULL, NULL, 7, '2024-12-15 21:22:27', '2025-03-20 13:17:11', 1);
INSERT INTO `links` VALUES (2, 442, 1, 'Lingid', 'Tallinna ja Harjumaa Kurtide Ühing', 'https://www.thky.ee', NULL, 1, 'Liikmesühingud', 9, '2024-12-15 14:25:18', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (3, 442, 1, 'Lingid', 'Eesti Puuetega Inimeste Koda', 'https://www.epikoda.ee', NULL, 2, 'Koostööpartnerid', 8, '2024-12-15 16:55:50', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (4, NULL, 4, 'Statistika uued lingid', 'Parimad sportlased ja seltsid', NULL, 2830, NULL, NULL, 6, '2025-01-11 23:11:19', '2025-01-16 15:56:56', 1);
INSERT INTO `links` VALUES (7, NULL, 4, 'Statistika uued lingid', 'Uus esitlus', NULL, 2755, NULL, NULL, 5, '2025-01-12 16:58:33', '2025-01-16 15:56:56', 1);
INSERT INTO `links` VALUES (9, NULL, 4, 'Statistika uued lingid', 'Põnev ettekanne', NULL, 2833, NULL, NULL, 4, '2025-01-12 20:53:42', '2025-01-16 15:56:56', 1);
INSERT INTO `links` VALUES (10, NULL, 1, 'Lingid', 'Testime uut aadressi', 'https://www.neti.ee', NULL, 2, 'Koostööpartnerid', 1, '2025-03-20 14:04:42', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (11, NULL, 3, 'Statistika lingid', 'Blaaaa', NULL, 2757, NULL, NULL, 3, '2025-03-20 18:58:56', '2025-03-20 18:59:02', 2);
INSERT INTO `links` VALUES (12, NULL, 1, 'Lingid', 'Uus aadress', 'https://www.neti.ee', NULL, NULL, NULL, 2, '2025-01-25 20:55:33', '2025-03-20 13:14:52', 1);
INSERT INTO `links` VALUES (13, NULL, 1, 'Lingid', 'Uus testi aadress', 'https://www.talkur.ee', NULL, 2, 'Koostööpartnerid', 0, '2025-03-20 14:05:31', '2025-03-20 14:05:28', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of links_category
-- ----------------------------
BEGIN;
INSERT INTO `links_category` VALUES (1, 'Liikmesühingud', 1, '2024-12-15 18:24:36', NULL);
INSERT INTO `links_category` VALUES (2, 'Koostööpartnerid', 1, '2024-12-15 18:24:49', '2024-12-15 18:31:56');
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
  `menu_content_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_type` int unsigned DEFAULT NULL,
  `link_type_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `links_settings` VALUES (1, 'Lingid', 'Partnerite lingid', 1, 1, 442, '/lingid', '2024-12-14 11:48:54', '2025-03-20 18:56:37', 1, 'John Doe', 1, 'Destination', 1);
INSERT INTO `links_settings` VALUES (2, 'Teised lingid', NULL, 1, 1, 443, '/teised-lingid', '2024-12-15 19:45:43', '2025-03-20 18:56:37', 1, 'John Doe', 1, 'Destination', 0);
INSERT INTO `links_settings` VALUES (3, 'Statistika lingid', NULL, 1, 1, 623, '/statistika-lingid', '2025-01-11 23:08:21', '2025-03-20 18:59:08', 1, 'John Doe', 2, 'Attachment', 1);
INSERT INTO `links_settings` VALUES (4, 'Statistika uued lingid', NULL, 1, 1, 624, '/statistika-uued-lingid', '2025-01-13 15:06:09', '2025-03-20 18:56:51', 1, 'John Doe', 2, 'Attachment', 1);
COMMIT;

-- ----------------------------
-- Table structure for list_of_galleries
-- ----------------------------
DROP TABLE IF EXISTS `list_of_galleries`;
CREATE TABLE `list_of_galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int unsigned DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `title_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `list_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `album_id_idx` (`album_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  CONSTRAINT `album_id_albums_ibfk` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `status_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of list_of_galleries
-- ----------------------------
BEGIN;
INSERT INTO `list_of_galleries` VALUES (11, 38, 994, 'Sügisene treeningulaager Jõulumäe Tervisekeskuses', '/galerii/sugisene-treeningulaager-joulumae-tervisekeskuses', 'sugisene-treeningulaager-joulumae-tervisekeskuses', 'Sügisene treeningulaager Jõulumäe Tervis', 'ERR | Tiit Papp', 1, '2024-02-05 17:14:25', '2024-08-31 14:16:38');
INSERT INTO `list_of_galleries` VALUES (13, 38, 996, 'EPK kergejõustik Türil 14.05.2003', '/galerii/epk-kergejoustik-turil-14-05-2003', 'epk-kergejoustik-turil-14-05-2003', 'Eesti Kurtide Liidu juhatuse jõulusoov 2023', 'John Doe', 1, '2024-02-16 14:01:14', '2024-02-28 19:23:39');
INSERT INTO `list_of_galleries` VALUES (25, 38, 1008, 'Lääne-Virumaa KÜ koosolek Rakveres 15.10.2022', '/galerii/lääne-virumaa-kü-koosolek-rakveres-15-10-2022', 'lääne-virumaa-kü-koosolek-rakveres-15-10-2022', 'Lääne-Virumaa KÜ koosolek Rakveres ', 'FOTO: Aire Toobal', 1, '2024-02-17 10:37:27', '2024-03-25 14:10:33');
INSERT INTO `list_of_galleries` VALUES (26, 38, 1009, 'Arlese album 28.02.2024', '/galerii/arlese-album-28-02-2024', 'arlese-album-28-02-2024', 'Eesti Kurtide Liidu suvaline popurii', 'FOTO: Sirle Papp', 1, '2024-02-28 18:11:46', '2024-03-25 15:17:55');
INSERT INTO `list_of_galleries` VALUES (32, 38, 1017, 'Uus popurii', '/galerii/uus-popurii', 'uus-popurii', '', '', 1, '2024-03-25 14:56:18', '2024-06-27 23:01:43');
INSERT INTO `list_of_galleries` VALUES (33, 38, 1018, 'Uus test', '/galerii/uus-test', 'uus-test', '', '', 1, '2024-03-27 10:57:00', '2024-08-07 15:10:21');
INSERT INTO `list_of_galleries` VALUES (34, 38, 1038, 'Blaaa', '/galerii/blaaa', 'blaaa', 'sedrftgyuik', 'asdfgyhjkl', 2, '2024-08-01 00:57:16', '2024-12-01 12:25:55');
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
INSERT INTO `locking` VALUES (1, 1, 'Free', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i>', 1);
INSERT INTO `locking` VALUES (2, 1, 'Locked', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i>', 1);
COMMIT;

-- ----------------------------
-- Table structure for login
-- ----------------------------
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int unsigned DEFAULT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_login_2` (`username`),
  UNIQUE KEY `IDX_login_1` (`person_id`),
  CONSTRAINT `person_login` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of login
-- ----------------------------
BEGIN;
INSERT INTO `login` VALUES (1, 1, 'jdoe', 'p@$$.w0rd', 0);
INSERT INTO `login` VALUES (2, 3, 'brobinson', 'p@$$.w0rd', 1);
INSERT INTO `login` VALUES (3, 4, 'mho', 'p@$$.w0rd', 1);
INSERT INTO `login` VALUES (4, 7, 'kwolfe', 'p@$$.w0rd', 0);
INSERT INTO `login` VALUES (5, NULL, 'system', 'p@$$.w0rd', 1);
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
INSERT INTO `members` VALUES (3, 2807, 2807, 396, 10, 'Spordiseltsid', 0, 'Tallinna Kurtide Spordiselts TALKUR', '80044916', NULL, 'Edgar Liim', NULL, '+372 567 12067', NULL, NULL, NULL, NULL, NULL, '+372 601 5361', 'Nõmme tee 2\nTallinn 13426', 'talkur93@gmail.com', 'https://talkur.ee', '75', 1, '2024-11-12 11:33:00', '2025-01-13 21:28:25');
INSERT INTO `members` VALUES (4, NULL, NULL, 396, 10, 'Spordiseltsid', 1, 'Pärnu Kurtide Spordiselts EERO', '80042975', NULL, 'Eero Pevkur', NULL, '+372 565 03052', NULL, NULL, NULL, NULL, NULL, '+372 442 7131', 'Lubja 48a\nPärnu 80010', 'ksseero@gmail.com', 'http://eero.onepagefree.com', '', 1, '2024-11-12 11:48:33', '2024-11-14 08:10:18');
INSERT INTO `members` VALUES (5, NULL, NULL, 396, 10, 'Spordiseltsid', 2, 'Tartu Kurtide Spordiselts KAAR', '80037661', NULL, 'Jaan-Raul Ojastu', NULL, '+372 585 44757', NULL, NULL, NULL, NULL, NULL, '', 'Suur-Kaar 56\nTartu 50404', 'kaaresport@kaaresport.ee', 'https://www.kaaresport.ee', '', 1, '2024-11-14 08:10:55', '2025-01-13 21:29:47');
INSERT INTO `members` VALUES (6, NULL, NULL, NULL, 10, 'Spordiseltsid', 3, 'BÖÖÖÖ', '', NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', 2, '2025-01-14 00:20:58', '2025-01-14 00:21:52');
INSERT INTO `members` VALUES (7, NULL, NULL, NULL, 10, 'Spordiseltsid', 4, 'BÄÄÄ', '', NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', 2, '2025-01-14 00:21:13', '2025-01-14 00:21:35');
INSERT INTO `members` VALUES (8, NULL, NULL, NULL, 10, 'Spordiseltsid', 5, 'OHOOOO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-14 00:22:06', NULL);
INSERT INTO `members` VALUES (9, NULL, NULL, NULL, 10, 'Spordiseltsid', 6, 'Kuu meeskond', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-14 00:24:12', NULL);
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
INSERT INTO `members_options` VALUES (83, 10, 2, 'Registry code', 1, 1);
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
INSERT INTO `members_options` VALUES (94, 10, 13, 'Address', 2, 1);
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
INSERT INTO `members_settings` VALUES (10, 'Spordiseltsid', 'Eesti Kurtide Spordiliidu liikmesseltsid', 1, 1, 396, '/organisatsioon/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid', '2024-11-12 11:19:25', '2025-01-14 00:24:12', 1, 'John Doe', 1, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (47, NULL, 0, 16, 17);
INSERT INTO `menu` VALUES (50, 621, 1, 23, 24);
INSERT INTO `menu` VALUES (247, NULL, 0, 26, 27);
INSERT INTO `menu` VALUES (283, 621, 1, 21, 22);
INSERT INTO `menu` VALUES (299, 550, 1, 13, 14);
INSERT INTO `menu` VALUES (329, NULL, 0, 42, 43);
INSERT INTO `menu` VALUES (336, NULL, 0, 28, 37);
INSERT INTO `menu` VALUES (337, 336, 1, 33, 34);
INSERT INTO `menu` VALUES (338, 336, 1, 31, 32);
INSERT INTO `menu` VALUES (342, 336, 1, 35, 36);
INSERT INTO `menu` VALUES (353, NULL, 0, 18, 19);
INSERT INTO `menu` VALUES (363, NULL, 0, 48, 49);
INSERT INTO `menu` VALUES (377, 550, 1, 5, 6);
INSERT INTO `menu` VALUES (378, 550, 1, 7, 8);
INSERT INTO `menu` VALUES (379, 550, 1, 9, 10);
INSERT INTO `menu` VALUES (396, 550, 1, 11, 12);
INSERT INTO `menu` VALUES (400, 336, 1, 29, 30);
INSERT INTO `menu` VALUES (420, NULL, 0, 46, 47);
INSERT INTO `menu` VALUES (434, NULL, 0, 52, 53);
INSERT INTO `menu` VALUES (439, 608, 1, 39, 40);
INSERT INTO `menu` VALUES (442, NULL, 0, 58, 59);
INSERT INTO `menu` VALUES (443, NULL, 0, 60, 61);
INSERT INTO `menu` VALUES (452, 456, 1, 63, 64);
INSERT INTO `menu` VALUES (453, 456, 1, 65, 66);
INSERT INTO `menu` VALUES (456, NULL, 0, 62, 69);
INSERT INTO `menu` VALUES (550, NULL, 0, 4, 15);
INSERT INTO `menu` VALUES (608, NULL, 0, 38, 41);
INSERT INTO `menu` VALUES (613, NULL, 0, 44, 45);
INSERT INTO `menu` VALUES (620, NULL, 0, 50, 51);
INSERT INTO `menu` VALUES (621, NULL, 0, 20, 25);
INSERT INTO `menu` VALUES (623, NULL, 0, 54, 55);
INSERT INTO `menu` VALUES (624, NULL, 0, 56, 57);
INSERT INTO `menu` VALUES (633, 456, 1, 67, 68);
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
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Homepage', NULL, 1, '/homepage', '', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (47, 47, 'QCubed arenduse koduleht', NULL, 8, '/qcubed-arenduse-koduleht', NULL, NULL, 1, NULL, 'https://qcubed.eu', NULL, 0, 2, 1);
INSERT INTO `menu_content` VALUES (50, 50, 'Poliitika uudised', '', 3, '/uudised/poliitika-uudised', '/uudised/poliitika-uudised/blaaaa/', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (247, 247, 'Kultuuri uudised', 'Kurtide kultuuri uudised', 3, '/kultuuri-uudised', '/kultuuri-uudisedkurtide-kultuuri-uudised', 1, NULL, NULL, NULL, NULL, 1, NULL, 1);
INSERT INTO `menu_content` VALUES (283, 283, 'Spordiuudised', NULL, 3, '/uudised/spordiuudised', '/uudised/spordiuudised', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (299, 299, 'Status', 'Eesti Kurtide Liidu põhikiri', 2, '/organisatsioon/status', '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', 1, NULL, NULL, NULL, NULL, 1, NULL, 1);
INSERT INTO `menu_content` VALUES (329, 329, 'Pildigalerii', '', 4, '/pildigalerii', '/pildigalerii', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (336, 336, 'Sündmuste kalender', 'Sündmuste kalender', 5, '/sundmuste-kalender', '/sundmuste-kalender/sundmuste-kalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (337, 337, 'Spordikalender', NULL, 6, '/sundmuste-kalender/spordikalender', '/sundmuste-kalender/spordikalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (338, 338, 'Spordisündmuste kalender', 'Spordisündmuste kalender', 6, '/sundmuste-kalender/spordisundmuste-kalender', '/sundmuste-kalender/spordisundmuste-kalender/spordisundmuste-kalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (342, 342, 'Spordialad', NULL, 10, '/sundmuste-kalender/spordialad', '/sundmuste-kalender/spordialad', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (353, 353, 'BLAAA', 'Uurime aadressi muutumist', 2, '/blaaa', '/blaaa/uurime-aadressi-muutumist', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (363, 363, 'Kogukonna galerii', 'Eesti Kurtide Liidu pildigalerii', 4, '/kogukonna-galerii', '/kogukonna-galerii/eesti-kurtide-liidu-pildigalerii', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (377, 377, 'Juhatus', 'Eesti Kurtide Liidu juhatus 2018 - 2023', 11, '/organisatsioon/juhatus', '/organisatsioon/juhatus/eesti-kurtide-liidu-juhatus-2018-2023', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (378, 378, 'Kultuuri juhatus', 'Kultuuri juhatus 2023 - 2028', 11, '/organisatsioon/kultuuri-juhatus', '/organisatsioon/kultuuri-juhatus/kultuuri-juhatus-2023-2028/kultuuri-juhatus-2023-2028', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (379, 379, 'Spordi juhatus', 'Spordi juhatus 2023 - 2028', 11, '/organisatsioon/spordi-juhatus', '/organisatsioon/spordi-juhatus/spordi-juhatus-2023-2028/spordi-juhatus-2023-2028', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (396, 396, 'Spordiseltsid', 'Eesti Kurtide Spordiliidu liikmesseltsid', 12, '/organisatsioon/spordiseltsid', '/organisatsioon/spordiseltsid/eesti-kurtide-spordiliidu-liikmesseltsid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (400, 400, 'Esimene kalender', NULL, 5, '/sundmuste-kalender/esimene-kalender', '/sundmuste-kalender/esimene-kalender', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (420, 420, 'Kurtide galerii', 'Eesti Kurtide kogukonna galerii', 4, '/kurtide-galerii', '/kurtide-galerii/eesti-kurtide-kogukonna-galerii', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (434, 434, 'Suunamine Netile', NULL, 8, '/suunamine-netile', NULL, NULL, 1, NULL, 'https://www.neti.ee/', NULL, 0, 1, 1);
INSERT INTO `menu_content` VALUES (439, 439, 'Videod', 'Videote list', 13, '/parent/videod', '/parent/videod/videote-list', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (442, 442, 'Lingid', 'Partnerite lingid', 17, '/lingid', '/lingidpartnerite-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (443, 443, 'Teised lingid', NULL, 17, '/teised-lingid', '/teised-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (452, 452, 'Rekordid', NULL, 14, '/statistika/rekordid', '/statistika/rekordid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (453, 453, 'Edetabelid', NULL, 15, '/statistika/edetabelid', '/statistika/edetabelid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (456, 456, 'Statistika', 'Statistika avapauk', 2, '/statistika', '/statistikastatistika-avapauk', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (550, 550, 'Organisatsioon', 'Organisatsiooni kontaktandmed', 2, '/organisatsioon', '/organisatsioon/organisatsiooni-kontaktandmed', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (608, 608, 'PARENT', 'Tänitame kindlati edasi', 2, '/parent', '/parent/tanitame-kindlati-edasi', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (613, 613, 'Esimene uus album', NULL, 4, '/esimene-uus-album', '/esimene-uus-album', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (620, 620, 'Uus suunamine põhikirjale', NULL, 7, '/uus-suunamine-pohikirjale', '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', 1, 2, '/organisatsioon/statuseesti-kurtide-liidu-pohikiri', NULL, 299, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (621, 621, 'Uudised', '', 7, '/uudised', '/kultuuri-uudisedkurtide-kultuuri-uudised', 1, 2, '/kultuuri-uudisedkurtide-kultuuri-uudised', NULL, 247, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (623, 623, 'Statistika lingid', NULL, 17, '/statistika-lingid', '/statistika-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (624, 624, 'Statistika uued lingid', NULL, 17, '/statistika-uued-lingid', '/statistika-uued-lingid', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
INSERT INTO `menu_content` VALUES (633, 633, 'Saavutused', NULL, 16, '/statistika/saavutused', '/statistika/saavutused', 1, NULL, NULL, NULL, NULL, 0, NULL, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, 'Avalehe võtmesõnad', 'Avalehe kirjeldus', 'Kodulehe autor');
INSERT INTO `metadata` VALUES (41, 50, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (74, 247, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (81, 283, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (91, 299, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (118, 329, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (120, 336, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (121, 337, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (122, 338, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (124, 342, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (133, 353, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (143, 363, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (155, 377, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (156, 378, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (157, 379, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (168, 396, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (170, 400, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (189, 420, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (191, 439, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (193, 442, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (194, 443, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (199, 452, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (200, 453, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (202, 456, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (203, 550, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (207, 608, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (211, 613, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (212, 623, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (213, 624, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (221, 633, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for milestone
-- ----------------------------
DROP TABLE IF EXISTS `milestone`;
CREATE TABLE `milestone` (
  `id` int unsigned NOT NULL,
  `project_id` int unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_milestoneproj_1` (`project_id`),
  CONSTRAINT `project_milestone` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of milestone
-- ----------------------------
BEGIN;
INSERT INTO `milestone` VALUES (1, 1, 'Milestone A');
INSERT INTO `milestone` VALUES (2, 1, 'Milestone B');
INSERT INTO `milestone` VALUES (3, 1, 'Milestone C');
INSERT INTO `milestone` VALUES (4, 2, 'Milestone D');
INSERT INTO `milestone` VALUES (5, 2, 'Milestone E');
INSERT INTO `milestone` VALUES (6, 3, 'Milestone F');
INSERT INTO `milestone` VALUES (7, 4, 'Milestone G');
INSERT INTO `milestone` VALUES (8, 4, 'Milestone H');
INSERT INTO `milestone` VALUES (9, 4, 'Milestone I');
INSERT INTO `milestone` VALUES (10, 4, 'Milestone J');
COMMIT;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `changes_id` int unsigned DEFAULT NULL,
  `news_group_id` int unsigned DEFAULT NULL,
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
  KEY `news_group_id_idx` (`news_group_id`) USING BTREE,
  KEY `news_group_title_id_idx` (`news_group_title_id`) USING BTREE,
  KEY `changes_id_idx` (`changes_id`) USING BTREE,
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_category_id`) REFERENCES `category_of_news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_2` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_4` FOREIGN KEY (`news_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_5` FOREIGN KEY (`news_group_title_id`) REFERENCES `news_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_6` FOREIGN KEY (`changes_id`) REFERENCES `news_changes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news
-- ----------------------------
BEGIN;
INSERT INTO `news` VALUES (96, NULL, 283, 46, 'Spordiuudised', 'Holger Peel: Eesti treenerikoolitus ei ela parimaid päevi', NULL, NULL, '/uudised/spordiuudised/holger-peel-eesti-treenerikoolitus-ei-ela-parimaid-paevi', NULL, '', NULL, NULL, '<p>Aasta parima meessportlase Johannes Ermi treener Holger Peel r&auml;&auml;kis Vikeraadios suurep&auml;rasest hooajast, kuid t&otilde;des, et mitmev&otilde;istluse kultuur Eestis v&otilde;ib tulevikus treenerite puudumisel hoobi saada.</p>\n\n<p>Peel r&auml;&auml;kis &quot;Vikerhommikus&quot; &uuml;tles, et k&uuml;mnev&otilde;istlusel on Eestis traditsioonid, millele panid aluse Aleksander Klumberg ja Fred Kudu. Lisaks t&otilde;i Peel v&auml;lja TV 10 Ol&uuml;mpiastardi v&otilde;istlussarja, mis annab noortele kergej&otilde;ustiklastele v&otilde;imaluse erinevate aladega tutvuda.</p>\n\n<p>&quot;Teave on meil v&auml;ga k&otilde;va, see on kindlasti teiste riikidega v&otilde;rreldes oluliselt k&otilde;vem. K&uuml;llalt noored lapsed teavad, et selline ala on olemas ja eestlane ilmselt ei olegi nii andekas, et &uuml;ksikaladel k&otilde;rgelt lennata,&quot; s&otilde;nas Peel. &quot;K&uuml;mnev&otilde;istlus on raske, seal on kaks pikka p&auml;eva, k&uuml;mme ala, v&auml;ga erinevad treeningud. Johanneski &uuml;tles aastal&otilde;pu peo&otilde;htul, et &uuml;ksikala oleks teha palju kergem.&quot;</p>\n\n<p>Kui talispordis peavad Eesti parimad sportlased k&otilde;rgematesse tippudesse j&otilde;udmiseks minema teiste koondiste juurde harjutama, siis mitmev&otilde;istluses sellist vajadust pole. &quot;K&uuml;mnev&otilde;istlejad k&uuml;ll ei pea Eestist &auml;ra minema, see on selge. M&otilde;nedel aladel on see vajadus tingimuste p&auml;rast, sest meie riik ei suuda kindlustada k&otilde;ike,&quot; &uuml;tles Peel.</p>\n', '2024-12-31 15:41:42', '2025-03-23 22:49:37', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (97, NULL, 50, 18, 'Poliitika uudised', 'Politico: Vene gaasitransiidi lõppemine tekitab regioonis pingeid', NULL, NULL, '/uudised/poliitika-uudised/blaaaa//politico-vene-gaasitransiidi-loppemine-tekitab-regioonis-pingeid', NULL, '', NULL, NULL, '<p>Vene gaasitarnete l&otilde;ppemine aastavahetusel Ukrainat l&auml;biva gaasitoru kaudu tekitab regioonis pingeid ja t&otilde;stab hindu, kuid ei peaks siiski ohustama energia varustuskindlust, kirjutas v&auml;ljaanne Politico teisip&auml;eval.</p>\n\n<p><strong>Ungari ja Slovakkia ettev&otilde;tted kaotavad konkurentsiv&otilde;imes</strong></p>\n\n<p><a href=\"https://www.politico.eu/newsletter/brussels-playbook/does-putin-turn-into-a-pumpkin-at-midnight/\" rel=\"noopener\" target=\"_blank\">Politico vahendatud eksperdi</a> hinnangul toob Vene odava gaasi l&otilde;ppemine kaasa hinnat&otilde;usu transiidi l&otilde;ppemise &uuml;le k&otilde;ige enam pahandanud Ungaris ja Slovakkias, kuid gaasi puudust pole ette n&auml;ha.</p>\n\n<p>&quot;Vene gaasitarnete j&auml;rsu languse t&otilde;ttu v&otilde;ivad Ungaris ja Slovakkias hinnad t&otilde;usta, millel on potentsiaalne m&otilde;ju kogu piirkonnale, suurendades survet Euroopa Liidule k&auml;rpida energiaarveid. Kuid ettev&otilde;tte ICIS gaasituru eksperdi Aura Sabaduse s&otilde;nul on k&uuml;simus pigem hinnas kui pakkumises,&quot; kirjutas Politico.</p>\n\n<p>&quot;T&scaron;ehhi gaasihoidlate t&auml;ituvus on umbes 67 protsenti, Slovakkias 76 protsenti ja Ungaris umbes 69 protsenti, nii et nendega peaks k&otilde;ik korras olema &ndash; n&otilde;udlus ei tundu olevat v&auml;ga suur ja ilmaprognoos j&auml;&auml;b hooaja keskmise piiresse,&quot; &uuml;tles Sabadus Politicole. &quot;L&otilde;ppkokkuv&otilde;ttes on k&uuml;simus Slovakkia ja Ungari ettev&otilde;tetes, mis teenivad t&auml;nu juurdep&auml;&auml;sule odavale Vene gaasile,&quot; lisas ta.</p>\n', '2025-01-03 19:37:55', '2025-03-23 22:49:00', 1, '2025-01-05 00:00:00', NULL, 3, 'Samantha Jones', 4, 0);
INSERT INTO `news` VALUES (98, NULL, 50, 18, 'Poliitika uudised', 'Saksamaa päikeseenergia sektor vaevleb pankrotilaine küüsis', NULL, NULL, '/uudised/poliitika-uudised/blaaaa//saksamaa-paikeseenergia-sektor-vaevleb-pankrotilaine-kuusis', 2827, '', NULL, NULL, '<p>P&auml;ikesepaneelid Saksamaa eluhoonete katustel Autor/allikas: SCANPIX/Caro/Oberhaeuser</p>\n\n<p>Saksamaa p&auml;ikeseenergia turgu tabas n&otilde;udluse kasvu pidurdumise t&otilde;ttu pankroti- ja koondamiste laine, kirjutab <a href=\"https://www.ft.com/content/83b927f7-db90-49de-8f2c-d0fd88631573\" target=\"_self\">Financial Times</a>.</p>\n\n<p>Mitmed Saksa p&auml;ikesepaneelide paigaldamisega tegelevad ettev&otilde;tted on pankrotti l&auml;inud v&otilde;i pidanud strateegiamuudatusi vastu v&otilde;tma.</p>\n\n<p>Kuigi p&auml;ikesepaneelide m&uuml;&uuml;gi langus ja sellest tulenev &uuml;lek&uuml;llus on tarbijate jaoks kaasa toonud j&auml;rsu hinnalanguse, on selle m&otilde;ju investorite hinnangule olnud negatiivne.</p>\n\n<p>&quot;Mingil m&auml;&auml;ral on see konsolideerumine p&auml;rast paari erakordset aastat,&quot; &uuml;tles t&ouml;&ouml;stuse lobir&uuml;hma Solarpower Europe tegevjuhi aset&auml;itja Dries Acke. &quot;Punaste numbritega ei saa olla rohelist &uuml;leminekut. Sektor peab olema kasumlik.&quot;</p>\n\n<p>Saksamaal kasvas n&otilde;udlus p&auml;ikesepaneelide j&auml;rele p&auml;rast Venemaa t&auml;iemahulist sissetungi Ukrainasse 2022. aastal, kui tarbijad, kes seisid silmitsi h&uuml;ppeliselt kasvavate energiaarvetega, hakkasid rohkem kasutama p&auml;ikeseenergiat.</p>\n', '2025-01-03 19:42:23', '2025-03-23 22:48:28', 0, NULL, NULL, 3, 'Samantha Jones', 3, 0);
INSERT INTO `news` VALUES (99, NULL, 283, 46, 'Spordiuudised', 'Aigro jäi Innsbruckis esimesena lõppvõistluse ukse taha', NULL, NULL, '/uudised/spordiuudised/aigro-jai-innsbruckis-esimesena-loppvoistluse-ukse-taha', 2826, '', NULL, NULL, '<p>Nelja h&uuml;ppem&auml;e turnee esimesel kahel etapil l&otilde;ppv&otilde;istlusele p&auml;&auml;senud Artti Aigro Innsbrucki edasip&auml;&auml;su ei taganud.</p>\n\n<p>Turnee avaeetapil Oberstdorfis&nbsp;21. koha ning siis Garmisch-Partenkirchenis 23. koha teeninud Aigro maandus Innsbrucki m&auml;el 108,5 meetri kaugusele ning teenis h&uuml;ppe eest 94,8 punkti.</p>\n\n<p>25-aastasel eestlasel s&auml;ilis k&uuml;ll v&auml;ike lootus edasi p&auml;&auml;seda, aga teised konkurendid ei v&auml;&auml;ratanud ning Aigro l&otilde;petas eelv&otilde;istluse 51. kohaga ehk j&auml;i kokkuv&otilde;ttes esimesena l&otilde;ppv&otilde;istluselt v&auml;lja. Viimasena p&auml;&auml;ses edasi sloveen Lovro Kos, kes sai 108-meetrise h&uuml;ppe eest 95,1 punkti.</p>\n\n<p>&quot;Ma olen s&otilde;natu, sest vaadates videot, siis ma ei leia sellist viga, mis karistaks mind 10-15 meetriga,&quot; kommenteeris Aigro p&auml;rast sooritust. &quot;Eks ma pean treeneritega pika arutelu tegema, et mingi suur viga &uuml;les leida, mis h&uuml;pet nii palju m&otilde;jutab.&quot;</p>\n\n<p>Aigro oli enne Innsbrucki v&otilde;istlust Nelja h&uuml;ppem&auml;e turnee &uuml;ldkokkuv&otilde;ttes 532,7 punktiga 19. kohal. &quot;Kahju on natuke tuuri &uuml;ldkokkuv&otilde;ttes,&quot; tunnistab eestlane. &quot;See koht langeb p&auml;ris k&otilde;vasti, aga midagi ei ole. L&auml;hme edasi Bischofshofenisse, kus &uuml;ritab natukenegi h&uuml;ppetaset parandada.&quot;</p>\n', '2025-01-03 19:43:00', '2025-03-23 22:47:34', 0, NULL, NULL, 3, 'Samantha Jones', 2, 0);
INSERT INTO `news` VALUES (100, NULL, 283, 46, 'Spordiuudised', 'Männama ja Lepik alustasid Dakari rallit: auto toimis suurepäraselt', NULL, NULL, '/uudised/spordiuudised/mannama-ja-lepik-alustasid-dakari-rallit-auto-toimis-suureparaselt', 2825, '', NULL, NULL, '<p>Eesti rallis&otilde;itjad Urvo M&auml;nnama ja Risto Lepik tegid reedel algust Dakari ralliga, kui teenisid 29 kilomeetri pikkusel proloogil 22. koha.</p>\n\n<p>M&auml;nnama ja Lepik (Overdrive Racing) l&auml;bisid distantsi ajaga 16.41, millega p&auml;lvisid 22. koha. &quot;Proloog on tehtud, auto toimis &uuml;ldpildis suurep&auml;raselt, tehniline pool toimis ideaalselt. Mul navigeerimispuldil tekkisid probleemid, aga kuna meil on seal mitu pulti ja erinevaid nuppe, siis v&otilde;tsime kohe j&auml;rgmised aparaadid kasutusele,&quot; r&auml;&auml;kis kaardilugeja Lepik p&auml;rast katset.</p>\n\n<p>&quot;Katse oli tore ja tulemus ka hea. Eks n&uuml;&uuml;d n&auml;ha, kuidas [laup&auml;eval] katsele peaminek meil on, kus t&auml;pselt asume. Eelmise aastaga on natuke seda olukorda muudetud, me ei pruugi 22. peale minna,&quot; lisas Lepik.</p>\n', '2025-01-03 19:43:33', '2025-03-23 22:47:05', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (101, 1, 247, 20, 'Kultuuri uudised', 'Marja Unt: raamatuaastal rõõmustame, aga peame rääkima ka murekohtadest', NULL, NULL, '/kultuuri-uudised/marja-unt-raamatuaastal-roomustame-aga-peame-raakima-ka-murekohtadest', 1121, '', NULL, NULL, '<p>Kirjandussaade &quot;Loetud ja kirjutatud&quot; heitis seekord pilgu ettepoole &ndash; mida toob meile eesti raamatu aasta 2025? Raamatuaasta peakorraldaja ning Eesti Kirjanduse Seltsi tegevjuht Marja Unt s&otilde;nas saates, et peamine eesm&auml;rk on selgitada inimestele omakeelse kirjanduse ja kirjakeele v&auml;&auml;rtust.</p>\n\n<p>Eesti raamatu aastat ei t&auml;histata sugugi esimest korda. &quot;Eesti raamatu aasta traditsioon sai alguse 1935. aastal, kui t&auml;histati toona esimeseks teadaolevaks eestikeelseks raamatuks peetud Wanradti ja Koelli katekismuse 400. aastap&auml;eva. Toimusid k&otilde;ikv&otilde;imalikud raamatu ja kirjandusega seotud s&uuml;ndmused &uuml;le Eesti ja esimese raamatu aasta &uuml;heks eesm&auml;rgiks oli meie omakeelset raamatu kultuuri v&auml;&auml;rtustada, teha toonases s&otilde;nakasutuses heas m&otilde;ttes raamatu propagandat, et meie omakeelset raamatut kui kultuuri alustala laiemalt rahva teadvusesse viia,&quot; r&auml;&auml;kis Unt.</p>\n', '2025-01-06 22:26:29', '2025-03-23 22:47:39', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
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
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_changes
-- ----------------------------
BEGIN;
INSERT INTO `news_changes` VALUES (1, 'Uuendatud', '2024-08-23 20:00:00', NULL, 1);
INSERT INTO `news_changes` VALUES (2, 'Täiendatud', '2024-08-23 18:00:00', '2024-08-23 21:27:11', 1);
INSERT INTO `news_changes` VALUES (3, 'Edasi lükatud', '2024-08-23 21:50:06', '2024-09-13 13:51:09', 1);
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
INSERT INTO `news_editors_assn` VALUES (96, 1);
INSERT INTO `news_editors_assn` VALUES (96, 2);
INSERT INTO `news_editors_assn` VALUES (97, 1);
INSERT INTO `news_editors_assn` VALUES (97, 2);
INSERT INTO `news_editors_assn` VALUES (98, 1);
INSERT INTO `news_editors_assn` VALUES (98, 2);
INSERT INTO `news_editors_assn` VALUES (99, 1);
INSERT INTO `news_editors_assn` VALUES (99, 2);
INSERT INTO `news_editors_assn` VALUES (100, 1);
INSERT INTO `news_editors_assn` VALUES (100, 2);
INSERT INTO `news_editors_assn` VALUES (101, 1);
INSERT INTO `news_editors_assn` VALUES (101, 2);
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
  `news_group_id` int unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of news_settings
-- ----------------------------
BEGIN;
INSERT INTO `news_settings` VALUES (18, 'Poliitika uudised', '', 1, 1, 50, '/uudised/poliitika-uudised/blaaaa/', '2021-05-25 23:05:45', '2025-01-11 13:14:02', 1);
INSERT INTO `news_settings` VALUES (20, 'Kultuuri uudised', 'Kurtide kultuuri uudised', 1, 1, 247, '/kultuuri-uudised', '2021-11-29 00:25:33', '2025-03-18 23:35:47', 1);
INSERT INTO `news_settings` VALUES (46, 'Spordiuudised', NULL, 1, 1, 283, '/uudised/spordiuudised', '2024-05-13 10:19:52', '2025-01-11 13:04:26', 1);
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
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `organizing_institution_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of organizing_institution
-- ----------------------------
BEGIN;
INSERT INTO `organizing_institution` VALUES (1, 'Eesti Paraolümpiakomitee võistlused', '2024-09-29 02:10:39', NULL, 1);
INSERT INTO `organizing_institution` VALUES (2, 'Rahvusvahelised võistlused', '2024-09-29 02:16:07', NULL, 1);
INSERT INTO `organizing_institution` VALUES (3, 'Treeninglaagrid', '2024-09-29 02:16:33', NULL, 1);
INSERT INTO `organizing_institution` VALUES (5, 'Klubide traditsioonilised võistlused ja üritused', '2024-09-29 02:17:47', '2024-09-29 19:30:39', 1);
INSERT INTO `organizing_institution` VALUES (6, 'Koolitused', '2024-11-24 23:46:24', NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for person
-- ----------------------------
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_person_1` (`last_name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of person
-- ----------------------------
BEGIN;
INSERT INTO `person` VALUES (1, 'John', 'Doe');
INSERT INTO `person` VALUES (2, 'Kendall', 'Public');
INSERT INTO `person` VALUES (3, 'Ben', 'Robinson');
INSERT INTO `person` VALUES (4, 'Mike', 'Ho');
INSERT INTO `person` VALUES (5, 'Alex', 'Smith');
INSERT INTO `person` VALUES (6, 'Wendy', 'Smith');
INSERT INTO `person` VALUES (7, 'Karen', 'Wolfe');
INSERT INTO `person` VALUES (8, 'Samantha', 'Jones');
INSERT INTO `person` VALUES (9, 'Linda', 'Brady');
INSERT INTO `person` VALUES (10, 'Jennifer', 'Smith');
INSERT INTO `person` VALUES (11, 'Brett', 'Carlisle');
INSERT INTO `person` VALUES (12, 'Jacob', 'Pratt');
COMMIT;

-- ----------------------------
-- Table structure for person_persontype_assn
-- ----------------------------
DROP TABLE IF EXISTS `person_persontype_assn`;
CREATE TABLE `person_persontype_assn` (
  `person_id` int unsigned NOT NULL,
  `person_type_id` int unsigned NOT NULL,
  PRIMARY KEY (`person_id`,`person_type_id`),
  KEY `person_type_id` (`person_type_id`),
  CONSTRAINT `person_persontype_assn_1` FOREIGN KEY (`person_type_id`) REFERENCES `person_type` (`id`),
  CONSTRAINT `person_persontype_assn_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of person_persontype_assn
-- ----------------------------
BEGIN;
INSERT INTO `person_persontype_assn` VALUES (3, 1);
INSERT INTO `person_persontype_assn` VALUES (10, 1);
INSERT INTO `person_persontype_assn` VALUES (1, 2);
INSERT INTO `person_persontype_assn` VALUES (3, 2);
INSERT INTO `person_persontype_assn` VALUES (7, 2);
INSERT INTO `person_persontype_assn` VALUES (1, 3);
INSERT INTO `person_persontype_assn` VALUES (3, 3);
INSERT INTO `person_persontype_assn` VALUES (9, 3);
INSERT INTO `person_persontype_assn` VALUES (2, 4);
INSERT INTO `person_persontype_assn` VALUES (7, 4);
INSERT INTO `person_persontype_assn` VALUES (2, 5);
INSERT INTO `person_persontype_assn` VALUES (5, 5);
COMMIT;

-- ----------------------------
-- Table structure for person_type
-- ----------------------------
DROP TABLE IF EXISTS `person_type`;
CREATE TABLE `person_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of person_type
-- ----------------------------
BEGIN;
INSERT INTO `person_type` VALUES (4, 'Company Car');
INSERT INTO `person_type` VALUES (1, 'Contractor');
INSERT INTO `person_type` VALUES (3, 'Inactive');
INSERT INTO `person_type` VALUES (2, 'Manager');
INSERT INTO `person_type` VALUES (5, 'Works From Home');
COMMIT;

-- ----------------------------
-- Table structure for person_with_lock
-- ----------------------------
DROP TABLE IF EXISTS `person_with_lock`;
CREATE TABLE `person_with_lock` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sys_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of person_with_lock
-- ----------------------------
BEGIN;
INSERT INTO `person_with_lock` VALUES (1, 'John', 'DoeS', '2025-01-21 14:02:43');
INSERT INTO `person_with_lock` VALUES (2, 'Kendall', 'Public', NULL);
INSERT INTO `person_with_lock` VALUES (3, 'Ben', 'Robinson', NULL);
INSERT INTO `person_with_lock` VALUES (4, 'Mike', 'Ho', NULL);
INSERT INTO `person_with_lock` VALUES (5, 'Alfred', 'Newman', NULL);
INSERT INTO `person_with_lock` VALUES (6, 'Wendy', 'Johnson', NULL);
INSERT INTO `person_with_lock` VALUES (7, 'Karen', 'Wolfe', NULL);
INSERT INTO `person_with_lock` VALUES (8, 'Samantha', 'Jones', NULL);
INSERT INTO `person_with_lock` VALUES (9, 'Linda', 'Brady', NULL);
INSERT INTO `person_with_lock` VALUES (10, 'Jennifer', 'Smith', NULL);
INSERT INTO `person_with_lock` VALUES (11, 'Brett', 'Carlisle', NULL);
INSERT INTO `person_with_lock` VALUES (12, 'Jacob', 'Pratt', NULL);
COMMIT;

-- ----------------------------
-- Table structure for project
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `project_status_type_id` int unsigned NOT NULL,
  `manager_person_id` int unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `spent` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_project_1` (`project_status_type_id`),
  KEY `IDX_project_2` (`manager_person_id`),
  CONSTRAINT `person_project` FOREIGN KEY (`manager_person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `project_status_type_project` FOREIGN KEY (`project_status_type_id`) REFERENCES `project_status_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of project
-- ----------------------------
BEGIN;
INSERT INTO `project` VALUES (1, 3, 7, 'ACME Website Redesign', 'The redesign of the main website for ACME Incorporated', '2004-03-01', '2004-07-01', 9560.25, 10250.75);
INSERT INTO `project` VALUES (2, 1, 4, 'State College HR System', 'Implementation of a back-office Human Resources system for State College', '2006-02-15', NULL, 80500.00, 73200.00);
INSERT INTO `project` VALUES (3, 1, 1, 'Blueman Industrial Site Architecture', 'Main website architecture for the Blueman Industrial Group', '2006-03-01', '2006-04-15', 2500.00, 4200.50);
INSERT INTO `project` VALUES (4, 2, 7, 'ACME Payment System', 'Accounts Payable payment system for ACME Incorporated', '2005-08-15', '2005-10-20', 5124.67, 5175.30);
COMMIT;

-- ----------------------------
-- Table structure for project_status_type
-- ----------------------------
DROP TABLE IF EXISTS `project_status_type`;
CREATE TABLE `project_status_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `guidelines` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_projectstatustype_1` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of project_status_type
-- ----------------------------
BEGIN;
INSERT INTO `project_status_type` VALUES (1, 'Open', 'The project is currently active', 'All projects that we are working on should be in this state', 1);
INSERT INTO `project_status_type` VALUES (2, 'Cancelled', 'The project has been canned', NULL, 1);
INSERT INTO `project_status_type` VALUES (3, 'Completed', 'The project has been completed successfully', 'Celebrate successes!', 1);
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
INSERT INTO `records` VALUES (22, 1, 1, 1, 8, 2, '12.56', 'seconds', '', '', 'Tallinn', '2014-07-28', 0, 3, 'Samantha Jones', '2025-02-22 17:50:03', NULL, 1);
INSERT INTO `records` VALUES (25, 1, 1, 2, 8, 3, '23.11', 'seconds', '', '', 'rtgyhujikolö', '2008-07-28', 0, 3, 'Samantha Jones', '2025-02-28 13:45:42', NULL, 1);
INSERT INTO `records` VALUES (26, 23, 1, 1, 8, 3, '23.10', 'seconds', '-0.01', '', 'rftgyhujkl', '2022-05-12', 0, 3, 'Samantha Jones', '2025-02-28 13:54:18', NULL, 1);
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
INSERT INTO `sliders` VALUES (103, 2, 1467, 1, 'Luik', NULL, '/Organisatsioon/vilinus reis 2262.jpg', 'jpg', '1936 x 1288', 676, 450, NULL, '2024-09-15 00:21:14', '2024-10-28 01:28:39', 1);
INSERT INTO `sliders` VALUES (104, 2, 1465, 2, NULL, NULL, '/Organisatsioon/valged_orhideed.jpg', 'jpg', '960 x 642', 960, 642, NULL, '2024-09-15 15:53:30', '2024-10-30 19:35:39', 1);
INSERT INTO `sliders` VALUES (109, 2, 2712, 3, 'Kõrred härmatises', NULL, '/galerii67681.jpg', 'jpg', '800 x 533', 800, 533, NULL, '2024-10-16 01:04:00', '2024-10-18 23:12:23', 1);
INSERT INTO `sliders` VALUES (113, 2, 1593, 0, 'Karikakrad vastu päikest', NULL, '/Konventeerimine/karikakrad_vihmas.jpg', 'jpg', '1280 x 868', 1280, 868, NULL, '2024-10-19 21:03:12', '2024-10-19 21:06:27', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vi_0900_ai_ci;

-- ----------------------------
-- Records of sliders_list
-- ----------------------------
BEGIN;
INSERT INTO `sliders_list` VALUES (1, 'Advertisements', 1, '2024-03-07 21:24:41', '2025-07-20 11:01:56', NULL);
INSERT INTO `sliders_list` VALUES (2, 'Sponsors', 1, '2024-03-06 22:26:00', '2025-07-20 13:17:28', NULL);
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
  `is_enabled` int unsigned DEFAULT NULL,
  `is_locked` int unsigned DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  KEY `is_locked_idx` (`is_locked`) USING BTREE,
  CONSTRAINT `areas_of_sports_ibfk` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_areas` VALUES (1, 'Bowling', NULL, NULL, '2021-07-08 23:41:56', '2024-09-27 22:38:13', 1, 1, NULL);
INSERT INTO `sports_areas` VALUES (2, 'Discgolf', NULL, NULL, '2021-07-08 23:42:34', '2021-07-09 23:51:43', 1, 0, NULL);
INSERT INTO `sports_areas` VALUES (3, 'Jalgpall', NULL, NULL, '2021-07-19 01:13:39', '2024-09-26 15:46:04', 1, 0, NULL);
INSERT INTO `sports_areas` VALUES (4, 'Kabe', NULL, NULL, '2021-07-19 01:14:11', '2025-01-16 12:48:02', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (5, 'Karate', NULL, NULL, '2021-07-19 01:14:35', '2021-08-04 14:54:50', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (6, 'Kelgutamine', NULL, NULL, '2021-07-19 01:33:57', '2021-08-04 14:48:28', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (7, 'Kepikõnd', NULL, NULL, '2021-07-19 01:34:18', '2021-11-26 03:31:03', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (8, 'Kergejõustik', '2025-02-28', NULL, '2021-07-19 01:34:50', '2024-09-27 13:14:07', 1, 1, NULL);
INSERT INTO `sports_areas` VALUES (9, 'Koroona', NULL, NULL, '2021-07-19 01:35:09', '2021-08-04 14:48:33', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (10, 'Korvpall', NULL, NULL, '2021-07-19 01:35:34', '2024-09-26 15:45:38', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (11, 'Lauatennis', NULL, NULL, '2021-07-19 01:35:52', '2021-08-04 14:51:14', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (12, 'Male', NULL, NULL, '2021-07-19 01:36:13', '2021-08-04 14:50:20', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (13, 'Minigolf', NULL, NULL, '2021-07-19 01:36:30', '2021-08-04 14:50:11', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (14, 'Murdmaajooks', NULL, NULL, '2021-07-19 03:44:02', '2024-09-26 15:45:33', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (15, 'Noolevise', NULL, NULL, '2021-07-19 03:47:13', '2021-08-04 14:49:59', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (16, 'Orienteerumine', NULL, NULL, '2021-07-19 03:47:40', '2024-09-26 15:45:54', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (17, 'Pesapall', NULL, NULL, '2021-07-19 03:47:58', '2021-08-04 14:49:52', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (18, 'Petank', NULL, NULL, '2021-07-19 03:48:17', '2021-08-04 14:49:43', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (19, 'Rannavolle', NULL, NULL, '2021-07-19 03:48:35', '2024-09-26 15:45:26', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (20, 'Rulluisutamine', NULL, NULL, '2021-07-19 03:48:54', '2024-09-26 15:45:09', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (21, 'Saalihoki', NULL, NULL, '2021-07-19 03:49:10', '2024-09-26 15:45:20', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (22, 'Saalijalgpall', NULL, NULL, '2021-07-19 03:49:29', '2021-08-04 14:49:36', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (23, 'Sisekergejõustik', NULL, NULL, '2021-07-19 03:49:46', '2025-01-16 15:46:22', 1, 1, NULL);
INSERT INTO `sports_areas` VALUES (24, 'Sulgpall', NULL, NULL, '2021-07-19 03:50:02', '2021-08-04 14:49:26', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (25, 'Suusatamine', NULL, NULL, '2021-07-19 03:50:20', '2024-09-26 15:45:02', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (26, 'Tennis', NULL, NULL, '2021-07-19 03:50:35', '2021-08-04 14:49:19', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (27, 'Triatlon', NULL, NULL, '2021-07-19 03:50:50', '2025-01-16 15:43:03', 1, 0, NULL);
INSERT INTO `sports_areas` VALUES (28, 'Uisutamine', NULL, NULL, '2021-07-19 03:51:05', '2021-08-04 14:48:55', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (29, 'Ujumine', NULL, NULL, '2021-07-19 03:51:37', '2025-01-18 12:15:01', 1, 1, NULL);
INSERT INTO `sports_areas` VALUES (30, 'Viievõistlus', NULL, NULL, '2021-07-19 03:51:55', '2024-09-27 22:42:20', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (31, 'Võrkpall', NULL, NULL, '2021-07-19 03:52:10', '2025-01-16 15:45:23', 2, 0, NULL);
INSERT INTO `sports_areas` VALUES (33, 'Muu', NULL, NULL, '2024-09-26 16:01:23', '2025-01-16 13:03:23', 2, 0, NULL);
COMMIT;

-- ----------------------------
-- Table structure for sports_areas_competition_areas
-- ----------------------------
DROP TABLE IF EXISTS `sports_areas_competition_areas`;
CREATE TABLE `sports_areas_competition_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sports_areas_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sports_competition_areas_id` int unsigned DEFAULT NULL,
  `sports_competition_areas_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of sports_areas_competition_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_areas_competition_areas` VALUES (1, 8, 'Kergejõustik', 2, '100 m', '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (2, 8, 'Kergejõustik', 3, '200 m', '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (3, 8, 'Kergejõustik', 5, '800 m', '2025-01-15 00:00:00', NULL, 2);
INSERT INTO `sports_areas_competition_areas` VALUES (4, 8, 'Kergejõustik', 4, '400 m', '2025-01-16 00:00:00', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (18, 29, 'Ujumine', 12, '50 m rinnuli', '2025-01-19 18:45:39', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (26, 8, 'Kergejõustik', 17, '10-võistlus', '2025-02-27 19:14:50', NULL, 1);
INSERT INTO `sports_areas_competition_areas` VALUES (27, 23, 'Sisekergejõustik', 23, '100 m', '2025-02-27 19:20:36', NULL, 1);
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
  `menu_content_group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sport_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizing_institution_id` int unsigned DEFAULT NULL,
  `organizing_institution_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_calendar
-- ----------------------------
BEGIN;
INSERT INTO `sports_calendar` VALUES (10, 2024, NULL, 338, 2, 'Spordisündmuste kalender', 1, 'Bowling', 2712, NULL, NULL, '', 3, NULL, 'Blaaa', '/sundmuste-kalender/spordisundmuste-kalender/spordisundmuste-kalender/2024/blaaa', 'Tallinnas', '2024-09-29', NULL, '10:00:00', NULL, NULL, NULL, '', 'https://www.facebook.com/eestikurtidespordiliit', NULL, 'https://www.facebook.com/eestikurtidespordiliit', 1, NULL, NULL, 'Anneli Ojastu', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 22:25:29', '2025-02-03 09:51:38', 3);
INSERT INTO `sports_calendar` VALUES (11, 2024, NULL, 337, 1, 'Spordikalender', 3, 'Jalgpall', NULL, NULL, NULL, NULL, NULL, NULL, 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/sundmuste-kalender/spordikalender/2024/eesti-ja-lati-kurtide-jalgpalli-sopruskohtumine', 'Jõgeva', '2024-10-10', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 23:57:13', '2025-01-11 00:24:07', 1);
INSERT INTO `sports_calendar` VALUES (12, 2024, 3, 337, 1, 'Spordikalender', 8, 'Kergejõustik', 2641, NULL, NULL, NULL, NULL, NULL, 'Maahoki', '/sundmuste-kalender/spordikalender/2024/maahoki', 'Tallinna staadion, Kalevi tn...', '2024-09-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Annely Ojastu', '+372 1234 5678', 'edso@edso.ee', 4, 'Brett Carlisle', '2024-09-28 00:04:50', '2025-01-15 09:10:14', 1);
INSERT INTO `sports_calendar` VALUES (13, 2024, NULL, 337, 1, 'Spordikalender', 8, 'Kergejõustik', 2680, NULL, NULL, NULL, NULL, NULL, 'EKSL sisekergejõustiku võistlused', '/sundmuste-kalender/spordikalender/2024/eksl-sisekergejoustiku-voistlused', 'Lasname Spordihallis, Punane 8, Tallinn', '2024-10-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 1234 5678', 'eksl@eksl.ee', 4, 'Brett Carlisle', '2024-09-28 00:12:20', '2025-01-11 00:24:07', 1);
INSERT INTO `sports_calendar` VALUES (22, 2025, NULL, 338, 2, 'Spordisündmuste kalender', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bobisõit 2025', '/sundmuste-kalender/spordisundmuste-kalender/2025/bobisoit-2025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'Brett Carlisle', '2025-01-07 21:24:08', '2025-01-11 00:24:05', 2);
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
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `sports_changes_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_changes
-- ----------------------------
BEGIN;
INSERT INTO `sports_changes` VALUES (1, 'Uuendatud', '2024-09-22 16:40:09', '2025-01-15 08:52:58', 1);
INSERT INTO `sports_changes` VALUES (2, 'Täiendatud', '2024-09-22 16:40:30', '2025-01-07 21:32:01', 2);
INSERT INTO `sports_changes` VALUES (3, 'Edasi lükatud', '2024-09-22 16:40:53', '2025-01-15 08:53:06', 1);
INSERT INTO `sports_changes` VALUES (4, 'Tühistatud', '2024-09-29 01:19:25', '2025-01-07 21:32:10', 2);
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
  `is_locked` int unsigned DEFAULT '0',
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
  CONSTRAINT `sports_competition_areas_ibfk_3` FOREIGN KEY (`is_detailed_result`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_competition_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_competition_areas` VALUES (2, '100 m', '2025-01-15 00:00:00', '2025-02-26 14:55:47', 1, 1, 1, 2, '100 m võitja: Sirle Papp');
INSERT INTO `sports_competition_areas` VALUES (3, '200 m', '2025-01-15 00:00:00', '2025-02-26 01:41:22', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (4, '400 m', '2025-01-15 17:15:20', '2025-01-16 11:58:04', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (5, '800 m', '2025-01-15 00:00:00', '2025-02-26 14:54:55', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (6, '1500 m', '2025-01-16 15:47:04', '2025-02-26 15:03:27', 1, 0, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (7, '5000 m', '2025-01-16 15:47:32', '2025-02-26 02:01:03', 2, 0, NULL, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (8, '3000 tj', '2025-01-16 15:48:13', '2025-02-25 21:46:49', 1, 0, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (9, 'Kaugushüpe', '2025-01-17 21:27:26', '2025-02-26 15:13:03', 1, 0, 2, 2, 'Kaugushüppe võitja: Kairit Olenko');
INSERT INTO `sports_competition_areas` VALUES (11, '100 m vabalt', '2025-01-18 12:13:33', '2025-02-26 18:20:42', 1, 0, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (12, '50 m rinnuli', '2025-01-18 12:14:01', '2025-02-25 23:31:53', 1, 1, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (13, '100 m rinnuli', '2025-01-18 12:14:28', '2025-02-26 01:18:15', 1, 0, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (17, '10-võistlus', '2025-02-25 21:49:04', '2025-02-26 18:19:45', 1, 1, 3, 1, NULL);
INSERT INTO `sports_competition_areas` VALUES (18, '7-võistlus', '2025-02-25 21:50:18', NULL, 1, 0, 3, 1, NULL);
INSERT INTO `sports_competition_areas` VALUES (19, '50 m vabalt', '2025-02-26 00:35:12', '2025-02-26 01:13:40', 1, 0, 1, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (20, 'Üksik', '2025-02-26 15:07:08', '2025-02-26 18:20:16', 1, 0, 3, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (21, 'Paar', '2025-02-26 15:09:30', NULL, 1, 0, 3, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (22, 'Kõrgushüpe', '2025-02-26 15:11:54', '2025-02-26 15:13:22', 1, 0, 2, 2, NULL);
INSERT INTO `sports_competition_areas` VALUES (23, '100 m', '2025-02-27 19:16:36', NULL, 1, 1, 1, 2, NULL);
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
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE,
  KEY `status_idx` (`status`),
  CONSTRAINT `sports_content_types_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_content_types
-- ----------------------------
BEGIN;
INSERT INTO `sports_content_types` VALUES (1, 'Juhendid', '2024-10-02 12:00:00', '2024-10-03 11:38:08', 1);
INSERT INTO `sports_content_types` VALUES (2, 'Tulemused', '2024-10-02 12:00:10', '2024-10-03 10:34:09', 1);
INSERT INTO `sports_content_types` VALUES (3, 'Ajakavad', '2024-10-02 12:00:30', '2024-10-06 00:51:05', 1);
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
INSERT INTO `sports_settings` VALUES (1, 'Spordikalender', NULL, '/sundmuste-kalender/spordikalender', 1, 1, 337, '2024-09-25 21:20:41', '2025-01-11 00:24:07', 1);
INSERT INTO `sports_settings` VALUES (2, 'Spordisündmuste kalender', 'Spordisündmuste kalender', '/sundmuste-kalender/spordisundmuste-kalender', 1, 1, 338, '2024-09-27 11:43:56', '2025-01-11 00:24:05', 1);
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
  `sports_content_type_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sports_area_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `sports_content_type_id_idx` (`sports_content_types_id`) USING BTREE,
  KEY `files_id_idx` (`files_id`) USING BTREE,
  KEY `sport_areas_id_idx` (`sports_areas_id`) USING BTREE,
  KEY `sports_calendar_group_id_idx` (`sports_calendar_group_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `sports_tables_ibfk_1` FOREIGN KEY (`sports_content_types_id`) REFERENCES `sports_content_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_2` FOREIGN KEY (`sports_areas_id`) REFERENCES `sports_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_3` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_tables_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sports_tables
-- ----------------------------
BEGIN;
INSERT INTO `sports_tables` VALUES (1, 13, 338, 2024, 'EKSL sisekergejõustiku võistluste juhend 2024', '2024-10-10 00:00:00', 1, 'Juhendid', 8, 'Kergejõustik', 2755, '2024-10-05 18:20:29', '2024-12-13 15:56:18', 1);
INSERT INTO `sports_tables` VALUES (2, 13, 338, 2024, 'EKSL sisekergejõustiku tulemused 2024', '2024-10-10 00:00:00', 2, 'Tulemused', 8, 'Kergejõustik', 2764, '2024-10-05 21:18:18', '2024-12-13 15:56:29', 1);
INSERT INTO `sports_tables` VALUES (5, 13, 338, 2024, 'EKSL siekergejõustiku ajakava 2023', '2024-10-10 00:00:00', 3, 'Ajakavad', 8, 'Kergejõustik', 2758, '2024-10-06 14:20:58', '2024-12-13 15:56:38', 1);
INSERT INTO `sports_tables` VALUES (6, 11, 338, 2024, 'Jalgpalli juhend 2021', '2025-01-15 00:00:00', 1, 'Juhendid', 3, 'Jalgpall', 2757, '2024-10-08 01:04:24', '2025-01-15 09:32:05', 1);
INSERT INTO `sports_tables` VALUES (7, 10, 338, 2024, 'EKSL kergejõustiku MV juhend 2012', '2023-07-27 00:00:00', 1, 'Juhendid', 8, 'Kergejõustik', 2761, '2024-10-09 20:33:50', '2024-12-13 15:34:10', 1);
INSERT INTO `sports_tables` VALUES (9, 10, 338, 2024, 'EKSL kergejõustiku MV juhend 2013', '2025-01-15 00:00:00', 1, 'Juhendid', 1, 'Bowling', 2754, '2024-12-03 15:54:05', '2025-01-15 09:31:38', 1);
INSERT INTO `sports_tables` VALUES (17, 12, 338, 2024, 'Suvaline tulemuste tabel', '2024-12-18 00:00:00', 2, 'Tulemused', 8, 'Kergejõustik', 2757, '2024-12-13 14:49:03', '2024-12-13 15:46:14', 1);
INSERT INTO `sports_tables` VALUES (18, 12, 338, 2024, 'Suvaline juhend 18.12.2024', '2024-12-18 00:00:00', 1, 'Juhendid', 8, 'Kergejõustik', 2764, '2024-12-13 15:53:48', NULL, 1);
INSERT INTO `sports_tables` VALUES (20, 12, 337, 2024, 'Eesti viipekeele ajakava', '2025-01-15 00:00:00', 3, 'Ajakavad', 8, 'Kergejõustik', 2833, '2025-01-15 09:09:47', '2025-01-15 09:32:23', 2);
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
INSERT INTO `statistics_settings` VALUES (14, 'records.php', 'Rekordid', NULL, 1, 1, 452, '/statistika/rekordid', '2024-12-20 19:48:56', '2025-01-21 23:44:22', 1, 'John Doe', 1);
INSERT INTO `statistics_settings` VALUES (15, 'rankings_list.php', 'Edetabelid', NULL, 1, 1, 453, '/statistika/edetabelid', '2024-12-20 19:51:25', '2025-01-21 23:44:22', 1, 'John Doe', 0);
INSERT INTO `statistics_settings` VALUES (16, 'achievements_list.php', 'Saavutused', NULL, 1, 1, 633, '/statistika/saavutused', '2025-03-19 12:55:30', NULL, 1, 'John Doe', 0);
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
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_id_fk` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of target_group_of_calendar
-- ----------------------------
BEGIN;
INSERT INTO `target_group_of_calendar` VALUES (2, 'Kalenderplaan', 1, '2021-06-09 00:47:50', '2025-01-14 10:18:19');
INSERT INTO `target_group_of_calendar` VALUES (3, 'Pensionäride kalenderplaan', 1, '2021-07-02 20:02:13', '2025-01-14 10:18:23');
INSERT INTO `target_group_of_calendar` VALUES (4, 'Sportlaste kalenderplaan', 2, '2021-07-04 23:09:26', '2025-01-07 18:12:27');
INSERT INTO `target_group_of_calendar` VALUES (9, 'Taidlejate kalenderplaan', 2, '2021-07-20 23:07:10', '2024-09-27 22:23:49');
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
-- Table structure for two_key
-- ----------------------------
DROP TABLE IF EXISTS `two_key`;
CREATE TABLE `two_key` (
  `server` varchar(50) NOT NULL,
  `directory` varchar(50) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `person_id` int unsigned NOT NULL,
  `project_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`server`,`directory`),
  KEY `person_id` (`person_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `two_key_person` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `two_key_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of two_key
-- ----------------------------
BEGIN;
INSERT INTO `two_key` VALUES ('cnn.com', 'us', 'news', 1, 1);
INSERT INTO `two_key` VALUES ('google.com', 'drive', '', 2, 2);
INSERT INTO `two_key` VALUES ('google.com', 'mail', 'mail.html', 3, 2);
INSERT INTO `two_key` VALUES ('google.com', 'news', 'news.php', 4, 3);
INSERT INTO `two_key` VALUES ('mail.google.com', 'mail', 'inbox', 5, NULL);
INSERT INTO `two_key` VALUES ('yahoo.com', '', '', 6, NULL);
COMMIT;

-- ----------------------------
-- Table structure for type_test
-- ----------------------------
DROP TABLE IF EXISTS `type_test`;
CREATE TABLE `type_test` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `test_int` int DEFAULT NULL,
  `test_float` float DEFAULT NULL,
  `test_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `test_bit` tinyint(1) DEFAULT NULL,
  `test_varchar` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of type_test
-- ----------------------------
BEGIN;
INSERT INTO `type_test` VALUES (40, '1973-01-05', NULL, NULL, 32, NULL, NULL, 0, NULL);
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
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_real_name_flag` tinyint(1) DEFAULT '0',
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_language` int unsigned DEFAULT NULL,
  `items_per_page_by_assigned_user` int unsigned NOT NULL,
  `preferred_date_time_format` int unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`) USING BTREE,
  KEY `first_name_idx` (`first_name`) USING BTREE,
  KEY `last_name_idx` (`last_name`) USING BTREE,
  KEY `items_per_page_by_assigned_user_idx` (`items_per_page_by_assigned_user`) USING BTREE,
  KEY `preferred_date_time_format_idx` (`preferred_date_time_format`) USING BTREE,
  KEY `preferred_language_id` (`preferred_language`) USING BTREE,
  CONSTRAINT `items_per_page_by_assigned_user_fk` FOREIGN KEY (`items_per_page_by_assigned_user`) REFERENCES `items_per_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preferred_date_time_format_fk` FOREIGN KEY (`preferred_date_time_format`) REFERENCES `date_and_time_formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preferred_language_fk` FOREIGN KEY (`preferred_language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'John', 'Doe', 'doe@gmail.com', 'johndoe', NULL, 0, NULL, 1, 3, 1, 1);
INSERT INTO `user` VALUES (2, 'Alex', 'Smith', 'smith@gmail.com', 'alexsmith', NULL, 0, NULL, 2, 2, 7, 1);
INSERT INTO `user` VALUES (3, 'Samantha', 'Jones', 'samanthajones@gmail.com', 'samantha', NULL, 0, NULL, NULL, 3, NULL, 1);
INSERT INTO `user` VALUES (4, 'Brett', 'Carlisle', 'carlisle@gmail.com', 'carlisle', NULL, 0, NULL, NULL, 4, NULL, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of videos
-- ----------------------------
BEGIN;
INSERT INTO `videos` VALUES (22, 439, 1, 'Videod', 'ICSD presidendi dr. Valery Rukhledev videosõnum', 2, NULL, '<iframe src=\"https://www.youtube.com/embed/Boogq_ipmRQ?si=Qb5CPzXj-QYZgj2B\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:10:16', '2025-03-20 13:02:16', 1);
INSERT INTO `videos` VALUES (23, 439, 1, 'Videod', 'ICSD kongressi kokkuvõte', 1, NULL, '<iframe src=\"https://www.youtube.com/embed/4eGkwY_0TUw?si=by6DzTQ4u0J-4vHD\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:18:02', '2025-03-20 13:02:16', 1);
INSERT INTO `videos` VALUES (24, 439, 1, 'Videod', 'Rahvusvahelise puuetega inimeste päeva tähistamine 3. detsembril 2023', 5, NULL, '<iframe src=\'https://videoteek.ead.ee/embed/65e19bc67f702\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Nii kui igal pool t&auml;histatakse rahvusvahelist puuetega inimeste p&auml;eva, siis meie ei j&auml;&auml; maha!</p>\n\n<p>Eesti Kurtide Liit t&auml;histab seda &uuml;heskoos teistega ning t&auml;na oli &uuml;htekuuluvuskontsert, mis oli meile imeliselt ligip&auml;&auml;setav - viipekeelsed laulud, viipekeele t&otilde;lgid k&otilde;rval, subtiitrid - &uuml;hes&otilde;naga t&auml;ielik ligip&auml;&auml;setavus oli tagatud.</p>\n\n<p>Kuigi lava peal r&auml;&auml;giti m&otilde;nedest olulistest teemadest, nagu sellest, et keegi ei tohiks maha j&auml;&auml;da v&otilde;i olla erinevalt koheldud, vaid k&otilde;ik peaksid olema v&otilde;rdsed, loodame meie, et neist s&otilde;nadest peetakse kinni!</p>\n\n<p>Suurimad t&auml;nud nendele, kes selle kontserdi suure s&uuml;damega panustasid ja korraldasid nii arvestades iga inimese tema vajadusega!</p>\n', '2023-12-03 14:54:46', '2025-03-20 13:02:16', 1);
INSERT INTO `videos` VALUES (25, 439, 1, 'Videod', 'Eesti Delegatsioon 23.Kurtide suveolümpiamängude avatseremoonial', 0, NULL, '<iframe src=\"https://www.youtube.com/embed/OT-BvoP6bfU?si=KJ__eEC_ZkbPdoTP\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '2024-12-10 14:56:29', '2025-03-20 13:02:16', 1);
INSERT INTO `videos` VALUES (27, 439, 1, 'Videod', 'Eesti Kurtide Liidu eesti viipekeele päeva viipekeelne videotervitus 2024', 3, NULL, '<iframe src=\'https://videoteek.ead.ee/embed/65e19154710b5\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Eesti Kurtide Liit on organisatsioon, mis esindab ja &uuml;hendab Eestis elavaid kurte. Eesti viipekeel on &auml;ram&auml;rkimist leidnud juba 2007. aastal 1. m&auml;rtsil kehtima hakanud keeleseaduses eesti riigikeelega v&otilde;rdv&auml;&auml;rses staatuses oleva iseseisva keelena.</p>\n\n<p>T&auml;histamaks v&auml;&auml;rikalt eesti viipekeele 15. aastap&auml;eva, otsustas Eesti Kurtide Liidu juhatus korraldada aktsiooni, kus meie k&auml;isime eesti viipekeelt &otilde;petamas.</p>\n\n<p>Kas tahate teada avaliku tegelaste nimeviipeid? Kas tahate teada, kust keegi on p&auml;rit?</p>\n\n<p>Meie t&auml;name k&otilde;iki osalejaid, kes vastasid positiivselt meie &uuml;leskutsele.</p>\n\n<p>Soovime k&otilde;igile head eesti viipekeele p&auml;eva!</p>\n\n<p>Eesti Kurtide Liidu juhatus</p>\n', '2024-03-01 17:14:25', '2025-03-20 13:02:16', 1);
INSERT INTO `videos` VALUES (28, 439, 1, 'Videod', 'Eesti Kurtide Liidu uue juhatuse väike kokkuvõte 2023. aastast', 4, NULL, '<iframe src=\'https://videoteek.ead.ee/embed/658ea467d4675\' frameborder=\'0\' allowfullscreen></iframe>', '<p>Eesti Kurtide Liidu liidukoosolekul 20. mail 2023 valitud uus juhatus sai l&uuml;hikese ajaga midagi korda saata. Uue juhatuse liikmed annavad &uuml;levaate oma l&uuml;hitegevusest.</p>\n\n<p>Eesti Kurtide Liidu juhatus soovib headele liikmetele, s&otilde;pradele ja koost&ouml;&ouml;partneritele rahulikke j&otilde;ulup&uuml;hi ning tegusat ja &otilde;nnelikku uut aastat!</p>\n', '2023-12-29 17:15:25', '2025-03-20 13:02:16', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of videos_settings
-- ----------------------------
BEGIN;
INSERT INTO `videos_settings` VALUES (1, 'Videod', 'Videote list', 1, 1, 439, '/parent/videod/videote-list', '2024-12-08 21:40:26', '2025-03-20 13:16:14', 1, 'John Doe', 1);
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
