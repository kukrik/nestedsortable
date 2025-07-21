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

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `articles_editors_assn`
--

CREATE TABLE `articles_editors_assn` (
  `articles_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `error_pages_editors_assn`
--

CREATE TABLE `error_pages_editors_assn` (
  `error_pages_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Tabeli struktuur tabelile `events_calendar_area_sports_assn`
--

CREATE TABLE `events_calendar_area_sports_assn` (
  `event_calendar_id` int(10) UNSIGNED NOT NULL,
  `area_sports_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `events_calendar_editors_assn`
--

CREATE TABLE `events_calendar_editors_assn` (
  `events_calendar_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, NULL, 0, 2, 3);

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
(1, 1, 'Home', 1, NULL, '/', 1, NULL, NULL, 1, NULL, 1, 1);

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
(1, 1, 'Avalehe võtmesõnad', 'Avalehe kirjeldus', 'Kodulehe autor');

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

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `news_editors_assn`
--

CREATE TABLE `news_editors_assn` (
  `news_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Tabeli struktuur tabelile `title_of_newsgroup`
--

CREATE TABLE `title_of_newsgroup` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_reserved` tinyint(1) DEFAULT 0,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
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
-- Indeksid tõmmistatud tabelitele
--

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
-- Indeksid tabelile `title_of_newsgroup`
--
ALTER TABLE `title_of_newsgroup`
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
-- AUTO_INCREMENT tabelile `aktivity`
--
ALTER TABLE `aktivity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabelile `areas_of_sports`
--
ALTER TABLE `areas_of_sports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `article`
--
ALTER TABLE `article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `category_of_article`
--
ALTER TABLE `category_of_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `category_of_news`
--
ALTER TABLE `category_of_news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `events_calendar`
--
ALTER TABLE `events_calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `frontend_links`
--
ALTER TABLE `frontend_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

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
-- AUTO_INCREMENT tabelile `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT tabelile `menu_content`
--
ALTER TABLE `menu_content`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT tabelile `metadata`
--
ALTER TABLE `metadata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT tabelile `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `status`
--
ALTER TABLE `status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `target_group_of_calendar`
--
ALTER TABLE `target_group_of_calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `target_type`
--
ALTER TABLE `target_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabelile `title_of_newsgroup`
--
ALTER TABLE `title_of_newsgroup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT tabelile `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Tõmmistatud tabelite piirangud
--

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
-- Piirangud tabelile `target_group_of_calendar`
--
ALTER TABLE `target_group_of_calendar`
  ADD CONSTRAINT `is_enabled_id_fk` FOREIGN KEY (`is_enabled`) REFERENCES `aktivity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
