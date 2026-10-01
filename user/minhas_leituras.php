<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_login('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$leituras = [];
$error_message = '';
$msg_sucesso = '';

if (isset($_GET['atualizado'])) {
    $msg_sucesso = 'Suas leituras foram atualizadas com sucesso!';
}

if ($user_id > 0) {
    // 1. Sincroniza automaticamente qualquer empréstimo ativo com a tabela de histórico
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
        $sync_stmt->bind_param("i", $user_id);
        $sync_stmt->execute();
        $sync_stmt->close();
    }

    // 2. Consulta o histórico completo de leituras do usuário
    $stmt = $conn->prepare("
        SELECT he.*, l.titulo as livro_titulo, l.autor as livro_autor, l.editora as livro_editora, l.ano_publicacao as livro_ano_publicacao,
               (CASE WHEN le.id IS NOT NULL THEN 1 ELSE 0 END) as is_ativo
        FROM historico_emprestimos he
        JOIN livros l ON he.livro_id = l.id
        LEFT JOIN livros_emprestados le 
          ON le.livro_id = he.livro_id AND le.user_id = he.user_id AND le.data_emprestimo = he.data_emprestimo
        WHERE he.user_id = ?
        ORDER BY he.data_emprestimo DESC
    ");

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $leituras = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    } else {
        $error_message = "Erro ao carregar o histórico de leituras.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minhas Leituras - Leitores de Papel</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .badge-status {
            font-size: 0.85rem;
            padding: 0.4em 0.7em;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2>Minhas Leituras</h2>
            <a href="minhas_leituras.php?atualizado=1" class="btn btn-danger">
                <i class="fa fa-sync-alt mr-1"></i> Atualizar Minhas Leituras
            </a>
        </div>

        <?php if (!empty($msg_sucesso)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg_sucesso); ?>
            </div>
        <?php } ?>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php } elseif (!empty($leituras)) { ?>
            <div class="row">
                <?php foreach ($leituras as $row) {
                    $is_ativo = !empty($row['is_ativo']) && $row['is_ativo'] == 1;
                    $data_emp = !empty($row['data_emprestimo']) ? date('d/m/Y H:i', strtotime($row['data_emprestimo'])) : '-';
                    $data_dev = !empty($row['data_devolucao']) ? date('d/m/Y H:i', strtotime($row['data_devolucao'])) : '-';
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-<?php echo $is_ativo ? 'warning' : 'light'; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title text-primary mb-1">
                                        <?php echo htmlspecialchars($row['livro_titulo']); ?>
                                    </h5>
                                    <?php if ($is_ativo) { ?>
                                        <span class="badge badge-warning badge-status text-dark">Em Andamento</span>
                                    <?php } else { ?>
                                        <span class="badge badge-success badge-status">Concluído</span>
                                    <?php } ?>
                                </div>
                                <h6 class="card-subtitle mb-3 text-muted">
                                    <i class="fa fa-user-edit mr-1"></i><?php echo htmlspecialchars($row['livro_autor']); ?>
                                </h6>
                                <p class="card-text text-secondary mb-2">
                                    <?php if (!empty($row['livro_editora'])) { ?>
                                        <strong>Editora:</strong> <?php echo htmlspecialchars($row['livro_editora']); ?><br>
                                    <?php } ?>
                                    <?php if (!empty($row['livro_ano_publicacao'])) { ?>
                                        <strong>Ano:</strong> <?php echo htmlspecialchars($row['livro_ano_publicacao']); ?><br>
                                    <?php } ?>
                                    <strong>Data do Empréstimo:</strong> <?php echo htmlspecialchars($data_emp); ?><br>
                                    <strong><?php echo $is_ativo ? 'Previsão de Devolução:' : 'Data da Devolução:'; ?></strong> <?php echo htmlspecialchars($data_dev); ?>
                                </p>
                            </div>
                            <?php if ($is_ativo) { ?>
                                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                                    <a href="devolve_livro.php" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-undo mr-1"></i> Devolver Livro
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-info" role="alert">Nenhum registro de empréstimo ou leitura encontrado.</div>
        <?php } ?>

        <div class="row mt-4 mb-4">
            <div class="col-md-4 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Página Principal</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="lista_livros.php" class="btn btn-secondary btn-block">Ver Catálogo de Livros</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="devolve_livro.php" class="btn btn-warning btn-block">Meus Empréstimos Ativos</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/atualizar_emprestimo.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>
