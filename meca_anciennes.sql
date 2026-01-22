-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 22 jan. 2026 à 00:34
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `meca_anciennes`
--

-- --------------------------------------------------------

--
-- Structure de la table `accueil_bloc`
--

CREATE TABLE `accueil_bloc` (
  `id` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `texte` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `ordre_affichage` int(11) DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `accueil_bloc`
--

INSERT INTO `accueil_bloc` (`id`, `titre`, `texte`, `image_url`, `ordre_affichage`, `actif`) VALUES
(1, 'QUI SOMMES-NOUS', 'Association de collectionneurs, nous nous rejoignons lors de manifestations pour partager des moments autour de notre passion.', 'images/qui.jpg', 1, 1),
(2, 'PASSION', 'Notre passion pour les mécaniques anciennes nous unit et nous rassemble régulièrement.', 'images/passion.jpg', 2, 1),
(3, 'PARTAGE', 'Nous aimons partager nos connaissances et nos découvertes de manière conviviale lors de nos manifestations.', 'images/partage.jpg', 3, 1),
(4, 'CONVIVIAL', 'Les manifestations nous permettent de partager nos connaissances et nos découvertes de manière conviviale.', 'images/convivial.jpg', 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `adhesion_demande`
--

CREATE TABLE `adhesion_demande` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `donnees_json` longtext NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'recu',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `annonce_vehicule`
--

CREATE TABLE `annonce_vehicule` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `modele` varchar(100) DEFAULT NULL,
  `annee` int(11) DEFAULT NULL,
  `moteur` varchar(100) DEFAULT NULL,
  `kilometrage` int(11) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonce_vehicule`
--

INSERT INTO `annonce_vehicule` (`id`, `utilisateur_id`, `titre`, `marque`, `modele`, `annee`, `moteur`, `kilometrage`, `prix`, `description`, `localisation`, `telephone_contact`, `date_creation`) VALUES
(1, 1, 'Magnifique Citroën 2CV 1978', 'Citroën', '2CV', 1978, '602 cc', 85000, 12500.00, 'Belle 2CV en excellent état, entièrement restaurée. Carrosserie saine, mécanique révisée. Carte grise de collection. Véhicule de passion à ne pas manquer !', 'Lyon (69)', '06 12 34 56 78', '2026-01-22 00:28:19'),
(2, 1, 'Renault 4L Vintage 1985', 'Renault', '4L', 1985, '1108 cc', 120000, 6800.00, 'Superbe 4L en état d\'origine, très bien conservée. Deuxième main, carnet d\'entretien complet. Idéale pour balades et collections. Quelques traces d\'usage normales pour l\'âge.', 'Bordeaux (33)', '06 98 76 54 32', '2026-01-22 00:28:19'),
(3, 1, 'Peugeot 205 GTI 1989 - Collector', 'Peugeot', '205 GTI', 1989, '1.9L 130ch', 95000, 18900.00, 'Mythique 205 GTI Phase 2, état collection. Peinture d\'origine, intérieur cuir refait à neuf. Jamais accidentée, toujours entretenue avec soin. Papiers et contrôle technique OK.', 'Paris (75)', '07 45 23 67 89', '2026-01-22 00:28:19');

-- --------------------------------------------------------

--
-- Déchargement des données de la table `image_vehicule`
--

INSERT INTO `image_vehicule` (`id`, `annonce_id`, `url`, `ordre`) VALUES
(1, 1, 'images/1.JPG', 1),
(2, 1, 'images/2.JPG', 2),
(3, 2, 'images/3.JPG', 1),
(4, 2, 'images/4.JPG', 2),
(5, 3, 'images/5.JPG', 1),
(6, 3, 'images/6.JPG', 2);

-- --------------------------------------------------------

--
-- Structure de la table `association`
--

CREATE TABLE `association` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `url_facebook` varchar(255) DEFAULT NULL,
  `url_instagram` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `association`
--

INSERT INTO `association` (`id`, `nom`, `description`, `adresse`, `email`, `telephone`, `url_facebook`, `url_instagram`) VALUES
(1, 'Les Mécaniques Anciennes', 'Association de passionnés de véhicules de collection', '12 Rue de la Mécanique, 69000 Lyon', 'contact@meca-anciennes.fr', '04 78 12 34 56', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(11) NOT NULL,
  `association_id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `type_vehicules` varchar(255) DEFAULT NULL,
  `image_principale` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id`, `association_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `type_vehicules`, `image_principale`, `date_creation`, `latitude`, `longitude`) VALUES
(1, 1, 'Rassemblement Printanier 2026', 'Grand rassemblement de véhicules de collection au Parc de la Tête d\'Or. Exposition, animations et démonstrations.', '2026-04-15 09:00:00', '2026-04-15 18:00:00', 'Parc de la Tête d\'Or, Lyon', 'Tous véhicules avant 1990', NULL, '2026-01-22 00:30:18', 45.774265, 4.857415),
(2, 1, 'Sortie Côte d\'Azur', 'Balade estivale sur les routes de la Côte d\'Azur avec halte déjeuner à Saint-Tropez. Inscription obligatoire.', '2026-06-20 08:00:00', '2026-06-20 19:00:00', 'Nice - Saint-Tropez', '2CV, 4L, Méhari', NULL, '2026-01-22 00:30:18', 43.696950, 7.271413),
(3, 1, 'Exposition Automobiles Anciennes', 'Exposition de véhicules de collection sur la Place Bellecour. Entrée libre pour le public.', '2026-09-12 10:00:00', '2026-09-13 18:00:00', 'Place Bellecour, Lyon', 'Véhicules d\'exception', NULL, '2026-01-22 00:30:18', 45.757814, 4.832011);

-- --------------------------------------------------------

--
-- Structure de la table `evenement_image`
--

CREATE TABLE `evenement_image` (
  `id` int(11) NOT NULL,
  `evenement_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `legende` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `image_vehicule`
--

CREATE TABLE `image_vehicule` (
  `id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ordre` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `id` int(11) NOT NULL,
  `evenement_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `info_vehicule` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_contact`
--

CREATE TABLE `message_contact` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `objet` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `est_lu` tinyint(1) NOT NULL DEFAULT 0,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mot_de_passe_hash` varchar(255) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `telephone`, `mot_de_passe_hash`, `date_inscription`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@example.com', '06 12 34 56 78', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJK', '2026-01-22 00:28:19');

-- --------------------------------------------------------

--
-- Structure de la table `video_archive`
--

CREATE TABLE `video_archive` (
  `id` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ordre_affichage` int(11) DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `accueil_bloc`
--
ALTER TABLE `accueil_bloc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accueil_bloc_actif` (`actif`),
  ADD KEY `idx_accueil_bloc_ordre` (`ordre_affichage`);

--
-- Index pour la table `adhesion_demande`
--
ALTER TABLE `adhesion_demande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_adhesion_email` (`email`),
  ADD KEY `idx_adhesion_statut` (`statut`);

--
-- Index pour la table `annonce_vehicule`
--
ALTER TABLE `annonce_vehicule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annonce_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_annonce_date` (`date_creation`);

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evenement_association` (`association_id`),
  ADD KEY `idx_evenement_dates` (`date_debut`,`date_fin`);

--
-- Index pour la table `evenement_image`
--
ALTER TABLE `evenement_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evenement_image_evenement` (`evenement_id`),
  ADD KEY `idx_evenement_image_ordre` (`evenement_id`,`ordre`);

--
-- Index pour la table `image_vehicule`
--
ALTER TABLE `image_vehicule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_image_annonce` (`annonce_id`),
  ADD KEY `idx_image_ordre` (`annonce_id`,`ordre`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inscription_evenement` (`evenement_id`),
  ADD KEY `idx_inscription_email` (`email`);

--
-- Index pour la table `message_contact`
--
ALTER TABLE `message_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_contact_est_lu` (`est_lu`),
  ADD KEY `idx_message_contact_date` (`date_envoi`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_utilisateur_email` (`email`);

--
-- Index pour la table `video_archive`
--
ALTER TABLE `video_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_video_archive_actif` (`actif`),
  ADD KEY `idx_video_archive_ordre` (`ordre_affichage`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `accueil_bloc`
--
ALTER TABLE `accueil_bloc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `adhesion_demande`
--
ALTER TABLE `adhesion_demande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `annonce_vehicule`
--
ALTER TABLE `annonce_vehicule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `evenement_image`
--
ALTER TABLE `evenement_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `image_vehicule`
--
ALTER TABLE `image_vehicule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message_contact`
--
ALTER TABLE `message_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `video_archive`
--
ALTER TABLE `video_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonce_vehicule`
--
ALTER TABLE `annonce_vehicule`
  ADD CONSTRAINT `annonce_vehicule_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evenement_image`
--
ALTER TABLE `evenement_image`
  ADD CONSTRAINT `evenement_image_ibfk_1` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `image_vehicule`
--
ALTER TABLE `image_vehicule`
  ADD CONSTRAINT `image_vehicule_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonce_vehicule` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
