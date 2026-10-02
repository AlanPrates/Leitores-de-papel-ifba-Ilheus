<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Administradores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        .admin-list-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f6;
        }

        .admin-list-wrapper h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            letter-spacing: -0.5px;
        }

        .table-responsive {
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .table-admins {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-admins thead th {
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

        .table-admins tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-admins tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-admins td.col-nome {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 190px;
        }

        .table-admins td.col-email {
            text-align: left;
            min-width: 220px;
            color: #475569;
        }

        .table-admins td.nowrap-cell {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .admin-list-wrapper {
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

    <div class="admin-list-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2><i class="fa fa-user-shield text-danger mr-2"></i>Lista de Administradores</h2>
            <a href="index.php" class="btn btn-warning">
                <i class="fa fa-arrow-left mr-1"></i> Voltar ao Painel
            </a>
        </div>

        <div class="table-responsive">
            <table class="table-admins table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT admin_username, nome, email, datanascimento, sexo, telefone FROM admin";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $data_nasc = '-';
                            if (!empty($row['datanascimento']) && $row['datanascimento'] !== '0000-00-00') {
                                $time = strtotime($row['datanascimento']);
                                $data_nasc = $time ? date('d/m/Y', $time) : htmlspecialchars($row['datanascimento']);
                            }

                            echo '<tr>';
                            echo '<td class="nowrap-cell font-weight-bold text-primary">' . htmlspecialchars($row["admin_username"]) . '</td>';
                            echo '<td class="col-nome">' . htmlspecialchars($row["nome"]) . '</td>';
                            echo '<td class="col-email">' . htmlspecialchars($row["email"]) . '</td>';
                            echo '<td class="nowrap-cell">' . $data_nasc . '</td>';
                            echo '<td class="nowrap-cell">' . htmlspecialchars($row["sexo"]) . '</td>';
                            echo '<td class="nowrap-cell">' . htmlspecialchars($row["telefone"]) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="p-4 text-muted">Nenhum administrador cadastrado.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fa fa-arrows-alt-h mr-1"></i> Role a tabela horizontalmente para ver todos os campos.
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <a href="index.php" class="btn btn-warning btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Voltar para Painel de Administrador
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>