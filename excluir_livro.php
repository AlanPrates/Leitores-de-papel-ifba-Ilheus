<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livro_id = $_POST['livro_id'];

    // Execute a consulta para excluir o livro
    $query_excluir_livro = "DELETE FROM livros WHERE id = $livro_id";
    $result_excluir_livro = $conn->query($query_excluir_livro);

    if ($result_excluir_livro) {
        // Livro excluído com sucesso
        $_SESSION['success_message'] = 'Livro excluído com sucesso.';
    } else {
        // Ocorreu um erro ao excluir o livro
        $_SESSION['error_message'] = 'Ocorreu um erro ao excluir o livro.';
    }

    // Redirecionar de volta para a página de listagem de livros
    header("Location: lista_livros_admin.php");
    exit;
} else {
    // Se o formulário não foi submetido corretamente, redirecionar para a página de listagem de livros
    header("Location: lista_livros_admin.php");
    exit;
}
?>
