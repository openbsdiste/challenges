-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 14 Mai 2013 à 18:45
-- Version du serveur: 5.1.53
-- Version de PHP: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `challenge`
--

-- --------------------------------------------------------

--
-- Structure de la table `arbre`
--

DROP TABLE IF EXISTS `arbre`;
CREATE TABLE IF NOT EXISTS `arbre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noeud` int(11) NOT NULL,
  `challenge` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `right` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `noeud_index` (`challenge`,`noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `arbre`
--


-- --------------------------------------------------------

--
-- Structure de la table `challenges`
--

DROP TABLE IF EXISTS `challenges`;
CREATE TABLE IF NOT EXISTS `challenges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee` char(4) NOT NULL,
  `organisateur` int(11) NOT NULL,
  `statut` int(11) NOT NULL DEFAULT '0',
  `datelimite` char(10) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `challenges`
--


-- --------------------------------------------------------

--
-- Structure de la table `participants`
--

DROP TABLE IF EXISTS `participants`;
CREATE TABLE IF NOT EXISTS `participants` (
  `challenge` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT '0',
  `calculee` varchar(30) NOT NULL DEFAULT '-1',
  `attribuee` varchar(30) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`challenge`,`club`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `participants`
--


-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL,
  `challenge` int(11) NOT NULL,
  `information` tinyint(1) NOT NULL DEFAULT '0',
  `crypte` tinyint(1) NOT NULL DEFAULT '1',
  `valeur` char(10) DEFAULT NULL,
  `auteur` varchar(50) NOT NULL DEFAULT '',
  `texte` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `clubreponse` int(11) NOT NULL DEFAULT '0',
  `statut` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`challenge`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `questions`
--


-- --------------------------------------------------------

--
-- Structure de la table `reponses`
--

DROP TABLE IF EXISTS `reponses`;
CREATE TABLE IF NOT EXISTS `reponses` (
  `id` int(11) NOT NULL,
  `challenge` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  `crypte` tinyint(1) NOT NULL DEFAULT '1',
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
  `texte` text NOT NULL,
  `notesinternes` text NOT NULL,
  `note` varchar(30) NOT NULL,
  PRIMARY KEY (`id`,`challenge`,`club`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `reponses`
--


-- --------------------------------------------------------

--
-- Structure de la table `statuts`
--

DROP TABLE IF EXISTS `statuts`;
CREATE TABLE IF NOT EXISTS `statuts` (
  `statut` varchar(200) NOT NULL DEFAULT '',
  `bloquant` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`statut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `statuts`
--


-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `memoire` varchar(2000) NOT NULL DEFAULT '',
  `role` varchar(20) NOT NULL DEFAULT 'utilisateur',
  `email` varchar(255) NOT NULL,
  `club` varchar(200) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `password`, `memoire`, `role`, `email`, `club`, `actif`) VALUES
(1, 'ADMIN', '73acd9a5972130b75066c82595a1fae3', '', 'administrateur', 'courriel@example.com', '', 1);
