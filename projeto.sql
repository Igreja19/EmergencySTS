-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 19, 2025 at 07:45 PM
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
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604),
('admin', '19', 1762983475),
('enfermeiro', '13', 1763136201),
('enfermeiro', '18', 1763136190),
('medico', '15', 1763136196),
('paciente', '16', 1763135389),
('paciente', '20', 1763135051),
('paciente', '21', 1763135962),
('paciente', '22', 1763135978),
('paciente', '24', 1763136036),
('paciente', '25', 1763136144),
('paciente', '26', 1763312479);

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
  `estado` enum('Aberta','Encerrada','Em curso') NOT NULL DEFAULT 'Aberta',
  `observacoes` text,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consulta`
--

INSERT INTO `consulta` (`id`, `data_consulta`, `estado`, `observacoes`, `userprofile_id`, `triagem_id`, `data_encerramento`, `relatorio_pdf`) VALUES
(5, '2025-11-18 00:52:17', 'Encerrada', 'bfhb', 8, 16, '2025-11-18 18:59:35', NULL),
(7, '2025-11-18 17:49:18', 'Em curso', 'dfsdf', 9, 11, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medicamento`
--

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `dosagem` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicamento`
--

INSERT INTO `medicamento` (`id`, `nome`, `dosagem`) VALUES
(1, 'Paracetamol', '500mg'),
(2, 'Paracetamol', '1g'),
(3, 'Ibuprofeno', '400mg'),
(4, 'Ibuprofeno', '600mg'),
(5, 'Aspirina', '500mg'),
(6, 'Amoxicilina', '500mg'),
(7, 'Amoxicilina', '875mg'),
(8, 'Clavulanato + Amoxicilina', '875mg/125mg'),
(9, 'Azitromicina', '500mg'),
(10, 'Ciprofloxacina', '500mg'),
(11, 'Metformina', '850mg'),
(12, 'Metformina', '1000mg'),
(13, 'Omeprazol', '20mg'),
(14, 'Pantoprazol', '40mg'),
(15, 'Losartan', '50mg'),
(16, 'Losartan', '100mg'),
(17, 'Amlodipina', '5mg'),
(18, 'Amlodipina', '10mg'),
(19, 'Enalapril', '20mg'),
(20, 'Simvastatina', '20mg'),
(21, 'Simvastatina', '40mg'),
(22, 'Atorvastatina', '20mg'),
(23, 'Atorvastatina', '40mg'),
(24, 'Furosemida', '40mg'),
(25, 'Prednisolona', '20mg'),
(26, 'Dexametasona', '4mg'),
(27, 'Insulina Rápida', '100 UI'),
(28, 'Insulina Basal', '100 UI'),
(29, 'Dipirona', '500mg'),
(30, 'Cetirizina', '10mg'),
(31, 'Loratadina', '10mg'),
(32, 'Salbutamol', '100mcg'),
(33, 'Budesonida + Formoterol', '160/4.5mcg'),
(34, 'Tramadol', '50mg'),
(35, 'Codeína', '30mg'),
(36, 'Clonazepam', '2.5mg/mL'),
(37, 'Diazepam', '10mg'),
(38, 'Sertralina', '50mg'),
(39, 'Sertralina', '100mg'),
(40, 'Fluoxetina', '20mg');

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `titulo` varchar(150) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `tipo` enum('Consulta','Prioridade','Geral') NOT NULL DEFAULT 'Geral',
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_userprofile_id` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `observacoes` text NOT NULL,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_prescricao` (`consulta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prescricao`
--

INSERT INTO `prescricao` (`id`, `observacoes`, `dataprescricao`, `consulta_id`) VALUES
(4, 'sdf', '2025-11-19 18:33:27', 7);

-- --------------------------------------------------------

--
-- Table structure for table `prescricaomedicamento`
--

DROP TABLE IF EXISTS `prescricaomedicamento`;
CREATE TABLE IF NOT EXISTS `prescricaomedicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `posologia` varchar(255) NOT NULL,
  `prescricao_id` int NOT NULL,
  `medicamento_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_prescricaoMed_prescricao` (`prescricao_id`),
  KEY `fk_prescricaoMed_medicamento` (`medicamento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescricaomedicamento`
--

INSERT INTO `prescricaomedicamento` (`id`, `posologia`, `prescricao_id`, `medicamento_id`) VALUES
(1, '1', 4, 1),
(2, '2', 4, 17),
(3, '3', 4, 12);

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(4, '9D3AA8E5', 'Azul', 'Em atendimento', '2025-10-29 20:45:21', 9),
(5, '97A510BD', 'Vermelho', 'Em espera', '2025-10-30 15:07:38', 9),
(6, 'B2882746', 'Verde', 'Em espera', '2025-10-30 16:40:46', 10),
(7, '34CC9466', 'Amarelo', 'Em atendimento', '2025-10-31 11:32:55', 8),
(8, 'C3FC873E', 'Pendente', 'Em espera', '2025-11-18 17:36:55', 20),
(9, '41B5091B', 'Pendente', 'Em espera', '2025-11-19 19:17:24', 11);

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `triagem`
--

INSERT INTO `triagem` (`id`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `alergias`, `medicacao`, `datatriagem`, `userprofile_id`, `pulseira_id`) VALUES
(11, 'Dor no Queixo', 'sdf', 'sdfs', '4333-03-12 23:32:00', 3, 'sdf', 'sdf', '2025-10-29 20:45:21', 9, 4),
(12, 'gfhfgh', 'fghfh', 'dghfh', '4334-03-12 03:23:00', 10, 'efsg', 'dfg', '2025-10-30 15:07:38', 9, 5),
(13, 'Dor no Queixo de baixo', 'Sangue no queixo', 'Doi ao tocar na testa', '3222-05-04 05:08:00', 10, 'nao tenoh', 'viogrum', '2025-10-30 16:40:46', 10, 6),
(16, 'teste', 'teste', 'teste', '2343-04-23 03:23:00', 10, 'teste', 'teste', '2025-10-31 11:32:55', 8, 7),
(18, 'sdf', 'dsf', 'sdf', '2005-03-12 02:23:00', 4, 'dsfg', 'dsdf', '2025-11-19 19:17:24', 11, 9);

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`, `primeiro_login`) VALUES
(13, 'henrique', 'vaP80G6lRRA6t6gzN8V8mdd4r2GTaFYA', '$2y$13$eb3e8WM7NkvGb8jJ/90emu.rvswWdGOBRwPMDJxO1cqR9iCIA/fRi', NULL, 'henrique@admin.com', 10, 1761753767, 1761907875, NULL, 0),
(14, 'henrique2', 'js3kFvtb9Wll0UeVVJsnycj6gxfzeuqO', '$2y$13$gj08.hqXkJZdLKWyLPL1buvBCEVoW74SeGVZViX1Sm3k4p.gTR/yC', NULL, 'henrique2@admin.com', 10, 1761765484, 1761907640, NULL, 0),
(15, 'henrique3', '63wiuOFwnacZUswcy7rsJvH0VbsALtIl', '$2y$13$Uqcj5pOm8btQqPmqHVD7xOOcuVMTiC3.PTNLwwG/js0JKNgi8l8tC', NULL, 'henrique3@admin.com', 10, 1761841878, 1761841884, NULL, 0),
(16, 'paciente', 'iwCBKSHgv3PdisglhLUwIi7uaodtv5KZ', '$2y$13$k5/Z4U83KEGiWv5pZaZWK.Hw0FYdcyZba6EmO.nr9MHCDkrwfMl.u', NULL, 'paciente@gmail.com', 10, 1762957973, 1762957982, NULL, 0),
(17, 'fabio', 'uzFzzNSoXGyx_G6WA_PXA-2XE9XsG2A-', '$2y$13$1H4srvB689klIiVTDDXvveyGhpbV5LITcpj.wXY5ikgtMUFuceO2m', NULL, 'fabio@gmail.com', 10, 1762960888, 1762960953, NULL, 0),
(18, 'zezoca', '5Bafu7LFi6mEO7F0RH7rLBcxEj0YhgMp', '$2y$13$01.YP4ozXB5DwglMWyfRFOqrQQpT6aDFvmdMpB2LVWhfAl02IQ2MW', NULL, 'zezoca@gmail.com', 10, 1762961282, 1762961299, NULL, 0),
(19, 'admin', '1eb4dvYH88w6nwTQQxOx8X4usCN5Vsx9', '$2y$13$c9RoUdyuZeDVhARmt/bLtOc73kvunKc1rFSn.O9.EZW2DtvniKOUi', NULL, 'admin@gmail.com', 10, 1762983420, 1762983426, NULL, 0),
(25, '12345', '069jWVY2Hf57qaZs7GVttH0C546yFHhr', '$2y$13$ZCoZAalg/kKJHluxdVOzsOJ01drMAzjy2a3f25KWUmYM2Ya8jONc6', NULL, '12345@gmail.com', 10, 1763136144, 1763136150, NULL, 0),
(26, 'teste2', 'swgEKZYTj4noVicIu0Gn9iULcVNsNeTJ', '$2y$13$LegFdmCEgQ5yL4kw7pgv4OJh.Xn2nY3ZWugAz8XgFUlPVxBdHStfq', NULL, 'teste@gmail.com', 10, 1763312479, 1763312485, NULL, 0);

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_userprofile_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`) VALUES
(8, 'henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '2333-02-23', 'M', '915429512', 13),
(9, 'Henrique Salgado', 'henriquesalgado@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14),
(10, 'henrique3', 'henrique3@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '2234-03-02', 'M', '915429512', 15),
(11, 'paciente', 'paciente@gmail.com', '1', '1', '1', '1111-01-01', 'M', '1', 16),
(12, 'zezoca', 'zezoca@gmail.com', 'rua', '123', '1234', '2025-11-11', 'M', '2343412313', 18),
(13, 'admin', 'admin@gmail.com', 'Leiria', '123', '123', '2005-07-25', 'M', '912881282', 19),
(19, '12345', '12345@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 25),
(20, 'teste2', 'teste@gmail.com', 's', 'sdf', 'sdf', '2005-04-13', 'M', 'sdf', 26);

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
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_userprofile_pulseira` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Constraints for table `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `fk_pulseira_id` FOREIGN KEY (`pulseira_id`) REFERENCES `pulseira` (`id`),
  ADD CONSTRAINT `fk_triagem_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Constraints for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
