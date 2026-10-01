<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem excluir livros
require_admin('../public/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livro_id = (int)$_POST['livro_id'];

    if ($livro_id <= 0) {
        $_SESSION['error_message'] = 'ID do livro inválido.';
        header("Location: lista_livros.php");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $livro_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Livro excluído com sucesso.';
        } else {
            $_SESSION['error_message'] = 'Ocorreu um erro ao excluir o livro.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'Erro ao preparar a exclusão.';
    }

    header("Location: lista_livros.php");
    exit;
} else {
    header("Location: lista_livros.php");
    exit;
}
