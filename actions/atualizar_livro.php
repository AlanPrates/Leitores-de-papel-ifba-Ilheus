<?php

session_start();

include '../config/database.php';



// Verifica se o usuário não está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: ../public/index.php");

    exit;

}



// Verifica se o formulário de atualização foi enviado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {

    // Obtém os valores do formulário

    $livro_id = $_POST['livro_id'];

    $titulo = $conn->real_escape_string($_POST['titulo']);

    $autor = $conn->real_escape_string($_POST['autor']);

    $ano_publicacao = $conn->real_escape_string($_POST['ano_publicacao']);

    $isbn = $conn->real_escape_string($_POST['isbn']);

    $genero = $conn->real_escape_string($_POST['genero']);

    $quantidade = $conn->real_escape_string($_POST['quantidade']);

    // Calcula a disponibilidade baseado na quantidade
    $disponivel = ($quantidade > 0) ? 1 : 0;



    // Atualiza os dados do livro no banco de dados

    $query = "UPDATE livros SET titulo='$titulo', autor='$autor', ano_publicacao='$ano_publicacao', isbn='$isbn', genero='$genero', disponivel='$disponivel', quantidade='$quantidade' WHERE id='$livro_id'";

    $result = $conn->query($query);



    // Verifica se a atualização foi bem-sucedida ou exibe uma mensagem de erro

    if ($result) {

        // Redireciona para a página de edição com mensagem de sucesso

        header("Location: ../admin/lista_livros.php?livro_id=$livro_id&success=true");

        exit;

    } else {

        echo "Erro ao atualizar o livro: " . $conn->error;

    }

} else {

    // Se o formulário não foi enviado, redireciona para a página de lista de livros

    header("Location: ../admin/lista_livros.php");

    exit;

}



