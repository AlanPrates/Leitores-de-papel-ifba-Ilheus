-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 10-Ago-2023 às 21:16
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
  `recuperar_senha` varchar(220) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `admin`
--

INSERT INTO `admin` (`id`, `admin_username`, `password`, `nome`, `email`, `categoria`, `datanascimento`, `sexo`, `telefone`, `matricula`, `recuperar_senha`, `is_admin`) VALUES
(1, 'alan', '$2y$10$vjrGdAVX4rSckwDf2q/v6uLSqN7l6kzwJfSuGZSUDJk5se5Nzy42W', 'Alan Prates', 'alan_nz2@live.com', 'Funcionario', '2023-06-24', 'Masculino', '73991636180', '001', NULL, 1),
(4, 'daniel1990', '$2y$10$KmMWnMFVSidpMl6QxvU8me59hnbNFEtzvIIBMRf3SW6G8ugdCcrti', 'Daniel Monteiro', 'daniel_ios90@hotmail.com', 'aluno', '1990-09-15', 'masculino', '73981525797', NULL, NULL, 1),
(5, 'maria', '$2y$10$4UH0G0mmx7kZhTTL18SGzeZcucMPQpv7bOWafDYIvKAmhxssPBZhS', 'Maria', 'alan_mz@live.com', 'funcionario', '1956-09-10', 'feminino', '73991936180', '2029', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `livro_id`, `user_id`, `comentario`) VALUES
(2, 17, 4, 'Livro otimo Ë†Ë† /:)'),
(3, 20, 4, '\"O CÃ³digo Limpo\" Ã© um livro indispensÃ¡vel para qualquer desenvolvedor que queira escrever cÃ³digo de alta qualidade. O livro fornece uma abordagem abrangente para escrever cÃ³digo que seja fÃ¡cil de ler, entender e manter.');

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
(154, 3, 13, '2023-08-04 01:44:44', '2023-08-19 01:44:44'),
(155, 4, 16, '2023-08-05 23:57:19', '2023-08-20 23:57:19'),
(156, 4, 14, '2023-08-05 23:58:16', '2023-08-20 23:58:16');

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
  `quantidade` int(11) NOT NULL,
  `quantidade_emprestada` int(11) NOT NULL DEFAULT 0,
  `quantidade_total` int(11) NOT NULL DEFAULT 0,
  `indisponivel` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor`, `editora`, `ano_publicacao`, `disponivel`, `quantidade`, `quantidade_emprestada`, `quantidade_total`, `indisponivel`) VALUES
(17, 'Arquitetura Limpa', 'Robert C. Martin', 'Pragmatic Programmers', 2012, 1, 11, 0, 0, 0),
(18, 'O Programador PragmÃ¡tico ', 'Andrew Hunt e David Thomas', 'Addison-Wesley Professional', 1999, 1, 20, 0, 0, 0),
(19, 'A Arte da ProgramaÃ§Ã£o', 'Erich Gamma, Richard Helm, Ralph Johnson e John Vlissides', 'Addison-Wesley Professional', 1995, 1, 8, 0, 0, 0),
(20, 'O CÃ³digo Limpo', 'Robert C. Martin', 'Pearson Education', 2008, 1, 29, 0, 0, 0),
(21, 'A Engenharia de Software', 'Roger S. Pressman', 'McGraw-Hill Education', 2019, 1, 11, 0, 0, 0);

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
(100, 13, 3, '2023-08-04 04:44:44', '2023-08-19 04:44:44'),
(150, 14, 3, '2023-08-06 02:58:58', '2023-08-21 02:58:58'),
(151, 16, 4, '2023-08-06 02:25:07', '2023-08-21 02:25:07'),
(154, 23, 4, '2023-08-06 06:33:13', '2023-08-21 06:33:13'),
(156, 24, 4, '2023-08-06 06:49:24', '2023-08-21 06:49:24'),
(157, 25, 4, '2023-08-06 07:09:59', '2023-08-21 07:09:59'),
(158, 26, 4, '2023-08-06 07:17:25', '2023-08-21 07:17:25'),
(159, 27, 4, '2023-08-06 07:22:27', '2023-08-21 07:22:27'),
(161, 30, 4, '2023-08-06 08:14:20', '2023-08-21 08:14:20'),
(162, 31, 4, '2023-08-06 08:33:30', '2023-08-21 08:33:30'),
(163, 32, 4, '2023-08-06 08:39:15', '2023-08-21 08:39:15'),
(166, 20, 4, '2023-08-06 08:59:54', '2023-08-21 08:59:54');

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
  `recuperar_senha` varchar(220) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nome`, `email`, `categoria`, `matricula`, `datanascimento`, `sexo`, `telefone`, `recuperar_senha`, `is_admin`) VALUES
(3, 'alan00', '$2y$10$JwtDLyItn9b7op6QhjfIa.XPFpfYhJfMGUBa2fPPd8xVmhzkfMESe', 'Alan Prates', 'alanpratesaps@gmail.com', 'aluno', '00026', '1989-07-10', 'masculino', '73991636180', NULL, 0),
(4, 'alanj', '$2y$10$vjrGdAVX4rSckwDf2q/v6uLSqN7l6kzwJfSuGZSUDJk5se5Nzy42W', 'Alan S2', 'alan_nz@live.com', 'Aluno', '20039', '1989-07-10', 'Masculino', '73991636180', NULL, 0),
(5, 'pedro', '$2y$10$4YT0SXh9grI0VrQcV5T7QuQtE4hzGTlt7i4ZD8PrlLhYWdlqP7HQi', 'pedro', 'pedro_nz@live.com', 'aluno', '00000', '1989-07-10', 'feminino', '7391636180', NULL, 0),
(6, 'andre', '$2y$10$hYE9Db4U.MoB1r0.pXJIN.iJqUO8PwR2ULqjxyBnl.FHqYdOL0YeG', 'André', 'andre_nz@live.com', 'Aluno', '2021', '1985-11-08', 'Masculino', '73991636180', NULL, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livro_id` (`livro_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `historico_emprestimos`
--
ALTER TABLE `historico_emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `livros_emprestados`
--
ALTER TABLE `livros_emprestados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
