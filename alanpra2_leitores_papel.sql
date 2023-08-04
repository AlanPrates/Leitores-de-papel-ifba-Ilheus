-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 02-Ago-2023 às 00:39
-- Versão do servidor: 10.3.27-MariaDB
-- versão do PHP: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `alanpra2_leitores_papel`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin`
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
  `is_admin` tinyint(1) NOT NULL DEFAULT 1,
  `recuperar_senha` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `admin`
--

INSERT INTO `admin` (`id`, `admin_username`, `password`, `nome`, `email`, `categoria`, `datanascimento`, `sexo`, `telefone`, `matricula`, `is_admin`, `recuperar_senha`) VALUES
(1, 'alan', '$2y$10$KdQz8o9eVw8WEkckdI8zceW..7A7krcfGw34RVkCT0mrjhEUhrn/u', 'Alan Prates', 'alan_nz2@live.com', 'Funcionario', '2023-06-24', 'Masculino', '73991636180', '001', 1, NULL),
(4, 'daniel1990', '$2y$10$KmMWnMFVSidpMl6QxvU8me59hnbNFEtzvIIBMRf3SW6G8ugdCcrti', 'Daniel Monteiro', 'daniel_ios90@hotmail.com', 'aluno', '1990-09-15', 'masculino', '73981525797', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_emprestimos`
--

CREATE TABLE `historico_emprestimos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `data_emprestimo` datetime DEFAULT NULL,
  `data_devolucao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `historico_emprestimos`
--

INSERT INTO `historico_emprestimos` (`id`, `user_id`, `livro_id`, `data_emprestimo`, `data_devolucao`) VALUES
(152, 4, 12, '2023-07-10 23:27:01', '2023-07-25 23:27:01'),
(153, 1, 14, '2023-06-30 00:53:39', '2023-07-15 00:53:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `editora` varchar(100) NOT NULL,
  `ano_publicacao` int(11) NOT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor`, `editora`, `ano_publicacao`, `disponivel`, `quantidade`) VALUES
(12, 'bbc', 'bbc', 'bbc', 2001, 1, 94),
(13, 'CNN', 'CNN', 'CNN', 2025, 1, 24),
(14, 'GLOBONEWS', 'GLOBONEWS', 'GLOBONEWS', 2024, 1, 18);

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros_emprestados`
--

CREATE TABLE `livros_emprestados` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data_emprestimo` timestamp NULL DEFAULT current_timestamp(),
  `data_devolucao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `livros_emprestados`
--

INSERT INTO `livros_emprestados` (`id`, `livro_id`, `user_id`, `data_emprestimo`, `data_devolucao`) VALUES
(93, 14, 1, '2023-06-30 06:53:39', '2023-07-15 06:53:39'),
(94, 12, 4, '2023-07-11 05:27:01', '2023-07-26 05:27:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
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
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nome`, `email`, `categoria`, `matricula`, `datanascimento`, `sexo`, `telefone`, `recuperar_senha`) VALUES
(3, 'alan00', '$2y$10$JwtDLyItn9b7op6QhjfIa.XPFpfYhJfMGUBa2fPPd8xVmhzkfMESe', 'Alan Prates', 'alanpratesaps@gmail.com', 'aluno', '00026', '1989-07-10', 'masculino', '73991636180', NULL),
(4, 'alanj', '$2y$10$ge4puQFMPp6mHS2ejxndveUw0U9Sg47T4Z74tt6V7nJzRudJBguMq', 'Alan Prates S2', 'alan_nz@live.com', 'Aluno', '20039', '1989-07-10', 'Masculino', '73991636180', NULL),
(5, 'pedro', '$2y$10$4YT0SXh9grI0VrQcV5T7QuQtE4hzGTlt7i4ZD8PrlLhYWdlqP7HQi', 'pedro', 'pedro_nz@live.com', 'aluno', '00000', '1989-07-10', 'feminino', '7391636180', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `historico_emprestimos`
--
ALTER TABLE `historico_emprestimos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `livros_emprestados`
--
ALTER TABLE `livros_emprestados`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `historico_emprestimos`
--
ALTER TABLE `historico_emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `livros_emprestados`
--
ALTER TABLE `livros_emprestados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
