<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem cadastrar livros
require_admin('../public/index.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = isset($_POST["titulo"]) ? trim($_POST["titulo"]) : '';
    $autor = isset($_POST["autor"]) ? trim($_POST["autor"]) : '';
    $ano = isset($_POST["ano"]) ? trim($_POST["ano"]) : '';
    $editora = isset($_POST["editora"]) ? trim($_POST["editora"]) : '';
    $quantidade = isset($_POST["quantidade"]) ? (int)$_POST["quantidade"] : 0;
    if ($quantidade < 0) {
        $quantidade = 0;
    }
    $isbn = isset($_POST["isbn"]) ? trim($_POST["isbn"]) : '';
    $genero = isset($_POST["genero"]) ? trim($_POST["genero"]) : '';

    if (empty($titulo)) {
        $_SESSION['error_message'] = "O título do livro é obrigatório.";
        header("Location: ../admin/cadastro_livro.php");
        exit;
    }

    $disponivel = ($quantidade > 0) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO livros (titulo, autor, ano_publicacao, editora, disponivel, quantidade, isbn, genero) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssiiss", $titulo, $autor, $ano, $editora, $disponivel, $quantidade, $isbn, $genero);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Livro cadastrado com sucesso.";
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar o livro.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erro ao preparar o cadastro do livro.";
    }

    header("Location: ../admin/cadastro_livro.php");
    exit;
} else {
    header("Location: ../admin/cadastro_livro.php");
    exit;
}
