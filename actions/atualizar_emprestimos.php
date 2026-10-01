<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas usuários autenticados podem sincronizar seus próprios empréstimos
require_login('../public/index.php');

$current_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($current_user_id <= 0) {
    exit;
}

// Busca registros do usuário logado que ainda não estão no seu histórico
$stmt = $conn->prepare("SELECT livro_id, data_emprestimo, data_devolucao 
                        FROM livros_emprestados 
                        WHERE user_id = ? 
                          AND livro_id NOT IN (
                              SELECT livro_id FROM historico_emprestimos WHERE user_id = ?
                          )");

if (!$stmt) {
    exit;
}

$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $insert_stmt = $conn->prepare("INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
    
    while ($row = $result->fetch_assoc()) {
        $livro_id = (int)$row["livro_id"];
        $data_emprestimo = $row["data_emprestimo"];
        $data_devolucao = $row["data_devolucao"];

        if ($insert_stmt) {
            $insert_stmt->bind_param("iiss", $livro_id, $current_user_id, $data_emprestimo, $data_devolucao);
            $insert_stmt->execute();
        }
    }

    if ($insert_stmt) {
        $insert_stmt->close();
    }
}

$stmt->close();
$conn->close();
