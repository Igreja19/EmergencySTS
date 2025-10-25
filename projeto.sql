-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 25, 2025 at 10:34 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

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
  `item_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604),
('author', '2', 1761233604);

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
('author', 1, NULL, NULL, NULL, 1761233604, 1761233604),
('createPost', 2, 'Create a post', NULL, NULL, 1761233604, 1761233604),
('updatePost', 2, 'Update post', NULL, NULL, 1761233604, 1761233604);

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
('admin', 'author'),
('author', 'createPost'),
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
  `id` int NOT NULL,
  `data_consulta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Aberta','Encerrada') NOT NULL DEFAULT 'Aberta',
  `diagnostico_id` int NOT NULL,
  `paciente_id` int NOT NULL,
  `utilizador_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_diagnostico1_idx` (`diagnostico_id`),
  KEY `fk_consulta_paciente1_idx` (`paciente_id`),
  KEY `fk_consulta_utilizador1_idx` (`utilizador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notificacao`
--

DROP TABLE IF EXISTS `notificacao`;
CREATE TABLE IF NOT EXISTS `notificacao` (
  `id` int NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` enum('Consulta','Prioridade','Geral') NOT NULL DEFAULT 'Geral',
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `paciente_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_paciente1_idx` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `observacao`
--

DROP TABLE IF EXISTS `observacao`;
CREATE TABLE IF NOT EXISTS `observacao` (
  `id` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `sintomas` text NOT NULL,
  `dataregisto` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_diagnostico_consulta1_idx` (`consulta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `paciente`
--

DROP TABLE IF EXISTS `paciente`;
CREATE TABLE IF NOT EXISTS `paciente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomecompleto` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nif` varchar(9) NOT NULL,
  `datanascimento` date NOT NULL,
  `sns` varchar(20) DEFAULT NULL,
  `genero` enum('Masculino','Feminino','Outro') NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `morada` varchar(255) NOT NULL,
  `observacoes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nif_UNIQUE` (`nif`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `paciente`
--

INSERT INTO `paciente` (`id`, `nomecompleto`, `nif`, `datanascimento`, `sns`, `genero`, `telefone`, `email`, `morada`, `observacoes`) VALUES
(1, 'Miguel', '256776857', '2005-07-25', '234', 'Masculino', '912881282', 'miguelctobias@gmail.com', 'Leiria', 'Nenhuma'),
(2, 'Catia', '853', '2004-03-12', '123', 'Feminino', '987654321', 'catia@gmail.com', 'Leiria', 'Nenhuma');

-- --------------------------------------------------------

--
-- Table structure for table `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL,
  `medicamento` varchar(100) NOT NULL,
  `dosagem` varchar(100) NOT NULL,
  `frequencia` varchar(100) NOT NULL,
  `observacoes` text NOT NULL,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_prescricao_consulta1_idx` (`consulta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pulseira`
--

DROP TABLE IF EXISTS `pulseira`;
CREATE TABLE IF NOT EXISTS `pulseira` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `prioridade` enum('Vermelho','Laranja','Amarelo','Verde','Azul') NOT NULL,
  `status` enum('Aguardando','Em atendimento','Atendido') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Aguardando',
  `tempoentrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `triagem_id` int NOT NULL,
  `paciente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pulseira_triagem1_idx` (`triagem_id`),
  KEY `paciente_id` (`paciente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `triagem_id`, `paciente_id`) VALUES
(1, 'AF2A8A37', '', 'Aguardando', '2025-10-25 19:02:56', 3, NULL),
(2, 'BDAFB908', '', 'Aguardando', '2025-10-25 19:05:58', 6, NULL),
(3, '8F5B69FB', '', 'Aguardando', '2025-10-25 19:06:06', 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `triagem`
--

DROP TABLE IF EXISTS `triagem`;
CREATE TABLE IF NOT EXISTS `triagem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomecompleto` varchar(100) DEFAULT NULL,
  `datanascimento` date DEFAULT NULL,
  `sns` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `motivoconsulta` varchar(255) DEFAULT NULL,
  `queixaprincipal` text,
  `descricaosintomas` text,
  `iniciosintomas` datetime DEFAULT NULL,
  `intensidadedor` int DEFAULT NULL,
  `condicoes` text,
  `alergias` text,
  `medicacao` text,
  `motivo` text NOT NULL,
  `prioridadeatribuida` enum('Vermelho','Laranja','Amarelo','Verde','Azul') NOT NULL,
  `datatriagem` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discriminacaoprincipal` varchar(255) DEFAULT NULL,
  `paciente_id` int NOT NULL,
  `utilizador_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_triagem_paciente1_idx` (`paciente_id`),
  KEY `fk_triagem_utilizador1_idx` (`utilizador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `triagem`
--

INSERT INTO `triagem` (`id`, `nomecompleto`, `datanascimento`, `sns`, `telefone`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `condicoes`, `alergias`, `medicacao`, `motivo`, `prioridadeatribuida`, `datatriagem`, `discriminacaoprincipal`, `paciente_id`, `utilizador_id`) VALUES
(1, 'Miguel', '2005-07-25', '2345678', '234567', 'garganta', 'Dor e tosse', 'Dor', '2025-10-25 17:00:00', 8, 'Dor', 'N', 'N', '', '', '2025-10-25 18:00:00', 'Dor', 0, 0),
(2, 'Miguel', '2005-07-25', '2345678', '234567', 'garganta', 'Dor e tosse', 'Dor', '2025-10-25 17:00:00', 8, 'Dor', 'N', 'N', '', '', '2025-10-25 18:00:00', 'Dor', 0, 0),
(3, 'Miguel', '2005-07-25', '256776857', '912881282', 'garganta', 'Dor', 'dor', '2025-07-25 11:11:00', 6, 'dor', 'nenhuma', 'nenhuma', '', '', '2025-10-25 11:11:00', 'Dor', 0, 0),
(4, 'Miguel', '2005-07-25', '256775857', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 0, 0),
(5, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 0, 0),
(6, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 0, 0),
(7, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 0, 0);

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'admin', 'wF3wFkGpMxcqjhdrmS3WJvWNEdYB2WaT', '$2y$13$EIvoQKhEciV2r1hAF3AJauyr5nuyHYnJ7X/S9d9nV4WR4dYUxUWfG', NULL, 'admin@gmail.com', 10, 1761233560, 1761233560, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE IF NOT EXISTS `userprofile` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `consulta_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_utilizador_consulta1_idx` (`consulta_id`),
  KEY `fk_utilizador_triagem1_idx` (`triagem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `pulseira_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
