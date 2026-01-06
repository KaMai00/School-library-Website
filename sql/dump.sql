-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.0.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bibliothek_mtsp
CREATE DATABASE IF NOT EXISTS `bibliothek_mtsp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bibliothek_mtsp`;

-- Dumping structure for table bibliothek_mtsp.ausleihen
CREATE TABLE IF NOT EXISTS `ausleihen` (
  `ausleihe_id` int(11) NOT NULL AUTO_INCREMENT,
  `buch_id` int(11) NOT NULL,
  `leser_id` int(11) NOT NULL,
  `bibliothekar_id` int(11) NOT NULL,
  `ausleihdatum` date NOT NULL,
  `rueckgabedatum_soll` date DEFAULT NULL,
  `rueckgabedatum_ist` date DEFAULT NULL,
  `status` enum('ausgeliehen','zurückgegeben','überfällig') DEFAULT 'ausgeliehen',
  PRIMARY KEY (`ausleihe_id`),
  KEY `buch_id` (`buch_id`),
  KEY `leser_id` (`leser_id`),
  KEY `bibliothekar_id` (`bibliothekar_id`),
  CONSTRAINT `ausleihen_ibfk_1` FOREIGN KEY (`buch_id`) REFERENCES `buecher` (`buch_id`),
  CONSTRAINT `ausleihen_ibfk_2` FOREIGN KEY (`leser_id`) REFERENCES `leser` (`leser_id`),
  CONSTRAINT `ausleihen_ibfk_3` FOREIGN KEY (`bibliothekar_id`) REFERENCES `bibliothekare` (`bibliothekar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table bibliothek_mtsp.bibliothekare
CREATE TABLE IF NOT EXISTS `bibliothekare` (
  `bibliothekar_id` int(11) NOT NULL AUTO_INCREMENT,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `benutzername` varchar(100) NOT NULL,
  `passwort_hash` char(60) NOT NULL,
  `aktiv` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`bibliothekar_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `benutzername` (`benutzername`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table bibliothek_mtsp.buecher
CREATE TABLE IF NOT EXISTS `buecher` (
  `buch_id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `verlag` varchar(255) DEFAULT NULL,
  `anschaffungspreis` decimal(10,2) DEFAULT NULL,
  `kategorie` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`buch_id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table bibliothek_mtsp.leser
CREATE TABLE IF NOT EXISTS `leser` (
  `leser_id` int(11) NOT NULL AUTO_INCREMENT,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `erstellt_am` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`leser_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
