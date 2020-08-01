-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 29. Jul 2020 um 11:45
-- Server-Version: 10.4.13-MariaDB
-- PHP-Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `roomhealth`
--
CREATE DATABASE IF NOT EXISTS `roomhealth` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `roomhealth`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `body` text NOT NULL,
  `visible` enum('all','loggedin','author','mods','none') NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `issue_id`, `timestamp`, `body`, `visible`) VALUES
(1, 2, 2, 1596003701, 'It is Wednesday, my dudes!', 'none'),
(2, 1, 2, 1596003790, 'Nicht hilfreich.', 'all');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `time_reported` bigint(20) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `item_id` int(11) NOT NULL DEFAULT -1,
  `room_id` int(11) NOT NULL DEFAULT -1,
  `severity` enum('critical','high','normal','low') NOT NULL,
  `assignee_id` int(11) NOT NULL,
  `status` enum('OPEN','CLOSED') NOT NULL,
  `resolution` enum('REPORTED','CONFIRMED','NEEDSINFO','WORKSFORME','DUPLICATE','WONTFIX','RESOLVED') NOT NULL,
  `allow_comments` enum('all','author','mod','admin') NOT NULL DEFAULT 'all',
  `last_updated` bigint(20) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `issues`
--

INSERT INTO `issues` (`id`, `time_reported`, `reporter_id`, `comment`, `item_id`, `room_id`, `severity`, `assignee_id`, `status`, `resolution`, `allow_comments`, `last_updated`) VALUES
(1, 0, 4, 'In Raum A001 ist der ELMO defekt. Auf dem Beamer bekommt man kein Bild vom Elmo. Das Bild vom Computer funktioniert.', 7, -1, 'high', 1, 'OPEN', 'REPORTED', 'all', -1),
(2, 1594500083, 1, 'Dein Vadder hat aufm Elmo gesessen.', -1, 4, 'high', -1, 'CLOSED', 'WONTFIX', 'all', 1596001350),
(3, 1594500835, 1, 'Deine Mudda hat aufm Elmo gesessen.', 7, -1, 'critical', 1, 'OPEN', 'REPORTED', 'all', -1),
(4, 1594570682, 1, 'ohne Gegenstand!', -1, 8, 'low', 1, 'OPEN', 'REPORTED', 'all', -1),
(5, 1594571941, 1, 'Da steht ein Pferd aufm Flur, das ist so niedlich.', -1, 16, 'critical', -1, 'OPEN', 'REPORTED', 'all', -1),
(6, 1594572295, 1, 'Ahle Wurscht!', -1, 16, 'normal', 1, 'OPEN', 'REPORTED', 'all', -1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `room_id` int(11) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `items`
--

INSERT INTO `items` (`id`, `name`, `type`, `room_id`, `comment`) VALUES
(1, 'Klavier', 'special', 1, 'Ein Klavier! Ein Klavier!'),
(2, 'Klavier', 'special', 2, 'Ein Klavier! Ein Klavier!'),
(3, 'Smartboard-Beamer', 'SB', 1, ''),
(4, 'SB-Eingabe', 'SB', 1, ''),
(5, 'Desktop-Computer', 'computer', 1, ''),
(6, 'Monitor', 'monitor', 1, ''),
(7, 'ELMO', 'elmo', 1, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rgroup` varchar(255) NOT NULL,
  `class` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `rgroup`, `class`) VALUES
(1, 'A001', '/A/L0/', ''),
(2, 'A002', '/A/L0/', ''),
(3, 'A004', '/A/L0/', ''),
(4, 'A102', '/A/L1/', '5a'),
(5, 'A103', '/A/L1/', '5b'),
(6, 'A203', '/A/L2/', '6a'),
(7, 'A205', '/A/L2/', '6b'),
(8, 'B002', '/B/L0/', '7a'),
(9, 'B003', '/B/L0/', '7b'),
(10, 'B103', '/B/L1/', '8a'),
(11, 'B104', '/B/L1/', '8b'),
(12, 'B203', '/B/L2/', '8g'),
(13, 'B204', '/B/L2/', '8h'),
(14, 'C002', '/C/L0/', ''),
(15, 'C003', '/C/L0/', ''),
(16, 'C005', '/C/L0/', '9e'),
(17, 'C007', '/C/L0/', '9f'),
(18, 'C102', '/C/L1/', ''),
(19, 'C104', '/C/L1/', ''),
(20, 'C105', '/C/L1/', ''),
(21, 'C203', '/C/L2/', ''),
(22, 'C205', '/C/L2/', ''),
(23, 'C206', '/C/L2/', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `expires` bigint(20) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_id`, `expires`) VALUES
(28, 1, '37049d9d3b6e00ba8cf3397bcd8874c3', 1596019207);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pwhash` varchar(255) NOT NULL,
  `permissions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `login`, `name`, `pwhash`, `permissions`) VALUES
(1, 'swz', 'Ian Schwarz', '$2y$10$bmuckG0j6wvZHytdoZZRYekkd46a9jLLgi2afu2E6Y6mJO8VYSiGe', 1023),
(2, 'abc', 'Abigail Cesar', '$2y$10$YcLgcCJPEIMlTM1Nol.ARemR1Yw5XEi.tTs58Frs6ihJyXtMHohq2', 255),
(3, 'def', 'Dennis Finger', '$2y$10$xhFbrt3t5ASuhSToQ9grq.Yo0oBdnRhCdvWe99C6NGFo/b2LuleIO', 255),
(4, 'xyz', 'Xander Yzmir', '$2y$10$6yiUQymJxAalWbFXX.BB1eyakNtreXaDNrCl0lMF0EUleb.8eQUce', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`login`) USING BTREE;

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
