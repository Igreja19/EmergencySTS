-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 29-Dez-2025 às 09:14
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
  `item_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1761233604),
('admin', '19', 1762983475),
('enfermeiro', '13', 1763136201),
('enfermeiro', '18', 1763136190),
('medico', '15', 1763136196),
('medico', '38', 1766589157),
('medico', '39', 1766583164),
('medico', '40', 1766587286),
('medico', '45', 1766587870),
('paciente', '16', 1763135389),
('paciente', '20', 1763135051),
('paciente', '21', 1763135962),
('paciente', '22', 1763135978),
('paciente', '24', 1763136036),
('paciente', '25', 1763136144),
('paciente', '26', 1763312479),
('paciente', '27', 1764160961),
('paciente', '28', 1766587218),
('paciente', '29', 1764162766),
('paciente', '30', 1764163099),
('paciente', '31', 1764163216),
('paciente', '32', 1764163663),
('paciente', '33', 1764163738),
('paciente', '34', 1764164327),
('paciente', '35', 1764164544),
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
  `estado` enum('Aberta','Encerrada','Em curso') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aberta',
  `observacoes` text COLLATE utf8mb4_general_ci,
  `userprofile_id` int NOT NULL,
  `triagem_id` int NOT NULL,
  `medicouserprofile_id` int NOT NULL,
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `consulta`
--

INSERT INTO `consulta` (`id`, `data_consulta`, `estado`, `observacoes`, `userprofile_id`, `triagem_id`, `medicouserprofile_id`, `data_encerramento`, `relatorio_pdf`) VALUES
(19, '2025-12-26 11:28:51', 'Encerrada', '', 22, 36, 13, '2025-12-28 11:34:59', NULL),
(21, '2025-12-28 11:37:54', 'Em curso', NULL, 30, 39, 13, NULL, NULL),
(22, '2025-12-28 11:56:17', 'Em curso', '', 11, 40, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_history`
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(34, 19, '2025-12-24 15:15:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0');

-- --------------------------------------------------------

--
-- Estrutura da tabela `medicamento`
--

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `dosagem` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `indicacao` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `version` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
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
  `titulo` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `mensagem` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tipo` enum('Consulta','Prioridade','Geral') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Geral',
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_userprofile_id` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(63, 'Consulta encerrada', 'A sua consulta foi encerrada.', 'Consulta', '2025-12-04 17:11:12', 0, 8),
(64, 'Utilizador ativado', 'A conta paciente4 foi ativada.', 'Geral', '2025-12-19 10:49:20', 0, 13),
(65, 'Novo utilizador criado', 'Foi criada uma nova conta: medico2', 'Geral', '2025-12-24 13:32:44', 0, 13),
(66, 'Novo utilizador criado', 'Foi criada uma nova conta: medico3', 'Geral', '2025-12-24 14:41:08', 0, 13),
(67, 'Novo utilizador criado', 'Foi criada uma nova conta: medico3', 'Geral', '2025-12-24 14:51:10', 0, 13),
(68, 'Consulta eliminada', 'A \'Consulta #17\' foi apagada do histórico.', 'Geral', '2025-12-24 15:10:13', 0, 13),
(69, 'Consulta eliminada', 'A \'Consulta #16\' foi apagada do histórico.', 'Geral', '2025-12-24 15:10:15', 0, 13),
(70, 'Consulta eliminada', 'A \'Consulta #18\' foi apagada do histórico.', 'Geral', '2025-12-28 11:36:05', 0, 13),
(71, 'Consulta eliminada', 'A \'Consulta #20\' foi apagada do histórico.', 'Geral', '2025-12-28 11:42:56', 0, 13);

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `prescricao`
--

INSERT INTO `prescricao` (`id`, `observacoes`, `dataprescricao`, `consulta_id`) VALUES
(17, '', '2025-12-28 11:34:46', 19),
(18, '', '2025-12-28 11:56:28', 22);

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `prescricaomedicamento`
--

INSERT INTO `prescricaomedicamento` (`id`, `posologia`, `prescricao_id`, `medicamento_id`) VALUES
(17, '4 c', 17, 3),
(18, '2 c', 18, 4),
(19, '1 c', 18, 5);

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(22, '19C5F289', 'Verde', 'Atendido', '2025-12-24 12:51:56', 22),
(24, '8B9E30F9', 'Azul', 'Em atendimento', '2025-12-24 15:13:26', 30),
(25, '792858F5', 'Laranja', 'Em atendimento', '2025-12-28 11:55:46', 11);

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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `triagem`
--

INSERT INTO `triagem` (`id`, `motivoconsulta`, `queixaprincipal`, `descricaosintomas`, `iniciosintomas`, `intensidadedor`, `alergias`, `medicacao`, `datatriagem`, `userprofile_id`, `pulseira_id`) VALUES
(36, 'teste2', 'teste2', 'teste2', '2025-12-24 12:51:00', 3, 'teste2', 'teste2', '2025-12-24 12:51:56', 22, 22),
(39, 'teste4', 'teste4', 'teste4', '2025-12-24 15:13:00', 0, 'teste4', 'teste4', '2025-12-24 15:13:26', 30, 24),
(40, 'Dor no Queixo', 'teste', 'teste', '2025-12-28 11:55:00', 1, 'tsetes', 'testse', '2025-12-28 11:55:46', 11, 25),
(41, 'Dor no Queixo', 'teste', 'teste', '2025-12-28 11:55:00', 1, 'tsetes', 'testse', '2025-12-28 11:56:01', 11, 25);

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
(28, 'paciente3', '_iifqcDX744sz7WQLO3E0sk-URNQQ2jC', '$2y$13$IvT3881ahISiO0Gt0RBUaOoPKTTbHN/Odu1NiN6A0sGU/jxYgMAp2', NULL, 'paciente3@gmail.com', 10, 1764161031, 1766587218, NULL, 0),
(36, 'paciente4', 'HYOTJpUaNFBVADfceoYCrtq5IocoA2aD', '$2y$13$cF4JtAIhv/2rfDnr62fdRekzJ8ekjuot0b0SRWuFD/KWZ/2iJ9b3i', NULL, 'paciente4@gmail.com', 10, 1764165030, 1766586510, NULL, 0),
(37, 'henrique4', 'x7yW-zYrwhIyp_i92W4VZBwGMeiWRRed', '$2y$13$B2Ctd3q4V2cq9jpD9vTm5eSAAXCku7c1GF4d7rVHxMQiPj5QGt02a', NULL, 'henrique4@gmail.com', 10, 1764762374, 1764762380, NULL, 0),
(38, 'Medico', 'dKJiVpmve8TbqFjTBDa7exaWdz3kKIlv', '$2y$13$d.dclh3Z6uB10tVyRfOFQOMRzZ4NT1lmStf4t6Vx.9PK6EAJaDQMy', NULL, 'medico@gmail.com', 10, 1764868352, 1766589157, NULL, 1),
(39, 'medico2', 'Wyqc-o7n9ox5BwE10Y0CmjtojJmwtJZ-', '$2y$13$oA/bD9qQo0Jyp/ti8N1JoOnW4f.6N1Hnnb73Nr6FIlfXnmfkICv0u', NULL, 'medico2@gmail.com', 10, 1766583164, 1766583164, NULL, 1),
(45, 'medico3', 'sQVFJGHyc_N_e2uWKKnZ8m8i4qvOBv3T', '$2y$13$t1e9Ug5Ur39AFs5FXARRKewWGQviRyXT1hvPDeV9NlEYBm8KYPAEm', NULL, 'medico3@gmail.com', 10, 1766587870, 1766587870, NULL, 1);

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
  `estado` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_userprofile_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`, `estado`) VALUES
(8, 'henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '2333-02-23', 'M', '915429512', 13, 1),
(9, 'Henrique Salgado', 'henriquesalgado@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14, 1),
(10, 'henrique3', 'henrique3@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '2234-03-02', 'M', '915429512', 15, 1),
(11, 'paciente', 'paciente@gmail.com', 'Rua das Flores, nº72 2445-034', '987654567', '098765456', '2005-02-02', 'M', '929956648', 16, 1),
(12, 'zezoca', 'zezoca@gmail.com', 'rua', '123', '1234', '2025-11-11', 'M', '2343412313', 18, 1),
(13, 'admin', 'admin@gmail.com', 'Leiria', '232', '123', '2005-07-25', 'M', '912881282', 19, 1),
(19, '12345', '12345@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 25, 1),
(20, 'teste2', 'teste@gmail.com', 's', 'sdf', 'sdf', '2005-04-13', 'M', 'sdf', 26, 1),
(21, 'paciente2', 'paciente2@gmail.com', 'Rua das Flores, nº72 2445-034', '648549245', '854926135', '2025-11-26', 'F', '915429512', 27, 1),
(22, 'paciente3', 'paciente3@gmail.com', 'Rua das Flores, nº72 2445-034', '548659135', '584965821', '2025-11-26', 'M', '915429512', 28, 1),
(30, 'paciente4', 'paciente4@gmail.com', 'Rua das Flores, nº72 2445-034', '858372838', '377823737', '2025-11-26', 'M', '915429512', 36, 0),
(31, 'henrique4', 'henrique4@gmail.com', 'Rua das Flores, nº72 2445-034', '234324324', '234234324', '2025-12-03', 'F', '915429512', 37, 1),
(32, 'Medico', 'medico@gmail.com', 'Rua das Flores, nº72 2445-034', '959595955', '259292929', '2025-12-04', 'M', '964586959', 38, 1),
(33, 'medico2', 'medico2@gmail.com', 'Rua das Flores, nº72 2445-034', '987654567', '876543456', '2006-06-06', 'M', '912881283', 39, 1),
(35, 'medico3', 'medico3@gmail.com', 'Rua das Flores, nº72 2445-034', '456789098', '876543456', '2004-07-07', 'M', '915429748', 45, 1);

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
