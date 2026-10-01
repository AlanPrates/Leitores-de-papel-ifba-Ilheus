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

// Sincroniza empréstimos ativos que ainda não estão gravados no histórico
$sync_stmt = $conn->prepare("
    INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao)
    SELECT le.livro_id, le.user_id, le.data_emprestimo, le.data_devolucao
    FROM livros_emprestados le
    WHERE le.user_id = ?
      AND NOT EXISTS (
          SELECT 1 FROM historico_emprestimos he
          WHERE he.livro_id = le.livro_id 
            AND he.user_id = le.user_id 
            AND he.data_emprestimo = le.data_emprestimo
      )
");

if ($sync_stmt) {
    $sync_stmt->bind_param("i", $current_user_id);
    $sync_stmt->execute();
    $sync_stmt->close();
}

$conn->close();
echo "OK";
