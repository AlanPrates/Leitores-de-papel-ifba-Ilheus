<?php

session_start();

global $conn;

include '../config/database.php';



// Verifica se o usuário não está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: ../public/index.php");

    exit;

}



// Verifica se o livro_id foi enviado via POST

if (isset($_POST['livro_id'])) {



    $livro_id = $_POST['livro_id'];

    $user_id = $_SESSION['user_id'];



    // Remove o livro emprestado do banco de dados

    $query = "DELETE FROM livros_emprestados WHERE livro_id = '$livro_id' AND user_id = '$user_id'";

    $result = $conn->query($query);



    if ($result) {
        $success_message = "Livro devolvido com sucesso!";
        $_SESSION['success_message'] = $success_message;
    } else {
        $error_message = "Erro ao devolver o livro.";
        $_SESSION['error_message'] = $error_message;
    }
} else {
    $error_message = "ID do livro não fornecido.";
    $_SESSION['error_message'] = $error_message;
}



// Redireciona de volta para a página de devolução de livros

$message = isset($success_message) ? $success_message : (isset($error_message) ? $error_message : '');
header("Location: ../admin/devolve_livro.php?message=" . urlencode($message));
exit;

