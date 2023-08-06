<?php
session_start();
global $conn;
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['livro_id']) && isset($_POST['comentario']) && isset($_SESSION['user_id'])) {
        $livro_id = $_POST['livro_id'];
        $comentario = $_POST['comentario'];
        $user_id = $_SESSION['user_id'];

        // Escapar os valores de entrada para evitar SQL Injection
        $livro_id = $conn->real_escape_string($livro_id);
        $comentario = $conn->real_escape_string($comentario);
        $user_id = $conn->real_escape_string($user_id);

        // Consulta para inserir o comentário na tabela
        $query = "INSERT INTO comentarios (livro_id, comentario, user_id) VALUES ('$livro_id', '$comentario', '$user_id')";

        if ($conn->query($query)) {
            // Comentário adicionado com sucesso
            // Redirecionar de volta para a página devolve_livro.php
            header("Location: devolve_livro.php");
            exit;
        } else {
            // Se houver um erro ao inserir o comentário
            echo "Erro ao adicionar o comentário: " . $conn->error;
        }
    }
}
