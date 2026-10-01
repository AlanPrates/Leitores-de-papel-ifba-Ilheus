<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_login('../public/index.php');

$current_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$historico = [];

if ($current_user_id > 0) {
    // Sincroniza qualquer empréstimo ativo com o histórico
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

    $stmt = $conn->prepare("SELECT l.titulo, l.autor, he.data_emprestimo, he.data_devolucao
                            FROM historico_emprestimos he
                            INNER JOIN livros l ON l.id = he.livro_id
                            WHERE he.user_id = ?
                            ORDER BY he.data_emprestimo DESC");
    if ($stmt) {
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result_historico = $stmt->get_result();
        if ($result_historico && $result_historico->num_rows > 0) {
            $historico = $result_historico->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Meu Histórico de Empréstimos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            white-space: nowrap;
            overflow-x: auto;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <br>
    <div class="container" style="min-height: 70vh;">
        <h2>Meu Histórico de Empréstimos</h2>
        <br>
        <?php if (!empty($historico)) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Título do Livro</th>
                            <th>Autor do Livro</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["titulo"]); ?></td>
                                <td><?php echo htmlspecialchars($row["autor"]); ?></td>
                                <td><?php echo htmlspecialchars($row["data_emprestimo"]); ?></td>
                                <td><?php echo htmlspecialchars($row["data_devolucao"]); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">Nenhum registro encontrado no seu histórico de empréstimos.</div>
        <?php } ?>

        <br>
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <div class="row mb-3 mt-4">
            <div class="col-md-12 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/atualizar_emprestimo.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>