<?php

session_start();

global $conn;

include '../config/database.php';



// Processamento do formulário

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = $_POST["titulo"];

    $autor = $_POST["autor"];

    $ano = $_POST["ano"];

    $editora = $_POST["editora"];

    $quantidade = $_POST["quantidade"];

    $isbn = $_POST["isbn"];

    $genero = $_POST["genero"];



    // Calcula a disponibilidade baseado na quantidade
    $disponivel = ($quantidade > 0) ? 1 : 0;

    // Inserir o livro no banco de dados

    $sql = "INSERT INTO livros (titulo, autor, ano_publicacao, editora, disponivel, quantidade, isbn, genero) 

            VALUES ('$titulo', '$autor', '$ano', '$editora', $disponivel, $quantidade, '$isbn', '$genero')";



    if ($conn->query($sql) === TRUE) {

        echo "Livro cadastrado com sucesso.";

        // Redirecionar para outra página após 2 segundos

        header("refresh:2; url=../admin/cadastro_livro.php");

        exit; // Encerra o script para evitar a execução do restante do código

    } else {

        echo "Erro ao cadastrar o livro: " . $conn->error;

    }

}

