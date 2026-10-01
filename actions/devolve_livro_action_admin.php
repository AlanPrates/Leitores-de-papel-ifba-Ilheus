<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem acessar
require_admin('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$livro_id = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;

if ($user_id <= 0) {
    header("Location: ../public/index.php");
    exit;
}

if ($livro_id <= 0) {
    $_SESSION['error_message'] = "ID do livro inválido.";
    header("Location: ../admin/devolve_livro.php");
    exit;
}

$conn->begin_transaction();

try {
    // 1. Verifica se o empréstimo existe para o usuário admin logado
    $stmt_check = $conn->prepare("SELECT id FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    if (!$stmt_check) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }
    $stmt_check->bind_param("ii", $livro_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        $stmt_check->close();
        throw new Exception("Empréstimo não encontrado para este administrador.");
    }
    $stmt_check->close();

    // Obtém data de empréstimo para sincronizar histórico
    $stmt_info = $conn->prepare("SELECT data_emprestimo FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    $data_emp = date('Y-m-d H:i:s');
    if ($stmt_info) {
        $stmt_info->bind_param("ii", $livro_id, $user_id);
        $stmt_info->execute();
        $res_info = $stmt_info->get_result();
        if ($res_info && $row_info = $res_info->fetch_assoc()) {
            $data_emp = $row_info['data_emprestimo'];
        }
        $stmt_info->close();
    }

    // 2. Remove o registro de empréstimo
    $stmt_del = $conn->prepare("DELETE FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    if (!$stmt_del) {
        throw new Exception("Erro ao remover empréstimo: " . $conn->error);
    }
    $stmt_del->bind_param("ii", $livro_id, $user_id);
    $stmt_del->execute();
    if ($stmt_del->affected_rows <= 0) {
        $stmt_del->close();
        throw new Exception("Falha ao registrar devolução.");
    }
    $stmt_del->close();

    // 3. Atualiza a quantidade do livro no acervo
    $stmt_upd = $conn->prepare("UPDATE livros SET quantidade = quantidade + 1, disponivel = 1 WHERE id = ?");
    if (!$stmt_upd) {
        throw new Exception("Erro ao atualizar estoque: " . $conn->error);
    }
    $stmt_upd->bind_param("i", $livro_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // 4. Atualiza o histórico de leituras com a devolução confirmada
    $data_devolucao_real = date('Y-m-d H:i:s');
    $stmt_upd_hist = $conn->prepare("UPDATE historico_emprestimos SET data_devolucao = ? WHERE livro_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1");
    if ($stmt_upd_hist) {
        $stmt_upd_hist->bind_param("sii", $data_devolucao_real, $livro_id, $user_id);
        $stmt_upd_hist->execute();
        if ($stmt_upd_hist->affected_rows === 0) {
            $stmt_ins_hist = $conn->prepare("INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
            if ($stmt_ins_hist) {
                $stmt_ins_hist->bind_param("iiss", $livro_id, $user_id, $data_emp, $data_devolucao_real);
                $stmt_ins_hist->execute();
                $stmt_ins_hist->close();
            }
        }
        $stmt_upd_hist->close();
    }

    $conn->commit();
    $_SESSION['success_message'] = "Livro devolvido com sucesso! Quantidade atualizada.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
}

header("Location: ../admin/devolve_livro.php");
exit;
