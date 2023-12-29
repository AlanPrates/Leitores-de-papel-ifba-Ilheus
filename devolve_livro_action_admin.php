<?php

session_start();

global $conn;

include 'conexao.php';



// Verifica se o usuário não está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: login_admin.php");

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

    } else {

        $error_message = "Erro ao devolver o livro.";

    }

} else {

    $error_message = "ID do livro não fornecido.";

}



// Redireciona de volta para a página de devolução de livros

header("Location: devolve_livro_admin.php?message=" . urlencode($success_message ?? $error_message));

exit;

