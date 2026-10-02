<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$livros = [];

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT livros.id, livros.titulo, livros.autor, livros.ano_publicacao, livros_emprestados.data_emprestimo, livros_emprestados.data_devolucao
              FROM livros_emprestados
              INNER JOIN livros ON livros_emprestados.livro_id = livros.id
              WHERE livros_emprestados.user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $livros = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Devolver Livro - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        .devolve-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f6;
        }

        .devolve-wrapper h2 {
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

        .table-devolucao {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-devolucao thead th {
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

        .table-devolucao tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-devolucao tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-devolucao td.col-titulo {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 200px;
        }

        .table-devolucao td.col-autor {
            text-align: left;
            min-width: 160px;
            color: #475569;
        }

        .table-devolucao td.nowrap-cell {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .devolve-wrapper {
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

    <div class="devolve-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2><i class="fa fa-undo text-danger mr-2"></i>Devolução de Livros</h2>
            <a href="lista_livros.php" class="btn btn-warning">
                <i class="fa fa-book mr-1"></i> Lista de Livros
            </a>
        </div>

        <?php
        $msg_sucesso = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : (isset($_GET['success_message']) ? $_GET['success_message'] : (isset($_GET['message']) ? $_GET['message'] : null));
        $msg_erro = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : (isset($_GET['error_message']) ? $_GET['error_message'] : null);
        if (isset($_SESSION['success_message'])) { unset($_SESSION['success_message']); }
        if (isset($_SESSION['error_message'])) { unset($_SESSION['error_message']); }
        ?>

        <?php if (!empty($msg_sucesso)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> <?php echo htmlspecialchars($msg_sucesso); ?>
            </div>
        <?php } ?>

        <?php if (!empty($msg_erro)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle mr-1"></i> <?php echo htmlspecialchars($msg_erro); ?>
            </div>
        <?php } ?>

        <?php if (!empty($livros)) { ?>
            <div class="table-responsive">
                <table class="table-devolucao table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Ano</th>
                            <th>Data de Empréstimo</th>
                            <th>Previsão de Devolução</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livros as $livro) {
                            $data_emp = !empty($livro['data_emprestimo']) ? date('d/m/Y', strtotime($livro['data_emprestimo'])) : '-';
                            $data_dev = !empty($livro['data_devolucao']) ? date('d/m/Y', strtotime($livro['data_devolucao'])) : '-';
                            ?>
                            <tr>
                                <td class="col-titulo">
                                    <?php echo htmlspecialchars($livro['titulo']); ?>
                                </td>
                                <td class="col-autor">
                                    <?php echo htmlspecialchars($livro['autor']); ?>
                                </td>
                                <td class="nowrap-cell">
                                    <?php echo htmlspecialchars($livro['ano_publicacao']); ?>
                                </td>
                                <td class="nowrap-cell font-weight-bold">
                                    <?php echo htmlspecialchars($data_emp); ?>
                                </td>
                                <td class="nowrap-cell text-danger font-weight-bold">
                                    <?php echo htmlspecialchars($data_dev); ?>
                                </td>
                                <td class="nowrap-cell">
                                    <form method="POST" action="../actions/devolve_livro_action_admin.php" style="display: inline-block; margin: 0;">
                                        <input type="hidden" name="livro_id" value="<?php echo htmlspecialchars($livro['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-undo mr-1"></i> Devolver
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center py-4 my-3" role="alert">
                <i class="fa fa-info-circle mr-1"></i> Você não possui livros emprestados no momento.
            </div>
        <?php } ?>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fa fa-arrows-alt-h mr-1"></i> Role a página horizontalmente para visualizar a tabela completa.
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="lista_livros.php" class="btn btn-warning btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Voltar para Lista de Livros
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="empresta_livro.php" class="btn btn-outline-danger btn-block">
                    <i class="fa fa-book-reader mr-1"></i> Emprestar Outro Livro
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>
