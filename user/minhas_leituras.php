<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_login('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$leituras = [];
$error_message = '';

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT historico_emprestimos.*, livros.titulo as livro_titulo, livros.autor as livro_autor, livros.editora as livro_editora, livros.ano_publicacao as livro_ano_publicacao 
                            FROM historico_emprestimos 
                            JOIN livros ON historico_emprestimos.livro_id = livros.id 
                            WHERE user_id = ? 
                            ORDER BY historico_emprestimos.data_emprestimo DESC");
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
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container main-content">
        <h2 class="mb-4">Minhas Leituras</h2>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php } elseif (!empty($leituras)) { ?>
            <?php foreach ($leituras as $row) { ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($row['livro_titulo']); ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?php echo htmlspecialchars($row['livro_autor']); ?>
                        </h6>
                        <p class="card-text">
                            <strong>Editora:</strong> <?php echo htmlspecialchars($row['livro_editora']); ?><br>
                            <strong>Ano de Publicação:</strong> <?php echo htmlspecialchars($row['livro_ano_publicacao']); ?><br>
                            <strong>Data Empréstimo:</strong> <?php echo htmlspecialchars($row['data_emprestimo']); ?><br>
                            <strong>Data Devolução:</strong> <?php echo htmlspecialchars($row['data_devolucao']); ?><br>
                        </p>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info" role="alert">Nenhum registro de empréstimo encontrado.</div>
        <?php } ?>

        <div class="row mt-4 mb-4">
            <div class="col-md-3 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Página Principal</a>
            </div>
            <div class="col-md-3 mb-2">
                <a href="minhas_leituras.php" class="btn btn-danger btn-block">Atualizar Minhas Leituras</a>
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
