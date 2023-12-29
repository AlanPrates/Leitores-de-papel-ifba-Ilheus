<?php

session_start();

global $conn;

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livro_id = $_POST['livro_id'];
    $comentario = $_POST['comentario'];
    $avaliacao = $_POST['avaliacao'];

    // Certifique-se de que $user_id é um valor inteiro
    $user_id = (int) $_SESSION['user_id'];

    // Use prepared statements para evitar injeção de SQL
    $stmt = $conn->prepare("INSERT INTO comentarios (livro_id, user_id, comentario, avaliacao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $livro_id, $user_id, $comentario, $avaliacao);

    if ($stmt->execute()) {
        // Comentário adicionado com sucesso! Redireciona para devolver-livro.php
        header("Location: devolve_livro.php");
        exit; // Certifique-se de encerrar o script após o redirecionamento
    } else {
        echo "Erro ao adicionar o comentário: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Acesso inválido.";
}
?>

