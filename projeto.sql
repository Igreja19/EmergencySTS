-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 04-Dez-2025 às 17:18
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
('admin', '1', 1761233604),
('admin', '19', 1762983475),
('enfermeiro', '13', 1763136201),
('enfermeiro', '18', 1763136190),
('medico', '15', 1763136196),
('medico', '38', 1764868352),
('paciente', '16', 1763135389),
('paciente', '20', 1763135051),
('paciente', '21', 1763135962),
('paciente', '22', 1763135978),
('paciente', '24', 1763136036),
('paciente', '25', 1763136144),
('paciente', '26', 1763312479),
('paciente', '27', 1764160961),
('paciente', '28', 1764161031),
('paciente', '29', 1764162766),
('paciente', '30', 1764163099),
('paciente', '31', 1764163216),
('paciente', '32', 1764163663),
('paciente', '33', 1764163738),
('paciente', '34', 1764164327),
('paciente', '35', 1764164544),
('paciente', '36', 1764165030),
('paciente', '37', 1764762374);

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
('paciente', 1, 'Paciente do sistema', NULL, NULL, 1763135051, 1763135051),
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
  `estado` enum('Aberta','Encerrada','Em curso') NOT NULL DEFAULT 'Aberta',
  `observacoes` text,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `medicouserprofile_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `consulta`
--

INSERT INTO `consulta` (`id`, `data_consulta`, `estado`, `observacoes`, `userprofile_id`, `triagem_id`, `medicouserprofile_id`, `data_encerramento`, `relatorio_pdf`) VALUES
(16, '2025-12-04 16:25:20', 'Encerrada', '', 22, 21, 10, '2025-12-04 17:07:34', NULL),
(17, '2025-12-04 17:10:54', 'Encerrada', '', 8, 28, 10, '2025-12-04 17:11:12', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_history`
--

DROP TABLE IF EXISTS `login_history`;
CREATE TABLE IF NOT EXISTS `login_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `data_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `login_history`
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
(12, 19, '2025-12-04 17:13:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0');

-- --------------------------------------------------------

--
-- Estrutura da tabela `medicamento`
--

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `dosagem` varchar(255) NOT NULL,
  `indicacao` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `medicamento`
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
-- Estrutura da tabela `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `titulo` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `mensagem` text CHARACTER SET latin1 NOT NULL,
  `tipo` enum('Consulta','Prioridade','Geral') CHARACTER SET latin1 NOT NULL DEFAULT 'Geral',
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_userprofile_id` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `notificacao`
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
(63, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-04 17:11:12', 0, 8);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `observacoes` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_prescricao` (`consulta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `prescricao`
--

INSERT INTO `prescricao` (`id`, `observacoes`, `dataprescricao`, `consulta_id`) VALUES
(15, '', '2025-12-04 16:48:04', 16),
(16, '', '2025-12-04 17:11:02', 17);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescricaomedicamento`
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `prescricaomedicamento`
--

INSERT INTO `prescricaomedicamento` (`id`, `posologia`, `prescricao_id`, `medicamento_id`) VALUES
(14, '1 c', 15, 2),
(15, '2 c', 15, 3),
(16, '.05 c', 16, 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pulseira`
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(5, '97A510BD', 'Vermelho', 'Em atendimento', '2025-10-30 15:07:38', 9),
(11, '4EBFB37A', 'Azul', 'Atendido', '2025-11-26 12:49:14', 22),
(13, '4F7F38E1', 'Amarelo', 'Atendido', '2025-11-26 22:49:51', 30),
(16, 'E134DFFC', 'Azul', 'Atendido', '2025-12-04 17:08:42', 8),
(17, '534D78DF', 'Pendente', 'Em espera', '2025-12-04 17:12:54', 11);

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `triagem`
--

INSERT INTO `triagem` (`id`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `alergias`, `medicacao`, `datatriagem`, `userprofile_id`, `pulseira_id`) VALUES
(12, 'gfhfgh', 'fghfh', 'dghfh', '4334-03-12 03:23:00', 10, 'efsg', 'dfg', '2025-10-30 15:07:38', 9, 5),
(21, 'triagem3', 'triagem3', 'triagem3', '2025-11-26 12:47:00', 3, 'triagem3', 'triagem3', '2025-11-26 12:49:14', 22, 11),
(26, 'teste', 'triagem2', 'tESTTESDF', '2025-12-02 14:30:00', 6, 'TEFSADF', 'SDFSDFS', '2025-12-02 14:30:00', 30, 13),
(28, 'teste', 'teste', 'teste', '2025-12-04 17:08:00', 0, 'teste', 'teste', '2025-12-04 17:08:42', 8, 16),
(29, 'teste', 'teste', 'teste', '2025-12-04 17:12:00', 4, 'teste', 'teste', '2025-12-04 17:12:54', 11, 17);

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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `user`
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
(26, 'teste2', 'swgEKZYTj4noVicIu0Gn9iULcVNsNeTJ', '$2y$13$LegFdmCEgQ5yL4kw7pgv4OJh.Xn2nY3ZWugAz8XgFUlPVxBdHStfq', NULL, 'teste@gmail.com', 10, 1763312479, 1763312485, NULL, 0),
(27, 'paciente2', 'wwWgciererk9OapR5MPcGqrWO_3On8EG', '$2y$13$99Rr4ZHLqQaRJzKPk0V0bulwAxXh9TU4Bgu.VvSf6wrGleQv9.oqe', NULL, 'paciente2@gmail.com', 10, 1764160961, 1764160965, NULL, 0),
(28, 'paciente3', 'BwOKJpuuVOqCRfQRFdjfFCDcXH4d5MTr', '$2y$13$9/hURE33k4ZIitqO5vj.I.kjWsWBqi8l5jCc7M3A1HuFlnc9H2RuC', NULL, 'paciente3@gmail.com', 10, 1764161031, 1764161046, NULL, 0),
(36, 'paciente4', 'p_TacdvrftsvqT-9zTZWpUWtaAmLltJK', '$2y$13$qeTenBbGo5VvvOmhDZQdFeawz7YrCgSFVWhAC.kpR5BxyXcpcMkle', NULL, 'paciente4@gmail.com', 10, 1764165030, 1764165036, NULL, 0),
(37, 'henrique4', 'x7yW-zYrwhIyp_i92W4VZBwGMeiWRRed', '$2y$13$B2Ctd3q4V2cq9jpD9vTm5eSAAXCku7c1GF4d7rVHxMQiPj5QGt02a', NULL, 'henrique4@gmail.com', 10, 1764762374, 1764762380, NULL, 0),
(38, 'medico@gmail.com', 'qdDTn-uLh6qxj-7GI997yyh9YY3HyjYs', '$2y$13$5oSojQr2W3W3oMK4uSeTA.YDVmP8x8yRbDVuJ15BrjAF/j1hJAUCi', NULL, 'medico@gmail.com', 9, 1764868352, 1764868352, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `userprofile`
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`) VALUES
(8, 'henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '2333-02-23', 'M', '915429512', 13),
(9, 'Henrique Salgado', 'henriquesalgado@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14),
(10, 'henrique3', 'henrique3@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '2234-03-02', 'M', '915429512', 15),
(11, 'paciente', 'paciente@gmail.com', '1', '1', '1', '1111-01-01', 'M', '1', 16),
(12, 'zezoca', 'zezoca@gmail.com', 'rua', '123', '1234', '2025-11-11', 'M', '2343412313', 18),
(13, 'admin', 'admin@gmail.com', 'Leiria', '232', '123', '2005-07-25', 'M', '912881282', 19),
(19, '12345', '12345@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 25),
(20, 'teste2', 'teste@gmail.com', 's', 'sdf', 'sdf', '2005-04-13', 'M', 'sdf', 26),
(21, 'paciente2', 'paciente2@gmail.com', 'Rua das Flores, nº72 2445-034', '648549245', '854926135', '2025-11-26', 'F', '915429512', 27),
(22, 'paciente3', 'paciente3@gmail.com', 'Rua das Flores, nº72 2445-034', '548659135', '584965821', '2025-11-26', 'M', '915429512', 28),
(30, 'paciente4', 'paciente4@gmail.com', 'Rua das Flores, nº72 2445-034', '858372838', '377823737', '2025-11-26', 'M', '915429512', 36),
(31, 'henrique4', 'henrique4@gmail.com', 'Rua das Flores, nº72 2445-034', '234324324', '234234324', '2025-12-03', 'F', '915429512', 37),
(32, 'Medico', 'medico@gmail.com', 'Rua das Flores, nº72 2445-034', '959595955', '259292929', '2025-12-04', 'M', '964586959', 38);

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
  ADD CONSTRAINT `fk_consulta_triagem` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_utilizador` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `fk_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Limitadores para a tabela `prescricao`
--
ALTER TABLE `prescricao`
  ADD CONSTRAINT `prescricao_ibfk_1` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `prescricaomedicamento`
--
ALTER TABLE `prescricaomedicamento`
  ADD CONSTRAINT `fk_prescricaoMed_medicamento` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamento` (`id`),
  ADD CONSTRAINT `fk_prescricaoMed_prescricao` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `fk_userprofile_pulseira` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Limitadores para a tabela `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `fk_pulseira_id` FOREIGN KEY (`pulseira_id`) REFERENCES `pulseira` (`id`),
  ADD CONSTRAINT `fk_triagem_userprofile_id` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`);

--
-- Limitadores para a tabela `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
