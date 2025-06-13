-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : ven. 13 juin 2025 à 10:23
-- Version du serveur : 8.0.42
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hobby-horse`
--

-- --------------------------------------------------------

--
-- Structure de la table `classements`
--

CREATE TABLE `classements` (
  `id` int NOT NULL,
  `tournoi_id` int NOT NULL,
  `joueur_id` int NOT NULL,
  `position` int DEFAULT NULL,
  `points` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `classements`
--

INSERT INTO `classements` (`id`, `tournoi_id`, `joueur_id`, `position`, `points`) VALUES
(1, 3, 8, 1, 8),
(2, 3, 14, 2, 7),
(3, 3, 12, 3, 6),
(4, 3, 53, 4, 5),
(5, 3, 13, 5, 4),
(6, 3, 10, 6, 3),
(7, 3, 55, 7, 2),
(8, 3, 20, 8, 1),
(770, 7, 16, 3, 18),
(771, 7, 17, 10, 12),
(772, 7, 12, 4, 18),
(773, 7, 5, 5, 18),
(774, 7, 9, 6, 18),
(775, 7, 18, 7, 18),
(776, 7, 55, 8, 15),
(777, 7, 15, 11, 12),
(778, 7, 50, 9, 15),
(779, 7, 54, 12, 12),
(780, 7, 47, 1, 21),
(781, 7, 13, 2, 21);

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `tournoi_id` int NOT NULL,
  `joueur1_id` int NOT NULL,
  `joueur2_id` int NOT NULL,
  `score_joueur1` int DEFAULT NULL,
  `score_joueur2` int DEFAULT NULL,
  `statut` enum('En cours','Terminé','A venir') NOT NULL,
  `vainqueur_id` int DEFAULT NULL,
  `phase` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matchs`
--

INSERT INTO `matchs` (`id`, `date`, `tournoi_id`, `joueur1_id`, `joueur2_id`, `score_joueur1`, `score_joueur2`, `statut`, `vainqueur_id`, `phase`) VALUES
(1, '2025-06-12', 3, 53, 13, 3, 2, 'Terminé', 53, 'quart'),
(2, '2025-06-12', 3, 10, 8, 2, 4, 'Terminé', 8, 'quart'),
(3, '2025-06-12', 3, 14, 20, 5, 0, 'Terminé', 14, 'quart'),
(4, '2025-06-12', 3, 55, 12, 2, 6, 'Terminé', 12, 'quart'),
(5, '2025-06-12', 3, 53, 8, 2, 4, 'Terminé', 8, 'demi'),
(6, '2025-06-12', 3, 14, 12, 7, 6, 'Terminé', 14, 'demi'),
(7, '2025-06-12', 3, 8, 14, 5, 4, 'Terminé', 8, 'finale'),
(8, '2025-06-13', 7, 16, 17, 4, 2, 'Terminé', 16, 'championnat'),
(9, '2025-06-13', 7, 16, 12, 2, 4, 'Terminé', 12, 'championnat'),
(10, '2025-06-13', 7, 16, 5, 3, 6, 'Terminé', 5, 'championnat'),
(11, '2025-06-13', 7, 16, 9, 2, 5, 'Terminé', 9, 'championnat'),
(12, '2025-06-13', 7, 16, 18, 4, 7, 'Terminé', 18, 'championnat'),
(13, '2025-06-13', 7, 16, 55, 8, 2, 'Terminé', 16, 'championnat'),
(14, '2025-06-13', 7, 16, 15, 6, 4, 'Terminé', 16, 'championnat'),
(15, '2025-06-13', 7, 16, 50, 5, 3, 'Terminé', 16, 'championnat'),
(16, '2025-06-13', 7, 16, 54, 8, 4, 'Terminé', 16, 'championnat'),
(17, '2025-06-13', 7, 16, 47, 9, 8, 'Terminé', 16, 'championnat'),
(18, '2025-06-13', 7, 16, 13, 2, 4, 'Terminé', 13, 'championnat'),
(19, '2025-06-13', 7, 17, 12, 3, 6, 'Terminé', 12, 'championnat'),
(20, '2025-06-13', 7, 17, 5, 2, 4, 'Terminé', 5, 'championnat'),
(21, '2025-06-13', 7, 17, 9, 4, 6, 'Terminé', 9, 'championnat'),
(22, '2025-06-13', 7, 17, 18, 8, 7, 'Terminé', 17, 'championnat'),
(23, '2025-06-13', 7, 17, 55, 2, 3, 'Terminé', 55, 'championnat'),
(24, '2025-06-13', 7, 17, 15, 6, 4, 'Terminé', 17, 'championnat'),
(25, '2025-06-13', 7, 17, 50, 2, 3, 'Terminé', 50, 'championnat'),
(26, '2025-06-13', 7, 17, 54, 6, 4, 'Terminé', 17, 'championnat'),
(27, '2025-06-13', 7, 17, 47, 2, 3, 'Terminé', 47, 'championnat'),
(28, '2025-06-13', 7, 17, 13, 8, 4, 'Terminé', 17, 'championnat'),
(29, '2025-06-13', 7, 12, 5, 4, 6, 'Terminé', 5, 'championnat'),
(30, '2025-06-13', 7, 12, 9, 6, 7, 'Terminé', 9, 'championnat'),
(31, '2025-06-13', 7, 12, 18, 8, 4, 'Terminé', 12, 'championnat'),
(32, '2025-06-13', 7, 12, 55, 6, 4, 'Terminé', 12, 'championnat'),
(33, '2025-06-13', 7, 12, 15, 8, 7, 'Terminé', 12, 'championnat'),
(34, '2025-06-13', 7, 12, 50, 6, 4, 'Terminé', 12, 'championnat'),
(35, '2025-06-13', 7, 12, 54, 2, 3, 'Terminé', 54, 'championnat'),
(36, '2025-06-13', 7, 12, 47, 1, 2, 'Terminé', 47, 'championnat'),
(37, '2025-06-13', 7, 12, 13, 3, 5, 'Terminé', 13, 'championnat'),
(38, '2025-06-13', 7, 5, 9, 2, 6, 'Terminé', 9, 'championnat'),
(39, '2025-06-13', 7, 5, 18, 7, 4, 'Terminé', 5, 'championnat'),
(40, '2025-06-13', 7, 5, 55, 5, 7, 'Terminé', 55, 'championnat'),
(41, '2025-06-13', 7, 5, 15, 3, 2, 'Terminé', 5, 'championnat'),
(42, '2025-06-13', 7, 5, 50, 4, 5, 'Terminé', 50, 'championnat'),
(43, '2025-06-13', 7, 5, 54, 8, 7, 'Terminé', 5, 'championnat'),
(44, '2025-06-13', 7, 5, 47, 6, 7, 'Terminé', 47, 'championnat'),
(45, '2025-06-13', 7, 5, 13, 6, 10, 'Terminé', 13, 'championnat'),
(46, '2025-06-13', 7, 9, 18, 3, 6, 'Terminé', 18, 'championnat'),
(47, '2025-06-13', 7, 9, 55, 4, 6, 'Terminé', 55, 'championnat'),
(48, '2025-06-13', 7, 9, 15, 7, 8, 'Terminé', 15, 'championnat'),
(49, '2025-06-13', 7, 9, 50, 6, 4, 'Terminé', 9, 'championnat'),
(50, '2025-06-13', 7, 9, 54, 2, 5, 'Terminé', 54, 'championnat'),
(51, '2025-06-13', 7, 9, 47, 8, 5, 'Terminé', 9, 'championnat'),
(52, '2025-06-13', 7, 9, 13, 5, 6, 'Terminé', 13, 'championnat'),
(53, '2025-06-13', 7, 18, 55, 3, 4, 'Terminé', 55, 'championnat'),
(54, '2025-06-13', 7, 18, 15, 9, 8, 'Terminé', 18, 'championnat'),
(55, '2025-06-13', 7, 18, 50, 4, 6, 'Terminé', 50, 'championnat'),
(56, '2025-06-13', 7, 18, 54, 6, 4, 'Terminé', 18, 'championnat'),
(57, '2025-06-13', 7, 18, 47, 2, 1, 'Terminé', 18, 'championnat'),
(58, '2025-06-13', 7, 18, 13, 9, 5, 'Terminé', 18, 'championnat'),
(59, '2025-06-13', 7, 55, 15, 4, 6, 'Terminé', 15, 'championnat'),
(60, '2025-06-13', 7, 55, 50, 6, 7, 'Terminé', 50, 'championnat'),
(61, '2025-06-13', 7, 55, 54, 6, 4, 'Terminé', 55, 'championnat'),
(62, '2025-06-13', 7, 55, 47, 2, 3, 'Terminé', 47, 'championnat'),
(63, '2025-06-13', 7, 55, 13, 5, 9, 'Terminé', 13, 'championnat'),
(64, '2025-06-13', 7, 15, 50, 0, 5, 'Terminé', 50, 'championnat'),
(65, '2025-06-13', 7, 15, 54, 6, 4, 'Terminé', 15, 'championnat'),
(66, '2025-06-13', 7, 15, 47, 3, 8, 'Terminé', 47, 'championnat'),
(67, '2025-06-13', 7, 15, 13, 8, 4, 'Terminé', 15, 'championnat'),
(68, '2025-06-13', 7, 50, 54, 6, 8, 'Terminé', 54, 'championnat'),
(69, '2025-06-13', 7, 50, 47, 2, 6, 'Terminé', 47, 'championnat'),
(70, '2025-06-13', 7, 50, 13, 3, 5, 'Terminé', 13, 'championnat'),
(71, '2025-06-13', 7, 54, 47, 2, 7, 'Terminé', 47, 'championnat'),
(72, '2025-06-13', 7, 54, 13, 8, 1, 'Terminé', 54, 'championnat'),
(73, '2025-06-13', 7, 47, 13, 2, 4, 'Terminé', 13, 'championnat');

-- --------------------------------------------------------

--
-- Structure de la table `tournois`
--

CREATE TABLE `tournois` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `statut` enum('En cours','Terminé','A venir') NOT NULL,
  `date` date NOT NULL,
  `type` enum('elimination','championnat') NOT NULL DEFAULT 'elimination',
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tournois`
--

INSERT INTO `tournois` (`id`, `nom`, `statut`, `date`, `type`, `description`) VALUES
(1, 'La course des fesses en bois', 'En cours', '2025-06-15', 'elimination', 'Tiens-toi bien à ton balai magique ! Cette course épique met à l’épreuve ton endurance, ton équilibre et ton sens du ridicule assumé. Tu vas transpirer du popotin.'),
(2, 'Coupe de France du Canasson Imaginaire', 'En cours', '2025-06-14', 'elimination', 'La plus grande compétition nationale dédiée à l’art noble du hobby horse ! Viens représenter ta région avec panache, grâce à ton fidèle destrier en tissu et ta passion démesurée pour les galopades fictives.'),
(3, 'Ligue Pro du Cheval Pas Vrai', 'En cours', '2025-06-12', 'elimination', 'Bienvenue dans l&#039;élite mondiale du galop imaginaire ! Affronte les meilleurs jockeys en mousse sur des parcours d’agilité dignes des Jeux Olympiques. Ici, seuls les vrais faux chevaux triomphent.'),
(4, 'Trotte-moi si tu peux', 'En cours', '2025-06-22', 'elimination', 'Attrape-moi si tu peux ! Relais, slaloms, épreuves de rapidité… Prépare-toi à cavaler comme jamais pour échapper à la concurrence. Une course où tout le monde finit avec le sourire (et un peu essoufflé).'),
(5, 'Kif Kif Bourrique', 'En cours', '2025-06-14', 'elimination', 'Une compétition déjantée où tous les niveaux sont les bienvenus. Défis absurdes, épreuves loufoques et ambiance décalée : viens kiffer la bourrique sans te prendre au sérieux !'),
(6, 'tagada', 'En cours', '2025-06-18', 'elimination', 'Un tournoi aussi doux que sucré ! Déguisements roses, parcours sucreries et bonne humeur garantie. Ici, on saute avec le cœur, pas avec Un tournoi aussi doux que sucré ! Déguisements roses, parcours sucreries et bonne humeur garantie. Ici, on saute avec le cœur, pas avec les sabots.les sabots.\r\n\r\n'),
(7, '&quot;Licorne Royale : Le Grand Galop&quot;', 'En cours', '2025-06-29', 'championnat', 'Prépare ta monture en peluche, coiffe ta crinière et entre dans l’arène du tournoi le plus étincelant de l’année !\r\n\r\nLa Licorne Royale : Le Grand Galop réunit les cavalières et cavaliers du monde entier pour une compétition de hobby horse épique, pleine de rires, de sauts spectaculaires et de paillettes.\r\n\r\nStyle, grâce, vitesse et panache seront tes meilleurs alliés pour franchir les obstacles… et conquérir le trône du Galop Royal !'),
(10, 'La Coupe Galop d’Or', 'En cours', '2025-06-16', 'championnat', 'Bienvenue à la Coupe Galop d’Or, le championnat le plus prestigieux du royaume des licornes et des cavaliers fantastiques.\r\nChaque participant enfourche fièrement sa monture enchantée, prêt à galoper vers la gloire à travers des joutes épiques et féériques.\r\nIci, l’agilité rencontre l’élégance, la stratégie s’allie à la magie.\r\nUn tournoi où l’honneur scintille autant que les sabots dorés.\r\nQui remportera le ruban arc-en-ciel et gravera son nom dans les légendes du Hobbyverse ?');

-- --------------------------------------------------------

--
-- Structure de la table `tournoi_participants`
--

CREATE TABLE `tournoi_participants` (
  `id` int NOT NULL,
  `tournoi_id` int NOT NULL,
  `joueur_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tournoi_participants`
--

INSERT INTO `tournoi_participants` (`id`, `tournoi_id`, `joueur_id`) VALUES
(1, 1, 2),
(2, 1, 5),
(3, 1, 8),
(4, 1, 10),
(5, 1, 12),
(6, 1, 50),
(7, 1, 49),
(8, 1, 41),
(9, 2, 12),
(10, 2, 6),
(11, 2, 10),
(12, 2, 5),
(13, 2, 20),
(14, 2, 17),
(15, 2, 9),
(16, 2, 56),
(17, 3, 14),
(18, 3, 8),
(19, 3, 13),
(20, 3, 20),
(21, 3, 55),
(22, 3, 12),
(23, 3, 53),
(24, 3, 10),
(25, 7, 16),
(26, 7, 17),
(27, 7, 12),
(28, 7, 5),
(29, 7, 9),
(30, 7, 18),
(31, 7, 55),
(32, 7, 15),
(33, 7, 50),
(34, 7, 54),
(35, 7, 47),
(36, 7, 13),
(37, 10, 18),
(38, 10, 16),
(39, 10, 10),
(40, 10, 7),
(41, 10, 55),
(42, 10, 15),
(43, 10, 8),
(44, 10, 52);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `nom_utilisateur` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','joueur') NOT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom_utilisateur`, `mot_de_passe`, `role`, `token`) VALUES
(1, 'admin', '$2y$10$vj7dJxbX8Jr5MBGh9KXOAe86gYUk0ezY5RE9zYSJHNRP9upnrxcdu', 'admin', 'fc3aa6bf41faa03350ec55c36f26998ab9bae3e226faf5fbe3c451af8dab9ad5'),
(2, 'Chloé', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', 'ffecc3c3f7245e1c3ec5037e3137c5e6bf02241471dcaa8a9878b4924295fcc1'),
(3, 'Glawdys', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', NULL),
(4, 'Inès', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', NULL),
(5, 'Louna', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', NULL),
(6, 'Jade', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', NULL),
(7, 'Aiko', '$2b$12$nJp6/8mZoeu3ePSteqToFel9dmpVL2lH/rI04qluEIaNjYARrOb/C', 'joueur', NULL),
(8, 'Sofia', '$2b$12$jX/nalFL.Zhe.OUvf4TQRe1KCpAMTxKKreMAEp0679biLxPvL.D1y', 'joueur', NULL),
(9, 'Yara', '$2b$12$sHznqZLo4jw50t5yy07d/On1eIsNCJm.mrDi/rWzlj36rQdE3d0.S', 'joueur', NULL),
(10, 'Freja', '$2b$12$FJt.2pkpZyoUO0YqMFehoOhDLbWMPWLtYHUGoLEyUVpNaW17kPCNS', 'joueur', NULL),
(11, 'Amira', '$2b$12$Jaul49JmK/PDDETWdnr99.LmBo9laCP6olrkbS5SpgKHS8L0uokxO', 'joueur', NULL),
(12, 'Hana', '$2b$12$cSa556q6xIv8vbHdx/Qmu.LalCkwrqnY121HGLBpmKJfZzQxii91W', 'joueur', NULL),
(13, 'Camila', '$2b$12$tL48ECX9iMFqvxixfLrny.I4HfXmeV8GULYrwa3WWNRDU/AE9/oyq', 'joueur', NULL),
(14, 'Giulia', '$2b$12$Bkst5TC7nZHLyvhw1V5yCu5MTbfxegEGlq72gcbTh1XEMqM1CgHPa', 'joueur', NULL),
(15, 'Amina', '$2b$12$X13bJaEDpx/ZUBqpMenjv.ZHgxaHOVDQjVFl1NGqVxPIUXwmPzsAy', 'joueur', NULL),
(16, 'Yuna', '$2b$12$mkLdOs8S3FSipG19tWSXwe07C1WKFM998tSJCQUvbLyow4bNoYvg.', 'joueur', NULL),
(17, 'Nova', '$2b$12$8ULZUv8XOKbwRW4Q4XCYOegydImBaTq5vx4hwehJc5hKnnGXgGRqO', 'joueur', NULL),
(18, 'Zoey', '$2b$12$1rIQvvs2HohsL5fpRtJpD.VgSqkdQerIOMSAVk/TG5yxvHTn97/kO', 'joueur', NULL),
(19, 'Océane', '$2b$12$FS1P4bq1M3yIdqS9bxQBUumqXKG/u7Ebm5cOmR6rfVRYQBC3xg86.', 'joueur', NULL),
(20, 'Nour', '$2b$12$lBNCacs22/btXf2pg/9xEO0kuk59ZzVcx3rjAePrXpe9HTdSuH3GW', 'joueur', NULL),
(21, 'Maya', '$2b$12$Emp/JD1FOIW0hVDNA52vmeq46WmfBGSm1lUqkfDf.IQ1PXk8xuuF6', 'joueur', NULL),
(22, 'Elsa', '$2b$12$zG6M0skXg5qjLMNnUe69JuyzthbBMH3tAiCOob/a5Ip69dW0btlsm', 'joueur', NULL),
(23, 'Ivy', '$2b$12$hoO6iAMV6MYwS3p1R6XWDO4WYtx00BZvdttwxkriQtFutx4O4STBS', 'joueur', NULL),
(24, 'Mila', '$2b$12$sBP.Ft0qC4qRToTqhzwZCOzJD95EYMLc76QRcOl4VYE3Va6IdCkYC', 'joueur', NULL),
(25, 'Jisoo', '$2b$12$pjV5ffqZd1gY7vxF/tUy9u5jeP3dxvjllGmPVhkHt.d/LRtcdj4em', 'joueur', NULL),
(26, 'Leonie', '$2b$12$UqzRriZ.bvz6UYwo3zYRpOousj9pT5bgeQ56t7UeDUwJY7TCogPxe', 'joueur', NULL),
(27, 'Larissa', '$2b$12$gxKjbxJzuFL30PVoZQ3Tm.JeMo9HmKa6fqwDSoAh5Uj0M.fFkJime', 'joueur', NULL),
(28, 'Riley', '$2b$12$qqIBWF5.mxMTmL6uVwTXyuWRdvBO8aM2XlHfCZ5r1IE2yakp1GQYi', 'joueur', NULL),
(29, 'Alba', '$2b$12$D3wuoX3kAVyPsD.8dulGc.nVT1teZGSn.0dN6Hv50ApdRdKt5TZfa', 'joueur', NULL),
(30, 'Nari', '$2b$12$DA3/2bfIVKcNMIPdVeM0.OYPM9hDQcb4igzY52F1EJXaruJ.wdega', 'joueur', NULL),
(31, 'Siiri', '$2b$12$aNg3ND3e0OxC2dl/auWX.OVaMbUwMFL9J/kgIjybjopZRiNxhhu/e', 'joueur', NULL),
(32, 'Addison', '$2b$12$tSbAARJg.dN6wDNwQq.2iux4kt6lzv1GJj2U/nkeFZlD8iNFMOVfS', 'joueur', NULL),
(33, 'Noa', '$2b$12$6CR0vaRKL/8taB1uP3Z7s.FhiADSgCA4SjRwKZJSa6WJLmII/2izi', 'joueur', NULL),
(34, 'Talia', '$2b$12$PtjB3Iy8Cqri6skxq8/o3OND3JaXZmn2yXNskAQUcqejAksHnmqfS', 'joueur', NULL),
(35, 'Harper', '$2b$12$ro0VUD675XQaI5u6VtdlDOUcTAAl1WaqGPBZvGEnz/7HrL1ML2NLq', 'joueur', NULL),
(36, 'Maja', '$2b$12$iP7T6ZcTvemqSkiMu/uo.erFdZ7c5HTf670dABt4Vy8i1Q1eJ8MX2', 'joueur', NULL),
(37, 'Isadora', '$2b$12$lt1gVq.D5wopJNZ9AXKJ0.22XDymyMvmu/VPqk76ykf2HemfTg4TC', 'joueur', NULL),
(38, 'Naomi', '$2b$12$9SqIN5.aek9AUmL7Baz4XeVsAw9C11DsRSUMLpxJzkZiOIIYsKGtS', 'joueur', NULL),
(39, 'Lina', '$2b$12$kpIaBSXlHaEA3CsoBtmnRe5zoHPPs5OAf2sttLgOl/DpXCuG3BIfu', 'joueur', NULL),
(40, 'Brooklyn', '$2b$12$w8OeD2YK46PcXQ8cnkGreezoeWzYsveIwz0TuuA3bH..aLMei9yBa', 'joueur', NULL),
(41, 'Poppy', '$2b$12$Qfsh5rUBvBEisC/IvwJK.OO6crlM4NWPjW/k0VX2r7sLzfo.0AU0m', 'joueur', NULL),
(42, 'Tilde', '$2b$12$cjp8Lw0gGuiE1W8lYv3/SuwFY6itRZByf8wV3J126xmXxYr1lzK7y', 'joueur', NULL),
(43, 'Ella', '$2b$12$c74YN0lNxphFmUcKAzvW7uRtM8ITWtBAnE74ngaW2FtBqJX7nthQm', 'joueur', NULL),
(44, 'Madison', '$2b$12$B0ZJKCKljw.BTRfyv1.oWO/rOk2lvh6j.U6dWREPYhsQi2Jihjjn6', 'joueur', NULL),
(45, 'Ariana', '$2b$12$lpAXBMZ8K4uMsunvX.FBYu6CiwjEKx07WHZ6G97QLDaNLVBhWY3gC', 'joueur', NULL),
(46, 'Bianca', '$2b$12$ib75.Jxz8S0Hn6s/FAMMuOdK5c8O/eParW7UiXL4SrhJSOmJw2Rvi', 'joueur', NULL),
(47, 'Scarlett', '$2b$12$oTqa62IRZQTehdzL.rH2aO6aAxkiVuISGIM8/Dcbba7kNyUhZfEXu', 'joueur', NULL),
(48, 'Carmen', '$2b$12$oowjXT32tlCBn81jZv9/POaRnhWi6Nrm9SfDBa11c4ot7fvq3nz46', 'joueur', NULL),
(49, 'Veera', '$2b$12$vAL81NlVCVk0h7NsJDCfm.K1eKZXla7CWuqkHxkmOM9XLT8JlG3ea', 'joueur', NULL),
(50, 'Clara', '$2b$12$g.WpIsxByfLjXFX7S8aNOue0qVbXS4PKilEDvJAd4ElQKUMUz2QHi', 'joueur', NULL),
(51, 'Emiko', '$2b$12$3fc/qVRW.LQyfaj4cXY8qeSmH7TZaXq7YDt9/KVERHFo8etIH4eUS', 'joueur', NULL),
(52, 'Rosie', '$2b$12$sF1byGLgDLhT34mirG9rVeu6B89DC13YXfV611VvSNUU2kNHrq5He', 'joueur', NULL),
(53, 'Ellie', '$2b$12$atNZ886OF/ySkub0s4/H2ey9Oxkh.A0zAoX7ZsDKGaNKVcaV621le', 'joueur', NULL),
(54, 'Gertrude', '$2y$10$MvOB9.8U/IfdkyC959ymGuVwnWSQOReCoxVX/2xf95JaYofBnHlH2', 'joueur', NULL),
(55, 'Madeleine', '$2y$10$q/mgxBFjdt9HCnLoN4W9u.Cc8tUI89dlUNtWvOI6xUQ.IB3KOs522', 'joueur', NULL),
(56, 'Mado', '$2y$10$AXRMqHIIFuUHkf.t8tQqDOz83h4RsVU5.rzUKFIJT/15PZD39DZj.', 'joueur', NULL),
(57, 'Abby', '$2y$10$ADZJsvTT59cNUfoU4rUOA.5noT9Lmn09GZEt5jxghLxMSjcKQcatK', 'joueur', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classements`
--
ALTER TABLE `classements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournoi_id` (`tournoi_id`),
  ADD KEY `joueur_id` (`joueur_id`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournoi_id` (`tournoi_id`),
  ADD KEY `joueur1_id` (`joueur1_id`),
  ADD KEY `joueur2_id` (`joueur2_id`),
  ADD KEY `vainqueur_id` (`vainqueur_id`);

--
-- Index pour la table `tournois`
--
ALTER TABLE `tournois`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tournoi_participants`
--
ALTER TABLE `tournoi_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournoi_id` (`tournoi_id`),
  ADD KEY `joueur_id` (`joueur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `classements`
--
ALTER TABLE `classements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=782;

--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT pour la table `tournois`
--
ALTER TABLE `tournois`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `tournoi_participants`
--
ALTER TABLE `tournoi_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `classements`
--
ALTER TABLE `classements`
  ADD CONSTRAINT `classements_ibfk_1` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classements_ibfk_2` FOREIGN KEY (`joueur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matchs_ibfk_2` FOREIGN KEY (`joueur1_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `matchs_ibfk_3` FOREIGN KEY (`joueur2_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `matchs_ibfk_4` FOREIGN KEY (`vainqueur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `tournoi_participants`
--
ALTER TABLE `tournoi_participants`
  ADD CONSTRAINT `tournoi_participants_ibfk_1` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournoi_participants_ibfk_2` FOREIGN KEY (`joueur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
