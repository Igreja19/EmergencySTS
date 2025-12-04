-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 04-Dez-2025 às 17:13
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
('enfermeiro', '18', 1763136190),
('medico', '13', 1763638950),
('medico', '15', 1763136196),
('paciente', '16', 1763135389),
('paciente', '20', 1763135051),
('paciente', '21', 1763135962),
('paciente', '22', 1763135978),
('paciente', '24', 1763136036),
('paciente', '25', 1763136144),
('paciente', '26', 1763312479),
('paciente', '27', 1763724679),
('paciente', '28', 1763724723),
('paciente', '29', 1763725618),
('paciente', '38', 1764848165);

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
  `data_encerramento` datetime DEFAULT NULL,
  `relatorio_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_triagem_idx` (`triagem_id`),
  KEY `fk_userprofile_consulta` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(31, 'Loratadina', '10mg', 'Rinite alérgica, urticária'),
(32, 'Salbutamol', '100mcg', 'Crise de asma, broncoespasmo'),
(33, 'Budesonida + Formoterol', '160/4.5mcg', 'Asma e DPOC'),
(34, 'Tramadol', '50mg', 'Dor moderada a intensa'),
(35, 'Codeína', '30mg', 'Dor moderada e tosse persistente'),
(36, 'Clonazepam', '2.5mg/mL', 'Ansiedade, epilepsia'),
(37, 'Diazepam', '10mg', 'Ansiedade, espasmos musculares'),
(38, 'Sertralina', '50mg', 'Depressão, ansiedade'),
(39, 'Sertralina', '100mg', 'Depressão, ansiedade'),
(40, 'Fluoxetina', '20mg', 'Depressão, ansiedade, compulsão alimentar');

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
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` enum('Consulta','Prioridade','Geral') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataenvio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `userprofile_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_userprofile_id` (`userprofile_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `notificacao`
--

INSERT INTO `notificacao` (`id`, `titulo`, `mensagem`, `tipo`, `dataenvio`, `lida`, `userprofile_id`) VALUES
(19, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente teste2.', 'Consulta', '2025-11-27 16:00:57', 0, 20),
(20, 'Triagem registada', 'Foi registada uma nova triagem para o paciente teste2.', 'Consulta', '2025-11-27 16:04:59', 0, 20),
(21, 'Prioridade Vermelho', 'O paciente teste2 encontra-se em prioridade Vermelho.', 'Prioridade', '2025-11-27 16:04:59', 0, 20),
(22, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente henrique3.', 'Consulta', '2025-11-27 16:56:56', 0, 10),
(23, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente henrique3.', 'Consulta', '2025-11-27 16:57:57', 0, 10),
(24, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente henrique.', 'Consulta', '2025-11-27 16:58:05', 0, 8),
(25, 'Pulseira atribuída', 'Foi criada uma nova pulseira pendente para o paciente zezoca.', 'Consulta', '2025-11-27 17:10:38', 0, 12);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescricao`
--

DROP TABLE IF EXISTS `prescricao`;
CREATE TABLE IF NOT EXISTS `prescricao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `observacoes` text NOT NULL,
  `dataprescricao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `consulta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_consulta_prescricao` (`consulta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pulseira`
--

INSERT INTO `pulseira` (`id`, `codigo`, `prioridade`, `status`, `tempoentrada`, `userprofile_id`) VALUES
(16, '735598F5', 'Vermelho', 'Em espera', '2025-11-27 16:00:57', 20),
(17, '35D18F02', 'Pendente', 'Em espera', '2025-11-27 16:56:56', 10),
(18, 'E3ADB0C8', 'Pendente', 'Em espera', '2025-11-27 16:57:57', 10),
(19, '617CB8EE', 'Pendente', 'Em espera', '2025-11-27 16:58:05', 8),
(20, '71CD4F12', 'Verde', 'Em espera', '2025-11-27 17:10:38', 12);

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
(24, '', '', '', NULL, 0, '', '', '2025-11-27 16:00:57', 20, 16),
(25, 'sdf', 'sdf', 'sdf', '2025-11-27 16:04:00', 5, 'sdf', 'sdf', '2025-11-27 16:04:00', 20, 16),
(26, '', '', '', NULL, 0, '', '', '2025-11-27 16:56:56', 10, 17),
(27, '', '', '', NULL, 0, '', '', '2025-11-27 16:57:57', 10, 18),
(28, '', '', '', NULL, 0, '', '', '2025-11-27 16:58:05', 8, 19),
(29, '', '', '', NULL, 0, '', '', '2025-11-27 17:10:38', 12, 20);

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
(18, 'zezoca', '5Bafu7LFi6mEO7F0RH7rLBcxEj0YhgMp', '$2y$13$01.YP4ozXB5DwglMWyfRFOqrQQpT6aDFvmdMpB2LVWhfAl02IQ2MW', NULL, 'zezoca@gmail.com', 10, 1762961282, 1762961299, NULL, 0),
(19, 'admin', '1eb4dvYH88w6nwTQQxOx8X4usCN5Vsx9', '$2y$13$c9RoUdyuZeDVhARmt/bLtOc73kvunKc1rFSn.O9.EZW2DtvniKOUi', NULL, 'admin@gmail.com', 10, 1762983420, 1762983426, NULL, 0),
(25, '12345', '069jWVY2Hf57qaZs7GVttH0C546yFHhr', '$2y$13$ZCoZAalg/kKJHluxdVOzsOJ01drMAzjy2a3f25KWUmYM2Ya8jONc6', NULL, '12345@gmail.com', 10, 1763136144, 1763136150, NULL, 0),
(26, 'teste2', 'swgEKZYTj4noVicIu0Gn9iULcVNsNeTJ', '$2y$13$LegFdmCEgQ5yL4kw7pgv4OJh.Xn2nY3ZWugAz8XgFUlPVxBdHStfq', NULL, 'teste@gmail.com', 10, 1763312479, 1763312485, NULL, 0),
(27, 'ola2@gmail.com', 'Y-qUHx2nCqaDQu0rxleO5EcS725__7mC', '$2y$13$GmI0U5WV8lbz7/0W71n6V.tyUf4P12MGBeVPLIeO63Y493s77eEga', NULL, 'ola2@gmail.com', 9, 1763724679, 1763724679, NULL, 1),
(28, 'adeus', '0WWI6J8aDsR2EyrAMJWaYZ90h2HiXh6z', '$2y$13$jo8yz97ypxvJsl1WBT6B3e/NibA5YydIayWoUsi1dXfdf8iN6nwKy', NULL, 'adeus@gmail.com', 10, 1763724723, 1763724731, NULL, 0),
(29, 'etapa1', 'MZQU4ZPuWK0wmVm9qWsGLuilc1bTTJxX', '$2y$13$AmPI9mrqg79mm9sGS1W1peFwGuxS1RDihg55.QHBis6GaNeb0YNKK', NULL, 'etapa1@gmail.com', 10, 1763725618, 1763725618, NULL, 1),
(38, 'teste4', 'if_KivTRbYHHa6QPMPjYepjMDHUz539h', '$2y$13$x.DME2O2orblwyWdvpNnquamLS1T4rAZgP9KUjJ5pIqqx2eqREz9C', NULL, 'teste4@gmail.com', 10, 1764848165, 1764848165, NULL, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userprofile`
--

INSERT INTO `userprofile` (`id`, `nome`, `email`, `morada`, `nif`, `sns`, `datanascimento`, `genero`, `telefone`, `user_id`) VALUES
(8, 'henrique', 'henrique@admin.com', 'Rua das Flores, nº72 2445-034', '234938493', '398493928', '2333-02-23', 'M', '915429512', 13),
(9, 'Henrique Salgado', 'henriquesalgado@gmail.com', 'Rua das Flores, nº72 2445-034', '483956185', '495284639', '2004-07-05', 'M', '915429512', 14),
(10, 'henrique3', 'henrique3@admin.com', 'Rua das Flores, nº72 2445-034', '234549264', '485429512', '2234-03-02', 'M', '915429512', 15),
(11, 'paciente', 'paciente@gmail.com', '1', '1', '1', '1111-01-01', 'M', '1', 16),
(12, 'zezoca', 'zezoca@gmail.com', 'rua', '123', '1234', '2025-11-11', 'M', '2343412313', 18),
(13, 'admin', 'admin@gmail.com', 'Leiria', '123', '123', '2005-07-25', 'M', '912881282', 19),
(19, '12345', '12345@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 25),
(20, 'teste2', 'teste@gmail.com', 's', 'sdf', 'sdf', '2005-04-13', 'M', 'sdf', 26),
(21, 'ola', 'ola2@gmail.com', 'leiria', '876', '463', '2025-11-21', 'M', '912881282', 27),
(22, 'adeus', 'adeus@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 28),
(23, 'etapa1', 'etapa1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 29),
(24, 'João Teste', 'teste4@gmail.com', NULL, '123476543', '987654321', '1999-05-10', 'M', '912345678', 38);

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
  ADD CONSTRAINT `consulta_ibfk_1` FOREIGN KEY (`triagem_id`) REFERENCES `triagem` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `consulta_ibfk_2` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `fk_prescricaoMed_prescricao` FOREIGN KEY (`prescricao_id`) REFERENCES `prescricao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `pulseira_ibfk_1` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `triagem`
--
ALTER TABLE `triagem`
  ADD CONSTRAINT `triagem_ibfk_1` FOREIGN KEY (`pulseira_id`) REFERENCES `pulseira` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `triagem_ibfk_2` FOREIGN KEY (`userprofile_id`) REFERENCES `userprofile` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `fk_userprofile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
