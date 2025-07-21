-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: d61402.mysql.zonevs.eu
-- Loomise aeg: Sept 16, 2021 kell 10:06 PL
-- Serveri versioon: 10.4.21-MariaDB-log
-- PHP versioon: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Andmebaas: `d61402sd165944`
--

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `address`
--

CREATE TABLE `address` (
  `id` int(10) UNSIGNED NOT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `street` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `address`
--

INSERT INTO `address` (`id`, `person_id`, `street`, `city`) VALUES
(1, 1, '1 Love Drive', 'Phoenix'),
(2, 2, '2 Doves and a Pine Cone Dr.', 'Dallas'),
(3, 3, '3 Gold Fish Pl.', 'New York'),
(4, 3, '323 W QCubed', 'New York'),
(5, 5, '22 Elm St', 'Palo Alto'),
(6, 7, '1 Pine St', 'San Jose'),
(7, 7, '421 Central Expw', 'Mountain View');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `aktivity`
--

CREATE TABLE `aktivity` (
  `id` int(10) UNSIGNED NOT NULL,
  `is_enabled` int(11) NOT NULL,
  `written_status` varchar(255) NOT NULL,
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `aktivity`
--

INSERT INTO `aktivity` (`id`, `is_enabled`, `written_status`, `drawn_status`, `visibility`) VALUES
(1, 1, 'Active', '<i class=\"fa fa-circle fa-lg\" style=\"color:#449d44;line-height:0.1;\"></i>  Active', 1),
(2, 2, 'Inactive', '<i class=\"fa fa-circle fa-lg\" style=\"color:#ff0000;line-height:0.1;\"></i> Inactive', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `areas_of_sports`
--

CREATE TABLE `areas_of_sports` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int(10) UNSIGNED DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `areas_of_sports`
--

INSERT INTO `areas_of_sports` (`id`, `name`, `is_enabled`, `post_date`, `post_update_date`) VALUES
(1, 'Bowling', 1, '2021-07-08 23:41:56', '2021-07-08 23:42:03'),
(2, 'Discgolf', 1, '2021-07-08 23:42:34', '2021-07-09 23:51:43'),
(3, 'Jalgpall', 2, '2021-07-19 01:13:39', '2021-08-04 14:55:28'),
(4, 'Kabe', 2, '2021-07-19 01:14:11', '2021-07-21 18:17:46'),
(5, 'Karate', 2, '2021-07-19 01:14:35', '2021-08-04 14:54:50'),
(6, 'Kelgutamine', 2, '2021-07-19 01:33:57', '2021-08-04 14:48:28'),
(7, 'KepikÃµnd', 2, '2021-07-19 01:34:18', '2021-08-04 14:48:39'),
(8, 'KergejÃµustik', 1, '2021-07-19 01:34:50', '2021-08-03 01:03:08'),
(9, 'Koroona', 2, '2021-07-19 01:35:09', '2021-08-04 14:48:33'),
(10, 'Korvpall', 1, '2021-07-19 01:35:34', NULL),
(11, 'Lauatennis', 2, '2021-07-19 01:35:52', '2021-08-04 14:51:14'),
(12, 'Male', 2, '2021-07-19 01:36:13', '2021-08-04 14:50:20'),
(13, 'Minigolf', 2, '2021-07-19 01:36:30', '2021-08-04 14:50:11'),
(14, 'Murdmaajooks', 1, '2021-07-19 03:44:02', NULL),
(15, 'Noolevise', 2, '2021-07-19 03:47:13', '2021-08-04 14:49:59'),
(16, 'Orienteerumine', 1, '2021-07-19 03:47:40', NULL),
(17, 'Pesapall', 2, '2021-07-19 03:47:58', '2021-08-04 14:49:52'),
(18, 'Petank', 2, '2021-07-19 03:48:17', '2021-08-04 14:49:43'),
(19, 'Rannavolle', 1, '2021-07-19 03:48:35', NULL),
(20, 'Rulluisutamine', 2, '2021-07-19 03:48:54', '2021-08-04 14:51:37'),
(21, 'Saalihoki', 1, '2021-07-19 03:49:10', NULL),
(22, 'Saalijalgpall', 2, '2021-07-19 03:49:29', '2021-08-04 14:49:36'),
(23, 'SisekergejÃµustik', 1, '2021-07-19 03:49:46', '2021-08-03 01:04:22'),
(24, 'Sulgpall', 2, '2021-07-19 03:50:02', '2021-08-04 14:49:26'),
(25, 'Suusatamine', 1, '2021-07-19 03:50:20', NULL),
(26, 'Tennis', 2, '2021-07-19 03:50:35', '2021-08-04 14:49:19'),
(27, 'Triatlon', 1, '2021-07-19 03:50:50', NULL),
(28, 'Uisutamine', 2, '2021-07-19 03:51:05', '2021-08-04 14:48:55'),
(29, 'Ujumine', 1, '2021-07-19 03:51:37', NULL),
(30, 'ViievÃµistlus', 2, '2021-07-19 03:51:55', '2021-08-04 14:48:59'),
(31, 'VÃµrkpall', 1, '2021-07-19 03:52:10', '2021-08-03 01:04:58'),
(32, 'Muu', 1, '2021-07-19 12:57:06', NULL);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `article`
--

CREATE TABLE `article` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_content_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `title_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `author_source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int(10) UNSIGNED DEFAULT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmation_asking` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `article`
--

INSERT INTO `article` (`id`, `menu_content_id`, `title`, `category_id`, `title_slug`, `picture`, `picture_description`, `author_source`, `content`, `post_date`, `post_update_date`, `assigned_by_user`, `author`, `confirmation_asking`) VALUES
(29, 42, 'About organisation', 2, 'about-organisation', NULL, '', '', '', '2021-05-24 18:42:27', '2021-08-03 02:39:33', 2, 'Alex Smith', 1),
(30, 43, 'About contacts', 4, 'about-contacts', NULL, NULL, NULL, NULL, '2021-05-24 18:43:03', '2021-06-06 23:04:32', 2, 'Alex Smith', 0),
(31, 44, 'About board', 3, 'about-board', NULL, NULL, NULL, '<p>sdfgn</p>\n', '2021-05-24 18:49:11', '2021-07-29 22:22:51', 2, 'Alex Smith', 0),
(44, 84, 'Teine proovi artikkel', NULL, 'teine-proovi-artikkel', NULL, NULL, NULL, NULL, '2021-07-29 18:04:31', '2021-07-30 00:49:24', 1, 'John Doe', 0),
(58, 99, 'Esimene veebikonto', NULL, 'esimene-veebikonto', NULL, NULL, NULL, NULL, '2021-08-03 02:20:53', '2021-08-03 02:21:18', 3, 'Samantha Jones', 0),
(59, 105, 'Neljas veebikonto nÃ¤ide', 3, 'neljas-veebikonto-naide', NULL, NULL, NULL, '<p>Neljas veebikonto n&auml;ide</p>\n', '2021-08-03 21:38:23', '2021-08-03 22:09:20', 3, 'Samantha Jones', 0);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `articles_editors_assn`
--

CREATE TABLE `articles_editors_assn` (
  `articles_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `articles_editors_assn`
--

INSERT INTO `articles_editors_assn` (`articles_id`, `user_id`) VALUES
(29, 1),
(29, 3),
(31, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `category_of_article`
--

CREATE TABLE `category_of_article` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` int(10) UNSIGNED DEFAULT 2,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `category_of_article`
--

INSERT INTO `category_of_article` (`id`, `name`, `is_enabled`, `post_date`, `post_update_date`) VALUES
(1, 'Education', 1, '2020-05-30 10:00:00', '2021-07-21 18:30:34'),
(2, 'Culture', 1, '2020-05-30 10:00:00', '2021-07-21 19:52:32'),
(3, 'Sport', 1, '2020-05-30 10:00:44', '2021-01-08 23:38:41'),
(4, 'History', 1, '2020-05-30 10:00:44', '2021-07-21 19:09:57'),
(5, 'Varia', 1, '2020-05-30 10:00:44', '2020-05-31 00:05:13'),
(6, 'Info', 1, '2021-06-29 22:10:57', '2021-07-01 17:16:36'),
(8, 'Politics', 1, '2021-06-29 22:23:59', '2021-07-21 19:02:48'),
(35, 'Uus blaaa', 2, '2021-07-01 17:19:22', '2021-08-02 23:37:08'),
(36, 'Blaaaaa', 2, '2021-07-01 17:20:01', NULL);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `category_of_news`
--

CREATE TABLE `category_of_news` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` int(10) UNSIGNED DEFAULT 2,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `category_of_news`
--

INSERT INTO `category_of_news` (`id`, `name`, `is_enabled`, `post_date`, `post_update_date`) VALUES
(1, 'Politics', 2, '2020-09-12 11:00:00', '2021-07-21 19:29:09'),
(2, 'Life', 1, '2020-09-12 11:00:00', '2021-07-21 19:29:33'),
(3, 'Education', 1, '2020-09-12 11:00:00', '2021-07-01 18:45:32'),
(4, 'Business', 1, '2020-09-13 00:00:00', '2021-07-01 18:45:35'),
(5, 'Health', 1, '2020-08-01 21:29:00', '2021-08-02 17:55:12');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `content_type`
--

CREATE TABLE `content_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `tabs_text` varchar(255) DEFAULT NULL,
  `class_names` varchar(255) DEFAULT NULL,
  `backend_template_url` varchar(255) DEFAULT NULL,
  `fronted_template_url` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Andmete tõmmistamine tabelile `content_type`
--

INSERT INTO `content_type` (`id`, `name`, `tabs_text`, `class_names`, `backend_template_url`, `fronted_template_url`, `is_enabled`) VALUES
(1, 'Home page', 'Edit homepage', 'HomeEditPanel', NULL, NULL, 1),
(2, 'Article', 'Edit article', 'ArticleEditPanel', NULL, NULL, 1),
(3, 'News', 'Edit news', 'NewsEditPanel', NULL, NULL, 1),
(4, 'Gallery', 'Edit gallery', 'GalleryEditPanel', NULL, NULL, 0),
(5, 'Events calendar', 'Edit events calendar', 'EventsCalendarEditPanel', NULL, NULL, 1),
(6, 'Sports calendar', 'Edit sports calendar ', 'SportsCalendarEditPanel', NULL, NULL, 0),
(7, 'Internal page link', 'Edit internal page link', 'InternalPageEditPanel', NULL, NULL, 1),
(8, 'Redirecting link', 'Edit redirecting link', 'RedirectingEditPanel', NULL, NULL, 1),
(9, 'Placeholder', 'Edit placeholder', 'PlaceholderEditPanel', NULL, NULL, 1),
(10, 'Error Page', 'Edit error page', 'ErrorPageEditPanel', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `content_types_management`
--

CREATE TABLE `content_types_management` (
  `id` int(10) UNSIGNED NOT NULL,
  `content_name` varchar(255) NOT NULL,
  `default_frontend_template_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `content_types_management`
--

INSERT INTO `content_types_management` (`id`, `content_name`, `default_frontend_template_id`) VALUES
(1, 'Home detail view', 12),
(2, 'Article detail view', 2),
(3, 'News list view', 3),
(4, 'News detail view', 4),
(5, 'Gallery list view', 5),
(6, 'Gallery detail view', 6),
(7, 'Events calerdar list view', 7),
(8, 'Events calendar detail view', 8),
(9, 'Sports calendar list view', 9),
(10, 'Sports calendar detail view', 10),
(11, 'Errorpage detail view', 14);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `date_and_time_formats`
--

CREATE TABLE `date_and_time_formats` (
  `id` int(10) UNSIGNED NOT NULL,
  `display_format` varchar(255) DEFAULT NULL,
  `date_format` varchar(255) DEFAULT NULL,
  `time_format` varchar(255) DEFAULT NULL,
  `calendar_date_format` varchar(255) DEFAULT NULL,
  `calendar_time_format` varchar(255) DEFAULT NULL,
  `calendar_show_meridian` tinyint(1) DEFAULT 0,
  `is_enabled` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `date_and_time_formats`
--

INSERT INTO `date_and_time_formats` (`id`, `display_format`, `date_format`, `time_format`, `calendar_date_format`, `calendar_time_format`, `calendar_show_meridian`, `is_enabled`) VALUES
(1, '31.12.2001 23.59.00', 'DD.MM.YYYY', 'hhhh.mm.ss', 'dd.mm.yyyy', ' hh.ii', 0, 1),
(2, '31.12.2001 23:59:00', 'DD.MM.YYYY', 'hhhh:mm:ss', 'dd.mm.yyyy', 'hh:ii', 0, 1),
(3, '31/12/2001 23:59:00', 'DD/MM/YYYY', 'hhhh:mm:ss', 'dd/mm/yyyy', 'hh:ii', 0, 1),
(4, '12/31/2001 23:59:00', 'MM/DD/YYYY', 'hhhh:mm:ss', 'mm/dd/yyyy', 'hh:ii', 0, 1),
(5, '31/12/2001 11:59 pm', 'DD/MM/YYYY', 'hh:mm z', 'dd/mm/yyyy', 'HH:ii p', 1, 1),
(6, '12/31/2001 11:59 pm', 'DD/MM/YYYY', 'hh:mm z', 'dd/mm/yyyy', 'HH:ii p', 1, 1),
(7, '31-12-2001 11:59 pm', 'DD-MM-YYYY', 'hh:mm z', 'dd-mm-yyyy', 'HH:ii p', 1, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `error_pages`
--

CREATE TABLE `error_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_content_id` int(10) UNSIGNED DEFAULT NULL,
  `error_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int(10) UNSIGNED DEFAULT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `error_pages`
--

INSERT INTO `error_pages` (`id`, `menu_content_id`, `error_title`, `title_slug`, `content`, `post_date`, `post_update_date`, `assigned_by_user`, `author`) VALUES
(7, 62, 'Teine veateade', 'teine-veateade', '<p>Siin on teine veateade!</p>\n', '2021-06-15 00:03:12', '2021-08-04 15:14:14', 2, 'Alex Smith');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `error_pages_editors_assn`
--

CREATE TABLE `error_pages_editors_assn` (
  `error_pages_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `error_pages_editors_assn`
--

INSERT INTO `error_pages_editors_assn` (`error_pages_id`, `user_id`) VALUES
(7, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `events_calendar`
--

CREATE TABLE `events_calendar` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_content_group_id` int(10) UNSIGNED DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `picture_descripton` text DEFAULT NULL,
  `author_source` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `target_group_id` int(10) UNSIGNED DEFAULT NULL,
  `target_group_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_slug` varchar(255) DEFAULT NULL,
  `event_place` text DEFAULT NULL,
  `beginning_event` date DEFAULT NULL,
  `end_event` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `information` text DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `instruction_link` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `website_target_type_id` int(10) UNSIGNED DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `facebook_target_type_id` int(10) UNSIGNED DEFAULT NULL,
  `organizers` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_by_user` int(10) UNSIGNED DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` int(10) UNSIGNED DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `events_calendar`
--

INSERT INTO `events_calendar` (`id`, `menu_content_group_id`, `picture`, `picture_descripton`, `author_source`, `year`, `target_group_id`, `target_group_name`, `title`, `title_slug`, `event_place`, `beginning_event`, `end_event`, `start_time`, `end_time`, `information`, `schedule`, `instruction_link`, `website_url`, `website_target_type_id`, `facebook_url`, `facebook_target_type_id`, `organizers`, `phone`, `email`, `assigned_by_user`, `author`, `post_date`, `post_update_date`, `status`) VALUES
(1, 59, NULL, NULL, NULL, 2021, 2, 'Kalenderplaan', 'EKSL MV 3 x 3 korvpallis', '/sundmuste-kalender/2021/kalenderplaan/eksl-mv-3-x-3-korvpallis', 'PÃ¤rnu hallis', '2021-07-31', '2021-08-01', '10:00:00', '14:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EKSL juhatus', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-07-03 17:30:28', '2021-08-04 15:04:55', 3),
(16, 59, NULL, NULL, NULL, 2021, 4, 'Sportlaste kalenderplaan', 'EKSL kergejÃµustiku meistrivÃµistlused', '/sundmuste-kalender/2021/sportlaste-kalenderplaan/eksl-kergejoustiku-meistrivoistlused', 'Tartus', '2021-07-17', NULL, '12:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Annely Ojastu', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-07-15 03:48:26', '2021-08-04 15:05:44', 1),
(17, 59, NULL, '', '', 2021, 4, 'Sportlaste kalenderplaan', 'Poksivoistlused', '/sundmuste-kalender/2021/sportlaste-kalenderplaan/poksivoistlused', 'Tartus', '2021-07-17', '2021-07-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Rein Maidla', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-07-15 03:53:50', '2021-08-04 15:05:53', 1),
(21, 59, NULL, NULL, NULL, 2021, 2, 'Kalenderplaan', 'Discgolfi meistrivoistlused II etapp', '/sundmuste-kalender/2021/kalenderplaan/discgolfi-meistrivoistlused-ii-etapp', 'Moskvas', '2021-09-01', '2021-09-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 521 8851', 'tiit.papp@gmail.com', 2, 'Alex Smith', '2021-07-28 22:55:41', '2021-08-04 15:04:36', 1),
(22, 59, NULL, NULL, NULL, 2021, 4, 'Sportlaste kalenderplaan', 'EKSL triatloni MV 2021 (TÃœHISTATUD)', '/sundmuste-kalender/2021/sportlaste-kalenderplaan/eksl-triatloni-mv-2021-tuhistatud', 'Porkuni', '2021-07-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sergei Matvijenko', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-07-29 01:51:40', '2021-08-04 15:05:14', 1),
(23, 81, NULL, NULL, NULL, 2021, 2, 'Kalenderplaan', 'Bobisoidu MV Tallinnas', '/teine-kalender/2021/kalenderplaan/bobisoidu-mv-tallinnas', 'MustamÃ¤el', '2021-12-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Argo Purv', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-07-29 17:40:51', '2021-08-04 15:01:24', 2),
(24, 59, NULL, NULL, NULL, 2021, 2, 'Kalenderplaan', 'Orienteerumine - sprint', '/sundmuste-kalender/2021/kalenderplaan/orienteerumine-sprint', 'Harjumaa', '2021-09-11', '2021-10-24', '10:00:00', '23:51:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-08-03 00:54:55', '2021-08-04 15:04:26', 1),
(25, 59, NULL, NULL, NULL, 2021, 2, 'Kalenderplaan', 'EKSLi rannavÃµrkpalli meistrivÃµistlused', '/sundmuste-kalender/2021/kalenderplaan/eksli-rannavorkpalli-meistrivoistlused', 'PÃ¤rnu rannas', '2021-07-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilvi Vare', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-08-03 01:20:41', '2021-08-04 15:05:38', 1),
(26, 59, NULL, NULL, NULL, 2021, 3, 'PensionÃ¤ride kalenderplaan', 'PensionÃ¤ride poksivÃµistlused ', '/sundmuste-kalender/2021/pensionaride-kalenderplaan/pensionaride-poksivoistlused', 'Tallinna Heleni vÃµimlas, Ehte tee 7', '2021-09-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Janis Golubenkov', '+372 521 8851', 'tiit.papp@gmail.com', 1, 'John Doe', '2021-08-03 22:10:26', '2021-08-04 15:04:31', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `events_calendar_area_sports_assn`
--

CREATE TABLE `events_calendar_area_sports_assn` (
  `event_calendar_id` int(10) UNSIGNED NOT NULL,
  `area_sports_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `events_calendar_area_sports_assn`
--

INSERT INTO `events_calendar_area_sports_assn` (`event_calendar_id`, `area_sports_id`) VALUES
(1, 10),
(16, 1),
(17, 32),
(21, 2),
(22, 27),
(23, 32),
(24, 16),
(25, 19),
(26, 32);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `events_calendar_editors_assn`
--

CREATE TABLE `events_calendar_editors_assn` (
  `events_calendar_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `events_calendar_editors_assn`
--

INSERT INTO `events_calendar_editors_assn` (`events_calendar_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(16, 4),
(17, 4),
(21, 1),
(22, 3);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `frontend_links`
--

CREATE TABLE `frontend_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `linked_id` int(10) UNSIGNED DEFAULT NULL,
  `content_types_managament_id` int(10) UNSIGNED DEFAULT NULL,
  `frontend_title_slug` varchar(255) DEFAULT NULL,
  `is_activated` int(10) UNSIGNED DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `frontend_links`
--

INSERT INTO `frontend_links` (`id`, `linked_id`, `content_types_managament_id`, `frontend_title_slug`, `is_activated`) VALUES
(1, 1, 8, '/sundmuste-kalender/2021/kalenderplaan/eksl-mv-3-x-3-korvpallis', 1),
(2, 42, 2, '/organisation/about-organisation', 1),
(3, 43, 2, '/contacts/about-contacts', 1),
(4, 44, 2, '/board/about-board', 1),
(5, 50, 3, '/uudised/poliitika-uudised', 1),
(6, 59, 7, '/sundmuste-kalender', 1),
(7, 62, 11, '/view/teine-veateade', 1),
(10, 19, 4, '/uudised/poliitika-uudised/esimene-katse', 1),
(11, 24, 8, '/sundmuste-kalender/2021/kalenderplaan/orienteerumine-sprint', 1),
(12, 26, 4, '/uudised/poliitika-uudised/kolmas-katse', 1),
(13, 30, 4, '/uudised/poliitika-uudised/neljas-katse', 1),
(14, 32, 4, '/uudised/poliitika-uudised/vaatame-siis', 1),
(15, 33, 4, '/uudised/poliitika-uudised/vaatame-teist-korda-uuesti', 1),
(17, 35, 4, '/uudised/poliitika-uudised/kas-saab-juba-valmis', 1),
(18, 36, 4, '/prooviuudised/spordiuudised/vaatame-ponevat-jalgpalli-voistlust', 1),
(19, 37, 4, '/prooviuudised/spordiuudised/tobe-kaotus-kull', 1),
(20, 38, 4, '/prooviuudised/spordiuudised/tore-uudis', 1),
(24, 81, 7, '/teine-kalender', 1),
(27, 16, 8, '/sundmuste-kalender/2021/sportlaste-kalenderplaan/eksl-kergejoustiku-meistrivoistlused', 1),
(28, 17, 8, '/sundmuste-kalender/2021/sportlaste-kalenderplaan/poksivoistlused', 1),
(32, 21, 8, '/sundmuste-kalender/2021/kalenderplaan/discgolfi-meistrivoistlused-ii-etapp', 1),
(33, 22, 8, '/sundmuste-kalender/2021/sportlaste-kalenderplaan/eksl-triatloni-mv-2021-tuhistatud', 1),
(35, 23, 8, '/teine-kalender/2021/kalenderplaan/bobisoidu-mv-tallinnas', 1),
(37, 84, 2, '/teine-proovi-artikkel/teine-proovi-artikkel', 1),
(42, 40, 4, '/uudised/poliitika-uudised/viies-katse', 1),
(57, 25, 8, '/sundmuste-kalender/2021/kalenderplaan/eksli-rannavorkpalli-meistrivoistlused', 1),
(58, 99, 2, '/esimene-veebikonto/esimene-veebikonto', 1),
(59, 105, 2, '/neljas-veebikonto/neljas-veebikonto-naide', 1),
(61, 45, 4, '/uudised/poliitika-uudised/analuutik-randesurve-leedule-voib-olla-valgevene-pettemanoover', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `frontend_options`
--

CREATE TABLE `frontend_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `frontend_template_name` varchar(255) NOT NULL,
  `class_names` varchar(255) NOT NULL,
  `content_type` int(11) NOT NULL,
  `view_type` int(11) NOT NULL,
  `status` int(10) UNSIGNED DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `frontend_options`
--

INSERT INTO `frontend_options` (`id`, `frontend_template_name`, `class_names`, `content_type`, `view_type`, `status`) VALUES
(1, 'Home (standard)', 'HomeController', 1, 1, 1),
(2, 'Article detail (standard)', 'ArticleController', 2, 1, 1),
(3, 'News list (standard)', 'NewsListController', 3, 2, 1),
(4, 'News detail (standard)', 'NewsDetailController', 3, 1, 1),
(5, 'Gallery list (standard)', 'GalleryListController', 4, 2, 1),
(6, 'Gallery detail (standard)', 'GalleryDetailController', 4, 1, 1),
(7, 'Events calendar list (standard))', 'EventsCalendarListController', 5, 2, 1),
(8, 'Events calendar detail (standard)', 'EventsCalendarDetailController', 5, 1, 1),
(9, 'Sports calendar list (standard)', 'SportsCalendarListController', 6, 2, 1),
(10, 'Sports calendar detail (standard)', 'SportsCalendarDetailController', 6, 1, 1),
(11, 'Errorpage detail (standard)', 'ErrorPageController', 10, 1, 1),
(12, 'Home (custom)', 'CustomHomeController', 1, 1, 1),
(13, 'Article detail (custom)', 'CustomArticleController', 2, 1, 1),
(14, 'Errorpage detail (custom)', 'CustomErrorPageController', 10, 1, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `items_per_page`
--

CREATE TABLE `items_per_page` (
  `id` int(10) UNSIGNED NOT NULL,
  `items_per` varchar(3) NOT NULL,
  `items_per_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `items_per_page`
--

INSERT INTO `items_per_page` (`id`, `items_per`, `items_per_num`) VALUES
(1, '10', 10),
(2, '25', 25),
(3, '50', 50),
(4, '100', 100);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `language`
--

CREATE TABLE `language` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `code` varchar(3) NOT NULL,
  `locale` varchar(5) NOT NULL,
  `is_active` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `language`
--

INSERT INTO `language` (`id`, `name`, `code`, `locale`, `is_active`) VALUES
(1, 'Estonian', 'et', 'et_EE', 1),
(2, 'English', 'en', 'en_US', 1),
(3, 'Russian', 'ru', 'ru_RU', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `login`
--

CREATE TABLE `login` (
  `id` int(10) UNSIGNED NOT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `login`
--

INSERT INTO `login` (`id`, `person_id`, `username`, `password`, `is_enabled`) VALUES
(1, 1, 'jdoe', 'p@$$.w0rd', 0),
(2, 3, 'brobinson', 'p@$$.w0rd', 1),
(3, 4, 'mho', 'p@$$.w0rd', 1),
(4, 7, 'kwolfe', 'p@$$.w0rd', 0),
(5, NULL, 'system', 'p@$$.w0rd', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT 0,
  `left` int(11) DEFAULT NULL,
  `right` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Andmete tõmmistamine tabelile `menu`
--

INSERT INTO `menu` (`id`, `parent_id`, `depth`, `left`, `right`) VALUES
(1, NULL, 0, 2, 3),
(42, NULL, 0, 4, 11),
(43, 42, 1, 5, 6),
(44, 42, 1, 7, 10),
(47, 44, 2, 8, 9),
(50, NULL, 0, 12, 13),
(51, NULL, 0, 20, 21),
(56, 99, 1, 29, 30),
(59, 62, 1, 15, 16),
(62, NULL, 0, 14, 19),
(81, 99, 1, 27, 28),
(84, 62, 1, 17, 18),
(99, NULL, 0, 26, 31),
(105, 107, 1, 23, 24),
(107, NULL, 0, 22, 25);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `menu_content`
--

CREATE TABLE `menu_content` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED DEFAULT NULL,
  `menu_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content_type` int(10) UNSIGNED DEFAULT NULL,
  `group_title_id` int(10) UNSIGNED DEFAULT NULL,
  `redirect_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homely_url` int(10) UNSIGNED DEFAULT NULL,
  `is_redirect` int(10) UNSIGNED DEFAULT NULL,
  `selected_page_id` int(10) UNSIGNED DEFAULT NULL,
  `selected_page_locked` int(11) DEFAULT 0,
  `target_type` int(10) UNSIGNED DEFAULT NULL,
  `is_enabled` int(11) DEFAULT 0,
  `title_exists` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `menu_content`
--

INSERT INTO `menu_content` (`id`, `menu_id`, `menu_text`, `content_type`, `group_title_id`, `redirect_url`, `homely_url`, `is_redirect`, `selected_page_id`, `selected_page_locked`, `target_type`, `is_enabled`, `title_exists`) VALUES
(1, 1, 'Home', 1, NULL, '/', 1, NULL, NULL, 1, NULL, 1, 1),
(42, 42, 'Organisation', 2, NULL, '/organisation/about-organisation', 1, NULL, NULL, 0, NULL, 1, 1),
(43, 43, 'Contacts', 2, NULL, '/contacts/about-contacts', 1, NULL, NULL, 0, NULL, 1, 1),
(44, 44, 'Board', 2, NULL, '/board/about-board', 1, NULL, NULL, 1, NULL, 1, 1),
(47, 47, 'Gallery', 8, NULL, 'https://qcubed.eu', NULL, 1, NULL, 0, 1, 1, 1),
(50, 50, 'Uudised', 3, 18, '/uudised/poliitika-uudised', 1, NULL, NULL, 0, NULL, 1, 1),
(51, 51, 'Suunamine', 7, NULL, '/board/about-board', 1, 2, 44, 1, NULL, 1, 1),
(56, 56, 'Uus test', 7, NULL, '/', 1, 2, 1, 0, NULL, 1, 1),
(59, 59, 'SÃ¼ndmuste kalender', 5, NULL, '/sundmuste-kalender', 1, NULL, NULL, 0, NULL, 1, 1),
(62, 62, 'View', 10, NULL, '/view/teine-veateade', 1, NULL, NULL, 0, NULL, 1, 1),
(81, 81, 'Teine kalender', 5, NULL, '/teine-kalender', 1, NULL, NULL, 0, NULL, 1, 1),
(84, 84, 'Teine proovi artikkel', 2, NULL, '/teine-proovi-artikkel/teine-proovi-artikkel', 1, NULL, NULL, 0, NULL, 1, 1),
(99, 99, 'Esimene veebikonto', 2, NULL, '/esimene-veebikonto/esimene-veebikonto', 1, NULL, NULL, 0, NULL, 1, 1),
(105, 105, 'Neljas veebikonto', 2, NULL, '/neljas-veebikonto/neljas-veebikonto-naide', 1, NULL, NULL, 0, NULL, 1, 1),
(107, 107, 'Teine veebikonto', 9, NULL, '#', 1, NULL, NULL, 0, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `metadata`
--

CREATE TABLE `metadata` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_content_id` int(10) UNSIGNED DEFAULT NULL,
  `keywords` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `metadata`
--

INSERT INTO `metadata` (`id`, `menu_content_id`, `keywords`, `description`, `author`) VALUES
(1, 1, 'Avalehe võtmesõnad', 'Avalehe kirjeldus', 'Kodulehe autor'),
(35, 42, NULL, NULL, NULL),
(36, 43, NULL, NULL, NULL),
(37, 44, '', '', ''),
(41, 50, NULL, NULL, NULL),
(46, 59, '', '', ''),
(52, 81, NULL, NULL, NULL),
(54, 84, NULL, NULL, NULL),
(68, 99, NULL, NULL, NULL),
(69, 105, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `milestone`
--

CREATE TABLE `milestone` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `milestone`
--

INSERT INTO `milestone` (`id`, `project_id`, `name`) VALUES
(1, 1, 'Milestone A'),
(2, 1, 'Milestone B'),
(3, 1, 'Milestone C'),
(4, 2, 'Milestone D'),
(5, 2, 'Milestone E'),
(6, 3, 'Milestone F'),
(7, 4, 'Milestone G'),
(8, 4, 'Milestone H'),
(9, 4, 'Milestone I'),
(10, 4, 'Milestone J');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `news_group_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `news_category_id` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `author_source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint(1) DEFAULT 0,
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int(10) UNSIGNED DEFAULT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(10) UNSIGNED DEFAULT 2,
  `confirmation_asking` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `news`
--

INSERT INTO `news` (`id`, `news_group_id`, `title`, `news_category_id`, `category`, `title_slug`, `picture`, `picture_description`, `author_source`, `content`, `post_date`, `post_update_date`, `use_publication_date`, `available_from`, `expiry_date`, `assigned_by_user`, `author`, `status`, `confirmation_asking`) VALUES
(19, 50, 'Esimene katse', 2, 'Life', '/uudised/poliitika-uudised/esimene-katse', NULL, NULL, NULL, '<p>wertyuiol&ouml; qwertyuil&ouml;</p>\n', '2021-06-19 21:09:23', '2021-08-04 15:11:35', 0, NULL, NULL, 2, 'Alex Smith', 1, 0),
(24, 50, 'Teine katsetus', 2, 'Life', '/uudised/poliitika-uudised/teine-katsetus', NULL, NULL, NULL, NULL, '2021-06-19 21:19:58', '2021-07-21 21:33:23', 0, NULL, NULL, 2, 'Alex Smith', 1, 0),
(26, 50, 'Kolmas katse', 3, 'Education', '/uudised/poliitika-uudised/kolmas-katse', NULL, NULL, NULL, NULL, '2021-06-19 21:22:49', '2021-07-21 21:33:45', 0, NULL, NULL, 2, 'Alex Smith', 3, 0),
(30, 50, 'Neljas katse', 4, 'Business', '/uudised/poliitika-uudised/neljas-katse', NULL, NULL, NULL, '', '2021-06-19 22:28:46', '2021-08-04 15:11:00', 0, NULL, NULL, 1, 'John Doe', 1, 0),
(32, 50, 'Vaatame siis', 2, 'Life', '/uudised/poliitika-uudised/vaatame-siis', NULL, NULL, NULL, '<p>qwertyuiop&ouml; qwertyul</p>\n', '2021-06-19 23:46:57', '2021-08-04 15:12:56', 0, NULL, NULL, 1, 'John Doe', 1, 0),
(33, 50, 'Vaatame teist korda uuesti?', 2, 'Life', '/uudised/poliitika-uudised/vaatame-teist-korda-uuesti', NULL, NULL, NULL, NULL, '2021-06-20 00:09:57', '2021-08-01 00:56:01', 0, NULL, NULL, 1, 'John Doe', 2, 0),
(35, 50, 'Kas saab juba valmis?', 1, 'Politics', '/uudised/poliitika-uudised/kas-saab-juba-valmis', NULL, NULL, NULL, '<p>wertyul&ouml; wertyuil</p>\n', '2021-06-20 00:22:15', '2021-08-04 15:09:39', 1, '2021-08-03 00:00:00', NULL, 1, 'John Doe', 4, 0),
(40, 50, 'Viies katse', 2, 'Life', '/uudised/poliitika-uudised/viies-katse', NULL, NULL, NULL, '<p>sdfghjkl&ouml; ol,</p>\n', '2021-08-02 18:12:43', '2021-08-04 15:08:57', 0, NULL, NULL, 1, 'John Doe', 1, 0),
(42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-08-02 19:13:58', NULL, 0, NULL, NULL, NULL, NULL, 2, 0),
(43, 50, 'Viies katse', 4, 'Business', '/uudised/poliitika-uudised/viies-katse', NULL, NULL, NULL, NULL, '2021-08-02 23:39:34', '2021-08-04 15:10:31', 0, NULL, NULL, 1, 'John Doe', 2, 0),
(44, 50, 'Teeme proovi', 3, 'Education', '/uudised/poliitika-uudised/teeme-proovi', NULL, NULL, NULL, NULL, '2021-08-02 23:42:36', '2021-08-04 15:07:04', 1, '2021-08-29 00:00:00', NULL, 1, 'John Doe', 4, 0),
(45, 50, 'AnalÃ¼Ã¼tik: rÃ¤ndesurve Leedule vÃµib olla Valgevene pettemanÃ¶Ã¶ver', 1, 'Politics', '/uudised/poliitika-uudised/analuutik-randesurve-leedule-voib-olla-valgevene-pettemanoover', NULL, NULL, NULL, '<p>Rahvusvahelise kaitseuuringute keskuse anal&uuml;&uuml;tiku Tomas Jermalaviciuse hinnangul v&otilde;ib Leedule avaldatav r&auml;ndesurve olla Valgevene petteman&ouml;&ouml;ver, et juhtida Euroopa t&auml;helepanu muudelt piirkondadelt mujale.</p>\n\n<p>Esmasp&auml;evased kaadrid Leedu piirialalt Rudninkai p&otilde;genikelaagrist hakkavad muutuma juba Leedu igap&auml;evaks. &Uuml;hiskonna rahulolematus, radikaliseerumine, sisepoliitiline kriis ja riigi v&auml;ljakurnamine on Valgevene h&uuml;briidr&uuml;nnaku esimesed sihid, vahendas &quot;Aktuaalne kaamera&quot;.</p>\n', '2021-08-03 22:24:03', '2021-08-04 15:07:00', 1, '2021-12-31 00:00:00', NULL, 1, 'John Doe', 4, 0);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `news_editors_assn`
--

CREATE TABLE `news_editors_assn` (
  `news_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `news_editors_assn`
--

INSERT INTO `news_editors_assn` (`news_id`, `user_id`) VALUES
(19, 1),
(24, 1),
(26, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `person`
--

CREATE TABLE `person` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `person`
--

INSERT INTO `person` (`id`, `first_name`, `last_name`) VALUES
(1, 'John', 'Doe'),
(2, 'Kendall', 'Public'),
(3, 'Ben', 'Robinson'),
(4, 'Mike', 'Ho'),
(5, 'Alex', 'Smith'),
(6, 'Wendy', 'Smith'),
(7, 'Karen', 'Wolfe'),
(8, 'Samantha', 'Jones'),
(9, 'Linda', 'Brady'),
(10, 'Jennifer', 'Smith'),
(11, 'Brett', 'Carlisle'),
(12, 'Jacob', 'Pratt'),
(13, 'Tiit', 'Papp'),
(14, 'Ene', 'Papp'),
(15, 'Sirle', 'Papp');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `person_persontype_assn`
--

CREATE TABLE `person_persontype_assn` (
  `person_id` int(10) UNSIGNED NOT NULL,
  `person_type_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `person_persontype_assn`
--

INSERT INTO `person_persontype_assn` (`person_id`, `person_type_id`) VALUES
(1, 2),
(1, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(5, 5),
(7, 2),
(7, 4),
(9, 3),
(10, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `person_type`
--

CREATE TABLE `person_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `person_type`
--

INSERT INTO `person_type` (`id`, `name`) VALUES
(4, 'Company Car'),
(1, 'Contractor'),
(3, 'Inactive'),
(2, 'Manager'),
(5, 'Works From Home');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `person_with_lock`
--

CREATE TABLE `person_with_lock` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `sys_timestamp` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `person_with_lock`
--

INSERT INTO `person_with_lock` (`id`, `first_name`, `last_name`, `sys_timestamp`) VALUES
(1, 'John', 'Doe', NULL),
(2, 'Kendall', 'Public', NULL),
(3, 'Ben', 'Robinson', NULL),
(4, 'Mike', 'Ho', NULL),
(5, 'Alfred', 'Newman', NULL),
(6, 'Wendy', 'Johnson', NULL),
(7, 'Karen', 'Wolfe', NULL),
(8, 'Samantha', 'Jones', NULL),
(9, 'Linda', 'Brady', NULL),
(10, 'Jennifer', 'Smith', NULL),
(11, 'Brett', 'Carlisle', NULL),
(12, 'Jacob', 'Pratt', NULL);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `project`
--

CREATE TABLE `project` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_status_type_id` int(10) UNSIGNED NOT NULL,
  `manager_person_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `spent` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `project`
--

INSERT INTO `project` (`id`, `project_status_type_id`, `manager_person_id`, `name`, `description`, `start_date`, `end_date`, `budget`, `spent`) VALUES
(1, 3, 7, 'ACME Website Redesign', 'The redesign of the main website for ACME Incorporated', '2004-03-01', '2004-07-01', '9560.25', '10250.75'),
(2, 1, 4, 'State College HR System', 'Implementation of a back-office Human Resources system for State College', '2006-02-15', NULL, '80500.00', '73200.00'),
(3, 1, 1, 'Blueman Industrial Site Architecture', 'Main website architecture for the Blueman Industrial Group', '2006-03-01', '2006-04-15', '2500.00', '4200.50'),
(4, 2, 7, 'ACME Payment System', 'Accounts Payable payment system for ACME Incorporated', '2005-08-15', '2005-10-20', '5124.67', '5175.30');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `project_status_type`
--

CREATE TABLE `project_status_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `guidelines` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `project_status_type`
--

INSERT INTO `project_status_type` (`id`, `name`, `description`, `guidelines`, `is_active`) VALUES
(1, 'Open', 'The project is currently active', 'All projects that we are working on should be in this state', 1),
(2, 'Cancelled', 'The project has been canned', NULL, 1),
(3, 'Completed', 'The project has been completed successfully', 'Celebrate successes!', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `qc_watchers`
--

CREATE TABLE `qc_watchers` (
  `table_key` varchar(200) NOT NULL,
  `ts` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `related_project_assn`
--

CREATE TABLE `related_project_assn` (
  `project_id` int(10) UNSIGNED NOT NULL,
  `child_project_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `related_project_assn`
--

INSERT INTO `related_project_assn` (`project_id`, `child_project_id`) VALUES
(1, 3),
(1, 4),
(4, 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `status`
--

CREATE TABLE `status` (
  `id` int(10) UNSIGNED NOT NULL,
  `is_enabled` int(11) NOT NULL,
  `written_status` varchar(255) NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) NOT NULL,
  `visibility` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `status`
--

INSERT INTO `status` (`id`, `is_enabled`, `written_status`, `drawn_status`, `visibility`) VALUES
(1, 1, 'Published', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #449d44; line-height: .1;\"></i>  Published', 1),
(2, 2, 'Hidden', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ff0000; line-height: .1;\"></i> Hidden', 1),
(3, 3, 'Draft', '<i class=\"fa fa-circle-o fa-lg\" aria-hidden=\"true\" style=\"color: #000000; line-height: .1;\"></i> Draft', 1),
(4, 4, 'Waiting...', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ffb00c; line-height: .1;\"></i> Waiting...', 1);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `target_group_of_calendar`
--

CREATE TABLE `target_group_of_calendar` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_enabled` int(10) UNSIGNED DEFAULT 2,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `target_group_of_calendar`
--

INSERT INTO `target_group_of_calendar` (`id`, `name`, `is_enabled`, `post_date`, `post_update_date`) VALUES
(2, 'Kalenderplaan', 1, '2021-06-09 00:47:50', '2021-07-21 01:13:51'),
(3, 'PensionÃ¤ride kalenderplaan', 1, '2021-07-02 20:02:13', '2021-08-03 01:02:06'),
(4, 'Sportlaste kalenderplaan', 1, '2021-07-04 23:09:26', '2021-07-21 18:38:06'),
(9, 'Taidlejate kalenderplaan', 2, '2021-07-20 23:07:10', '2021-07-21 17:51:09');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `target_type`
--

CREATE TABLE `target_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Andmete tõmmistamine tabelile `target_type`
--

INSERT INTO `target_type` (`id`, `name`, `target`) VALUES
(1, 'New Window (_blank)', '_blank'),
(2, 'Topmost Window (_top)', '_top'),
(3, 'Same Window (_self)', '_self'),
(4, 'Parent Window (_parent)', '_parent');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `team_member_project_assn`
--

CREATE TABLE `team_member_project_assn` (
  `person_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `team_member_project_assn`
--

INSERT INTO `team_member_project_assn` (`person_id`, `project_id`) VALUES
(1, 3),
(1, 4),
(2, 1),
(2, 2),
(2, 4),
(3, 4),
(4, 2),
(4, 3),
(5, 1),
(5, 2),
(5, 4),
(6, 1),
(6, 3),
(7, 1),
(7, 2),
(8, 1),
(8, 3),
(8, 4),
(9, 2),
(10, 2),
(10, 3),
(11, 4),
(12, 4);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `title_of_newsgroup`
--

CREATE TABLE `title_of_newsgroup` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_reserved` tinyint(1) DEFAULT 0,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `title_of_newsgroup`
--

INSERT INTO `title_of_newsgroup` (`id`, `name`, `is_reserved`, `post_date`, `post_update_date`) VALUES
(15, 'Spordiuudised', 1, '2021-05-25 00:50:33', '2021-06-17 02:10:03'),
(16, 'Uudised', 0, '2020-05-26 02:00:00', '2021-05-30 16:48:50'),
(18, 'Poliitika uudised', 1, '2021-05-25 23:05:45', '2021-08-04 14:57:46'),
(19, 'Sotsiaaluudised', 1, '2020-02-29 23:00:00', '2021-06-17 01:45:18');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `two_key`
--

CREATE TABLE `two_key` (
  `server` varchar(50) NOT NULL,
  `directory` varchar(50) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `person_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Andmete tõmmistamine tabelile `two_key`
--

INSERT INTO `two_key` (`server`, `directory`, `file_name`, `person_id`, `project_id`) VALUES
('cnn.com', 'us', 'news', 1, 1),
('google.com', 'drive', '', 2, 2),
('google.com', 'mail', 'mail.html', 3, 2),
('google.com', 'news', 'news.php', 4, 3),
('mail.google.com', 'mail', 'inbox', 5, NULL),
('yahoo.com', '', '', 6, NULL);

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `type_test`
--

CREATE TABLE `type_test` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `test_int` int(11) DEFAULT NULL,
  `test_float` float DEFAULT NULL,
  `test_text` text DEFAULT NULL,
  `test_bit` tinyint(1) DEFAULT NULL,
  `test_varchar` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `display_real_name_flag` tinyint(1) DEFAULT 0,
  `display_name` varchar(255) DEFAULT NULL,
  `preferred_language` int(10) UNSIGNED DEFAULT NULL,
  `items_per_page_by_assigned_user` int(10) UNSIGNED NOT NULL,
  `preferred_date_time_format` int(10) UNSIGNED DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Andmete tõmmistamine tabelile `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `display_real_name_flag`, `display_name`, `preferred_language`, `items_per_page_by_assigned_user`, `preferred_date_time_format`, `is_enabled`) VALUES
(1, 'John', 'Doe', 'doe@gmail.com', 'johndoe', NULL, 0, NULL, 1, 1, 1, 1),
(2, 'Alex', 'Smith', 'smith@gmail.com', 'alexsmith', NULL, 0, NULL, 2, 2, 7, 1),
(3, 'Samantha', 'Jones', 'samanthajones@gmail.com', 'samantha', NULL, 0, NULL, NULL, 3, NULL, 1),
(4, 'Brett', 'Carlisle', 'carlisle@gmail.com', 'carlisle', NULL, 0, NULL, NULL, 4, NULL, 1);

--
-- Indeksid tõmmistatud tabelitele
--

--
-- Indeksid tabelile `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_address_1` (`person_id`);

--
-- Indeksid tabelile `aktivity`
--
ALTER TABLE `aktivity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled` (`is_enabled`);

--
-- Indeksid tabelile `areas_of_sports`
--
ALTER TABLE `areas_of_sports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled` (`is_enabled`) USING BTREE;

--
-- Indeksid tabelile `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  ADD KEY `category_id_idx` (`category_id`) USING BTREE,
  ADD KEY `user_id_idx` (`assigned_by_user`) USING BTREE;

--
-- Indeksid tabelile `articles_editors_assn`
--
ALTER TABLE `articles_editors_assn`
  ADD PRIMARY KEY (`articles_id`,`user_id`),
  ADD KEY `articles_id_idx` (`articles_id`) USING BTREE,
  ADD KEY `articles_users_idx` (`user_id`);

--
-- Indeksid tabelile `category_of_article`
--
ALTER TABLE `category_of_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled_idx` (`is_enabled`) USING BTREE;

--
-- Indeksid tabelile `category_of_news`
--
ALTER TABLE `category_of_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled_idx` (`is_enabled`) USING BTREE;

--
-- Indeksid tabelile `content_type`
--
ALTER TABLE `content_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING BTREE;

--
-- Indeksid tabelile `content_types_management`
--
ALTER TABLE `content_types_management`
  ADD PRIMARY KEY (`id`),
  ADD KEY `default_frontend_template_id` (`default_frontend_template_id`) USING BTREE;

--
-- Indeksid tabelile `date_and_time_formats`
--
ALTER TABLE `date_and_time_formats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled_idx` (`is_enabled`) USING BTREE;

--
-- Indeksid tabelile `error_pages`
--
ALTER TABLE `error_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  ADD KEY `user_id_idx` (`assigned_by_user`) USING BTREE;

--
-- Indeksid tabelile `error_pages_editors_assn`
--
ALTER TABLE `error_pages_editors_assn`
  ADD PRIMARY KEY (`error_pages_id`,`user_id`),
  ADD KEY `error_pages_id_idx` (`error_pages_id`) USING BTREE,
  ADD KEY `error_pages_users_idx` (`user_id`);

--
-- Indeksid tabelile `events_calendar`
--
ALTER TABLE `events_calendar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `target_group_id_idx` (`target_group_id`) USING BTREE,
  ADD KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  ADD KEY `status_idx` (`status`) USING BTREE,
  ADD KEY `website_target_type_id_idx` (`website_target_type_id`) USING BTREE,
  ADD KEY `facebook_target_type_id_idx` (`facebook_target_type_id`) USING BTREE,
  ADD KEY `menu_content_group_id_idx` (`menu_content_group_id`) USING BTREE;

--
-- Indeksid tabelile `events_calendar_area_sports_assn`
--
ALTER TABLE `events_calendar_area_sports_assn`
  ADD PRIMARY KEY (`event_calendar_id`,`area_sports_id`),
  ADD KEY `area_sports_id_idx` (`area_sports_id`) USING BTREE;

--
-- Indeksid tabelile `events_calendar_editors_assn`
--
ALTER TABLE `events_calendar_editors_assn`
  ADD PRIMARY KEY (`events_calendar_id`,`user_id`),
  ADD KEY `events_calendar_id_idx` (`events_calendar_id`) USING BTREE,
  ADD KEY `user_id_idx` (`user_id`) USING BTREE;

--
-- Indeksid tabelile `frontend_links`
--
ALTER TABLE `frontend_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_types_managament_id_idx` (`content_types_managament_id`) USING BTREE,
  ADD KEY `is_activated_idx` (`is_activated`) USING BTREE;

--
-- Indeksid tabelile `frontend_options`
--
ALTER TABLE `frontend_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_idx` (`status`) USING BTREE;

--
-- Indeksid tabelile `items_per_page`
--
ALTER TABLE `items_per_page`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active_idx` (`is_active`) USING BTREE;

--
-- Indeksid tabelile `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IDX_login_2` (`username`),
  ADD UNIQUE KEY `IDX_login_1` (`person_id`);

--
-- Indeksid tabelile `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `menu_content`
--
ALTER TABLE `menu_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id_idx` (`menu_id`) USING BTREE,
  ADD KEY `content_type_idx` (`content_type`) USING BTREE,
  ADD KEY `target_type_idx` (`target_type`) USING BTREE,
  ADD KEY `selected_page_id_idx` (`selected_page_id`) USING BTREE,
  ADD KEY `group_title_id_idx` (`group_title_id`) USING BTREE;

--
-- Indeksid tabelile `metadata`
--
ALTER TABLE `metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE;

--
-- Indeksid tabelile `milestone`
--
ALTER TABLE `milestone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_milestoneproj_1` (`project_id`);

--
-- Indeksid tabelile `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_category_id_idx` (`news_category_id`) USING BTREE,
  ADD KEY `post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `available_from_idx` (`available_from`) USING BTREE,
  ADD KEY `status_idx` (`status`) USING BTREE,
  ADD KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  ADD KEY `news_group_id_idx` (`news_group_id`) USING BTREE;

--
-- Indeksid tabelile `news_editors_assn`
--
ALTER TABLE `news_editors_assn`
  ADD PRIMARY KEY (`news_id`,`user_id`),
  ADD KEY `news_id_idx` (`news_id`) USING BTREE,
  ADD KEY `news_users_assn_2` (`user_id`);

--
-- Indeksid tabelile `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_person_1` (`last_name`);

--
-- Indeksid tabelile `person_persontype_assn`
--
ALTER TABLE `person_persontype_assn`
  ADD PRIMARY KEY (`person_id`,`person_type_id`),
  ADD KEY `person_type_id` (`person_type_id`);

--
-- Indeksid tabelile `person_type`
--
ALTER TABLE `person_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksid tabelile `person_with_lock`
--
ALTER TABLE `person_with_lock`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_project_1` (`project_status_type_id`),
  ADD KEY `IDX_project_2` (`manager_person_id`);

--
-- Indeksid tabelile `project_status_type`
--
ALTER TABLE `project_status_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IDX_projectstatustype_1` (`name`);

--
-- Indeksid tabelile `qc_watchers`
--
ALTER TABLE `qc_watchers`
  ADD PRIMARY KEY (`table_key`);

--
-- Indeksid tabelile `related_project_assn`
--
ALTER TABLE `related_project_assn`
  ADD PRIMARY KEY (`project_id`,`child_project_id`),
  ADD KEY `IDX_relatedprojectassn_2` (`child_project_id`);

--
-- Indeksid tabelile `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `target_group_of_calendar`
--
ALTER TABLE `target_group_of_calendar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_enabled_idx` (`is_enabled`) USING BTREE;

--
-- Indeksid tabelile `target_type`
--
ALTER TABLE `target_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING BTREE;

--
-- Indeksid tabelile `team_member_project_assn`
--
ALTER TABLE `team_member_project_assn`
  ADD PRIMARY KEY (`person_id`,`project_id`),
  ADD KEY `IDX_teammemberprojectassn_2` (`project_id`);

--
-- Indeksid tabelile `title_of_newsgroup`
--
ALTER TABLE `title_of_newsgroup`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `two_key`
--
ALTER TABLE `two_key`
  ADD PRIMARY KEY (`server`,`directory`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indeksid tabelile `type_test`
--
ALTER TABLE `type_test`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_idx` (`username`) USING BTREE,
  ADD KEY `first_name_idx` (`first_name`) USING BTREE,
  ADD KEY `last_name_idx` (`last_name`) USING BTREE,
  ADD KEY `items_per_page_by_assigned_user_idx` (`items_per_page_by_assigned_user`) USING BTREE,
  ADD KEY `preferred_date_time_format_idx` (`preferred_date_time_format`) USING BTREE,
  ADD KEY `preferred_language_id` (`preferred_language`) USING BTREE;

--
-- AUTO_INCREMENT tõmmistatud tabelitele
--

--
-- AUTO_INCREMENT tabelile `address`
--
ALTER TABLE `address`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT tabelile `aktivity`
--
ALTER TABLE `aktivity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabelile `areas_of_sports`
--
ALTER TABLE `areas_of_sports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT tabelile `article`
--
ALTER TABLE `article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT tabelile `category_of_article`
--
ALTER TABLE `category_of_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT tabelile `category_of_news`
--
ALTER TABLE `category_of_news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `content_type`
--
ALTER TABLE `content_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT tabelile `content_types_management`
--
ALTER TABLE `content_types_management`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT tabelile `date_and_time_formats`
--
ALTER TABLE `date_and_time_formats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT tabelile `error_pages`
--
ALTER TABLE `error_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT tabelile `events_calendar`
--
ALTER TABLE `events_calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT tabelile `frontend_links`
--
ALTER TABLE `frontend_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT tabelile `frontend_options`
--
ALTER TABLE `frontend_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT tabelile `items_per_page`
--
ALTER TABLE `items_per_page`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `language`
--
ALTER TABLE `language`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT tabelile `login`
--
ALTER TABLE `login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT tabelile `menu_content`
--
ALTER TABLE `menu_content`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT tabelile `metadata`
--
ALTER TABLE `metadata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT tabelile `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT tabelile `person`
--
ALTER TABLE `person`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT tabelile `person_type`
--
ALTER TABLE `person_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `person_with_lock`
--
ALTER TABLE `person_with_lock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT tabelile `project`
--
ALTER TABLE `project`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `project_status_type`
--
ALTER TABLE `project_status_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT tabelile `status`
--
ALTER TABLE `status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `target_group_of_calendar`
--
ALTER TABLE `target_group_of_calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT tabelile `target_type`
--
ALTER TABLE `target_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `title_of_newsgroup`
--
ALTER TABLE `title_of_newsgroup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT tabelile `type_test`
--
ALTER TABLE `type_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabelile `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tõmmistatud tabelite piirangud
--

--
-- Piirangud tabelile `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `person_address` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Piirangud tabelile `areas_of_sports`
--
ALTER TABLE `areas_of_sports`
  ADD CONSTRAINT `areas_of_sports_ibfk` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `category_id_article_fk` FOREIGN KEY (`category_id`) REFERENCES `category_of_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_content_id_article_fk` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_article_fk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `articles_editors_assn`
--
ALTER TABLE `articles_editors_assn`
  ADD CONSTRAINT `articles_users_assn_1` FOREIGN KEY (`articles_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articles_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `category_of_article`
--
ALTER TABLE `category_of_article`
  ADD CONSTRAINT `is_enabled_ibfk_1` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `category_of_news`
--
ALTER TABLE `category_of_news`
  ADD CONSTRAINT `is_enabled_ibfk_2` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `content_types_management`
--
ALTER TABLE `content_types_management`
  ADD CONSTRAINT `default_frontend_template_id_fronted_options_fk` FOREIGN KEY (`default_frontend_template_id`) REFERENCES `frontend_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `date_and_time_formats`
--
ALTER TABLE `date_and_time_formats`
  ADD CONSTRAINT `is_enabled_ibfk_3` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `error_pages`
--
ALTER TABLE `error_pages`
  ADD CONSTRAINT `error_pages_ibfk_1` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `error_pages_ibfk_2` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `error_pages_editors_assn`
--
ALTER TABLE `error_pages_editors_assn`
  ADD CONSTRAINT `error_pages_users_assn_1` FOREIGN KEY (`error_pages_id`) REFERENCES `error_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `error_pages_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `events_calendar`
--
ALTER TABLE `events_calendar`
  ADD CONSTRAINT `events_calendar_fk_1` FOREIGN KEY (`website_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_fk_3` FOREIGN KEY (`target_group_id`) REFERENCES `target_group_of_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_fk_4` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_fk_5` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_fk_6` FOREIGN KEY (`facebook_target_type_id`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_fk_7` FOREIGN KEY (`menu_content_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `events_calendar_area_sports_assn`
--
ALTER TABLE `events_calendar_area_sports_assn`
  ADD CONSTRAINT `eventscalendarareasportsassn_1` FOREIGN KEY (`event_calendar_id`) REFERENCES `events_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventscalendarareasportsassn_2` FOREIGN KEY (`area_sports_id`) REFERENCES `areas_of_sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `events_calendar_editors_assn`
--
ALTER TABLE `events_calendar_editors_assn`
  ADD CONSTRAINT `events_calendar_users_assn_1` FOREIGN KEY (`events_calendar_id`) REFERENCES `events_calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `events_calendar_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `frontend_links`
--
ALTER TABLE `frontend_links`
  ADD CONSTRAINT `content_types_managament_id_frontend_links_fk` FOREIGN KEY (`content_types_managament_id`) REFERENCES `content_types_management` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `is_activated_idx` FOREIGN KEY (`is_activated`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `frontend_options`
--
ALTER TABLE `frontend_options`
  ADD CONSTRAINT `frontend_options_ibfk_1` FOREIGN KEY (`status`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `language`
--
ALTER TABLE `language`
  ADD CONSTRAINT `is_active_fk` FOREIGN KEY (`is_active`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `person_login` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Piirangud tabelile `menu_content`
--
ALTER TABLE `menu_content`
  ADD CONSTRAINT `content_type_menu_content_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_title_id_menu_content_fk` FOREIGN KEY (`group_title_id`) REFERENCES `title_of_newsgroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_id_menu_content_fk` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `selected_page_id_fk` FOREIGN KEY (`selected_page_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `target_type_menu_content_fk` FOREIGN KEY (`target_type`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `metadata`
--
ALTER TABLE `metadata`
  ADD CONSTRAINT `menu_content_id_metadata_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `milestone`
--
ALTER TABLE `milestone`
  ADD CONSTRAINT `project_milestone` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);

--
-- Piirangud tabelile `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_category_id`) REFERENCES `category_of_news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_ibfk_4` FOREIGN KEY (`news_group_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `news_editors_assn`
--
ALTER TABLE `news_editors_assn`
  ADD CONSTRAINT `news_users_assn_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  ADD CONSTRAINT `news_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Piirangud tabelile `person_persontype_assn`
--
ALTER TABLE `person_persontype_assn`
  ADD CONSTRAINT `person_persontype_assn_1` FOREIGN KEY (`person_type_id`) REFERENCES `person_type` (`id`),
  ADD CONSTRAINT `person_persontype_assn_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Piirangud tabelile `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `person_project` FOREIGN KEY (`manager_person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `project_status_type_project` FOREIGN KEY (`project_status_type_id`) REFERENCES `project_status_type` (`id`);

--
-- Piirangud tabelile `related_project_assn`
--
ALTER TABLE `related_project_assn`
  ADD CONSTRAINT `related_project_assn_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  ADD CONSTRAINT `related_project_assn_2` FOREIGN KEY (`child_project_id`) REFERENCES `project` (`id`);

--
-- Piirangud tabelile `target_group_of_calendar`
--
ALTER TABLE `target_group_of_calendar`
  ADD CONSTRAINT `is_enabled_id_fk` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Piirangud tabelile `team_member_project_assn`
--
ALTER TABLE `team_member_project_assn`
  ADD CONSTRAINT `person_team_member_project_assn` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `project_team_member_project_assn` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);

--
-- Piirangud tabelile `two_key`
--
ALTER TABLE `two_key`
  ADD CONSTRAINT `two_key_person` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `two_key_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);

--
-- Piirangud tabelile `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `items_per_page_by_assigned_user_fk` FOREIGN KEY (`items_per_page_by_assigned_user`) REFERENCES `items_per_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `preferred_date_time_format_fk` FOREIGN KEY (`preferred_date_time_format`) REFERENCES `date_and_time_formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `preferred_language_fk` FOREIGN KEY (`preferred_language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
