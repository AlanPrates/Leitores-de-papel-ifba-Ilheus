<?php
session_start();
include 'conexao.php';
// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Busca os registros na tabela livros_emprestados que não estão na tabela historico_emprestimos
$sql = "SELECT * FROM livros_emprestados WHERE livro_id NOT IN (SELECT livro_id FROM historico_emprestimos)";
$result = $conn->query($sql);

// Verifica se ocorreu um erro na consulta SQL
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Verifica se existem registros para serem transferidos
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $livro_id = $row["livro_id"];

        // Verifica se o livro já possui um registro no histórico
        $check_sql = "SELECT * FROM historico_emprestimos WHERE livro_id = '$livro_id'";
        $check_result = $conn->query($check_sql);

        // Insere os dados na tabela historico_emprestimos apenas se não houver registro para o livro
        if ($check_result->num_rows === 0) {
            $user_id = $row["user_id"];
            $data_emprestimo = $row["data_emprestimo"];
            $data_devolucao = $row["data_devolucao"];

            // Insere os dados na tabela historico_emprestimos
            $insert_sql = "INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES ('$livro_id', '$user_id', '$data_emprestimo', '$data_devolucao')";
            $conn->query($insert_sql);
        }
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>