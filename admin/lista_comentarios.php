<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

// Consulta todos os comentários com avaliação
$query_comentarios = "SELECT c.id, l.titulo, u.username, c.comentario, c.avaliacao
                      FROM comentarios c
                      LEFT JOIN livros l ON c.livro_id = l.id
                      LEFT JOIN usuarios u ON c.user_id = u.id
                      ORDER BY c.id DESC";

$result_comentarios = $conn->query($query_comentarios);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Comentários - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        .comments-wrapper {
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

        .comments-wrapper h2 {
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

        .table-comments {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-comments thead th {
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

        .table-comments tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-comments tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-comments td.col-livro {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 200px;
        }

        .table-comments td.col-usuario {
            white-space: nowrap;
            font-weight: 600;
            color: #2563eb;
        }

        .table-comments td.col-comentario {
            text-align: left;
            min-width: 250px;
            color: #334155;
        }

        .table-comments td.nowrap-cell {
            white-space: nowrap;
        }

        .stars-gold {
            color: #f59e0b;
            font-size: 15px;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .comments-wrapper {
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

    <div class="comments-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2><i class="fa fa-comments text-danger mr-2"></i>Comentários e Avaliações dos Livros</h2>
            <a href="lista_livros.php" class="btn btn-warning">
                <i class="fa fa-book mr-1"></i> Lista de Livros
            </a>
        </div>

        <div class="table-responsive">
            <table class="table-comments table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Livro</th>
                        <th>Usuário</th>
                        <th>Avaliação</th>
                        <th>Comentário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_comentarios && $result_comentarios->num_rows > 0) {
                        while ($row_comentario = $result_comentarios->fetch_assoc()) {
                            $avaliacao = isset($row_comentario['avaliacao']) ? (int)$row_comentario['avaliacao'] : 0;
                            ?>
                            <tr>
                                <td class="nowrap-cell"><code>#<?php echo htmlspecialchars($row_comentario['id']); ?></code></td>
                                <td class="col-livro"><?php echo htmlspecialchars($row_comentario['titulo'] ? $row_comentario['titulo'] : 'Livro não identificado'); ?></td>
                                <td class="col-usuario">
                                    <i class="fa fa-user-circle mr-1"></i><?php echo htmlspecialchars($row_comentario['username'] ? $row_comentario['username'] : 'Anônimo'); ?>
                                </td>
                                <td class="nowrap-cell stars-gold">
                                    <?php
                                    if ($avaliacao > 0) {
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $avaliacao) ? '★' : '☆';
                                        }
                                    } else {
                                        echo '<span class="text-muted small">-</span>';
                                    }
                                    ?>
                                </td>
                                <td class="col-comentario"><?php echo nl2br(htmlspecialchars($row_comentario['comentario'])); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="p-4 text-muted">Nenhum comentário encontrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fa fa-arrows-alt-h mr-1"></i> Role a página horizontalmente para visualizar a tabela completa.
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="lista_livros.php" class="btn btn-warning btn-block">
                    <i class="fa fa-book mr-1"></i> Voltar para Lista de Livros
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="index.php" class="btn btn-outline-secondary btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Voltar para Painel Admin
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>