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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Meu Histórico de Empréstimos - Leitores de Papel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        .history-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f6;
            min-height: 60vh;
        }

        .history-wrapper h2 {
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
            margin-bottom: 25px;
        }

        .table-responsive {
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .table-history {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-history thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
            padding: 14px 16px;
            border-bottom: 2px solid #cbd5e1;
            border-top: none;
        }

        .table-history tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-history tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-history td.col-titulo {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 220px;
        }

        .table-history td.col-autor {
            text-align: left;
            min-width: 180px;
            color: #475569;
        }

        .table-history td.nowrap-cell {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .history-wrapper {
                width: 100% !important;
                margin: 15px auto !important;
                padding: 20px 15px;
                border-radius: 0;
                border: none;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="history-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2><i class="fa fa-history text-danger mr-2"></i>Histórico de Empréstimos</h2>
            <a href="minhas_leituras.php" class="btn btn-warning">
                <i class="fa fa-book-reader mr-1"></i> Minhas Leituras
            </a>
        </div>

        <?php if (!empty($historico)) { ?>
            <div class="table-responsive">
                <table class="table-history table-striped">
                    <thead>
                        <tr>
                            <th>Título do Livro</th>
                            <th>Autor do Livro</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $row) {
                            $data_emp = !empty($row["data_emprestimo"]) ? date('d/m/Y H:i', strtotime($row["data_emprestimo"])) : '-';
                            $data_dev = !empty($row["data_devolucao"]) ? date('d/m/Y H:i', strtotime($row["data_devolucao"])) : '-';
                            ?>
                            <tr>
                                <td class="col-titulo"><?php echo htmlspecialchars($row["titulo"]); ?></td>
                                <td class="col-autor"><?php echo htmlspecialchars($row["autor"]); ?></td>
                                <td class="nowrap-cell"><?php echo htmlspecialchars($data_emp); ?></td>
                                <td class="nowrap-cell font-weight-bold text-secondary"><?php echo htmlspecialchars($data_dev); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center py-4 my-3">
                <i class="fa fa-info-circle mr-1"></i> Nenhum registro encontrado no seu histórico de empréstimos.
            </div>
        <?php } ?>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fa fa-arrows-alt-h mr-1"></i> Role a página horizontalmente para visualizar a tabela completa.
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Voltar para Painel Principal
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="lista_livros.php" class="btn btn-outline-secondary btn-block">
                    <i class="fa fa-book mr-1"></i> Ver Catálogo de Livros
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/atualizar_emprestimo.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>