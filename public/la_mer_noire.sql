-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  sam. 05 jan. 2019 à 22:42
-- Version du serveur :  10.3.10-MariaDB
-- Version de PHP :  7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `la_mer_noire`
--

-- --------------------------------------------------------

--
-- Structure de la table `kebab`
--

CREATE TABLE `kebab` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `salade` tinyint(1) NOT NULL,
  `tomate` tinyint(1) NOT NULL,
  `oignon` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Déchargement des données de la table `kebab`
--

INSERT INTO `kebab` (`id`, `name`, `salade`, `tomate`, `oignon`) VALUES
(3, 'La tortilla', 0, 0, 1),
(59, 'Le test', 0, 0, 1),
(62, 'L\\\'original', 1, 1, 1),
(63, 'Le bun', 0, 1, 1),
(65, 'Chicken tandoori', 1, 0, 1),
(66, 'Le faux (sans oignons)', 1, 1, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `kebab`
--
ALTER TABLE `kebab`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `kebab`
--
ALTER TABLE `kebab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
