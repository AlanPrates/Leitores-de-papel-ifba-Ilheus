<?php
session_start();
global $conn;
include 'conexao.php';

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $autor = $_POST["autor"];

    // Inserir o livro no banco de dados
    $sql = "INSERT INTO livros (titulo, autor, disponivel) VALUES ('$titulo', '$autor', 1)";

    if ($conn->query($sql) === TRUE) {
        echo "Livro cadastrado com sucesso.";
    } else {
        echo "Erro ao cadastrar o livro: " . $conn->error;
    }
}
