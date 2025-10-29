-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 29-Out-2025 às 14:36
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
  `item_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604),
('author', '10', 1761748330),
('author', '4', 1761736523),
('author', '9', 1761737418);

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
('author', 1, NULL, NULL, NULL, 1761233604, 1761233604),
('createPost', 2, 'Create a post', NULL, NULL, 1761233604, 1761233604),
('updatePost', 2, 'Update post', NULL, NULL, 1761233604, 1761233604);

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
('admin', 'author'),
('author', 'createPost'),
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
  `prioridade` enum('Vermelho','Laranja','Amarelo','Verde','Azul') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `prescricao_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `tempo_consulta` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `relatorio_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_utilizador_idx` (`userprofile_id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_prescricao_id` (`prescricao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `medicamento` varchar(100) NOT NULL,
  `dosagem` varchar(100) NOT NULL,
  `frequencia` varchar(100) NOT NULL,
  `observacoes` text NOT NULL,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pulseira`
--

DROP TABLE IF EXISTS `pulseira`;
CREATE TABLE IF NOT EXISTS `pulseira` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `prioridade` enum('Vermelho','Laranja','Amarelo','Verde','Azul') NOT NULL,
  `status` enum('Aguardando','Em atendimento','Atendido') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Aguardando',
  `tempoentrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

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
  `motivo` text NOT NULL,
  `datatriagem` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userprofile_id` int NOT NULL,
  `pulseira_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pulseira_id` (`pulseira_id`),
  KEY `fk_triagem_userprofile_id` (`userprofile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Acionadores `triagem`
--
DROP TRIGGER IF EXISTS `trg_after_triagem_insert`;
DELIMITER $$
CREATE TRIGGER `trg_after_triagem_insert` AFTER INSERT ON `triagem` FOR EACH ROW BEGIN
    INSERT INTO consulta (
        data_consulta,
        estado,
        prioridade,
        motivo,
        paciente_id,
        userprofile_id,
        triagem_id
    )
    VALUES (
        NEW.datatriagem,              -- vem da triagem
        'Aberta',                     -- estado inicial
        NEW.prioridadeatribuida,      -- prioridade da triagem
        NEW.motivoconsulta,           -- motivo
        NEW.paciente_id,              -- user-profile associado
        CASE
            WHEN NEW.utilizador_id = 0 THEN 1  -- se não houver utilizador, usa admin
            ELSE NEW.utilizador_id
        END,
        NEW.id                        -- triagem → consulta
    );
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_triagem_set_paciente`;
DELIMITER $$
CREATE TRIGGER `trg_triagem_set_paciente` BEFORE INSERT ON `triagem` FOR EACH ROW BEGIN
    DECLARE v_paciente_id INT;

    -- procura o user-profile pelo nome
    SELECT id INTO v_paciente_id
    FROM paciente
    WHERE nomecompleto = NEW.nomecompleto
    LIMIT 1;

    -- se encontrar, atualiza o paciente_id automaticamente
    IF v_paciente_id IS NOT NULL THEN
        SET NEW.paciente_id = v_paciente_id;
    ELSE
        -- se não encontrar, define paciente_id como NULL ou 0
        SET NEW.paciente_id = NULL;
    END IF;
END
$$
DELIMITER ;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'admin', 'wF3wFkGpMxcqjhdrmS3WJvWNEdYB2WaT', '$2y$13$EIvoQKhEciV2r1hAF3AJauyr5nuyHYnJ7X/S9d9nV4WR4dYUxUWfG', NULL, 'admin@gmail.com', 10, 1761233560, 1761233560, NULL),
(10, 'henrique', 'KYauC7LSedyBCgnLURqZKgMimZlgdZ7M', '$2y$13$UCMDXzqQYxsMNmbpXTfoV.d7IGxyTGMtqBBi/XnzNCC24vDBNX5pW', NULL, 'henrique@admin.com', 10, 1761748330, 1761748330, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `userprofile`
--

DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE IF NOT EXISTS `userprofile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nif` varchar(9) NOT NULL,
  `sns` varchar(9) NOT NULL,
  `datanascimento` date NOT NULL,
  `genero` char(1) NOT NULL,
  `telefone` varchar(30) NOT NULL,
  `consulta_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_utilizador_consulta1_idx` (`consulta_id`),
  KEY `fk_utilizador_triagem1_idx` (`triagem_id`),
  KEY `fk_userprofile_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `consulta_id`, `triagem_id`, `user_id`) VALUES
(6, 'henrique', 'henrique@admin.com', '', '', '0000-00-00', '', '', 0, 0, 10);

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
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescricao_id` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `fk_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
