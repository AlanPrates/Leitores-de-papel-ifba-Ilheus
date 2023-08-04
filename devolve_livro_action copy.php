<?php
session_start();
global $conn;
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se o livro_id foi enviado via POST
if (isset($_POST['livro_id'])) {

    $livro_id = $_POST['livro_id'];
    $user_id = $_SESSION['user_id'];

    // Verifica se o livro está emprestado para o usuário antes de devolver
    $query_check_emprestado = "SELECT * FROM livros_emprestados WHERE livro_id = '$livro_id' AND user_id = '$user_id'";
    $result_check_emprestado = $conn->query($query_check_emprestado);

    if ($result_check_emprestado && $result_check_emprestado->num_rows > 0) {
        // Remove o livro emprestado do banco de dados
        $query_remove_emprestado = "DELETE FROM livros_emprestados WHERE livro_id = '$livro_id' AND user_id = '$user_id'";
        $result_remove_emprestado = $conn->query($query_remove_emprestado);

        // Verifica se a remoção do livro emprestado foi bem sucedida
        if ($result_remove_emprestado) {
            // Atualiza a quantidade emprestada na tabela de livros
            $query_update_livro = "UPDATE livros SET quantidade_emprestada = quantidade_emprestada - 1, disponivel = 1 WHERE id = '$livro_id'";
            $result_update_livro = $conn->query($query_update_livro);

            if ($result_update_livro) {
                $success_message = "Livro devolvido com sucesso!";
            } else {
                $error_message = "Erro ao atualizar a quantidade de livros emprestados.";
            }
        } else {
            $error_message = "Erro ao devolver o livro.";
        }
    } else {
        $error_message = "O livro não está emprestado para este usuário.";
    }

} else {
    $error_message = "ID do livro não fornecido.";
}

// Redireciona de volta para a página de devolução de livros
header("Location: devolve_livro.php?message=".urlencode($success_message ?? $error_message));
exit;
?>
