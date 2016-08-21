-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Gegenereerd op: 21 aug 2016 om 16:33
-- Serverversie: 5.7.13-0ubuntu0.16.04.2
-- PHP-versie: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testassignment`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `form_fields`
--

CREATE TABLE IF NOT EXISTS `form_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id of record',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of creation',
  `date_timedus` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unixtime` int(11) NOT NULL,
  `phptime` int(11) NOT NULL,
  `datum` char(16) NOT NULL COMMENT 'de datum',
  `e-mail` varchar(254) NOT NULL COMMENT 'the emailaddress',
  `bericht` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
