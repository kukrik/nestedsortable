/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 90001
 Source Host           : localhost:3306
 Source Schema         : qcubed-5

 Target Server Type    : MySQL
 Target Server Version : 90001
 File Encoding         : 65001

 Date: 30/11/2024 23:45:39
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
  `written_status` varchar(255) NOT NULL,
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=3;

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
  `street` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_address_1` (`person_id`),
  CONSTRAINT `person_address` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9;

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
-- Table structure for album
-- ----------------------------
DROP TABLE IF EXISTS `album`;
CREATE TABLE `album` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gallery_list_id` int unsigned DEFAULT NULL,
  `gallery_group_title_id` int unsigned DEFAULT NULL,
  `group_title` varchar(255) DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `file_id` int unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `path` text,
  `photo_description` text,
  `photo_author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=1150;

-- ----------------------------
-- Records of album
-- ----------------------------
BEGIN;
INSERT INTO `album` VALUES (1060, 40, 17, 'Pildigalerii', 1105, 2559, 'Tiit mõtleb.jpg', '/pildigalerii/tiidu-album/Tiit mõtleb.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', '2024-09-17 17:41:29');
INSERT INTO `album` VALUES (1062, 40, 17, 'Pildigalerii', 1105, 2561, 'f_DSC01660.jpg', '/pildigalerii/tiidu-album/f_DSC01660.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', NULL);
INSERT INTO `album` VALUES (1063, 40, 17, 'Pildigalerii', 1105, 2562, 'karikakrad_vihmas.jpg', '/pildigalerii/tiidu-album/karikakrad_vihmas.jpg', NULL, NULL, 1, '2024-08-30 16:53:46', NULL);
INSERT INTO `album` VALUES (1066, 40, 17, 'Pildigalerii', 1105, 2565, 'Pildistamisel.jpg', '/pildigalerii/tiidu-album/Pildistamisel.jpg', NULL, NULL, 1, '2024-08-30 16:53:47', '2024-09-17 17:41:58');
INSERT INTO `album` VALUES (1068, 40, 17, 'Pildigalerii', 1105, 2567, 'Luik.jpg', '/pildigalerii/tiidu-album/Luik.jpg', NULL, NULL, 1, '2024-08-30 16:54:28', '2024-09-17 17:41:42');
INSERT INTO `album` VALUES (1070, 41, 21, 'Kogukonna galerii', 1106, 2569, '310596090_1482278652270764_6161734453730055725_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1071, 41, 21, 'Kogukonna galerii', 1106, 2570, '310625658_5771591356213069_6130322049604942068_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1072, 41, 21, 'Kogukonna galerii', 1106, 2571, '310651429_413903317415234_1877068238628190472_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1073, 41, 21, 'Kogukonna galerii', 1106, 2572, '310986468_785287795913568_6096172368795184477_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1074, 41, 21, 'Kogukonna galerii', 1106, 2573, '311163895_800320941296129_7328794715150918241_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1075, 41, 21, 'Kogukonna galerii', 1106, 2574, '311271898_5500936233356667_4481537757649627936_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1076, 41, 21, 'Kogukonna galerii', 1106, 2575, '311451979_627793208998847_3710757790573382164_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1077, 41, 21, 'Kogukonna galerii', 1106, 2576, '311464218_606307097952705_2986433564733245675_n.jpeg', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, NULL, 1, '2024-08-30 20:39:37', NULL);
INSERT INTO `album` VALUES (1083, 45, 17, 'Pildigalerii', 1110, 2595, '403617_297643386939380_307791209_n.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/403617_297643386939380_307791209_n.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1084, 45, 17, 'Pildigalerii', 1110, 2596, '6954421-christmas-lights.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/6954421-christmas-lights.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1085, 45, 17, 'Pildigalerii', 1110, 2597, '2078524051_ed4de415ef_o.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/2078524051_ed4de415ef_o.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1086, 45, 17, 'Pildigalerii', 1110, 2598, '2094750459_7e05256e05_o.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/2094750459_7e05256e05_o.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1087, 45, 17, 'Pildigalerii', 1110, 2599, 'Bnowchristmas_1600x1200.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Bnowchristmas_1600x1200.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1088, 45, 17, 'Pildigalerii', 1110, 2600, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1089, 45, 17, 'Pildigalerii', 1110, 2601, 'Christmas_candles_by_SizkaS.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_candles_by_SizkaS.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1090, 45, 17, 'Pildigalerii', 1110, 2602, 'Christmas_Greetings_2009.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Greetings_2009.jpg', NULL, NULL, 1, '2024-08-31 13:45:11', NULL);
INSERT INTO `album` VALUES (1091, 45, 17, 'Pildigalerii', 1110, 2603, 'Christmas_Wallpaper_Snowman_Snow.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Wallpaper_Snowman_Snow.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1093, 45, 17, 'Pildigalerii', 1110, 2605, 'christmas-2618263_1280.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2618263_1280.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1094, 45, 17, 'Pildigalerii', 1110, 2606, 'christmas-2877141_1280.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2877141_1280.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1095, 45, 17, 'Pildigalerii', 1110, 2607, 'Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1096, 45, 17, 'Pildigalerii', 1110, 2608, 'christmas-night-magic-house.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-night-magic-house.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1097, 45, 17, 'Pildigalerii', 1110, 2609, 'christmas-wallpapers-backgrounds.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers-backgrounds.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1098, 45, 17, 'Pildigalerii', 1110, 2610, 'christmas-wallpapers.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1099, 45, 17, 'Pildigalerii', 1110, 2611, 'ChristmasCandlelightss1.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ChristmasCandlelightss1.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1100, 45, 17, 'Pildigalerii', 1110, 2612, 'ehted_pky.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ehted_pky.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1101, 45, 17, 'Pildigalerii', 1110, 2613, 'ekl_jolukaart_2015.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_jolukaart_2015.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1103, 45, 17, 'Pildigalerii', 1110, 2615, 'ekl_joulukaart_2012.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2012.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1106, 45, 17, 'Pildigalerii', 1110, 2618, 'ekl_joulukaart_2013.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2013.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1109, 45, 17, 'Pildigalerii', 1110, 2621, 'ekl_joulukaart_2016.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2016.jpg', NULL, NULL, 1, '2024-08-31 13:45:12', NULL);
INSERT INTO `album` VALUES (1110, 45, 17, 'Pildigalerii', 1110, 2622, 'ekl_joulukaart_2021.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2021.jpg', NULL, NULL, 1, '2024-08-31 13:45:13', NULL);
INSERT INTO `album` VALUES (1112, 45, 17, 'Pildigalerii', 1110, 2729, 'f_DSC01660.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/f_DSC01660.jpg', NULL, NULL, 1, '2024-09-12 18:40:38', NULL);
INSERT INTO `album` VALUES (1113, 45, 17, 'Pildigalerii', 1110, 2730, 'file60471593_d5a21f14.jpg', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-09-12 18:40:38', NULL);
INSERT INTO `album` VALUES (1116, 46, 17, 'Pildigalerii', 1112, 2733, 'raamatud.jpg', '/pildigalerii/uus-album/raamatud.jpg', NULL, NULL, 1, '2024-09-12 19:19:07', NULL);
INSERT INTO `album` VALUES (1117, 46, 17, 'Pildigalerii', 1112, 2734, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', '/pildigalerii/uus-album/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, NULL, 1, '2024-09-12 19:19:07', NULL);
INSERT INTO `album` VALUES (1118, 46, 17, 'Pildigalerii', 1112, 2735, 'r 175.jpg', '/pildigalerii/uus-album/r 175.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1119, 46, 17, 'Pildigalerii', 1112, 2736, 'rukkilill.jpg', '/pildigalerii/uus-album/rukkilill.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1120, 46, 17, 'Pildigalerii', 1112, 2737, 'seinakell.jpg', '/pildigalerii/uus-album/seinakell.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1121, 46, 17, 'Pildigalerii', 1112, 2738, 'vilinus reis 2263.jpg', '/pildigalerii/uus-album/vilinus reis 2263.jpg', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1122, 46, 17, 'Pildigalerii', 1112, 2739, 'valentinikyynlas.JPG', '/pildigalerii/uus-album/valentinikyynlas.JPG', NULL, NULL, 1, '2024-09-12 19:19:08', NULL);
INSERT INTO `album` VALUES (1133, 48, 17, 'Pildigalerii', 1123, 2777, 'DSC_0008.JPG', '/pildigalerii/tanugala-2024/DSC_0008.JPG', NULL, NULL, 1, '2024-10-10 16:27:49', NULL);
INSERT INTO `album` VALUES (1134, 48, 17, 'Pildigalerii', 1123, 2778, 'allkiri.png', '/pildigalerii/tanugala-2024/allkiri.png', NULL, NULL, 1, '2024-10-10 16:27:49', '2024-10-21 01:53:36');
INSERT INTO `album` VALUES (1135, 48, 17, 'Pildigalerii', 1123, 2779, 'DSC_0084.JPG', '/pildigalerii/tanugala-2024/DSC_0084.JPG', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1136, 48, 17, 'Pildigalerii', 1123, 2780, 'DSC_5197_1.jpg', '/pildigalerii/tanugala-2024/DSC_5197_1.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1137, 48, 17, 'Pildigalerii', 1123, 2781, 'DSC_5177_1.jpg', '/pildigalerii/tanugala-2024/DSC_5177_1.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', NULL);
INSERT INTO `album` VALUES (1138, 48, 17, 'Pildigalerii', 1123, 2782, 'DSC_7550.jpg', '/pildigalerii/tanugala-2024/DSC_7550.jpg', NULL, NULL, 1, '2024-10-10 16:27:50', '2024-10-21 23:02:04');
INSERT INTO `album` VALUES (1140, 48, 17, 'Pildigalerii', 1123, 2784, 'seebimullid.jpg', '/pildigalerii/tanugala-2024/seebimullid.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', '2024-10-30 15:08:00');
INSERT INTO `album` VALUES (1141, 48, 17, 'Pildigalerii', 1123, 2785, 'kuldnokk puuladvas.jpg', '/pildigalerii/tanugala-2024/kuldnokk puuladvas.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', '2024-10-30 15:08:57');
INSERT INTO `album` VALUES (1144, 48, 17, 'Pildigalerii', 1123, 2788, 'file60471593_d5a21f14.jpg', '/pildigalerii/tanugala-2024/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', NULL);
INSERT INTO `album` VALUES (1145, 48, 17, 'Pildigalerii', 1123, 2789, 'galerii67681.jpg', '/pildigalerii/tanugala-2024/galerii67681.jpg', NULL, NULL, 1, '2024-10-24 20:45:11', NULL);
INSERT INTO `album` VALUES (1146, 40, 17, 'Pildigalerii', 1105, 2790, 'DSC_5197_1.jpg', '/pildigalerii/tiidu-album/DSC_5197_1.jpg', NULL, NULL, 1, '2024-10-24 21:02:43', NULL);
INSERT INTO `album` VALUES (1147, 40, 17, 'Pildigalerii', 1105, 2791, 'DSC_5177_1.jpg', '/pildigalerii/tiidu-album/DSC_5177_1.jpg', NULL, NULL, 1, '2024-10-24 21:02:43', NULL);
COMMIT;

-- ----------------------------
-- Table structure for albums
-- ----------------------------
DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `path` text,
  `title_slug` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_activity` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39;

-- ----------------------------
-- Records of albums
-- ----------------------------
BEGIN;
INSERT INTO `albums` VALUES (38, 989, 'galerii', '/galerii', 'galerii', '2024-02-04 20:09:11', '2024-02-18 19:21:58', 1);
COMMIT;

-- ----------------------------
-- Table structure for areas_of_sports
-- ----------------------------
DROP TABLE IF EXISTS `areas_of_sports`;
CREATE TABLE `areas_of_sports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`) USING BTREE,
  CONSTRAINT `areas_of_sports_ibfk` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35;

-- ----------------------------
-- Records of areas_of_sports
-- ----------------------------
BEGIN;
INSERT INTO `areas_of_sports` VALUES (1, 'Bowling', 1, '2021-07-08 23:41:56', '2024-09-27 22:38:13');
INSERT INTO `areas_of_sports` VALUES (2, 'Discgolf', 1, '2021-07-08 23:42:34', '2021-07-09 23:51:43');
INSERT INTO `areas_of_sports` VALUES (3, 'Jalgpall', 1, '2021-07-19 01:13:39', '2024-09-26 15:46:04');
INSERT INTO `areas_of_sports` VALUES (4, 'Kabe', 2, '2021-07-19 01:14:11', '2021-07-21 18:17:46');
INSERT INTO `areas_of_sports` VALUES (5, 'Karate', 2, '2021-07-19 01:14:35', '2021-08-04 14:54:50');
INSERT INTO `areas_of_sports` VALUES (6, 'Kelgutamine', 2, '2021-07-19 01:33:57', '2021-08-04 14:48:28');
INSERT INTO `areas_of_sports` VALUES (7, 'Kepikõnd', 2, '2021-07-19 01:34:18', '2021-11-26 03:31:03');
INSERT INTO `areas_of_sports` VALUES (8, 'Kergejõustik', 1, '2021-07-19 01:34:50', '2024-09-27 13:14:07');
INSERT INTO `areas_of_sports` VALUES (9, 'Koroona', 2, '2021-07-19 01:35:09', '2021-08-04 14:48:33');
INSERT INTO `areas_of_sports` VALUES (10, 'Korvpall', 2, '2021-07-19 01:35:34', '2024-09-26 15:45:38');
INSERT INTO `areas_of_sports` VALUES (11, 'Lauatennis', 2, '2021-07-19 01:35:52', '2021-08-04 14:51:14');
INSERT INTO `areas_of_sports` VALUES (12, 'Male', 2, '2021-07-19 01:36:13', '2021-08-04 14:50:20');
INSERT INTO `areas_of_sports` VALUES (13, 'Minigolf', 2, '2021-07-19 01:36:30', '2021-08-04 14:50:11');
INSERT INTO `areas_of_sports` VALUES (14, 'Murdmaajooks', 2, '2021-07-19 03:44:02', '2024-09-26 15:45:33');
INSERT INTO `areas_of_sports` VALUES (15, 'Noolevise', 2, '2021-07-19 03:47:13', '2021-08-04 14:49:59');
INSERT INTO `areas_of_sports` VALUES (16, 'Orienteerumine', 2, '2021-07-19 03:47:40', '2024-09-26 15:45:54');
INSERT INTO `areas_of_sports` VALUES (17, 'Pesapall', 2, '2021-07-19 03:47:58', '2021-08-04 14:49:52');
INSERT INTO `areas_of_sports` VALUES (18, 'Petank', 2, '2021-07-19 03:48:17', '2021-08-04 14:49:43');
INSERT INTO `areas_of_sports` VALUES (19, 'Rannavolle', 2, '2021-07-19 03:48:35', '2024-09-26 15:45:26');
INSERT INTO `areas_of_sports` VALUES (20, 'Rulluisutamine', 2, '2021-07-19 03:48:54', '2024-09-26 15:45:09');
INSERT INTO `areas_of_sports` VALUES (21, 'Saalihoki', 2, '2021-07-19 03:49:10', '2024-09-26 15:45:20');
INSERT INTO `areas_of_sports` VALUES (22, 'Saalijalgpall', 2, '2021-07-19 03:49:29', '2021-08-04 14:49:36');
INSERT INTO `areas_of_sports` VALUES (23, 'Sisekergejõustik', 2, '2021-07-19 03:49:46', '2024-09-26 15:45:14');
INSERT INTO `areas_of_sports` VALUES (24, 'Sulgpall', 2, '2021-07-19 03:50:02', '2021-08-04 14:49:26');
INSERT INTO `areas_of_sports` VALUES (25, 'Suusatamine', 2, '2021-07-19 03:50:20', '2024-09-26 15:45:02');
INSERT INTO `areas_of_sports` VALUES (26, 'Tennis', 2, '2021-07-19 03:50:35', '2021-08-04 14:49:19');
INSERT INTO `areas_of_sports` VALUES (27, 'Triatlon', 2, '2021-07-19 03:50:50', '2024-09-26 15:44:57');
INSERT INTO `areas_of_sports` VALUES (28, 'Uisutamine', 2, '2021-07-19 03:51:05', '2021-08-04 14:48:55');
INSERT INTO `areas_of_sports` VALUES (29, 'Ujumine', 2, '2021-07-19 03:51:37', '2024-09-26 15:44:13');
INSERT INTO `areas_of_sports` VALUES (30, 'Viievõistlus', 2, '2021-07-19 03:51:55', '2024-09-27 22:42:20');
INSERT INTO `areas_of_sports` VALUES (31, 'Võrkpall', 2, '2021-07-19 03:52:10', '2024-09-27 22:43:56');
INSERT INTO `areas_of_sports` VALUES (33, 'Muu', 2, '2024-09-26 16:01:23', NULL);
COMMIT;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `picture_id` int DEFAULT NULL,
  `files_ids` varchar(255) DEFAULT NULL,
  `picture_description` text,
  `author_source` varchar(255) DEFAULT NULL,
  `content` text,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `category_id_article_fk` FOREIGN KEY (`category_id`) REFERENCES `category_of_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_content_id_article_fk` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_article_fk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82;

-- ----------------------------
-- Records of article
-- ----------------------------
BEGIN;
INSERT INTO `article` VALUES (70, 299, 'Eesti Kurtide Liidu põhikiri', NULL, 'eesti-kurtide-liidu-pohikiri', NULL, '', NULL, NULL, '<p><img alt=\"\" src=\"/qcubed-4/project/tmp/_files/thumbnail/Konventeerimine/karikakrad_vihmas.jpg\" style=\"float:left; height:217px; margin:5px 10px; width:320px\" /></p>\n', '2024-07-03 20:43:31', '2024-10-19 21:10:08', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (71, 313, 'Organisatsiooni kontaktandmed', 1, 'organisatsiooni-kontaktandmed', 1121, '', NULL, NULL, '<table align=\"center\" border=\"0\" style=\"width:600px\">\n	<tbody>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Organisatsiooni nimi:</strong></td>\n			<td style=\"vertical-align:top\">MT&Uuml; Eesti Kurtide Liit</td>\n		</tr>\n		<tr>\n			<td style=\"vertical-align:top\"><strong>Juriidiline aadress:</strong></td>\n			<td>N&otilde;mme tee 2, 13426 Tallinn</td>\n		</tr>\n		<tr>\n			<td><strong>Telefon:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>Faks:</strong></td>\n			<td>+372 655 2510</td>\n		</tr>\n		<tr>\n			<td><strong>SMS:</strong></td>\n			<td>+372 5218851</td>\n		</tr>\n		<tr>\n			<td><strong>E-mail:</strong></td>\n			<td>ead<img alt=\"\" src=\"http://www.ead.ee/automatweb/images/at.png\" />ead.ee</td>\n		</tr>\n		<tr>\n			<td><strong>Registrikood:</strong></td>\n			<td>80007861</td>\n		</tr>\n		<tr>\n			<td><strong>Arveldusarve:</strong></td>\n			<td>EE891010022002532007&nbsp;SEB</td>\n		</tr>\n		<tr>\n			<td><strong>SWIFT kood (BIC):</strong></td>\n			<td>EEUHEE2X</td>\n		</tr>\n		<tr>\n			<td><strong>Asutatud:</strong></td>\n			<td>1922</td>\n		</tr>\n		<tr>\n			<td><strong>Liikmete arv:</strong></td>\n			<td>9 &uuml;hingut ja 2 organisatsiooni, 3 ettev&otilde;tet,&nbsp; 857 &uuml;ksikisikut <em>(01.09.2020. a. seisuga)</em></td>\n		</tr>\n		<tr>\n			<td><strong>Juhatuse esimees:</strong></td>\n			<td>\n			<p>Tiit Papp</p>\n			</td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n', '2024-07-30 18:25:17', '2024-11-16 00:39:55', 2, 'Alex Smith', 0);
INSERT INTO `article` VALUES (80, 352, 'Vaatame kaasautorlust', NULL, 'vaatame-kaasautorlust', 2641, '', NULL, NULL, NULL, '2024-10-17 02:29:00', '2024-11-24 00:54:54', 1, 'John Doe', 0);
INSERT INTO `article` VALUES (81, 353, 'Böööö', NULL, 'boooo', 2712, '', 'Bööö oli tubli', 'John Doe', '', '2024-10-17 02:35:53', '2024-11-16 00:39:07', 1, 'John Doe', 0);
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
) ENGINE=InnoDB;

-- ----------------------------
-- Records of articles_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `articles_editors_assn` VALUES (70, 2);
INSERT INTO `articles_editors_assn` VALUES (70, 3);
INSERT INTO `articles_editors_assn` VALUES (71, 1);
INSERT INTO `articles_editors_assn` VALUES (71, 3);
INSERT INTO `articles_editors_assn` VALUES (80, 2);
INSERT INTO `articles_editors_assn` VALUES (80, 3);
INSERT INTO `articles_editors_assn` VALUES (81, 3);
COMMIT;

-- ----------------------------
-- Table structure for board
-- ----------------------------
DROP TABLE IF EXISTS `board`;
CREATE TABLE `board` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int unsigned DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `board_id` int unsigned DEFAULT NULL,
  `board_id_title` varchar(255) DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `areas_responsibility` varchar(255) DEFAULT NULL,
  `interests` text,
  `description` text,
  `telephone` varchar(255) DEFAULT NULL,
  `sms` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `board_id_idx` (`board_id`) USING BTREE,
  CONSTRAINT `board_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `board_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14;

-- ----------------------------
-- Records of board
-- ----------------------------
BEGIN;
INSERT INTO `board` VALUES (1, 2806, '/Juhatus/2018-2023/crop_Tiit_Papp.png', 2806, 7, 'Juhatus', 0, 'Tiit Papp', 'Juhatuse esimees', 'Liidu esindamine, juhatuse töö korraldamine, üldjuhtimine', NULL, NULL, '', '+372 521 8851', NULL, NULL, 'ead@ead.ee', NULL, 1, '2024-11-01 21:50:11', '2024-11-14 09:06:24');
INSERT INTO `board` VALUES (2, 2805, '/Juhatus/2018-2023/crop_Sirle_Papp.png', 2805, 7, 'Juhatus', 2, 'Sirle Papp', 'Juhatuse liige', 'Meedia, haridus, töö noortega\n', NULL, NULL, NULL, '+372 5331 7152', NULL, NULL, 'sirlepappgmail.com', NULL, 1, '2024-11-02 00:41:19', '2024-11-14 09:05:04');
INSERT INTO `board` VALUES (6, 2804, '/Juhatus/2018-2023/crop_Riina_Kuusk.png', 2804, 7, 'Juhatus', 1, 'Riina Kuusk', 'Juhatuse aseesimees', 'Tööhõive, töö pensionäridega, esimehe äraolekul liidu esindamine, juhatuse töö korraldamine\n\n', NULL, NULL, NULL, '+372 5650 3051', NULL, NULL, 'riinak61gmail.com', NULL, 1, '2024-11-02 19:43:46', '2024-11-14 09:05:04');
INSERT INTO `board` VALUES (7, 2801, '/Juhatus/2018-2023/crop_Helle_Sass.png', 2801, 7, 'Juhatus', 3, 'Helle Sass', 'Juhatuse liige', 'Kultuuritöö, liidu esindamine Eesti Puuetega Inimeste Kojas', NULL, NULL, '+372 5399 7837', '+372 5399 7837', NULL, NULL, 'helle.sassgmail.com', NULL, 1, '2024-11-02 19:50:30', '2024-11-14 09:05:04');
INSERT INTO `board` VALUES (8, 2802, '/Juhatus/2018-2023/crop_Janis_Golubenkov.png', 2802, 7, 'Juhatus', 4, 'Janis Golubenkov', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 19:58:05', '2024-11-14 09:05:04');
INSERT INTO `board` VALUES (10, 2803, '/Juhatus/2018-2023/crop_Mati_Kartus.png', 2803, 7, 'Juhatus', 5, 'Mati Kartus', 'Juhatuse liige', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 20:52:28', '2024-11-14 09:05:04');
INSERT INTO `board` VALUES (13, NULL, NULL, NULL, 8, 'Kultuuri juhatus', 1, 'Jakob Hurd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2024-11-02 22:34:12', NULL);
COMMIT;

-- ----------------------------
-- Table structure for board_options
-- ----------------------------
DROP TABLE IF EXISTS `board_options`;
CREATE TABLE `board_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `settings_id` int unsigned DEFAULT NULL,
  `input_key` int unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `activity_status` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`activity_status`) USING BTREE,
  KEY `boards_settings_id_idx` (`settings_id`) USING BTREE,
  CONSTRAINT `boards_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `status_ibfk` FOREIGN KEY (`activity_status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34;

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
) ENGINE=InnoDB;

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
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=10;

-- ----------------------------
-- Records of boards_settings
-- ----------------------------
BEGIN;
INSERT INTO `boards_settings` VALUES (7, 'Juhatus', 'Eesti Kurtide Liidu juhatus 2018 - 2023', 1, 1, 377, '/juhatus', '/juhatus', '2024-10-30 22:19:29', '2024-11-24 21:09:52', 1, 'John Doe', 1, 1);
INSERT INTO `boards_settings` VALUES (8, 'Kultuuri juhatus', 'Kultuuri juhatus 2023 - 2028', 1, 1, 378, '/kultuuri-juhatus', '/kultuuri-juhatus', '2024-11-01 01:38:19', '2024-11-07 01:03:25', 1, 'John Doe', 1, 1);
INSERT INTO `boards_settings` VALUES (9, 'Spordi juhatus', 'Spordi juhatus 2023 - 2028', 1, 1, 379, '/spordi-juhatus', '/spordi-juhatus', '2024-11-01 13:21:14', '2024-11-11 21:05:42', 1, 'John Doe', 0, 2);
COMMIT;

-- ----------------------------
-- Table structure for category_of_article
-- ----------------------------
DROP TABLE IF EXISTS `category_of_article`;
CREATE TABLE `category_of_article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_1` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41;

-- ----------------------------
-- Records of category_of_article
-- ----------------------------
BEGIN;
INSERT INTO `category_of_article` VALUES (1, 'Education', 1, '2020-05-30 10:00:00', '2024-11-16 00:13:07');
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
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_2` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19;

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
-- Table structure for content_type
-- ----------------------------
DROP TABLE IF EXISTS `content_type`;
CREATE TABLE `content_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tabs_text` varchar(255) DEFAULT NULL,
  `class_names` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15;

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
INSERT INTO `content_type` VALUES (9, 'Placeholder', 'Edit placeholder', 'PlaceholderEditPanel', 1);
INSERT INTO `content_type` VALUES (10, 'Sports areas', 'Edit sports areas', 'SportsAreasEditPanel', 1);
INSERT INTO `content_type` VALUES (11, 'Board', 'Edit board', 'BoardEditPanel', 1);
INSERT INTO `content_type` VALUES (12, 'Members', 'Edit members', 'MembersEditPanel', 1);
INSERT INTO `content_type` VALUES (13, 'Videos', 'Edit videos', 'VideosEditPanel', 1);
INSERT INTO `content_type` VALUES (14, 'Statistics', 'Edit Statistics', 'StatisticsEditPanel', 1);
COMMIT;

-- ----------------------------
-- Table structure for content_types_management
-- ----------------------------
DROP TABLE IF EXISTS `content_types_management`;
CREATE TABLE `content_types_management` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content_name` varchar(255) NOT NULL,
  `content_type` int unsigned NOT NULL,
  `view_type` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_type_id_idx` (`content_type`) USING BTREE,
  KEY `view_type_id_idx` (`view_type`) USING BTREE,
  KEY `id` (`id`,`content_name`),
  KEY `content_name` (`content_name`),
  CONSTRAINT `content_type_id_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `view_type_id_fk` FOREIGN KEY (`view_type`) REFERENCES `view_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14;

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
COMMIT;

-- ----------------------------
-- Table structure for date_and_time_formats
-- ----------------------------
DROP TABLE IF EXISTS `date_and_time_formats`;
CREATE TABLE `date_and_time_formats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `display_format` varchar(255) DEFAULT NULL,
  `date_format` varchar(255) DEFAULT NULL,
  `time_format` varchar(255) DEFAULT NULL,
  `calendar_date_format` varchar(255) DEFAULT NULL,
  `calendar_time_format` varchar(255) DEFAULT NULL,
  `calendar_show_meridian` tinyint(1) DEFAULT '0',
  `is_enabled` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_ibfk_3` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8;

-- ----------------------------
-- Records of date_and_time_formats
-- ----------------------------
BEGIN;
INSERT INTO `date_and_time_formats` VALUES (1, '31.12.2001 23.59.00', 'DD.MM.YYYY', 'hhhh.mm.ss', 'dd.mm.yyyy', ' hh.ii', 0, 1);
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
  `events_group_name` varchar(255) DEFAULT NULL,
  `target_group_id` int unsigned DEFAULT NULL,
  `target_group_title` varchar(255) DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) DEFAULT NULL,
  `picture_description` text,
  `author_source` varchar(255) DEFAULT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `event_place` text,
  `beginning_event` date DEFAULT NULL,
  `end_event` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `information` text,
  `schedule` text,
  `instruction_link` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `website_target_type_id` int unsigned DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `facebook_target_type_id` int unsigned DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `instagram_target_type_id` int unsigned DEFAULT NULL,
  `organizers` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=45;

-- ----------------------------
-- Records of events_calendar
-- ----------------------------
BEGIN;
INSERT INTO `events_calendar` VALUES (38, NULL, 336, 1, 'Sündmuste kalender', 2, 'Kalenderplaan', 2726, NULL, NULL, NULL, 2024, 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev-1', 'Kuu peal', '2024-10-26', NULL, NULL, NULL, NULL, NULL, NULL, 'www.kuu.ku', 4, NULL, NULL, NULL, NULL, 'Humanoid', '+372 1234 5678', 'humanoid@huanoid.ku', 3, 'Samantha Jones', '2024-09-20 17:27:27', '2024-11-24 23:10:17', 1);
INSERT INTO `events_calendar` VALUES (39, 4, 336, 1, 'Sündmuste kalender', 2, 'Kalenderplaan', NULL, NULL, NULL, NULL, 2024, 'Naistepäev', '/sundmuste-kalender/2024/naistepaev', 'Mujal', '2024-10-31', NULL, NULL, NULL, NULL, NULL, NULL, 'www.thky.ee', 1, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 521 8851', 'spordiliit@ead.ee', 3, 'Samantha Jones', '2024-09-20 23:04:07', '2024-11-23 00:25:58', 3);
INSERT INTO `events_calendar` VALUES (40, NULL, 336, 1, 'Sündmuste kalender', 2, 'Kalenderplaan', 2751, NULL, NULL, NULL, 2024, 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev', 'Tallinna teletorni juures', '2024-09-23', NULL, '21:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sirle Papp', '+372 58 99990', 'ead@ead.ee', 3, 'Samantha Jones', '2024-09-20 23:04:42', '2024-11-19 22:34:15', 1);
INSERT INTO `events_calendar` VALUES (41, NULL, 345, 4, 'Kolmas kalender', 2, 'Kalenderplaan', 1693, NULL, NULL, NULL, 2024, 'Naistepäev', '/kolmas-kalender/2024/naistepaev', 'Tartus', '2024-09-29', NULL, NULL, NULL, '<p>Head naistep&auml;eva!</p>\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 521 8851', 'tiit.papp@gmail.com', 3, 'Samantha Jones', '2024-09-20 23:43:59', '2024-11-24 23:09:12', 1);
INSERT INTO `events_calendar` VALUES (42, 6, 345, 4, 'Kolmas kalender', 2, 'Kalenderplaan', 2722, NULL, 'Blaaaa voootttt', 'Foto: Tiit Papp', 2024, 'Loeng', '/kolmas-kalender/2024/loeng', 'Tallinna kurtide klubis, Nõmme tee 2', '2024-09-30', NULL, '17:30:00', NULL, NULL, '<p>Blaaaaa</p>\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 521 8851', 'tiit.papp@gmail.com', 3, 'Samantha Jones', '2024-09-20 23:46:11', '2024-11-24 23:10:32', 2);
INSERT INTO `events_calendar` VALUES (43, NULL, 345, 4, 'Kolmas kalender', 2, 'Kalenderplaan', 2753, NULL, NULL, NULL, 2024, 'Pensionäride kokkutulek', '/kolmas-kalender/2024/pensionaride-kokkutulek', 'Rakvere linnuses', '2024-06-07', NULL, '11:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tiit Papp', '+372 1234 5678', 'ead@ead.ee', 3, 'Samantha Jones', '2024-09-28 10:14:44', '2024-11-24 23:09:58', 1);
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
) ENGINE=InnoDB;

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
  `title` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `events_chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7;

-- ----------------------------
-- Records of events_changes
-- ----------------------------
BEGIN;
INSERT INTO `events_changes` VALUES (4, 'Uuendatud', '2024-09-22 16:40:09', '2024-09-23 13:03:39', 1);
INSERT INTO `events_changes` VALUES (5, 'Täiendatud', '2024-09-22 16:40:30', '2024-09-23 13:03:44', 1);
INSERT INTO `events_changes` VALUES (6, 'Edasi lükatud', '2024-09-22 16:40:53', '2024-09-23 18:19:55', 1);
COMMIT;

-- ----------------------------
-- Table structure for events_settings
-- ----------------------------
DROP TABLE IF EXISTS `events_settings`;
CREATE TABLE `events_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=5;

-- ----------------------------
-- Records of events_settings
-- ----------------------------
BEGIN;
INSERT INTO `events_settings` VALUES (1, 'Sündmuste kalender', 'Sündmuste kalender', 1, 1, 336, '2024-09-18 16:00:00', '2024-11-24 23:08:10', 1);
INSERT INTO `events_settings` VALUES (4, 'Kolmas kalender', NULL, 1, 1, 345, '2024-10-09 00:57:27', '2024-11-24 23:09:12', 1);
COMMIT;

-- ----------------------------
-- Table structure for example
-- ----------------------------
DROP TABLE IF EXISTS `example`;
CREATE TABLE `example` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3;

-- ----------------------------
-- Records of example
-- ----------------------------
BEGIN;
INSERT INTO `example` VALUES (1, '<h2>Midagi alustuseks</h2>\n', 1121, '');
INSERT INTO `example` VALUES (2, '<p><img alt=\"\" id=\"1593\" src=\"/qcubed-4/project/tmp/_files/thumbnail/Konventeerimine/karikakrad_vihmas.jpg\" style=\"float:left; height:217px; margin:5px 10px; width:320px\" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie. Duis velit augue,</p>\n', NULL, '1593');
COMMIT;

-- ----------------------------
-- Table structure for files
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `description` text,
  `extension` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int DEFAULT NULL,
  `mtime` int DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `locked_file` int unsigned DEFAULT '0',
  `activities_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE,
  KEY `folder_id_idx` (`folder_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2808;

-- ----------------------------
-- Records of files
-- ----------------------------
BEGIN;
INSERT INTO `files` VALUES (500, 1, '9.Kurtide_kultuur_Lilli_ja_Triin-1-1.ppt', 'file', '/9.Kurtide_kultuur_Lilli_ja_Triin-1-1.ppt', NULL, 'ppt', 'application/vnd.ms-powerpoint', 812544, 1707925609, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (501, 1, 'Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1.pdf', 'file', '/Eesti viipekeel 10. Teabepäev Tartus 17.12.2018-1.pdf', NULL, 'pdf', 'application/pdf', 286801, 1706914786, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (754, 929, 'seinakell.jpg', 'file', '/Varia/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1700646067, '611 x 404', 611, 404, 0, 0);
INSERT INTO `files` VALUES (755, 929, 'sirlu.jpg', 'file', '/Varia/sirlu.jpg', NULL, 'jpg', 'image/jpeg', 49122, 1700646067, '450 x 600', NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (756, 929, 'sp2_fotologs_net.jpg', 'file', '/Varia/sp2_fotologs_net.jpg', NULL, 'jpg', 'image/jpeg', 17070, 1700646067, '500 x 375', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (1121, 1, 'Kolletanud lehed maas.jpg', 'file', '/Kolletanud lehed maas.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1710772062, '900 x 585', NULL, NULL, 6, 0);
INSERT INTO `files` VALUES (1134, 1, 'EKL aruanne.xlsx', 'file', '/EKL aruanne.xlsx', NULL, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 244192, 1704465357, NULL, NULL, NULL, 11, 0);
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
INSERT INTO `files` VALUES (1694, 1026, 'crop_seinakell.png', 'file', '/crop-test/crop_seinakell.png', NULL, 'png', 'image/png', 120716, 1719520181, '611 x 228', 611, 228, 1, 0);
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
INSERT INTO `files` VALUES (2559, 1105, 'Tiit mõtleb.jpg', 'file', '/pildigalerii/tiidu-album/Tiit mõtleb.jpg', NULL, 'jpg', 'image/jpeg', 720646, 1726584089, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2561, 1105, 'f_DSC01660.jpg', 'file', '/pildigalerii/tiidu-album/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1725026026, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2562, 1105, 'karikakrad_vihmas.jpg', 'file', '/pildigalerii/tiidu-album/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1725026026, '1280 x 868', 1280, 868, 1, 1);
INSERT INTO `files` VALUES (2565, 1105, 'Pildistamisel.jpg', 'file', '/pildigalerii/tiidu-album/Pildistamisel.jpg', NULL, 'jpg', 'image/jpeg', 878514, 1726584118, '3072 x 2304', 3072, 2304, 1, 1);
INSERT INTO `files` VALUES (2567, 1105, 'Luik.jpg', 'file', '/pildigalerii/tiidu-album/Luik.jpg', NULL, 'jpg', 'image/jpeg', 820580, 1726584102, '2953 x 1918', 2953, 1918, 1, 1);
INSERT INTO `files` VALUES (2569, 1106, '310596090_1482278652270764_6161734453730055725_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310596090_1482278652270764_6161734453730055725_n.jpeg', NULL, 'jpeg', 'image/jpeg', 271363, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2570, 1106, '310625658_5771591356213069_6130322049604942068_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310625658_5771591356213069_6130322049604942068_n.jpeg', NULL, 'jpeg', 'image/jpeg', 231678, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2571, 1106, '310651429_413903317415234_1877068238628190472_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310651429_413903317415234_1877068238628190472_n.jpeg', NULL, 'jpeg', 'image/jpeg', 168905, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2572, 1106, '310986468_785287795913568_6096172368795184477_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/310986468_785287795913568_6096172368795184477_n.jpeg', NULL, 'jpeg', 'image/jpeg', 256565, 1725039577, '2000 x 1126', 2000, 1126, 1, 1);
INSERT INTO `files` VALUES (2573, 1106, '311163895_800320941296129_7328794715150918241_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311163895_800320941296129_7328794715150918241_n.jpeg', NULL, 'jpeg', 'image/jpeg', 176036, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2574, 1106, '311271898_5500936233356667_4481537757649627936_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311271898_5500936233356667_4481537757649627936_n.jpeg', NULL, 'jpeg', 'image/jpeg', 232041, 1725039577, '1120 x 2000', 1120, 2000, 1, 1);
INSERT INTO `files` VALUES (2575, 1106, '311451979_627793208998847_3710757790573382164_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311451979_627793208998847_3710757790573382164_n.jpeg', NULL, 'jpeg', 'image/jpeg', 147897, 1725039577, '1200 x 1600', 1200, 1600, 1, 1);
INSERT INTO `files` VALUES (2576, 1106, '311464218_606307097952705_2986433564733245675_n.jpeg', 'file', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022/311464218_606307097952705_2986433564733245675_n.jpeg', NULL, 'jpeg', 'image/jpeg', 264296, 1725039577, '2000 x 1126', 2000, 1126, 1, 1);
INSERT INTO `files` VALUES (2595, 1110, '403617_297643386939380_307791209_n.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/403617_297643386939380_307791209_n.jpg', NULL, 'jpg', 'image/jpeg', 72044, 1725101111, '960 x 768', 960, 768, 1, 1);
INSERT INTO `files` VALUES (2596, 1110, '6954421-christmas-lights.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/6954421-christmas-lights.jpg', NULL, 'jpg', 'image/jpeg', 1583745, 1725101111, '2560 x 1600', 2560, 1600, 1, 1);
INSERT INTO `files` VALUES (2597, 1110, '2078524051_ed4de415ef_o.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/2078524051_ed4de415ef_o.jpg', NULL, 'jpg', 'image/jpeg', 301663, 1725101111, '800 x 536', 800, 536, 1, 1);
INSERT INTO `files` VALUES (2598, 1110, '2094750459_7e05256e05_o.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/2094750459_7e05256e05_o.jpg', NULL, 'jpg', 'image/jpeg', 147720, 1725101111, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2599, 1110, 'Bnowchristmas_1600x1200.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Bnowchristmas_1600x1200.jpg', NULL, 'jpg', 'image/jpeg', 417297, 1725101111, '1600 x 1200', 1600, 1200, 1, 1);
INSERT INTO `files` VALUES (2600, 1110, 'Cartoon-Christmas-house-background-02-vector-material-20608.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Cartoon-Christmas-house-background-02-vector-material-20608.jpg', NULL, 'jpg', 'image/jpeg', 55655, 1725101111, '600 x 465', 600, 465, 1, 1);
INSERT INTO `files` VALUES (2601, 1110, 'Christmas_candles_by_SizkaS.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_candles_by_SizkaS.jpg', NULL, 'jpg', 'image/jpeg', 304103, 1725101111, '700 x 468', 700, 468, 1, 1);
INSERT INTO `files` VALUES (2602, 1110, 'Christmas_Greetings_2009.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Greetings_2009.jpg', NULL, 'jpg', 'image/jpeg', 353985, 1725101111, '800 x 536', 800, 536, 1, 1);
INSERT INTO `files` VALUES (2603, 1110, 'Christmas_Wallpaper_Snowman_Snow.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas_Wallpaper_Snowman_Snow.jpg', NULL, 'jpg', 'image/jpeg', 66529, 1725101111, '1600 x 1200', 1600, 1200, 1, 1);
INSERT INTO `files` VALUES (2605, 1110, 'christmas-2618263_1280.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2618263_1280.jpg', NULL, 'jpg', 'image/jpeg', 178822, 1725101112, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2606, 1110, 'christmas-2877141_1280.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-2877141_1280.jpg', NULL, 'jpg', 'image/jpeg', 253363, 1725101112, '1280 x 853', 1280, 853, 1, 1);
INSERT INTO `files` VALUES (2607, 1110, 'Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/Christmas-HQ-wallpapers-christmas-2768066-1600-1000.jpg', NULL, 'jpg', 'image/jpeg', 200233, 1725101112, '1600 x 1000', 1600, 1000, 1, 1);
INSERT INTO `files` VALUES (2608, 1110, 'christmas-night-magic-house.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-night-magic-house.jpg', NULL, 'jpg', 'image/jpeg', 141329, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2609, 1110, 'christmas-wallpapers-backgrounds.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers-backgrounds.jpg', NULL, 'jpg', 'image/jpeg', 142281, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2610, 1110, 'christmas-wallpapers.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/christmas-wallpapers.jpg', NULL, 'jpg', 'image/jpeg', 79595, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2611, 1110, 'ChristmasCandlelightss1.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ChristmasCandlelightss1.jpg', NULL, 'jpg', 'image/jpeg', 76705, 1725101112, '800 x 600', 800, 600, 1, 1);
INSERT INTO `files` VALUES (2612, 1110, 'ehted_pky.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ehted_pky.jpg', NULL, 'jpg', 'image/jpeg', 49971, 1725101112, '960 x 772', 960, 772, 1, 1);
INSERT INTO `files` VALUES (2613, 1110, 'ekl_jolukaart_2015.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_jolukaart_2015.jpg', NULL, 'jpg', 'image/jpeg', 299740, 1725101112, '720 x 501', 720, 501, 1, 1);
INSERT INTO `files` VALUES (2615, 1110, 'ekl_joulukaart_2012.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2012.jpg', NULL, 'jpg', 'image/jpeg', 464952, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2618, 1110, 'ekl_joulukaart_2013.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2013.jpg', NULL, 'jpg', 'image/jpeg', 625113, 1725101112, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2621, 1110, 'ekl_joulukaart_2016.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2016.jpg', NULL, 'jpg', 'image/jpeg', 401329, 1725101112, '800 x 372', 800, 372, 1, 1);
INSERT INTO `files` VALUES (2622, 1110, 'ekl_joulukaart_2021.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/ekl_joulukaart_2021.jpg', NULL, 'jpg', 'image/jpeg', 423608, 1725101112, '960 x 768', 960, 768, 1, 1);
INSERT INTO `files` VALUES (2641, 929, 'margus_raud.jpg', 'file', '/Varia/margus_raud.jpg', NULL, 'jpg', 'image/jpeg', 254427, 1725900510, '1365 x 2048', 1365, 2048, 1, 0);
INSERT INTO `files` VALUES (2642, 929, 'crop_margus_raud.png', 'file', '/Varia/crop_margus_raud.png', NULL, 'png', 'image/png', 581210, 1725900551, '624 x 623', 624, 623, 0, 0);
INSERT INTO `files` VALUES (2667, 933, 'DSC_0084.JPG', 'file', '/Avaleht/DSC_0084.JPG', NULL, 'jpg', 'image/jpeg', 2817798, 1726072956, '3008 x 2000', 3008, 2000, 0, 0);
INSERT INTO `files` VALUES (2668, 933, 'DSC_7550.jpg', 'file', '/Avaleht/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1726072956, '2953 x 1918', 2953, 1918, 0, 0);
INSERT INTO `files` VALUES (2669, 933, 'file60471593_d5a21f14.jpg', 'file', '/Avaleht/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726072957, '900 x 585', 900, 585, 0, 0);
INSERT INTO `files` VALUES (2670, 933, 'f_DSC01660.jpg', 'file', '/Avaleht/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726072957, '2500 x 1667', 2500, 1667, 0, 0);
INSERT INTO `files` VALUES (2671, 1078, 'IMG_0875.jpeg', 'file', '/Uudised/Uudised 2024/IMG_0875.jpeg', NULL, 'jpeg', 'image/jpeg', 1547525, 1726073722, '4032 x 3024', 4032, 3024, 1, 0);
INSERT INTO `files` VALUES (2672, 1078, 'ilus_vanavarav_looduses.jpg', 'file', '/Uudised/Uudised 2024/ilus_vanavarav_looduses.jpg', NULL, 'jpg', 'image/jpeg', 488115, 1726073766, '1024 x 683', 1024, 683, 1, 0);
INSERT INTO `files` VALUES (2673, 1078, 'karikakrad_vihmas.jpg', 'file', '/Uudised/Uudised 2024/karikakrad_vihmas.jpg', NULL, 'jpg', 'image/jpeg', 602670, 1726073766, '1280 x 868', 1280, 868, 0, 0);
INSERT INTO `files` VALUES (2674, 1078, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/Uudised/Uudised 2024/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1726073766, '1024 x 768', 1024, 768, 1, 0);
INSERT INTO `files` VALUES (2675, 1078, 'r 175.jpg', 'file', '/Uudised/Uudised 2024/r 175.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1726073766, '3072 x 2304', 3072, 2304, 0, 0);
INSERT INTO `files` VALUES (2676, 1078, 'crop_karikakrad_vihmas.png', 'file', '/Uudised/Uudised 2024/crop_karikakrad_vihmas.png', NULL, 'png', 'image/png', 1012954, 1726074085, '868 x 868', 868, 868, 0, 0);
INSERT INTO `files` VALUES (2677, 1078, 'crop_f_DSC01660.png', 'file', '/Uudised/Uudised 2024/crop_f_DSC01660.png', NULL, 'png', 'image/png', 138405, 1726074904, '328 x 327', 328, 327, 1, 0);
INSERT INTO `files` VALUES (2678, 1078, 'crop_r 175.png', 'file', '/Uudised/Uudised 2024/crop_r 175.png', NULL, 'png', 'image/png', 2940897, 1726075068, '1276 x 1275', 1276, 1275, 0, 0);
INSERT INTO `files` VALUES (2679, 1078, 'vanavarav_uudiseks.png', 'file', '/Uudised/Uudised 2024/vanavarav_uudiseks.png', NULL, 'png', 'image/png', 164293, 1726075181, '495 x 151', 495, 151, 1, 0);
INSERT INTO `files` VALUES (2680, 1078, 'vilinus reis 2263.jpg', 'file', '/Uudised/Uudised 2024/vilinus reis 2263.jpg', NULL, 'jpg', 'image/jpeg', 1138428, 1726075313, '1936 x 1288', 1936, 1288, 1, 0);
INSERT INTO `files` VALUES (2681, 933, 'crop_DSC_7550.png', 'file', '/Avaleht/crop_DSC_7550.png', NULL, 'png', 'image/png', 1408605, 1726079729, '936 x 937', 936, 937, 0, 0);
INSERT INTO `files` VALUES (2712, 1, 'galerii67681.jpg', 'file', '/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1728053575, '800 x 533', NULL, NULL, 5, 0);
INSERT INTO `files` VALUES (2721, 1111, 'f_DSC01660.jpg', 'file', '/TEST/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726152151, '2500 x 1667', NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2722, 1111, 'file60471593_d5a21f14.jpg', 'file', '/TEST/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726152152, '900 x 585', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2726, 1111, 'crop_DSC_5177_1.png', 'file', '/TEST/crop_DSC_5177_1.png', NULL, 'png', 'image/png', 670209, 1726153669, '670 x 670', 670, 670, 1, 0);
INSERT INTO `files` VALUES (2727, 1111, 'crop_Tiit_Papp_töölaua_taga.png', 'file', '/TEST/crop_Tiit_Papp_töölaua_taga.png', NULL, 'png', 'image/png', 111514, 1730493216, '288 x 289', 288, 289, 0, 0);
INSERT INTO `files` VALUES (2728, 1111, 'Tiit_Papp_2021.jpg', 'file', '/TEST/Tiit_Papp_2021.jpg', NULL, 'jpg', 'image/jpeg', 275742, 1726153789, '1200 x 1600', NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2729, 1110, 'f_DSC01660.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726155638, '2500 x 1667', 2500, 1667, 1, 1);
INSERT INTO `files` VALUES (2730, 1110, 'file60471593_d5a21f14.jpg', 'file', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726155638, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2733, 1112, 'raamatud.jpg', 'file', '/pildigalerii/uus-album/raamatud.jpg', NULL, 'jpg', 'image/jpeg', 17729, 1726157947, '228 x 270', 228, 270, 1, 1);
INSERT INTO `files` VALUES (2734, 1112, 'rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', 'file', '/pildigalerii/uus-album/rahvuslill_ja_mesilind-_m6lemad_eesti_rahvale_armsad.jpg', NULL, 'jpg', 'image/jpeg', 511879, 1726157947, '1024 x 768', 1024, 768, 1, 1);
INSERT INTO `files` VALUES (2735, 1112, 'r 175.jpg', 'file', '/pildigalerii/uus-album/r 175.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1726157947, '3072 x 2304', 3072, 2304, 1, 1);
INSERT INTO `files` VALUES (2736, 1112, 'rukkilill.jpg', 'file', '/pildigalerii/uus-album/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1726157948, '2288 x 1712', 2288, 1712, 1, 1);
INSERT INTO `files` VALUES (2737, 1112, 'seinakell.jpg', 'file', '/pildigalerii/uus-album/seinakell.jpg', NULL, 'jpg', 'image/jpeg', 34102, 1726157948, '611 x 404', 611, 404, 1, 1);
INSERT INTO `files` VALUES (2738, 1112, 'vilinus reis 2263.jpg', 'file', '/pildigalerii/uus-album/vilinus reis 2263.jpg', NULL, 'jpg', 'image/jpeg', 1138428, 1726157948, '1936 x 1288', 1936, 1288, 1, 1);
INSERT INTO `files` VALUES (2739, 1112, 'valentinikyynlas.JPG', 'file', '/pildigalerii/uus-album/valentinikyynlas.JPG', NULL, 'jpg', 'image/jpeg', 2381402, 1726157948, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2740, 1111, 'logo-sliderUi.svg', 'file', '/TEST/logo-sliderUi.svg', NULL, 'svg', 'image/svg+xml', 690, 1726216629, NULL, NULL, NULL, 2, 0);
INSERT INTO `files` VALUES (2741, 1111, '02262060750a05.jpg', 'file', '/TEST/02262060750a05.jpg', NULL, 'jpg', 'image/jpeg', 47856, 1726410055, '720 x 359', 720, 359, 0, 0);
INSERT INTO `files` VALUES (2743, 1038, 'langenud lehed pargis.jpg', 'file', '/galerii/blaaa/langenud lehed pargis.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1726421709, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2744, 1038, 'kolletanud_vahtralehed.jpg', 'file', '/galerii/blaaa/kolletanud_vahtralehed.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1726421709, '1600 x 1064', 1600, 1064, 0, 1);
INSERT INTO `files` VALUES (2745, 1038, 'f_DSC01660.jpg', 'file', '/galerii/blaaa/f_DSC01660.jpg', NULL, 'jpg', 'image/jpeg', 932107, 1726421709, '2500 x 1667', 2500, 1667, 0, 1);
INSERT INTO `files` VALUES (2746, 1038, 'file60471593_d5a21f14.jpg', 'file', '/galerii/blaaa/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1726421709, '900 x 585', 900, 585, 0, 1);
INSERT INTO `files` VALUES (2747, 1038, 'rukkilill.jpg', 'file', '/galerii/blaaa/rukkilill.jpg', NULL, 'jpg', 'image/jpeg', 3135190, 1726421709, '2288 x 1712', 2288, 1712, 0, 1);
INSERT INTO `files` VALUES (2749, 1038, 'Tiit pildistab.jpg', 'file', '/galerii/blaaa/Tiit pildistab.jpg', NULL, 'jpg', 'image/jpeg', 6121394, 1726421709, '3072 x 2304', 3072, 2304, 0, 1);
INSERT INTO `files` VALUES (2751, 1078, 'sinine_teletorn.jpg', 'file', '/Uudised/Uudised 2024/sinine_teletorn.jpg', NULL, 'jpg', 'image/jpeg', 47345, 1727266275, '720 x 960', 720, 960, 0, 0);
INSERT INTO `files` VALUES (2753, 1078, 'crop_sinine_teletorn.png', 'file', '/Uudised/Uudised 2024/crop_sinine_teletorn.png', NULL, 'png', 'image/png', 104367, 1727267563, '637 x 295', 637, 295, 4, 0);
INSERT INTO `files` VALUES (2754, 1120, '18.A.Ojastuauhinjuhend2017.pdf', 'file', '/spordialad/kergejoustik/juhendid/18.A.Ojastuauhinjuhend2017.pdf', NULL, 'pdf', 'application/pdf', 201732, 1727867442, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2755, 1120, '2013 EKSL sisekj_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2756, 1120, '2013 EKSL sisekj_juhend1.pdf', 'file', '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend1.pdf', NULL, 'pdf', 'application/pdf', 120378, 1727867442, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2757, 1120, 'Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', 'file', '/spordialad/kergejoustik/juhendid/Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', NULL, 'pdf', 'application/pdf', 471776, 1727867442, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2758, 1120, 'EKSL MV juhend2013 kergej.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2013 kergej.pdf', NULL, 'pdf', 'application/pdf', 173725, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2759, 1120, 'EKSL MV juhend2014 kergej 310514.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV juhend2014 kergej 310514.pdf', NULL, 'pdf', 'application/pdf', 276908, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2760, 1120, 'EKSL MV KJ  juhend 2018.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL MV KJ  juhend 2018.pdf', NULL, 'pdf', 'application/pdf', 109552, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2761, 1120, 'EKSL_kergejõustiku_MV_juhend_2012.pdf', 'file', '/spordialad/kergejoustik/juhendid/EKSL_kergejõustiku_MV_juhend_2012.pdf', NULL, 'pdf', 'application/pdf', 72509, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2762, 1120, 'EPKMVkergej_15062019_juhend.pdf', 'file', '/spordialad/kergejoustik/juhendid/EPKMVkergej_15062019_juhend.pdf', NULL, 'pdf', 'application/pdf', 131368, 1727867443, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2763, 1120, 'epok-kergej-mv-juhend-2016_OapQlwhK.pdf', 'file', '/spordialad/kergejoustik/juhendid/epok-kergej-mv-juhend-2016_OapQlwhK.pdf', NULL, 'pdf', 'application/pdf', 519683, 1727867443, NULL, NULL, NULL, 1, 0);
INSERT INTO `files` VALUES (2764, 1121, '2012_EKSL_MV_protkergej 260512.pdf', 'file', '/spordialad/kergejoustik/tulemused/2012_EKSL_MV_protkergej 260512.pdf', NULL, 'pdf', 'application/pdf', 276151, 1727867694, NULL, NULL, NULL, 0, 0);
INSERT INTO `files` VALUES (2777, 1123, 'DSC_0008.JPG', 'file', '/pildigalerii/tanugala-2024/DSC_0008.JPG', NULL, 'jpg', 'image/jpeg', 2432573, 1728566869, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2778, 1123, 'allkiri.png', 'file', '/pildigalerii/tanugala-2024/allkiri.png', NULL, 'png', 'image/png', 156790, 1729464816, '300 x 300', 300, 300, 1, 1);
INSERT INTO `files` VALUES (2779, 1123, 'DSC_0084.JPG', 'file', '/pildigalerii/tanugala-2024/DSC_0084.JPG', NULL, 'jpg', 'image/jpeg', 2817798, 1728566869, '3008 x 2000', 3008, 2000, 1, 1);
INSERT INTO `files` VALUES (2780, 1123, 'DSC_5197_1.jpg', 'file', '/pildigalerii/tanugala-2024/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1728566870, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2781, 1123, 'DSC_5177_1.jpg', 'file', '/pildigalerii/tanugala-2024/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1728566870, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2782, 1123, 'DSC_7550.jpg', 'file', '/pildigalerii/tanugala-2024/DSC_7550.jpg', NULL, 'jpg', 'image/jpeg', 3966308, 1729540924, '2953 x 1918', 2953, 1918, 1, 1);
INSERT INTO `files` VALUES (2784, 1123, 'seebimullid.jpg', 'file', '/pildigalerii/tanugala-2024/seebimullid.jpg', NULL, 'jpg', 'image/jpeg', 493210, 1730293680, '1024 x 873', 1024, 873, 1, 1);
INSERT INTO `files` VALUES (2785, 1123, 'kuldnokk puuladvas.jpg', 'file', '/pildigalerii/tanugala-2024/kuldnokk puuladvas.jpg', NULL, 'jpg', 'image/jpeg', 454624, 1730293737, '1024 x 683', 1024, 683, 1, 1);
INSERT INTO `files` VALUES (2788, 1123, 'file60471593_d5a21f14.jpg', 'file', '/pildigalerii/tanugala-2024/file60471593_d5a21f14.jpg', NULL, 'jpg', 'image/jpeg', 76862, 1729791911, '900 x 585', 900, 585, 1, 1);
INSERT INTO `files` VALUES (2789, 1123, 'galerii67681.jpg', 'file', '/pildigalerii/tanugala-2024/galerii67681.jpg', NULL, 'jpg', 'image/jpeg', 245964, 1729791911, '800 x 533', 800, 533, 1, 1);
INSERT INTO `files` VALUES (2790, 1105, 'DSC_5197_1.jpg', 'file', '/pildigalerii/tiidu-album/DSC_5197_1.jpg', NULL, 'jpg', 'image/jpeg', 256192, 1729792962, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2791, 1105, 'DSC_5177_1.jpg', 'file', '/pildigalerii/tiidu-album/DSC_5177_1.jpg', NULL, 'jpg', 'image/jpeg', 268402, 1729792963, '1600 x 1064', 1600, 1064, 1, 1);
INSERT INTO `files` VALUES (2794, 1, 'kurtide_liidu_maja_2013.jpg', 'file', '/kurtide_liidu_maja_2013.jpg', NULL, 'jpg', 'image/jpeg', 770166, 1729795447, '2048 x 1362', 2048, 1362, 1, 0);
INSERT INTO `files` VALUES (2795, 1126, 'Helle_Sass.png', 'file', '/Juhatus/2018-2023/Helle_Sass.png', NULL, 'png', 'image/png', 655482, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2796, 1126, 'Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/Janis_Golubenkov.png', NULL, 'png', 'image/png', 755152, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2797, 1126, 'Mati_Kartus.png', 'file', '/Juhatus/2018-2023/Mati_Kartus.png', NULL, 'png', 'image/png', 696185, 1730573593, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2798, 1126, 'Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/Riina_Kuusk.png', NULL, 'png', 'image/png', 711437, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2799, 1126, 'Sirle_Papp.png', 'file', '/Juhatus/2018-2023/Sirle_Papp.png', NULL, 'png', 'image/png', 642962, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2800, 1126, 'Tiit_Papp.png', 'file', '/Juhatus/2018-2023/Tiit_Papp.png', NULL, 'png', 'image/png', 619869, 1730567901, '568 x 850', 568, 850, 0, 0);
INSERT INTO `files` VALUES (2801, 1126, 'crop_Helle_Sass.png', 'file', '/Juhatus/2018-2023/crop_Helle_Sass.png', NULL, 'png', 'image/png', 387605, 1730567977, '558 x 559', 558, 559, 1, 0);
INSERT INTO `files` VALUES (2802, 1126, 'crop_Janis_Golubenkov.png', 'file', '/Juhatus/2018-2023/crop_Janis_Golubenkov.png', NULL, 'png', 'image/png', 340410, 1730568004, '550 x 549', 550, 549, 1, 0);
INSERT INTO `files` VALUES (2803, 1126, 'crop_Mati_Kartus.png', 'file', '/Juhatus/2018-2023/crop_Mati_Kartus.png', NULL, 'png', 'image/png', 360208, 1730573579, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2804, 1126, 'crop_Riina_Kuusk.png', 'file', '/Juhatus/2018-2023/crop_Riina_Kuusk.png', NULL, 'png', 'image/png', 411006, 1730568055, '550 x 550', 550, 550, 1, 0);
INSERT INTO `files` VALUES (2805, 1126, 'crop_Sirle_Papp.png', 'file', '/Juhatus/2018-2023/crop_Sirle_Papp.png', NULL, 'png', 'image/png', 359834, 1730568080, '550 x 550', 550, 550, 3, 0);
INSERT INTO `files` VALUES (2806, 1126, 'crop_Tiit_Papp.png', 'file', '/Juhatus/2018-2023/crop_Tiit_Papp.png', NULL, 'png', 'image/png', 357732, 1730568106, '540 x 540', 540, 540, 2, 0);
INSERT INTO `files` VALUES (2807, 1111, 'crop_Tiit_Papp_2021.png', 'file', '/TEST/crop_Tiit_Papp_2021.png', NULL, 'png', 'image/png', 1513373, 1731281307, '1102 x 1102', 1102, 1102, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for folders
-- ----------------------------
DROP TABLE IF EXISTS `folders`;
CREATE TABLE `folders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `path` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(5) DEFAULT NULL,
  `mtime` int DEFAULT NULL,
  `locked_file` int unsigned DEFAULT '0',
  `activities_locked` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1127;

-- ----------------------------
-- Records of folders
-- ----------------------------
BEGIN;
INSERT INTO `folders` VALUES (1, NULL, '/', 'Repository', 'dir', 1729795420, 1, 0);
INSERT INTO `folders` VALUES (923, 1, '/Organisatsioon', 'Organisatsioon', 'dir', 1710165414, 1, 0);
INSERT INTO `folders` VALUES (929, 1, '/Varia', 'Varia', 'dir', 1725900551, 1, 0);
INSERT INTO `folders` VALUES (933, 1, '/Avaleht', 'Avaleht', 'dir', 1728055017, 1, 0);
INSERT INTO `folders` VALUES (935, 933, '/Avaleht/test 2', 'test 2', 'dir', 1726071766, 0, 0);
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
INSERT INTO `folders` VALUES (1021, 933, '/Avaleht/test', 'test', 'dir', 1724702584, 1, 0);
INSERT INTO `folders` VALUES (1026, 1, '/crop-test', 'crop-test', 'dir', 1725148715, 1, 0);
INSERT INTO `folders` VALUES (1038, 989, '/galerii/blaaa', 'Blaaa', 'dir', 1726421709, 1, 1);
INSERT INTO `folders` VALUES (1077, 1, '/Uudised', 'Uudised', 'dir', 1724399541, 1, 0);
INSERT INTO `folders` VALUES (1078, 1077, '/Uudised/Uudised 2024', 'Uudised 2024', 'dir', 1727267563, 1, 0);
INSERT INTO `folders` VALUES (1101, 1, '/pildigalerii', 'Pildigalerii', 'dir', 1726157912, 1, 1);
INSERT INTO `folders` VALUES (1105, 1101, '/pildigalerii/tiidu-album', 'Tiidu album', 'dir', 1724664207, 1, 1);
INSERT INTO `folders` VALUES (1106, 1124, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', 'dir', 1725026203, 1, 1);
INSERT INTO `folders` VALUES (1110, 1101, '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024', 'Spordiliidu juubeli tähistamine 31.12.2024', 'dir', 1725101014, 1, 1);
INSERT INTO `folders` VALUES (1111, 1, '/TEST', 'TEST', 'dir', 1731281307, 1, 0);
INSERT INTO `folders` VALUES (1112, 1101, '/pildigalerii/uus-album', 'Uus album', 'dir', 1726157912, 1, 1);
INSERT INTO `folders` VALUES (1113, 1101, '/pildigalerii/tiidu-album-1', 'Tiidu album', 'dir', 1726510522, 1, 1);
INSERT INTO `folders` VALUES (1114, 1, '/spordialad', 'spordialad', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1118, 1114, '/spordialad/kergejoustik', 'kergejoustik', 'dir', 1727867052, 1, 0);
INSERT INTO `folders` VALUES (1119, 1118, '/spordialad/kergejoustik/ajakavad', 'ajakavad', 'dir', 1727867077, 0, 0);
INSERT INTO `folders` VALUES (1120, 1118, '/spordialad/kergejoustik/juhendid', 'juhendid', 'dir', 1727866923, 1, 0);
INSERT INTO `folders` VALUES (1121, 1118, '/spordialad/kergejoustik/tulemused', 'tulemused', 'dir', 1727867651, 1, 0);
INSERT INTO `folders` VALUES (1123, 1101, '/pildigalerii/tanugala-2024', 'Tänugala 2024', 'dir', 1728565538, 1, 1);
INSERT INTO `folders` VALUES (1124, 1, '/kogukonna-galerii', 'Kogukonna galerii', 'dir', 1729540333, 1, 1);
INSERT INTO `folders` VALUES (1125, 1, '/Juhatus', 'Juhatus', 'dir', 1730567842, 1, 0);
INSERT INTO `folders` VALUES (1126, 1125, '/Juhatus/2018-2023', '2018-2023', 'dir', 1730568106, 1, 0);
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
  `frontend_class_name` varchar(255) DEFAULT NULL,
  `frontend_template_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `frontend_title_slug` varchar(255) DEFAULT NULL,
  `is_activated` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `content_types_managament_id_idx` (`content_types_managament_id`) USING BTREE,
  KEY `is_activated_idx` (`is_activated`) USING BTREE,
  KEY `linked_id_idx` (`linked_id`) USING BTREE,
  KEY `grouped_id_idx` (`grouped_id`) USING BTREE,
  CONSTRAINT `content_types_managament_id_frontend_links_fk` FOREIGN KEY (`content_types_managament_id`) REFERENCES `content_types_management` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `is_activated_idx` FOREIGN KEY (`is_activated`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=349;

-- ----------------------------
-- Records of frontend_links
-- ----------------------------
BEGIN;
INSERT INTO `frontend_links` VALUES (42, 40, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev', 1);
INSERT INTO `frontend_links` VALUES (61, 45, 329, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Analüütik: rändesurve Leedule võib olla Valgevene pettemanööver', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024', 1);
INSERT INTO `frontend_links` VALUES (132, 62, NULL, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Toiduhinnad on stabiliseerumas, kuid endisi hindu enam ei tule', '/poliitika-uudised/toiduhinnad-on-stabiliseerumas-kuid-endisi-hindu-enam-ei-tule', 1);
INSERT INTO `frontend_links` VALUES (135, 247, NULL, 3, 'StandardNewsListController', 'StandardNewsListController.tpl.php', 'Kultuuri uudised', '/kultuuri-uudised', 1);
INSERT INTO `frontend_links` VALUES (157, 69, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Teeme uue katse', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (158, 70, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Kolmas katse', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (161, 75, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Neljas katse', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (162, 76, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Kolmas katse', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (164, 299, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Eesti Kurtide Liidu põhikiri', '/status/eesti-kurtide-liidu-pohikiri', 1);
INSERT INTO `frontend_links` VALUES (175, 78, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Venemaa ründas julmalt Ukraina lastehaiglat', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (176, 79, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Ukraina õhujõud ründasid Kurskis Vene väerühma baasi', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (177, 80, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'ISW: Wagneri Malis kantud kaotused võivad mõjutada rinnet Ukrainas', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (182, 313, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Organisatsiooni kontaktandmed', '/contact/organisatsiooni-kontaktandmed', 1);
INSERT INTO `frontend_links` VALUES (202, 325, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Pildiarhiiv', '/pildiarhiiv', 1);
INSERT INTO `frontend_links` VALUES (204, 13, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'EKSL sisekergejõustiku võistlused', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (205, 14, 310, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Teeme suure albumi', '/pildigalerii/teeme-suure-albumi/galerii63462.jpg', 1);
INSERT INTO `frontend_links` VALUES (248, 329, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Pildigalerii', '/pildigalerii', 1);
INSERT INTO `frontend_links` VALUES (252, 40, 329, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Tiidu album', '/pildiarhiiv/tiidu-album', 1);
INSERT INTO `frontend_links` VALUES (255, 41, 363, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 1);
INSERT INTO `frontend_links` VALUES (260, 45, 330, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Spordiliidu juubeli tähistamine 31.12.2024', '/pildiarhiiv/spordiliidu-juubeli-tahistamine-31-12-2024', 1);
INSERT INTO `frontend_links` VALUES (261, 87, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'kairiti uus uudis', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (266, 43, 345, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Pensionäride kokkutulek', '/kolmas-kalender/2024/pensionaride-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (267, 46, 329, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Uus album', '/pildigalerii/uus-album', 1);
INSERT INTO `frontend_links` VALUES (268, 47, 329, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Tiidu album', '/pildigalerii/tiidu-album-1', 1);
INSERT INTO `frontend_links` VALUES (269, 336, NULL, 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 'Sündmuste kalender', '/sundmuste-kalender', 1);
INSERT INTO `frontend_links` VALUES (272, 38, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev', '/sundmuste-kalender/2024/kurtide-paev-1', 1);
INSERT INTO `frontend_links` VALUES (273, 39, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/sundmuste-kalender/2024/naistepaev', 1);
INSERT INTO `frontend_links` VALUES (274, 40, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kurtide päev', '/sundmuste-kalender/2024/pensionaride-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (275, 41, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Naistepäev', '/sundmuste-kalender/2024/pensionaride-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (276, 42, 345, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Loeng', '/kolmas-kalender/2024/loeng', 1);
INSERT INTO `frontend_links` VALUES (277, 337, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 'Spordikalender', '/spordikalender', 1);
INSERT INTO `frontend_links` VALUES (278, 1, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maavõistlused', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (279, 338, NULL, 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 'Spordisündmuste kalender', '/spordisundmuste-kalender', 1);
INSERT INTO `frontend_links` VALUES (282, 4, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maavõistlused Tartus', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (286, 8, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Kergejõustikuvõistlused', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (288, 10, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Blaaa', '/spordisundmuste-kalender/2024/blaaa', 1);
INSERT INTO `frontend_links` VALUES (289, 11, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (290, 12, 338, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Maahoki', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (291, 13, 338, 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 'Jäähoki', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 1);
INSERT INTO `frontend_links` VALUES (292, 43, 336, 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 'Pensionäride kokkutulek', '/sundmuste-kalender/2024/pensionaride-kokkutulek', 1);
INSERT INTO `frontend_links` VALUES (293, 1, NULL, 1, 'CustomHomeController', 'CustomHomeController.tpl.php', '/', '/', 1);
INSERT INTO `frontend_links` VALUES (295, 342, NULL, 11, 'SportsAreasController', 'SportsAreasController.tpl.php', 'Spordialad', '/spordialad', 1);
INSERT INTO `frontend_links` VALUES (296, 345, NULL, 7, NULL, NULL, 'Kolmas kalender', '/kolmas-kalender', 1);
INSERT INTO `frontend_links` VALUES (297, 33, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Vaatame teist korda uuesti?', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (299, 48, 329, 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 'Tänugala 2024', '/pildigalerii/tanugala-2024', 1);
INSERT INTO `frontend_links` VALUES (300, 90, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Eesti otsib võimalust Ukrainalt relvastuse hankimiseks', '/poliitika-uudised/blaaa', 1);
INSERT INTO `frontend_links` VALUES (304, 91, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'BLAAA', '/poliitika-uudised/blaaa', 1);
INSERT INTO `frontend_links` VALUES (305, 348, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Kaasautor', '/kaasautorlus/vaatame-kaasautorlust', 1);
INSERT INTO `frontend_links` VALUES (310, 353, NULL, 2, 'StandardArticleController', 'StandardArticleController.tpl.php', 'Böööö', '/blaaa/boooo', 1);
INSERT INTO `frontend_links` VALUES (321, 363, NULL, 5, 'StandardGalleryListController', 'StandardGalleryListController.tpl.php', 'Kogukonna galerii', '/kogukonna-galerii', 1);
INSERT INTO `frontend_links` VALUES (322, 93, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Arles on uudishimulik', '/poliitika-uudised/arles-on-uudishimulik', 1);
INSERT INTO `frontend_links` VALUES (323, 366, NULL, 3, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', '/juhatus', 1);
INSERT INTO `frontend_links` VALUES (324, 30, 50, 4, 'StandardNewsDetailController', 'StandardNewsDetailController.tpl.php', 'Neljas katse', '/poliitika-uudised/kairiti-uus-uudis', 1);
INSERT INTO `frontend_links` VALUES (325, 367, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (326, 368, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (327, 369, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (328, 370, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (329, 371, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (330, 372, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (331, 373, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (332, 374, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (333, 375, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (334, 376, NULL, 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', NULL, 2);
INSERT INTO `frontend_links` VALUES (335, 377, NULL, 3, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Juhatus', '/juhatus', 1);
INSERT INTO `frontend_links` VALUES (336, 378, NULL, 3, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Kultuuri juhatus', '/kultuuri-juhatus', 1);
INSERT INTO `frontend_links` VALUES (337, 379, NULL, 3, 'StandardBoardController', 'StandardBoardController.tpl.php', 'Spordi juhatus', '/spordi-juhatus', 1);
INSERT INTO `frontend_links` VALUES (345, 392, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Liikmesühingud', '/liikmesuhingud', 1);
INSERT INTO `frontend_links` VALUES (346, 393, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordiseltsid', '/spordiseltsid', 1);
INSERT INTO `frontend_links` VALUES (347, 394, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Kultuuriseltsid', '/kultuuriseltsid', 1);
INSERT INTO `frontend_links` VALUES (348, 396, NULL, 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 'Spordiseltsid', '/spordiseltsid', 1);
COMMIT;

-- ----------------------------
-- Table structure for frontend_options
-- ----------------------------
DROP TABLE IF EXISTS `frontend_options`;
CREATE TABLE `frontend_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `frontend_template_name` varchar(255) NOT NULL,
  `content_types_management_id` int unsigned NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `frontend_template_path` varchar(255) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=22;

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
INSERT INTO `frontend_options` VALUES (7, 'Events calendar list (standard))', 7, 'StandardEventsCalendarListController', 'StandardEventsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (8, 'Events calendar detail (standard)', 8, 'StandardEventsCalendarDetailController', 'StandardEventsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (9, 'Sports calendar list (standard)', 9, 'StandardSportsCalendarListController', 'StandardSportsCalendarListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (10, 'Sports calendar detail (standard)', 10, 'StandardSportsCalendarDetailController', 'StandardSportsCalendarDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (11, 'Sports areas detail (standard)', 11, 'SportsAreasController', 'SportsAreasController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (12, 'Board detail (standard)', 12, 'StandardBoardController', 'StandardBoardController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (13, 'Members detail (standard)', 13, 'StandardMembersController', 'StandardMembersController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (14, 'Home (custom)', 1, 'CustomHomeController', 'CustomHomeController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (15, 'Article detail (custom)', 2, 'CustomArticleController', 'CustomArticleController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (16, 'Sports areas detail (custom)', 11, 'CustomSportsAreasController', 'CustomSportsAreasController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (17, 'News list (custom)', 3, 'CustomNewsListController', 'CustomNewsListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (18, 'News detail (custom)', 4, 'CustomNewsDetailController', 'CustomNewsDetailController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (19, 'Gallery list (custom)', 5, 'CustomGalleryListController', 'CustomGalleryListController.tpl.php', 1);
INSERT INTO `frontend_options` VALUES (20, 'Gallery detail (custom)', 6, 'CustomGalleryDetailController', 'CustomGalleryDetailController.tpl.php', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=14;

-- ----------------------------
-- Records of frontend_template_locking
-- ----------------------------
BEGIN;
INSERT INTO `frontend_template_locking` VALUES (2, 2);
INSERT INTO `frontend_template_locking` VALUES (3, 3);
INSERT INTO `frontend_template_locking` VALUES (4, 4);
INSERT INTO `frontend_template_locking` VALUES (5, 5);
INSERT INTO `frontend_template_locking` VALUES (7, 7);
INSERT INTO `frontend_template_locking` VALUES (8, 8);
INSERT INTO `frontend_template_locking` VALUES (9, 9);
INSERT INTO `frontend_template_locking` VALUES (10, 10);
INSERT INTO `frontend_template_locking` VALUES (11, 11);
INSERT INTO `frontend_template_locking` VALUES (12, 12);
INSERT INTO `frontend_template_locking` VALUES (13, 13);
INSERT INTO `frontend_template_locking` VALUES (1, 14);
INSERT INTO `frontend_template_locking` VALUES (6, 20);
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
  `name` varchar(255) DEFAULT NULL,
  `path` text,
  `description` text,
  `author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=252;

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
INSERT INTO `galleries` VALUES (245, 38, 34, 1038, 2743, 'langenud lehed pargis.jpg', '/galerii/blaaa/langenud lehed pargis.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-09-15 20:35:09');
INSERT INTO `galleries` VALUES (246, 38, 34, 1038, 2744, 'kolletanud_vahtralehed.jpg', '/galerii/blaaa/kolletanud_vahtralehed.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-09-15 20:35:09');
INSERT INTO `galleries` VALUES (247, 38, 34, 1038, 2745, 'f_DSC01660.jpg', '/galerii/blaaa/f_DSC01660.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-09-15 20:35:09');
INSERT INTO `galleries` VALUES (248, 38, 34, 1038, 2746, 'file60471593_d5a21f14.jpg', '/galerii/blaaa/file60471593_d5a21f14.jpg', NULL, NULL, 1, '2024-09-15 20:22:29', '2024-09-15 20:35:09');
INSERT INTO `galleries` VALUES (249, 38, 34, 1038, 2747, 'rukkilill.jpg', '/galerii/blaaa/rukkilill.jpg', NULL, NULL, 1, '2024-09-15 20:26:23', '2024-09-15 20:35:09');
INSERT INTO `galleries` VALUES (251, 38, 34, 1038, 2749, 'Tiit pildistab.jpg', '/galerii/blaaa/Tiit pildistab.jpg', NULL, NULL, 1, '2024-09-15 20:26:23', '2024-09-15 20:35:09');
COMMIT;

-- ----------------------------
-- Table structure for gallery_list
-- ----------------------------
DROP TABLE IF EXISTS `gallery_list`;
CREATE TABLE `gallery_list` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gallery_group_title_id` int unsigned DEFAULT NULL,
  `group_title` varchar(255) DEFAULT NULL,
  `parent_folder_id` int unsigned DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `photo_author` varchar(255) DEFAULT NULL,
  `photo_description` text,
  `path` text,
  `title_slug` varchar(255) DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
  CONSTRAINT `gallery_group_title_id_menu_content_ibfk` FOREIGN KEY (`gallery_group_title_id`) REFERENCES `gallery_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_folder_id_ibfk` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_parent_folder_id_ibfk` FOREIGN KEY (`parent_folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_list_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_gallery_list_ibfk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49;

-- ----------------------------
-- Records of gallery_list
-- ----------------------------
BEGIN;
INSERT INTO `gallery_list` VALUES (40, 17, 'Pildigalerii', 1101, 1105, 'Tiidu album', '', '', '/pildigalerii/tiidu-album', '/pildigalerii/tiidu-album', 2, 'Alex Smith', 3, '2024-08-26 12:23:27', '2024-11-17 20:52:30');
INSERT INTO `gallery_list` VALUES (41, 21, 'Kogukonna galerii', 1124, 1106, 'Lääne-Virumaa KÜ üldkoosolek 15.10.2022', NULL, NULL, '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', '/kogukonna-galerii/lääne-virumaa-kü-uldkoosolek-15-10-2022', 2, 'Alex Smith', 2, '2024-08-30 16:56:43', '2024-11-28 00:08:05');
INSERT INTO `gallery_list` VALUES (45, 17, 'Pildigalerii', 1101, 1110, 'Spordiliidu juubeli tähistamine 31.12.2024', NULL, NULL, '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024', '/pildigalerii/spordiliidu-juubeli-tahistamine-31-12-2024', 2, 'Alex Smith', 1, '2024-08-31 13:43:34', '2024-11-17 20:46:00');
INSERT INTO `gallery_list` VALUES (46, 17, 'Pildigalerii', 1101, 1112, 'Uus album', NULL, NULL, '/pildigalerii/uus-album', '/pildigalerii/uus-album', 2, 'Alex Smith', 1, '2024-09-12 19:18:32', '2024-11-17 20:46:00');
INSERT INTO `gallery_list` VALUES (47, 17, 'Pildigalerii', 1101, 1113, 'Tiidu album', '', '', '/pildigalerii/tiidu-album-1', '/pildigalerii/tiidu-album-1', 2, 'Alex Smith', 2, '2024-09-16 21:15:22', '2024-11-17 20:46:00');
INSERT INTO `gallery_list` VALUES (48, 17, 'Pildigalerii', 1101, 1123, 'Tänugala 2024', '', '', '/pildigalerii/tanugala-2024', '/pildigalerii/tanugala-2024', 3, 'Samantha Jones', 1, '2024-10-10 16:05:38', '2024-11-17 20:51:58');
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
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=22;

-- ----------------------------
-- Records of gallery_settings
-- ----------------------------
BEGIN;
INSERT INTO `gallery_settings` VALUES (17, 329, 1, 1, 1101, 'Pildigalerii', 'Pildigalerii', '/pildigalerii', '/pildigalerii', '2024-08-25 14:13:22', '2024-11-24 22:13:55', 1);
INSERT INTO `gallery_settings` VALUES (21, 363, 1, 1, 1124, 'Kogukonna galerii', NULL, '/kogukonna-galerii', '/kogukonna-galerii', '2024-10-21 20:56:28', '2024-11-17 20:45:59', 1);
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
) ENGINE=InnoDB;

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
COMMIT;

-- ----------------------------
-- Table structure for items_per_page
-- ----------------------------
DROP TABLE IF EXISTS `items_per_page`;
CREATE TABLE `items_per_page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `items_per` varchar(3) NOT NULL,
  `items_per_num` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5;

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
  `name` varchar(45) NOT NULL,
  `code` varchar(3) NOT NULL,
  `locale` varchar(5) NOT NULL,
  `is_active` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_active_idx` (`is_active`) USING BTREE,
  CONSTRAINT `is_active_fk` FOREIGN KEY (`is_active`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4;

-- ----------------------------
-- Records of language
-- ----------------------------
BEGIN;
INSERT INTO `language` VALUES (1, 'Estonian', 'et', 'et_EE', 1);
INSERT INTO `language` VALUES (2, 'English', 'en', 'en_US', 1);
INSERT INTO `language` VALUES (3, 'Russian', 'ru', 'ru_RU', 1);
COMMIT;

-- ----------------------------
-- Table structure for list_of_galleries
-- ----------------------------
DROP TABLE IF EXISTS `list_of_galleries`;
CREATE TABLE `list_of_galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int unsigned DEFAULT NULL,
  `folder_id` int unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `path` text,
  `title_slug` varchar(255) DEFAULT NULL,
  `list_description` text,
  `list_author` varchar(255) DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `album_id_idx` (`album_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  CONSTRAINT `album_id_albums_ibfk` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `status_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35;

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
INSERT INTO `list_of_galleries` VALUES (34, 38, 1038, 'Blaaa', '/galerii/blaaa', 'blaaa', NULL, NULL, 1, '2024-08-01 00:57:16', '2024-09-15 20:35:09');
COMMIT;

-- ----------------------------
-- Table structure for list_of_sliders
-- ----------------------------
DROP TABLE IF EXISTS `list_of_sliders`;
CREATE TABLE `list_of_sliders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3;

-- ----------------------------
-- Records of list_of_sliders
-- ----------------------------
BEGIN;
INSERT INTO `list_of_sliders` VALUES (1, 'Sponsors', 1, 1, '2024-03-06 22:26:00', '2024-09-14 21:43:21');
INSERT INTO `list_of_sliders` VALUES (2, 'Advertising', 1, 1, '2024-03-07 21:24:41', '2024-10-11 21:28:10');
COMMIT;

-- ----------------------------
-- Table structure for login
-- ----------------------------
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int unsigned DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_login_2` (`username`),
  UNIQUE KEY `IDX_login_1` (`person_id`),
  CONSTRAINT `person_login` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6;

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
  `file_path` varchar(255) DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `member_id` int unsigned DEFAULT NULL,
  `member_id_title` varchar(255) DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `member_name` varchar(255) DEFAULT NULL,
  `registry_code` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `representative_fullname` varchar(255) DEFAULT NULL,
  `representative_telephone` varchar(255) DEFAULT NULL,
  `representative_sms` varchar(255) DEFAULT NULL,
  `representative_fax` varchar(255) DEFAULT NULL,
  `representative_email` varchar(255) DEFAULT NULL,
  `description` text,
  `telephone` varchar(255) DEFAULT NULL,
  `sms` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `members_number` varchar(255) DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `member_id_idx` (`member_id`) USING BTREE,
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6;

-- ----------------------------
-- Records of members
-- ----------------------------
BEGIN;
INSERT INTO `members` VALUES (3, NULL, NULL, NULL, 10, 'Spordiseltsid', 0, 'Tallinna Kurtide Spordiselts TALKUR', '80044916', NULL, 'Edgar Liim', NULL, '+372 567 12067', NULL, NULL, NULL, NULL, NULL, '+372 601 5361', 'Nõmme tee 2\nTallinn 13426', 'talkur93@gmail.com', 'https://talkur.ee', '75', 1, '2024-11-12 11:33:00', '2024-11-14 09:08:26');
INSERT INTO `members` VALUES (4, NULL, NULL, NULL, 10, 'Spordiseltsid', 1, 'Pärnu Kurtide Spordiselts EERO', '80042975', NULL, 'Eero Pevkur', NULL, '+372 565 03052', NULL, NULL, NULL, NULL, NULL, '+372 442 7131', 'Lubja 48a\nPärnu 80010', 'ksseero@gmail.com', 'http://eero.onepagefree.com', '', 1, '2024-11-12 11:48:33', '2024-11-14 08:10:18');
INSERT INTO `members` VALUES (5, NULL, NULL, NULL, 10, 'Spordiseltsid', 2, 'Tartu Kurtide Spordiselts KAAR', '80037661', NULL, 'Jaan-Raul Ojastu', NULL, '+372 585 44757', NULL, NULL, NULL, NULL, NULL, '', 'Suur-Kaar 56\nTartu 50404', 'kaaresport@kaaresport.ee', 'https://www.kaaresport.ee', '', 1, '2024-11-14 08:10:55', '2024-11-14 09:08:52');
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
) ENGINE=InnoDB;

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
  `name` varchar(255) DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `activity_status` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`activity_status`) USING BTREE,
  KEY `members_settings_id_idx` (`settings_id`) USING BTREE,
  CONSTRAINT `activity_status_ibfk` FOREIGN KEY (`activity_status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `members_settings_id_ibfk` FOREIGN KEY (`settings_id`) REFERENCES `members_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98;

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
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT '2',
  `status` int unsigned DEFAULT '2',
  `menu_content_id` int unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=11;

-- ----------------------------
-- Records of members_settings
-- ----------------------------
BEGIN;
INSERT INTO `members_settings` VALUES (10, 'Spordiseltsid', 'Eesti Kurtide Spordiliidu liikmesseltsid', 1, 1, 396, '/spordiseltsid', '/spordiseltsid', '2024-11-12 11:19:25', '2024-11-24 20:56:59', 1, 'John Doe', 1, 2);
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (42, NULL, 0, 4, 15);
INSERT INTO `menu` VALUES (47, NULL, 0, 24, 25);
INSERT INTO `menu` VALUES (50, 315, 1, 19, 20);
INSERT INTO `menu` VALUES (247, 315, 1, 17, 18);
INSERT INTO `menu` VALUES (283, 315, 1, 21, 22);
INSERT INTO `menu` VALUES (299, 42, 1, 13, 14);
INSERT INTO `menu` VALUES (313, 42, 1, 5, 6);
INSERT INTO `menu` VALUES (315, NULL, 0, 16, 23);
INSERT INTO `menu` VALUES (329, NULL, 0, 26, 27);
INSERT INTO `menu` VALUES (336, NULL, 0, 30, 31);
INSERT INTO `menu` VALUES (337, NULL, 0, 34, 35);
INSERT INTO `menu` VALUES (338, NULL, 0, 36, 37);
INSERT INTO `menu` VALUES (342, NULL, 0, 32, 33);
INSERT INTO `menu` VALUES (345, NULL, 0, 28, 29);
INSERT INTO `menu` VALUES (352, NULL, 0, 38, 39);
INSERT INTO `menu` VALUES (353, NULL, 0, 40, 41);
INSERT INTO `menu` VALUES (363, NULL, 0, 42, 43);
INSERT INTO `menu` VALUES (377, 42, 1, 7, 8);
INSERT INTO `menu` VALUES (378, 42, 1, 9, 10);
INSERT INTO `menu` VALUES (379, 42, 1, 11, 12);
INSERT INTO `menu` VALUES (380, NULL, 0, 44, 45);
INSERT INTO `menu` VALUES (396, NULL, 0, 46, 47);
INSERT INTO `menu` VALUES (397, NULL, 0, 48, 49);
COMMIT;

-- ----------------------------
-- Table structure for menu_content
-- ----------------------------
DROP TABLE IF EXISTS `menu_content`;
CREATE TABLE `menu_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int unsigned DEFAULT NULL,
  `menu_text` varchar(255) NOT NULL,
  `content_type` int unsigned DEFAULT NULL,
  `group_title_id` int unsigned DEFAULT NULL,
  `gallery_title_id` int unsigned DEFAULT NULL,
  `events_title_id` int unsigned DEFAULT NULL,
  `sports_title_id` int unsigned DEFAULT NULL,
  `boards_title_id` int unsigned DEFAULT NULL,
  `members_title_id` int unsigned DEFAULT NULL,
  `videos_title_id` int unsigned DEFAULT NULL,
  `statistics_title_id` int unsigned DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  `homely_url` int unsigned DEFAULT NULL,
  `is_redirect` int unsigned DEFAULT NULL,
  `selected_page_id` int unsigned DEFAULT NULL,
  `selected_page_locked` int DEFAULT '0',
  `target_type` int unsigned DEFAULT NULL,
  `is_enabled` int DEFAULT '0',
  `title_exists` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `content_type_idx` (`content_type`) USING BTREE,
  KEY `target_type_idx` (`target_type`) USING BTREE,
  KEY `selected_page_id_idx` (`selected_page_id`) USING BTREE,
  KEY `group_title_id_idx` (`group_title_id`) USING BTREE,
  KEY `gallery_title_id_idx` (`gallery_title_id`) USING BTREE,
  KEY `events_title_id_idx` (`events_title_id`) USING BTREE,
  KEY `sports_title_id_idx` (`sports_title_id`) USING BTREE,
  KEY `boards_title_id_idx` (`boards_title_id`) USING BTREE,
  KEY `members_title_id_idx` (`members_title_id`) USING BTREE,
  KEY `videos_title_id_idx` (`videos_title_id`) USING BTREE,
  KEY `statistics_title_id_idx` (`statistics_title_id`) USING BTREE,
  CONSTRAINT `boards_title_id_menu_content_fk` FOREIGN KEY (`boards_title_id`) REFERENCES `boards_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `content_type_menu_content_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `events_title_id_menu_content_fk` FOREIGN KEY (`events_title_id`) REFERENCES `events_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gallery_title_id_menu_content_fk` FOREIGN KEY (`gallery_title_id`) REFERENCES `gallery_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_title_id_menu_content_fk` FOREIGN KEY (`group_title_id`) REFERENCES `news_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_id_menu_content_fk` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `selected_page_id_fk` FOREIGN KEY (`selected_page_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_title_id_menu_content_fk` FOREIGN KEY (`sports_title_id`) REFERENCES `sports_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target_type_menu_content_fk` FOREIGN KEY (`target_type`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=398;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Homepage', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (42, 42, 'Organisation', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '#', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (47, 47, 'QCubed arenduse koduleht', 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://qcubed.eu', NULL, 1, NULL, 0, 1, 1, 1);
INSERT INTO `menu_content` VALUES (50, 50, 'Poliitika uudised', 3, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/poliitika-uudised', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (247, 247, 'Kultuuri uudised', 3, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/kultuuri-uudised', 1, NULL, NULL, 1, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (283, 283, 'Spordiuudised', 3, 46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/spordiuudised', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (299, 299, 'Status', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/status/eesti-kurtide-liidu-pohikiri', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (313, 313, 'Contact', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/contact/organisatsiooni-kontaktandmed', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (315, 315, 'Uudised', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/kultuuri-uudised', 1, 2, 247, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (329, 329, 'Pildigalerii', 4, NULL, 17, NULL, NULL, NULL, NULL, NULL, NULL, '/pildigalerii', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (336, 336, 'Sündmuste kalender', 5, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '/sundmuste-kalender', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (337, 337, 'Spordikalender', 6, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '/spordikalender', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (338, 338, 'Spordisündmuste kalender', 6, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '/spordisundmuste-kalender', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (342, 342, 'Spordialad', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/spordialad', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (345, 345, 'Kolmas kalender', 5, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, '/kolmas-kalender', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (352, 352, 'Kaasautorlus', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/kaasautorlus/vaatame-kaasautorlust', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (353, 353, 'BLAAA', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/blaaa/boooo', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (363, 363, 'Kogukonna galerii', 4, NULL, 21, NULL, NULL, NULL, NULL, NULL, NULL, '/kogukonna-galerii', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (377, 377, 'Juhatus', 11, NULL, NULL, NULL, NULL, 7, NULL, NULL, NULL, '/juhatus', 1, NULL, NULL, 1, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (378, 378, 'Kultuuri juhatus', 11, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, '/kultuuri-juhatus', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (379, 379, 'Spordi juhatus', 11, NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, '/spordi-juhatus', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (380, 380, 'Suunamine juhatusele', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/juhatus', 1, 2, 377, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (396, 396, 'Spordiseltsid', 12, NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, '/spordiseltsid', 1, NULL, NULL, 0, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (397, 397, 'Blöööö', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 2, 0);
COMMIT;

-- ----------------------------
-- Table structure for metadata
-- ----------------------------
DROP TABLE IF EXISTS `metadata`;
CREATE TABLE `metadata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `keywords` text,
  `description` text,
  `author` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  CONSTRAINT `menu_content_id_metadata_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=169;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, 'Avalehe võtmesõnad', 'Avalehe kirjeldus', 'Kodulehe autor');
INSERT INTO `metadata` VALUES (41, 50, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (74, 247, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (81, 283, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (91, 299, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (105, 313, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (118, 329, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (120, 336, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (121, 337, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (122, 338, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (124, 342, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (125, 345, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (132, 352, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (133, 353, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (143, 363, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (155, 377, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (156, 378, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (157, 379, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (168, 396, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for milestone
-- ----------------------------
DROP TABLE IF EXISTS `milestone`;
CREATE TABLE `milestone` (
  `id` int unsigned NOT NULL,
  `project_id` int unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_milestoneproj_1` (`project_id`),
  CONSTRAINT `project_milestone` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB;

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
  `group_title` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `news_category_id` int unsigned DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) DEFAULT NULL,
  `picture_description` text,
  `author_source` varchar(255) DEFAULT NULL,
  `content` text,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint unsigned DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=94;

-- ----------------------------
-- Records of news
-- ----------------------------
BEGIN;
INSERT INTO `news` VALUES (30, 2, 50, 18, 'Poliitika uudised', 'Neljas katse', 17, 'Politics', '/poliitika-uudised/neljas-katse', 2794, '', '', 'FOTO: EKL|Tiit Papp', '', '2024-10-30 18:18:14', '2024-11-24 21:43:06', 0, NULL, NULL, 1, 'John Doe', 2, 0);
INSERT INTO `news` VALUES (33, NULL, 50, 18, 'Poliitika uudised', 'Vaatame teist korda uuesti?', NULL, NULL, '/poliitika-uudised/vaatame-teist-korda-uuesti', 2677, '', NULL, NULL, '<p>Tiidu t&ouml;&ouml;tuba</p>\n', '2024-10-11 21:29:06', '2024-11-24 22:03:24', 0, NULL, NULL, 1, 'John Doe', 1, 0);
INSERT INTO `news` VALUES (40, NULL, 50, 18, 'Poliitika uudised', 'Viies katse', NULL, NULL, '/poliitika-uudised/viies-katse', 2671, '', NULL, NULL, '<p>EKL juhatuse vanad liikmed astuvad tagasi...</p>\n\n<p>&nbsp;</p>\n', '2024-10-11 21:29:06', '2024-10-11 21:29:06', 0, NULL, NULL, 1, 'John Doe', 1, 0);
INSERT INTO `news` VALUES (43, NULL, 50, 18, 'Poliitika uudised', 'Viies katse', NULL, NULL, '/poliitika-uudised/viies-katse-1', 2672, '', NULL, NULL, NULL, '2024-10-11 21:29:06', '2024-10-11 21:29:06', 0, NULL, NULL, 1, 'John Doe', 1, 0);
INSERT INTO `news` VALUES (45, NULL, 50, 18, 'Poliitika uudised', 'Analüütik: rändesurve Leedule võib olla Valgevene pettemanööver', NULL, NULL, '/poliitika-uudised/analuutik-randesurve-leedule-voib-olla-valgevene-pettemanoover', NULL, '', NULL, NULL, '<p>Rahvusvahelise kaitseuuringute keskuse anal&uuml;&uuml;tiku Tomas Jermalaviciuse hinnangul v&otilde;ib Leedule avaldatav r&auml;ndesurve olla Valgevene petteman&ouml;&ouml;ver, et juhtida Euroopa t&auml;helepanu muudelt piirkondadelt mujale.</p>\n\n<p>Esmasp&auml;evased kaadrid Leedu piirialalt Rudninkai p&otilde;genikelaagrist hakkavad muutuma juba Leedu igap&auml;evaks. &Uuml;hiskonna rahulolematus, radikaliseerumine, sisepoliitiline kriis ja riigi v&auml;ljakurnamine on Valgevene h&uuml;briidr&uuml;nnaku esimesed sihid, vahendas &quot;Aktuaalne kaamera&quot;.</p>\n', '2021-08-03 22:24:03', '2024-08-23 16:57:02', 0, NULL, NULL, 1, 'John Doe', 2, 0);
INSERT INTO `news` VALUES (62, NULL, 50, 18, 'Poliitika uudised', 'Toiduhinnad on stabiliseerumas, kuid endisi hindu enam ei tule', NULL, NULL, '/poliitika-uudised/toiduhinnad-on-stabiliseerumas-kuid-endisi-hindu-enam-ei-tule', NULL, '1694', NULL, NULL, '<h2><img alt=\"\" id=\"1694\" src=\"/qcubed-4/project/tmp/_files/thumbnail/crop-test/crop_seinakell.png\" style=\"float:left; height:82px; margin-left:10px; margin-right:10px; width:220px\" /> M&ouml;&ouml;dunud aasta oli oma rekordilise hinnat&otilde;usuga erakordne nii tootjatele, jaekettidele kui ka tarbijatele. K&otilde;ige hullemad hinnat&otilde;usud on m&ouml;&ouml;das, kuid stabiliseerumine kestab umbes s&uuml;giseni ning endisi toiduhindu me poes enam ei n&auml;e, t&otilde;devad nii tootjad kui ka m&uuml;&uuml;jad.</h2>\n\n<p>Aastaga on toidukorv nii palju kallimaks l&auml;inud, et see sunnib tarbijat oma valikuid &uuml;mber hindama. Kuigi elektri- ja k&uuml;tusehinnad on taas odavamaks l&auml;inud, uuris &quot;Aktuaalne kaamera. N&auml;dal&quot;, millal toit taas odavamaks l&auml;heb.</p>\n\n<p>Toidukorv on aastaga kallinenud keskmiselt 20 protsenti. Ent kaubagrupiti on hinnat&otilde;usud olnud v&auml;ga erinevad. Suhkur, toidu&otilde;li, teravili, liha- ja piimatooted on kallinenud palju rohkem.</p>\n\n<p>Rimi ostujuhi Talis Raagi s&otilde;nul ei ole ilmselt kaubagruppe, kus aasta jooksul hind t&otilde;usnud pole. &quot;Tahaks &ouml;elda, et on, aga ma ei tea k&uuml;ll &uuml;htegi n&auml;idet, nii et ma pigem &uuml;tlen, et vist ei ole,&quot; t&otilde;des ta.</p>\n', '2024-10-11 19:27:49', '2024-11-24 22:03:12', 1, '2024-11-30 00:00:00', NULL, 1, 'John Doe', 4, 0);
INSERT INTO `news` VALUES (69, NULL, 50, 18, 'Poliitika uudised', 'Teeme uue katse', NULL, NULL, '/poliitika-uudised/teeme-uue-katse', NULL, '', NULL, NULL, NULL, '2024-07-02 15:30:27', '2024-07-02 15:31:00', 0, NULL, NULL, 2, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (70, NULL, 50, 18, 'Poliitika uudised', 'Kolmas katse', NULL, NULL, '/poliitika-uudised/kolmas-katse', NULL, '', NULL, NULL, NULL, '2024-07-02 17:38:08', NULL, 0, NULL, NULL, 2, 'Alex Smith', 2, 0);
INSERT INTO `news` VALUES (75, NULL, 50, 18, 'Poliitika uudised', 'Neljas katse', NULL, NULL, '/poliitika-uudised/neljas-katse-1', NULL, '', NULL, NULL, NULL, '2024-07-02 20:41:43', '2024-07-03 22:17:24', 0, NULL, NULL, 2, 'Alex Smith', 2, 0);
INSERT INTO `news` VALUES (76, NULL, 50, 18, 'Poliitika uudised', 'Kolmas katse', NULL, NULL, '/poliitika-uudised/kolmas-katse-1', NULL, '', NULL, NULL, NULL, '2024-07-02 20:42:45', '2024-07-06 19:29:21', 0, NULL, NULL, 2, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (78, NULL, 50, 18, 'Poliitika uudised', 'Venemaa ründas julmalt Ukraina lastehaiglat', NULL, NULL, '/poliitika-uudised/venemaa-rundas-julmalt-ukraina-lastehaiglat', NULL, '', NULL, NULL, NULL, '2024-07-30 13:19:28', '2024-08-23 16:57:38', 0, NULL, NULL, 2, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (79, NULL, 50, 18, 'Poliitika uudised', 'Ukraina õhujõud ründasid Kurskis Vene väerühma baasi', NULL, NULL, '/poliitika-uudised/ukraina-ohujoud-rundasid-kurskis-vene-vaeruhma-baasi', NULL, '', 'Ukraina sõdurid õppustel Suurbritannias', 'SCANPIX/AFP/JUSTIN TALLIS', '<p>Ukraina &otilde;huj&otilde;ud v&otilde;tsid sihikule Kurski oblastis asuva Venemaa v&auml;er&uuml;hma baasi ning kasutasid r&uuml;nnakus &Uuml;hendriikides valmistatud t&auml;ppispomme GBU-39.</p>\n\n<p><strong>Oluline reedel, 23. augustil kell 11.07:</strong></p>\n\n<p><em><strong>- Ukraina &otilde;huj&otilde;ud r&uuml;ndasid Kurskis Vene v&auml;er&uuml;hma baasi;</strong></em></p>\n\n<p><em><strong>- Modi alustas visiiti Ukrainasse;</strong></em></p>\n\n<p><em><strong>- M&otilde;ttekoja juht: Ukraina operatsioon Kurski oblastis r&otilde;hutab l&auml;&auml;ne vajadust teha rohkem.</strong></em></p>\n\n<p><strong>Ukraina &otilde;huj&otilde;ud r&uuml;ndasid Kurskis Vene v&auml;er&uuml;hma baasi</strong></p>\n\n<p>Ukraina &otilde;huj&otilde;ud v&otilde;tsid sihikule Kurski oblastis asuva Venemaa v&auml;er&uuml;hma baasi ning kasutasid r&uuml;nnakus &Uuml;hendriikides valmistatud t&auml;ppispomme GBU-39.</p>\n\n<p>Informatsiooni kinnitas &otilde;huj&otilde;udude &uuml;lem kindralleitnant M&otilde;kola Ole&scaron;t&scaron;uk. Tema andmetel tabasid &otilde;huj&otilde;ud muu hulgas droonide juhtimiskeskust, elektroonilise s&otilde;japidamise &uuml;ksust ning s&otilde;javarustust</p>\n\n<p><strong>M&otilde;ttekoja juht: Ukraina operatsioon Kurski oblastis r&otilde;hutab l&auml;&auml;ne vajadust teha rohkem</strong></p>\n\n<p>Ukraina operatsioon Venemaa Kurski oblastis jahmatas Moskvat ja r&otilde;hutab samas l&auml;&auml;ne vajadust teha rohkem, hindas neljap&auml;eval Ukraina parlamendi endine liige ja Atlandi-&uuml;lese v&auml;lispoliitika m&otilde;ttekoja Henry Jackson Society tegevdirektor Alena Glivko.</p>\n\n<p>Glivko s&otilde;nul valmistab Kreml elanikkonda juba ette selleks, et sissetung v&otilde;ib veel n&auml;dalaid kesta.</p>\n\n<p>&quot;Ja siiski on oluline tunnistada, et operatsiooni s&otilde;jaline edukus on endiselt v&auml;gagi nii&ouml;elda kaalukausil. Kuigi on t&otilde;endeid selle kohta, et m&otilde;ned Vene v&auml;ed paigutatakse Ukrainast sissetungi vastu v&otilde;itlemiseks &uuml;mber, siis j&auml;tkub Moskva pealetung Donbassis raugematult. See on murettekitav m&auml;rk, kuna strateegiline sabotaaž on Ukraina jaoks peamine sihtm&auml;rk,&quot; kirjutas Glivko.</p>\n', '2024-07-30 13:23:44', '2024-08-23 12:45:39', 0, NULL, NULL, 2, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (80, NULL, 50, 18, 'Poliitika uudised', 'ISW: Wagneri Malis kantud kaotused võivad mõjutada rinnet Ukrainas', NULL, NULL, '/poliitika-uudised/isw-wagneri-malis-kantud-kaotused-voivad-mojutada-rinnet-ukrainas', NULL, '', NULL, NULL, '<p>Wagneri r&uuml;hmituse Vene v&otilde;itlejad Malis kandsid hiljuti kolossaalseid kaotusi ja n&uuml;&uuml;d vajab Kreml Aafrikas ulatuslikku asendust, mis t&auml;hendaks Venemaa kaitseministeeriumi Aafrika Korpuse s&otilde;durite &auml;ra viimist Harkivi oblasti rindealalt, vahendas Unian Ameerika s&otilde;jauuringute instituudi aruannet.</p>\n\n<p>Osa Aafrika korpusest saadeti hiljuti Ukraina Harkivi oblastisse toetama okupatsiooniv&auml;gede r&uuml;ndej&otilde;udu.</p>\n\n<p>ISW andmetel v&auml;idavad mitmed kriitilised Vene s&otilde;jablogijad, et kaitseministeerium pigem r&otilde;&otilde;mustab wagnerlaste kaotuste &uuml;le ning v&otilde;ib kasutada olukorda selleks, et asendada nende tegevus Saheli piirkonnas Aafrika Korpusega.</p>\n\n<p>Blogijad on seejuures viidanud anon&uuml;&uuml;mseks j&auml;&auml;nud allikatele Venemaa administratsioonis, kes on selliseid v&otilde;imalikke muutusi kinnitanud. Sealjuures eeldatakse, et v&otilde;itlejad vahetatakse v&auml;lja kogu nn Saheli kolmikus &ndash; nii Malis, Nigeris kui ka Burkina Fasos.</p>\n\n<p>ISW hinnangul puudub aga Aafrika Korpusel praegu v&otilde;imalus wagnerlaste t&auml;ielikuks v&auml;ljavahetamiseks, eriti Malis. P&otilde;hjus v&otilde;ib peituda selles, et osa Aafrika korpusest suunati hiljuti Harkivi oblastisse ning n&uuml;&uuml;d on tekkimas olukord, kus nad v&otilde;idakse Ukrainast &uuml;ldse &auml;ra viia.</p>\n', '2024-07-30 15:16:35', '2024-07-31 00:12:37', 0, NULL, NULL, 2, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (87, NULL, 50, 18, 'Poliitika uudised', 'kairiti uus uudis', NULL, NULL, '/poliitika-uudised/kairiti-uus-uudis', NULL, '', NULL, NULL, NULL, '2024-08-31 13:58:20', '2024-08-31 14:01:27', 0, NULL, NULL, 2, 'Alex Smith', 2, 0);
INSERT INTO `news` VALUES (90, NULL, 50, 18, 'Poliitika uudised', 'Eesti otsib võimalust Ukrainalt relvastuse hankimiseks', NULL, NULL, '/poliitika-uudised/eesti-otsib-voimalust-ukrainalt-relvastuse-hankimiseks', 2679, NULL, NULL, NULL, NULL, '2024-10-30 15:10:43', '2024-11-24 21:43:30', 0, '2024-10-30 17:30:00', '2024-10-20 00:00:00', 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (91, 1, 50, 18, 'Poliitika uudised', 'BLAAA', 17, 'Politics', '/poliitika-uudised/blaaa', 2674, '', NULL, NULL, NULL, '2024-10-16 11:37:58', '2024-11-16 00:03:56', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (93, NULL, 50, 18, 'Poliitika uudised', 'Arles on uudishimulik', NULL, NULL, '/poliitika-uudised/arles-on-uudishimulik', 1121, '', '', 'Samanta Jones', '', '2024-10-24 20:43:26', '2024-11-24 00:45:06', 0, NULL, NULL, 3, 'Samantha Jones', 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for news_changes
-- ----------------------------
DROP TABLE IF EXISTS `news_changes`;
CREATE TABLE `news_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `chnges_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4;

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
) ENGINE=InnoDB;

-- ----------------------------
-- Records of news_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `news_editors_assn` VALUES (30, 2);
INSERT INTO `news_editors_assn` VALUES (30, 3);
INSERT INTO `news_editors_assn` VALUES (33, 2);
INSERT INTO `news_editors_assn` VALUES (33, 3);
INSERT INTO `news_editors_assn` VALUES (40, 2);
INSERT INTO `news_editors_assn` VALUES (40, 3);
INSERT INTO `news_editors_assn` VALUES (43, 2);
INSERT INTO `news_editors_assn` VALUES (43, 3);
INSERT INTO `news_editors_assn` VALUES (45, 3);
INSERT INTO `news_editors_assn` VALUES (62, 2);
INSERT INTO `news_editors_assn` VALUES (62, 3);
INSERT INTO `news_editors_assn` VALUES (62, 4);
INSERT INTO `news_editors_assn` VALUES (69, 4);
INSERT INTO `news_editors_assn` VALUES (78, 3);
INSERT INTO `news_editors_assn` VALUES (79, 1);
INSERT INTO `news_editors_assn` VALUES (87, 3);
INSERT INTO `news_editors_assn` VALUES (90, 2);
INSERT INTO `news_editors_assn` VALUES (91, 2);
INSERT INTO `news_editors_assn` VALUES (93, 2);
COMMIT;

-- ----------------------------
-- Table structure for news_settings
-- ----------------------------
DROP TABLE IF EXISTS `news_settings`;
CREATE TABLE `news_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_reserved` int unsigned DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `news_group_id` int unsigned DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `news_locked` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_reserved_idx` (`is_reserved`) USING BTREE,
  KEY `news_locked_idx` (`news_locked`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `is_reserved_ibfk` FOREIGN KEY (`is_reserved`) REFERENCES `reserve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50;

-- ----------------------------
-- Records of news_settings
-- ----------------------------
BEGIN;
INSERT INTO `news_settings` VALUES (18, 'Poliitika uudised', NULL, 1, 1, 50, '2021-05-25 23:05:45', '2024-11-16 22:29:46', 1);
INSERT INTO `news_settings` VALUES (20, 'Kultuuri uudised', 'Kultuuri uudised', 1, 1, 247, '2021-11-29 00:25:33', '2024-11-24 23:45:25', 0);
INSERT INTO `news_settings` VALUES (46, 'Spordiuudised', NULL, 1, 1, 283, '2024-05-13 10:19:52', '2024-11-16 22:29:53', 0);
COMMIT;

-- ----------------------------
-- Table structure for organizing_institution
-- ----------------------------
DROP TABLE IF EXISTS `organizing_institution`;
CREATE TABLE `organizing_institution` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `organizing_institution_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7;

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
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_person_1` (`last_name`)
) ENGINE=InnoDB AUTO_INCREMENT=14;

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
) ENGINE=InnoDB;

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
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6;

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
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `sys_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13;

-- ----------------------------
-- Records of person_with_lock
-- ----------------------------
BEGIN;
INSERT INTO `person_with_lock` VALUES (1, 'John', 'Doe', NULL);
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
  `name` varchar(100) NOT NULL,
  `description` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `spent` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_project_1` (`project_status_type_id`),
  KEY `IDX_project_2` (`manager_person_id`),
  CONSTRAINT `person_project` FOREIGN KEY (`manager_person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `project_status_type_project` FOREIGN KEY (`project_status_type_id`) REFERENCES `project_status_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5;

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
  `name` varchar(50) NOT NULL,
  `description` text,
  `guidelines` text,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_projectstatustype_1` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4;

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
  `table_key` varchar(200) NOT NULL,
  `ts` varchar(40) NOT NULL,
  PRIMARY KEY (`table_key`)
) ENGINE=InnoDB;

-- ----------------------------
-- Records of qc_watchers
-- ----------------------------
BEGIN;
INSERT INTO `qc_watchers` VALUES ('qcubed-5.address', '0.96962000 1729575936');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.album', '0.57664400 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.areas_of_sports', '0.74142200 1727466236');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.article', '0.49302200 1732402494');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.articles_editors_assn', '0.67334800 1730985905');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.board', '0.15897100 1731567984');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.board_options', '0.58746100 1730980288');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.boards_editors_assn', '0.13256500 1730579004');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.boards_settings', '0.53726400 1732475392');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.category_of_article', '0.82865700 1731708787');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.category_of_news', '0.92552700 1731707238');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.changes', '0.40599200 1724427629');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.content_types_management', '0.50445300 1731080608');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.error_pages', '0.85367600 1725100463');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.error_pages_editors_assn', '0.84335300 1722446899');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar', '0.45802700 1732482632');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar_area_sports_assn', '0.02561500 1726813560');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_calendar_editors_assn', '0.66452300 1732482429');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_changes', '0.74006900 1727104795');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_settings', '0.56565000 1732482552');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.events_target_calendar_assn', '0.02737000 1726813560');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.example', '0.53349500 1730211355');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.files', '0.56400300 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.folders', '0.53616600 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_links', '0.57936900 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_options', '0.45480800 1731080995');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.frontend_template_locking', '0.85899800 1727730684');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.galleries', '0.37342200 1726421709');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallery_list', '0.79920900 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallery_settings', '0.58466100 1732745285');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.gallerylist_editors_assn', '0.99889500 1730394248');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.list_of_galleries', '0.35385900 1726421709');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.list_of_sliders', '0.05261900 1728671290');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members', '0.97207000 1731568132');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_editors_assn', '0.04096100 1731403980');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_options', '0.86948000 1731403755');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.members_settings', '0.38478900 1732474619');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.menu', '0.87798000 1731880605');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.menu_content', '0.49034700 1732402494');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.metadata', '0.95342300 1731403165');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.milestone', '0.09908100 1729575937');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news', '0.18488100 1732478659');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_changes', '0.97413400 1726224669');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_editors_assn', '0.12538100 1732478562');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.news_settings', '0.74857300 1732484725');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.organizing_institution', '0.76334300 1732484784');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.person', '0.79891400 1730408560');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.project', '0.00256700 1729575937');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders', '0.54016600 1730309739');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders_editors_assn', '0.00201600 1730394249');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sliders_settings', '0.93041800 1730310842');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_areas', '0.64553500 1728496899');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_calendar', '0.80082300 1732485399');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_calendar_editors_assn', '0.79806000 1732485399');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_changes', '0.66476500 1727561970');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_content_types', '0.75886500 1728165065');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.sports_settings', '0.46332500 1732484694');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.target_group_of_calendar', '0.31418700 1727465068');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.team_member_project_assn', '0.99436100 1729575936');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.temp_data', '0.32284400 1728132291');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.title_of_newsgroup', '0.83788400 1728515402');
INSERT INTO `qc_watchers` VALUES ('qcubed-5.type_test', '0.25194500 1729575937');
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
) ENGINE=InnoDB;

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
  `written_status` varchar(255) NOT NULL,
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=3;

-- ----------------------------
-- Records of reserve
-- ----------------------------
BEGIN;
INSERT INTO `reserve` VALUES (1, 1, 'Is reserved', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i>  Is reserved', 1);
INSERT INTO `reserve` VALUES (2, 2, 'Free', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i> Free', 1);
COMMIT;

-- ----------------------------
-- Table structure for slider_list_status
-- ----------------------------
DROP TABLE IF EXISTS `slider_list_status`;
CREATE TABLE `slider_list_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=5;

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
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=115;

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
INSERT INTO `sliders` VALUES (108, 2, 1121, 4, 'Lehed maas', NULL, '/Kolletanud lehed maas.jpg', 'jpg', '900 x 585', 1500, 975, NULL, '2024-10-14 01:03:11', '2024-10-18 22:58:24', 1);
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
) ENGINE=InnoDB;

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
-- Table structure for sliders_settings
-- ----------------------------
DROP TABLE IF EXISTS `sliders_settings`;
CREATE TABLE `sliders_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `admin_status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint unsigned DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  KEY `id_idx` (`id`) USING BTREE,
  KEY `admin_status_idx` (`admin_status`),
  KEY `assigned_by_user_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `sliders_settings_ibfk_1` FOREIGN KEY (`status`) REFERENCES `slider_list_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sliders_settings_ibfk_2` FOREIGN KEY (`admin_status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sliders_settings_ibfk_3` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3;

-- ----------------------------
-- Records of sliders_settings
-- ----------------------------
BEGIN;
INSERT INTO `sliders_settings` VALUES (1, 'Sponsors', 1, '2024-03-06 22:26:00', '2024-10-19 21:57:37', 0, NULL, NULL, 1, 'John Doe', 1);
INSERT INTO `sliders_settings` VALUES (2, 'Advertising', 1, '2024-03-07 21:24:41', '2024-10-30 19:54:02', 0, NULL, NULL, 1, 'John Doe', 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_areas
-- ----------------------------
DROP TABLE IF EXISTS `sports_areas`;
CREATE TABLE `sports_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sports_calendar_group_id` int unsigned DEFAULT NULL,
  `menu_content_group_id` int unsigned DEFAULT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sports_content_types_id` int unsigned DEFAULT NULL,
  `sports_content_type_name` varchar(255) DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sports_area_name` varchar(255) DEFAULT NULL,
  `files_id` int unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `sports_content_type_id_idx` (`sports_content_types_id`) USING BTREE,
  KEY `files_id_idx` (`files_id`) USING BTREE,
  KEY `sport_areas_id_idx` (`sports_areas_id`) USING BTREE,
  KEY `sports_calendar_group_id_idx` (`sports_calendar_group_id`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `sports_areas_ibfk_1` FOREIGN KEY (`sports_content_types_id`) REFERENCES `sports_content_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_areas_ibfk_2` FOREIGN KEY (`sports_areas_id`) REFERENCES `areas_of_sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_areas_ibfk_3` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_areas_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9;

-- ----------------------------
-- Records of sports_areas
-- ----------------------------
BEGIN;
INSERT INTO `sports_areas` VALUES (1, 13, 338, 2024, 'EKSL sisekergejõustiku võistluste juhend 2024', 1, 'Juhendid', 8, 'Kergejõustik', 2755, '/spordialad/kergejoustik/juhendid/2013 EKSL sisekj_juhend.pdf', '2024-10-05 18:20:29', '2024-10-06 10:44:27', 1);
INSERT INTO `sports_areas` VALUES (2, 13, 338, 2024, 'EKSL sisekergejõustiku tulemused 2024', 2, 'Tulemused', 8, 'Kergejõustik', 2764, '/spordialad/kergejoustik/tulemused/2012_EKSL_MV_protkergej 260512.pdf', '2024-10-05 21:18:18', NULL, 1);
INSERT INTO `sports_areas` VALUES (5, 13, 338, 2024, 'EKSL siekergejõustiku ajakava 2023', 3, 'Ajakavad', 8, 'Kergejõustik', 2758, '/spordialad/kergejoustik/juhendid/EKSL MV juhend2013 kergej.pdf', '2024-10-06 14:20:58', '2024-10-09 20:35:58', 1);
INSERT INTO `sports_areas` VALUES (6, 11, 338, 2024, 'Jalgpalli juhend 2021', 1, 'Juhendid', 3, 'Jalgpall', 2757, '/spordialad/kergejoustik/juhendid/Eesti_suvised_parakergejoustiku_MV_juhend_2021_07_14.pdf', '2024-10-08 01:04:24', '2024-10-08 01:12:16', 1);
INSERT INTO `sports_areas` VALUES (7, 10, 338, 2024, 'EKSL kergejõustiku MV juhend 2012', 1, 'Juhendid', 8, 'Kergejõustik', 2761, '/spordialad/kergejoustik/juhendid/EKSL_kergejõustiku_MV_juhend_2012.pdf', '2024-10-09 20:33:50', NULL, 1);
INSERT INTO `sports_areas` VALUES (8, 10, 338, 2024, 'EKL aruanne 1994', 2, 'Tulemused', 8, 'Kergejõustik', 1134, '/EKL aruanne.xlsx', '2024-10-09 21:01:25', '2024-10-09 21:01:39', 2);
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
  `menu_content_group_name` varchar(255) DEFAULT NULL,
  `sports_areas_id` int unsigned DEFAULT NULL,
  `sport_area` varchar(255) DEFAULT NULL,
  `picture_id` int unsigned DEFAULT NULL,
  `files_ids` varchar(255) DEFAULT NULL,
  `picture_description` text,
  `author_source` varchar(255) DEFAULT NULL,
  `organizing_institution_id` int unsigned DEFAULT NULL,
  `organizing_institution_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `event_place` text,
  `beginning_event` date DEFAULT NULL,
  `end_event` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `information` text,
  `schedule` text,
  `sports_content_types_ids` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `website_target_type_id` int unsigned DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `facebook_target_type_id` int unsigned DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `instagram_target_type_id` int unsigned DEFAULT NULL,
  `organizers` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
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
  CONSTRAINT `sports_calendar_fk_3` FOREIGN KEY (`sports_areas_id`) REFERENCES `areas_of_sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_4` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_5` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_6` FOREIGN KEY (`facebook_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_7` FOREIGN KEY (`menu_content_group_title_id`) REFERENCES `sports_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_8` FOREIGN KEY (`events_changes_id`) REFERENCES `sports_changes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sports_calendar_fk_9` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22;

-- ----------------------------
-- Records of sports_calendar
-- ----------------------------
BEGIN;
INSERT INTO `sports_calendar` VALUES (10, 2024, 1, 338, 2, 'Spordisündmuste kalender', 1, 'Bowling', 2712, NULL, NULL, '', 3, NULL, 'Blaaa', '/spordisundmuste-kalender/2024/blaaa', 'Tallinnas', '2024-09-29', '2024-09-29', '10:00:00', '16:00:00', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'Anneli Ojastu', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 22:25:29', '2024-11-23 23:50:42', 1);
INSERT INTO `sports_calendar` VALUES (11, 2024, NULL, 338, 2, 'Spordisündmuste kalender', 3, 'Jalgpall', NULL, NULL, NULL, NULL, NULL, NULL, 'Eesti ja Läti kurtide jalgpalli sõpruskohtumine', '/spordisundmuste-kalender/2024/eesti-ja-lati-kurtide-jalgpalli-sopruskohtumine', 'Jõgeva', '2024-10-10', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 1234 5678', 'blaa@blaa.ee', 4, 'Brett Carlisle', '2024-09-27 23:57:13', '2024-10-09 20:06:06', 1);
INSERT INTO `sports_calendar` VALUES (12, 2024, NULL, 338, 2, 'Spordisündmuste kalender', 8, 'Kergejõustik', NULL, NULL, NULL, NULL, NULL, NULL, 'Maahoki', '/spordisundmuste-kalender/2024/maahoki', 'Tallinna staadion, Kalevi tn...', '2024-09-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 1234 5678', 'edso@edso.ee', 4, 'Brett Carlisle', '2024-09-28 00:04:50', '2024-10-09 20:06:06', 1);
INSERT INTO `sports_calendar` VALUES (13, 2024, NULL, 338, 2, 'Spordisündmuste kalender', 8, 'Kergejõustik', 2680, NULL, NULL, NULL, NULL, NULL, 'EKSL sisekergejõustiku võistlused', '/spordisundmuste-kalender/2024/eksl-sisekergejoustiku-voistlused', 'Lasname Spordihallis, Punane 8, Tallinn', '2024-10-26', NULL, '10:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 1234 5678', 'eksl@eksl.ee', 4, 'Brett Carlisle', '2024-09-28 00:12:20', '2024-10-09 20:35:58', 1);
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
) ENGINE=InnoDB;

-- ----------------------------
-- Records of sports_calendar_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `sports_calendar_editors_assn` VALUES (10, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (11, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (12, 1);
INSERT INTO `sports_calendar_editors_assn` VALUES (13, 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_changes
-- ----------------------------
DROP TABLE IF EXISTS `sports_changes`;
CREATE TABLE `sports_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `sports_changes_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5;

-- ----------------------------
-- Records of sports_changes
-- ----------------------------
BEGIN;
INSERT INTO `sports_changes` VALUES (1, 'Uuendatud', '2024-09-22 16:40:09', '2024-09-23 13:03:39', 1);
INSERT INTO `sports_changes` VALUES (2, 'Täiendatud', '2024-09-22 16:40:30', '2024-09-23 13:03:44', 1);
INSERT INTO `sports_changes` VALUES (3, 'Edasi lükatud', '2024-09-22 16:40:53', '2024-09-29 01:19:30', 1);
INSERT INTO `sports_changes` VALUES (4, 'Tühistatud', '2024-09-29 01:19:25', NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for sports_content_types
-- ----------------------------
DROP TABLE IF EXISTS `sports_content_types`;
CREATE TABLE `sports_content_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`name`) USING BTREE,
  KEY `sports_content_types_status_ibfk` (`status`),
  CONSTRAINT `sports_content_types_status_ibfk` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4;

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
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3;

-- ----------------------------
-- Records of sports_settings
-- ----------------------------
BEGIN;
INSERT INTO `sports_settings` VALUES (1, 'Spordikalender', NULL, 1, 1, 337, '2024-09-25 21:20:41', '2024-11-16 22:59:57', 0);
INSERT INTO `sports_settings` VALUES (2, 'Spordisündmuste kalender', 'Spordisündmuste kalender', 1, 1, 338, '2024-09-27 11:43:56', '2024-11-24 23:44:54', 1);
COMMIT;

-- ----------------------------
-- Table structure for status
-- ----------------------------
DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=7;

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
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_enabled_idx` (`is_enabled`) USING BTREE,
  CONSTRAINT `is_enabled_id_fk` FOREIGN KEY (`is_enabled`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11;

-- ----------------------------
-- Records of target_group_of_calendar
-- ----------------------------
BEGIN;
INSERT INTO `target_group_of_calendar` VALUES (2, 'Kalenderplaan', 1, '2021-06-09 00:47:50', '2024-09-22 13:54:18');
INSERT INTO `target_group_of_calendar` VALUES (3, 'Pensionäride kalenderplaan', 2, '2021-07-02 20:02:13', '2024-09-22 14:20:52');
INSERT INTO `target_group_of_calendar` VALUES (4, 'Sportlaste kalenderplaan', 1, '2021-07-04 23:09:26', '2024-09-27 22:24:28');
INSERT INTO `target_group_of_calendar` VALUES (9, 'Taidlejate kalenderplaan', 2, '2021-07-20 23:07:10', '2024-09-27 22:23:49');
COMMIT;

-- ----------------------------
-- Table structure for target_type
-- ----------------------------
DROP TABLE IF EXISTS `target_type`;
CREATE TABLE `target_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5;

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
) ENGINE=InnoDB;

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
  `path` varchar(255) DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `areas_responsibility` varchar(255) DEFAULT NULL,
  `interests` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `organisation_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE,
  CONSTRAINT `status_teams_ibfk` FOREIGN KEY (`status`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

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
  `test_text` text,
  `test_bit` tinyint(1) DEFAULT NULL,
  `test_varchar` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40;

-- ----------------------------
-- Records of type_test
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `display_real_name_flag` tinyint(1) DEFAULT '0',
  `display_name` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=5;

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
-- Table structure for view_type
-- ----------------------------
DROP TABLE IF EXISTS `view_type`;
CREATE TABLE `view_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5;

-- ----------------------------
-- Records of view_type
-- ----------------------------
BEGIN;
INSERT INTO `view_type` VALUES (3, 'Detail type');
INSERT INTO `view_type` VALUES (1, 'Home type');
INSERT INTO `view_type` VALUES (2, 'List type');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
