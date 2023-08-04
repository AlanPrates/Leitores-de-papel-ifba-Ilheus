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

    // Obtém a quantidade atual do livro no banco de dados
    $query = "SELECT quantidade FROM livros WHERE id = '$livro_id'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $livro = $result->fetch_assoc();
        $quantidade_atual = $livro['quantidade'];

        // Atualiza a quantidade do livro no banco de dados
        $nova_quantidade = $quantidade_atual + 1;
        $update_query = "UPDATE livros SET quantidade = $nova_quantidade WHERE id = '$livro_id'";
        $update_result = $conn->query($update_query);

        if ($update_result) {
            // Remove o livro emprestado do banco de dados
            $delete_query = "DELETE FROM livros_emprestados WHERE livro_id = '$livro_id' AND user_id = '$user_id'";
            $delete_result = $conn->query($delete_query);

            if ($delete_result) {
                $success_message = "Livro devolvido com sucesso! Quantidade atualizada.";
            } else {
                $error_message = "Erro ao devolver o livro.";
            }
        } else {
            $error_message = "Erro ao atualizar a quantidade do livro.";
        }
    } else {
        $error_message = "Livro não encontrado.";
    }

} else {
    $error_message = "ID do livro não fornecido.";
}

// Redireciona de volta para a página de devolução de livros
header("Location: devolve_livro.php?message=".urlencode($success_message ?? $error_message));
exit;
?>
