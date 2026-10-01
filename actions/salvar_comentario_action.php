<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_login('../public/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livro_id = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
    $avaliacao = isset($_POST['avaliacao']) ? trim($_POST['avaliacao']) : '';
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($livro_id <= 0 || $user_id <= 0 || empty($comentario)) {
        $_SESSION['error_message'] = "Dados inválidos para registrar comentário.";
        header("Location: ../user/devolve_livro.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO comentarios (livro_id, user_id, comentario, avaliacao) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiss", $livro_id, $user_id, $comentario, $avaliacao);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Comentário registrado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar comentário.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erro ao preparar o comentário.";
    }

    header("Location: ../user/devolve_livro.php");
    exit;
} else {
    header("Location: ../user/devolve_livro.php");
    exit;
}
