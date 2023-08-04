-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 04, 2023 at 11:24 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biblioteca`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `password` varchar(512) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `categoria` varchar(20) DEFAULT NULL,
  `datanascimento` date DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `matricula` varchar(20) DEFAULT NULL,
  `recuperar_senha` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_username`, `password`, `nome`, `email`, `categoria`, `datanascimento`, `sexo`, `telefone`, `matricula`, `recuperar_senha`) VALUES
(1, 'alan', '$2y$10$inzboJI.o2afRqhoOXwRIe5B.4bbFfvkHuR0E2MhNlwFasVwbe7P2', 'Alan Prates', 'alan_nz2@live.com', 'Funcionario', '2023-06-24', 'Masculino', '73991636180', '001', NULL),
(4, 'daniel1990', '$2y$10$KmMWnMFVSidpMl6QxvU8me59hnbNFEtzvIIBMRf3SW6G8ugdCcrti', 'Daniel Monteiro', 'daniel_ios90@hotmail.com', 'aluno', '1990-09-15', 'masculino', '73981525797', NULL, NULL),
(5, 'maria', '$2y$10$4UH0G0mmx7kZhTTL18SGzeZcucMPQpv7bOWafDYIvKAmhxssPBZhS', 'Maria', 'alan_mz@live.com', 'funcionario', '1956-09-10', 'feminino', '73991936180', '2029', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `historico_emprestimos`
--

CREATE TABLE `historico_emprestimos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `data_emprestimo` datetime DEFAULT NULL,
  `data_devolucao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `historico_emprestimos`
--

INSERT INTO `historico_emprestimos` (`id`, `user_id`, `livro_id`, `data_emprestimo`, `data_devolucao`) VALUES
(152, 4, 12, '2023-07-10 23:27:01', '2023-07-25 23:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `editora` varchar(100) NOT NULL,
  `ano_publicacao` int(11) NOT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT '1',
  `quantidade` int(11) NOT NULL,
  `quantidade_emprestada` int(11) NOT NULL DEFAULT '0',
  `quantidade_total` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor`, `editora`, `ano_publicacao`, `disponivel`, `quantidade`, `quantidade_emprestada`, `quantidade_total`) VALUES
(12, 'bbc', 'bbc', 'bbc', 2001, 1, 105, -13, 0),
(13, 'CNN', 'CNN', 'CNN', 2025, 1, 240, 0, 0),
(14, 'GLOBONEWS', 'GLOBONEWS', 'GLOBONEWS', 2024, 1, 16, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `livros_emprestados`
--

CREATE TABLE `livros_emprestados` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data_emprestimo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_devolucao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `livros_emprestados`
--

INSERT INTO `livros_emprestados` (`id`, `livro_id`, `user_id`, `data_emprestimo`, `data_devolucao`) VALUES
(100, 13, 3, '2023-08-04 04:44:44', '2023-08-19 04:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(512) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `categoria` varchar(20) DEFAULT NULL,
  `matricula` varchar(50) DEFAULT NULL,
  `datanascimento` date DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `recuperar_senha` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nome`, `email`, `categoria`, `matricula`, `datanascimento`, `sexo`, `telefone`, `recuperar_senha`) VALUES
(3, 'alan00', '$2y$10$JwtDLyItn9b7op6QhjfIa.XPFpfYhJfMGUBa2fPPd8xVmhzkfMESe', 'Alan Prates', 'alanpratesaps@gmail.com', 'aluno', '00026', '1989-07-10', 'masculino', '73991636180', NULL),
(4, 'alanj', '$2y$10$vjrGdAVX4rSckwDf2q/v6uLSqN7l6kzwJfSuGZSUDJk5se5Nzy42W', 'Alan Prates S2', 'alan_nz@live.com', 'Aluno', '20039', '1989-07-10', 'Masculino', '73991636180', NULL),
(5, 'pedro', '$2y$10$4YT0SXh9grI0VrQcV5T7QuQtE4hzGTlt7i4ZD8PrlLhYWdlqP7HQi', 'pedro', 'pedro_nz@live.com', 'aluno', '00000', '1989-07-10', 'feminino', '7391636180', NULL),
(6, 'andre', '$2y$10$hYE9Db4U.MoB1r0.pXJIN.iJqUO8PwR2ULqjxyBnl.FHqYdOL0YeG', 'André', 'andre_nz@live.com', 'Aluno', '2021', '1985-11-08', 'Masculino', '73991636180', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `historico_emprestimos`
--
ALTER TABLE `historico_emprestimos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `livros_emprestados`
--
ALTER TABLE `livros_emprestados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `historico_emprestimos`
--
ALTER TABLE `historico_emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `livros_emprestados`
--
ALTER TABLE `livros_emprestados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
