-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 22. Aug 2020 um 18:52
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
-- Tabellenstruktur für Tabelle `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `classes`
--

INSERT INTO `classes` (`id`, `name`, `room_id`, `teacher_id`) VALUES
(1, '05a', 9, NULL),
(2, '05b', 10, NULL),
(3, '05c', 12, NULL),
(4, '05d', 14, NULL),
(5, '05e', 15, NULL),
(6, '05f', 16, NULL),
(7, '05g', 5, NULL),
(8, '06a', 19, NULL),
(9, '06b', 20, NULL),
(10, '06c', 22, NULL),
(11, '06d', 23, NULL),
(12, '06e', 24, NULL),
(13, '06f', 25, NULL),
(14, '07a', 26, NULL),
(15, '07b', 27, NULL),
(16, '07c', 28, NULL),
(17, '07d', 29, NULL),
(18, '07e', 31, NULL),
(19, '07f', 33, NULL),
(20, '08a', 36, NULL),
(21, '08b', 37, NULL),
(22, '08c', 38, NULL),
(23, '08d', 40, NULL),
(24, '08e', 41, NULL),
(25, '08f', 42, NULL),
(26, '09a', 51, NULL),
(27, '09b', 46, NULL),
(28, '09c', 47, NULL),
(29, '09d', 49, 1),
(30, '09e', 50, NULL),
(31, '09f', 45, NULL),
(32, '10a', 55, NULL),
(33, '10b', 57, NULL),
(34, '10c', 111, NULL),
(35, '10d', 64, NULL),
(36, '10e', 66, NULL),
(37, '10f', 112, NULL);

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
(2, 1, 2, 1596003790, 'Nicht hilfreich.', 'all'),
(5, 1, 5, 1596631251, 'Kommentar eins!\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 'all'),
(6, 1, 5, 1596631394, 'Kommentar zwei!\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 'all'),
(7, 1, 5, 1596631411, 'Kommentar drei - nur für eingeloggte Nutzer!\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 'all'),
(8, 0, 2, 1596634100, 'Ian Schwarz hat die Fehlerbeschreibung geändert:\r\nLÖSUNG: **WORKSFORME**\r\n', 'all'),
(9, 0, 5, 1596634635, 'Ian Schwarz hat die Fehlerbeschreibung geändert:\r\nZUGEWIESEN: **Ian Schwarz**\r\n', 'all'),
(10, 0, 5, 1596723376, 'Ian Schwarz hat die Fehlerbeschreibung geändert:\r\nSTATUS: **CLOSED**\r\nLÖSUNG: **WONTFIX**\r\n', 'all');

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
(1, 0, 4, 'In Raum A001 ist der ELMO defekt. Auf dem Beamer bekommt man kein Bild vom Elmo. Das Bild vom Computer funktioniert.', 5, -1, 'high', 1, 'OPEN', 'NEEDSINFO', 'all', 1596288683),
(2, 1594500083, 1, 'Dein Vadder hat aufm Elmo gesessen.', -1, 4, 'high', 1, 'CLOSED', 'WORKSFORME', 'all', 1596634100),
(3, 1594500835, 1, 'Deine Mudda hat aufm Elmo gesessen.', 5, -1, 'critical', 1, 'OPEN', 'CONFIRMED', 'all', 1596286350),
(4, 1594570682, 1, 'ohne Gegenstand!', -1, 8, 'low', 1, 'CLOSED', 'REPORTED', 'all', 1596621263),
(5, 1594571941, 1, 'Da steht ein Pferd aufm Flur, das ist so niedlich!', -1, 6, 'normal', 1, 'CLOSED', 'WONTFIX', 'all', 1596723376),
(11, 1597221500, 1, 'Falsches Üben von Xylophonmusik quält jeden größeren Zwerg...!\r\n\r\nEcht!!', 17, 5, 'high', -1, 'OPEN', 'REPORTED', 'all', 1598108457);

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
(4, 'Desktop-Computer', 'computer', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rgroup` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `rgroup`, `description`) VALUES
(0, 'A 001', '/A/L0/', 'Musikraum 1'),
(1, 'A 002', '/A/L0/', 'Musikraum 2'),
(2, 'A 003', '/A/L0/', ''),
(3, 'A 004', '/A/L0/', 'Musikraum 3'),
(4, 'A 005', '/A/L0/', ''),
(5, 'A 006', '/A/L0/', 'Kunstraum 1'),
(6, 'A 007', '/A/L0/', 'Kunstraum 2'),
(7, 'A 009', '/A/L0/', 'Kunstraum 3'),
(8, 'A 101', '/A/L1/', ''),
(9, 'A 102', '/A/L1/', ''),
(10, 'A 103', '/A/L1/', ''),
(11, 'A 104', '/A/L1/', ''),
(12, 'A 105', '/A/L1/', ''),
(13, 'A 106', '/A/L1/', 'Differenzierungsraum'),
(14, 'A 107', '/A/L1/', ''),
(15, 'A 108', '/A/L1/', ''),
(16, 'A 109', '/A/L1/', ''),
(17, 'A 110', '/A/L1/', ''),
(18, 'A 202', '/A/L2/', ''),
(19, 'A 203', '/A/L2/', ''),
(20, 'A 205', '/A/L2/', ''),
(21, 'A 206', '/A/L2/', 'Differenzierungsraum'),
(22, 'A 207', '/A/L2/', ''),
(23, 'A 208', '/A/L2/', ''),
(24, 'A 209', '/A/L2/', ''),
(25, 'A 210', '/A/L2/', ''),
(26, 'B 002', '/B/L0/', ''),
(27, 'B 003', '/B/L0/', ''),
(28, 'B 004', '/B/L0/', ''),
(29, 'B 005', '/B/L0/', ''),
(30, 'B 006', '/B/L0/', 'Differenzierungsraum'),
(31, 'B 007', '/B/L0/', ''),
(32, 'B 008', '/B/L0/', ''),
(33, 'B 009', '/B/L0/', ''),
(34, 'B 010', '/B/L0/', ''),
(35, 'B 102', '/B/L1/', 'Profilraum'),
(36, 'B 103', '/B/L1/', ''),
(37, 'B 104', '/B/L1/', ''),
(38, 'B 105', '/B/L1/', ''),
(39, 'B 106', '/B/L1/', 'Chinesisch-Fachraum'),
(40, 'B 107', '/B/L1/', ''),
(41, 'B 108', '/B/L1/', ''),
(42, 'B 109', '/B/L1/', ''),
(43, 'B 110', '/B/L1/', ''),
(44, 'B 202', '/B/L2/', 'Profilraum 3'),
(45, 'B 203', '/B/L2/', ''),
(46, 'B 204', '/B/L2/', ''),
(47, 'B 205', '/B/L2/', ''),
(48, 'B 206', '/B/L2/', 'Differenzierungsraum'),
(49, 'B 207', '/B/L2/', ''),
(50, 'B 208', '/B/L2/', ''),
(51, 'B 209', '/B/L2/', ''),
(52, 'B 210', '/B/L2/', ''),
(53, 'C 002', '/C/L0/', 'Werkraum'),
(54, 'C 003', '/C/L0/', 'Kursraum (E+D-Werkstätten)'),
(55, 'C 005', '/C/L0/', ''),
(56, 'C 006', '/C/L0/', 'Kursraum 05'),
(57, 'C 007', '/C/L0/', ''),
(58, 'C 009', '/C/L0/', ''),
(59, 'C 010', '/C/L0/', 'Kursraum 06'),
(60, 'C 011', '/C/L0/', 'Kursraum 07'),
(61, 'C 102', '/C/L1/', 'Biologie 3 (Profilraum)'),
(62, 'C 103', '/C/L1/', 'Kursraum'),
(63, 'C 104', '/C/L1/', 'Differenzierungsraum'),
(64, 'C 105', '/C/L1/', ''),
(65, 'C 106', '/C/L1/', 'Differenzierungsraum'),
(66, 'C 107', '/C/L1/', ''),
(67, 'C 109', '/C/L1/', ''),
(68, 'C 110', '/C/L1/', 'Kursraum 09'),
(69, 'C 111', '/C/L1/', 'Kursraum 10'),
(70, 'C 201', '/C/L2/', ''),
(71, 'C 202', '/C/L2/', ''),
(72, 'C 203', '/C/L2/', 'Computerraum 1'),
(73, 'C 204', '/C/L2/', ''),
(74, 'C 205', '/C/L2/', 'Computerraum 2'),
(75, 'C 206', '/C/L2/', 'Computerraum 3'),
(76, 'C 208', '/C/L2/', 'Schulradio (Medienraum)'),
(77, 'C 210', '/C/L2/', ''),
(78, 'C 211', '/C/L2/', ''),
(79, 'C 212', '/C/L2/', ''),
(80, 'D 001', '/D/L0/', 'Aula'),
(81, 'D 101', '/D/L1/', ''),
(82, 'D 103', '/D/L1/', ''),
(83, 'D 104', '/D/L1/', ''),
(84, 'D 105', '/D/L1/', ''),
(85, 'D 106', '/D/L1/', ''),
(86, 'D 107', '/D/L1/', ''),
(87, 'D 108', '/D/L1/', ''),
(88, 'D 114', '/D/L1/', ''),
(89, 'D 115', '/D/L1/', ''),
(90, 'D 116', '/D/L1/', ''),
(91, 'D 117', '/D/L1/', ''),
(92, 'E 004', '/E/L0/', 'Theaterraum'),
(93, 'E 101', '/E/L1/', 'Physik 1'),
(94, 'E 102', '/E/L1/', ''),
(95, 'E 103', '/E/L1/', 'Physik 2'),
(96, 'E 104', '/E/L1/', 'Physik 3'),
(97, 'E 106', '/E/L1/', 'Biologie 2 / NaWi'),
(98, 'F 001', '/F/L0/', ''),
(99, 'F 002', '/F/L0/', ''),
(100, 'F 003', '/F/L0/', ''),
(101, 'F 004', '/F/L0/', ''),
(102, 'F 006', '/F/L0/', ''),
(103, 'F 101', '/F/L1/', 'Chemie 1'),
(104, 'F 102', '/F/L1/', ''),
(105, 'F 103', '/F/L1/', 'Chemie 2'),
(106, 'F 104', '/F/L1/', ''),
(107, 'F 105', '/F/L1/', 'Biologie 1'),
(108, 'F 106', '/F/L1/', ''),
(109, 'F 107', '/F/L1/', 'NaWi-Raum (Bio+Che)'),
(110, 'G 101', '/G/L1/', ''),
(111, 'C 008', '/C/L0/', ''),
(112, 'C 108', '/C/L1/', '');

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
(94, 1, 'e68a8b3126221710fe826fde02af9823', 1598118632);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pwhash` varchar(255) NOT NULL,
  `permissions` int(11) NOT NULL,
  `email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `login`, `name`, `pwhash`, `permissions`, `email`) VALUES
(0, '', 'system', 'INVALID', 0, ''),
(1, 'swz', 'Ian Schwarz', '$2y$10$bmuckG0j6wvZHytdoZZRYekkd46a9jLLgi2afu2E6Y6mJO8VYSiGe', 1023, ''),
(2, 'abc', 'Abigail Cesar', '$2y$10$YcLgcCJPEIMlTM1Nol.ARemR1Yw5XEi.tTs58Frs6ihJyXtMHohq2', 255, ''),
(3, 'def', 'Dennis Finger', '$2y$10$xhFbrt3t5ASuhSToQ9grq.Yo0oBdnRhCdvWe99C6NGFo/b2LuleIO', 255, ''),
(4, 'xyz', 'Xander Yzmir', '$2y$10$6yiUQymJxAalWbFXX.BB1eyakNtreXaDNrCl0lMF0EUleb.8eQUce', 0, '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT für Tabelle `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=656;

--
-- AUTO_INCREMENT für Tabelle `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT für Tabelle `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
