-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 31-Out-2025 às 12:00
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_assignment`
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
-- Extraindo dados da tabela `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604);

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_item`
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
-- Extraindo dados da tabela `auth_item`
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
('updatePost', 2, 'Update post', NULL, NULL, 1761233604, 1761233604),
('verRegisto', 2, 'Visualizar registos', NULL, NULL, 1761233604, 1761233604);

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'updatePost');

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_rule`
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
-- Estrutura da tabela `consulta`
--

DROP TABLE IF EXISTS `consulta`;
CREATE TABLE IF NOT EXISTS `consulta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_consulta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Aberta','Encerrada','Em curso') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aberta',
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `prescricao_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_prescricao_id` (`prescricao_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `medicamento`
--

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `dosagem` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1761748339);

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
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
-- Estrutura da tabela `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL,
  `observacoes` text NOT NULL,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_prescricao` (`consulta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescricaomedicamento`
--

DROP TABLE IF EXISTS `prescricaomedicamento`;
CREATE TABLE IF NOT EXISTS `prescricaomedicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `posologia` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prescricao_id` int NOT NULL,
  `medicamento_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_prescricaoMed_prescricao` (`prescricao_id`),
  KEY `fk_prescricaoMed_medicamento` (`medicamento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pulseira`
--

DROP TABLE IF EXISTS `pulseira`;
CREATE TABLE IF NOT EXISTS `pulseira` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `prioridade` enum('Vermelho','Laranja','Amarelo','Verde','Azul','Pendente') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` enum('Em espera','Em atendimento','Atendido') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Em espera',
  `tempoentrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userprofile_pulseira` (`userprofile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(4, '9D3AA8E5', 'Azul', 'Em espera', '2025-10-29 20:45:21', 9),
(5, '97A510BD', '', 'Em espera', '2025-10-30 15:07:38', 9),
(6, 'B2882746', 'Verde', 'Em espera', '2025-10-30 16:40:46', 10),
(7, '34CC9466', 'Amarelo', 'Em espera', '2025-10-31 11:32:55', 8);

-- --------------------------------------------------------

--
-- Estrutura da tabela `triagem`
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `triagem`
--

INSERT INTO `triagem` (`id`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `alergias`, `medicacao`, `datatriagem`, `userprofile_id`, `pulseira_id`) VALUES
(11, 'Dor no Queixo', 'sdf', 'sdfs', '4333-03-12 23:32:00', 3, 'sdf', 'sdf', '2025-10-29 20:45:21', 9, 4),
(12, 'gfhfgh', 'fghfh', 'dghfh', '4334-03-12 03:23:00', 10, 'efsg', 'dfg', '2025-10-30 15:07:38', 9, 5),
(13, 'Dor no Queixo de baixo', 'Sangue no queixo', 'Doi ao tocar na testa', '3222-05-04 05:08:00', 10, 'nao tenoh', 'viogrum', '2025-10-30 16:40:46', 10, 6),
(16, 'teste', 'teste', 'teste', '2343-04-23 03:23:00', 10, 'teste', 'teste', '2025-10-31 11:32:55', 8, 7);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`, `primeiro_login`) VALUES
(1, 'admin', 'wF3wFkGpMxcqjhdrmS3WJvWNEdYB2WaT', '$2y$13$EIvoQKhEciV2r1hAF3AJauyr5nuyHYnJ7X/S9d9nV4WR4dYUxUWfG', NULL, 'admin@gmail.com', 10, 1761233560, 1761233560, NULL, 1),
(13, 'henrique', 'vaP80G6lRRA6t6gzN8V8mdd4r2GTaFYA', '$2y$13$eb3e8WM7NkvGb8jJ/90emu.rvswWdGOBRwPMDJxO1cqR9iCIA/fRi', NULL, 'henrique@admin.com', 10, 1761753767, 1761907875, NULL, 0),
(14, 'henrique2', 'js3kFvtb9Wll0UeVVJsnycj6gxfzeuqO', '$2y$13$gj08.hqXkJZdLKWyLPL1buvBCEVoW74SeGVZViX1Sm3k4p.gTR/yC', NULL, 'henrique2@admin.com', 10, 1761765484, 1761907640, NULL, 0),
(15, 'henrique3', '63wiuOFwnacZUswcy7rsJvH0VbsALtIl', '$2y$13$Uqcj5pOm8btQqPmqHVD7xOOcuVMTiC3.PTNLwwG/js0JKNgi8l8tC', NULL, 'henrique3@admin.com', 10, 1761841878, 1761841884, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `userprofile`
--

DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE IF NOT EXISTS `userprofile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `morada` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nif` varchar(9) NOT NULL,
  `sns` varchar(9) NOT NULL,
  `datanascimento` date NOT NULL,
  `genero` char(1) NOT NULL,
  `telefone` varchar(30) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_userprofile_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`) VALUES
(8, 'henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '2333-02-23', 'M', '915429512', 13),
(9, 'Henrique Salgado', 'henriquesalgado@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14),
(10, 'henrique3', 'henrique3@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '2234-03-02', 'M', '915429512', 15);

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescricao_id` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `fk_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `prescricao`
--
ALTER TABLE `prescricao`
  ADD CONSTRAINT `fk_consulta_prescricao` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `prescricaomedicamento`
--
ALTER TABLE `prescricaomedicamento`
  ADD CONSTRAINT `fk_prescricaoMed_medicamento` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamento` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_prescricaoMed_prescricao` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `fk_userprofile_pulseira` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `fk_pulseira_id` FOREIGN KEY (`pulseira_id`) REFERENCES `pulseira` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_triagem_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
