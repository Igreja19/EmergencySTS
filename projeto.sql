-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 24-Out-2025 às 09:13
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dados: `projeto`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604),
('author', '2', 1761233604);

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
('author', 1, NULL, NULL, NULL, 1761233604, 1761233604),
('createPost', 2, 'Create a post', NULL, NULL, 1761233604, 1761233604),
('updatePost', 2, 'Update post', NULL, NULL, 1761233604, 1761233604);

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'author'),
('author', 'createPost'),
('admin', 'updatePost');

-- --------------------------------------------------------

--
-- Estrutura da tabela `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
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
  `idmedico` int DEFAULT NULL,
  `idpulseria` int DEFAULT NULL,
  `data_consulta` datetime DEFAULT NULL,
  `estado` enum('Agendada','Em andamento','Finalizada','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `utilizador_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idmedico` (`idmedico`),
  KEY `idpulseria` (`idpulseria`),
  KEY `utilizador_id` (`utilizador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `diagnostico`
--

DROP TABLE IF EXISTS `diagnostico`;
CREATE TABLE IF NOT EXISTS `diagnostico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idconsulta` int DEFAULT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dataregisto` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idconsulta` (`idconsulta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1761232904),
('m130524_201442_init', 1761232907),
('m190124_110200_add_verification_token_column_to_user_table', 1761232907),
('m251023_151545_init_rbac', 1761232907),
('m251023_152137_init_rbac', 1761232907),
('m140506_102106_rbac_init', 1761232914),
('m170907_052038_rbac_add_index_on_auth_assignment_user_id', 1761232914),
('m180523_151638_rbac_updates_indexes_without_prefix', 1761232914),
('m200409_110543_rbac_update_mssql_trigger', 1761232914);

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
--

DROP TABLE IF EXISTS `notificacao`;
CREATE TABLE IF NOT EXISTS `notificacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idpaciente` int DEFAULT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo` enum('alerta','informativo','urgente') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datanotificacao` datetime DEFAULT NULL,
  `lida` tinyint(1) DEFAULT '0',
  `paciente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `observacao`
--

DROP TABLE IF EXISTS `observacao`;
CREATE TABLE IF NOT EXISTS `observacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idenfermeiro` int DEFAULT NULL,
  `idpulseria` int DEFAULT NULL,
  `tipo` enum('inicial','seguimento','alta') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `notasadicionais` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_registo` datetime DEFAULT NULL,
  `utilizador_id` int DEFAULT NULL,
  `pulseria_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idenfermeiro` (`idenfermeiro`),
  KEY `idpulseria` (`idpulseria`),
  KEY `utilizador_id` (`utilizador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `paciente`
--

DROP TABLE IF EXISTS `paciente`;
CREATE TABLE IF NOT EXISTS `paciente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nif` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` enum('M','F','Outro') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `morada` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idconsulta` int DEFAULT NULL,
  `medicamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dosagem` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `frequencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dataprescricao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idconsulta` (`idconsulta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pulseria`
--

DROP TABLE IF EXISTS `pulseria`;
CREATE TABLE IF NOT EXISTS `pulseria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prioridade` enum('Vermelha','Laranja','Amarela','Verde','Azul') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datapriorizacao` datetime DEFAULT NULL,
  `idpaciente` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idpaciente` (`idpaciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sinaisvitais`
--

DROP TABLE IF EXISTS `sinaisvitais`;
CREATE TABLE IF NOT EXISTS `sinaisvitais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idtriagem` int DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `pressao` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `frequenciacardiaca` int DEFAULT NULL,
  `frequenciarespiratoria` int DEFAULT NULL,
  `saturacaooxigenio` int DEFAULT NULL,
  `glicemia` int DEFAULT NULL,
  `data_registo` datetime DEFAULT NULL,
  `triagempulseria_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idtriagem` (`idtriagem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `triagem`
--

DROP TABLE IF EXISTS `triagem`;
CREATE TABLE IF NOT EXISTS `triagem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idenfermeiro` int DEFAULT NULL,
  `idpulseria` int DEFAULT NULL,
  `queixaprincipal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `discriminacaoprincipal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prioridadeatribuida` enum('Vermelha','Laranja','Amarela','Verde','Azul') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datatriagem` datetime DEFAULT NULL,
  `utilizador_id` int DEFAULT NULL,
  `paciente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idenfermeiro` (`idenfermeiro`),
  KEY `idpulseria` (`idpulseria`),
  KEY `utilizador_id` (`utilizador_id`),
  KEY `paciente_id` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `verification_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'admin', 'wF3wFkGpMxcqjhdrmS3WJvWNEdYB2WaT', '$2y$13$EIvoQKhEciV2r1hAF3AJauyr5nuyHYnJ7X/S9d9nV4WR4dYUxUWfG', NULL, 'admin@gmail.com', 10, 1761233560, 1761233560, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizador`
--

DROP TABLE IF EXISTS `utilizador`;
CREATE TABLE IF NOT EXISTS `utilizador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `paciente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `paciente_id` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `consulta_ibfk_1` FOREIGN KEY (`idmedico`) REFERENCES `utilizador` (`id`),
  ADD CONSTRAINT `consulta_ibfk_2` FOREIGN KEY (`idpulseria`) REFERENCES `pulseria` (`id`),
  ADD CONSTRAINT `consulta_ibfk_3` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizador` (`id`);

--
-- Limitadores para a tabela `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `diagnostico_ibfk_1` FOREIGN KEY (`idconsulta`) REFERENCES `consulta` (`id`);

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `notificacao_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`);

--
-- Limitadores para a tabela `observacao`
--
ALTER TABLE `observacao`
  ADD CONSTRAINT `observacao_ibfk_1` FOREIGN KEY (`idenfermeiro`) REFERENCES `utilizador` (`id`),
  ADD CONSTRAINT `observacao_ibfk_2` FOREIGN KEY (`idpulseria`) REFERENCES `pulseria` (`id`),
  ADD CONSTRAINT `observacao_ibfk_3` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizador` (`id`);

--
-- Limitadores para a tabela `prescricao`
--
ALTER TABLE `prescricao`
  ADD CONSTRAINT `prescricao_ibfk_1` FOREIGN KEY (`idconsulta`) REFERENCES `consulta` (`id`);

--
-- Limitadores para a tabela `pulseria`
--
ALTER TABLE `pulseria`
  ADD CONSTRAINT `pulseria_ibfk_1` FOREIGN KEY (`idpaciente`) REFERENCES `paciente` (`id`);

--
-- Limitadores para a tabela `sinaisvitais`
--
ALTER TABLE `sinaisvitais`
  ADD CONSTRAINT `sinaisvitais_ibfk_1` FOREIGN KEY (`idtriagem`) REFERENCES `triagem` (`id`);

--
-- Limitadores para a tabela `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `triagem_ibfk_1` FOREIGN KEY (`idenfermeiro`) REFERENCES `utilizador` (`id`),
  ADD CONSTRAINT `triagem_ibfk_2` FOREIGN KEY (`idpulseria`) REFERENCES `pulseria` (`id`),
  ADD CONSTRAINT `triagem_ibfk_3` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizador` (`id`),
  ADD CONSTRAINT `triagem_ibfk_4` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`);

--
-- Limitadores para a tabela `utilizador`
--
ALTER TABLE `utilizador`
  ADD CONSTRAINT `utilizador_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
