-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 24, 2026 at 08:50 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projeto`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '19', 1761233604),
('enfermeiro', '13', 1769286939),
('enfermeiro', '14', 1769269612),
('enfermeiro', '15', 1769269607),
('enfermeiro', '18', 1769269401),
('medico', '38', 1769267645),
('medico', '39', 1769267638),
('medico', '40', 1766587286),
('medico', '45', 1769285872),
('paciente', '16', 1769269446),
('paciente', '20', 1763135051),
('paciente', '21', 1763135962),
('paciente', '22', 1763135978),
('paciente', '24', 1763136036),
('paciente', '25', 1769267769),
('paciente', '26', 1769267747),
('paciente', '27', 1769267728),
('paciente', '28', 1769267689),
('paciente', '29', 1764162766),
('paciente', '30', 1764163099),
('paciente', '31', 1764163216),
('paciente', '32', 1764163663),
('paciente', '33', 1764163738),
('paciente', '34', 1764164327),
('paciente', '35', 1764164544),
('paciente', '36', 1769267697),
('paciente', '37', 1769267706),
('paciente', '49', 1769267528);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `rule_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, NULL, NULL, NULL, 1761233604, 1761233604),
('atualizarRegisto', 2, 'Atualizar registo existente', NULL, NULL, 1761233604, 1761233604),
('createPost', 2, 'Create a post', NULL, NULL, 1761233604, 1761233604),
('criarRegisto', 2, 'Criar novo registo', NULL, NULL, 1761233604, 1761233604),
('editarRegisto', 2, 'Editar registo existente', NULL, NULL, 1761233604, 1761233604),
('eliminarRegisto', 2, 'Eliminar registo existente', NULL, NULL, 1761233604, 1761233604),
('enfermeiro', 1, 'Acesso a triagem e pacientes', NULL, NULL, 1761233604, 1761233604),
('medico', 1, 'Acesso a consultas e relatórios', NULL, NULL, 1761233604, 1761233604),
('paciente', 1, 'Paciente do sistema', NULL, NULL, 1763135051, 1763135051),
('updatePost', 2, 'Update post', NULL, NULL, 1761233604, 1761233604),
('verRegisto', 2, 'Visualizar registos', NULL, NULL, 1761233604, 1761233604);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'updatePost');

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consulta`
--

DROP TABLE IF EXISTS `consulta`;
CREATE TABLE IF NOT EXISTS `consulta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_consulta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Aberta','Encerrada','Em curso') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aberta',
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `userprofile_id` int DEFAULT NULL,
  `triagem_id` int DEFAULT NULL,
  `medicouserprofile_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `medico_nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consulta`
--

INSERT INTO `consulta` (`id`, `data_consulta`, `estado`, `observacoes`, `userprofile_id`, `triagem_id`, `medicouserprofile_id`, `data_encerramento`, `relatorio_pdf`, `medico_nome`) VALUES
(19, '2025-12-26 11:28:51', 'Encerrada', '', 22, 36, 13, '2025-12-28 11:34:59', NULL, NULL),
(22, '2025-12-28 11:56:17', 'Encerrada', '', 11, 40, 13, '2025-12-30 10:04:20', NULL, NULL),
(24, '2025-12-30 10:09:42', 'Encerrada', '', 21, 42, 13, '2025-12-30 10:10:32', NULL, 'admin'),
(26, '2026-01-17 14:13:49', 'Encerrada', '', 39, 93, 13, '2026-01-17 14:32:56', NULL, 'admin'),
(27, '2026-01-17 14:28:30', 'Encerrada', '', 11, 95, 13, '2026-01-17 14:42:29', NULL, 'admin'),
(28, '2026-01-17 14:43:06', 'Encerrada', NULL, 22, 97, 13, '2026-01-17 14:52:55', NULL, 'admin'),
(29, '2026-01-24 20:45:29', 'Encerrada', '', 11, 99, 13, '2026-01-24 20:46:16', NULL, 'admin'),
(30, '2026-01-24 20:47:36', 'Encerrada', 'nada acrescentar\r\n', 39, 101, 32, '2026-01-24 20:48:46', NULL, 'Dinis');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

DROP TABLE IF EXISTS `login_history`;
CREATE TABLE IF NOT EXISTS `login_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `data_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `data_login`, `ip`, `user_agent`) VALUES
(1, 13, '2025-12-03 14:08:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(2, 19, '2025-12-03 14:08:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(3, 19, '2025-12-03 14:19:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(4, 19, '2025-12-04 15:56:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(5, 15, '2025-12-04 15:56:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(6, 15, '2025-12-04 17:09:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(7, 19, '2025-12-04 17:09:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(8, 15, '2025-12-04 17:10:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(9, 19, '2025-12-04 17:11:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(10, 19, '2025-12-04 17:11:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(11, 19, '2025-12-04 17:11:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(12, 19, '2025-12-04 17:13:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(13, 19, '2025-12-19 11:20:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(14, 19, '2025-12-24 10:27:44', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36'),
(15, 19, '2025-12-24 10:28:54', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36'),
(16, 19, '2025-12-24 11:07:34', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36'),
(17, 19, '2025-12-24 13:24:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(18, 19, '2025-12-24 13:26:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(19, 19, '2025-12-24 13:28:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(20, 13, '2025-12-24 13:29:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(21, 19, '2025-12-24 13:31:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(22, 13, '2025-12-24 14:07:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(23, 19, '2025-12-24 14:12:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(24, 19, '2025-12-24 14:25:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(25, 19, '2025-12-24 14:39:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(26, 45, '2025-12-24 14:52:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(27, 19, '2025-12-24 15:04:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(28, 19, '2025-12-24 15:09:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(29, 45, '2025-12-24 15:12:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(30, 19, '2025-12-24 15:12:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(31, 38, '2025-12-24 15:12:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(32, 19, '2025-12-24 15:13:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(33, 38, '2025-12-24 15:14:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(34, 19, '2025-12-24 15:15:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0'),
(35, 19, '2026-01-02 19:49:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0'),
(36, 13, '2026-01-07 15:03:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0'),
(37, 19, '2026-01-07 16:48:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0'),
(38, 19, '2026-01-18 22:46:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(39, 13, '2026-01-18 22:46:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(40, 15, '2026-01-18 22:47:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(41, 19, '2026-01-20 18:10:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(42, 13, '2026-01-24 15:16:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(43, 19, '2026-01-24 15:39:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(44, 19, '2026-01-24 20:13:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(45, 19, '2026-01-24 20:13:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(46, 19, '2026-01-24 20:17:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(47, 45, '2026-01-24 20:18:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(48, 19, '2026-01-24 20:18:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(49, 18, '2026-01-24 20:19:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(50, 19, '2026-01-24 20:20:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(51, 13, '2026-01-24 20:35:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(52, 19, '2026-01-24 20:35:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(53, 38, '2026-01-24 20:45:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(54, 19, '2026-01-24 20:46:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(55, 38, '2026-01-24 20:46:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(56, 19, '2026-01-24 20:46:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(57, 38, '2026-01-24 20:47:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0'),
(58, 19, '2026-01-24 20:49:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 OPR/126.0.0.0');

-- --------------------------------------------------------

--
-- Table structure for table `medicamento`
--

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dosagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `indicacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicamento`
--

INSERT INTO `medicamento` (`id`, `nome`, `dosagem`, `indicacao`) VALUES
(1, 'Paracetamol', '500mg', 'Alívio da dor e febre'),
(2, 'Paracetamol', '1g', 'Dor moderada e febre alta'),
(3, 'Ibuprofeno', '400mg', 'Dor inflamatória, febre'),
(4, 'Ibuprofeno', '600mg', 'Inflamação e dor intensa'),
(5, 'Aspirina', '500mg', 'Dor leve, febre, anti-inflamatório'),
(6, 'Amoxicilina', '500mg', 'Infeções bacterianas (vias respiratórias, urinárias)'),
(7, 'Amoxicilina', '875mg', 'Infeções moderadas a graves'),
(8, 'Clavulanato + Amoxicilina', '875mg/125mg', 'Sinusite, otite, infeções respiratórias e urinárias'),
(9, 'Azitromicina', '500mg', 'Infeções respiratórias e genitais'),
(10, 'Ciprofloxacina', '500mg', 'Infeções urinárias e gastrointestinais'),
(11, 'Metformina', '850mg', 'Diabetes tipo 2'),
(12, 'Metformina', '1000mg', 'Diabetes tipo 2'),
(13, 'Omeprazol', '20mg', 'Refluxo, gastrite'),
(14, 'Pantoprazol', '40mg', 'Refluxo grave, esofagite'),
(15, 'Losartan', '50mg', 'Hipertensão'),
(16, 'Losartan', '100mg', 'Hipertensão'),
(17, 'Amlodipina', '5mg', 'Hipertensão, angina'),
(18, 'Amlodipina', '10mg', 'Hipertensão resistente'),
(19, 'Enalapril', '20mg', 'Hipertensão, insuficiência cardíaca'),
(20, 'Simvastatina', '20mg', 'Colesterol elevado'),
(21, 'Simvastatina', '40mg', 'Colesterol muito elevado'),
(22, 'Atorvastatina', '20mg', 'Colesterol elevado'),
(23, 'Atorvastatina', '40mg', 'Colesterol muito elevado'),
(24, 'Furosemida', '40mg', 'Retenção de líquidos, hipertensão'),
(25, 'Prednisolona', '20mg', 'Inflamações graves, alergias, crises respiratórias'),
(26, 'Dexametasona', '4mg', 'Inflamação, alergias graves'),
(27, 'Insulina Rápida', '100 UI', 'Diabetes tipo 1 e 2'),
(28, 'Insulina Basal', '100 UI', 'Diabetes tipo 1 e 2'),
(29, 'Dipirona', '500mg', 'Dor intensa e febre'),
(30, 'Cetirizina', '10mg', 'Alergias, rinite'),
(32, 'Salbutamol', '100mcg', 'Crise de asma, broncoespasmo'),
(33, 'Budesonida + Formoterol', '160/4.5mcg', 'Asma e DPOC'),
(34, 'Tramadol', '50mg', 'Dor moderada a intensa'),
(35, 'Codeína', '30mg', 'Dor moderada e tosse persistente'),
(36, 'Clonazepam', '2.5mg/mL', 'Ansiedade, epilepsia'),
(37, 'Diazepam', '10mg', 'Ansiedade, espasmos musculares'),
(38, 'Sertralina', '50mg', 'Depressão, ansiedade'),
(39, 'Sertralina', '100mg', 'Depressão, ansiedade'),
(40, 'Fluoxetina', '30mg', 'Depressão, ansiedade, compulsão alimentar');

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1761748339);

-- --------------------------------------------------------

--
-- Table structure for table `notificacao`
--

DROP TABLE IF EXISTS `notificacao`;
CREATE TABLE IF NOT EXISTS `notificacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `mensagem` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tipo` enum('Consulta','Prioridade','Geral') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Geral',
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_userprofile_id` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notificacao`
--

INSERT INTO `notificacao` (`id`, `titulo`, `mensagem`, `tipo`, `dataenvio`, `lida`, `userprofile_id`) VALUES
(11, 'Triagem registada', 'Foi registada uma nova triagem para o paciente paciente.', 'Consulta', '2025-11-26 12:29:46', 0, 11),
(12, 'Prioridade Laranja', 'O paciente paciente encontra-se em prioridade Laranja.', 'Prioridade', '2025-11-26 12:29:46', 0, 11),
(13, 'Triagem registada', 'Foi registada uma nova triagem para o paciente teste2.', 'Consulta', '2025-11-26 12:36:30', 0, 20),
(14, 'Triagem registada', 'Foi registada uma nova triagem para o paciente paciente3.', 'Consulta', '2025-11-26 12:53:43', 0, 22),
(15, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-11-27 15:21:54', 0, 9),
(16, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-11-27 15:22:01', 0, 9),
(17, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-11-27 15:22:27', 0, 9),
(18, 'Consulta iniciada', 'A sua consulta foi iniciada.', 'Consulta', '2025-11-27 15:44:11', 0, 20),
(19, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente teste2.', 'Consulta', '2025-11-27 15:45:03', 0, 20),
(20, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-11-27 15:45:23', 0, 20),
(21, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente2.', 'Consulta', '2025-11-27 16:06:20', 0, 21),
(22, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente2.', 'Consulta', '2025-11-27 16:58:38', 0, 21),
(23, 'Prescrição atualizada', 'A prescrição do paciente paciente2 foi atualizada.', 'Consulta', '2025-11-27 17:01:38', 0, 21),
(24, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente paciente.', 'Consulta', '2025-11-27 17:08:01', 0, 11),
(25, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-11-28 10:29:41', 0, 21),
(26, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-11-28 10:29:47', 0, 21),
(27, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-11-28 10:43:31', 0, 21),
(28, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-11-28 10:52:42', 0, 10),
(29, 'Triagem registada', 'Foi registada uma nova triagem para o paciente paciente4.', 'Consulta', '2025-12-02 14:30:16', 0, 30),
(30, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-03 11:06:00', 0, 20),
(31, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-03 11:52:06', 0, 20),
(32, 'Consulta iniciada', 'A sua consulta foi iniciada.', 'Consulta', '2025-12-03 11:52:42', 0, 11),
(33, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente.', 'Consulta', '2025-12-03 12:11:51', 0, 11),
(34, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 12:12:14', 0, 11),
(35, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 12:15:58', 0, 11),
(36, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 12:22:49', 0, 11),
(37, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 13:36:38', 0, 11),
(38, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-03 13:36:45', 0, 11),
(39, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente.', 'Consulta', '2025-12-03 13:37:40', 0, 11),
(40, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 13:37:49', 0, 11),
(41, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 13:38:24', 0, 11),
(42, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente.', 'Consulta', '2025-12-03 13:42:06', 0, 11),
(43, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 13:42:13', 0, 11),
(44, 'Prescrição atualizada', 'A prescrição do paciente paciente foi atualizada.', 'Consulta', '2025-12-03 13:42:16', 0, 11),
(45, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-03 13:42:21', 0, 11),
(46, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-03 13:44:19', 0, 11),
(47, 'Consulta iniciada', 'A sua consulta foi iniciada.', 'Consulta', '2025-12-03 15:43:47', 0, 30),
(48, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente4.', 'Consulta', '2025-12-03 15:44:18', 0, 30),
(49, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-03 15:44:25', 0, 30),
(50, 'Prescrição atualizada', 'A prescrição do paciente paciente4 foi atualizada.', 'Consulta', '2025-12-04 15:56:51', 0, 30),
(51, 'Prescrição atualizada', 'A prescrição do paciente paciente4 foi atualizada.', 'Consulta', '2025-12-04 15:56:58', 0, 30),
(52, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-04 15:57:05', 0, 30),
(53, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-04 15:57:15', 0, 30),
(54, 'Consulta iniciada', 'A sua consulta foi iniciada.', 'Consulta', '2025-12-04 16:25:20', 0, 22),
(55, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente paciente3.', 'Consulta', '2025-12-04 16:48:04', 0, 22),
(56, 'Prescrição atualizada', 'A prescrição do paciente paciente3 foi atualizada.', 'Consulta', '2025-12-04 16:52:47', 0, 22),
(57, 'Prescrição atualizada', 'A prescrição do paciente paciente3 foi atualizada.', 'Consulta', '2025-12-04 17:06:13', 0, 22),
(58, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-04 17:06:15', 0, 22),
(59, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-04 17:07:34', 0, 22),
(60, 'Consulta iniciada', 'A sua consulta foi iniciada.', 'Consulta', '2025-12-04 17:10:54', 0, 8),
(61, 'Nova prescrição', 'Foi emitida uma nova prescrição para o paciente henrique.', 'Consulta', '2025-12-04 17:11:02', 0, 8),
(62, 'Consulta retomada', 'A consulta foi retomada.', 'Consulta', '2025-12-04 17:11:04', 0, 8),
(63, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-04 17:11:12', 0, 8),
(64, 'Utilizador ativado', 'A conta paciente4 foi ativada.', 'Geral', '2025-12-19 10:49:20', 0, 13),
(65, 'Novo utilizador criado', 'Foi criada uma nova conta: medico2', 'Geral', '2025-12-24 13:32:44', 0, 13),
(66, 'Novo utilizador criado', 'Foi criada uma nova conta: medico3', 'Geral', '2025-12-24 14:41:08', 0, 13),
(67, 'Novo utilizador criado', 'Foi criada uma nova conta: medico3', 'Geral', '2025-12-24 14:51:10', 0, 13),
(68, 'Consulta eliminada', 'A \'Consulta #17\' foi apagada do histórico.', 'Geral', '2025-12-24 15:10:13', 0, 13),
(69, 'Consulta eliminada', 'A \'Consulta #16\' foi apagada do histórico.', 'Geral', '2025-12-24 15:10:15', 0, 13),
(70, 'Consulta eliminada', 'A \'Consulta #18\' foi apagada do histórico.', 'Geral', '2025-12-28 11:36:05', 0, 13),
(71, 'Consulta eliminada', 'A \'Consulta #20\' foi apagada do histórico.', 'Geral', '2025-12-28 11:42:56', 0, 13),
(72, 'Novo utilizador criado', 'Foi criada uma nova conta: paciente5', 'Geral', '2025-12-30 09:24:32', 0, 13),
(73, 'Novo utilizador criado', 'Foi criada uma nova conta: paciente6', 'Geral', '2025-12-30 09:44:06', 0, 13),
(74, 'Utilizador eliminado', 'A conta paciente6 foi eliminada.', 'Geral', '2025-12-30 09:44:09', 0, 13),
(75, 'Utilizador eliminado', 'A conta paciente5 foi eliminada.', 'Geral', '2025-12-30 09:44:59', 0, 13),
(76, 'Novo utilizador criado', 'Foi criada uma nova conta: paciente5', 'Geral', '2025-12-30 09:45:54', 0, 13),
(77, 'Utilizador eliminado', 'A conta paciente5 foi eliminada.', 'Geral', '2025-12-30 09:53:51', 0, 13),
(78, 'Consulta eliminada', 'A \'Consulta #23\' foi apagada do histórico.', 'Geral', '2025-12-30 10:03:33', 0, 13),
(79, 'Consulta eliminada', 'A \'Consulta #21\' foi apagada do histórico.', 'Geral', '2025-12-30 10:09:27', 0, 13),
(80, 'Novo utilizador criado', 'Foi criada uma nova conta: paciente5', 'Geral', '2025-12-30 10:12:20', 0, 13),
(81, 'Utilizador ativado', 'A conta paciente4 foi ativada.', 'Geral', '2026-01-02 20:53:19', 0, 13),
(82, 'Utilizador ativado', 'A conta paciente4 foi ativada.', 'Geral', '2026-01-02 20:53:26', 0, 13),
(83, 'Utilizador ativado', 'A conta paciente51 foi ativada.', 'Geral', '2026-01-17 14:31:09', 0, 13),
(84, 'Utilizador ativado', 'A conta paciente51 foi ativada.', 'Geral', '2026-01-17 14:31:24', 0, 13),
(85, 'Utilizador ativado', 'A conta paciente4 foi ativada.', 'Geral', '2026-01-21 19:16:16', 0, 13);

-- --------------------------------------------------------

--
-- Table structure for table `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `observacoes` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_prescricao` (`consulta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prescricao`
--

INSERT INTO `prescricao` (`id`, `observacoes`, `dataprescricao`, `consulta_id`) VALUES
(17, '', '2025-12-28 11:34:46', 19),
(18, '', '2025-12-28 11:56:28', 22),
(21, '', '2025-12-30 10:09:47', 24),
(22, '1', '2026-01-17 14:32:47', 26),
(23, '2', '2026-01-17 14:33:57', 27),
(24, '2', '2026-01-17 14:52:51', 28),
(25, '', '2026-01-24 20:45:50', 29),
(26, '', '2026-01-24 20:48:09', 30);

-- --------------------------------------------------------

--
-- Table structure for table `prescricaomedicamento`
--

DROP TABLE IF EXISTS `prescricaomedicamento`;
CREATE TABLE IF NOT EXISTS `prescricaomedicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `posologia` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prescricao_id` int NOT NULL,
  `medicamento_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_prescricaoMed_prescricao` (`prescricao_id`),
  KEY `fk_prescricaoMed_medicamento` (`medicamento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescricaomedicamento`
--

INSERT INTO `prescricaomedicamento` (`id`, `posologia`, `prescricao_id`, `medicamento_id`) VALUES
(17, '4 c', 17, 3),
(18, '2 c', 18, 4),
(19, '1 c', 18, 5),
(22, '5 c', 21, 5),
(23, '2', 22, 2),
(24, '2', 23, 4),
(25, '2', 24, 5),
(26, '2', 25, 12),
(27, '2', 26, 38);

-- --------------------------------------------------------

--
-- Table structure for table `pulseira`
--

DROP TABLE IF EXISTS `pulseira`;
CREATE TABLE IF NOT EXISTS `pulseira` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `prioridade` enum('Vermelho','Laranja','Amarelo','Verde','Azul','Pendente') NOT NULL,
  `status` enum('Em espera','Em atendimento','Atendido') DEFAULT 'Em espera',
  `tempoentrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userprofile_pulseira` (`userprofile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(22, '19C5F289', 'Verde', 'Atendido', '2025-12-24 12:51:56', 22),
(25, '792858F5', 'Laranja', 'Atendido', '2025-12-28 11:55:46', 11),
(26, '189BEB91', 'Vermelho', 'Atendido', '2025-12-29 10:20:39', 21),
(30, '5F8CF10C', 'Vermelho', 'Em atendimento', '2026-01-02 20:47:40', 11),
(70, '5420A0C3', 'Vermelho', 'Atendido', '2026-01-15 17:34:04', 39),
(75, 'D1E04AE4', 'Vermelho', 'Atendido', '2026-01-17 14:12:29', 39),
(76, '79B5FEC8', 'Amarelo', 'Atendido', '2026-01-17 14:18:30', 11),
(77, '5A8BF185', 'Azul', 'Atendido', '2026-01-17 14:28:13', 11),
(78, 'AF366D1A', 'Laranja', 'Atendido', '2026-01-17 14:34:08', 39),
(79, 'F4D8A2F7', 'Laranja', 'Atendido', '2026-01-17 14:42:42', 22),
(81, '1A2617C0', 'Laranja', 'Atendido', '2026-01-17 15:08:16', 11),
(82, '1A884777', 'Pendente', 'Em espera', '2026-01-20 18:10:50', 30),
(83, 'F8281E12', 'Verde', 'Atendido', '2026-01-24 20:47:07', 39);

-- --------------------------------------------------------

--
-- Table structure for table `triagem`
--

DROP TABLE IF EXISTS `triagem`;
CREATE TABLE IF NOT EXISTS `triagem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `motivoconsulta` varchar(255) DEFAULT NULL,
  `queixaprincipal` text,
  `descricaosintomas` text,
  `iniciosintomas` datetime DEFAULT NULL,
  `intensidadedor` int DEFAULT NULL,
  `alergias` text,
  `medicacao` text,
  `datatriagem` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userprofile_id` int NOT NULL,
  `pulseira_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pulseira_id` (`pulseira_id`),
  KEY `fk_triagem_userprofile_id` (`userprofile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `triagem`
--

INSERT INTO `triagem` (`id`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `alergias`, `medicacao`, `datatriagem`, `userprofile_id`, `pulseira_id`) VALUES
(36, 'teste2', 'teste2', 'teste2', '2025-12-24 12:51:00', 3, 'teste2', 'teste2', '2025-12-24 12:51:56', 22, 22),
(40, 'Dor no Queixo', 'teste', 'teste', '2025-12-28 11:55:00', 1, 'tsetes', 'testse', '2025-12-28 11:55:46', 11, 25),
(41, 'Dor no Queixo', 'teste', 'teste', '2025-12-28 11:55:00', 1, 'tsetes', 'testse', '2025-12-28 11:56:01', 11, 25),
(42, 'Dor no Queixo2', 'dOR NO QUERIO', 'querio', '2025-12-29 10:20:00', 7, 'dor', 'no querio', '2025-12-29 10:20:39', 21, 26),
(43, 'Dor no Queixo2', 'dOR NO QUERIO', 'querio', '2025-12-29 10:20:00', 7, 'dor', 'no querio', '2025-12-29 10:45:45', 21, 26),
(93, 'sad', 'sad', 'asd', '2026-01-17 14:12:00', 6, 'sad', 'asd', '2026-01-17 14:12:29', 39, 75),
(94, '', '', '', NULL, 0, '', '', '2026-01-17 14:18:30', 11, 76),
(95, '', '', '', NULL, 0, '', '', '2026-01-17 14:28:13', 11, 77),
(96, 'sdf', 'sdf', 'sdf', '2026-01-17 14:34:00', 7, 'sdfsd', 'f', '2026-01-17 14:34:08', 39, 78),
(97, 'ewf', 'ewf', 'ewf', '2026-01-17 14:42:00', 4, 'wef', 'wef', '2026-01-17 14:42:42', 22, 79),
(99, '', '', '', NULL, 0, '', '', '2026-01-17 15:08:16', 11, 81),
(100, '', '', '', NULL, 0, '', '', '2026-01-20 18:10:50', 30, 82),
(101, '', '', '', NULL, 0, '', '', '2026-01-24 20:47:07', 39, 83);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `verification_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `primeiro_login` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`, `primeiro_login`) VALUES
(13, 'Henrique', '0H9szpXIDEGRETNZV8Ek9-MGPyD6fr6D', '$2y$13$Vc7qBR3svsaIkQBi4CVeXefltcylcwDO534Bj3XiQllSxK5wGt1mi', NULL, 'henrique@admin.com', 10, 1761753767, 1769286939, NULL, 0),
(14, 'Alfredo', 'MJ6vfKvxKc55si89uS4LFZ8idnQir-V_', '$2y$13$V89G4x2gPrOT.daBle0b9OMAVRg3z5V/lMYgjw5.hyEm0NIoleL1m', NULL, 'alfredo@gmail.com', 10, 1761765484, 1769269470, NULL, 0),
(15, 'Renato', 'DHU1lrPcz4pIVPwgC6PLwRRBzV6OAzQO', '$2y$13$kLlON8JaHqYPIoQbmCJp/OIkMRIBYt6fIjMbjT0oRfrdmvi53fao2', NULL, 'renato@admin.com', 10, 1761841878, 1769269429, NULL, 0),
(16, 'Gil', 'pW5Yn4GxUh8BgNglX2ftCiizXcfhbWc3', '$2y$13$/WzBBrgE.NmrxPzoDEYcO.O7kC7oY4UirlkgUFif3I1Ql7wDLHxyy', NULL, 'gil@gmail.com', 10, 1762957973, 1769269446, NULL, 0),
(17, 'fabio', 'uzFzzNSoXGyx_G6WA_PXA-2XE9XsG2A-', '$2y$13$1H4srvB689klIiVTDDXvveyGhpbV5LITcpj.wXY5ikgtMUFuceO2m', NULL, 'fabio@gmail.com', 10, 1762960888, 1762960953, NULL, 0),
(18, 'Paulo', 'b3R7Smmh_RItS1EM9ljuvZlRD8vAz3ou', '$2y$13$JXHr5Q0leMCTdNKBMRyoe.9APMMV6qweaYm3WinZwa7VFcbBfUxMa', NULL, 'paulo@gmail.com', 10, 1762961282, 1769269401, NULL, 0),
(19, 'admin', '1eb4dvYH88w6nwTQQxOx8X4usCN5Vsx9', '$2y$13$c9RoUdyuZeDVhARmt/bLtOc73kvunKc1rFSn.O9.EZW2DtvniKOUi', NULL, 'admin@gmail.com', 10, 1762983420, 1762983426, NULL, 0),
(25, 'Jose', 'XwqsVZrd-l2Se_x-z3LmJWsR_MW_Vzez', '$2y$13$4Dxl1XDoyn2940SAJqqDfeG1aYWf3TWtNe2vFQSBuJ.jith3Pv6Xu', NULL, 'jose@gmail.com', 10, 1763136144, 1769267769, NULL, 0),
(26, 'Pedro', 'rJqHeDquPDduFwRPdC9pRaqsZ2XAeWi_', '$2y$13$.IGIDv/Kxo58wBu3pC7GIuB5JZC73z8AsQuMO3hCJ09HnBhlwOYyy', NULL, 'pedro@gmail.com', 10, 1763312479, 1769267747, NULL, 0),
(27, 'Inês', 'M12Ywnxc6T9QqSUk7ByDE2dt5KrmSXqF', '$2y$13$A3Lv7bumptwJjInQWMyFV.QWZJbIFGnrKlJy9Rp2XjsS/iOsHVKa2', NULL, 'ines@gmail.com', 10, 1764160961, 1769267728, NULL, 0),
(28, 'Miguel', 'k13dhaCCRRBjtj2D0Xpa4Hq-nUAyfF65', '$2y$13$.6E1rZ7dieB9rySuOaCQa.l3NQish3vfLICA5JkVJbdsxSNH1.Sim', NULL, 'miguel@gmail.com', 10, 1764161031, 1769267689, NULL, 0),
(36, 'João', 'nMAjM7NytYFZvUaHcJgCYuZr1qRv9yoo', '$2y$13$qXyjg6QOcS5Jy0VufQznSOOijgRgP7ad9QYXvxVN4oj2ep7R0C4tC', NULL, 'joao@gmail.com', 10, 1764165030, 1769267697, NULL, 0),
(37, 'Filipa', 'vKH9pVNKLaFVOox1TdgOhIPOa02SQFNu', '$2y$13$ktzfeIknk8e79AyE6WUy1uAn3KOA/boZvVu4wM1kiFI9bFxNKF4eC', NULL, 'filipa@gmail.com', 10, 1764762374, 1769267706, NULL, 0),
(38, 'Dinis', 'lNAsnHHNyU_2Aw9tyFltj3GFCECxMNe2', '$2y$13$qvXjCdfCZ8XhNXK385umD.NY3/Gu2hV80nDqzrd4fc69eF1EDDuY6', NULL, 'dinis@gmail.com', 10, 1764868352, 1769267645, NULL, 1),
(39, 'Alberto', 'y3teXimPkVPWYKsD5E7v7qk83j2Qq6SC', '$2y$13$moT6dBtL1KNKV3GbLZdaJe6at6EEQkEPWBbBb9w.EgDIjmFMGze2q', NULL, 'alberto@gmail.com', 10, 1766583164, 1769267638, NULL, 1),
(45, 'Gonçalo', 'lFPX8J0tkhNsbsuvxX6X_IVn-2kDsErn', '$2y$13$ZOlDHO0NCEhnpRiAK1ILue8aL8dRqMC1nfgaJONkSAmFAiSU7bQ6O', NULL, 'goncalo@gmail.com', 10, 1766587870, 1769285872, NULL, 1),
(49, 'Maria', '8UVJwFUAdtxrFA7wTqH-QUK2a6fzMfQq', '$2y$13$qJlj0jbEfMwDGPQvxQogouJKuTwsioKn1Zg0Iz5Ebil.XeBwiwSZy', NULL, 'maria@gmail.com', 10, 1767089540, 1769267528, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE IF NOT EXISTS `userprofile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `morada` varchar(255) DEFAULT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `sns` varchar(9) DEFAULT NULL,
  `datanascimento` date DEFAULT NULL,
  `genero` char(1) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `user_id` int NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_userprofile_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`, `estado`) VALUES
(8, 'Henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '1990-07-19', 'M', '915429512', 13, 1),
(9, 'Alfredo', 'alfredo@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14, 1),
(10, 'Renato', 'renato@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '1989-12-12', 'M', '915429512', 15, 1),
(11, 'Gil', 'gil@gmail.com', 'Rua das Flores, nº72 2445-0345', '498765456', '798765456', '2005-02-02', 'M', '929956648', 16, 1),
(12, 'Paulo', 'paulo@gmail.com', 'rua', '312386423', '312386423', '2025-11-11', 'M', '2343412313', 18, 1),
(13, 'admin', 'admin@gmail.com', 'Leiria..', '666666666', '666666666', '2005-07-25', 'M', '912881282', 19, 1),
(19, 'Jose', 'jose@gmail.com', 'Leiria', '387654567', '198765456', '2013-06-13', 'M', '929956648', 25, 1),
(20, 'Pedro', 'pedro@gmail.com', 's', '834467326', '834467326', '2005-04-13', 'M', '917857332', 26, 1),
(21, 'Inês', 'ines@gmail.com', 'Rua das Flores, nº72 2445-034', '648549245', '854926135', '2025-11-26', 'F', '915429512', 27, 1),
(22, 'Miguel', 'miguel@gmail.com', 'Rua das Flores, nº72 2445-034', '548659135', '584965821', '2025-11-26', 'M', '915429512', 28, 1),
(30, 'João', 'joao@gmail.com', 'Rua das Flores, nº72 2445-034', '858372838', '377823737', '2025-11-26', 'M', '915429512', 36, 1),
(31, 'Filipa', 'filipa@gmail.com', 'Rua das Flores, nº72 2445-034', '234324324', '234234324', '2025-12-03', 'F', '915429512', 37, 1),
(32, 'Dinis', 'dinis@gmail.com', 'Rua das Flores, nº72 2445-034', '959595955', '259292929', '2025-12-04', 'M', '964586959', 38, 1),
(33, 'Alberto', 'alberto@gmail.com', 'Rua das Flores, nº72 2445-034', '987654567', '876543456', '2006-06-06', 'M', '912881283', 39, 1),
(35, 'Gonçalo', 'goncalo@gmail.com', 'Rua das Flores, nº72 2445-034', '456789098', '456789098', '2004-07-07', 'M', '915429748', 45, 1),
(39, 'Maria', 'maria@gmail.com', 'Rua das Flores, nº72 2445-0344', '876543256', '876546789', '2025-12-18', 'F', '912881495', 49, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `fk_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Constraints for table `prescricao`
--
ALTER TABLE `prescricao`
  ADD CONSTRAINT `prescricao_ibfk_1` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `prescricaomedicamento`
--
ALTER TABLE `prescricaomedicamento`
  ADD CONSTRAINT `fk_prescricaoMed_medicamento` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamento` (`id`),
  ADD CONSTRAINT `fk_prescricaoMed_prescricao` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `fk_userprofile_pulseira` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `fk_pulseira_id` FOREIGN KEY (`pulseira_id`) REFERENCES `pulseira` (`id`),
  ADD CONSTRAINT `fk_triagem_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
