<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem cadastrar livros
require_admin('../public/index.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = isset($_POST["titulo"]) ? trim($_POST["titulo"]) : '';
    $autor = isset($_POST["autor"]) ? trim($_POST["autor"]) : '';

    if (empty($titulo)) {
        $_SESSION['error_message'] = "Título é obrigatório.";
        header("Location: ../admin/cadastro_livro.php");
        exit;
    }

    $disponivel = 1;

    $stmt = $conn->prepare("INSERT INTO livros (titulo, autor, disponivel) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssi", $titulo, $autor, $disponivel);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Livro cadastrado com sucesso.";
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar o livro.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erro ao preparar cadastro.";
    }

    header("Location: ../admin/cadastro_livro.php");
    exit;
} else {
    header("Location: ../admin/cadastro_livro.php");
    exit;
}
