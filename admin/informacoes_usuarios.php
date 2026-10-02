<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$matricula = isset($_GET['matricula']) ? trim($_GET['matricula']) : '';
$data_inicio = isset($_GET['data_inicio']) ? trim($_GET['data_inicio']) : '';
$data_fim = isset($_GET['data_fim']) ? trim($_GET['data_fim']) : '';
$listar_todos = isset($_GET['listar_todos']);
$pesquisar = isset($_GET['pesquisar']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Empréstimos - Leitores de Papel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .report-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .icon-avatar {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.12), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .filter-panel {
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 22px 24px;
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

        .input-group .input-group-prepend .input-group-text {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
            font-size: 14px;
            min-width: 42px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: 1px solid #dc2626;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
            height: auto;
            color: #1e293b;
        }

        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        }

        .table-responsive {
            margin-top: 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            overflow: hidden;
        }

        .table-report {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-report thead th {
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

        .table-report tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-report tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-report td.text-left {
            text-align: left !important;
        }

        .nowrap-cell {
            white-space: nowrap;
        }

        .badge-status-returned {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }

        .badge-status-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }

        .stat-count-badge {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
        }

        .btn-action-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-action-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .btn-action-secondary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #1e293b;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-action-secondary:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .report-wrapper {
                width: 100% !important;
                padding: 18px 14px !important;
                margin: 10px auto !important;
                border-radius: 8px;
            }

            .filter-panel {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="report-wrapper">
        <!-- Topo da Página -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-avatar mr-3">
                    <i class="fa fa-file-invoice text-danger"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #1e293b; font-size: 22px;">Relatório de Empréstimos de Livros</h2>
                    <span class="text-muted small">Consulte o histórico institucional de empréstimos, devoluções e pendências</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="lista_livros.php" class="btn btn-outline-secondary btn-sm mr-2 shadow-sm font-weight-bold">
                    <i class="fa fa-list mr-1"></i> Lista de Livros
                </a>
                <a href="index.php" class="btn btn-warning btn-sm shadow-sm font-weight-bold text-dark">
                    <i class="fa fa-arrow-left mr-1"></i> Painel Admin
                </a>
            </div>
        </div>

        <!-- Painel de Filtros -->
        <form method="GET" action="informacoes_usuarios.php" class="filter-panel">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="matricula">Número de Matrícula</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="matricula" name="matricula"
                            placeholder="Ex: 20261099" value="<?php echo htmlspecialchars($matricula); ?>">
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="data_inicio">Data de Início</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                            value="<?php echo htmlspecialchars($data_inicio); ?>">
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="data_fim">Data de Fim</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                        </div>
                        <input type="date" name="data_fim" id="data_fim" class="form-control"
                            value="<?php echo htmlspecialchars($data_fim); ?>">
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-5 mb-2">
                    <button type="submit" class="btn btn-action-primary btn-block font-weight-bold" name="pesquisar" value="1">
                        <i class="fas fa-search mr-2"></i>Filtrar Relatório
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-action-secondary btn-block font-weight-bold" name="listar_todos" value="1">
                        <i class="fas fa-list mr-2"></i>Listar Todos os Empréstimos
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="informacoes_usuarios.php" class="btn btn-outline-secondary btn-block font-weight-bold py-2">
                        <i class="fas fa-undo mr-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>

        <?php
        if ($listar_todos || ($pesquisar && (!empty($matricula) || !empty($data_inicio) || !empty($data_fim)))) {
            $sql = "SELECT u.nome, l.titulo, l.autor, le.data_emprestimo, le.data_devolucao
                    FROM livros_emprestados le
                    INNER JOIN usuarios u ON u.id = le.user_id
                    INNER JOIN livros l ON l.id = le.livro_id
                    WHERE 1=1";
            $params = [];
            $types = "";

            if (!$listar_todos && !empty($matricula)) {
                $sql .= " AND u.matricula = ?";
                $params[] = $matricula;
                $types .= "s";
            }

            if (!empty($data_inicio)) {
                $sql .= " AND le.data_emprestimo >= ?";
                $params[] = $data_inicio . " 00:00:00";
                $types .= "s";
            }

            if (!empty($data_fim)) {
                $sql .= " AND le.data_emprestimo <= ?";
                $params[] = $data_fim . " 23:59:59";
                $types .= "s";
            }

            $sql .= " ORDER BY le.data_emprestimo DESC";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                if (!empty($params)) {
                    $bind_names = array($types);
                    for ($i = 0; $i < count($params); $i++) {
                        $bind_name = 'bind' . $i;
                        $$bind_name = $params[$i];
                        $bind_names[] = &$$bind_name;
                    }
                    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
                }

                $stmt->execute();
                $result_relatorio = $stmt->get_result();

                if ($result_relatorio && $result_relatorio->num_rows > 0) {
                    $totalRegistros = $result_relatorio->num_rows;
                    echo '<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">';
                    echo '<span class="stat-count-badge mb-2"><i class="fa fa-chart-bar text-danger mr-2"></i>' . $totalRegistros . ' registro(s) encontrado(s)</span>';
                    echo '</div>';

                    echo '<div class="table-responsive">';
                    echo '<table class="table table-report table-hover">';
                    echo '<thead><tr>';
                    echo '<th style="text-align: left;">Nome do Usuário</th>';
                    echo '<th style="text-align: left;">Título do Livro</th>';
                    echo '<th style="text-align: left;">Autor</th>';
                    echo '<th>Data de Empréstimo</th>';
                    echo '<th>Status / Devolução</th>';
                    echo '</tr></thead>';
                    echo '<tbody>';

                    while ($row = $result_relatorio->fetch_assoc()) {
                        $dtEmp = !empty($row["data_emprestimo"]) ? date('d/m/Y H:i', strtotime($row["data_emprestimo"])) : '-';
                        if (!empty($row["data_devolucao"])) {
                            $statusDev = '<span class="badge-status-returned"><i class="fa fa-check-circle mr-1"></i>' . date('d/m/Y H:i', strtotime($row["data_devolucao"])) . '</span>';
                        } else {
                            $statusDev = '<span class="badge-status-pending"><i class="fa fa-clock mr-1"></i>Pendente</span>';
                        }

                        echo '<tr>';
                        echo '<td class="text-left font-weight-bold text-dark"><i class="fa fa-user-circle text-muted mr-2"></i>' . htmlspecialchars($row["nome"]) . '</td>';
                        echo '<td class="text-left font-weight-bold" style="color: #0f172a;"><i class="fa fa-book text-danger mr-2"></i>' . htmlspecialchars($row["titulo"]) . '</td>';
                        echo '<td class="text-left text-muted">' . htmlspecialchars($row["autor"]) . '</td>';
                        echo '<td class="nowrap-cell font-weight-bold text-secondary">' . $dtEmp . '</td>';
                        echo '<td class="nowrap-cell">' . $statusDev . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info text-center mt-3 shadow-sm"><i class="fas fa-info-circle mr-2"></i>Nenhum empréstimo encontrado para os filtros selecionados.</div>';
                }
                $stmt->close();
            } else {
                echo '<div class="alert alert-danger text-center mt-3 shadow-sm">Erro ao processar a consulta no banco de dados.</div>';
            }
        } elseif ($pesquisar && empty($matricula) && empty($data_inicio) && empty($data_fim)) {
            echo '<div class="alert alert-warning text-center mt-3 shadow-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Por favor, forneça ao menos um filtro (matrícula ou período) para pesquisar.</div>';
        }
        ?>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fas fa-arrows-alt-h mr-1"></i> Role a tabela horizontalmente para visualizar todas as informações.
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="index.php" class="btn btn-warning btn-block font-weight-bold py-2 shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel Admin
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>