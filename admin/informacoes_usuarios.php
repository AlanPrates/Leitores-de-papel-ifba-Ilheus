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
<html>

<head>
    <title>Informações dos Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <br>
    <br>

    <div class="container">
        <h2>Relatório de Empréstimos de Livros</h2>

        <form method="GET" action="informacoes_usuarios.php">
            <div class="form-group text-left">
                <label for="matricula" class="form-label">Número de Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>">
            </div>

            <div class="form-group text-left">
                <label for="data_inicio" class="form-label">Data de Início</label>
                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo htmlspecialchars($data_inicio); ?>">
            </div>

            <div class="form-group text-left">
                <label for="data_fim" class="form-label">Data de Fim</label>
                <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo htmlspecialchars($data_fim); ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-warning w-100 d-flex align-items-center justify-content-center"
                        name="pesquisar" value="1" style="height: 38px;">Pesquisar</button>
                </div>
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-warning w-100 d-flex align-items-center justify-content-center"
                        name="listar_todos" value="1" style="height: 38px;">Listar Todos</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-2">
                    <a href="index.php" class="btn btn-warning w-100 d-flex align-items-center justify-content-center"
                        style="height: 38px;">Voltar para Painel de Usuário</a>
                </div>
            </div>
        </form>

        <br>
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <br>

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
                    // PHP 5.6 compatible dynamic bind_param
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
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped table-bordered">';
                    echo '<thead><tr><th>Nome do Usuário</th><th>Título do Livro</th><th>Autor do Livro</th><th>Data de Empréstimo</th><th>Data de Devolução</th></tr></thead>';
                    echo '<tbody>';

                    while ($row = $result_relatorio->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row["nome"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["titulo"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["autor"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["data_emprestimo"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["data_devolucao"]) . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">Nenhum empréstimo encontrado com os filtros informados.</div>';
                }
                $stmt->close();
            } else {
                echo '<div class="alert alert-danger">Erro ao processar consulta.</div>';
            }
        } elseif ($pesquisar && empty($matricula) && empty($data_inicio) && empty($data_fim)) {
            echo '<div class="alert alert-warning">Por favor, forneça ao menos um filtro (matrícula ou período) para pesquisar.</div>';
        }
        ?>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>