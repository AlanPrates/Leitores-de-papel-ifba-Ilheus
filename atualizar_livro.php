<?php
session_start();
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se o formulário de atualização foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    // Obtém os valores do formulário
    $livro_id = $_POST['livro_id'];
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $autor = $conn->real_escape_string($_POST['autor']);
    $ano_publicacao = $conn->real_escape_string($_POST['ano_publicacao']);
    $disponivel = $conn->real_escape_string($_POST['disponivel']);
    $quantidade = $conn->real_escape_string($_POST['quantidade']);

    // Atualiza os dados do livro no banco de dados
    $query = "UPDATE livros SET titulo='$titulo', autor='$autor', ano_publicacao='$ano_publicacao', disponivel='$disponivel', quantidade='$quantidade' WHERE id='$livro_id'";
    $result = $conn->query($query);

    // Verifica se a atualização foi bem-sucedida ou exibe uma mensagem de erro
    if ($result) {
        // Redireciona de volta para a página de lista de livros
        header("Location: lista_livros_admin.php");
        exit;
    } else {
        echo "Erro ao atualizar o livro: " . $conn->error;
    }
} else {
    // Se o formulário não foi enviado, redireciona para a página de lista de livros
    header("Location: lista_livros_admin.php");
    exit;
}
