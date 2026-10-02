<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livro_id = (int)$_POST['livro_id'];

    if ($livro_id <= 0 || $user_id <= 0) {
        $error_message = "Parâmetros inválidos para empréstimo.";
    } else {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("SELECT quantidade, titulo FROM livros WHERE id = ? FOR UPDATE");
            if (!$stmt) {
                throw new Exception("Erro ao consultar livro.");
            }
            $stmt->bind_param("i", $livro_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $livro = $result->fetch_assoc();
                $quantidade = (int)$livro['quantidade'];
                $titulo = $livro['titulo'];
                $stmt->close();

                if ($quantidade > 0) {
                    $data_emprestimo = date('Y-m-d H:i:s');
                    $data_devolucao = date('Y-m-d H:i:s', strtotime('+15 days'));

                    $insert_stmt = $conn->prepare("INSERT INTO livros_emprestados (livro_id, user_id, data_emprestimo, data_devolucao, titulo) VALUES (?, ?, ?, ?, ?)");
                    if (!$insert_stmt) {
                        throw new Exception("Erro ao registrar empréstimo.");
                    }
                    $insert_stmt->bind_param("iisss", $livro_id, $user_id, $data_emprestimo, $data_devolucao, $titulo);
                    $insert_stmt->execute();
                    $insert_stmt->close();

                    // Grava imediatamente no histórico de leituras
                    $insert_hist = $conn->prepare("INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
                    if ($insert_hist) {
                        $insert_hist->bind_param("iiss", $livro_id, $user_id, $data_emprestimo, $data_devolucao);
                        $insert_hist->execute();
                        $insert_hist->close();
                    }

                    $nova_quantidade = $quantidade - 1;
                    $disponivel = ($nova_quantidade > 0) ? 1 : 0;
                    $update_stmt = $conn->prepare("UPDATE livros SET quantidade = ?, disponivel = ? WHERE id = ?");
                    if (!$update_stmt) {
                        throw new Exception("Erro ao decrementar estoque.");
                    }
                    $update_stmt->bind_param("iii", $nova_quantidade, $disponivel, $livro_id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    $conn->commit();
                    $success_message = "Livro emprestado com sucesso!";
                } else {
                    $conn->rollback();
                    $error_message = "Não há exemplares disponíveis para empréstimo.";
                }
            } else {
                $stmt->close();
                $conn->rollback();
                $error_message = "Livro não encontrado.";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Empréstimo de Livro - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container container-form mt-4 mb-4" style="min-height: 60vh;">
        <h2><i class="fa fa-book-reader text-danger mr-2"></i>Empréstimo de Livro</h2>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php } ?>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle mr-1"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php } ?>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="lista_livros.php" class="btn btn-warning btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Lista de Livros
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="devolve_livro.php" class="btn btn-outline-danger btn-block">
                    <i class="fa fa-undo mr-1"></i> Devoluções Ativas
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>
