-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 27-Out-2025 às 21:11
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

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pulseira`
--
ALTER TABLE `pulseira`
  ADD CONSTRAINT `pulseira_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
