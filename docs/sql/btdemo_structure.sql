-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 15, 2023 at 01:00 AM
-- Server version: 8.0.32-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `syncdev_btdemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `btd_articles`
--

DROP TABLE IF EXISTS `btd_articles`;
CREATE TABLE `btd_articles` (
  `idArticle` int UNSIGNED NOT NULL,
  `idSection` int NOT NULL,
  `idSubSection` int NOT NULL,
  `idAuthor` int NOT NULL,
  `id` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `lead_paragraph` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `published` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `btd_authors`
--

DROP TABLE IF EXISTS `btd_authors`;
CREATE TABLE `btd_authors` (
  `idAuthor` int UNSIGNED NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `middlename` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `btd_sections`
--

DROP TABLE IF EXISTS `btd_sections`;
CREATE TABLE `btd_sections` (
  `idSection` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `btd_settings`
--

DROP TABLE IF EXISTS `btd_settings`;
CREATE TABLE `btd_settings` (
  `idEntry` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `btd_subsections`
--

DROP TABLE IF EXISTS `btd_subsections`;
CREATE TABLE `btd_subsections` (
  `idSubSection` int UNSIGNED NOT NULL,
  `idSection` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `btd_articles`
--
ALTER TABLE `btd_articles`
  ADD PRIMARY KEY (`idArticle`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `idSection` (`idSection`),
  ADD KEY `idSubSection` (`idSubSection`),
  ADD KEY `idAuthor` (`idAuthor`);

--
-- Indexes for table `btd_authors`
--
ALTER TABLE `btd_authors`
  ADD PRIMARY KEY (`idAuthor`),
  ADD KEY `alias` (`alias`);

--
-- Indexes for table `btd_sections`
--
ALTER TABLE `btd_sections`
  ADD PRIMARY KEY (`idSection`);

--
-- Indexes for table `btd_settings`
--
ALTER TABLE `btd_settings`
  ADD PRIMARY KEY (`idEntry`);

--
-- Indexes for table `btd_subsections`
--
ALTER TABLE `btd_subsections`
  ADD PRIMARY KEY (`idSubSection`),
  ADD KEY `idSection` (`idSection`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `btd_articles`
--
ALTER TABLE `btd_articles`
  MODIFY `idArticle` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `btd_authors`
--
ALTER TABLE `btd_authors`
  MODIFY `idAuthor` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `btd_sections`
--
ALTER TABLE `btd_sections`
  MODIFY `idSection` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `btd_settings`
--
ALTER TABLE `btd_settings`
  MODIFY `idEntry` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `btd_subsections`
--
ALTER TABLE `btd_subsections`
  MODIFY `idSubSection` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
