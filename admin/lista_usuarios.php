<?php
session_start();
include_once '../includes/auth.php';
require_admin();

global $conn;
include '../config/database.php';

// Definir a categoria selecionada
if (isset($_GET['categoria'])) {
    $categoria = $_GET['categoria'];
} else {
    $categoria = 'all';
}

// Definir o termo de pesquisa
if (isset($_GET['pesquisa'])) {
    $pesquisa = $_GET['pesquisa'];
} else {
    $pesquisa = '';
}

// Definir a ordem de classificação
if (isset($_GET['ordem']) && ($_GET['ordem'] == 'az' || $_GET['ordem'] == 'za')) {
    $ordem = $_GET['ordem'];
} else {
    $ordem = 'az';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        /* Expandir no Desktop sem ficar confinado a 800px */
        .user-list-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f6;
        }

        .user-list-wrapper h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            letter-spacing: -0.5px;
        }

        .filter-panel {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .filter-panel label {
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-responsive {
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .table-users {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-users thead th {
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

        .table-users tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-users tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-users td.col-nome {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 190px;
        }

        .table-users td.col-email {
            text-align: left;
            min-width: 220px;
            color: #475569;
        }

        .table-users td.nowrap-cell {
            white-space: nowrap;
        }

        /* Badges de Categoria */
        .badge-cat {
            display: inline-block;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-aluno {
            background-color: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .badge-professor {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-funcionario {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-filter {
            height: 38px;
            font-weight: 600;
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-filter:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #fff;
        }

        .btn-back {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
            font-weight: 600;
            padding: 10px 24px;
            transition: all 0.2s ease-in-out;
        }

        .btn-back:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #212529;
        }

        @media (max-width: 768px) {
            .user-list-wrapper {
                width: 100% !important;
                padding: 15px !important;
                margin: 10px auto !important;
                border-radius: 0;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>

    <?php include '../includes/header.php'; ?>

    <div class="user-list-wrapper">

        <h2 class="text-center">Lista de Usuários</h2>

        <form method="GET" action="lista_usuarios.php" class="filter-panel">
            <div class="row align-items-end">

                <div class="col-md-3 mb-2">
                    <div class="form-group mb-0 text-left">
                        <label for="categoria">Filtrar por categoria</label>
                        <select name="categoria" id="categoria" class="form-control w-100">
                            <option value="all" <?php if ($categoria == 'all') echo 'selected'; ?>>Todos</option>
                            <option value="aluno" <?php if ($categoria == 'aluno') echo 'selected'; ?>>Aluno</option>
                            <option value="professor" <?php if ($categoria == 'professor') echo 'selected'; ?>>Professor</option>
                            <option value="funcionario" <?php if ($categoria == 'funcionario') echo 'selected'; ?>>Funcionário</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="form-group mb-0 text-left">
                        <label for="ordem">Ordenar por</label>
                        <select name="ordem" id="ordem" class="form-control w-100">
                            <option value="az" <?php if ($ordem == 'az') echo 'selected'; ?>>Nome (A-Z)</option>
                            <option value="za" <?php if ($ordem == 'za') echo 'selected'; ?>>Nome (Z-A)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="form-group mb-0 text-left">
                        <label for="pesquisa">Pesquisar Usuário</label>
                        <input type="text" name="pesquisa" id="pesquisa" class="form-control w-100"
                            placeholder="Buscar por nome ou username..."
                            value="<?php echo htmlspecialchars($pesquisa, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-filter w-100 d-flex align-items-center justify-content-center">
                        <i class="fas fa-filter mr-2"></i> Filtrar
                    </button>
                </div>

            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-users table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Categoria</th>
                        <th>Matrícula</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Construir a consulta SQL
                    $sql = "SELECT * FROM usuarios WHERE 1=1";

                    if ($categoria != 'all') {
                        $eCat = $conn->real_escape_string($categoria);
                        $sql .= " AND categoria = '$eCat'";
                    }

                    if (!empty($pesquisa)) {
                        $ePesq = $conn->real_escape_string($pesquisa);
                        $sql .= " AND (username LIKE '%$ePesq%' OR nome LIKE '%$ePesq%')";
                    }

                    if ($ordem == 'az') {
                        $sql .= " ORDER BY nome ASC";
                    } else {
                        $sql .= " ORDER BY nome DESC";
                    }

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $catRaw = strtolower(trim($row["categoria"] ?? ''));
                            $badgeClass = 'badge-funcionario';
                            if ($catRaw === 'aluno') {
                                $badgeClass = 'badge-aluno';
                            } elseif ($catRaw === 'professor') {
                                $badgeClass = 'badge-professor';
                            }

                            $data_nasc = (!empty($row["datanascimento"]) && $row["datanascimento"] !== '0000-00-00')
                                ? date('d/m/Y', strtotime($row["datanascimento"]))
                                : '-';

                            $sexo = !empty($row["sexo"]) ? ucfirst(htmlspecialchars($row["sexo"], ENT_QUOTES, 'UTF-8')) : '-';
                            $categoria_fmt = !empty($row["categoria"]) ? ucfirst(htmlspecialchars($row["categoria"], ENT_QUOTES, 'UTF-8')) : '-';

                            echo '<tr>';
                            echo '<td class="nowrap-cell font-weight-bold text-muted">' . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td class="nowrap-cell"><strong>' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '</strong></td>';
                            echo '<td class="col-nome">' . htmlspecialchars($row["nome"], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td class="col-email">' . htmlspecialchars($row["email"], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td class="nowrap-cell"><span class="badge-cat ' . $badgeClass . '">' . $categoria_fmt . '</span></td>';
                            echo '<td class="nowrap-cell">' . (!empty($row["matricula"]) ? htmlspecialchars($row["matricula"], ENT_QUOTES, 'UTF-8') : '-') . '</td>';
                            echo '<td class="nowrap-cell">' . $data_nasc . '</td>';
                            echo '<td class="nowrap-cell">' . $sexo . '</td>';
                            echo '<td class="nowrap-cell">' . (!empty($row["telefone"]) ? htmlspecialchars($row["telefone"], ENT_QUOTES, 'UTF-8') : '-') . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9" class="py-4 text-muted">Nenhum usuário encontrado com os filtros selecionados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fas fa-arrows-alt-h mr-1"></i> Por favor, role a tabela horizontalmente para visualizar todas as colunas.
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="index.php" class="btn btn-back btn-block d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar para Painel de Usuário
                </a>
            </div>
        </div>

    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>

</body>

</html>