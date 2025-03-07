-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 02 fév. 2025 à 20:38
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `butify`
--

CREATE DATABASE IF NOT EXISTS butify;
USE butify;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `grade` int NOT NULL DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '/storage/user/profile/default.png',
  `banner` varchar(255) NOT NULL DEFAULT '/storage/user/banner/default.png',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `artist` tinyint NOT NULL,
  `expire` date DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `report_artist`
--

CREATE TABLE `report_artist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `artist` int NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `report_playlist`
--

CREATE TABLE `report_playlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `playlist` int NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `report_reason`
--

CREATE TABLE `report_reason` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `report_sound`
--

CREATE TABLE `report_sound` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `sound` int NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Structure de la table `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `sound` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `like_artist`
--

DROP TABLE IF EXISTS `like_artist`;
CREATE TABLE IF NOT EXISTS `like_artist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `artist` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `like_playlist`
--

DROP TABLE IF EXISTS `like_playlist`;
CREATE TABLE IF NOT EXISTS `like_playlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `playlist` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `like_sound`
--

DROP TABLE IF EXISTS `like_sound`;
CREATE TABLE IF NOT EXISTS `like_sound` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int NOT NULL,
  `sound` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '/storage/playlist/default.png',
  `public` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `playlist_collaborator`
--

DROP TABLE IF EXISTS `playlist_collaborator`;
CREATE TABLE IF NOT EXISTS `playlist_collaborator` (
  `id` int NOT NULL AUTO_INCREMENT,
  `playlist` int NOT NULL,
  `collaborator` int NOT NULL,
  `modify` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `playlist` (`playlist`),
  KEY `collaborator` (`collaborator`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `playlist_sound`
--

DROP TABLE IF EXISTS `playlist_sound`;
CREATE TABLE IF NOT EXISTS `playlist_sound` (
  `id` int NOT NULL AUTO_INCREMENT,
  `playlist` int NOT NULL,
  `sound` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `playlist` (`playlist`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `sound`
--

DROP TABLE IF EXISTS `sound`;
CREATE TABLE IF NOT EXISTS `sound` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` int NOT NULL,
  `type` smallint NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '/storage/sound/default.png',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artist` (`artist`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
CREATE TABLE IF NOT EXISTS `subscription` (
  `user` int not null,
  `type` varchar(255) null,
  `created_at` datetime null,
  `price` float null,
  `update_at` datetime null,
  `session` varchar(255) not null,
  `payment` varchar(255) null,
  `subscription` varchar(255) null,
  PRIMARY KEY (`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Structure de la table `youtube`
--

DROP TABLE IF EXISTS `youtube`;
CREATE TABLE IF NOT EXISTS `youtube` (
  `value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `exedeed` tinyint NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
