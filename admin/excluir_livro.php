<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem excluir livros
require_admin('../public/index.php');

$livro_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livro_id = (int)$_POST['livro_id'];
} elseif (isset($_GET['livro_id'])) {
    $livro_id = (int)$_GET['livro_id'];
} elseif (isset($_GET['id'])) {
    $livro_id = (int)$_GET['id'];
}

if ($livro_id <= 0) {
    $_SESSION['error_message'] = 'ID do livro inválido.';
    header("Location: lista_livros.php");
    exit;
}

// 1. Verifica se o livro existe
$check = $conn->prepare("SELECT id, titulo FROM livros WHERE id = ?");
if (!$check) {
    $_SESSION['error_message'] = 'Erro ao verificar livro no banco de dados.';
    header("Location: lista_livros.php");
    exit;
}
$check->bind_param("i", $livro_id);
$check->execute();
$res_livro = $check->get_result();
$livro = $res_livro ? $res_livro->fetch_assoc() : null;
$check->close();

if (!$livro) {
    $_SESSION['error_message'] = 'Livro não encontrado ou já excluído.';
    header("Location: lista_livros.php");
    exit;
}

// 2. Impede a exclusão se houver empréstimos ativos pendentes
$stmt_loans = $conn->prepare("SELECT COUNT(*) AS total FROM livros_emprestados WHERE livro_id = ?");
if ($stmt_loans) {
    $stmt_loans->bind_param("i", $livro_id);
    $stmt_loans->execute();
    $res_loans = $stmt_loans->get_result()->fetch_assoc();
    $stmt_loans->close();

    if ($res_loans && (int)$res_loans['total'] > 0) {
        $_SESSION['error_message'] = 'Não é possível excluir o livro "' . htmlspecialchars($livro['titulo']) . '" pois existem exemplares emprestados que ainda não foram devolvidos.';
        header("Location: lista_livros.php");
        exit;
    }
}

// 3. Executa exclusão transacional segura
$conn->begin_transaction();
try {
    // Remove comentários associados ao livro
    $del_com = $conn->prepare("DELETE FROM comentarios WHERE livro_id = ?");
    if ($del_com) {
        $del_com->bind_param("i", $livro_id);
        $del_com->execute();
        $del_com->close();
    }

    // Remove referências no histórico
    $del_hist = $conn->prepare("DELETE FROM historico_emprestimos WHERE livro_id = ?");
    if ($del_hist) {
        $del_hist->bind_param("i", $livro_id);
        $del_hist->execute();
        $del_hist->close();
    }

    // Exclui o livro da tabela livros
    $stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Falha ao preparar exclusão do livro: " . $conn->error);
    }
    $stmt->bind_param("i", $livro_id);
    if (!$stmt->execute()) {
        throw new Exception("Falha ao executar exclusão: " . $stmt->error);
    }
    $stmt->close();

    $conn->commit();
    $_SESSION['success_message'] = 'O livro "' . htmlspecialchars($livro['titulo']) . '" foi excluído com sucesso.';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = 'Ocorreu um erro ao excluir o livro: ' . $e->getMessage();
}

header("Location: lista_livros.php");
exit;
