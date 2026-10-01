<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas usuários autenticados podem devolver livros
require_login('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$livro_id = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;

if ($user_id <= 0) {
    header("Location: ../public/index.php");
    exit;
}

if ($livro_id <= 0) {
    $_SESSION['error_message'] = "ID do livro inválido.";
    header("Location: ../user/devolve_livro.php");
    exit;
}

// Inicia transação para garantir integridade e isolamento
$conn->begin_transaction();

try {
    // 1. Verifica se o usuário autenticado realmente possui esse livro emprestado (Controle de Posse / Anti-IDOR)
    $stmt_check = $conn->prepare("SELECT id FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    if (!$stmt_check) {
        throw new Exception("Erro ao preparar consulta de empréstimo: " . $conn->error);
    }
    $stmt_check->bind_param("ii", $livro_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        $stmt_check->close();
        throw new Exception("Você não possui um empréstimo ativo deste livro.");
    }
    $stmt_check->close();

    // 2. Remove o registro do empréstimo para o usuário atual
    $stmt_del = $conn->prepare("DELETE FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    if (!$stmt_del) {
        throw new Exception("Erro ao remover empréstimo: " . $conn->error);
    }
    $stmt_del->bind_param("ii", $livro_id, $user_id);
    $stmt_del->execute();
    if ($stmt_del->affected_rows <= 0) {
        $stmt_del->close();
        throw new Exception("Falha ao remover o empréstimo.");
    }
    $stmt_del->close();

    // 3. Atualiza o estoque do livro incrementando 1 unidade
    $stmt_upd = $conn->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?");
    if (!$stmt_upd) {
        throw new Exception("Erro ao atualizar acervo: " . $conn->error);
    }
    $stmt_upd->bind_param("i", $livro_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // Confirma a transação
    $conn->commit();
    $_SESSION['success_message'] = "Livro devolvido com sucesso! Quantidade atualizada.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
}

header("Location: ../user/devolve_livro.php");
exit;
