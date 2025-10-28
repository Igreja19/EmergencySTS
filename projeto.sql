-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 28-Out-2025 às 13:52
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
('author', '2', 1761233604);

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
  `paciente_id` int NOT NULL,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `diagnostico_id` int DEFAULT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `tempo_consulta` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `relatorio_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_paciente_idx` (`paciente_id`),
  KEY `fk_consulta_utilizador_idx` (`userprofile_id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `consulta`
--

INSERT INTO `consulta` (`id`, `data_consulta`, `estado`, `prioridade`, `motivo`, `observacoes`, `paciente_id`, `userprofile_id`, `triagem_id`, `diagnostico_id`, `data_encerramento`, `tempo_consulta`, `relatorio_pdf`) VALUES
(3, '2025-10-25 18:00:00', 'Aberta', '', 'garganta', NULL, 1, 1, 2, NULL, NULL, NULL, NULL),
(4, '2025-10-25 11:11:00', 'Aberta', '', 'garganta', NULL, 2, 1, 3, NULL, NULL, NULL, NULL);

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
  `paciente_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_paciente1_idx` (`paciente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `notificacao`
--

INSERT INTO `notificacao` (`id`, `titulo`, `mensagem`, `tipo`, `dataenvio`, `lida`, `paciente_id`) VALUES
(6, NULL, 'O seu número A-247 será chamado em breve. Dirija-se à Sala B.', 'Consulta', '2025-10-27 15:19:36', 0, 1),
(7, NULL, 'A sua prioridade foi reavaliada de Verde para Amarela.', 'Prioridade', '2025-10-27 15:19:36', 0, 1),
(8, NULL, 'O tempo de espera estimado foi atualizado para aproximadamente 35 minutos.', 'Geral', '2025-10-27 15:19:36', 0, 1),
(9, NULL, 'A sua triagem foi concluída. Pulseira: A-247 - Prioridade Amarela.', 'Consulta', '2025-10-27 14:19:36', 1, 1),
(10, NULL, 'O seu formulário clínico foi recebido com sucesso.', 'Geral', '2025-10-27 13:19:36', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `paciente`
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
-- Extraindo dados da tabela `paciente`
--

INSERT INTO `paciente` (`id`, `nomecompleto`, `nif`, `datanascimento`, `sns`, `genero`, `telefone`, `email`, `morada`, `observacoes`) VALUES
(1, 'Miguel', '256776857', '2005-07-25', '234', 'Masculino', '912881282', 'miguelctobias@gmail.com', 'Leiria', 'Nenhuma'),
(2, 'Catia', '853', '2004-03-12', '123', 'Feminino', '987654321', 'catia@gmail.com', 'Leiria', 'Nenhuma');

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
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_prescricao_consulta1_idx` (`consulta_id`)
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
  `triagem_id` int NOT NULL,
  `paciente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pulseira_triagem1_idx` (`triagem_id`),
  KEY `paciente_id` (`paciente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `triagem`
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
-- Extraindo dados da tabela `triagem`
--

INSERT INTO `triagem` (`id`, `nomecompleto`, `datanascimento`, `sns`, `telefone`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `condicoes`, `alergias`, `medicacao`, `motivo`, `prioridadeatribuida`, `datatriagem`, `discriminacaoprincipal`, `paciente_id`, `utilizador_id`) VALUES
(1, 'Miguel', '2005-07-25', '2345678', '234567', 'garganta', 'Dor e tosse', 'Dor', '2025-10-25 17:00:00', 8, 'Dor', 'N', 'N', '', '', '2025-10-25 18:00:00', 'Dor', 0, 0),
(2, 'Miguel', '2005-07-25', '2345678', '234567', 'garganta', 'Dor e tosse', 'Dor', '2025-10-25 17:00:00', 8, 'Dor', 'N', 'N', '', '', '2025-10-25 18:00:00', 'Dor', 1, 0),
(3, 'Miguel', '2005-07-25', '256776857', '912881282', 'garganta', 'Dor', 'dor', '2025-07-25 11:11:00', 6, 'dor', 'nenhuma', 'nenhuma', '', '', '2025-10-25 11:11:00', 'Dor', 2, 0),
(4, 'Miguel', '2005-07-25', '256775857', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 3, 0),
(5, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 4, 0),
(6, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 5, 0),
(7, 'Afonso', '2005-07-25', '12346', '912881282', 'garganta', 'dor', 'dor', '2025-10-25 11:11:00', 8, 'dor', 'n', 'N', '', '', '2025-10-25 11:11:00', 'DOR', 6, 0);

--
-- Triggers `triagem`
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
        NEW.paciente_id,              -- paciente associado
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

    -- procura o paciente pelo nome
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'admin', 'wF3wFkGpMxcqjhdrmS3WJvWNEdYB2WaT', '$2y$13$EIvoQKhEciV2r1hAF3AJauyr5nuyHYnJ7X/S9d9nV4WR4dYUxUWfG', NULL, 'admin@gmail.com', 10, 1761233560, 1761233560, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `userprofile`
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
  ADD CONSTRAINT `fk_consulta_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `pulseira_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
