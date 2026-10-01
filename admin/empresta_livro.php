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
            $stmt = $conn->prepare("SELECT quantidade FROM livros WHERE id = ? FOR UPDATE");
            if (!$stmt) {
                throw new Exception("Erro ao consultar livro.");
            }
            $stmt->bind_param("i", $livro_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $livro = $result->fetch_assoc();
                $quantidade = (int)$livro['quantidade'];
                $stmt->close();

                if ($quantidade > 0) {
                    $data_emprestimo = date('Y-m-d H:i:s');
                    $data_devolucao = date('Y-m-d H:i:s', strtotime('+15 days'));

                    $insert_stmt = $conn->prepare("INSERT INTO livros_emprestados (livro_id, user_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
                    if (!$insert_stmt) {
                        throw new Exception("Erro ao registrar empréstimo.");
                    }
                    $insert_stmt->bind_param("iiss", $livro_id, $user_id, $data_emprestimo, $data_devolucao);
                    $insert_stmt->execute();
                    $insert_stmt->close();

                    // Grava imediatamente no histórico de leituras
                    $insert_hist = $conn->prepare("INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
                    if ($insert_hist) {
                        $insert_hist->bind_param("iiss", $livro_id, $user_id, $data_emprestimo, $data_devolucao);
                        $insert_hist->execute();
                        $insert_hist->close();
                    }

                    $update_stmt = $conn->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?");
                    if (!$update_stmt) {
                        throw new Exception("Erro ao decrementar estoque.");
                    }
                    $update_stmt->bind_param("i", $livro_id);
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
<html>

<head>
    <title>Empréstimo de Livro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4 mb-4" style="min-height: 70vh;">
        <h2>Empréstimo de Livro</h2>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php } ?>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php } ?>

        <br>
        <a href="lista_livros.php" class="btn btn-warning">Voltar para Lista de Livros</a>
        <br><br>
        <a href="devolve_livro.php" class="btn btn-warning">Devolver Livro</a>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>
