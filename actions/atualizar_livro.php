<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem atualizar livros
require_admin('../public/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livro_id = (int)$_POST['livro_id'];
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $autor = isset($_POST['autor']) ? trim($_POST['autor']) : '';
    $ano_publicacao = isset($_POST['ano_publicacao']) ? trim($_POST['ano_publicacao']) : '';
    $isbn = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
    $genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
    if ($quantidade < 0) {
        $quantidade = 0;
    }
    $disponivel = ($quantidade > 0) ? 1 : 0;

    if ($livro_id <= 0 || empty($titulo)) {
        $_SESSION['error_message'] = "Dados inválidos para atualização do livro.";
        header("Location: ../admin/lista_livros.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE livros SET titulo = ?, autor = ?, ano_publicacao = ?, isbn = ?, genero = ?, disponivel = ?, quantidade = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sssssiii", $titulo, $autor, $ano_publicacao, $isbn, $genero, $disponivel, $quantidade, $livro_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Livro atualizado com sucesso.";
            header("Location: ../admin/lista_livros.php?livro_id=" . $livro_id . "&success=true");
            $stmt->close();
            exit;
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar livro.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erro ao preparar a atualização.";
    }

    header("Location: ../admin/lista_livros.php");
    exit;
} else {
    header("Location: ../admin/lista_livros.php");
    exit;
}
